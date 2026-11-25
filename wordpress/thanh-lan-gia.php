<?php
/**
 * Cập nhật bảng giá liệu trình về nhà và gỡ ô hotline ở đầu trang chủ.
 * Chỉ chạy bằng PHP dòng lệnh, rồi tự xóa.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require '/home/giqisvhphosting/public_html/wp-load.php';
kses_remove_filters();

$log = WP_CONTENT_DIR . '/uploads/thanh-lan-gia.log';
$write = static function ($message) use ($log) {
    file_put_contents($log, $message . "\n", FILE_APPEND);
    fwrite(STDOUT, $message . "\n");
};

function tl_src($file) {
    $web = WP_CONTENT_DIR . '/uploads/2026/11/web/' . $file;
    $rel = file_exists($web) ? 'uploads/2026/11/web/' . $file : 'uploads/2026/11/' . $file;
    return esc_url(content_url($rel));
}

function tl_img($file, $alt, $class = '') {
    $class_attr = $class !== '' ? ' class="' . esc_attr($class) . '"' : '';
    return '<img src="' . tl_src($file) . '" alt="' . esc_attr($alt) . '"' . $class_attr . ' />';
}

$home = home_url('/');
$u = static function ($path) use ($home) {
    return esc_url(trailingslashit($home) . ltrim($path, '/'));
};

$packages = array(
    array('cham-me-tai-nha-1.jpg', 'Chăm sóc mẹ tại nhà', 'Mẹ 10 ngày', 'Chăm sóc phục hồi, 90 phút mỗi buổi.', '4.500.000đ', $u('cham-soc-me-be/')),
    array('cham-me-tai-nha-3.jpg', 'Lều xông cho mẹ', 'Mẹ 20 ngày', 'Chăm sóc chuyên sâu, 90 phút mỗi buổi.', '8.700.000đ', $u('cham-soc-me-be/')),
    array('cham-me-tai-nha-1.jpg', 'Massage cho mẹ', 'Mẹ 30 ngày', 'Chăm sóc toàn diện, 90 phút mỗi buổi.', '11.250.000đ', $u('cham-soc-me-be/')),
    array('tam-cho-be.jpg', 'Tắm cho bé', 'Bé 15 ngày', 'Chăm sóc phục hồi, 45 phút mỗi buổi.', '2.550.000đ', $u('tam-be/')),
    array('matxa-cho-be.jpg', 'Massage cho bé', 'Bé 30 ngày', 'Chăm sóc chuyên sâu, 45 phút mỗi buổi.', '4.500.000đ', $u('tam-be/')),
    array('cham-me-tai-nha-2.jpg', 'Mẹ và bé ở nhà', 'Combo 20 ngày', 'Chăm sóc cơ bản cho mẹ và bé.', '6.900.000đ', $u('o-cu-tai-nha/')),
    array('day-thang-be-2.jpg', 'Gia đình và kỹ thuật viên', 'Combo 30 ngày', 'Chăm sóc cơ bản cho mẹ và bé.', '9.900.000đ', $u('o-cu-tai-nha/')),
);

$cards = '';
foreach ($packages as $item) {
    $cards .= '<article class="card">' . tl_img($item[0], $item[1])
        . '<div class="pad"><h3>' . esc_html($item[2]) . '</h3><p>' . esc_html($item[3]) . '</p><div class="price">' . esc_html($item[4]) . '</div><a class="more" href="' . $item[5] . '">Xem gói này</a></div></article>';
}

$price_section = '<section class="section"><div class="wrap"><h2>Bảng giá</h2><p class="intro">Liệu trình về nhà của Thanh Lan Spa. Mẹ chọn gói để xem những việc được làm trong buổi.</p><div class="grid services">' . $cards . '</div></div></section>';

$front_id = (int) get_option('page_on_front');
$front = get_post($front_id);
$html = $front->post_content;
$html = preg_replace('/<article><strong>0975\.399\.319<\/strong><span>Hotline Ms Tô Lan<\/span><\/article>/', '', $html);
$html = preg_replace('/<section class="section"><div class="wrap"><h2>Dịch Vụ<\/h2>.*?(?=<section class="section" style="background:#fff">)/s', $price_section, $html, 1, $count);
if (!$count) {
    $write('front section not found');
} else {
    wp_update_post(array('ID' => $front_id, 'post_content' => $html));
    $write('front ' . $front_id);
}

$me_list_10 = '<ul class="clean"><li>Gội đầu thảo dược 2 lần</li><li>Chăm sóc da mặt 2 lần</li><li>Giảm bọng mắt 5 lần</li><li>Massage ngực, thông tuyến sữa theo tình trạng</li><li>Massage body 2 lần</li><li>Làm sáng nách 5 lần</li><li>Định hình săn chắc bụng 5 lần</li><li>Tẩy tế bào chết body 2 lần</li><li>Ủ trắng body 2 lần</li><li>Thoa rượu gừng, nghệ 7 lần</li><li>Xông thảo dược toàn thân 2 lần</li><li>Xông vùng kín 4 lần</li><li>Ngâm chân muối thảo dược 4 lần</li><li>Ủ ấm bằng chăn điện 7 lần</li><li>Tặng 1 chai cốt nhàu, không quy đổi</li></ul>';
$me_list_20 = '<ul class="clean"><li>Gội đầu thảo dược 4 lần</li><li>Chăm sóc da mặt 4 lần</li><li>Giảm bọng mắt 10 lần</li><li>Massage ngực, thông tuyến sữa theo tình trạng</li><li>Massage body 5 lần</li><li>Làm sáng nách 10 lần</li><li>Định hình bằng máy EMS 10 lần</li><li>Tẩy tế bào chết body 4 lần</li><li>Ủ trắng body 4 lần</li><li>Thoa rượu gừng, nghệ 10 lần</li><li>Xông thảo dược toàn thân 4 lần</li><li>Xông vùng kín 15 lần</li><li>Ngâm chân muối thảo dược 10 lần</li><li>Ủ ấm bằng chăn điện 10 lần</li><li>Tặng 2 chai cốt nhàu, không quy đổi</li></ul>';
$me_list_30 = '<ul class="clean"><li>Gội đầu thảo dược 5 lần</li><li>Chăm sóc da mặt 6 lần</li><li>Giảm bọng mắt 15 lần</li><li>Massage ngực, thông tuyến sữa theo tình trạng</li><li>Massage body 6 lần</li><li>Làm sáng nách 15 lần</li><li>Định hình bằng máy EMS 15 lần</li><li>Tẩy tế bào chết body 5 lần</li><li>Ủ trắng body 5 lần</li><li>Thoa rượu gừng, nghệ 20 lần</li><li>Xông thảo dược toàn thân 5 lần</li><li>Xông vùng kín 20 lần</li><li>Ngâm chân muối thảo dược 15 lần</li><li>Ủ ấm bằng chăn điện 20 lần</li><li>Tặng 3 chai cốt nhàu, không quy đổi</li></ul>';

$pages = array(
    'dich-vu' => '<section class="section"><div class="wrap"><p class="crumbs"><a href="' . $u('') . '">Trang chủ</a> / Dịch Vụ</p><h2>Bảng giá liệu trình về nhà</h2><p class="intro">Giá theo bảng liệu trình Thanh Lan Spa. Mỗi gói mở trang chi tiết.</p><div class="grid services">' . $cards . '</div></div></section>',
    'tam-be' => '<section class="page-hero"><div class="wrap split"><div><p class="crumbs"><a href="' . $u('') . '">Trang chủ</a> / <a href="' . $u('dich-vu/') . '">Dịch Vụ</a> / Liệu trình cho bé</p>'
        . '<p class="kicker">Dịch Vụ</p><h1>Liệu trình cho bé</h1><p class="lead">Mỗi buổi 45 phút, kỹ thuật viên đến nhà. Gói tính theo số ngày, không tính lẻ từng buổi.</p>'
        . '<p class="price">15 ngày: 2.550.000đ</p><p class="price">30 ngày: 4.500.000đ</p>'
        . '<p>Trong buổi có massage vận động và kích thích hệ tiêu hóa, tắm bé và vệ sinh, hơ lá trầu giữ ấm, kích hoạt đa giác quan, hỗ trợ khớp ngậm và hướng dẫn vỗ ợ hơi.</p>'
        . '<button class="btn" type="button" data-book="Liệu trình bé 15 ngày (2.550.000đ) hoặc 30 ngày (4.500.000đ)">Đặt lịch gói này</button></div>'
        . '<figure class="panel">' . tl_img('tam-cho-be.jpg', 'Tắm cho bé', 'cover') . '</figure></div>'
        . '<div class="wrap shots">' . '<figure>' . tl_img('matxa-cho-be.jpg', 'Massage cho bé') . '<figcaption class="caption">Massage trong buổi chăm bé.</figcaption></figure>'
        . '<figure>' . tl_img('ho-la-trau-2.jpg', 'Hơ lá trầu') . '<figcaption class="caption">Hơ lá trầu giữ ấm.</figcaption></figure>'
        . '<figure>' . tl_img('tam-be.jpg', 'Bế bé sau buổi chăm') . '<figcaption class="caption">Giữ ấm sau buổi tắm.</figcaption></figure></div></section>',
    'cham-soc-me-be' => '<section class="page-hero"><div class="wrap"><p class="crumbs"><a href="' . $u('') . '">Trang chủ</a> / <a href="' . $u('dich-vu/') . '">Dịch Vụ</a> / Liệu trình cho mẹ</p>'
        . '<p class="kicker">Dịch Vụ</p><h1>Liệu trình cho mẹ</h1><p class="lead">Mỗi buổi 90 phút tại nhà. Mẹ chọn 10, 20 hoặc 30 ngày.</p>'
        . '<div class="grid three" style="margin-top:16px">'
        . '<article class="panel"><h3>10 ngày</h3><p class="price">4.500.000đ</p><p>Chăm sóc phục hồi.</p>' . $me_list_10 . '</article>'
        . '<article class="panel"><h3>20 ngày</h3><p class="price">8.700.000đ</p><p>Chăm sóc chuyên sâu. Gói có 10 lần máy EMS và nằm máy rung giảm eo.</p>' . $me_list_20 . '</article>'
        . '<article class="panel"><h3>30 ngày</h3><p class="price">11.250.000đ</p><p>Chăm sóc toàn diện. Gói có 15 lần máy EMS và nằm máy rung giảm eo.</p>' . $me_list_30 . '</article>'
        . '</div><p style="margin-top:16px"><button class="btn" type="button" data-book="Liệu trình mẹ 10, 20 hoặc 30 ngày">Đặt lịch gói này</button></p>'
        . '<figure class="panel" style="margin-top:16px">' . tl_img('cham-me-tai-nha-1.jpg', 'Chăm sóc mẹ tại nhà', 'cover') . '</figure></div></section>',
    'o-cu-tai-nha' => '<section class="page-hero"><div class="wrap"><p class="crumbs"><a href="' . $u('') . '">Trang chủ</a> / <a href="' . $u('dich-vu/') . '">Dịch Vụ</a> / Combo mẹ và bé</p>'
        . '<p class="kicker">Dịch Vụ</p><h1>Combo mẹ và bé</h1><p class="lead">Mẹ 90 phút, bé 45 phút, cùng một lịch đến nhà.</p>'
        . '<div class="grid three"><article class="panel"><h3>Combo 20 ngày</h3><p class="price">6.900.000đ</p><p>Chăm sóc cơ bản.</p><ul class="clean"><li>Gội đầu thảo dược 3 lần</li><li>Đắp mặt nạ 6 lần</li><li>Massage vai gáy 3 lần</li><li>Quấn muối nóng bụng 17 lần</li><li>Tẩy tế bào chết body 2 lần</li><li>Thoa rượu nghệ 16 lần</li><li>Thoa rượu gừng 16 lần</li><li>Xông thảo dược toàn thân 2 lần</li><li>Xông vùng kín 15 lần</li><li>Ngâm chân muối thảo dược 10 lần</li></ul><p>Bé được massage, tắm, hơ lá trầu, hỗ trợ ti và vỗ ợ.</p></article>'
        . '<article class="panel"><h3>Combo 30 ngày</h3><p class="price">9.900.000đ</p><p>Chăm sóc cơ bản.</p><ul class="clean"><li>Gội đầu thảo dược 5 lần</li><li>Đắp mặt nạ 10 lần</li><li>Massage vai gáy 5 lần</li><li>Quấn muối nóng bụng 25 lần</li><li>Tẩy tế bào chết body 5 lần</li><li>Thoa rượu nghệ 25 lần</li><li>Thoa rượu gừng 25 lần</li><li>Xông thảo dược toàn thân 5 lần</li><li>Xông vùng kín 25 lần</li><li>Ngâm chân muối thảo dược 25 lần</li></ul><p>Bé được massage, tắm, hơ lá trầu, hỗ trợ ti và vỗ ợ.</p></article></div>'
        . '<p style="margin-top:16px"><button class="btn" type="button" data-book="Combo mẹ và bé 20 ngày (6.900.000đ) hoặc 30 ngày (9.900.000đ)">Đặt lịch gói này</button></p></div></section>',
    'thong-tac-tia-sua' => '<section class="page-hero"><div class="wrap split"><div><p class="crumbs"><a href="' . $u('') . '">Trang chủ</a> / <a href="' . $u('dich-vu/') . '">Dịch Vụ</a> / Thông tắc tia sữa</p>'
        . '<p class="kicker">Dịch Vụ</p><h1>Thông tắc tia sữa</h1><p class="lead">Massage ngực và thông tuyến sữa được làm trong liệu trình mẹ, theo tình trạng của mẹ.</p><p class="price">Nằm trong gói mẹ</p>'
        . '<p>Bảng giá không tách một mức riêng cho từng buổi thông tắc. Mẹ đang dùng gói 10, 20 hoặc 30 ngày thì kỹ thuật viên xem bên nào cứng và làm trong buổi 90 phút.</p>'
        . '<p>Sốt, vết đỏ lan hoặc ngực vẫn cứng sau nhiều giờ thì mẹ cần gặp bác sĩ. Buổi chăm không thay lần khám đó.</p>'
        . '<p><a class="btn" href="' . $u('cham-soc-me-be/') . '">Xem liệu trình mẹ</a></p></div>'
        . '<figure class="panel">' . tl_img('cham-me-tai-nha-1.jpg', 'Chăm sóc mẹ tại nhà', 'cover') . '</figure></div></section>',
    'giam-eo' => '<section class="page-hero"><div class="wrap split"><div><p class="crumbs"><a href="' . $u('') . '">Trang chủ</a> / <a href="' . $u('dich-vu/') . '">Dịch Vụ</a> / Giảm eo sau sinh</p>'
        . '<p class="kicker">Dịch Vụ</p><h1>Giảm eo sau sinh</h1><p class="lead">Định hình eo nằm trong liệu trình mẹ 20 ngày và 30 ngày, không bán lẻ một buổi.</p>'
        . '<p class="price">20 ngày: 8.700.000đ</p><p class="price">30 ngày: 11.250.000đ</p>'
        . '<p>Gói 20 ngày có 10 lần máy EMS. Gói 30 ngày có 15 lần. Bảng giá ghi kèm nằm máy rung giảm eo trong hai gói này.</p>'
        . '<p><a class="btn" href="' . $u('cham-soc-me-be/') . '">Xem liệu trình mẹ</a></p></div>'
        . '<figure class="panel">' . tl_img('cham-me-tai-nha-3.jpg', 'Lều xông sau sinh', 'cover') . '</figure></div></section>',
);

foreach ($pages as $slug => $content) {
    $page = get_page_by_path($slug);
    if (!$page) {
        $write('missing ' . $slug);
        continue;
    }
    wp_update_post(array('ID' => $page->ID, 'post_content' => $content));
    $write('page ' . $slug . ' ' . $page->ID);
}

$replacements = array(
    'Giá đang ghi là 250.000đ một buổi.' => 'Liệu trình bé 15 ngày là 2.550.000đ, 30 ngày là 4.500.000đ.',
    'Gói chăm mẹ bé trên website là 4.500.000đ cho 10 buổi. Ở cữ theo ngày thì báo giá khi mẹ nhắn số ngày cần người đến.' => 'Gói mẹ 10 ngày là 4.500.000đ, 20 ngày là 8.700.000đ, 30 ngày là 11.250.000đ. Combo mẹ và bé 20 ngày là 6.900.000đ, 30 ngày là 9.900.000đ.',
    'giá đang ghi 450.000đ một buổi, và không nặn đến bầm.' => 'việc này nằm trong liệu trình mẹ và làm theo tình trạng, không tách giá từng buổi.',
);
$posts = get_posts(array('post_type' => 'post', 'posts_per_page' => 20, 'post_status' => 'publish'));
foreach ($posts as $post) {
    $next = str_replace(array_keys($replacements), array_values($replacements), $post->post_content);
    if ($next !== $post->post_content) {
        wp_update_post(array('ID' => $post->ID, 'post_content' => $next));
        $write('post ' . $post->ID);
    }
}

do_action('litespeed_purge_all');
$write('done');
@unlink(__FILE__);
