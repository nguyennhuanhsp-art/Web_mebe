<?php
/**
 * Plugin Name: Thanh Lan Home Layout
 * Description: Dựng trang chủ theo bố cục ảnh giới thiệu, dịch vụ bé, dịch vụ mẹ, bài viết và sản phẩm nổi bật. Chạy một lần rồi tự dừng.
 */
if (!defined('ABSPATH')) {
    exit;
}

add_action('init', 'thanh_lan_home_layout_run', 30);

function tl_src($file) {
    $web = WP_CONTENT_DIR . '/uploads/2026/11/web/' . $file;
    $rel = file_exists($web) ? 'uploads/2026/11/web/' . $file : 'uploads/2026/11/' . $file;
    return esc_url('https://www.tolan.vn/wp-content/' . $rel);
}

function tl_img($file, $alt, $class = '') {
    $class_attr = $class !== '' ? ' class="' . esc_attr($class) . '"' : '';
    return '<img src="' . tl_src($file) . '" alt="' . esc_attr($alt) . '"' . $class_attr . ' />';
}

$home = 'https://www.tolan.vn/';
$u = static function ($path) {
    return esc_url('https://www.tolan.vn/' . ltrim($path, '/'));
};

function tl_card($file, $alt, $title, $text, $url, $label) {
    return '<article class="card">' . tl_img($file, $alt)
        . '<div class="pad"><h3>' . esc_html($title) . '</h3><p>' . esc_html($text) . '</p>'
        . '<a class="more" href="' . esc_url($url) . '">' . esc_html($label) . '</a></div></article>';
}

