<?php
if(!defined('ABSPATH')) {
    exit;
}

/*
* Delete the audio file and post meta
*/
add_action('wp_ajax_delete_tts', 'ai_tts_delete_tts_callback');
function ai_tts_delete_tts_callback() {

    // If user is admin
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'You do not have permission to delete this file.']);
    }
    // Check nonce
    if (!wp_verify_nonce(esc_html(sanitize_text_field(wp_unslash($_POST['nonce']))), 'ai_tts_nonce')) {
        wp_send_json_error(['message' => 'Nonce verification failed.']);
    }

    // Get the file storage location
    $file_storage_location = get_post_meta($_POST['post_id'], 'ai_tts_location', true);

    // Delete the audio file
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $file_url = get_post_meta($post_id, 'ai_tts_file_url', true);

    // If the file is stored locally, delete it
    $file_path = '';
    if ($file_storage_location == 'local'|| !$file_storage_location) {
        $file_path = str_replace(content_url(), WP_CONTENT_DIR, $file_url);
        unlink($file_path);
    }

    // Allow custom actions to delete the file
    do_action('delete_ai_tts_file', $file_url, $file_path, $post_id);

    // Delete the post meta
    delete_post_meta($post_id, 'ai_tts_data');

    wp_send_json_success(['message' => 'File deleted successfully.']);

}