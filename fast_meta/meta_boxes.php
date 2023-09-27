<?php

if (!defined('ABSPATH')) exit;

// Add meta box to posts, pages, and taxonomies
function custom_meta_box() {
    $post_types = array('post', 'page','game','casino'); // Add any additional post types if required
    
    foreach ($post_types as $post_type) {
        add_meta_box(
            'custom-meta-box',
            '&#9201; Fast META',
            'custom_meta_box_callback',
            $post_type,
            'normal',
            'high'
        );
    }
    
    $taxonomies = get_taxonomies(); // Get all taxonomies
    
    foreach ($taxonomies as $taxonomy) {
        add_meta_box(
            'custom-meta-box',
            'Fast META',
            'custom_meta_box_callback',
            $taxonomy,
            'normal',
            'high'
        );
    }
}
add_action('add_meta_boxes', 'custom_meta_box');

// Callback function for the custom meta box
function custom_meta_box_callback($post) {
    // Retrieve current values from post meta
    $meta_title = get_post_meta($post->ID, 'fast_meta_title', true);
    $meta_description = get_post_meta($post->ID, 'fast_meta_description', true);
    $indexing_options = get_post_meta($post->ID, 'fast_indexing_options', true);
    
	// Output the meta box HTML+
	$current_url = get_the_permalink();
	$url_parts = parse_url($current_url);
	$path = trim($url_parts['path'], '/');
	$path_segments = explode('/', $path);
	$domain = $url_parts['host'];


    ?>

<div class="fast_meta_container">
	<div class="preview_container">
		<div class="url_and_title_container">
			<div class="url_preview_container">
				<div class="sub_url_preview">
					<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABs0lEQVR4AWL4//8/RRjO8Iucx+noO0MWUDo16FYABMGP6ZfUcRnWtm27jVPbtm3bttuH2t3eFPcY9pLz7NxiLjCyVd87pKnHyqXyxtCs8APd0rnyxiu4qSeA3QEDrAwBDrT1s1Rc/OrjLZwqVmOSu6+Lamcpp2KKMA9PH1BYXMe1mUP5qotvXTywsOEEYHXxrY+3cqk6TMkYpNr2FeoY3KIr0RPtn9wQ2unlA+GMkRw6+9TFw4YTwDUzx/JVvARj9KaedXRO8P5B1Du2S32smzqUrcKGEyA+uAgQjKX7zf0boWHGfn71jIKj2689gxp7OAGShNcBUmLMPVjZuiKcA2vuWHHDCQxMCz629kXAIU4ApY15QwggAFbfOP9DhgBJ+nWVJ1AZAfICAj1pAlY6hCADZnveQf7bQIwzVONGJonhLIlS9gr5mFg44Xd+4S3XHoGNPdJl1INIwKyEgHckEhgTe1bGiFY9GSFBYUwLh1IkiJUbY407E7syBSFxKTszEoiE/YdrgCEayDmtaJwCI9uu8TKMuZSVfSa4BpGgzvomBR/INhLGzrqDotp01ZR8pn/1L0JN9d9XNyx0AAAAAElFTkSuQmCC" alt="" class="preview_favicon">
					<span class="page_txt_preview">
					<span class="dom_txt_preview"><?php echo '<span class="dom_txt_preview">' . $domain . '</span>'; ?></span><?php foreach ($path_segments as $segment) { echo ' › <span>' . $segment . '</span>'; } ?></span>
				</div>
			</div>
		<div class="preview_meta_title"><?php if ( $meta_title !== '' ) { echo do_shortcode(esc_attr($meta_title)); } else { echo get_the_title(); } ?></div>
		</div>


	<div class="preview_meta_description"><?php if ( $meta_description !== '' ) { echo do_shortcode(esc_textarea($meta_description)); } else { echo 'Please provide a meta description by editing the snippet below. If you don’t, Google will try to find a relevant part of your post to show in the search results.'; } ?></div>
	</div>

    <div class="fast_meta_field">
        <div class="meta_title_flex"><label for="custom-meta-title">SEO title</label><span id="meta-title-count" class="meta_counter">0</span></div>
        <input type="text" name="fast_meta_title" id="custom-meta-title" value="<?php if ( $meta_title !== '' ) { echo esc_attr($meta_title); } else { echo get_post()->post_title; } ?>">
    </div>
    
    <div class="fast_meta_field fast_des_field">
        <div class="meta_title_flex"><label for="custom-meta-description">Meta description</label><span id="meta-des-count" class="meta_counter">0</span></div>
        <textarea name="fast_meta_description" id="custom-meta-description" rows="5"><?php if ( $meta_description !== '' ) { echo esc_textarea($meta_description); } else { echo ''; } ?></textarea>
    </div>
    
    <div class="fast_meta_field">
    <div class="fast_meta_field_split">
		<div>
        <label for="custom-indexing-options">Indexing options</label>
        <select name="fast_indexing_options" id="custom-indexing-options">
            <option value="index_follow" <?php selected($indexing_options, 'index_follow'); ?>>Index, Follow</option>
            <option value="noindex_follow" <?php selected($indexing_options, 'noindex_follow'); ?>>Noindex, Follow</option>
            <option value="noindex_nofollow" <?php selected($indexing_options, 'noindex_nofollow'); ?>>Noindex, Nofollow</option>
        </select>
		</div>
		<div>
        <label>&nbsp;</label>
			<a href="#" id="update_post_lower" class="button publish_lower_button">Update Post</a>
		</div>
    </div>
</div>
</div>

<?php addMetaScript(); ?>


    <?php
}

