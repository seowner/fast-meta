<?php

//  Current year
// ------------------------------------------------------------------
function fastCurrentYear() {
    $year = date('Y');
    return $year;
}
add_shortcode('year', 'fastCurrentYear');



//  Current month
// ------------------------------------------------------------------
function fastCurrentMonth() {
    $month = date('F');
    return $month;
}
add_shortcode('month', 'fastCurrentMonth');


//  Current day
// ------------------------------------------------------------------
function fastCurrentDay() {
    $day = date('j');
    return $day;
}
add_shortcode('day', 'fastCurrentDay');


//  Sitename
// ------------------------------------------------------------------
function fastCurrentSitename() {
    $site_name = get_option('blogname');
    return $site_name;
}
add_shortcode('sitename', 'fastCurrentSitename');



//  Add to titles
// ------------------------------------------------------------------
add_filter( 'the_title', 'do_shortcode' );
add_filter( 'single_term_title', 'do_shortcode' );
add_filter('single_cat_title', 'do_shortcode');


//  Add to wp_title
// ------------------------------------------------------------------
function process_shortcodes_in_title($title) {
	$title = do_shortcode($title);
	return $title;
}

add_filter('wp_title', 'process_shortcodes_in_title', 999);
add_filter('pre_get_document_title', 'process_shortcodes_in_title', 999);



?>