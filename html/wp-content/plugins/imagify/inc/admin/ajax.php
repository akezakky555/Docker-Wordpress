<?php
defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

/** --------------------------------------------------------------------------------------------- */
/** MANUAL OPTIMIZATION ========================================================================= */
/** --------------------------------------------------------------------------------------------- */

add_action( 'wp_ajax_imagify_manual_upload',    '_do_admin_post_imagify_manual_upload' );
add_action( 'admin_post_imagify_manual_upload', '_do_admin_post_imagify_manual_upload' );
/**
 * Process all thumbnails of a specific image with Imagify with the manual method.
 *
 * @since 1.0
 * @author Jonathan Buttigieg
 */
function _do_admin_post_imagify_manual_upload() {
	if ( defined( 'DOING_AJAX' ) ) {
		check_ajax_referer( 'imagify-manual-upload' );
	} else {
		check_admin_referer( 'imagify-manual-upload' );
	}

	if ( ! isset( $_GET['attachment_id'], $_GET['context'] ) || ! current_user_can( 'upload_files' ) ) {
		if ( defined( 'DOING_AJAX' ) ) {
			wp_send_json_error();
		} else {
			wp_nonce_ays( '' );
		}
	}

	$context       = esc_html( $_GET['context'] );
	$attachment_id = absint( $_GET['attachment_id'] );
	$class_name    = get_imagify_attachment_class_name( $context, $attachment_id, 'imagify_manual_upload' );
	$attachment    = new $class_name( $attachment_id );

	// Optimize it!!!!!
	$attachment->optimize();

	if ( ! defined( 'DOING_AJAX' ) ) {
		wp_safe_redirect( wp_get_referer() );
		die();
	}

	// Return the optimization statistics.
	$output = get_imagify_attachment_optimization_text( $attachment, $context );
	wp_send_json_success( $output );
}

add_action( 'wp_ajax_imagify_manual_override_upload',    '_do_admin_post_imagify_manual_override_upload' );
add_action( 'admin_post_imagify_manual_override_upload', '_do_admin_post_imagify_manual_override_upload' );
/**
 * Process all thumbnails of a specific image with Imagify with a different optimization level.
 *
 * @since 1.0
 * @author Jonathan Buttigieg
 */
function _do_admin_post_imagify_manual_override_upload() {
	if ( defined( 'DOING_AJAX' ) ) {
		check_ajax_referer( 'imagify-manual-override-upload' );
	} else {
		check_admin_referer( 'imagify-manual-override-upload' );
	}

	if ( ! isset( $_GET['attachment_id'], $_GET['context'] ) || ! current_user_can( 'upload_files' ) ) {
		if ( defined( 'DOING_AJAX' ) ) {
			wp_send_json_error();
		} else {
			wp_nonce_ays( '' );
		}
	}

	$context       = esc_html( $_GET['context'] );
	$attachment_id = absint( $_GET['attachment_id'] );
	$class_name    = get_imagify_attachment_class_name( $context, $attachment_id, 'imagify_manual_override_upload' );
	$attachment    = new $class_name( $attachment_id );

	// Restore the backup file.
	$attachment->restore();

	// Optimize it!!!!!
	$attachment->optimize( (int) $_GET['optimization_level'] );

	if ( ! defined( 'DOING_AJAX' ) ) {
		wp_safe_redirect( wp_get_referer() );
		die();
	}

	// Return the optimization statistics.
	$output = get_imagify_attachment_optimization_text( $attachment, $context );
	wp_send_json_success( $output );
}

add_action( 'wp_ajax_imagify_restore_upload',    '_do_admin_post_imagify_restore_upload' );
add_action( 'admin_post_imagify_restore_upload', '_do_admin_post_imagify_restore_upload' );
/**
 * Process a restoration to the original attachment.
 *
 * @since 1.0
 * @author Jonathan Buttigieg
 */
