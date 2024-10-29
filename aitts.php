<?php

/**
* Plugin Name: AI Text to Speech
* Description: Easily generate and display an audio version for your posts using OpenAI's TTS API.
* Version: 2.0.3
* Author: Elliot Sowersby, RelyWP
* Author URI: https://relywp.com
* License: GPLv3
* Text Domain: ai-text-to-speech
* Domain Path: /languages
*
*/
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/* Freemius */
if ( function_exists( 'aitts_fs' ) ) {
    aitts_fs()->set_basename( false, __FILE__ );
} else {
    if ( !function_exists( 'aitts_fs' ) ) {
        // Create a helper function for easy SDK access.
        function aitts_fs() {
            global $aitts_fs;
            if ( !isset( $aitts_fs ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $aitts_fs = fs_dynamic_init( array(
                    'id'             => '14778',
                    'slug'           => 'ai-text-to-speech',
                    'premium_slug'   => 'ai-text-to-speech-pro',
                    'type'           => 'plugin',
                    'public_key'     => 'pk_29ce6983a9bad27c0fda5e4370067',
                    'is_premium'     => false,
                    'premium_suffix' => 'Pro',
                    'has_addons'     => false,
                    'has_paid_plans' => true,
                    'menu'           => array(
                        'slug'       => 'ai-text-to-speech',
                        'first-path' => 'admin.php?page=ai-text-to-speech',
                        'account'    => false,
                        'contact'    => false,
                        'support'    => false,
                        'pricing'    => false,
                        'parent'     => array(
                            'slug' => 'options-general.php',
                        ),
                    ),
                    'is_live'        => true,
                ) );
            }
            return $aitts_fs;
        }

        // Init Freemius.
        aitts_fs();
        // Signal that SDK was initiated.
        do_action( 'aitts_fs_loaded' );
    }
    // Define the path for the uploads
    define( 'AI_TTS_UPLOAD_DIR', WP_CONTENT_DIR . '/uploads/ai-text-to-speech/' );
    // Includes
    include plugin_dir_path( __FILE__ ) . 'inc/save.php';
    include plugin_dir_path( __FILE__ ) . 'inc/delete.php';
    include plugin_dir_path( __FILE__ ) . 'inc/player.php';
    include plugin_dir_path( __FILE__ ) . 'inc/meta-box.php';
    include plugin_dir_path( __FILE__ ) . 'inc/options.php';
    // Premium Includes
    if ( aitts_fs()->is_paying_or_trial() ) {
        require_once plugin_dir_path( __FILE__ ) . 'premium-files/inc/vendor/autoload.php';
        include plugin_dir_path( __FILE__ ) . 'premium-files/functions-premium.php';
        if ( ai_tts_get_option( 'enable_statistics' ) ) {
            include plugin_dir_path( __FILE__ ) . 'premium-files/inc/stats/stats.php';
            include plugin_dir_path( __FILE__ ) . 'premium-files/inc/stats/admin-stats.php';
        }
        if ( PHP_VERSION_ID >= 70100 ) {
            include plugin_dir_path( __FILE__ ) . 'premium-files/inc/integrations/dropbox.php';
        }
        include plugin_dir_path( __FILE__ ) . 'premium-files/inc/player-styles.php';
    }
    // Includes (Content Generation)
    include plugin_dir_path( __FILE__ ) . 'inc/content/generate.php';
    include plugin_dir_path( __FILE__ ) . 'inc/content/replace/twitter.php';
    include plugin_dir_path( __FILE__ ) . 'inc/content/replace/numbers.php';
    // Activation hook to create the upload directory
    register_activation_hook( __FILE__, 'ai_tts_create_upload_dir' );
    function ai_tts_create_upload_dir() {
        if ( !file_exists( AI_TTS_UPLOAD_DIR ) ) {
            mkdir( AI_TTS_UPLOAD_DIR, 0755, true );
        }
        // Redirect to the settings page only once on activation
        update_option( 'ai_tts_do_activation_redirect', true );
    }

    // Redirect to the settings page on activation
    add_action( 'admin_init', 'ai_tts_redirect' );
    function ai_tts_redirect() {
        if ( get_option( 'ai_tts_do_activation_redirect', false ) ) {
            delete_option( 'ai_tts_do_activation_redirect' );
            wp_redirect( admin_url( 'options-general.php?page=ai-text-to-speech' ) );
        }
    }

    // Add settings link to plugins list
    add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'ai_tts_settings_link' );
    function ai_tts_settings_link(  $links  ) {
        $settings_link = '<a href="options-general.php?page=ai-text-to-speech">' . esc_html__( 'Settings', 'ai-text-to-speech' ) . '</a>';
        array_unshift( $links, $settings_link );
        $analytics_link = '<a href="admin.php?page=ai-text-to-speech-stats">' . esc_html__( 'Statistics', 'ai-text-to-speech' ) . '</a>';
        array_unshift( $links, $analytics_link );
        return $links;
    }

    // Enqueue JavaScript Frontend
    add_action( 'wp_enqueue_scripts', 'ai_tts_enqueue_scripts_frontend' );
    function ai_tts_enqueue_scripts_frontend() {
        wp_enqueue_script(
            'ai-tts-js',
            plugin_dir_url( __FILE__ ) . 'premium-files/js/player.js',
            array('jquery'),
            null,
            true
        );
        wp_localize_script( 'ai-tts-js', 'aiTTS', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'ai-tts-nonce' ),
        ) );
    }

    // Enqueue JavaScript Admin
    add_action( 'admin_enqueue_scripts', 'ai_tts_enqueue_scripts' );
    function ai_tts_enqueue_scripts() {
        // Only on the post edit screen
        global $post;
        $post_types_option = ai_tts_get_option( 'post_types' );
        $post_types_to_enable = ai_tts_get_option( 'post_types_to_enable' );
        if ( $post_types_option == 'all' ) {
            $post_types = get_post_types( array(
                'public' => true,
            ) );
        } else {
            $post_types = $post_types_to_enable;
        }
        $post_types = get_post_types( array(
            'public' => true,
        ) );
        if ( !is_object( $post ) || !in_array( $post->post_type, $post_types ) ) {
            return;
        }
        wp_enqueue_script(
            'ai-tts-script',
            plugin_dir_url( __FILE__ ) . 'js/script.js',
            array('jquery'),
            '1.0.0',
            true
        );
        wp_enqueue_script(
            'ai-tts-post-script',
            plugin_dir_url( __FILE__ ) . 'js/post.js',
            '',
            '1.0.0',
            true
        );
    }

    // Enqueue JavaScript on Settings Page
    add_action( 'admin_enqueue_scripts', 'ai_tts_enqueue_scripts_settings' );
    function ai_tts_enqueue_scripts_settings() {
        // Only on the plugin settings page
        if ( !isset( $_GET['page'] ) || $_GET['page'] != 'ai-text-to-speech' ) {
            return;
        }
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script(
            'ai-tts-settings-script',
            plugin_dir_url( __FILE__ ) . 'js/settings.js',
            array('jquery', 'wp-color-picker'),
            '1.0.0',
            true
        );
    }

    // Enqueue Admin Posts CSS
    add_action( 'admin_enqueue_scripts', 'ai_tts_enqueue_posts_styles' );
    function ai_tts_enqueue_posts_styles() {
        // On all post type post edit screen
        global $post;
        $post_types_option = ai_tts_get_option( 'post_types' );
        $post_types_to_enable = ai_tts_get_option( 'post_types_to_enable' );
        if ( $post_types_option == 'all' ) {
            $post_types = get_post_types( array(
                'public' => true,
            ) );
        } else {
            $post_types = $post_types_to_enable;
        }
        if ( !is_array( $post_types ) ) {
            $post_types = array();
        }
        if ( !is_object( $post ) || is_array( $post_types ) && !in_array( $post->post_type, $post_types ) ) {
            return;
        }
        wp_enqueue_style(
            'ai-tts-style',
            plugin_dir_url( __FILE__ ) . 'css/post.css',
            array(),
            '1.0.0'
        );
    }

    // Enqueue CSS admin settings page
    add_action( 'admin_enqueue_scripts', 'ai_tts_enqueue_styles_admin' );
    function ai_tts_enqueue_styles_admin() {
        // Only on the plugin settings page
        if ( !isset( $_GET['page'] ) || $_GET['page'] != 'ai-text-to-speech' ) {
            return;
        }
        wp_enqueue_style(
            'ai-tts-style-admin',
            plugin_dir_url( __FILE__ ) . 'css/settings.css',
            array(),
            '1.0.0'
        );
    }

}