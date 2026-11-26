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
    wp_enqueue_style('thanh-lan-site', get_template_directory_uri() . '/assets/site.css', array('thanh-lan-fonts'), '1.7.3');
    wp_enqueue_style('thanh-lan', get_stylesheet_uri(), array('thanh-lan-site'), '1.2.0');
    wp_enqueue_script('thanh-lan-site', get_template_directory_uri() . '/assets/site.js', array(), '1.7.4', true);
    wp_localize_script('thanh-lan-site', 'thanhLanLead', array(
        'ajax' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('thanh_lan_lead'),
    ));
});

add_filter('woocommerce_enqueue_styles', '__return_empty_array');

remove_action('wp_head', 'wp_site_icon', 99);

add_action('wp_head', function () {
    $base = trailingslashit(get_template_directory_uri()) . 'assets/';
    $share = $base . 'share-thanh-lan.png';
    $title = wp_get_document_title();
    $desc = 'Mẹ và bé, trước và sau sinh. Thanh Lan Spa chăm sóc mẹ bầu, mẹ sau sinh và bé. Hotline 0975.399.319.';
    if (is_singular()) {
        $excerpt = trim(wp_strip_all_tags(get_the_excerpt()));
        if ($excerpt !== '') {
            $desc = wp_html_excerpt($excerpt, 180, '…');
        }
        $url = get_permalink();
    } else {
        global $wp;
        $path = isset($wp->request) ? trim((string) $wp->request, '/') : '';
        $url = $path === '' ? home_url('/') : home_url('/' . $path . '/');
    }
    echo '<link rel="icon" href="' . esc_url($base . 'favicon.ico') . '" sizes="any" />' . "\n";
    echo '<link rel="icon" href="' . esc_url($base . 'favicon-32.png') . '" type="image/png" sizes="32x32" />' . "\n";
    echo '<link rel="icon" href="' . esc_url($base . 'favicon-192.png') . '" type="image/png" sizes="192x192" />' . "\n";
    echo '<link rel="apple-touch-icon" href="' . esc_url($base . 'apple-touch-icon.png') . '" />' . "\n";
    echo '<meta property="og:locale" content="vi_VN" />' . "\n";
    echo '<meta property="og:type" content="website" />' . "\n";
    echo '<meta property="og:site_name" content="Thanh Lan Spa" />' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . '" />' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($desc) . '" />' . "\n";
    echo '<meta property="og:url" content="' . esc_url($url) . '" />' . "\n";
    echo '<meta property="og:image" content="' . esc_url($share) . '" />' . "\n";
    echo '<meta property="og:image:secure_url" content="' . esc_url($share) . '" />' . "\n";
    echo '<meta property="og:image:type" content="image/png" />' . "\n";
    echo '<meta property="og:image:width" content="1200" />' . "\n";
    echo '<meta property="og:image:height" content="630" />' . "\n";
    echo '<meta property="og:image:alt" content="Thanh Lan Spa — Mẹ và bé, trước và sau sinh" />' . "\n";
    echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr($title) . '" />' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr($desc) . '" />' . "\n";
    echo '<meta name="twitter:image" content="' . esc_url($share) . '" />' . "\n";
}, 1);