function _do_admin_post_imagify_restore_upload() {
	if ( defined( 'DOING_AJAX' ) ) {
		check_ajax_referer( 'imagify-restore-upload' );
	} else {
		check_admin_referer( 'imagify-restore-upload' );
	}

	if ( ! isset( $_GET['attachment_id'], $_GET['context'] ) || ! current_user_can( 'upload_files' ) ) {
		if ( defined( 'DOING_AJAX' ) ) {
			wp_send_json_error();
		} else {
			wp_nonce_ays( '' );
		}
	}

	$context       = esc_html( $_GET['context'] );
	$attachment_id = absint( $_GET['attachment_id'] );
	$class_name    = get_imagify_attachment_class_name( $context, $attachment_id, 'imagify_restore_upload' );
	$attachment    = new $class_name( $attachment_id );

	// Restore the backup file.
	$attachment->restore();

	if ( ! defined( 'DOING_AJAX' ) ) {
		wp_safe_redirect( wp_get_referer() );
		die();
	}

	// Return the optimization button.
	$output = '<a id="imagify-upload-' . $attachment->id . '" href="' . esc_url( get_imagify_admin_url( 'manual-upload', array( 'attachment_id' => $attachment->id ) ) ) . '" class="button-primary button-imagify-manual-upload" data-waiting-label="' . esc_attr__( 'Optimizing...', 'imagify' ) . '">' . __( 'Optimize', 'imagify' ) . '</a>';
	wp_send_json_success( $output );
}

add_action( 'wp_ajax_imagify_bulk_upload', '_do_wp_ajax_imagify_bulk_upload' );
/**
 * Process all thumbnails of a specific image with Imagify with the bulk method.
 *
 * @since 1.0
 * @author Jonathan Buttigieg
 */
function _do_wp_ajax_imagify_bulk_upload() {
	check_ajax_referer( 'imagify-bulk-upload', 'imagifybulkuploadnonce' );

	if ( ! isset( $_POST['image'], $_POST['context'] ) || ! current_user_can( 'upload_files' ) ) {
		wp_send_json_error();
	}

	$context            = esc_html( $_POST['context'] );
	$attachment_id      = absint( $_POST['image'] );
	$class_name         = get_imagify_attachment_class_name( $context, $attachment_id, 'imagify_bulk_upload' );
	$attachment         = new $class_name( $attachment_id );
	$optimization_level = get_transient( 'imagify_bulk_optimization_level' );

	// Restore it if the optimization level is updated.
	if ( $optimization_level !== $attachment->get_optimization_level() ) {
		$attachment->restore();
	}

	// Optimize it!!!!!
	$attachment->optimize( $optimization_level );

	// Return the optimization statistics.
	$fullsize_data = $attachment->get_size_data();
	$stats_data    = $attachment->get_stats_data();
	$user          = new Imagify_User();
	$data          = array();

	if ( ! $attachment->is_optimized() ) {
		$data['success'] = false;
		$data['error']   = $fullsize_data['error'];

		wp_send_json_error( $data );
	}

	$data['success']               = true;
	$data['original_size']         = $fullsize_data['original_size'];
	$data['new_size']              = $fullsize_data['optimized_size'];
	$data['percent']               = $fullsize_data['percent'];
	$data['overall_saving'] 	   = $stats_data['original_size'] - $stats_data['optimized_size'];
	$data['original_overall_size'] = $stats_data['original_size'];
	$data['new_overall_size']      = $stats_data['optimized_size'];
	$data['thumbnails']            = $attachment->get_optimized_sizes_count();

	wp_send_json_success( $data );
}

/** --------------------------------------------------------------------------------------------- */
/** AUTOMATIC OPTIMIZATION ====================================================================== */
/** --------------------------------------------------------------------------------------------- */

