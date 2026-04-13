<?php
/**
 * [current_year] shortcode.
 *
 * Outputs the current four-digit year in the site's timezone.
 *
 * @package wp-basetheme
 */

defined( 'ABSPATH' ) || exit;

add_shortcode( 'current_year', 'wp_basetheme_shortcode_current_year' );

function wp_basetheme_shortcode_current_year() {
	return wp_date( 'Y' );
}
