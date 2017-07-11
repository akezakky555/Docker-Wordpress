<?php
/**
 * Plugin generic functions file
 *
 * @package WP Featured Post
 * @since 1.0.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Update default settings
 * 
 * @package WP Featured Post
 * @since 1.0.0
 */
function wpfp_default_settings() {
	
	global $wpfp_options;
	
	$wpfp_options = array(
		'custom_css' 	=> '',
		);

	$default_options = apply_filters('wpfp_options_default_values', $wpfp_options );
	
	// Update default options
	update_option( 'wpfp_options', $default_options );

	// Overwrite global variable when option is update
	$wpfp_options = wpfp_get_settings();
}

/**
 * Get Settings From Option Page
 * 
 * Handles to return all settings value
 * 
 * @package WP Featured Post
 * @since 1.0.0
 */
function wpfp_get_settings() {
	
	$options = get_option('wpfp_options');
	
	$settings = is_array($options) 	? $options : array();
	
	return $settings;
}

/**
 * Get an option
 * Looks to see if the specified setting exists, returns default if not
 * 
 * @package WP Featured Post
 * @since 1.0.0
 */
function wpfp_get_option( $key = '', $default = false ) {
	global $wpfp_options;

	$value = ! empty( $wpfp_options[ $key ] ) ? $wpfp_options[ $key ] : $default;
	$value = apply_filters( 'wpfp_get_option', $value, $key, $default );
	return apply_filters( 'wpfp_get_option_' . $key, $value, $key, $default );
}

/**
 * Escape Tags & Slashes
 *
 * Handles escapping the slashes and tags
 *
 * @package WP Featured Post
 * @since 1.1.0
 */
function wpfp_esc_attr($data) {
	return esc_attr( stripslashes($data) );
}

/**
 * Strip Slashes From Array
 *
 * @package WP Featured Post
 * @since 1.0.0
 */
function wpfp_slashes_deep($data = array(), $flag = false) {
	
	if($flag != true) {
		$data = wpfp_nohtml_kses($data);
	}
	$data = stripslashes_deep($data);
	return $data;
}

/**
 * Strip Html Tags 
 * 
 * It will sanitize text input (strip html tags, and escape characters)
 * 
 * @package WP Featured Post
 * @since 1.0.0
 */
function wpfp_nohtml_kses($data = array()) {
	
	if ( is_array($data) ) {
		
		$data = array_map('wpfp_nohtml_kses', $data);
		
	} elseif ( is_string( $data ) ) {
		$data = trim( $data );
		$data = wp_filter_nohtml_kses($data);
	}
	
	return $data;
}

/**
 * Function to get limited words
 * 
 * @package WP Featured Post
 * @since 1.0.0
 */
function wpfp_limit_words($string, $word_limit) {
	$words = explode(' ', $string, ($word_limit + 1));
	if(count($words) > $word_limit)
		array_pop($words);
	return implode(' ', $words);
}

/**
 * Function to unique number value
 * 
 * @package WP Featured Post
 * @since 1.0.0
 */
function wpfp_get_unique() {
	static $unique = 0;
	$unique++;

	return $unique;
}

/**
 * Function to add array after specific key
 * 
 * @package WP Featured Post
 * @since 1.0.0
 */
function wpfp_add_array(&$array, $value, $index) {
	
	if( is_array($array) && is_array($value) ){
		$split_arr 	= array_splice($array, max(0, $index));
		$array 		= array_merge( $array, $value, $split_arr);
	}

	return $array;
}

/**
 * Function to get post featured image
 * 
 * @package WP Featured Post
 * @since 1.0.0
 */
function wpfp_get_post_featured_image( $post_id = '', $size = 'full' ) {
	$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $size );
	
	if( !empty($image) ) {
		$image = isset($image[0]) ? $image[0] : '';
	}

	return $image;
}

/**
 * Function to get 'featured_post' shortcode designs
 * 
 * @package WP Featured Post
 * @since 1.0.0
 */
function wpfp_featured_post_designs() {
	$design_arr = array(
		'design-1'	=> __('Design 1', 'featured-post'),
		);
	return apply_filters('wpfp_featured_post_designs', $design_arr );
}