add_action( 'wp_ajax_imagify_async_optimize_upload_new_media', '_do_admin_post_async_optimize_upload_new_media' );
/**
 * Optimize image on picture uploading with async request.
 *
 * @since 1.5
 * @author Julio Potier
 * @see _imagify_optimize_attachment()
 */
function _do_admin_post_async_optimize_upload_new_media() {
	if ( ! isset( $_POST['_ajax_nonce'], $_POST['attachment_id'], $_POST['metadata'], $_POST['context'] ) ) { // WPCS: CSRF ok.
		return;
	}

	check_ajax_referer( 'new_media-' . $_POST['attachment_id'] );

	$context       = esc_html( $_POST['context'] );
	$attachment_id = absint( $_POST['attachment_id'] );
	$class_name    = get_imagify_attachment_class_name( $context, $attachment_id, 'imagify_async_optimize_upload_new_media' );
	$attachment    = new $class_name( $attachment_id );

	// Optimize it!!!!!
	$attachment->optimize( null, $_POST['metadata'] );
	die( 1 );
}

add_action( 'wp_ajax_imagify_async_optimize_save_image_editor_file', '_do_admin_post_async_optimize_save_image_editor_file' );
/**
 * Optimize image on picture editing (resize, crop...) with async request.
 *
 * @since 1.4
 * @author Julio Potier
 */
function _do_admin_post_async_optimize_save_image_editor_file() {
	if ( ! isset( $_POST['do'], $_POST['postid'] ) ) { // WPCS: CSRF ok.
		return;
	}

	check_ajax_referer( 'image_editor-' . $_POST['postid'] );

	$attachment_id = absint( $_POST['postid'] );

	if ( ! get_post_meta( $attachment_id, '_imagify_data', true ) ) {
		return;
	}

	$optimization_level = (int) get_post_meta( $attachment_id, '_imagify_optimization_level', true );
	$class_name         = get_imagify_attachment_class_name( 'wp', $attachment_id, 'wp_ajax_imagify_async_optimize_save_image_editor_file' );
	$attachment         = new $class_name( $attachment_id );
	$metadata           = wp_get_attachment_metadata( $attachment_id );

	// Remove old optimization data.
	$attachment->delete_imagify_data();

	if ( 'restore' === $_POST['do'] ) {
		// Restore the backup file.
		$attachment->restore();

		// Get old metadata to regenerate all thumbnails.
		$metadata     = array( 'sizes' => array() );
		$backup_sizes = (array) get_post_meta( $attachment_id, '_wp_attachment_backup_sizes', true );

		foreach ( $backup_sizes as $size_key => $size_data ) {
			$size_key = str_replace( '-origin', '' , $size_key );
			$metadata['sizes'][ $size_key ] = $size_data;
		}
	}

	// Optimize it!!!!!
	$attachment->optimize( $optimization_level, $metadata );
	die( 1 );
}

/** --------------------------------------------------------------------------------------------- */
/** VARIOUS FOR OPTIMIZATION ==================================================================== */
/** --------------------------------------------------------------------------------------------- */

add_action( 'wp_ajax_imagify_get_unoptimized_attachment_ids', '_do_wp_ajax_imagify_get_unoptimized_attachment_ids' );
/**
 * Get all unoptimized attachment ids.
 *
 * @since 1.0
 * @author Jonathan Buttigieg
 */
