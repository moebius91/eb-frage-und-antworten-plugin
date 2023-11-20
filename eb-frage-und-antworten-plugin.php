<?php
/**
 * Plugin Name: EB Frage und Antworten Plugin (Jan-Nikolas Othersen)
 * Description: Ein einfaches "Frage und Antworten"-Plugin für WordPress.
 * Version: 1.1.3
 * Author: Jan-Nikolas Othersen
 */

if (!defined('ABSPATH')) {
    exit; // Exit wenn das Verzeichnis direkt aufgerufen wird.
}

function eb_register_faq_post_type() {
    $args = array(
        'public' => false,
        'show_ui' => true,
        'label'  => 'FAQ',
        'supports' => array('title', 'editor'),
        'menu_icon' => 'dashicons-editor-help',
		'show_in_rest' => true,
    );
    register_post_type('eb_faq', $args);
}
add_action('init', 'eb_register_faq_post_type');

function eb_faq_shortcode($atts) {
    global $eb_faq_used;
    $eb_faq_used = true;

    $atts = shortcode_atts(array('id' => ''), $atts);
    $post_id = intval($atts['id']);

    if(!$post_id) return '';

    $faq_post = get_post($post_id);

    if(!$faq_post || $faq_post->post_type !== 'eb_faq') return '';

    $content = apply_filters('the_content', $faq_post->post_content);

    // Generieren einer eindeutigen ID für die Checkbox und das Label
    $unique_id = 'eb-faq-' . $post_id . '-' . wp_generate_uuid4();

    $output = "<div class='eb-faq-item'>";
    $output .= "<input type='checkbox' id='{$unique_id}' class='eb-faq-toggle'>";
    $output .= "<label for='{$unique_id}' class='eb-faq-question'>{$faq_post->post_title}</label>";
    $output .= "<div class='eb-faq-answer'>{$content}</div>";
    $output .= "</div>";
    
    return $output;
}
add_shortcode('eb_faq', 'eb_faq_shortcode');


function eb_faq_styles() {
    global $post;
    if ( has_shortcode($post->post_content, 'eb_faq') ) {
    ?>
    <style>
        .eb-faq-item {
            margin-bottom: 10px;
        }

        .eb-faq-question {
            display: block;
            padding: 10px;
            background-color: #d9d9d9;
            cursor: pointer;
            border: 1px solid #dcdcdc;
        }

        .eb-faq-answer {
            display: none;
            padding: 10px;
            border-left: 1px solid #dcdcdc;
            border-right: 1px solid #dcdcdc;
            border-bottom: 1px solid #dcdcdc;
        }

        .eb-faq-toggle {
            display: none;
        }

        .eb-faq-toggle:checked + .eb-faq-question + .eb-faq-answer {
            display: block;
        }
    </style>
    <?php }
}
add_action('wp_head', 'eb_faq_styles');

function eb_register_gutenberg_faq_block() {
    // Pfad zum JS-Skript innerhalb des Plugin-Verzeichnisses
    $script_path = plugin_dir_url(__FILE__) . 'js/eb-faq-block-editor.js';

    // Registriere das Block-Editor-Skript
    wp_register_script(
        'eb-faq-block-editor',
        $script_path,
        array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-data'), // Abhängigkeiten
        filemtime(plugin_dir_path(__FILE__) . 'js/eb-faq-block-editor.js')
    );

    // Registriere den Block
    register_block_type('eb/faq-block', array(
        'editor_script' => 'eb-faq-block-editor',
        'render_callback' => 'eb_faq_block_render_callback' // Optional: für serverseitiges Rendering
    ));
}
add_action('init', 'eb_register_gutenberg_faq_block');

// Optional: Serverseitiges Rendering des Blocks
function eb_faq_block_render_callback($attributes) {
    if(empty($attributes['selectedPost'])) {
        return ''; // Kein Beitrag ausgewählt
    }

    $post_id = intval($attributes['selectedPost']);
    $faq_post = get_post($post_id);

    if(!$faq_post || $faq_post->post_type !== 'eb_faq') {
        return ''; // Ungültiger Beitrag oder falscher Beitragstyp
    }

    // Verwenden Sie eine eindeutige ID für die Checkbox und das Label
    $unique_id = 'eb-faq-' . $post_id . '-' . wp_generate_uuid4();

    // Generieren des HTML-Codes für die Anzeige des Beitrags
    $content = apply_filters('the_content', $faq_post->post_content);
    $output = "<div class='eb-faq-item'>";
    $output .= "<input type='checkbox' id='{$unique_id}' class='eb-faq-toggle'>";
    $output .= "<label for='{$unique_id}' class='eb-faq-question'>{$faq_post->post_title}</label>";
    $output .= "<div class='eb-faq-answer'>{$content}</div>";
    $output .= "</div>";

    return $output;
}

