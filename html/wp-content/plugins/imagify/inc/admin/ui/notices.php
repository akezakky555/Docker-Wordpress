<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

add_action( 'all_admin_notices', '_imagify_warning_empty_api_key_notice' );
/**
 * This warning is displayed when the API key is empty.
 *
 * @since 1.0
 * @author Jonathan Buttigieg
 */
function _imagify_warning_empty_api_key_notice() {
	$current_screen  = get_current_screen();

	if ( ! empty( $current_screen ) && ( 'settings_page_imagify' === $current_screen->base || 'settings_page_imagify-network' === $current_screen->base ) ) {
		return;
	}

	if ( imagify_notice_is_dismissed( 'welcome-steps' ) || get_imagify_option( 'api_key', false ) || ! current_user_can( imagify_get_capacity() ) ) {
		return;
	}
	?>
	<div class="imagify-welcome">
		<div class="imagify-title">
			<img class="imagify-logo" src="<?php echo IMAGIFY_ASSETS_IMG_URL; ?>imagify-logo.png" width="225" height="26" alt="Imagify" /> <small><sup><?php echo IMAGIFY_VERSION; ?></sup></small>
			<span class="baseline">
				<?php _e( 'Welcome to Imagify, the best way to easily optimize your images!', 'imagify' ); ?>
			</span>
			<a href="<?php echo esc_url( get_imagify_admin_url( 'dismiss-notice', 'welcome-steps' ) ); ?>" class="imagify-notice-dismiss imagify-welcome-remove" title="<?php esc_attr_e( 'Dismiss this notice', 'imagify' ); ?>"><span class="dashicons dashicons-dismiss"></span><span class="screen-reader-text"><?php _e( 'Dismiss this notice', 'imagify' ); ?></span></a>
		</div>
		<div class="imagify-settings-section">
			<div class="imagify-columns counter">
				<div class="col-1-3">
					<img src="<?php echo IMAGIFY_ASSETS_IMG_URL; ?>user.svg" width="48" height="48" alt="">
					<div class="imagify-col-content">
						<p class="imagify-col-title"><?php _e( 'Create an Account', 'imagify' ); ?></p>
						<p class="imagify-col-desc"><?php _e( 'Don\'t have an Imagify account yet? Optimize your images by creating an account in a few seconds!', 'imagify' ); ?></p>
						<p>
							<?php wp_nonce_field( 'imagify-signup', 'imagifysignupnonce', false ); ?>
							<a id="imagify-signup" href="<?php echo IMAGIFY_APP_MAIN; ?>/#/register" class="button button-primary"><?php _e( 'Sign up, It\'s FREE!', 'imagify' ); ?></a></p>
					</div>
				</div>
				<div class="col-1-3">
					<img src="<?php echo IMAGIFY_ASSETS_IMG_URL; ?>key.svg" width="48" height="48" alt="">
					<div class="imagify-col-content">
						<p class="imagify-col-title"><?php _e( 'Enter your API Key', 'imagify' ); ?></p>
						<p class="imagify-col-desc">
							<?php
							printf(
								/* translators: 1 is a link tag start, 2 is the link tag end. */
								__( 'Save your API Key you have received by email or you can get it on your %1$sImagify account page%2$s.', 'imagify' ),
								'<a href="' . IMAGIFY_APP_MAIN . '/#/api">',
								'</a>'
							);
							?>
						</p>
						<p>
							<?php wp_nonce_field( 'imagify-check-api-key', 'imagifycheckapikeynonce', false ); ?>
							<a id="imagify-save-api-key" href="<?php echo esc_url( get_imagify_admin_url() ); ?>" class="button button-primary"><?php _e( 'I have my API key', 'imagify' ); ?></a>
						</p>
					</div>
				</div>
				<div class="col-1-3">
					<img src="<?php echo IMAGIFY_ASSETS_IMG_URL; ?>gear.svg" width="48" height="48" alt="">
					<div class="imagify-col-content">
						<p class="imagify-col-title"><?php _e( 'Configure it', 'imagify' ); ?></p>
						<p class="imagify-col-desc"><?php _e( 'It’s almost done! You have just to configure your optimization settings.', 'imagify' ); ?></p>
						<p><a href="<?php echo esc_url( get_imagify_admin_url() ); ?>" class="button button-primary"><?php _e( 'Go to Settings', 'imagify' ); ?></a></p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}

add_action( 'all_admin_notices', '_imagify_warning_wrong_api_key_notice' );
/**
 * This warning is displayed when the API key is empty.
 *
 * @since 1.0
 * @author Jonathan Buttigieg
 */