function _do_wp_ajax_imagify_get_unoptimized_attachment_ids() {
	global $wpdb;

	check_ajax_referer( 'imagify-bulk-upload', 'imagifybulkuploadnonce' );

	if ( ! current_user_can( 'upload_files' ) ) {
		wp_send_json_error();
	}

	if ( ! imagify_valid_key() ) {
		wp_send_json_error( array( 'message' => 'invalid-api-key' ) );
	}

	$user = new Imagify_User();

	if ( $user->is_over_quota() ) {
		wp_send_json_error( array( 'message' => 'over-quota' ) );
	}

	@set_time_limit( 0 );

	$optimization_level = (int) $_GET['optimization_level'];
	$optimization_level = ( -1 !== $optimization_level ) ? $optimization_level : get_imagify_option( 'optimization_level', 1 );
	$optimization_level = (int) $optimization_level;

	$meta_query = array(
		'relation' => 'OR',
		array(
			'key'     => '_imagify_optimization_level',
			'value'   => $optimization_level,
			'compare' => '!=',
		),
		array(
			'key'     => '_imagify_optimization_level',
			'compare' => 'NOT EXISTS',
		),
		array(
			'key'     => '_imagify_status',
			'value'   => 'error',
			'compare' => '=',
		),
	);

	/**
	 * Filter the unoptimized attachments limit query.
	 *
	 * @since 1.4.4
	 *
	 * @param int The limit (-1 for unlimited).
	 */
	$unoptimized_attachment_limit = apply_filters( 'imagify_unoptimized_attachment_limit', 10000 );

	$args = array(
		'fields'                 => 'ids',
		'post_type'              => 'attachment',
		'post_status'            => 'any',
		'post_mime_type'         => get_imagify_mime_type(),
		'meta_query'             => $meta_query,
		'posts_per_page'         => $unoptimized_attachment_limit,
		'orderby'                => 'ID',
		'order'                  => 'DESC',
		'no_found_rows'          => true,
		'update_post_term_cache' => false,
	);

	$data        = array();
	$attachments = new WP_Query( $args );
	$ids         = $attachments->posts;
	$ids         = array_filter( (array) $ids );
	$sql_ids     = implode( ',', $ids );

	if ( empty( $sql_ids ) ) {
		wp_send_json_error( array( 'message' => 'no-images' ) );
	}

	// Get attachments filename.
	$attachments_filename = $wpdb->get_results( // WPCS: unprepared SQL ok.
		"SELECT pm.post_id as id, pm.meta_value as value
		 FROM $wpdb->postmeta as pm
		 WHERE pm.meta_key = '_wp_attached_file'
			 AND pm.post_id IN ($sql_ids)
		 ORDER BY pm.post_id DESC",
		ARRAY_A
	);

	$attachments_filename = imagify_query_results_combine( $ids, $attachments_filename );

	// Get attachments data.
	$attachments_data = $wpdb->get_results( // WPCS: unprepared SQL ok.
		"SELECT pm.post_id as id, pm.meta_value as value
		 FROM $wpdb->postmeta as pm
		 WHERE pm.meta_key = '_imagify_data'
			 AND pm.post_id IN ($sql_ids)
		 ORDER BY pm.post_id DESC",
		ARRAY_A
	);

	$attachments_data = imagify_query_results_combine( $ids, $attachments_data );
	$attachments_data = array_map( 'maybe_unserialize', $attachments_data );

	// Get attachments optimization level.
	$attachments_optimization_level = $wpdb->get_results( // WPCS: unprepared SQL ok.
		"SELECT pm.post_id as id, pm.meta_value as value
		 FROM $wpdb->postmeta as pm
		 WHERE pm.meta_key = '_imagify_optimization_level'
			 AND pm.post_id IN ($sql_ids)
		 ORDER BY pm.post_id DESC",
		ARRAY_A
	);

	$attachments_optimization_level = imagify_query_results_combine( $ids, $attachments_optimization_level );

	// Get attachments status.
	$attachments_status = $wpdb->get_results( // WPCS: unprepared SQL ok.
		"SELECT pm.post_id as id, pm.meta_value as value
		 FROM $wpdb->postmeta as pm
		 WHERE pm.meta_key = '_imagify_status'
			 AND pm.post_id IN ($sql_ids)
		 ORDER BY pm.post_id DESC",
		ARRAY_A
	);

	$attachments_status = imagify_query_results_combine( $ids, $attachments_status );

	// Save the optimization level in a transient to retrieve it later during the process.
	set_transient( 'imagify_bulk_optimization_level', $optimization_level );

	foreach ( $ids as $id ) {
		/** This filter is documented in inc/functions/process.php. */
		$file_path = apply_filters( 'imagify_file_path', get_imagify_attached_file( $attachments_filename[ $id ] ) );

		if ( ! file_exists( $file_path ) ) {
			continue;
		}

		$attachment_data  = isset( $attachments_data[ $id ] ) ? $attachments_data[ $id ] : false;
		$attachment_error = '';

		if ( isset( $attachment_data['sizes']['full']['error'] ) ) {
			$attachment_error = $attachment_data['sizes']['full']['error'];
		}

		$attachment_error              = trim( $attachment_error );
		$attachment_status             = isset( $attachments_status[ $id ] ) ? $attachments_status[ $id ] : false;
		$attachment_optimization_level = isset( $attachments_optimization_level[ $id ] ) ? $attachments_optimization_level[ $id ] : false;
		$attachment_backup_path        = get_imagify_attachment_backup_path( $file_path );

		// Don't try to re-optimize if the optimization level is still the same.
		if ( $optimization_level === $attachment_optimization_level && is_string( $attachment_error ) ) {
			continue;
		}

		// Don't try to re-optimize if there is no backup file.
		if ( 'success' === $attachment_status && $optimization_level !== $attachment_optimization_level && ! file_exists( $attachment_backup_path ) ) {
			continue;
		}

		// Don't try to re-optimize images already compressed.
		if ( 'already_optimized' === $attachment_status && $attachment_optimization_level >= $optimization_level ) {
			continue;
		}

		// Don't try to re-optimize images with an empty error message.
		if ( 'error' === $attachment_status && empty( $attachment_error ) ) {
			continue;
		}

		$data[ '_' . $id ] = get_imagify_attachment_url( $attachments_filename[ $id ] );
	} // End foreach().

	if ( $data ) {
		wp_send_json_success( $data );
	}

	wp_send_json_error( array( 'message' => 'no-images' ) );
}

