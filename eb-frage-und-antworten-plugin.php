<?php
/**
 * Plugin Name: EB Frage und Antworten Plugin (Jan-Nikolas Othersen)
 * Description: Ein einfaches FAQ-Plugin für WordPress.
 * Version: 1.0.0
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
    $output = "<div class='eb-faq-item'>";
    $output .= "<input type='checkbox' id='eb-faq-{$post_id}' class='eb-faq-toggle'>";
    $output .= "<label for='eb-faq-{$post_id}' class='eb-faq-question'>{$faq_post->post_title}</label>";
    $output .= "<div class='eb-faq-answer'>{$content}</div>";
    $output .= "</div>";
    
    return $output;
}
add_shortcode('eb_faq', 'eb_faq_shortcode');

function eb_faq_styles() {
    global $eb_faq_used;
    if (!$eb_faq_used) return;

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
    <?php
}
add_action('wp_head', 'eb_faq_styles');
