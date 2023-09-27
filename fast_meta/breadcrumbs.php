<?php

if (!function_exists('fast_breadcrumb')) {
    function fast_breadcrumb($before = '<p id="breadcrumbs">', $after = '</p>') {
        global $post;
        $links = array();

        $links[] = array(
            'url' => get_home_url(),
            'text' => 'Home'
        );

	// Add post type archive if necessary
	if (is_singular() && $post->post_type != 'post' && $post->post_type != 'page') {
		$links[] = array(
			'url' => get_post_type_archive_link($post->post_type),
			'text' => get_post_type_object($post->post_type)->labels->name
		);
	}

        // Add taxonomy term link if necessary
        if (is_tax()) {
            $term = get_queried_object();
            $links[] = array(
                'url' => get_term_link($term),
                'text' => $term->name
            );
        }

        // Add category/taxonomy links if necessary
        if (is_singular() || is_category()) {
            $categories = get_the_category();
            if ($categories) {
                $links[] = array(
                    'url' => get_category_link($categories[0]->term_id),
                    'text' => $categories[0]->name
                );
            }
        }

        // Add parent page(s) if necessary
        if (is_page() && $post->post_parent) {
            $parent_id   = $post->post_parent;
            $breadcrumbs = array();
            while ($parent_id) {
                $page          = get_page($parent_id);
                $breadcrumbs[] = array(
                    'url' => get_permalink($page->ID),
                    'text' => get_the_title($page->ID)
                );
                $parent_id     = $page->post_parent;
            }
            $breadcrumbs = array_reverse($breadcrumbs);
            $links       = array_merge($links, $breadcrumbs);
        }

        // Add current page
        if (is_singular() || is_page()) {
            $links[] = array(
                'url' => get_permalink(),
                'text' => get_the_title()
            );
        }

        // Apply filters
		$links = apply_filters('fast_breadcrumb_links', $links);
		$links = array_values(array_filter($links)); // reindex the array
//var_dump($links);
		// Display breadcrumbs
		echo $before;
		foreach ($links as $i => $link) {
			if ($i < count($links) - 1) {
				echo '<a href="' . esc_url($link['url']) . '">' . do_shortcode(esc_html($link['text'])) . '</a> &rsaquo; ';
			} else {
				echo '<span class="breadcrumb_last" aria-current="page">' . do_shortcode(esc_html($link['text'])) . '</span>';
			}
		}
		echo $after;
    }
}

?>