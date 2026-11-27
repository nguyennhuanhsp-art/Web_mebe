<?php
/**
 * Đưa ảnh vào Thư viện WordPress, gắn ảnh đại diện và tạo bài Kiến thức theo chuyên mục.
 * Chỉ chạy bằng PHP dòng lệnh.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require '/home/giqisvhphosting/public_html/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$admin = get_user_by('login', 'tolan');
if ($admin) {
    wp_set_current_user($admin->ID);
}
kses_remove_filters();

$log = WP_CONTENT_DIR . '/uploads/thanh-lan-media.log';
$write = function ($message) use ($log) {
    file_put_contents($log, $message . "\n", FILE_APPEND);
    fwrite(STDOUT, $message . "\n");
};
$write('start ' . gmdate('c'));

$catalog = array(
    'hero.jpg' => array('Mẹ và bé tại nhà', 'Ảnh minh họa mẹ và bé tại nhà', 'trang-chu'),
    'tam-be.jpg' => array('Tắm bé sơ sinh', 'Ảnh minh họa tắm bé sơ sinh', 'tam-be'),
    'me-be.jpg' => array('Chăm sóc mẹ và bé', 'Ảnh minh họa chăm sóc mẹ và bé', 'cham-soc-me-be'),
    'bau.jpg' => array('Chăm sóc bầu tại nhà', 'Ảnh minh họa chăm sóc bầu tại nhà', 'cham-soc-bau'),
    'sua.jpg' => array('Thông tắc tia sữa', 'Ảnh minh họa thông tắc tia sữa', 'thong-tac-tia-sua'),
    'eo.jpg' => array('Giảm eo sau sinh', 'Ảnh minh họa giảm eo sau sinh', 'giam-eo'),
    'o-cu.jpg' => array('Ở cữ tại nhà', 'Ảnh minh họa ở cữ tại nhà', 'o-cu-tai-nha'),
    'sp-bau.jpg' => array('Sản phẩm chăm sóc bầu', 'Ảnh minh họa dầu massage bầu', ''),
    'sp-be.jpg' => array('Sản phẩm chăm sóc bé', 'Ảnh minh họa dầu tắm bé', ''),
    'sp-me.jpg' => array('Sản phẩm chăm sóc mẹ', 'Ảnh minh họa muối thảo dược mẹ', ''),
    'sp-toc.jpg' => array('Sản phẩm chăm sóc tóc', 'Ảnh minh họa dầu dưỡng tóc sau sinh', ''),
    'kien-thuc.jpg' => array('Bài viết kiến thức', 'Ảnh minh họa bài viết kiến thức', 'kien-thuc'),
    'logo-thanh-lan.jpg' => array('Logo Thanh Lan Spa', 'Logo Thanh Lan Spa', ''),
);

$logo_src = get_template_directory() . '/assets/logo-thanh-lan.jpg';
$logo_dest = WP_CONTENT_DIR . '/uploads/thanh-lan/logo-thanh-lan.jpg';
if (!file_exists($logo_dest) && file_exists($logo_src)) {
    copy($logo_src, $logo_dest);
}

function thanh_lan_attachment($file, $title, $alt, $parent_id) {
    $path = WP_CONTENT_DIR . '/uploads/thanh-lan/' . $file;
    if (!file_exists($path)) {
        return 0;
    }
    global $wpdb;
    $id = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value = %s LIMIT 1",
        'thanh-lan/' . $file
    ));
    if ($id) {
        // keep the exact file already in the library
    } else {
        $filetype = wp_check_filetype($path);
        $id = wp_insert_attachment(array(
            'post_mime_type' => $filetype['type'],
            'post_title' => $title,
            'post_excerpt' => $alt,
            'post_status' => 'inherit',
            'post_parent' => $parent_id,
        ), $path, $parent_id);
        if (is_wp_error($id) || !$id) {
            return 0;
        }
        $id = (int) $id;
        wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $path));
    }
    wp_update_post(array(
        'ID' => $id,
        'post_title' => $title,
        'post_excerpt' => $alt,
        'post_parent' => $parent_id,
    ));
    update_post_meta($id, '_wp_attachment_image_alt', $alt);
    return (int) $id;
}

$ids = array();
foreach ($catalog as $file => $meta) {
    $parent = 0;
    if ($meta[2] !== '') {
        $page = get_page_by_path($meta[2]);
        if ($page) {
            $parent = (int) $page->ID;
        }
    }
    $ids[$file] = thanh_lan_attachment($file, $meta[0], $meta[1], $parent);
    $write('media ' . $file . ' ' . $ids[$file]);
}

$featured = array(
    'trang-chu' => 'hero.jpg',
    'gioi-thieu' => 'hero.jpg',
    'dich-vu' => 'me-be.jpg',
    'tam-be' => 'tam-be.jpg',
    'cham-soc-me-be' => 'me-be.jpg',
    'o-cu-tai-nha' => 'o-cu.jpg',
    'cham-soc-bau' => 'bau.jpg',
    'thong-tac-tia-sua' => 'sua.jpg',
    'giam-eo' => 'eo.jpg',
    'feedback' => 'me-be.jpg',
    'kien-thuc' => 'kien-thuc.jpg',
    'tu-van' => 'hero.jpg',
);
foreach ($featured as $slug => $file) {
    $page = get_page_by_path($slug);
    if ($page && !empty($ids[$file])) {
        set_post_thumbnail($page->ID, $ids[$file]);
        $write('thumb page ' . $slug);
    }
}

$products = array(
    'Dầu massage bầu' => 'sp-bau.jpg',
    'Dầu tắm bé' => 'sp-be.jpg',
    'Muối thảo dược mẹ' => 'sp-me.jpg',
    'Dầu dưỡng tóc sau sinh' => 'sp-toc.jpg',
);
foreach ($products as $name => $file) {
    $product_post = get_page_by_title($name, OBJECT, 'product');
    if (!$product_post || empty($ids[$file]) || !function_exists('wc_get_product')) {
        continue;
    }
    $product = wc_get_product($product_post->ID);
    if ($product) {
        $product->set_image_id($ids[$file]);
        $product->save();
        $write('product image ' . $name);
    }
}

$categories = array(
    'huong-dan-cham-soc-be' => 'Hướng dẫn chăm sóc bé',
    'huong-dan-me' => 'Hướng dẫn mẹ',
    'me-bau-can-biet' => 'Mẹ bầu cần biết',
    'me-sau-sinh-can-biet' => 'Mẹ sau sinh cần biết',
    'dau-an-thanh-lan' => 'Dấu ấn Thanh Lan Spa',
);
$cat_ids = array();
foreach ($categories as $slug => $name) {
    $existing = term_exists($slug, 'category');
    if ($existing) {
        $cat_ids[$slug] = (int) (is_array($existing) ? $existing['term_id'] : $existing);
    } else {
        $created = wp_insert_term($name, 'category', array('slug' => $slug));
        $cat_ids[$slug] = is_wp_error($created) ? 0 : (int) $created['term_id'];
    }
}

$articles = array(
    array(
        'slug' => 'massage-bung-va-giac-ngu',
        'title' => 'Massage bụng và giấc ngủ những tuần đầu',
        'cat' => 'huong-dan-cham-soc-be',
        'html' => '<p>Tay mẹ ấm. Xoa bụng bé theo vòng tròn nhẹ để đỡ đầy hơi, rồi vỗ ợ.</p><p>Bú, ợ, thay bỉm, rồi mới dỗ ngủ. Giữ chuỗi này giúp bé phân biệt lúc chơi và lúc nghỉ.</p><p>Nếu bé bú kém, sốt, thở nhanh hoặc rốn có mùi, mẹ đưa bé đi khám.</p><p class="caption">Bài minh họa. Nhân viên sửa bài này hoặc đăng bài mới trong cùng chuyên mục.</p>',
    ),
    array(
        'slug' => 'viec-me-lam-trong-tuan-dau',
        'title' => 'Việc mẹ làm trong tuần đầu ở nhà',
        'cat' => 'huong-dan-me',
        'html' => '<p>Nghỉ khi bé ngủ. Nhờ người nhà nấu và dọn để mẹ chỉ tập trung bú và nghỉ.</p><p>Tắm bằng nước ấm trong phòng kín gió, lau khô tóc ngay.</p><p>Ghi lại câu hỏi cho buổi chăm kế tiếp của Thanh Lan Spa.</p><p class="caption">Bài minh họa. Nhân viên sửa bài này hoặc đăng bài mới trong cùng chuyên mục.</p>',
    ),
    array(
        'slug' => 'me-bau-nghi-the-nao',
        'title' => 'Mẹ bầu nghỉ thế nào những tuần cuối',
        'cat' => 'me-bau-can-biet',
        'html' => '<p>Nằm nghiêng, kê gối giữa hai gối và một gối mỏng sau lưng. Tránh nằm ngửa lâu nếu chóng mặt.</p><p>Đi lại trong nhà, dừng khi có cơn gò. Massage chỉ ở thế ngồi hoặc nằm nghiêng, không đè bụng.</p><p>Ra nước ối, ra máu, thai máy ít hẳn hoặc đau đầu kèm mắt mờ thì mẹ đi khám. Buổi spa không thay lần khám đó.</p><p class="caption">Bài minh họa. Nhân viên sửa bài này hoặc đăng bài mới trong cùng chuyên mục.</p>',
    ),
    array(
        'slug' => 'o-cu-va-tac-tia-sua',
        'title' => 'Ở cữ và việc làm khi tắc tia sữa',
        'cat' => 'me-sau-sinh-can-biet',
        'html' => '<p>Ở cữ nên giữ người sạch, ăn món mẹ tiêu hóa được và đi lại nhẹ trong nhà.</p><p>Tắc sớm thường là một vùng ngực cứng, da nóng, sữa một bên kém hơn. Mẹ không nặn mạnh đến bầm.</p><p>Chườm ấm vài phút rồi cho bú hoặc hút. Sốt cao hoặc vùng đỏ lan nhanh thì cần người đã học về thông tắc xem trực tiếp.</p><p class="caption">Bài minh họa. Nhân viên sửa bài này hoặc đăng bài mới trong cùng chuyên mục.</p>',
    ),
    array(
        'slug' => 'dau-an-thanh-lan-spa',
        'title' => 'Dấu ấn Thanh Lan Spa',
        'cat' => 'dau-an-thanh-lan',
        'html' => '<p>Bốn chặng mẹ đi cùng Tô Lan, từ tháng cuối thai kỳ đến ngày ở cữ ổn định.</p><p><strong>1. Lắng nghe mẹ.</strong> Mẹ kể đang bầu, sắp sinh, tuần đầu sau sinh hoặc đang tắc sữa. Thanh Lan Spa gợi ý buổi phù hợp.</p><p><strong>2. Chuyên viên tới nhà.</strong> Đúng giờ, dụng cụ sạch, làm việc trong không gian riêng của gia đình.</p><p><strong>3. Chăm đúng giai đoạn.</strong> Massage mẹ bầu, tắm bé, xông hoặc thông tắc tia sữa. Mẹ được hướng dẫn việc làm lại được tại nhà.</p><p><strong>4. Ở lại bằng bài viết.</strong> Sau buổi chăm, mẹ đọc tiếp ở mục Kiến thức và nhắn Zalo khi cần.</p>',
    ),
);

$image_id = !empty($ids['kien-thuc.jpg']) ? $ids['kien-thuc.jpg'] : 0;
foreach ($articles as $article) {
    $existing = get_page_by_path($article['slug'], OBJECT, 'post');
    $data = array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'post_title' => $article['title'],
        'post_name' => $article['slug'],
        'post_content' => $article['html'],
        'post_category' => array($cat_ids[$article['cat']]),
    );
    if ($existing) {
        $data['ID'] = $existing->ID;
        $post_id = wp_update_post($data);
    } else {
        $post_id = wp_insert_post($data);
    }
    if ($post_id && $image_id) {
        set_post_thumbnail($post_id, $image_id);
    }
    $write('post ' . $article['slug'] . ' ' . $post_id);
}

$knowledge = get_page_by_path('kien-thuc');
if ($knowledge) {
    $html = '<main><section class="section"><div class="wrap"><p class="crumbs"><a href="' . esc_url(home_url('/')) . '">Trang chủ</a> / Kiến thức</p><h2>Kiến thức</h2><p class="intro">Nhân viên đăng bài mới trong quản trị, chọn một trong bốn chuyên mục. Bài vừa đăng hiện ở đây.</p>[thanh_lan_bai_viet]</div></section></main>';
    wp_update_post(array('ID' => $knowledge->ID, 'post_content' => $html));
    $write('kien-thuc updated');
}

$front = get_page_by_path('trang-chu');
if ($front) {
    $content = $front->post_content;
    $content = str_replace('từ 2019', 'từ 2018', $content);
    $replacements = array(
        home_url('/me-bau/') => home_url('/me-bau-nghi-the-nao/'),
        home_url('/me-sau-sinh/') => home_url('/o-cu-va-tac-tia-sua/'),
        home_url('/cham-soc-be/') => home_url('/massage-bung-va-giac-ngu/'),
    );
    $content = str_replace(array_keys($replacements), array_values($replacements), $content);
    wp_update_post(array('ID' => $front->ID, 'post_content' => $content));
    $write('front links updated');
}

$about = get_page_by_path('gioi-thieu');
if ($about && strpos($about->post_content, 'MẸ &amp; BÉ THANH LAN') === false && strpos($about->post_content, 'MẸ & BÉ THANH LAN') === false) {
    $extra = 'Thành lập 05/08/2018. Tên giao dịch: MẸ &amp; BÉ THANH LAN. Tên quốc tế: THANH LAN MOM BABY CARE.<br />';
    $content = str_replace('MST 0319180928', $extra . 'MST 0319180928', $about->post_content);
    wp_update_post(array('ID' => $about->ID, 'post_content' => $content));
    $write('about updated');
}

flush_rewrite_rules();
update_option('thanh_lan_media_done', gmdate('c'));
$write('done');
@unlink(__FILE__);
