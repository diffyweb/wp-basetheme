<?php
/**
 * Enqueue text-balance utility stylesheet.
 *
 * Ships a tiny stylesheet that applies native CSS text-wrap: balance
 * to all headings and any element tagged with the .text-balance
 * utility class (or its legacy alias .balance-text). Replaces the
 * former vanilla-balance-text JS enqueue — no JavaScript, degrades
 * gracefully in browsers that do not yet support text-wrap: balance.
 *
 * @package wp-basetheme
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', 'wp_basetheme_enqueue_text_balance' );

function wp_basetheme_enqueue_text_balance() {
	$relative_path = 'assets/css/text-balance.css';

	wp_enqueue_style(
		'wp-basetheme-text-balance',
		get_theme_file_uri( $relative_path ),
		array(),
		filemtime( get_theme_file_path( $relative_path ) )
	);
}