/** --------------------------------------------------------------------------------------------- */
/** IMAGIFY ACCOUNT ============================================================================= */
/** --------------------------------------------------------------------------------------------- */

add_action( 'wp_ajax_imagify_signup', '_do_wp_ajax_imagify_signup' );
/**
 * Create a new Imagify account.
 *
 * @since 1.0
 * @author Jonathan Buttigieg
 */
function _do_wp_ajax_imagify_signup() {
	check_ajax_referer( 'imagify-signup', 'imagifysignupnonce' );

	if ( ! isset( $_GET['email'] ) ) {
		wp_send_json_error();
	}

	$data = array(
		'email'    => $_GET['email'],
		'password' => wp_generate_password( 12, false ),
		'lang'     => get_locale(),
	);

	$response = add_imagify_user( $data );

	if ( is_wp_error( $response ) ) {
		wp_send_json_error( $response->get_error_message() );
	}

	wp_send_json_success();
}

add_action( 'wp_ajax_imagify_check_api_key_validity', '_do_wp_ajax_imagify_check_api_key_validity' );
/**
 * Process an API key check validity.
 *
 * @since 1.0
 * @author Jonathan Buttigieg
 */
function _do_wp_ajax_imagify_check_api_key_validity() {
	check_ajax_referer( 'imagify-check-api-key', 'imagifycheckapikeynonce' );

	if ( ! isset( $_GET['api_key'] ) ) {
		wp_send_json_error();
	}

	$response = get_imagify_status( $_GET['api_key'] );

	if ( is_wp_error( $response ) ) {
		wp_send_json_error( $response->get_error_message() );
	}

	$options            = get_site_option( IMAGIFY_SETTINGS_SLUG );
	$options['api_key'] = sanitize_key( $_GET['api_key'] );

	update_site_option( IMAGIFY_SETTINGS_SLUG, $options );

	wp_send_json_success();
}

