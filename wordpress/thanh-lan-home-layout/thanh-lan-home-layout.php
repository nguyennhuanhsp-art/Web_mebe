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
    global $home, $u;
    if (get_option('thanh_lan_home_layout_v') === '3') {
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
            . '<p class="lead">Ms. Tô Lan và đội ngũ đồng hành cùng mẹ bầu, mẹ sau sinh và bé. Cơ sở có mặt tại 4 chi nhánh Bệnh viện Đa khoa Xuyên Á.</p>'
            . '<div class="actions"><a class="btn" href="' . $u('dich-vu/') . '">Xem Dịch Vụ</a><a class="btn light" href="' . $u('tu-van/') . '">Nhận tư vấn</a></div>'
            . '<div class="facts"><article><strong>Gần 10 năm</strong><span>Kinh nghiệm mẹ và bé, từ 2018</span></article><article><strong>20.000+</strong><span>Em bé và bà mẹ đã đồng hành</span></article><article><strong>4 chi nhánh</strong><span>Trong bệnh viện Xuyên Á</span></article></div></div>'
            . '<figure class="frame"><div class="hero-slides">' . $slides . '</div><figcaption class="caption">Đội Ngũ Thanh Lan Spa</figcaption></figure></div></section>';
    }

    $belief = '<section class="section belief"><div class="wrap"><div class="belief-top">'
        . '<article class="belief-card mission"><h2>Sứ mệnh</h2><p>Đồng hành chăm sóc và nâng đỡ người mẹ từ gốc rễ, để ươm mầm thế hệ tương lai khỏe mạnh, trí tuệ và nhân ái.</p></article>'
        . '<article class="belief-card vision"><h2>Tầm nhìn</h2><p>Trở thành hệ sinh thái chăm sóc - giáo dục mẹ và bé hàng đầu Việt Nam, tiên phong nâng tầm người mẹ, góp phần kiến tạo một thế hệ tương lai ưu tú.</p></article>'
        . '</div><article class="belief-card values"><h2>Giá trị cốt lõi</h2><div class="value-grid">'
        . '<article><strong>1. Tận tâm</strong><p>Chăm sóc bằng cả trái tim: thấu hiểu, yêu thương và đặt lợi ích của mẹ và bé làm trọng tâm trong mọi hành động.</p></article>'
        . '<article><strong>2. Chất lượng</strong><p>Lấy an toàn làm nền tảng: kiên định với tiêu chuẩn chuyên môn, an toàn và chất lượng trong từng dịch vụ.</p></article>'
        . '<article><strong>3. Trí tuệ</strong><p>Trao tri thức, tạo giá trị: không ngừng học hỏi, chia sẻ kiến thức và nâng cao năng lực của người mẹ trên hành trình nuôi dưỡng thế hệ tương lai.</p></article>'
        . '<article><strong>4. Chính trực</strong><p>Giữ trọn niềm tin: trung thực, minh bạch, trách nhiệm và nhất quán giữa lời nói với hành động.</p></article>'
        . '<article><strong>5. Đồng hành</strong><p>Cùng mẹ kiến tạo tương lai: gắn bó, thấu hiểu và hỗ trợ người mẹ, trẻ em và gia đình trong những giai đoạn quan trọng của cuộc sống.</p></article>'
        . '<article><strong>6. Khát vọng</strong><p>Vươn xa vì thế hệ mai sau: không ngừng đổi mới và phát triển để tạo ra những giá trị bền vững cho gia đình, cộng đồng và xã hội.</p></article>'
        . '</div></article></div></section>';

    $intro = $belief;

    $baby_cat = get_term_by('slug', 'huong-dan-cham-soc-be', 'category');
    $baby_cat_url = ($baby_cat && !is_wp_error($baby_cat)) ? get_term_link($baby_cat) : $u('kien-thuc/');
    if (is_wp_error($baby_cat_url)) {
        $baby_cat_url = $u('kien-thuc/');
    }
    $baby_cat_url = str_replace('http://www.tolan.vn', 'https://www.tolan.vn', $baby_cat_url);
    $baby = '<section class="section" style="background:#fff"><div class="wrap"><h2>Dịch vụ cho bé</h2><p class="intro">Chọn một thẻ để đọc nội dung buổi chăm. Bảng giá nằm ở cuối trang dịch vụ.</p><div class="grid services">'
        . tl_card('tam-cho-be.jpg', 'Tắm cho bé', 'Tắm bé sơ sinh', 'Massage, tắm, vệ sinh và hơ lá trầu. Mỗi buổi 45 phút tại nhà.', $u('tam-be/'), 'Xem dịch vụ')
        . '</div></div></section>';

    $mother = '<section class="section"><div class="wrap"><h2>Dịch vụ cho mẹ</h2><p class="intro">Mỗi dịch vụ một trang. Giá của gói được ghi ở phần dưới trang đó.</p><div class="grid services">'
        . tl_card('cham-me-tai-nha-1.jpg', 'Chăm sóc mẹ tại nhà', 'Chăm sóc mẹ sau sinh', 'Liệu trình 10, 20 hoặc 30 ngày. Mỗi buổi 90 phút.', $u('cham-soc-me-be/'), 'Xem dịch vụ')
        . tl_card('cham-me-tai-nha-1.jpg', 'Buổi chăm mẹ', 'Thông tắc tia sữa', 'Làm trong liệu trình mẹ, theo tình trạng.', $u('thong-tac-tia-sua/'), 'Xem dịch vụ')
        . tl_card('cham-me-tai-nha-3.jpg', 'Lều xông sau sinh', 'Liệu trình giảm eo', 'Nằm trong liệu trình mẹ 20 ngày và 30 ngày.', $u('giam-eo/'), 'Xem dịch vụ')
        . '</div></div></section>';

    $knowledge_cards = '';
    $knowledge_topics = array(
        'me-bau-can-biet' => 'Mẹ bầu cần biết',
        'me-sau-sinh-can-biet' => 'Mẹ sau sinh cần biết',
        'dau-an-thanh-lan' => 'Dấu ấn Thanh Lan',
    );
    foreach ($knowledge_topics as $topic_slug => $topic_label) {
        $topic_query = new WP_Query(array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'category_name' => $topic_slug,
        ));
        if (!$topic_query->have_posts()) {
            wp_reset_postdata();
            continue;
        }
        $topic_post = $topic_query->posts[0];
        $topic_image = get_the_post_thumbnail_url($topic_post->ID, 'large');
        if (!$topic_image) {
            $topic_image = tl_src('kien-thuc.jpg');
        }
        $topic_image = str_replace('http://www.tolan.vn', 'https://www.tolan.vn', $topic_image);
        $topic_link = str_replace('http://www.tolan.vn', 'https://www.tolan.vn', get_permalink($topic_post));
        $topic_text = wp_trim_words(wp_strip_all_tags(get_the_excerpt($topic_post) ?: $topic_post->post_content), 22);
        $knowledge_cards .= '<article class="card"><img src="' . esc_url($topic_image) . '" alt="' . esc_attr(get_the_title($topic_post)) . '" />'
            . '<div class="pad"><p class="kicker">' . esc_html($topic_label) . '</p><h3>' . esc_html(get_the_title($topic_post)) . '</h3>'
            . '<p>' . esc_html($topic_text) . '</p><a class="more" href="' . esc_url($topic_link) . '">Đọc bài</a></div></article>';
        wp_reset_postdata();
    }
    $knowledge = '<section class="section knowledge-home" style="background:#fff"><div class="wrap"><h2>Kinh nghiệm chăm sóc</h2><div class="grid three">'
        . $knowledge_cards
        . '</div><p style="text-align:center;margin-top:18px"><a class="more" href="' . $u('kien-thuc/') . '">Xem tất cả bài viết</a></p></div></section>';

    $shop = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : $u('cua-hang/');
    $shop = str_replace('http://www.tolan.vn', 'https://www.tolan.vn', (string) $shop);
    $product_cards = '';
    $catalog = array(
        'cham-soc-bau' => 'Chăm sóc bầu',
        'cham-soc-be' => 'Chăm sóc bé',
        'cham-soc-me' => 'Chăm sóc mẹ',
    );
    foreach ($catalog as $slug => $label) {
        $found = new WP_Query(array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'tax_query' => array(array(
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => $slug,
            )),
        ));
        if (!$found->have_posts()) {
            wp_reset_postdata();
            continue;
        }
        $product_post = $found->posts[0];
        $product = function_exists('wc_get_product') ? wc_get_product($product_post->ID) : null;
        $image = get_the_post_thumbnail_url($product_post->ID, 'medium');
        if (!$image) {
            $image = tl_src('doi-ngu-1.jpg');
        }
        $image = str_replace('http://www.tolan.vn', 'https://www.tolan.vn', $image);
        $term = get_term_by('slug', $slug, 'product_cat');
        $link = ($term && !is_wp_error($term)) ? get_term_link($term) : $shop;
        if (is_wp_error($link)) {
            $link = $shop;
        }
        $link = str_replace('http://www.tolan.vn', 'https://www.tolan.vn', $link);
        $blurb = $product ? wp_strip_all_tags($product->get_short_description()) : '';
        $product_cards .= '<article class="card"><img src="' . esc_url($image) . '" alt="' . esc_attr($product ? $product->get_name() : $label) . '" />'
            . '<div class="pad"><p class="product-label static">' . esc_html($label) . '</p><h3>' . esc_html($product ? $product->get_name() : $label) . '</h3>'
            . '<p>' . esc_html($blurb) . '</p><a class="btn" href="' . esc_url($link) . '">Vào xem cửa hàng</a></div></article>';
        wp_reset_postdata();
    }
    $products = '<section class="section"><div class="wrap"><h2>Cửa hàng</h2><p class="shop-note">Sản phẩm được phân phối trong hệ thống Thanh Lan Spa và các cơ sở chăm sóc mẹ &amp; bé các bệnh viện Xuyên Á trên toàn quốc. Liên hệ để được báo giá sỉ.</p>'
        . '<div class="grid three shop-home">' . $product_cards . '</div></div></section>';
    $write('products ' . substr_count($product_cards, '<article'));

    $service_card = static function ($file, $alt, $title) use ($u) {
        $href = add_query_arg('dichvu', $title, 'https://www.tolan.vn/tu-van/');
        return '<article class="card">' . tl_img($file, $alt)
            . '<div class="pad"><h3>' . esc_html($title) . '</h3>'
            . '<a class="btn" href="' . esc_url($href) . '">Liên hệ tư vấn</a></div></article>';
    };
    $services = '<section class="section" style="background:#fff"><div class="wrap"><h2>Dịch vụ Tại Thanh Lan Spa</h2><div class="grid four service-home">'
        . $service_card('tam-cho-be.jpg', 'Tắm bé sơ sinh', 'Tắm bé sơ sinh')
        . $service_card('cham-me-tai-nha-1.jpg', 'Chăm sóc mẹ sau sinh', 'Chăm sóc mẹ sau sinh')
        . $service_card('cham-me-tai-nha-2.jpg', 'Thông tắc tia sữa', 'Thông tắc tia sữa')
        . $service_card('cham-me-tai-nha-3.jpg', 'Liệu trình giảm eo', 'Liệu trình giảm eo')
        . '</div></div></section>';
    $front_html = str_replace('http://www.tolan.vn', 'https://www.tolan.vn', $hero . $intro . $knowledge . $services . $products);
    wp_update_post(array('ID' => $front_id, 'post_content' => $front_html));
    $write('front ' . $front_id);

    $me10 = '<ul class="clean"><li>Gội đầu thảo dược 2 lần</li><li>Chăm sóc da mặt 2 lần</li><li>Giảm bọng mắt 5 lần</li><li>Massage ngực, thông tuyến sữa theo tình trạng</li><li>Massage body 2 lần</li><li>Làm sáng nách 5 lần</li><li>Định hình săn chắc bụng 5 lần</li><li>Tẩy tế bào chết body 2 lần</li><li>Ủ trắng body 2 lần</li><li>Thoa rượu gừng, nghệ 7 lần</li><li>Xông thảo dược toàn thân 2 lần</li><li>Xông vùng kín 4 lần</li><li>Ngâm chân muối thảo dược 4 lần</li><li>Ủ ấm bằng chăn điện 7 lần</li><li>Tặng 1 chai cốt nhàu, không quy đổi</li></ul>';
    $me20 = '<ul class="clean"><li>Gội đầu thảo dược 4 lần</li><li>Chăm sóc da mặt 4 lần</li><li>Giảm bọng mắt 10 lần</li><li>Massage ngực, thông tuyến sữa theo tình trạng</li><li>Massage body 5 lần</li><li>Làm sáng nách 10 lần</li><li>Định hình bằng máy EMS 10 lần</li><li>Tẩy tế bào chết body 4 lần</li><li>Ủ trắng body 4 lần</li><li>Thoa rượu gừng, nghệ 10 lần</li><li>Xông thảo dược toàn thân 4 lần</li><li>Xông vùng kín 15 lần</li><li>Ngâm chân muối thảo dược 10 lần</li><li>Ủ ấm bằng chăn điện 10 lần</li><li>Tặng 2 chai cốt nhàu, không quy đổi</li></ul>';
    $me30 = '<ul class="clean"><li>Gội đầu thảo dược 5 lần</li><li>Chăm sóc da mặt 6 lần</li><li>Giảm bọng mắt 15 lần</li><li>Massage ngực, thông tuyến sữa theo tình trạng</li><li>Massage body 6 lần</li><li>Làm sáng nách 15 lần</li><li>Định hình bằng máy EMS 15 lần</li><li>Tẩy tế bào chết body 5 lần</li><li>Ủ trắng body 5 lần</li><li>Thoa rượu gừng, nghệ 20 lần</li><li>Xông thảo dược toàn thân 5 lần</li><li>Xông vùng kín 20 lần</li><li>Ngâm chân muối thảo dược 15 lần</li><li>Ủ ấm bằng chăn điện 20 lần</li><li>Tặng 3 chai cốt nhàu, không quy đổi</li></ul>';

    $pages = array(
        'dich-vu' => '<section class="section"><div class="wrap"><p class="crumbs"><a href="' . $home . '">Trang chủ</a> / Dịch Vụ</p><h2>Dịch vụ</h2><p class="intro">Chọn dịch vụ để đọc nội dung. Bảng giá nằm ở cuối mỗi trang.</p><div class="grid services">'
            . tl_card('tam-cho-be.jpg', 'Tắm cho bé', 'Tắm bé sơ sinh', 'Massage, tắm, vệ sinh và hơ lá trầu tại nhà.', $u('tam-be/'), 'Xem dịch vụ')
            . tl_card('cham-me-tai-nha-1.jpg', 'Chăm sóc mẹ', 'Chăm sóc mẹ sau sinh', 'Liệu trình 10, 20 hoặc 30 ngày.', $u('cham-soc-me-be/'), 'Xem dịch vụ')
            . tl_card('cham-me-tai-nha-1.jpg', 'Chăm mẹ', 'Thông tắc tia sữa', 'Theo tình trạng, trong liệu trình mẹ.', $u('thong-tac-tia-sua/'), 'Xem dịch vụ')
            . tl_card('cham-me-tai-nha-3.jpg', 'Xông', 'Liệu trình giảm eo', 'Trong gói mẹ 20 ngày và 30 ngày.', $u('giam-eo/'), 'Xem dịch vụ')
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

    $css_path = get_stylesheet_directory() . '/assets/site.css';
    if (is_readable($css_path) && is_writable($css_path)) {
        $css = file_get_contents($css_path);
        $css = str_replace(
            'footer { background: #22313f; color: #d7e1ea;',
            'footer { background: var(--blue-dark); color: #eaf6fd;',
            $css
        );
        file_put_contents($css_path, $css);
        $write('css footer');
    }
    $functions_path = get_stylesheet_directory() . '/functions.php';
    if (is_readable($functions_path) && is_writable($functions_path)) {
        $functions = file_get_contents($functions_path);
        $functions = str_replace("array('thanh-lan-fonts'), '1.4.0'", "array('thanh-lan-fonts'), '1.5.0'", $functions);
        file_put_contents($functions_path, $functions);
        $write('css version');
    }

    do_action('litespeed_purge_all');
    $write('done');
    update_option('thanh_lan_home_layout_v', '3', false);
}
