<?php
if(!defined('ABSPATH')) {
    exit;
}

/*
* Implement ai_tts_save_audio_file function to save the file
*
* @param string $response The response from the API.
* @param int $post_id The post ID.
*/
function ai_tts_save_audio_file($response, $post_id) {

    // If user is admin
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'You do not have permission to edit this file.']);
    }
    // Check nonce
    if (!wp_verify_nonce(esc_html(sanitize_text_field(wp_unslash($_POST['nonce']))), 'ai_tts_nonce')) {
        wp_send_json_error(['message' => 'Nonce verification failed']);
    }

    // Convert post title to URL friendly string
    $post_title = get_post_field('post_title', $post_id);
    $post_title = sanitize_title($post_title);
    $post_title = implode('-', array_slice(explode('-', $post_title), 0, 10));

    // Get the file storage location
    $file_storage_location = ai_tts_get_option('file_storage_location');
    if($file_storage_location != 'local') {
        update_post_meta($post_id, 'ai_tts_location', $file_storage_location);
    }

    // Generate a random string for the file name
    $random_string = wp_generate_password(2, false);
    $file_name = 'post-' . $post_id . '-' . $random_string . '-' . $post_title . '.mp3';

    // Convert the response to an MP3 file and keep file duration
    $mp3 = file_get_contents('data:audio/mp3;base64,' . base64_encode($response));

    // Check if folder exists, if not create it
    if (!file_exists(AI_TTS_UPLOAD_DIR)) {
        mkdir(AI_TTS_UPLOAD_DIR, 0755, true);
    }

    // Delete any other files that start with post-$post_id-
    $files = glob(AI_TTS_UPLOAD_DIR . 'post-' . $post_id . '-*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }

    // Save the file to the uploads directory
    $file_path = AI_TTS_UPLOAD_DIR . $file_name;

    // Save the file to the uploads directory
    global $wp_filesystem;
    if (empty($wp_filesystem)) {
        require_once (ABSPATH . '/wp-admin/includes/file.php');
        WP_Filesystem();
    }
    $wp_filesystem->put_contents($file_path, $mp3, FS_CHMOD_FILE);

    // Get the file URL
    $file_url = content_url('/uploads/ai-text-to-speech/' . $file_name);

    // Allow modification of the file URL
    $file_url = apply_filters('modify_ai_tts_file_url', $file_url, $file_path, $file_name, $post_id);
    
    // Check if successful
    if (!$file_url) {
        wp_send_json_error(['message' => 'There was an error saving the file.']);
    }

    // Sanitize
    $file_url = $file_url;
    $voice = sanitize_text_field($_POST['voice']);

    // Save the file URL to post meta
    update_post_meta($post_id, 'ai_tts_file_url', $file_url);
    if($file_storage_location != 'local') {
        update_post_meta($post_id, 'ai_tts_file_name', $file_name);
    }

    // Return the file URL
    return $file_url;

}