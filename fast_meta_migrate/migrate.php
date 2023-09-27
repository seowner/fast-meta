<?php
/**
 * Plugin Name: Fast Meta Migration
 * Description: Migrate metadata from Yoast SEO plugin to Fast Meta plugin
 * Version: 1.0
 * Author: Your Name
 */

if (!defined('ABSPATH')) exit;

function fast_meta_migration_menu() {
    add_menu_page(
        'Fast Meta Migration',
        'Fast Meta Migration',
        'manage_options',
        'fast-meta-migration',
        'fast_meta_migration_page'
    );
}
add_action('admin_menu', 'fast_meta_migration_menu');

function fast_meta_migration_page() {
    if (isset($_POST['migrate'])) {
        migrate_post_meta_data();
        migrate_taxonomy_meta_data();
        echo '<div id="message" class="updated notice is-dismissible"><p>Data migration completed successfully!</p></div>';
    }

    echo '<div class="wrap">';
    echo '<h1>Fast Meta Migration</h1>';
    echo '<form method="post" action="">';
    echo '<input type="submit" name="migrate" id="migrate" class="button button-primary" value="Migrate">';
    echo '</form>';
    echo '</div>';
}

// Place the earlier provided functions here (migrate_post_meta_data and migrate_taxonomy_meta_data)




function migrate_post_meta_data() {
    $args = array(
        'post_type' => 'any',
        'posts_per_page' => -1,
    );
    
    $posts = get_posts($args);
    
    foreach ($posts as $post) {
        $yoast_title = get_post_meta($post->ID, '_yoast_wpseo_title', true);
        $yoast_desc = get_post_meta($post->ID, '_yoast_wpseo_metadesc', true);
        $yoast_noindex = get_post_meta($post->ID, '_yoast_wpseo_meta-robots-noindex', true);
        $yoast_nofollow= get_post_meta($post->ID, '_yoast_wpseo_meta-robots-nofollow', true);

		$yoast_title = str_replace('%%currentyear%%', '[year]', $yoast_title);
		$yoast_title = str_replace('%%currentmonth%%', '[month]', $yoast_title);
		$yoast_title = str_replace('%%sitename%%', '[sitename]', $yoast_title);
		$yoast_desc = str_replace('%%currentyear%%', '[year]', $yoast_desc);
		$yoast_desc = str_replace('%%currentmonth%%', '[month]', $yoast_desc);
		$yoast_desc = str_replace('%%sitename%%', '[sitename]', $yoast_desc);
        
        if (isset($yoast_title)) {
            update_post_meta($post->ID, 'fast_meta_title', sanitize_text_field($yoast_title));
			delete_post_meta($post->ID, '_yoast_wpseo_title');
        }

        if (isset($yoast_desc)) {
            update_post_meta($post->ID, 'fast_meta_description', sanitize_textarea_field($yoast_desc));
            delete_post_meta($post->ID, '_yoast_wpseo_metadesc');
        }


        if (isset($yoast_noindex)) {
			if ( $yoast_noindex == '' && $yoast_nofollow == '' ) {
				update_post_meta($post->ID, 'fast_indexing_options', 'index_follow');
			} elseif ( $yoast_noindex == '1' && $yoast_nofollow == '' ) {
				update_post_meta($post->ID, 'fast_indexing_options', 'noindex_follow');
			} elseif ( $yoast_noindex == '1' && $yoast_nofollow == '1' ) {
				update_post_meta($post->ID, 'fast_indexing_options', 'noindex_nofollow');
			} else {
				update_post_meta($post->ID, 'fast_indexing_options', 'index_follow');
			}
		}


		// Disable revisions
		remove_action('post_updated', 'wp_save_post_revision');

        wp_update_post(array(
            'ID' => $post->ID,
            'post_title' => $post->post_title // This is needed to prevent changing the title.
        ));


		// Re-enable revisions
		add_action('post_updated', 'wp_save_post_revision');

    }


    
    return 'Post/Page Meta Data Migrated and Yoast Data Deleted Successfully!';
}

