<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/*
* Add the meta box
*/
add_action( 'add_meta_boxes', 'ai_tts_add_meta_box' );
function ai_tts_add_meta_box() {
    add_meta_box(
        'ai-tts-meta-box',
        esc_html__( 'AI Text to Speech', 'ai-text-to-speech' ),
        'ai_tts_meta_box_callback',
        'post',
        'side',
        'low'
    );
    // Add to all other post types
    $post_types = get_post_types( array(
        'public' => true,
    ) );
    foreach ( $post_types as $post_type ) {
        add_meta_box(
            'ai-tts-meta-box',
            esc_html__( 'AI Text to Speech', 'ai-text-to-speech' ),
            'ai_tts_meta_box_callback',
            $post_type,
            'side',
            'low'
        );
    }
}

/*
* Meta box display callback
*
* @param WP_Post $post Current post object.
*/
function ai_tts_meta_box_callback(  $post  ) {
    // Add a nonce field for security
    wp_nonce_field( 'ai_tts_save_meta_box_data', 'ai_tts_meta_box_nonce' );
    ?>

    <!-- Voice selection field for generation only -->
    <p id="ai-tts-voice-field" <?php 
    if ( get_post_meta( $post->ID, 'ai_tts_file_url', true ) ) {
        echo 'style="display: none;"';
    }
    ?>>
        <label for="ai-tts-voice"><?php 
    echo esc_html__( 'Voice:', 'ai-text-to-speech' );
    ?></label>
        <select id="ai-tts-voice" name="ai-tts-voice" style="width: 50%;">
            <option value="alloy">Alloy</option>
            <option value="echo">Echo</option>
            <option value="fable">Fable</option>
            <option value="onyx">Onyx</option>
            <option value="nova">Nova</option>
            <option value="shimmer">Shimmer</option>
        </select>
    </p>

    <!-- Estimate of cost -->
    <p id="tts-cost" style="<?php 
    if ( get_post_meta( $post->ID, 'ai_tts_file_url', true ) ) {
        echo 'display: none;';
    }
    ?>font-size: 10px;">
        <span id="tts-cost-characters" style="display: inline-block;">0</span> <?php 
    echo esc_html__( 'characters.', 'ai-text-to-speech' );
    ?>
        <span title="<?php 
    echo esc_html__( 'Estimate for $0.015 per character. This estimate may not be 100% accurate.', 'ai-text-to-speech' );
    ?>"
        style="display: inline-block;">
            <?php 
    echo esc_html__( 'Estimate cost:', 'ai-text-to-speech' );
    ?> <span id="tts-cost-amount" style="display: inline-block;">$0.00</span>
        </span>
    </p>

    <!-- Checkbox: Customise content -->
    <p id="ai-tts-customise-checkbox" <?php 
    if ( !aitts_fs()->is_paying_or_trial() ) {
        ?>style="pointer-events: none; opacity: 0.5;"<?php 
    }
    ?>>
        <input type="checkbox" id="ai-tts-customise" name="ai-tts-customise"
        <?php 
    checked( get_post_meta( $post->ID, 'ai_tts_customise', true ), 'on' );
    ?> />
        <label for="ai-tts-customise">
            <?php 
    echo esc_html__( 'Customise TTS content', 'ai-text-to-speech' );
    ?>
            <?php 
    if ( !aitts_fs()->is_paying_or_trial() ) {
        ?>
                (PRO)
            <?php 
    }
    ?>
        </label>
    </p>

    <script>
    jQuery(document).ready(function($) {
        if ($('#tts-link-text').val()) {
            $('#ai-tts-customise-checkbox').hide();
        }
        $('#ai-tts-customise').change(function() {
            if ($(this).is(':checked')) {
                $('#ai-tts-customise-options').show();
            } else {
                $('#ai-tts-customise-options').hide();
            }
        });
        $('#ai-tts-customise').change();
    });
    </script>

    <div id="ai-tts-customise-options">

    <?php 
    ?>

    </div>

    <!-- Generate TTS button -->
    <p id="generate-tts-content" <?php 
    if ( get_post_meta( $post->ID, 'ai_tts_file_url', true ) ) {
        echo 'style="display: none;"';
    }
    ?> style="margin: 0;">
        <button id="generate-tts" data-postid="<?php 
    echo esc_attr( $post->ID );
    ?>"
            data-nonce="<?php 
    echo esc_html( wp_create_nonce( 'ai_tts_nonce' ) );
    ?>"
            class="button button-primary" style="width: 100%;">
            <?php 
    echo esc_html__( 'Generate TTS', 'ai-text-to-speech' );
    ?>
        </button>
    </p>

    <div id="tts-loading" style="display: none;">
        <p style="margin: 0;"><span class="dashicons dashicons-update dashicons-spin tts-spin"></span> <?php 
    echo esc_html__( 'Generating audio file...', 'ai-text-to-speech' );
    ?></p>
    </div>

    <div id="tts-deleting" style="display: none;">
        <p><span class="dashicons dashicons-update dashicons-spin tts-spin"></span> <?php 
    echo esc_html__( 'Deleting file...', 'ai-text-to-speech' );
    ?></p>
    </div>

    <!-- Player for the audio file -->
    <?php 
    $tts_file_url = get_post_meta( $post->ID, 'ai_tts_file_url', true );
    if ( !file_exists( str_replace( content_url(), WP_CONTENT_DIR, $tts_file_url ) ) && (get_post_meta( $post->ID, 'ai_tts_location', true ) == 'local' || !get_post_meta( $post->ID, 'ai_tts_location', true )) ) {
        $tts_file_url = '';
        delete_post_meta( $post->ID, 'ai_tts_file_url' );
        delete_post_meta( $post->ID, 'ai_tts_file_name' );
        delete_post_meta( $post->ID, 'ai_tts_location' );
    }
    ?>
    <div id="tts-player" style="display: none;">

        <p style="margin: 10px 0 5px 0;"><?php 
    echo esc_html__( 'Generated TTS file:', 'ai-text-to-speech' );
    ?> <?php 
    if ( $tts_file_url && file_exists( str_replace( content_url(), WP_CONTENT_DIR, $tts_file_url ) ) ) {
        ?>
            <span id="tts-file-size">
                <?php 
        echo esc_html( round( filesize( str_replace( content_url(), WP_CONTENT_DIR, $tts_file_url ) ) / 1000, 2 ) );
        ?> KB
            </span>
        <?php 
    }
    ?></p>

        <audio id="tts-player-audio"controls src="<?php 
    echo esc_url( $tts_file_url );
    ?>" style="background: #f3f3f3; border-radius: 5px;"></audio>

        <!-- Link Text Field -->
        <p style="margin: 5px 0 5px 0;">
            <input type="text" id="tts-link-text" name="tts-link-text" value="<?php 
    echo esc_attr( get_post_meta( $post->ID, 'ai_tts_file_url', true ) );
    ?>" style="width: 100%;"/>
        </p>

        <!-- Copy File URL -->
        <p id="tts-copy-url" style="font-size: 10px; text-align: center; margin: 5px 0 5px 0; padding-bottom: 0;">
            <button id="copy-tts-url" class="button button-secondary" style="width: 49%;">
                <?php 
    echo esc_html__( 'Copy File URL', 'ai-text-to-speech' );
    ?>
            </button>
            <!-- Delete File -->
            <button id="delete-tts" data-postid="<?php 
    echo esc_html( $post->ID );
    ?>"
                data-nonce="<?php 
    echo esc_html( wp_create_nonce( 'ai_tts_nonce' ) );
    ?>"
                class="button button-secondary" style="width: 49%;">
                <?php 
    echo esc_html__( 'Delete File', 'ai-text-to-speech' );
    ?>
            </button>
        </p>

    </div>

    <?php 
    if ( !aitts_fs()->is_paying_or_trial() ) {
        ?>
    <br/><p style="font-size: 9px; color: #333;"><?php 
        echo esc_html__( 'Want more AI TTS features?', 'ai-text-to-speech' );
        ?>
        <a href="<?php 
        echo esc_url( aitts_fs()->get_upgrade_url() );
        ?>" target="_blank"><?php 
        echo esc_html__( 'Upgrade to PRO', 'ai-text-to-speech' );
        ?></a>
    </p>
    <?php 
    }
    ?>

    <?php 
    // Action after the meta box
    do_action( 'ai_tts_meta_box_after', $post );
}
