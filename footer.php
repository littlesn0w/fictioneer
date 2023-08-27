<?php
/**
 * Template Part: Footer
 *
 * Closes the site container and includes the breadcrumbs, footer menu, cookie
 * consent banner, TTS interface, application wide icons, and HTML footer.
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 3.0
 * @see fictioneer_get_breadcrumbs()
 * @see fictioneer_get_footer_copyright_note()
 * @see partials/_tts-interface.php
 * @see partials/_consent-banner.php
 * @see wp_footer()
 *
 * @internal $args['post_id']      Optional. Current post ID.
 * @internal $args['post_type']    Optional. Current post type.
 * @internal $args['breadcrumbs']  Array of breadcrumb tuples with label (0) and link (1).
 */
?>

<?php

// IDs
$page_id = get_queried_object_id();

if ( $page_id != $args['post_id'] ) {
  $args['post_id'] = $page_id;
}

// Null post ID for generated pages
if ( is_archive() || is_search() || is_404() ) {
  $args['post_id'] = null;
}

// Hook after #main closes
do_action( 'fictioneer_after_main', $args );

?>

      <footer class="footer layout-links">
        <div class="footer__wrapper">
          <?php do_action( 'fictioneer_site_footer', $args ); ?>
        </div>
      </footer>
    </div> <!-- #site -->

    <?php
      // Render TTS interface if required
      if (
        ! empty( $args['post_id'] ) &&
        $args['post_type'] == 'fcn_chapter' &&
        ! post_password_required()
      ) {
        get_template_part( 'partials/_tts-interface' );
      }

      // Render cookie banner HTML if required
      if ( get_option( 'fictioneer_cookie_banner' ) ) {
        get_template_part( 'partials/_consent-banner' );
      }
    ?>

    <?php /* Adding the AJAX nonce this way allows caching plugins to update it dynamically. */ ?>
    <input id="fictioneer-nonce" name="fictioneer_nonce" type="hidden" value="<?php echo wp_create_nonce( 'fictioneer_nonce' ); ?>">

    <?php
      // Lightbox container
      if ( get_option( 'fictioneer_enable_lightbox' ) ) {
        echo '<div id="fictioneer-lightbox" class="lightbox"><button type="button" class="lightbox__close" aria-label="' . esc_attr__( 'Close lightbox', 'fictioneer' ) . '">' . fictioneer_get_icon( 'fa-xmark' ) . '</button><i class="fa-solid fa-spinner fa-spin loader" style="--fa-animation-duration: .8s;"></i><div class="lightbox__content target"></div></div>';
      }

      // Fictioneer footer hook
      do_action( 'fictioneer_footer', $args );

      // WordPress footer hook (includes modals)
      wp_footer();
    ?>

    <injectjs /> <!-- Autoptimize insert position -->
  </body>
</html>

<?php

if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
  global $fictioneer_render_start_time;
  echo '<!-- Render Time: ' . number_format( microtime( true ) - $fictioneer_render_start_time, 4 ) . ' seconds. -->';
}

?>