// Save custom meta box data
function save_custom_meta_box_data($post_id) {

    // Save the meta box data
    if (isset($_POST['fast_meta_title'])) {
        update_post_meta($post_id, 'fast_meta_title', sanitize_text_field($_POST['fast_meta_title']));
    }
    
    if (isset($_POST['fast_meta_description'])) {
        update_post_meta($post_id, 'fast_meta_description', sanitize_textarea_field($_POST['fast_meta_description']));
    }
    
    if (isset($_POST['fast_indexing_options'])) {
        update_post_meta($post_id, 'fast_indexing_options', sanitize_text_field($_POST['fast_indexing_options']));
    }
}
add_action('save_post', 'save_custom_meta_box_data');

// Display existing meta values when editing a post or taxonomy term
function display_existing_meta_values($tag) {
    $meta_title = get_term_meta($tag->term_id, 'fast_meta_title', true);
    $meta_description = get_term_meta($tag->term_id, 'fast_meta_description', true);
    $indexing_options = get_term_meta($tag->term_id, 'fast_indexing_options', true);


    // Output the meta box HTML+
	$current_url = get_term_link($term->term_id);
	$url_parts = parse_url($current_url);
	$path = trim($url_parts['path'], '/');
	$path_segments = explode('/', $path);
	$domain = $url_parts['host'];

    
    // Output the meta box HTML
if( !strstr($_SERVER['REQUEST_URI'], 'wp-admin/edit-tags.php') ) {
    ?>
<div class="fast_meta_container">
	<div class="preview_container">
		<div class="url_and_title_container">
			<div class="url_preview_container">
				<div class="sub_url_preview">
					<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABs0lEQVR4AWL4//8/RRjO8Iucx+noO0MWUDo16FYABMGP6ZfUcRnWtm27jVPbtm3bttuH2t3eFPcY9pLz7NxiLjCyVd87pKnHyqXyxtCs8APd0rnyxiu4qSeA3QEDrAwBDrT1s1Rc/OrjLZwqVmOSu6+Lamcpp2KKMA9PH1BYXMe1mUP5qotvXTywsOEEYHXxrY+3cqk6TMkYpNr2FeoY3KIr0RPtn9wQ2unlA+GMkRw6+9TFw4YTwDUzx/JVvARj9KaedXRO8P5B1Du2S32smzqUrcKGEyA+uAgQjKX7zf0boWHGfn71jIKj2689gxp7OAGShNcBUmLMPVjZuiKcA2vuWHHDCQxMCz629kXAIU4ApY15QwggAFbfOP9DhgBJ+nWVJ1AZAfICAj1pAlY6hCADZnveQf7bQIwzVONGJonhLIlS9gr5mFg44Xd+4S3XHoGNPdJl1INIwKyEgHckEhgTe1bGiFY9GSFBYUwLh1IkiJUbY407E7syBSFxKTszEoiE/YdrgCEayDmtaJwCI9uu8TKMuZSVfSa4BpGgzvomBR/INhLGzrqDotp01ZR8pn/1L0JN9d9XNyx0AAAAAElFTkSuQmCC" alt="" class="preview_favicon">
					<span class="page_txt_preview">
					<span class="dom_txt_preview"><?php echo '<span class="dom_txt_preview">' . $domain . '</span>'; ?></span><?php foreach ($path_segments as $segment) { echo ' › <span>' . $segment . '</span>'; } ?></span>
				</div>
			</div>
		<div class="preview_meta_title"><?php if ( $meta_title !== '' ) { echo do_shortcode(esc_attr($meta_title)); } else { echo get_the_title(); } ?></div>
		</div>


	<div class="preview_meta_description"><?php if ( $meta_description !== '' ) { echo do_shortcode(esc_textarea($meta_description)); } else { echo 'Please provide a meta description by editing the snippet below. If you don’t, Google will try to find a relevant part of your post to show in the search results.'; } ?></div>
	</div>

    <div class="fast_meta_field">
        <label for="custom-meta-title">SEO title</label>
        <input type="text" name="fast_meta_title" id="custom-meta-title" value="<?php if ( $meta_title !== '' ) { echo esc_attr($meta_title); } else { echo get_term($term->term_id)->name; } ?>">
    </div>

    <div class="fast_meta_field fast_des_field">
        <label for="custom-meta-description">Meta description</label>
        <textarea name="fast_meta_description" id="custom-meta-description" rows="5"><?php if ( $meta_description !== '' ) { echo esc_textarea($meta_description); } else { echo ''; } ?></textarea>
    </div>
    
    <div class="fast_meta_field">
        <label for="custom-indexing-options">Indexing options</label>
        <select name="fast_indexing_options" id="custom-indexing-options">
            <option value="index_follow" <?php selected($indexing_options, 'index_follow'); ?>>Index, Follow</option>
            <option value="noindex_follow" <?php selected($indexing_options, 'noindex_follow'); ?>>Noindex, Follow</option>
            <option value="noindex_nofollow" <?php selected($indexing_options, 'noindex_nofollow'); ?>>Noindex, Nofollow</option>
        </select>
    </div>
</div>

<?php addMetaScript(); ?>

    <?php
}
}