add_action( 'wp_ajax_imagify_get_admin_bar_profile', '_do_wp_ajax_imagify_get_admin_bar_profile' );
/**
 * Get admin bar profile output
 *
 * @since 1.2.3
 * @author Jonathan Buttigieg
 */
function _do_wp_ajax_imagify_get_admin_bar_profile() {
	check_ajax_referer( 'imagify-get-admin-bar-profile', 'imagifygetadminbarprofilenonce' );

	$user             = new Imagify_User();
	$unconsumed_quota = $user->get_percent_unconsumed_quota();
	$meteo_icon       = '<img src="' . IMAGIFY_ASSETS_IMG_URL . 'sun.svg" width="37" height="38" alt="" />';
	$bar_class        = 'positive';
	$message          = '';

	if ( $unconsumed_quota >= 21 && $unconsumed_quota <= 50 ) {
		$bar_class  = 'neutral';
		$meteo_icon = '<img src="' . IMAGIFY_ASSETS_IMG_URL . 'cloudy-sun.svg" width="37" height="38" alt="" />';
	}
	elseif ( $unconsumed_quota <= 20 ) {
		$bar_class  = 'negative';
		$meteo_icon = '<img src="' . IMAGIFY_ASSETS_IMG_URL . 'stormy.svg" width="38" height="36" alt="" />';
	}

	if ( $unconsumed_quota <= 20 && $unconsumed_quota > 0 ) {
		$message  = '<div class="imagify-error">';
			$message .= '<p><i class="dashicons dashicons-warning" aria-hidden="true"></i><strong>' . __( 'Oops, It\'s almost over!', 'imagify' ) . '</strong></p>';
			/* translators: %s is a line break. */
			$message .= '<p>' . sprintf( __( 'You have almost used all your credit.%sDon\'t forget to upgrade your subscription to continue optimizing your images.', 'imagify' ), '<br/><br/>' ) . '</p>';
			$message .= '<p class="center txt-center text-center"><a class="btn btn-ghost" href="' . IMAGIFY_APP_MAIN . '/#/subscription" target="_blank">' . __( 'View My Subscription', 'imagify' ) . '</a></p>';
		$message .= '</div>';
	}

	if ( 0 === $unconsumed_quota ) {
		$message  = '<div class="imagify-error">';
			$message .= '<p><i class="dashicons dashicons-warning" aria-hidden="true"></i><strong>' . __( 'Oops, It\'s Over!', 'imagify' ) . '</strong></p>';
			$message .= '<p>' . sprintf(
				/* translators: 1 is a data quota, 2 is a date. */
				__( 'You have consumed all your credit for this month. You will have <strong>%1$s back on %2$s</strong>.', 'imagify' ),
				size_format( $user->quota * 1048576 ),
				date_i18n( get_option( 'date_format' ), strtotime( $user->next_date_update ) )
			) . '</p>';
			$message .= '<p class="center txt-center text-center"><a class="btn btn-ghost" href="' . IMAGIFY_APP_MAIN . '/#/subscription" target="_blank">' . __( 'Upgrade My Subscription', 'imagify' ) . '</a></p>';
		$message .= '</div>';
	}

	// Custom HTML.
	$quota_section  = '<div class="imagify-admin-bar-quota">';
		$quota_section .= '<div class="imagify-abq-row">';

	if ( 1 === $user->plan_id ) {
		$quota_section .= '<div class="imagify-meteo-icon">' . $meteo_icon . '</div>';
	}

	$quota_section .= '<div class="imagify-account">';
		$quota_section .= '<p class="imagify-meteo-title">' . __( 'Account status', 'imagify' ) . '</p>';
		$quota_section .= '<p class="imagify-meteo-subs">' . __( 'Your subscription:', 'imagify' ) . '&nbsp;<strong class="imagify-user-plan">' . $user->plan_label . '</strong></p>';
	$quota_section .= '</div>'; // .imagify-account
	$quota_section .= '</div>'; // .imagify-abq-row

	if ( 1 === $user->plan_id ) {
		$quota_section .= '<div class="imagify-abq-row">';
			$quota_section .= '<div class="imagify-space-left">';
				/* translators: %s is a data quota. */
				$quota_section .= '<p>' . sprintf( __( 'You have %s space credit left', 'imagify' ), '<span class="imagify-unconsumed-percent">' . $unconsumed_quota . '%</span>' ) . '</p>';
				$quota_section .= '<div class="imagify-bar-' . $bar_class . '">';
					$quota_section .= '<div style="width: ' . $unconsumed_quota . '%;" class="imagify-unconsumed-bar imagify-progress"></div>';
				$quota_section .= '</div>'; // .imagify-bar-{$bar_class}
			$quota_section .= '</div>'; // .imagify-space-left
		$quota_section .= '</div>'; // .imagify-abq-row
	}

	$quota_section .= '<p class="imagify-abq-row">';
		$quota_section .= '<a class="imagify-account-link" href="' . IMAGIFY_APP_MAIN . '/#/subscription" target="_blank">';
			$quota_section .= '<span class="dashicons dashicons-admin-users"></span>';
			$quota_section .= '<span class="button-text">' . __( 'View my subscription', 'imagify' ) . '</span>';
		$quota_section .= '</a>'; // .imagify-account-link
	$quota_section .= '</p>'; // .imagify-abq-row
	$quota_section .= '</div>'; // .imagify-admin-bar-quota
	$quota_section .= $message;

	wp_send_json_success( $quota_section );
}

