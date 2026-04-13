<?php
/**
 * Enqueue vanilla-balance-text.
 *
 * Registers the locally-vendored balance-text library and attaches an
 * inline initializer that applies balancing to all headings and any
 * element tagged with the .balance-text class, on load and on resize.
 *
 * Upstream: https://github.com/qgustavor/vanilla-balance-text
 * Pinned commit: 30adf5e9c9dce66a5f38292492e5c550ed795c08 (2016-12-16,
 * latest available — upstream has been dormant since 2016).
 * Vendored at: assets/js/vendor/balancetext.min.js
 *
 * @package wp-basetheme
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', 'wp_basetheme_enqueue_balance_text' );

function wp_basetheme_enqueue_balance_text() {
	$relative_path = 'assets/js/vendor/balancetext.min.js';

	wp_enqueue_script(
		'balance-text',
		get_theme_file_uri( $relative_path ),
		array(),
		filemtime( get_theme_file_path( $relative_path ) ),
		true
	);

	$inline = <<<'JS'
document.addEventListener('DOMContentLoaded', function () {
	function applyBalanceText() {
		balanceText(document.querySelectorAll('h1, h2, h3, h4, h5, h6, .balance-text'));
	}
	applyBalanceText();
	window.addEventListener('resize', applyBalanceText);
});
JS;

	wp_add_inline_script( 'balance-text', $inline );
}
