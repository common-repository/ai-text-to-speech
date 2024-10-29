<?php
if(!defined('ABSPATH')) {
    exit;
}

/*
* Add the audio player to the top of the post content
*
* @param string $content The post content.
*
* @return string The updated post content.
*/
add_filter('the_content', 'ai_tts_add_audio_player');
function ai_tts_add_audio_player($content) {
    if (is_single() && in_the_loop() && is_main_query()) {
        $post_id = get_the_ID();
        $tts_file_url = get_post_meta($post_id, 'ai_tts_file_url', true);
        if ($tts_file_url) {

            if ( aitts_fs()->is_paying_or_trial() && ai_tts_get_option('enable_statistics') ) {
                // Track view when the post with an audio player is loaded
                ai_tts_track_view($post_id);
            }

            // Get audio player
            $audio_player = '<audio class="ai-tts-player" controls preload="metadata" data-post-id="' . $post_id . '"
            src="' . esc_url($tts_file_url) . '" style="width: 100%; max-width: 790px; display: block; margin-bottom: 10px;"></audio>';
            $audio_player = apply_filters('ai_tts_audio_player', $audio_player, $post_id);

            // Add player label
            $show_player_label = ai_tts_get_option('show_player_label');
            $player_label = ai_tts_get_option('player_label');
            if($show_player_label && $player_label) {
                $audio_player .= '<p style="font-size: 10px; text-align: center; margin-bottom: 25px; margin-top: -5px; padding-bottom: 0;"><span data-nosnippet>'.esc_html($player_label).'</span></p>';
            } else {
                $audio_player .= '<br/>';
            }

            $content = $audio_player . $content;
        }
    }
    return $content;
}