add_action( 'wp_ajax_imagify_get_prices', '_imagify_get_prices_from_api' );
/**
 * Get pricings from API for Onetime and Plans at the same time.
 *
 * @since  1.6
 * @author Geoffrey Crofte
 */
function _imagify_get_prices_from_api() {
	if ( ! check_ajax_referer( 'imagify_get_pricing_' . get_current_user_id(), 'imagifynonce', false ) ) {
		wp_send_json_error( 'check_ajax_referer for prices failed' );
	}

	$prices_all = get_imagify_all_prices();

	if ( is_wp_error( $prices_all ) ) {
		wp_send_json_error( 'Prices variable is a WP_Error: ' . $prices_all->get_error_message() );
	}

	if ( ! is_object( $prices_all ) ) {
		wp_send_json_error( 'Prices variable is not an object' );
	}

	wp_send_json_success( array(
		'onetimes'  => $prices_all->Packs,
		'monthlies' => $prices_all->Plans,
	) );
}

add_action( 'wp_ajax_imagify_check_coupon', '_imagify_check_coupon_code' );
/**
 * Check Coupon code on modal popin.
 *
 * @since  1.6
 * @author Geoffrey Crofte
 */
function _imagify_check_coupon_code() {
	if ( ! check_ajax_referer( 'imagify_get_pricing_' . get_current_user_id(), 'imagifynonce', false ) ) {
		wp_send_json_error( 'check_ajax_referer for coupon code checking failed' );
	}

	wp_send_json_success( check_imagify_coupon_code( $_POST['coupon'] ) );
}

add_action( 'wp_ajax_imagify_get_discount', '_imagify_get_discount' );
/**
 * Get current discount promotion to display information on payment modal.
 *
 * @since  1.6.3
 * @author Geoffrey Crofte
 */
function _imagify_get_discount() {
	if ( ! check_ajax_referer( 'imagify_get_pricing_' . get_current_user_id(), 'imagifynonce', false ) ) {
		wp_send_json_error( 'check_ajax_referer for getting discount failed' );
	}

	wp_send_json_success( check_imagify_discount() );
}

add_action( 'wp_ajax_imagify_get_images_counts', '_imagify_get_estimated_sizes' );
/**
 * Get estimated sizes from the WordPress library.
 *
 * @since  1.6
 * @author Geoffrey Crofte
 */