function thanh_lan_home_layout_run() {
    if (get_option('thanh_lan_home_layout_v') === '2') {
        return;
    }
    if (get_transient('thanh_lan_home_layout_lock')) {
        return;
    }
    set_transient('thanh_lan_home_layout_lock', '1', 3 * MINUTE_IN_SECONDS);
    kses_remove_filters();
    $log = WP_CONTENT_DIR . '/uploads/thanh-lan-home.log';
    $write = static function ($message) use ($log) {
        file_put_contents($log, $message . "\n", FILE_APPEND);
    };

    $front_id = (int) get_option('page_on_front');
    $front = get_post($front_id);
    $hero = '';
    if ($front && preg_match('/<section class="hero">.*?<\/section>/s', $front->post_content, $match)) {
        $hero = $match[0];
    }
    if ($hero === '') {
        $slides = '';
        for ($n = 1; $n <= 6; $n++) {
            $slides .= tl_img('doi-ngu-' . $n . '.jpg', 'Đội Ngũ Thanh Lan Spa');
        }
        $hero = '<section class="hero"><div class="wrap hero-grid"><div><p class="kicker">Thanh Lan Spa</p><h1>Chăm mẹ và bé trước và sau sinh</h1>'
            . '<p class="lead">Tô Thị Lan và đội ngũ đồng hành cùng mẹ bầu, mẹ sau sinh và bé. Cơ sở có mặt tại 4 chi nhánh Bệnh viện Đa khoa Xuyên Á.</p>'
            . '<div class="actions"><a class="btn" href="' . $u('dich-vu/') . '">Xem Dịch Vụ</a><a class="btn light" href="' . $u('tu-van/') . '">Nhận tư vấn</a></div>'
            . '<div class="facts"><article><strong>Gần 10 năm</strong><span>Kinh nghiệm mẹ và bé, từ 2018</span></article><article><strong>20.000+</strong><span>Em bé và bà mẹ đã đồng hành</span></article><article><strong>4 chi nhánh</strong><span>Trong bệnh viện Xuyên Á</span></article></div></div>'
            . '<figure class="frame"><div class="hero-slides">' . $slides . '</div><figcaption class="caption">Đội Ngũ Thanh Lan Spa</figcaption></figure></div></section>';
    }

    $intro = '<section class="section"><div class="wrap"><h2>Thanh Lan đồng hành</h2><p class="intro">Hình ảnh đội ngũ và những buổi chăm tại nhà. <a href="' . $u('gioi-thieu/') . '">Đọc câu chuyện chị Tô Lan</a>.</p><div class="shots">'
        . '<figure>' . tl_img('doi-ngu-3.jpg', 'Đội Ngũ Thanh Lan Spa') . '<figcaption class="caption">Đội Ngũ Thanh Lan Spa.</figcaption></figure>'
        . '<figure>' . tl_img('day-thang-be-2.jpg', 'Kỹ thuật viên và gia đình') . '<figcaption class="caption">Có mặt cùng gia đình trong lễ đầy tháng.</figcaption></figure>'
        . '<figure>' . tl_img('tam-be.jpg', 'Kỹ thuật viên bế bé') . '<figcaption class="caption">Sau buổi tắm và massage cho bé.</figcaption></figure>'
        . '</div></div></section>';

    $baby_cat = get_term_by('slug', 'huong-dan-cham-soc-be', 'category');
    $baby_cat_url = ($baby_cat && !is_wp_error($baby_cat)) ? get_term_link($baby_cat) : $u('kien-thuc/');
    if (is_wp_error($baby_cat_url)) {
        $baby_cat_url = $u('kien-thuc/');
    }
    $baby_cat_url = str_replace('http://www.tolan.vn', 'https://www.tolan.vn', $baby_cat_url);
    $baby = '<section class="section" style="background:#fff"><div class="wrap"><h2>Dịch vụ cho bé</h2><p class="intro">Chọn một thẻ để đọc nội dung buổi chăm. Bảng giá nằm ở cuối trang dịch vụ.</p><div class="grid services">'
        . tl_card('tam-cho-be.jpg', 'Tắm cho bé', 'Tắm và massage bé', 'Massage, tắm, vệ sinh và hơ lá trầu. Mỗi buổi 45 phút tại nhà.', $u('tam-be/'), 'Xem dịch vụ')
        . tl_card('ho-la-trau-2.jpg', 'Hơ lá trầu cho bé', 'Kinh nghiệm chăm bé', 'Cách tắm, massage và dùng lá trầu trong buổi chăm sơ sinh.', $u('tam-massage-va-la-trau-cho-be/'), 'Đọc bài')
        . tl_card('matxa-cho-be.jpg', 'Massage cho bé', 'Hướng dẫn chăm sóc bé', 'Mục bài viết để mẹ xem lại cách chăm bé tại nhà.', $baby_cat_url, 'Mở chuyên mục')
        . '</div></div></section>';

    $mother = '<section class="section"><div class="wrap"><h2>Dịch vụ cho mẹ</h2><p class="intro">Mỗi dịch vụ một trang. Giá của gói được ghi ở phần dưới trang đó.</p><div class="grid services">'
        . tl_card('cham-me-tai-nha-1.jpg', 'Chăm sóc mẹ tại nhà', 'Chăm sóc mẹ sau sinh', 'Liệu trình 10, 20 hoặc 30 ngày. Mỗi buổi 90 phút.', $u('cham-soc-me-be/'), 'Xem dịch vụ')
        . tl_card('cham-me-tai-nha-2.jpg', 'Mẹ và bé ở nhà', 'Combo mẹ và bé', 'Mẹ và bé được chăm trong cùng một lịch đến nhà.', $u('o-cu-tai-nha/'), 'Xem dịch vụ')
        . tl_card('doi-ngu-6.jpg', 'Đội ngũ Thanh Lan', 'Chăm sóc bầu tại nhà', 'Massage nhẹ và cách nghỉ những tuần cuối.', $u('cham-soc-bau/'), 'Xem dịch vụ')
        . tl_card('cham-me-tai-nha-1.jpg', 'Buổi chăm mẹ', 'Thông tắc tia sữa', 'Làm trong liệu trình mẹ, theo tình trạng.', $u('thong-tac-tia-sua/'), 'Xem dịch vụ')
        . tl_card('cham-me-tai-nha-3.jpg', 'Lều xông sau sinh', 'Giảm eo sau sinh', 'Nằm trong liệu trình mẹ 20 ngày và 30 ngày.', $u('giam-eo/'), 'Xem dịch vụ')
        . tl_card('day-thang.jpg', 'Lễ đầy tháng', 'Kinh nghiệm cho mẹ', 'Nhịp một ngày ở cữ và những việc mẹ cần biết sau sinh.', $u('nhip-ngay-me-o-cu/'), 'Đọc bài')
        . '</div></div></section>';

    $knowledge = '<section class="section" style="background:#fff"><div class="wrap"><h2>Kinh nghiệm chăm sóc</h2><p class="intro">Bài viết mẹ đọc thêm. Nhân viên đăng bài mới trong mục Kiến thức.</p><div class="grid three">'
        . tl_card('doi-ngu-4.jpg', 'Đội ngũ Thanh Lan Spa', 'Mẹ bầu cần biết', 'Cách nghỉ những tuần cuối. Buổi spa không thay lần khám.', $u('me-bau-nghi-nhung-tuan-cuoi/'), 'Đọc bài')
        . tl_card('day-thang-be-1.jpg', 'Bé trong lễ đầy tháng', 'Mẹ sau sinh cần biết', 'Ngày đầu sau sinh và mốc đầy tháng.', $u('ngay-dau-sau-sinh-va-day-thang/'), 'Đọc bài')
        . tl_card('doi-ngu-1.jpg', 'Lễ tốt nghiệp kỹ thuật viên', 'Dấu ấn Thanh Lan', 'Hành trình đội ngũ từ năm 2018.', $u('dau-an-doi-ngu-va-le-day-thang/'), 'Đọc bài')
        . '</div><p style="text-align:center;margin-top:18px"><a class="more" href="' . $u('kien-thuc/') . '">Xem tất cả bài viết</a></p></div></section>';

    $shop = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : $u('cua-hang/');
    $shop = str_replace('http://www.tolan.vn', 'https://www.tolan.vn', (string) $shop);
    $product_cards = '';
    if (function_exists('wc_get_product')) {
        $product_query = new WP_Query(array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => 4,
            'orderby' => 'date',
            'order' => 'DESC',
        ));
        foreach ($product_query->posts as $product_post) {
            $product = wc_get_product($product_post->ID);
            if (!$product) {
                continue;
            }
            $image = get_the_post_thumbnail_url($product_post->ID, 'medium');
            if (!$image) {
                $image = tl_src('doi-ngu-1.jpg');
            }
            $image = str_replace('http://www.tolan.vn', 'https://www.tolan.vn', $image);
            $link = str_replace('http://www.tolan.vn', 'https://www.tolan.vn', get_permalink($product_post));
            $amount = number_format((float) $product->get_price(), 0, ',', '.') . 'đ';
            $product_cards .= '<article class="card"><img src="' . esc_url($image) . '" alt="' . esc_attr($product->get_name()) . '" />'
                . '<div class="pad"><h3>' . esc_html($product->get_name()) . '</h3><p class="price">' . esc_html($amount) . '</p>'
                . '<a class="more" href="' . esc_url($link) . '">Xem sản phẩm</a></div></article>';
        }
        wp_reset_postdata();
    }
    $products = '<section class="section"><div class="wrap"><h2>Sản phẩm nổi bật</h2><p class="intro">Sản phẩm mẹ và bé đang có tại cửa hàng. Admin đăng, sửa và xóa trong quản trị.</p>'
        . '<div class="grid four">' . $product_cards . '</div>'
        . '<p style="text-align:center;margin-top:18px"><a class="btn" href="' . esc_url($shop) . '">Xem cửa hàng</a></p></div></section>';
    $write('products ' . substr_count($product_cards, '<article'));

    $front_html = str_replace('http://www.tolan.vn', 'https://www.tolan.vn', $hero . $intro . $baby . $mother . $knowledge . $products);
    wp_update_post(array('ID' => $front_id, 'post_content' => $front_html));
    $write('front ' . $front_id);

    $me10 = '<ul class="clean"><li>Gội đầu thảo dược 2 lần</li><li>Chăm sóc da mặt 2 lần</li><li>Giảm bọng mắt 5 lần</li><li>Massage ngực, thông tuyến sữa theo tình trạng</li><li>Massage body 2 lần</li><li>Làm sáng nách 5 lần</li><li>Định hình săn chắc bụng 5 lần</li><li>Tẩy tế bào chết body 2 lần</li><li>Ủ trắng body 2 lần</li><li>Thoa rượu gừng, nghệ 7 lần</li><li>Xông thảo dược toàn thân 2 lần</li><li>Xông vùng kín 4 lần</li><li>Ngâm chân muối thảo dược 4 lần</li><li>Ủ ấm bằng chăn điện 7 lần</li><li>Tặng 1 chai cốt nhàu, không quy đổi</li></ul>';
    $me20 = '<ul class="clean"><li>Gội đầu thảo dược 4 lần</li><li>Chăm sóc da mặt 4 lần</li><li>Giảm bọng mắt 10 lần</li><li>Massage ngực, thông tuyến sữa theo tình trạng</li><li>Massage body 5 lần</li><li>Làm sáng nách 10 lần</li><li>Định hình bằng máy EMS 10 lần</li><li>Tẩy tế bào chết body 4 lần</li><li>Ủ trắng body 4 lần</li><li>Thoa rượu gừng, nghệ 10 lần</li><li>Xông thảo dược toàn thân 4 lần</li><li>Xông vùng kín 15 lần</li><li>Ngâm chân muối thảo dược 10 lần</li><li>Ủ ấm bằng chăn điện 10 lần</li><li>Tặng 2 chai cốt nhàu, không quy đổi</li></ul>';
    $me30 = '<ul class="clean"><li>Gội đầu thảo dược 5 lần</li><li>Chăm sóc da mặt 6 lần</li><li>Giảm bọng mắt 15 lần</li><li>Massage ngực, thông tuyến sữa theo tình trạng</li><li>Massage body 6 lần</li><li>Làm sáng nách 15 lần</li><li>Định hình bằng máy EMS 15 lần</li><li>Tẩy tế bào chết body 5 lần</li><li>Ủ trắng body 5 lần</li><li>Thoa rượu gừng, nghệ 20 lần</li><li>Xông thảo dược toàn thân 5 lần</li><li>Xông vùng kín 20 lần</li><li>Ngâm chân muối thảo dược 15 lần</li><li>Ủ ấm bằng chăn điện 20 lần</li><li>Tặng 3 chai cốt nhàu, không quy đổi</li></ul>';

    $pages = array(
        'dich-vu' => '<section class="section"><div class="wrap"><p class="crumbs"><a href="' . $home . '">Trang chủ</a> / Dịch Vụ</p><h2>Dịch vụ</h2><p class="intro">Chọn dịch vụ để đọc nội dung. Bảng giá nằm ở cuối mỗi trang.</p>'
            . '<h3>Cho bé</h3><div class="grid services">'
            . tl_card('tam-cho-be.jpg', 'Tắm cho bé', 'Tắm và massage bé', 'Massage, tắm, vệ sinh và hơ lá trầu tại nhà.', $u('tam-be/'), 'Xem dịch vụ')
            . tl_card('ho-la-trau-2.jpg', 'Hơ lá trầu', 'Kinh nghiệm chăm bé', 'Bài viết về tắm, massage và lá trầu.', $u('tam-massage-va-la-trau-cho-be/'), 'Đọc bài')
            . '</div><h3 style="margin-top:28px">Cho mẹ</h3><div class="grid services">'
            . tl_card('cham-me-tai-nha-1.jpg', 'Chăm sóc mẹ', 'Chăm sóc mẹ sau sinh', 'Liệu trình 10, 20 hoặc 30 ngày.', $u('cham-soc-me-be/'), 'Xem dịch vụ')
            . tl_card('cham-me-tai-nha-2.jpg', 'Mẹ và bé', 'Combo mẹ và bé', 'Cùng một lịch đến nhà.', $u('o-cu-tai-nha/'), 'Xem dịch vụ')
            . tl_card('doi-ngu-6.jpg', 'Đội ngũ', 'Chăm sóc bầu tại nhà', 'Massage nhẹ những tuần cuối.', $u('cham-soc-bau/'), 'Xem dịch vụ')
            . tl_card('cham-me-tai-nha-1.jpg', 'Chăm mẹ', 'Thông tắc tia sữa', 'Theo tình trạng, trong liệu trình mẹ.', $u('thong-tac-tia-sua/'), 'Xem dịch vụ')
            . tl_card('cham-me-tai-nha-3.jpg', 'Xông', 'Giảm eo sau sinh', 'Trong gói mẹ 20 ngày và 30 ngày.', $u('giam-eo/'), 'Xem dịch vụ')
            . '</div></div></section>',
        'tam-be' => '<section class="page-hero"><div class="wrap split"><div><p class="crumbs"><a href="' . $home . '">Trang chủ</a> / <a href="' . $u('dich-vu/') . '">Dịch Vụ</a> / Tắm và massage bé</p>'
            . '<p class="kicker">Dịch vụ cho bé</p><h1>Tắm và massage bé</h1><p class="lead">Mỗi buổi 45 phút tại nhà. Bé được massage, tắm, vệ sinh và hơ lá trầu nếu mẹ muốn dùng.</p>'
            . '<p>Kỹ thuật viên xem da và rốn trước. Massage đi từ tay, chân, lưng rồi bụng. Tắm bằng nước ấm. Mắt, mũi, miệng và rốn được lau riêng, không ngâm rốn còn chưa rụng. Mẹ đứng cạnh để học cách đỡ đầu và quấn khăn.</p>'
            . '<ul class="clean"><li>Massage vận động và kích thích hệ tiêu hóa</li><li>Tắm bé và vệ sinh chuyên sâu</li><li>Hơ lá trầu giữ ấm</li><li>Kích hoạt đa giác quan</li><li>Hỗ trợ khớp ngậm và hướng dẫn vỗ ợ hơi</li></ul></div>'
            . '<figure class="panel">' . tl_img('tam-cho-be.jpg', 'Tắm cho bé', 'cover') . '</figure></div>'
            . '<div class="wrap shots"><figure>' . tl_img('matxa-cho-be.jpg', 'Massage cho bé') . '<figcaption class="caption">Massage trong buổi chăm.</figcaption></figure>'
            . '<figure>' . tl_img('ho-la-trau-2.jpg', 'Hơ lá trầu') . '<figcaption class="caption">Hơ lá trầu giữ ấm.</figcaption></figure>'
            . '<figure>' . tl_img('tam-be.jpg', 'Bế bé sau buổi') . '<figcaption class="caption">Giữ ấm sau tắm.</figcaption></figure></div>'
            . '<div class="wrap" style="margin-top:28px"><h2>Bảng giá</h2><p class="intro">Gói tính theo số ngày, không tính lẻ từng buổi.</p><div class="grid three">'
            . '<article class="panel"><h3>15 ngày</h3><p class="price">2.550.000đ</p><p>Chăm sóc phục hồi.</p></article>'
            . '<article class="panel"><h3>30 ngày</h3><p class="price">4.500.000đ</p><p>Chăm sóc chuyên sâu.</p></article></div>'
            . '<p style="margin-top:16px"><button class="btn" type="button" data-book="Liệu trình bé 15 ngày hoặc 30 ngày">Đặt lịch gói này</button></p></div></section>',
        'cham-soc-me-be' => '<section class="page-hero"><div class="wrap split"><div><p class="crumbs"><a href="' . $home . '">Trang chủ</a> / <a href="' . $u('dich-vu/') . '">Dịch Vụ</a> / Chăm sóc mẹ sau sinh</p>'
            . '<p class="kicker">Dịch vụ cho mẹ</p><h1>Chăm sóc mẹ sau sinh</h1><p class="lead">Kỹ thuật viên đến nhà, mỗi buổi 90 phút. Mẹ chọn 10, 20 hoặc 30 ngày.</p>'
            . '<p>Buổi đầu hỏi ngày sinh, sinh thường hay sinh mổ, và chỗ mẹ đang đau. Massage vai, lưng và chân. Bụng chỉ làm khi mẹ đã được cho phép, không đè lên vết mổ còn đau. Thông tuyến sữa làm theo tình trạng, trong cùng buổi.</p></div>'
            . '<figure class="panel">' . tl_img('cham-me-tai-nha-1.jpg', 'Chăm sóc mẹ tại nhà', 'cover') . '</figure></div>'
            . '<div class="wrap" style="margin-top:28px"><h2>Bảng giá</h2><div class="grid three" style="margin-top:16px">'
            . '<article class="panel"><h3>10 ngày</h3><p class="price">4.500.000đ</p><p>Chăm sóc phục hồi.</p>' . $me10 . '</article>'
            . '<article class="panel"><h3>20 ngày</h3><p class="price">8.700.000đ</p><p>Chăm sóc chuyên sâu, có máy EMS và nằm máy rung giảm eo.</p>' . $me20 . '</article>'
            . '<article class="panel"><h3>30 ngày</h3><p class="price">11.250.000đ</p><p>Chăm sóc toàn diện, có máy EMS và nằm máy rung giảm eo.</p>' . $me30 . '</article></div>'
            . '<p style="margin-top:16px"><button class="btn" type="button" data-book="Liệu trình mẹ 10, 20 hoặc 30 ngày">Đặt lịch gói này</button></p></div></section>',
        'o-cu-tai-nha' => '<section class="page-hero"><div class="wrap split"><div><p class="crumbs"><a href="' . $home . '">Trang chủ</a> / <a href="' . $u('dich-vu/') . '">Dịch Vụ</a> / Combo mẹ và bé</p>'
            . '<p class="kicker">Dịch vụ cho mẹ</p><h1>Combo mẹ và bé</h1><p class="lead">Một lịch đến nhà: mẹ 90 phút, bé 45 phút. Thanh Lan làm phần chăm sóc, rồi để mẹ tự làm tiếp những việc nhẹ.</p>'
            . '<p>Bé được massage, tắm, hơ lá trầu, hỗ trợ ti và vỗ ợ. Mẹ được gội, đắp mặt, massage, quấn muối, xông và ngâm chân theo số lần của gói.</p></div>'
            . '<figure class="panel">' . tl_img('cham-me-tai-nha-2.jpg', 'Mẹ và bé ở nhà', 'cover') . '</figure></div>'
            . '<div class="wrap" style="margin-top:28px"><h2>Bảng giá</h2><div class="grid three"><article class="panel"><h3>20 ngày</h3><p class="price">6.900.000đ</p><ul class="clean"><li>Gội đầu thảo dược 3 lần</li><li>Đắp mặt nạ 6 lần</li><li>Massage vai gáy 3 lần</li><li>Quấn muối nóng bụng 17 lần</li><li>Tẩy tế bào chết body 2 lần</li><li>Thoa rượu nghệ 16 lần và rượu gừng 16 lần</li><li>Xông thảo dược toàn thân 2 lần</li><li>Xông vùng kín 15 lần</li><li>Ngâm chân muối thảo dược 10 lần</li></ul></article>'
            . '<article class="panel"><h3>30 ngày</h3><p class="price">9.900.000đ</p><ul class="clean"><li>Gội đầu thảo dược 5 lần</li><li>Đắp mặt nạ 10 lần</li><li>Massage vai gáy 5 lần</li><li>Quấn muối nóng bụng 25 lần</li><li>Tẩy tế bào chết body 5 lần</li><li>Thoa rượu nghệ 25 lần và rượu gừng 25 lần</li><li>Xông thảo dược toàn thân 5 lần</li><li>Xông vùng kín 25 lần</li><li>Ngâm chân muối thảo dược 25 lần</li></ul></article></div>'
            . '<p style="margin-top:16px"><button class="btn" type="button" data-book="Combo mẹ và bé 20 ngày hoặc 30 ngày">Đặt lịch gói này</button></p></div></section>',
        'cham-soc-bau' => '<section class="page-hero"><div class="wrap split"><div><p class="crumbs"><a href="' . $home . '">Trang chủ</a> / <a href="' . $u('dich-vu/') . '">Dịch Vụ</a> / Chăm sóc bầu tại nhà</p>'
            . '<p class="kicker">Dịch vụ cho mẹ</p><h1>Chăm sóc bầu tại nhà</h1><p class="lead">Massage nhẹ và cách nghỉ cho những tuần cuối. Buổi spa không thay lần khám thai.</p>'
            . '<p>Mẹ nằm nghiêng, một gối giữa hai đầu gối và một gối mỏng sau lưng. Kỹ thuật viên massage vai, lưng và chân, không đè bụng. Ra nước ối, ra máu, thai máy ít hẳn hoặc đau đầu kèm mắt mờ thì mẹ đi khám.</p>'
            . '<p>Bảng giá liệu trình về nhà hiện dành cho mẹ sau sinh và bé. Gói bầu báo khi mẹ nhắn tuần thai và quận đang ở.</p>'
            . '<button class="btn" type="button" data-book="Chăm sóc bầu tại nhà">Đặt lịch dịch vụ này</button></div>'
            . '<figure class="panel">' . tl_img('doi-ngu-6.jpg', 'Đội ngũ Thanh Lan', 'cover') . '</figure></div></section>',
        'thong-tac-tia-sua' => '<section class="page-hero"><div class="wrap split"><div><p class="crumbs"><a href="' . $home . '">Trang chủ</a> / <a href="' . $u('dich-vu/') . '">Dịch Vụ</a> / Thông tắc tia sữa</p>'
            . '<p class="kicker">Dịch vụ cho mẹ</p><h1>Thông tắc tia sữa</h1><p class="lead">Massage ngực và thông tuyến sữa làm theo tình trạng, trong buổi chăm mẹ.</p>'
            . '<p>Mẹ cho bú hoặc hút bên đang căng trước. Kỹ thuật viên hỏi bên nào cứng, có sốt hay không, và dừng nếu mẹ đau quá mức chịu được. Sốt, vết đỏ lan hoặc ngực vẫn cứng sau nhiều giờ thì mẹ cần gặp bác sĩ.</p></div>'
            . '<figure class="panel">' . tl_img('cham-me-tai-nha-1.jpg', 'Chăm sóc mẹ tại nhà', 'cover') . '</figure></div>'
            . '<div class="wrap" style="margin-top:28px"><h2>Bảng giá</h2><p>Không tách giá từng buổi. Việc này nằm trong liệu trình mẹ 10, 20 và 30 ngày.</p><p><a class="btn" href="' . $u('cham-soc-me-be/') . '">Xem bảng giá liệu trình mẹ</a></p></div></section>',
        'giam-eo' => '<section class="page-hero"><div class="wrap split"><div><p class="crumbs"><a href="' . $home . '">Trang chủ</a> / <a href="' . $u('dich-vu/') . '">Dịch Vụ</a> / Giảm eo sau sinh</p>'
            . '<p class="kicker">Dịch vụ cho mẹ</p><h1>Giảm eo sau sinh</h1><p class="lead">Định hình eo làm trong liệu trình mẹ, bằng máy EMS và nằm máy rung. Không bán lẻ một buổi.</p>'
            . '<p>Mẹ bắt đầu khi vết mổ đã liền và đi lại thoải mái. Bụng mổ chưa được đè.</p></div>'
            . '<figure class="panel">' . tl_img('cham-me-tai-nha-3.jpg', 'Phục hồi sau sinh tại nhà', 'cover') . '</figure></div>'
            . '<div class="wrap" style="margin-top:28px"><h2>Bảng giá</h2><div class="grid three"><article class="panel"><h3>20 ngày</h3><p class="price">8.700.000đ</p><p>10 lần máy EMS, kèm nằm máy rung giảm eo.</p></article><article class="panel"><h3>30 ngày</h3><p class="price">11.250.000đ</p><p>15 lần máy EMS, kèm nằm máy rung giảm eo.</p></article></div><p style="margin-top:16px"><a class="btn" href="' . $u('cham-soc-me-be/') . '">Xem đủ liệu trình mẹ</a></p></div></section>',
    );

    foreach ($pages as $slug => $content) {
        $page = get_page_by_path($slug);
        if (!$page) {
            $write('missing ' . $slug);
            continue;
        }
        $content = str_replace('http://www.tolan.vn', 'https://www.tolan.vn', $content);
        wp_update_post(array('ID' => $page->ID, 'post_content' => $content));
        $write('page ' . $slug . ' ' . $page->ID);
    }

    do_action('litespeed_purge_all');
        $write('done');
        update_option('thanh_lan_home_layout_v', '2', false);
}
