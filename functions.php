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

// site title shortcode
function site_title_shortcode() {
	return get_bloginfo('name');
}
add_shortcode('site_title', 'site_title_shortcode');

// enqueue balance-text script(s)
add_action('wp_enqueue_scripts', 'enqueue_balance_text_script');
function enqueue_balance_text_script() {
	wp_enqueue_script('balance-text-script', 'https://cdn.jsdelivr.net/gh/qgustavor/vanilla-balance-text@8baf155680199691e1ecd260a519f71a652f014b/balancetext.min.js', array(), false, true);
}
function custom_balance_text_insertion() { ?>
<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
	function applyBalanceText() {
		balanceText(document.querySelectorAll('h1, h2, h3, h4, h5, h6, .balance-text'));
	}

	// Apply balanceText on page load
	applyBalanceText();

	// Re-apply balanceText on window resize
	window.addEventListener('resize', applyBalanceText);
});
</script>
<?php }
add_action('wp_footer', 'custom_balance_text_insertion', 20);

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
