<?php
function find_unused_media() {
    $attachments = get_posts(array('post_type' => 'attachment', 'posts_per_page' => -1));
    $unused = array();

    foreach ($attachments as $attachment) {
        $is_used = false;
        $attachment_id = $attachment->ID;

        // Check post content
        $post_query = new WP_Query(array(
            's' => wp_get_attachment_url($attachment_id),
            'posts_per_page' => 1,
        ));
        if ($post_query->have_posts()) {
            $is_used = true;
        }
        wp_reset_postdata();

        // Check custom fields
        global $wpdb;
        $meta_check = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $wpdb->postmeta WHERE meta_value = %d",
            $attachment_id
        ));
        if ($meta_check > 0) {
            $is_used = true;
        }

        if (!$is_used) {
            $unused[] = $attachment;
        }
    }
    return $unused;
}
?>
<div class="wrap">
    <h1>Unused Media</h1>
    <table class="wp-list-table widefat">
        <thead>
            <tr>
                <th>File</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (find_unused_media() as $media) : ?>
                <tr>
                    <td><?php echo esc_html($media->post_title); ?></td>
                    <td><button class="button delete-media" data-id="<?php echo esc_attr($media->ID); ?>">Delete</button></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>