// Save custom meta box data for taxonomies
function save_custom_taxonomy_meta_box_data($term_id) {

    
    // Save the meta box data
    if (isset($_POST['fast_meta_title'])) {
        update_term_meta($term_id, 'fast_meta_title', sanitize_text_field($_POST['fast_meta_title']));
    }
    
    if (isset($_POST['fast_meta_description'])) {
        update_term_meta($term_id, 'fast_meta_description', sanitize_textarea_field($_POST['fast_meta_description']));
    }
    
    if (isset($_POST['fast_indexing_options'])) {
        update_term_meta($term_id, 'fast_indexing_options', sanitize_text_field($_POST['fast_indexing_options']));
    }
}
add_action('edited_terms', 'save_custom_taxonomy_meta_box_data');
add_action('create_term', 'save_custom_taxonomy_meta_box_data');

// Display existing meta values when editing a taxonomy term
function display_existing_taxonomy_meta_values($term) {
    $meta_title = get_term_meta($term->term_id, 'fast_meta_title', true);
    $meta_description = get_term_meta($term->term_id, 'fast_meta_description', true);
    $indexing_options = get_term_meta($term->term_id, 'fast_indexing_options', true);
    
    // Output the meta box HTML+
	$current_url = get_term_link($term->term_id);
	$url_parts = parse_url($current_url);
	$path = trim($url_parts['path'], '/');
	$path_segments = explode('/', $path);
	$domain = $url_parts['host'];

    // Output the meta box HTML
if( !strstr($_SERVER['REQUEST_URI'], 'wp-admin/edit-tags.php') ) {
    ?>
 <div class="fast_meta_container" style="margin-left:185px;padding:0;">
<div class="postbox-header"><h2 class="hndle ui-sortable-handle" style="font-size: 14px;padding: 8px 12px;margin: 0;line-height: 1.4;">⏱ Fast META</h2></div>
	<div class="preview_container">
		<div class="url_and_title_container">
			<div class="url_preview_container">
				<div class="sub_url_preview">
					<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABs0lEQVR4AWL4//8/RRjO8Iucx+noO0MWUDo16FYABMGP6ZfUcRnWtm27jVPbtm3bttuH2t3eFPcY9pLz7NxiLjCyVd87pKnHyqXyxtCs8APd0rnyxiu4qSeA3QEDrAwBDrT1s1Rc/OrjLZwqVmOSu6+Lamcpp2KKMA9PH1BYXMe1mUP5qotvXTywsOEEYHXxrY+3cqk6TMkYpNr2FeoY3KIr0RPtn9wQ2unlA+GMkRw6+9TFw4YTwDUzx/JVvARj9KaedXRO8P5B1Du2S32smzqUrcKGEyA+uAgQjKX7zf0boWHGfn71jIKj2689gxp7OAGShNcBUmLMPVjZuiKcA2vuWHHDCQxMCz629kXAIU4ApY15QwggAFbfOP9DhgBJ+nWVJ1AZAfICAj1pAlY6hCADZnveQf7bQIwzVONGJonhLIlS9gr5mFg44Xd+4S3XHoGNPdJl1INIwKyEgHckEhgTe1bGiFY9GSFBYUwLh1IkiJUbY407E7syBSFxKTszEoiE/YdrgCEayDmtaJwCI9uu8TKMuZSVfSa4BpGgzvomBR/INhLGzrqDotp01ZR8pn/1L0JN9d9XNyx0AAAAAElFTkSuQmCC" alt="" class="preview_favicon">
					<span class="page_txt_preview">
					<span class="dom_txt_preview"><?php echo '<span class="dom_txt_preview">' . $domain . '</span>'; ?></span><?php foreach ($path_segments as $segment) { echo ' › <span>' . $segment . '</span>'; } ?></span>
				</div>
			</div>
		<div class="preview_meta_title"><?php if ( $meta_title !== '' ) { echo do_shortcode(esc_attr($meta_title)); } else { echo get_the_title(); } ?></div>
		</div>


	<div class="preview_meta_description"><?php if ( $meta_description !== '' ) { echo do_shortcode(esc_textarea($meta_description)); } else { echo 'Please provide a meta description by editing the snippet below. If you don’t, Google will try to find a relevant part of your post to show in the search results.'; } ?></div>
	</div>

    <div class="fast_meta_field">
        <label for="custom-meta-title">SEO title</label>
        <input type="text" name="fast_meta_title" id="custom-meta-title" value="<?php if ( $meta_title !== '' ) { echo esc_attr($meta_title); } else { echo get_term($term->term_id)->name; } ?>">
    </div>
    <div class="fast_meta_field fast_des_field">
        <label for="custom-meta-description">Meta description</label>
        <textarea name="fast_meta_description" id="custom-meta-description" rows="5"><?php if ( $meta_description !== '' ) { echo esc_textarea($meta_description); } else { echo ''; } ?></textarea>
    </div>
    
    <div class="fast_meta_field">
        <label for="custom-indexing-options">Indexing options</label>
        <select name="fast_indexing_options" id="custom-indexing-options">
            <option value="index_follow" <?php selected($indexing_options, 'index_follow'); ?>>Index, Follow</option>
            <option value="noindex_follow" <?php selected($indexing_options, 'noindex_follow'); ?>>Noindex, Follow</option>
            <option value="noindex_nofollow" <?php selected($indexing_options, 'noindex_nofollow'); ?>>Noindex, Nofollow</option>
        </select>
    </div>
</div>


<?php addMetaScript(); ?>


    <?php
}
}