function _imagify_warning_wrong_api_key_notice() {
	$current_screen = get_current_screen();

	if ( empty( $current_screen ) || 'media_page_imagify-bulk-optimization' !== $current_screen->base ) {
		return;
	}

	$api_key = get_imagify_option( 'api_key', false );

	if ( imagify_notice_is_dismissed( 'wrong-api-key' ) || empty( $api_key ) || imagify_valid_key() || ! current_user_can( imagify_get_capacity() ) ) {
		return;
	}
	?>
	<div class="clear"></div>
	<div class="error imagify-notice below-h2">
		<div class="imagify-notice-logo">
			<img class="imagify-logo" src="<?php echo IMAGIFY_ASSETS_IMG_URL; ?>imagify-logo.png" width="138" height="16" alt="Imagify" />
		</div>
		<div class="imagify-notice-content">
			<p class="imagify-notice-title"><strong><?php _e( 'Your API key isn\'t valid!', 'imagify' ); ?></strong></p>
			<p>
				<?php wp_nonce_field( 'imagify-signup', 'imagifysignupnonce', false ); ?>
				<?php
				printf(
					/* translators: 1 and 2 are link tag starts, 3 is a link tag end. */
					__( 'Go to your Imagify account page to get your API Key and specify it on %1$syour settings%3$s or %2$screate an account for free%3$s if you don\'t have one yet.', 'imagify' ),
					'<a href="' . get_imagify_admin_url() . '">',
					'<a id="imagify-signup" href="' . IMAGIFY_WEB_MAIN . '">',
					'</a>'
				);
				?>
			</p>
		</div>
		<a href="<?php echo esc_url( get_imagify_admin_url( 'dismiss-notice', 'wrong-api-key' ) ); ?>" class="imagify-notice-dismiss notice-dismiss" title="<?php esc_attr_e( 'Dismiss this notice', 'imagify' ); ?>"><span class="screen-reader-text"><?php _e( 'Dismiss this notice', 'imagify' ); ?></span></a>
	</div>

	<?php
}

add_action( 'all_admin_notices', '_imagify_warning_plugins_to_deactivate_notice' );
/**
 * This warning is displayed when some plugins may conflict with Imagify
 *
 * @since 1.0
 * @author Jonathan Buttigieg
 */
function _imagify_warning_plugins_to_deactivate_notice() {
	$plugins_to_deactivate = array();

	// Deactivate all plugins who can cause conflicts with Imagify.
	$plugins = array(
		'wp-smush'     => 'wp-smushit/wp-smush.php', // WP Smush.
		'wp-smush-pro' => 'wp-smush-pro/wp-smush.php', // WP Smush Pro.
		'kraken'       => 'kraken-image-optimizer/kraken.php', // Kraken.io.
		'tinypng'      => 'tiny-compress-images/tiny-compress-images.php', // TinyPNG.
		'shortpixel'   => 'shortpixel-image-optimiser/wp-shortpixel.php', // Shortpixel.
		'ewww'         => 'ewww-image-optimizer/ewww-image-optimizer.php', // EWWW Image Optimizer.
		'ewww-cloud'   => 'ewww-image-optimizer-cloud/ewww-image-optimizer-cloud.php', // EWWW Image Optimizer Cloud.
		'imagerecycle' => 'imagerecycle-pdf-image-compression/wp-image-recycle.php', // ImageRecycle.
	);

	/**
	 * Filter the recommended plugins to deactivate to prevent conflicts.
	 *
	 * @since 1.0
	 *
	 * @param string $plugins List of recommended plugins to deactivate.
	*/
	$plugins = apply_filters( 'imagify_plugins_to_deactivate', $plugins );
	$plugins = array_filter( $plugins, 'is_plugin_active' );

	if ( ! $plugins || ! current_user_can( imagify_get_capacity() ) ) {
		return;
	}
	?>
	<div class="clear"></div>
	<div class="imagify-notice error below-h2">
		<div class="imagify-notice-logo">
			<img class="imagify-logo" src="<?php echo IMAGIFY_ASSETS_IMG_URL; ?>imagify-logo.png" width="138" height="16" alt="Imagify" />
		</div>
		<div class="imagify-notice-content">
			<p><?php _e( 'The following plugins are not compatible with this plugin and may cause unexpected results:', 'imagify' ); ?></p>

			<ul class="imagify-plugins-error">
			<?php
			foreach ( $plugins as $plugin ) {
				$plugin_data    = get_plugin_data( WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $plugin );
				$deactivate_url = esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=imagify_deactivate_plugin&plugin=' . rawurlencode( $plugin ) ), 'imagifydeactivatepluginnonce' ) );
				echo '<li>' . $plugin_data['Name'] . '</span> <a href="' . $deactivate_url . '" class="button button-mini alignright">' . __( 'Deactivate', 'imagify' ) . '</a></li>';
			}
			?>
			</ul>
		</div>
	</div>
	<?php
}

