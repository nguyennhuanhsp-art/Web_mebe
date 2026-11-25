<?php
if (!defined('ABSPATH')) {
    exit;
}

add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_image_size('thanh-lan-card', 960, 640, true);
    add_theme_support('woocommerce');
    add_theme_support('html5', array('search-form', 'gallery', 'caption'));
});

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'thanh-lan-fonts',
        'https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;600;700;800&family=Great+Vibes&family=Playfair+Display:wght@600;700&display=swap',
        array(),
        null
    );
    wp_enqueue_style('thanh-lan-site', get_template_directory_uri() . '/assets/site.css', array('thanh-lan-fonts'), '1.2.0');
    wp_enqueue_style('thanh-lan', get_stylesheet_uri(), array('thanh-lan-site'), '1.2.0');
    wp_enqueue_script('thanh-lan-site', get_template_directory_uri() . '/assets/site.js', array(), '1.2.0', true);
});

add_filter('woocommerce_enqueue_styles', '__return_empty_array');