function migrate_taxonomy_meta_data() {
   global $wpdb;
   $taxonomies = get_taxonomies(); // fetches all taxonomies
    
    foreach ($taxonomies as $taxonomy) {
        $terms = get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
        ));

        if (!empty($terms) && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                $yoast_title = WPSEO_Taxonomy_Meta::get_term_meta($term, $taxonomy, 'title');
                $yoast_desc = WPSEO_Taxonomy_Meta::get_term_meta($term, $taxonomy, 'description');


				$yoast_noindex = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT is_robots_noindex FROM {$wpdb->prefix}yoast_indexable WHERE object_type = 'term' AND object_id = %d",
						$term->term_id
					)
				);


                $yoast_title = str_replace('%%currentyear%%', '[year]', $yoast_title);
                $yoast_title = str_replace('%%currentmonth%%', '[month]', $yoast_title);
                $yoast_title = str_replace('%%sitename%%', '[sitename]', $yoast_title);
                $yoast_desc = str_replace('%%currentyear%%', '[year]', $yoast_desc);
                $yoast_desc = str_replace('%%currentmonth%%', '[month]', $yoast_desc);
                $yoast_desc = str_replace('%%sitename%%', '[sitename]', $yoast_desc);


                if (isset($yoast_title)) {
                    update_term_meta($term->term_id, 'fast_meta_title', sanitize_text_field($yoast_title));
                }

                if (isset($yoast_desc)) {
                    update_term_meta($term->term_id, 'fast_meta_description', sanitize_textarea_field($yoast_desc));
                }



				if ( $yoast_noindex == null || $yoast_noindex == false ) {
					update_term_meta($term->term_id, 'fast_indexing_options', 'index_follow');
				} else {
					update_term_meta($term->term_id, 'fast_indexing_options', 'noindex_follow');
				}



			wp_update_term($term->term_id, $taxonomy);

            }
        }
    }


    $tables = array(
        'wp_yoast_indexable',
        'wp_yoast_indexable_hierarchy',
        'wp_yoast_migrations',
        'wp_yoast_primary_term',
        'wp_yoast_seo_links',
    );

    foreach ($tables as $table) {
        $wpdb->query("DROP TABLE IF EXISTS $table");
    }


    $yoast_options = array(
        'wpseo_taxonomy_meta',
        'wpseo',
        'wpseo_rss',
        'wpseo_internallinks',
        'wpseo_xml',
        'wpseo_titles',
        'wpseo_social',
        'wpseo_permalinks',
        '_transient_timeout_wpseo_total_unindexed_posts_limited',
        '_transient_wpseo_total_unindexed_posts_limited',
        '_transient_timeout_wpseo_total_unindexed_terms_limited',
        '_transient_wpseo_total_unindexed_terms_limited',
        '_transient_timeout_wpseo_total_unindexed_post_type_archives',
        '_transient_wpseo_total_unindexed_post_type_archives',
        '_transient_timeout_wpseo_total_unindexed_general_items',
        '_transient_wpseo_total_unindexed_general_items',
        '_transient_timeout_wpseo_unindexed_post_link_count',
        '_transient_wpseo_unindexed_post_link_count',
        '_transient_wpseo_unindexed_term_link_count',
        '_transient_timeout_wpseo_unindexed_term_link_count',
    );

    foreach ($yoast_options as $option) {
        $wpdb->delete($wpdb->options, array('option_name' => $option));
    }


    // Deletes all cron jobs
    $wpdb->query( "DELETE FROM $wpdb->options WHERE option_name = 'cron'" );


	// ----------------------------------------------------------------------------------------
	//  Optimize tables
	// ----------------------------------------------------------------------------------------
	$tables = $wpdb->get_col( "SHOW TABLES" );
	foreach ( $tables as $table ) {
		$wpdb->query( "OPTIMIZE TABLE $table" );
	}



    return 'Taxonomy Meta Data Migrated and Yoast Data Deleted Successfully!';
}


// Use these functions inside an action hook, or shortcode as per your need





?>