<?php

if (!defined('ABSPATH')) exit;

function modify_page_term_meta_tags() {
    static $title_modified = false; // Static variable to check if the title was already modified

    if (is_singular()) {
        // For posts and pages
        $id = get_queried_object_id();
        $meta_title = do_shortcode(get_post_meta($id, 'fast_meta_title', true));
        $meta_description = do_shortcode(get_post_meta($id, 'fast_meta_description', true));
        $indexing_options = get_post_meta($id, 'fast_indexing_options', true) ?: 'index_follow';
    } elseif (is_category() || is_tag() || is_tax()) {
        // For terms
        $id = get_queried_object_id();
        $meta_title = do_shortcode(get_term_meta($id, 'fast_meta_title', true));
        $meta_description = do_shortcode(get_term_meta($id, 'fast_meta_description', true));
        $indexing_options = get_term_meta($id, 'fast_indexing_options', true) ?: 'index_follow';
    } else {
        // If not a post, page, or term, return without making changes
        return;
    }

    // Modify the <title> tag
    if ( $meta_title || $meta_title !== '' ) {
        // Newer themes
        add_filter('pre_get_document_title', function() use ($meta_title, &$title_modified) {
            $title_modified = true; // Set the flag as modified
            return $meta_title;
        }, 9999);
        
        // Older themes
        add_filter('wp_title', function ($original_title) use ($meta_title, &$title_modified) {
            if ($title_modified) return $original_title;
            return $meta_title;
        }, 9998, 1);
    } else {
        // Newer themes
        add_filter('pre_get_document_title', function() use (&$title_modified) {
            $title_modified = true; // Set the flag as modified
            if (is_singular()) {
                return get_the_title('', false);
            } elseif (is_category() || is_tag() || is_tax()) {
                return single_term_title('', false);
            }
            return null;
        }, 9999);

        // Older themes
        add_filter('wp_title', function ($original_title) use (&$title_modified) {
            if ($title_modified) return $original_title;
            if (is_singular()) {
                return get_the_title('', false);
            } elseif (is_category() || is_tag() || is_tax()) {
                return single_term_title('', false);
            }
            return $original_title;
        }, 9998, 1);
    }






    // Modify the meta description tag
    if ($meta_description) {
        add_action('wp_head', function () use ($meta_description) {
			$processed_description = do_shortcode($meta_description);
            echo '<meta name="description" content="' . esc_attr($processed_description) . '" />' . "\n";
        }, 1);
    }

	
// Modify the robots meta tag
if ($indexing_options) {
    add_filter('wp_robots', function ($robots) use ($indexing_options) {
        // Check the 'blog_public' option
        $blog_public = get_option('blog_public');

        // If 'blog_public' is '0' (Discourage search engines from indexing this site),
        // enforce 'noindex, nofollow'
        if ($blog_public === '0') {
            return ['noindex' => true, 'nofollow' => true];
        }

        // Start with an empty array
        $indexing_value = [];
        switch ($indexing_options) {
            case 'index_follow':
                $indexing_value = ['index' => true, 'follow' => true];
                break;
            case 'noindex_follow':
                $indexing_value = ['noindex' => true, 'follow' => true];
                break;
            case 'noindex_nofollow':
                $indexing_value = ['noindex' => true, 'nofollow' => true];
                break;
            default:
                $indexing_value = ['index' => true, 'follow' => true];
        }

        // Clear out the conflicting values
        $conflicting_keys = ['index', 'follow', 'noindex', 'nofollow'];
        foreach($conflicting_keys as $key) {
            unset($robots[$key]);
        }
        
        // Replace the values in $robots with the custom values
        $robots = array_replace($indexing_value,$robots);

        return $robots;
    });
}



}
add_action('template_redirect', 'modify_page_term_meta_tags');





?>