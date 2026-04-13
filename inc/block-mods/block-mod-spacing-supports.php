<?php
/**
 * Enable spacing supports for selected core blocks.
 *
 * core/template-part and core/post-content do not ship with spacing
 * supports by default. This filter adds margin + padding controls so
 * they can be tuned from the Site Editor like any other block.
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

	$args['supports']            = $args['supports'] ?? array();
	$args['supports']['spacing'] = array(
		'margin'  => true,
		'padding' => true,
	);

	return $args;
}
