<?php
/**
 * Chạy một lần bằng PHP dòng lệnh trên host để dựng trang, cửa hàng và đơn minh họa.
 * Từ chối khi mở bằng trình duyệt.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$root = '/home/giqisvhphosting/public_html';
require $root . '/wp-load.php';

if (get_option('thanh_lan_setup_done')) {
    fwrite(STDOUT, "already done\n");
    exit(0);
}

@set_time_limit(0);
kses_remove_filters();

$log = WP_CONTENT_DIR . '/uploads/thanh-lan-setup.log';
$write = function ($message) use ($log) {
    file_put_contents($log, $message . "\n", FILE_APPEND);
    fwrite(STDOUT, $message . "\n");
};

$write('start ' . gmdate('c'));

update_option('blogname', 'Thanh Lan Spa');
update_option('blogdescription', 'Mẹ Đẹp Bé Khỏe Vui Vẻ Cả Nhà');
update_option('timezone_string', 'Asia/Ho_Chi_Minh');
update_option('date_format', 'd/m/Y');
update_option('time_format', 'H:i');
update_option('start_of_week', 1);
update_option('permalink_structure', '/%postname%/');
update_option('admin_email', 'thanhlanspa238@gmail.com');

$admin = get_user_by('login', 'tolan');
if ($admin) {
    wp_set_current_user($admin->ID);
    wp_update_user(array('ID' => $admin->ID, 'user_email' => 'thanhlanspa238@gmail.com', 'display_name' => 'Tô Lan'));
}

switch_theme('thanh-lan');
$write('theme on');

require_once ABSPATH . 'wp-admin/includes/plugin.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

if (!is_plugin_active('woocommerce/woocommerce.php')) {
    $activated = activate_plugin('woocommerce/woocommerce.php');
    if (is_wp_error($activated)) {
        $write('woo activate error ' . $activated->get_error_message());
        exit(1);
    }
}
$write('woo active');

update_option('woocommerce_currency', 'VND');
update_option('woocommerce_currency_pos', 'right_space');
update_option('woocommerce_price_thousand_sep', '.');
update_option('woocommerce_price_decimal_sep', ',');
update_option('woocommerce_price_num_decimals', '0');
update_option('woocommerce_default_country', 'VN:SG');
update_option('woocommerce_store_address', 'Số 77, đường 537, Ấp Bàu Trăn');
update_option('woocommerce_store_city', 'Xã Nhuận Đức');
update_option('woocommerce_store_postcode', '');
update_option('woocommerce_allowed_countries', 'specific');
update_option('woocommerce_specific_allowed_countries', array('VN'));
update_option('woocommerce_enable_guest_checkout', 'yes');
update_option('woocommerce_calc_taxes', 'no');

$shop_id = function_exists('wc_get_page_id') ? (int) wc_get_page_id('shop') : 0;
if ($shop_id > 0) {
    wp_update_post(array(
        'ID' => $shop_id,
        'post_title' => 'Cửa hàng',
        'post_name' => 'cua-hang',
        'post_status' => 'publish',
    ));
}

$categories = array(
    'cham-soc-bau' => 'Chăm sóc bầu',
    'cham-soc-be' => 'Chăm sóc bé',
    'cham-soc-me' => 'Chăm sóc mẹ',
    'cham-soc-toc' => 'Chăm sóc tóc',
);
$cat_ids = array();
foreach ($categories as $slug => $name) {
    $existing = term_exists($slug, 'product_cat');
    if ($existing) {
        $cat_ids[$slug] = (int) (is_array($existing) ? $existing['term_id'] : $existing);
        continue;
    }
    $created = wp_insert_term($name, 'product_cat', array('slug' => $slug));
    if (is_wp_error($created)) {
        $write('cat error ' . $slug . ' ' . $created->get_error_message());
        continue;
    }
    $cat_ids[$slug] = (int) $created['term_id'];
}

function thanh_lan_image($file) {
    $path = WP_CONTENT_DIR . '/uploads/thanh-lan/' . $file;
    if (!file_exists($path)) {
        return 0;
    }
    $existing = get_posts(array(
        'post_type' => 'attachment',
        'title' => $file,
        'posts_per_page' => 1,
        'post_status' => 'inherit',
        'fields' => 'ids',
    ));
    if ($existing) {
        return (int) $existing[0];
    }
    $filetype = wp_check_filetype($path);
    $id = wp_insert_attachment(array(
        'post_mime_type' => $filetype['type'],
        'post_title' => $file,
        'post_status' => 'inherit',
    ), $path);
    if (is_wp_error($id) || !$id) {
        return 0;
    }
    wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $path));
    return (int) $id;
}

$samples = array(
    array('Dầu massage bầu', '220000', '120000', 'cham-soc-bau', 'sp-bau.jpg', 'Giá minh họa cho nhóm chăm sóc bầu.'),
    array('Dầu tắm bé', '180000', '90000', 'cham-soc-be', 'sp-be.jpg', 'Giá minh họa cho nhóm chăm sóc bé.'),
    array('Muối thảo dược mẹ', '150000', '70000', 'cham-soc-me', 'sp-me.jpg', 'Giá minh họa cho nhóm chăm sóc mẹ.'),
    array('Dầu dưỡng tóc sau sinh', '160000', '80000', 'cham-soc-toc', 'sp-toc.jpg', 'Giá minh họa cho nhóm chăm sóc tóc.'),
);
$made = array();
foreach ($samples as $sample) {
    list($name, $price, $cost, $cat, $image, $blurb) = $sample;
    $found = get_page_by_title($name, OBJECT, 'product');
    if ($found) {
        $made[] = wc_get_product($found->ID);
        continue;
    }
    $product = new WC_Product_Simple();
    $product->set_name($name);
    $product->set_status('publish');
    $product->set_catalog_visibility('visible');
    $product->set_regular_price($price);
    $product->set_short_description($blurb . ' Admin sửa giá, ảnh và nội dung trong mục Sản phẩm.');
    $product->set_description('Sản phẩm minh họa của Thanh Lan Spa. Giá này chưa phải bảng giá chốt. Admin đăng, sửa hoặc xóa trực tiếp trong quản trị.');
    if (!empty($cat_ids[$cat])) {
        $product->set_category_ids(array($cat_ids[$cat]));
    }
    $product->update_meta_data('_thanh_lan_cost', $cost);
    $image_id = thanh_lan_image($image);
    if ($image_id) {
        $product->set_image_id($image_id);
    }
    $product->save();
    $made[] = $product;
}
$write('products ' . count($made));

if (function_exists('wc_get_orders') && !wc_get_orders(array('limit' => 1))) {
    $order = wc_create_order(array('created_via' => 'thanh-lan-setup'));
    if (!is_wp_error($order) && !empty($made[0]) && !empty($made[1])) {
        $order->add_product($made[0], 1);
        $order->add_product($made[1], 2);
        $order->set_address(array(
            'first_name' => 'Khách',
            'last_name' => 'minh họa',
            'phone' => '0975399319',
            'email' => 'thanhlanspa238@gmail.com',
            'address_1' => 'Số 77, đường 537, Ấp Bàu Trăn',
            'city' => 'Xã Nhuận Đức',
            'country' => 'VN',
        ), 'billing');
        $order->set_customer_note('Đơn minh họa để xem lợi nhuận, số lượng và lịch sử. Admin xóa được.');
        $order->calculate_totals();
        $order->set_status('completed', 'Đơn minh họa', true);
        $order->set_date_paid(time());
        $order->save();
        $write('sample order ' . $order->get_id());
    }
}

$import_path = WP_CONTENT_DIR . '/thanh-lan-import.json';
$pages = json_decode(file_get_contents($import_path), true);
$shop_url = $shop_id ? get_permalink($shop_id) : home_url('/cua-hang/');
$link_map = array(
    'index.html' => home_url('/'),
    'cua-hang.html' => $shop_url,
    'tim-kiem.html' => home_url('/'),
);
foreach ($pages as $page) {
    $link_map[$page['file']] = !empty($page['front']) ? home_url('/') : home_url('/' . $page['slug'] . '/');
}
foreach ($categories as $slug => $name) {
    $file = array(
        'cham-soc-bau' => 'san-pham-bau.html',
        'cham-soc-be' => 'san-pham-be.html',
        'cham-soc-me' => 'san-pham-me.html',
        'cham-soc-toc' => 'san-pham-toc.html',
    )[$slug];
    $term = get_term_by('slug', $slug, 'product_cat');
    $link_map[$file] = ($term && !is_wp_error(get_term_link($term))) ? get_term_link($term) : $shop_url;
}

$rewrite = function ($html) use (&$link_map) {
    $html = preg_replace_callback('/href="([^"]+\.html)"/', function ($match) use (&$link_map) {
        $file = basename(parse_url($match[1], PHP_URL_PATH));
        $url = isset($link_map[$file]) ? $link_map[$file] : home_url('/');
        return 'href="' . esc_url($url) . '"';
    }, $html);
    return str_replace('src="library/', 'src="' . content_url('/uploads/thanh-lan/'), $html);
};

$front_id = 0;
foreach ($pages as $page) {
    $existing = get_page_by_path($page['slug']);
    $html = $rewrite($page['html']);
    if ($existing) {
        wp_update_post(array('ID' => $existing->ID, 'post_content' => $html, 'post_status' => 'publish', 'post_title' => $page['title']));
        $id = $existing->ID;
    } else {
        $id = wp_insert_post(array(
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_title' => $page['title'],
            'post_name' => $page['slug'],
            'post_content' => $html,
        ));
    }
    if (!empty($page['front'])) {
        $front_id = $id;
    }
    $write('page ' . $page['slug'] . ' ' . $id);
}

if ($front_id) {
    update_option('show_on_front', 'page');
    update_option('page_on_front', $front_id);
}

if (function_exists('wp_download_language_pack')) {
    $vi = wp_download_language_pack('vi');
    if ($vi) {
        update_option('WPLANG', 'vi');
        $write('locale vi');
    }
}

flush_rewrite_rules();
update_option('thanh_lan_setup_done', gmdate('c'));
$write('done');
@unlink(__FILE__);
