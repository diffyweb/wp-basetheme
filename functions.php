<?php

// current year shortcode
function current_year_shortcode() {
	return date('Y');
}
add_shortcode('current_year', 'current_year_shortcode');

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
