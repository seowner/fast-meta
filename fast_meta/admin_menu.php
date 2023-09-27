<?php

/*
Plugin Name: Fast META
Description: Adds custom meta box to posts, pages, and taxonomies.
Version: 1.0
License: GPLv3
*/

if (!defined('ABSPATH')) exit;


	//  Only include metaboxes on posts/pages/terms
	// ------------------------------------------------------------------
	if ( is_admin() ) {

	function fast_meta_settings_init() {
		register_setting('fast_meta', 'fast_meta_options');

		add_settings_section(
			'fast_meta_section',
			'Fast META Settings',
			'fast_meta_section_cb',
			'fast_meta'
		);

		add_settings_field(
			'fast_meta_breadcrumbs',
			'Enable Breadcrumbs',
			'fast_meta_field_cb',
			'fast_meta',
			'fast_meta_section'
		);
	}

	function fast_meta_section_cb() {
		echo '<p>Configure the settings for Fast META plugin.</p>';
	}

	function fast_meta_field_cb() {
		$options = get_option('fast_meta_options');
		$checkbox = isset($options['breadcrumbs']) ? $options['breadcrumbs'] : 0;
		echo '<input type="checkbox" id="enable_bread" name="fast_meta_options[breadcrumbs]" value="1" ' . checked(1, $checkbox, false) . ' />';
		echo '<label for="enable_bread" class="description">Enable or disable breadcrumbs functionality.</label>';
	}

	function fast_meta_settings_page() {
		?>
		<div class="wrap">
			<h2>Fast META Settings</h2>
			<form action="options.php" method="post">
				<?php
				settings_fields('fast_meta');
				do_settings_sections('fast_meta');
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	function fast_meta_admin_menu() {

		add_menu_page(
			'Fast META Settings',   // Page title
			'Fast META',            // Menu title
			'manage_options',       // Capability
			'fast_meta',            // Menu slug
			'fast_meta_settings_page', // Callback function
			'dashicons-admin-generic', // Icon URL (dashicon)
			999                      // Position
		);
	}


	add_action('admin_init', 'fast_meta_settings_init');
	add_action('admin_menu', 'fast_meta_admin_menu');




	include_once ABSPATH . 'wp-content/plugins/fast_meta/meta_boxes.php';

	if( strstr($_SERVER['REQUEST_URI'], 'wp-admin/post-new.php') || strstr($_SERVER['REQUEST_URI'], 'wp-admin/post.php') || strstr($_SERVER['REQUEST_URI'], 'wp-admin/term.php') ) {
		function fast_meta_stylesheet() {
		  wp_enqueue_style( 'style',  plugins_url( 'style.css?v=1.6.9' , __FILE__ ));
		}
		add_action('admin_enqueue_scripts', 'fast_meta_stylesheet');
	}


	// Add new column to the post list
	function add_seo_title_column($columns) {
		$columns['seo_title'] = 'SEO Title';
		return $columns;
	}
	add_filter('manage_posts_columns', 'add_seo_title_column');
	add_filter('manage_pages_columns', 'add_seo_title_column');

	// Populate the new column with data
	function display_seo_title_column($column, $post_id) {
		if ($column == 'seo_title') {
			$seo_title = get_post_meta($post_id, 'fast_meta_title', true);
			if (!empty($seo_title)) {
				echo do_shortcode($seo_title);
			}
		}
	}
	add_action('manage_posts_custom_column', 'display_seo_title_column', 10, 2);
	add_action('manage_pages_custom_column', 'display_seo_title_column', 10, 2);



}


//  Display only outside meta
// ------------------------------------------------------------------
if ( !is_admin() ) {
	include_once ABSPATH . 'wp-content/plugins/fast_meta/display.php';

	$options = get_option('fast_meta_options', array('breadcrumbs' => 0));
	if (isset($options['breadcrumbs']) && $options['breadcrumbs']) {
		include_once ABSPATH . 'wp-content/plugins/fast_meta/breadcrumbs.php';
	}

}


	include_once ABSPATH . 'wp-content/plugins/fast_meta/shortcodes.php';



?>