add_action( 'all_admin_notices', '_imagify_http_block_external_notice' );
/**
 * This notice is displayed when external HTTP requests are blocked via the WP_HTTP_BLOCK_EXTERNAL constant.
 *
 * @since 1.0
 * @author Jonathan Buttigieg
 */
function _imagify_http_block_external_notice() {
	$current_screen  = get_current_screen();

	if ( empty( $current_screen ) || ( 'settings_page_imagify' === $current_screen->base || 'settings_page_imagify-network' === $current_screen->base ) ) {
		return;
	}

	if ( imagify_notice_is_dismissed( 'http-block-external' ) || ! current_user_can( imagify_get_capacity( true ) ) || ! is_imagify_blocked() ) {
		return;
	}
	?>
	<div class="clear"></div>
	<div class="error imagify-notice below-h2">
		<div class="imagify-notice-logo">
			<img class="imagify-logo" src="<?php echo IMAGIFY_ASSETS_IMG_URL; ?>imagify-logo.png" width="138" height="16" alt="Imagify" />
		</div>
		<div class="imagify-notice-content">
			<p class="imagify-notice-title"><strong><?php _e( 'The external HTTP requests are blocked!', 'imagify' ); ?></strong></p>
			<p>
				<?php _e( 'You defined the <code>WP_HTTP_BLOCK_EXTERNAL</code> constant in the <code>wp-config.php</code> to block all external HTTP requests.', 'imagify' ); ?>
			</p>
			<p>
				<?php _e( 'To optimize your images, you have to put the following code in your <code>wp-config.php</code> file so that it works correctly.', 'imagify' ); ?><br/>
				<?php _e( 'Click on the field and press Ctrl-A to select all.', 'imagify' ); ?>
			</p>
			<p><textarea readonly="readonly" class="large-text readonly" rows="1">define( 'WP_ACCESSIBLE_HOSTS', '*.imagify.io' );</textarea></p>
		</div>
		<a href="<?php echo esc_url( get_imagify_admin_url( 'dismiss-notice', 'http-block-external' ) ); ?>" class="imagify-notice-dismiss notice-dismiss" title="<?php esc_attr_e( 'Dismiss this notice', 'imagify' ); ?>"><span class="screen-reader-text"><?php _e( 'Dismiss this notice', 'imagify' ); ?></span></a>
	</div>
	<?php
}

add_action( 'all_admin_notices', '_imagify_warning_grid_view_notice' );
/**
 * This warning is displayed when the grid view is active on the library.
 *
 * @since 1.0.2
 * @author Jonathan Buttigieg
 */
function _imagify_warning_grid_view_notice() {
	global $wp_version;

	$current_screen = get_current_screen();

	if ( empty( $current_screen ) || 'upload' !== $current_screen->base ) {
		return;
	}

	$media_library_mode = get_user_option( 'media_library_mode', get_current_user_id() );

	if ( imagify_notice_is_dismissed( 'grid-view' ) || ! current_user_can( 'upload_files' ) || 'list' === $media_library_mode || version_compare( $wp_version, '4.0' ) < 0 ) {
		return;
	}

	// Don't display the notice if the API key isn't valid.
	if ( ! imagify_valid_key() ) {
		return;
	}
	?>
	<div class="clear"></div>
	<div class="error imagify-notice below-h2">
		<div class="imagify-notice-logo">
			<img class="imagify-logo" src="<?php echo IMAGIFY_ASSETS_IMG_URL; ?>imagify-logo.png" width="138" height="16" alt="Imagify" />
		</div>
		<div class="imagify-notice-content">
			<p class="imagify-notice-title"><strong><?php _e( 'You\'re missing out!', 'imagify' ); ?></strong></p>
			<p><?php _e( 'Use the List view to optimize images with Imagify.', 'imagify' ); ?></p>
			<p><a href="<?php echo esc_url( admin_url( 'upload.php?mode=list' ) ); ?>"><?php _e( 'Switch to the List View', 'imagify' ); ?></a></p>
		</div>
		<a href="<?php echo esc_url( get_imagify_admin_url( 'dismiss-notice', 'grid-view' ) ); ?>" class="imagify-notice-dismiss notice-dismiss" title="<?php esc_attr_e( 'Dismiss this notice', 'imagify' ); ?>"><span class="screen-reader-text"><?php _e( 'Dismiss this notice', 'imagify' ); ?></span></a>
	</div>
	<?php
}

