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
    wp_enqueue_style('thanh-lan-site', get_template_directory_uri() . '/assets/site.css', array('thanh-lan-fonts'), '1.8.2');
    wp_enqueue_style('thanh-lan', get_stylesheet_uri(), array('thanh-lan-site'), '1.3.4');
    wp_enqueue_script('thanh-lan-site', get_template_directory_uri() . '/assets/site.js', array(), '1.7.6', true);
    wp_localize_script('thanh-lan-site', 'thanhLanLead', array(
        'ajax' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('thanh_lan_lead'),
    ));
});

add_filter('woocommerce_enqueue_styles', '__return_empty_array');

add_filter('woocommerce_product_tabs', function ($tabs) {
    if (isset($tabs['description'])) {
        $tabs['description']['title'] = 'Mô tả';
    }
    return $tabs;
});
add_filter('woocommerce_product_description_heading', function () {
    return 'Mô tả';
});
add_filter('woocommerce_product_related_products_heading', function () {
    return 'Sản phẩm liên quan';
});
add_filter('gettext', function ($translated, $text, $domain) {
    if ($domain !== 'woocommerce') {
        return $translated;
    }
    if ($text === 'Description') {
        return 'Mô tả';
    }
    if ($text === 'Related products') {
        return 'Sản phẩm liên quan';
    }
    return $translated;
}, 10, 3);

add_filter('woocommerce_is_purchasable', '__return_false');
add_filter('woocommerce_cart_redirect_after_error', function () {
    return home_url('/cua-hang/');
});
add_action('template_redirect', function () {
    if (function_exists('is_cart') && (is_cart() || is_checkout())) {
        wp_safe_redirect(home_url('/cua-hang/'));
        exit;
    }
});
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
add_action('wp_loaded', function () {
    if (!function_exists('WC') || !WC()->cart) {
        return;
    }
    if (!WC()->cart->is_empty()) {
        WC()->cart->empty_cart();
    }
    if (function_exists('wc_clear_notices')) {
        wc_clear_notices();
    }
}, 20);

function thanh_lan_product_blurb() {
    global $product;
    if (!$product) {
        return;
    }
    $text = trim(wp_strip_all_tags($product->get_short_description()));
    if ($text === '') {
        return;
    }
    echo '<p class="product-blurb">' . esc_html($text) . '</p>';
}
add_action('woocommerce_after_shop_loop_item_title', 'thanh_lan_product_blurb', 8);

function thanh_lan_product_label() {
    global $product;
    if (!$product) {
        return;
    }
    $terms = get_the_terms($product->get_id(), 'product_cat');
    if (!$terms || is_wp_error($terms)) {
        return;
    }
    echo '<p class="product-label">' . esc_html($terms[0]->name) . '</p>';
}
add_action('woocommerce_shop_loop_item_title', 'thanh_lan_product_label', 5);
add_action('woocommerce_single_product_summary', 'thanh_lan_product_label', 4);

function thanh_lan_quote_button($centered = false) {
    global $product;
    $name = $product ? $product->get_name() : '';
    $url = add_query_arg('sanpham', $name, home_url('/tu-van/'));
    $link = '<a class="button quote" href="' . esc_url($url) . '">Liên Hệ Báo Giá</a>';
    if ($centered) {
        echo '<p class="product-quote">' . $link . '</p>';
        return;
    }
    echo $link;
}
add_action('woocommerce_after_shop_loop_item', 'thanh_lan_quote_button', 15);
add_action('woocommerce_single_product_summary', function () {
    thanh_lan_quote_button(true);
}, 15);

function thanh_lan_shop_note() {
    echo '<p class="shop-note">Sản phẩm được phân phối trong hệ thống Thanh Lan Spa và các cơ sở chăm sóc mẹ &amp; bé các bệnh viện Xuyên Á trên toàn quốc. Liên hệ để được báo giá sỉ.</p>';
}
add_action('woocommerce_before_shop_loop', 'thanh_lan_shop_note', 5);
add_action('woocommerce_before_single_product', 'thanh_lan_shop_note', 5);

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
