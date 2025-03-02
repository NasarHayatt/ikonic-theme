<?php
// Enqueue styles and scripts
function my_custom_theme_scripts() {
    wp_enqueue_style('theme-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'my_custom_theme_scripts');

// Register navigation menu
function my_custom_theme_setup() {
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'my-custom-theme'),
    ));
}
add_action('after_setup_theme', 'my_custom_theme_setup');

// Register Projects custom post type
function register_projects_post_type() {
    $args = array(
        'public' => true,
        'label' => 'Projects',
        'supports' => array('title', 'editor'),
        'has_archive' => true,
    );
    register_post_type('projects', $args);

    // Add custom fields manually (without ACF)
    add_action('add_meta_boxes', function() {
        add_meta_box('project_details', 'Project Details', 'project_meta_box_callback', 'projects', 'normal', 'high');
    });

    add_action('save_post', function($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (get_post_type($post_id) !== 'projects') return;
        if (!current_user_can('edit_post', $post_id)) return;

        $fields = ['project_description', 'project_start_date', 'project_end_date', 'project_url'];
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
            }
        }
    });
}
add_action('init', 'register_projects_post_type');

function project_meta_box_callback($post) {
    wp_nonce_field('project_meta_box', 'project_meta_box_nonce');
    $fields = [
        'project_description' => 'Description',
        'project_start_date' => 'Start Date',
        'project_end_date' => 'End Date',
        'project_url' => 'URL',
    ];
    foreach ($fields as $key => $label) {
        $value = get_post_meta($post->ID, $key, true);
        echo "<p><label>$label:</label><br>";
        if ($key === 'project_description') {
            echo "<textarea name='$key'>$value</textarea>";
        } else {
            echo "<input type='text' name='$key' value='" . esc_attr($value) . "'></p>";
        }
    }
}

// Custom REST API endpoint for Projects
add_action('rest_api_init', function() {
    register_rest_route('mytheme/v1', '/projects', array(
        'methods' => 'GET',
        'callback' => 'get_projects_api',
        'permission_callback' => '__return_true',
    ));
});

function get_projects_api($request) {
    $args = array('post_type' => 'projects', 'posts_per_page' => -1);
    $projects = get_posts($args);
    $data = array();

    foreach ($projects as $project) {
        $data[] = array(
            'title' => get_the_title($project->ID),
            'url' => get_post_meta($project->ID, 'project_url', true),
            'start_date' => get_post_meta($project->ID, 'project_start_date', true),
            'end_date' => get_post_meta($project->ID, 'project_end_date', true),
        );
    }
    return rest_ensure_response($data);
}
// Add Unused Media admin page
function unused_media_menu() {
    add_menu_page('Unused Media', 'Unused Media', 'manage_options', 'unused-media', 'unused_media_page', 'dashicons-media-default');
}
add_action('admin_menu', 'unused_media_menu');

function unused_media_page() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    require_once get_template_directory() . '/admin-unused-media.php';
}

// Enqueue admin script
function unused_media_scripts($hook) {
    if ($hook !== 'toplevel_page_unused-media') return;
    wp_enqueue_script('unused-media-script', get_template_directory_uri() . '/js/admin-script.js', array('jquery'), '1.0', true);
    wp_localize_script('unused-media-script', 'unusedMedia', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('delete_unused_media'),
    ));
}
add_action('admin_enqueue_scripts', 'unused_media_scripts');

// AJAX handler for deletion
add_action('wp_ajax_delete_unused_media', 'delete_unused_media_callback');
function delete_unused_media_callback() {
    check_ajax_referer('delete_unused_media', 'nonce');
    if (!current_user_can('manage_options')) wp_die('Unauthorized');

    $attachment_id = intval($_POST['attachment_id']);
    if ($attachment_id && wp_delete_attachment($attachment_id, true)) {
        wp_send_json_success('Media deleted');
    } else {
        wp_send_json_error('Deletion failed');
    }
}