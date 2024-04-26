<?php
/**
 * Partial: Connections Settings
 *
 * @package WordPress
 * @subpackage Fictioneer
 * @since 4.7.0
 */

// Setup
$patreon_tiers = get_option( 'fictioneer_connection_patreon_tiers' );
$patreon_tiers = is_array( $patreon_tiers ) ? $patreon_tiers : [];

?>

<div class="fictioneer-settings">

  <?php fictioneer_settings_header( 'connections' ); ?>

  <div class="fictioneer-settings__content">
    <form method="post" action="options.php" class="fictioneer-form">
      <?php settings_fields( 'fictioneer-settings-connections-group' ); ?>
      <?php do_settings_sections( 'fictioneer-settings-connections-group' ); ?>

      <div class="fictioneer-columns fictioneer-columns--two-columns">

        <div class="fictioneer-card">
          <div class="fictioneer-card__wrapper">
            <h3 class="fictioneer-card__header"><?php _e( 'Discord', 'fictioneer' ); ?></h3>
            <div class="fictioneer-card__content">

              <div class="fictioneer-card__row">
                <?php fictioneer_settings_text_input( 'fictioneer_discord_client_id' ); ?>
              </div>

              <div class="fictioneer-card__row">
                <?php fictioneer_settings_text_input( 'fictioneer_discord_client_secret' ); ?>
              </div>

              <div class="fictioneer-card__row">
                <?php fictioneer_settings_text_input( 'fictioneer_discord_invite_link' ); ?>
              </div>

              <hr>

              <div class="fictioneer-card__row">
                <p><?php printf( __( 'You can set up <a href="%s" target="_blank" rel="noreferrer noopener nofollow">channel webhooks</a> to send notifications to Discord whenever a comment, story, or chapter is first <em>published</em>. This is useful for moderation (instead of emails) and to keep people posted. These are embeds, which can be disabled in Discord.', 'fictioneer' ), 'https://support.discord.com/hc/en-us/articles/228383668-Intro-to-Webhooks' ); ?></p>
              </div>

              <div class="fictioneer-card__row">
                <?php fictioneer_settings_text_input( 'fictioneer_discord_channel_comments_webhook' ); ?>
              </div>

              <div class="fictioneer-card__row">
                <?php fictioneer_settings_text_input( 'fictioneer_discord_channel_stories_webhook' ); ?>
              </div>

              <div class="fictioneer-card__row">
                <?php fictioneer_settings_text_input( 'fictioneer_discord_channel_chapters_webhook' ); ?>
              </div>

            </div>
          </div>
        </div>

        <div class="fictioneer-card">
          <div class="fictioneer-card__wrapper">
            <h3 class="fictioneer-card__header"><?php _e( 'Twitch', 'fictioneer' ); ?></h3>
            <div class="fictioneer-card__content">

              <div class="fictioneer-card__row">
                <?php fictioneer_settings_text_input( 'fictioneer_twitch_client_id' ); ?>
              </div>

              <div class="fictioneer-card__row">
                <?php fictioneer_settings_text_input( 'fictioneer_twitch_client_secret' ); ?>
              </div>

            </div>
          </div>
        </div>

        <div class="fictioneer-card">
          <div class="fictioneer-card__wrapper">
            <h3 class="fictioneer-card__header"><?php _e( 'Google', 'fictioneer' ); ?></h3>
            <div class="fictioneer-card__content">

              <div class="fictioneer-card__row">
                <?php fictioneer_settings_text_input( 'fictioneer_google_client_id' ); ?>
              </div>

              <div class="fictioneer-card__row">
                <?php fictioneer_settings_text_input( 'fictioneer_google_client_secret' ); ?>
              </div>

            </div>
          </div>
        </div>

        <div class="fictioneer-card">
          <div class="fictioneer-card__wrapper">
            <h3 class="fictioneer-card__header"><?php _e( 'Patreon', 'fictioneer' ); ?></h3>
            <div class="fictioneer-card__content">

              <div class="fictioneer-card__row">
                <?php fictioneer_settings_text_input( 'fictioneer_patreon_client_id' ); ?>
              </div>

              <div class="fictioneer-card__row">
                <?php fictioneer_settings_text_input( 'fictioneer_patreon_client_secret' ); ?>
              </div>

              <hr>

              <div class="fictioneer-card__row">
                <?php _e( 'Once you have connected your Patreon client, you can pull your tiers into the theme and assign them to password-protected posts. Eligible users logged-in via Patreon can then simply access the posts. You still need to set a password and changes to tiers on Patreon are not automatically synchronized, but you can always pull them again.', 'fictioneer' ); ?>
              </div>

              <div class="fictioneer-card__row fictioneer-card__row--buttons">
                <a class="button button--secondary" href="<?php echo fictioneer_admin_action( 'fictioneer_connection_get_patreon_tiers' ); ?>"><?php _e( 'Pull Tiers', 'fictioneer' ); ?></a>
                <a class="button button--secondary" href="<?php echo fictioneer_admin_action( 'fictioneer_connection_delete_patreon_tiers' ); ?>"><?php _e( 'Delete Tiers', 'fictioneer' ); ?></a>
              </div>

              <?php if ( $patreon_tiers ) : ?>
                <div class="fictioneer-card__row">
                  <p><strong><?php _e( 'Patreon Tiers', 'fictioneer' ); ?></strong></p>
                  <ul><?php
                    foreach ( $patreon_tiers as $tier ) {
                      printf(
                        _x( '<li>%s (ID: %s | Amount Cents: %s)</li>', 'List of Patreon tiers.', 'fictioneer' ),
                        $tier['title'],
                        $tier['id'],
                        $tier['amount_cents']
                      );
                    }
                  ?></ul>
                </div>
              <?php endif; ?>

            </div>
          </div>
        </div>

        <?php do_action( 'fictioneer_admin_settings_connections' ); ?>

      </div>

      <div class="fictioneer-actions"><?php submit_button(); ?></div>

    </form>
  </div>
</div>
