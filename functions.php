<?php
/**
 * wp-basetheme functions and definitions.
 *
 * Auto-loads every PHP file in inc/<category>/ so theme features live
 * in focused, single-responsibility files. Drop a new file into any
 * inc/<category>/ directory and it will be picked up on the next load.
 *
 * @package wp-basetheme
 */

defined( 'ABSPATH' ) || exit;

foreach ( (array) glob( get_theme_file_path( 'inc/*/*.php' ) ) as $wp_basetheme_include ) {
	require_once $wp_basetheme_include;
}
unset( $wp_basetheme_include );

add_filter('register_block_type_args', function($args, $name) {
    if ($name === 'core/template-part') {
        $args['supports'] = $args['supports'] ?? [];
        $args['supports']['spacing'] = [
            'margin'  => true,
            'padding' => true,
        ];
    }
    if ($name === 'core/post-content') {
        $args['supports'] = $args['supports'] ?? [];
        $args['supports']['spacing'] = [
            'margin'  => true,
            'padding' => true,
        ];
    }
    return $args;
}, 10, 2);
