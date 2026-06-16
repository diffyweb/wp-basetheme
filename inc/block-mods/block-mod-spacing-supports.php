<?php
/**
 * Backfill spacing supports for selected core blocks.
 *
 * core/template-part has no native spacing support, and on older WordPress
 * (the 6.8 floor) core/post-content lacks it too — so neither exposes
 * margin/padding controls in the Site Editor. This filter adds them.
 *
 * It is deliberately additive: if a block already declares spacing support
 * (e.g. core/post-content gained native spacing in WP 7.0, which is richer —
 * it includes blockGap), we leave it untouched rather than overwrite it with
 * a poorer set. So the filter only fills a gap; it never clobbers core.
 *
 * @package wp-basetheme
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'register_block_type_args', 'wp_basetheme_block_mod_spacing_supports', 10, 2 );

function wp_basetheme_block_mod_spacing_supports( $args, $name ) {
	$targets = array( 'core/template-part', 'core/post-content' );

	if ( ! in_array( $name, $targets, true ) ) {
		return $args;
	}

	$args['supports'] = $args['supports'] ?? array();

	// Only backfill when core provides nothing — don't overwrite native support.
	if ( empty( $args['supports']['spacing'] ) ) {
		$args['supports']['spacing'] = array(
			'margin'  => true,
			'padding' => true,
		);
	}

	return $args;
}
