<?php

// =============================================================================
// VALIDATION
// =============================================================================

/**
 * Validate settings AJAX request
 *
 * Checks whether the request comes from the admin panel, the nonce, and the
 * current user’s capabilities.
 *
 * @since Fictioneer 4.0
 *
 * @return boolean True if valid, false if not.
 */

function fictioneer_validate_settings_ajax() {
	return is_admin() &&
         wp_doing_ajax() &&
         current_user_can( 'manage_options' ) &&
         ! empty( $_POST['nonce'] ) &&
         check_admin_referer( 'fictioneer_settings_actions', 'nonce' );
}

// =============================================================================
// DELETE EPUB AJAX
// =============================================================================

/**
 * AJAX: Delete an ePUB file from the server
 *
 * @since Fictioneer 4.0
 */

function fictioneer_ajax_delete_epub() {
  if ( fictioneer_validate_settings_ajax() ) {
    // Abort if...
    if ( empty( $_POST['post_id'] ?? '' ) ) {
      wp_send_json_error( array( 'notice' => __( 'Name missing', 'fictioneer' ) ) );
    }

    // Setup
    $file_name = sanitize_text_field( $_POST['post_id'] ) . '.epub';
    $path = wp_upload_dir()['basedir'] . '/epubs/' . $file_name;

    // Delete file from directory
		wp_delete_file( $path );

    // Check if deletion was successful
    if ( ! file_exists( $path ) ) {
      // Log
      fictioneer_log(
        sprintf(
          _x(
            'Deleted file from server: %s',
            'Pattern for deleted files in logs: "Deleted file from server: {Filename}".',
            'fictioneer'
          ),
          $file_name
        )
      );

      // Success
      wp_send_json_success(
        array(
          'notice' => sprintf(
            __( '%s deleted.', 'fictioneer' ),
            $file_name
          )
        )
      );
    } else {
      // Error
      wp_send_json_error(
        array(
          'notice' => sprintf(
            __( 'Error. %s could not be deleted.', 'fictioneer' ),
            $file_name
          )
        )
      );
    }
  }

  // Invalid
  wp_send_json_error( array( 'notice' => __( 'Invalid request.', 'fictioneer' ) ) );
}
add_action( 'wp_ajax_fictioneer_ajax_delete_epub', 'fictioneer_ajax_delete_epub' );

// =============================================================================
// PURGE SCHEMA AJAX
// =============================================================================

/**
 * AJAX: Purge schema for a post/page
 *
 * @since Fictioneer 4.0
 */

function fictioneer_ajax_purge_schema() {
	if ( fictioneer_validate_settings_ajax() ) {
    // Abort if...
    if ( empty( $_POST['post_id'] ?? '' ) ) {
      wp_send_json_error( array( 'notice' => __( 'ID missing', 'fictioneer' ) ) );
    }

    // Setup
		$post_id = fictioneer_validate_id( $_POST['post_id'] );

    // Delete schema stored in post meta
		if ( $post_id && delete_post_meta( $post_id, 'fictioneer_schema' ) ) {
      // Log
      fictioneer_log(
        sprintf(
          _x(
            'Purged schema graph of #%s',
            'Pattern for purged schema graphs in logs: "Purged schema graph of #{%s}".',
            'fictioneer'
          ),
          $post_id
        )
      );

      // Purge caches
      delete_post_meta( $post_id, 'fictioneer_seo_title_cache' );
      delete_post_meta( $post_id, 'fictioneer_seo_description_cache' );
      delete_post_meta( $post_id, 'fictioneer_seo_og_image_cache' );
      fictioneer_refresh_post_caches( $post_id );

      // Success
      wp_send_json_success(
        array(
          'notice' => sprintf(
            __( 'Schema of post #%s purged.', 'fictioneer' ),
            $post_id
          )
        )
      );
		} else {
      // Error
      wp_send_json_error(
        array(
          'notice' => sprintf(
            __( 'Error. Schema of post #%s could not be purged.', 'fictioneer' ),
            $post_id
          )
        )
      );
		}
	}

  // Invalid
  wp_send_json_error( array( 'notice' => __( 'Invalid request.', 'fictioneer' ) ) );
}
add_action( 'wp_ajax_fictioneer_ajax_purge_schema', 'fictioneer_ajax_purge_schema' );

/**
 * AJAX: Purge schemas for all posts and pages
 *
 * @since Fictioneer 5.7.2
 */

function fictioneer_ajax_purge_all_schemas() {
  // Validate
  if ( ! fictioneer_validate_settings_ajax() ) {
    wp_send_json_error( array( 'notice' => __( 'Invalid request.', 'fictioneer' ) ) );
  }

  // Setup
  $offset = absint( $_POST['offset'] ?? 0 );
  $limit = 100;

  // Query
  $args = array(
    'post_type' => ['post', 'page', 'fcn_story', 'fcn_chapter', 'fcn_collection', 'fcn_recommendation'],
    'post_status' => 'any',
    'fields' => 'ids',
    'ignore_sticky_posts' => 1,
    'offset' => $offset,
    'posts_per_page' => $limit,
    'update_post_meta_cache' => false,
    'update_post_term_cache' => false
  );

  $query = new WP_Query( $args );

  // If post have been found...
  if ( $query->have_posts() ) {
    global $wpdb;

    // Prepare SQL
    $placeholders = implode( ', ', array_fill( 0, count( $query->posts ), '%d' ) );
    $meta_keys = array(
      'fictioneer_schema',
      'fictioneer_seo_title_cache',
      'fictioneer_seo_description_cache',
      'fictioneer_seo_og_image_cache'
    );

    // Execute
    foreach ( $meta_keys as $meta_key ) {
      $wpdb->query(
        $wpdb->prepare(
          "DELETE FROM {$wpdb->postmeta} WHERE post_id IN ($placeholders) AND meta_key = %s",
          array_merge( $query->posts, [ $meta_key ] )
        )
      );
    }

    // Log
    fictioneer_log(
      __( 'Purged all schemas graphs.', 'fictioneer' )
    );

    // ... continue with next batch
    wp_send_json_success(
      array(
        'finished' => false,
        'processed' => count( $query->posts ),
        'total' => $query->found_posts,
        'next_offset' => $offset + $limit
      )
    );
  } else {
    // Purge caches
    fictioneer_purge_all_caches();

    // ... all done
    wp_send_json_success(
      array(
        'finished' => true,
        'processed' => 0,
        'total' => $query->found_posts,
        'next_offset' => -1
      )
    );
  }
}
add_action( 'wp_ajax_fictioneer_ajax_purge_all_schemas', 'fictioneer_ajax_purge_all_schemas' );

?>
