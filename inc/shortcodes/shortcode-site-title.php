<?php
/**
 * [site_title] shortcode.
 *
 * Outputs the site name from Settings > General.
 *
 * @package wp-basetheme
 */

defined( 'ABSPATH' ) || exit;

add_shortcode( 'site_title', 'wp_basetheme_shortcode_site_title' );

function wp_basetheme_shortcode_site_title() {
	return get_bloginfo( 'name' );
}