function _imagify_get_estimated_sizes() {
	if ( ! check_ajax_referer( 'imagify_get_pricing_' . get_current_user_id(), 'imagifynonce', false ) ) {
		wp_send_json_error( 'check_ajax_referer for estimated image sizes failed' );
	}

	$raw_total_size_in_library = imagify_calculate_total_size_images_library();
	$raw_average_per_month     = imagify_calculate_average_size_images_per_month();
	update_imagify_option( 'total_size_images_library', array( 'raw' => $raw_total_size_in_library, 'human' => size_format( $raw_total_size_in_library ) ) );
	update_imagify_option( 'average_size_images_per_month', array( 'raw' => $raw_average_per_month, 'human' => size_format( $raw_average_per_month ) ) );

	wp_send_json_success( array(
		'total_library_size' => get_imagify_option( 'total_size_images_library', null ),
		'average_month_size' => get_imagify_option( 'average_size_images_per_month', null ),
	) );
}

add_action( 'wp_ajax_imagify_update_estimate_sizes', '_imagify_update_estimate_sizes' );
/**
 * Estimate sizes and update the options values for them.
 *
 * @since  1.6
 * @author Remy Perona
 */
function _imagify_update_estimate_sizes() {
	check_ajax_referer( 'update_estimate_sizes' );

	$raw_total_size_in_library = imagify_calculate_total_size_images_library();
	$raw_average_per_month     = imagify_calculate_average_size_images_per_month();

	update_imagify_option( 'total_size_images_library', array( 'raw' => $raw_total_size_in_library, 'human' => size_format( $raw_total_size_in_library ) ) );
	update_imagify_option( 'average_size_images_per_month', array( 'raw' => $raw_average_per_month, 'human' => size_format( $raw_average_per_month ) ) );

	die( 1 );
}

/** --------------------------------------------------------------------------------------------- */
/** OTHER ======================================================================================= */
/** --------------------------------------------------------------------------------------------- */

add_action( 'wp_ajax_imagify_dismiss_notice',    '_do_admin_post_imagify_dismiss_notice' );
add_action( 'admin_post_imagify_dismiss_notice', '_do_admin_post_imagify_dismiss_notice' );
/**
 * Process a dismissed notice.
 *
 * @since 1.0
 * @author Jonathan Buttigieg
 */
function _do_admin_post_imagify_dismiss_notice() {
	if ( defined( 'DOING_AJAX' ) ) {
		check_ajax_referer( 'imagify-dismiss-notice' );
	} else {
		check_admin_referer( 'imagify-dismiss-notice' );
	}

	if ( ! isset( $_GET['notice'] ) || ! current_user_can( 'manage_options' ) ) {
		if ( defined( 'DOING_AJAX' ) ) {
			wp_send_json_error();
		} else {
			wp_nonce_ays( '' );
		}
	}

	$notice = $_GET['notice'];

	imagify_dismiss_notice( $notice );

	/**
	 * Fires when a notice is dismissed.
	 *
	 * @since 1.4.2
	 *
	 * @param int $notice The notice slug
	*/
	do_action( 'imagify_dismiss_notice', $notice );

	if ( ! defined( 'DOING_AJAX' ) ) {
		wp_safe_redirect( wp_get_referer() );
		die();
	}

	wp_send_json_success();
}

add_action( 'admin_post_imagify_deactivate_plugin', '_imagify_deactivate_plugin' );
/**
 * Disable a plugin which can be in conflict with Imagify
 *
 * @since 1.2
 * @author Jonathan Buttigieg
 */
function _imagify_deactivate_plugin() {
	if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'imagifydeactivatepluginnonce' ) ) {
		wp_nonce_ays( '' );
	}

	deactivate_plugins( $_GET['plugin'] );

	wp_safe_redirect( wp_get_referer() );
	die();
}