add_action( 'all_admin_notices', '_imagify_warning_over_quota_notice' );
/**
 * This warning is displayed to warn the user that its quota is consumed for the current month.
 *
 * @since 1.1.1
 * @author Jonathan Buttigieg
 */
function _imagify_warning_over_quota_notice() {
	$current_screen = get_current_screen();

	if ( empty( $current_screen ) || ( 'media_page_imagify-bulk-optimization' !== $current_screen->base && 'settings_page_imagify' !== $current_screen->base && 'settings_page_imagify-network' !== $current_screen->base ) ) {
		return;
	}

	if ( imagify_notice_is_dismissed( 'free-over-quota' ) || ! current_user_can( imagify_get_capacity() ) ) {
		return;
	}

	$user = new Imagify_User();

	// Don't display the notice if the user doesn't use all his quota or the API key isn't valid.
	if ( ! $user->is_over_quota() || ! imagify_valid_key() ) {
		return;
	}

	?>
	<div class="clear"></div>
	<div class="error imagify-notice below-h2">
		<div class="imagify-notice-logo">
			<img class="imagify-logo" src="<?php echo IMAGIFY_ASSETS_IMG_URL; ?>imagify-logo.png" width="138" height="16" alt="Imagify" />
		</div>
		<div class="imagify-notice-content">
			<p class="imagify-notice-title"><strong><?php _e( 'Oops, It\'s Over!', 'imagify' ); ?></strong></p>
			<p><?php
				printf(
					/* translators: 1 is a "bold" tag start, 2 is a formatted data quota, 3 is a date, 4 is the "bold" tag end. */
					__( 'You have consumed all your credit for this month. You will have %1$s%2$s back on %3$s%4$s.', 'imagify' ),
					'<strong>',
					size_format( $user->quota * 1048576 ),
					date_i18n( get_option( 'date_format' ), strtotime( $user->next_date_update ) ),
					'</strong>'
				);
				echo '<br/><br/>';
				printf(
					/* translators: 1 is a link tag start, 2 is the link tag end. */
					__( 'To continue to optimize your images, log in to your Imagify account to %1$sbuy a pack or subscribe to a plan%2$s.', 'imagify' ),
					'<a href="' . IMAGIFY_APP_MAIN . '/#/subscription">',
					'</a>'
				);
			?></p>
		</div>
		<a href="<?php echo get_imagify_admin_url( 'dismiss-notice', 'free-over-quota' ); ?>" class="imagify-notice-dismiss notice-dismiss" title="<?php esc_attr_e( 'Dismiss this notice', 'imagify' ); ?>"><span class="screen-reader-text"><?php _e( 'Dismiss this notice', 'imagify' ); ?></span></a>
	</div>
	<?php
}

add_action( 'admin_notices', '_imagify_rocket_notice' );
/**
 * Add a message about WP Rocket on the "Bulk Optimization" screen.
 *
 * @since 2.7
 * @author Jonathan Buttigieg
 */
function _imagify_rocket_notice() {
	$current_screen = get_current_screen();

	if ( empty( $current_screen ) || 'media_page_imagify-bulk-optimization' !== $current_screen->base ) {
		return;
	}

	if ( defined( 'WP_ROCKET_VERSION' ) || imagify_notice_is_dismissed( 'wp-rocket' ) || ! current_user_can( imagify_get_capacity( true ) ) ) {
		return;
	}

	$dismiss_url  = get_imagify_admin_url( 'dismiss-notice', 'wp-rocket' );
	$coupon_code  = 'IMAGIFY20';
	$wprocket_url = 'http://wp-rocket.me/';

	switch ( get_locale() ) {
		case 'fr_FR' :
			$wprocket_url = 'http://wp-rocket.me/fr/';
			break;
		case 'es_ES' :
			$wprocket_url = 'http://wp-rocket.me/es/';
			break;
		case 'it_IT' :
			$wprocket_url = 'http://wp-rocket.me/it/';
			break;
		case 'de_DE' :
			$wprocket_url = 'http://wp-rocket.me/de/';
			break;
	}

	$wprocket_url .= '?utm_source=imagify-coupon&utm_medium=plugin&utm_campaign=imagify';
	?>

	<div class="updated imagify-rkt-notice">
		<a href="<?php echo $dismiss_url; ?>" class="imagify-cross"><span class="dashicons dashicons-no"></span></a>

		<p class="imagify-rkt-logo">
			<img src="<?php echo IMAGIFY_ASSETS_IMG_URL ?>logo-wprocket.png" srcset="<?php echo IMAGIFY_ASSETS_IMG_URL ?>logo-wprocket2x.png 2x" alt="WP Rocket" width="118" height="32">
		</p>
		<p class="imagify-rkt-msg">
			<?php
			esc_html_e( 'Discover the best caching plugin to speed up your website.', 'imagify' );
			echo '<br>';
			printf(
				/* translators: 1 is a "bold" tag start, 2 is a pourcentage, 3 is the "bold" tag end, 4 is a coupon code. */
				esc_html__( '%1$sGet %2$s off%3$s with this coupon code: %4$s', 'imagify' ),
				'<strong>', '20%', '</strong>', $coupon_code
			);
			?>
		</p>
		<p class="imagify-rkt-coupon">
			<span class="imagify-rkt-coupon-code"><?php echo $coupon_code; ?></span>
		</p>
		<p class="imagify-rkt-cta">
			<a href="<?php echo $wprocket_url; ?>" class="button button-primary tgm-plugin-update-modal"><?php esc_html_e( 'Get WP Rocket now', 'imagify' ); ?></a>
		</p>
	</div>

	<?php
}