// Add custom meta box to taxonomy edit screens
function add_custom_meta_box_to_taxonomy() {
    $taxonomies = get_taxonomies(); // Get all taxonomies
    
    foreach ($taxonomies as $taxonomy) {
        add_action($taxonomy . '_edit_form', 'display_existing_taxonomy_meta_values');
        add_action($taxonomy . '_add_form_fields', 'display_existing_taxonomy_meta_values');
    }
}
add_action('admin_init', 'add_custom_meta_box_to_taxonomy');





function addMetaScript() {
?>
<script>
jQuery(document).ready(function($) {

    $('#custom-meta-title, #custom-meta-description').on('keyup', function() {
        updatePreview($(this).attr('id'));
    });

    function updatePreview(id) {
        var text = $('#' + id).val();

        var shortcodes = {
            '[year]': new Date().getFullYear().toString(),  // ensure a string
            '[month]': new Date().toLocaleString('default', { month: 'long' }),
            '[day]': new Date().toLocaleString('default', { day: 'numeric' }).toString(), // ensure a string
            '[sitename]': <?php echo "'" . get_option('blogname') .  "'"; ?>
            // ... add more shortcodes here ...
        };

        for (var shortcode in shortcodes) {
            text = text.replace(new RegExp('\\' + shortcode, 'g'), shortcodes[shortcode]);
        }

	// Check and update the character count based on the ID
	if (id === 'custom-meta-title') {
		var titleLength = text.length;
		$("#meta-title-count").text(titleLength);

		if (titleLength > 60) {
			$("#meta-title-count").css('color', '#B22222');
		} else {
			$("#meta-title-count").css('color', ''); // default color
		}
	$('.preview_meta_title').text(text);
	} else if (id === 'custom-meta-description') {
		var descriptionLength = text.length;
		$("#meta-des-count").text(descriptionLength);

		if (descriptionLength > 160) {
			$("#meta-des-count").css('color', '#B22222');
		} else {
			$("#meta-des-count").css('color', ''); // default color
		}
	}


        var placeholderText = 'Please provide a meta description by editing the snippet below. If you don’t, Google will try to find a relevant part of your post to show in the search results.';
        if (text.trim() !== '') {
            $('.preview_meta_description').text(text);
        } else {
            $('.preview_meta_description').text(placeholderText);
        }
    }

    // Run the update function for both inputs upon page load
    updatePreview('custom-meta-title');
    updatePreview('custom-meta-description');

    $("#update_post_lower").click(function(e) {
        e.preventDefault();
        $("#publish").click();
    });
});
</script>
<?php
}

?>