add_action( 'all_admin_notices', '_imagify_rating_notice' );
/**
 * This notice is displayed to rate the plugin after 100 optimization & 7 days after the first installation.
 *
 * @since 1.4.2
 * @author Jonathan Buttigieg
 */
function _imagify_rating_notice() {
	$current_screen = get_current_screen();

	if ( empty( $current_screen ) || ( 'media_page_imagify-bulk-optimization' !== $current_screen->base && 'upload' !== $current_screen->base && 'media' !== $current_screen->base ) ) {
		return;
	}

	if ( imagify_notice_is_dismissed( 'rating' ) || ! current_user_can( imagify_get_capacity( true ) ) ) {
		return;
	}

	$user_images_count = (int) get_site_transient( 'imagify_user_images_count' );

	if ( ! $user_images_count || get_site_transient( 'imagify_seen_rating_notice' ) ) {
		return;
	}
	?>
	<div class="clear"></div>
	<div class="updated imagify-notice below-h2">
		<div class="imagify-notice-logo">
			<img class="imagify-logo" src="<?php echo IMAGIFY_ASSETS_IMG_URL; ?>imagify-logo.png" width="138" height="16" alt="Imagify" />
		</div>
		<div class="imagify-notice-content">
			<p><?php
				printf(
					/* translators: 1 is a "bold" tag start, 2 is the "bold" tag end, 3 is a formatted number (don't use %3$d). */
					__( '%1$sCongratulations%2$s, you have optimized %1$s%3$s images%2$s and improved your website\'s speed by reducing your images size.', 'imagify' ),
					'<strong>',
					'</strong>',
					number_format_i18n( $user_images_count )
				);
			?></p>
			<p class="imagify-rate-us">
				<?php
				$imagify_rate_url = 'https://wordpress.org/support/view/plugin-reviews/imagify?rate=5#postform';

				printf(
					/* translators: 1 is a "bold" tag start, 2 is the "bold" tag end + a line break tag, 3 is a link tag start, 4 is the link tag end. */
					__( '%1$sDo you like this plugin?%2$s Please take a few seconds to %3$srate it on WordPress.org%4$s!', 'imagify' ),
					'<strong>',
					'</strong><br />',
					'<a href="' . $imagify_rate_url . '">',
					'</a>'
				);
				?>
				<br>
				<a class="stars" href="<?php echo $imagify_rate_url; ?>">☆☆☆☆☆</a>
			</p>
		</div>
		<a href="<?php echo esc_url( get_imagify_admin_url( 'dismiss-notice', 'rating' ) ); ?>" class="imagify-notice-dismiss notice-dismiss" title="<?php esc_attr_e( 'Dismiss this notice', 'imagify' ); ?>"><span class="screen-reader-text"><?php _e( 'Dismiss this notice', 'imagify' ); ?></span></a>
	</div>
	<?php
}

add_action( 'imagify_dismiss_notice', '_imagify_clear_scheduled_rating' );
/**
 * Stop the rating cron when the notice is dismissed.
 *
 * @param string $notice The notice name.
 */
function _imagify_clear_scheduled_rating( $notice ) {
	if ( 'rating' === $notice ) {
		set_site_transient( 'do_imagify_rating_cron', 'no' );
		wp_clear_scheduled_hook( 'imagify_rating_event' );
	}
}
