<?php
/**
 * Trang Giới thiệu theo bài chị viết trong bảng: câu chuyện, quá trình hình thành, sứ mệnh, tầm nhìn, giá trị.
 */
if (!function_exists('thanh_lan_cau_chuyen_html')) {
    function thanh_lan_anh($file, $alt, $class = '') {
        $src = 'https://www.tolan.vn/wp-content/uploads/2026/11/web/' . $file;
        $class_attr = $class !== '' ? ' class="' . esc_attr($class) . '"' : '';
        return '<img src="' . esc_url($src) . '" alt="' . esc_attr($alt) . '"' . $class_attr . ' />';
    }

    function thanh_lan_cau_chuyen_html($home) {
        $home = esc_url($home);
        $h = static function ($file, $alt, $caption) {
            return '<figure>' . thanh_lan_anh($file, $alt) . '<figcaption class="caption">' . esc_html($caption) . '</figcaption></figure>';
        };
        return '<section class="page-hero"><div class="wrap">'
            . '<p class="crumbs"><a href="' . $home . '">Trang chủ</a> / Giới thiệu</p>'
            . '<p class="kicker">Giới thiệu</p>'
            . '<h1>Câu chuyện chị Tô Lan</h1>'
            . '<p class="lead">Người phụ nữ bình dị, với trái tim muốn trở thành điểm tựa cho hàng ngàn phụ nữ.</p>'
            . '<div class="split" style="margin-top:18px"><div>'
            . '<h2>Khởi nguồn từ một câu hỏi</h2>'
            . '<p>Trước khi Thanh Lan Spa ra đời, chị Tô Lan có một công việc văn phòng ổn định và một nhịp sống bình lặng. Trong vòng quay ấy, chị vẫn tự hỏi liệu mình chỉ dừng ở một cuộc đời an toàn, hay còn có thể làm một việc thực sự chạm tới người khác. Chị muốn công việc vừa lo được cuộc sống, vừa khiến trái tim ấm lại mỗi khi kết thúc một ngày.</p>'
            . '<h2>Bước ngoặt: phụ nữ sau sinh cần được yêu thương</h2>'
            . '<p>Chị rẽ sang chăm sóc mẹ và bé không phải vì một bài toán kinh doanh. Chị thấy người mẹ, sau hành trình vượt cạn, thường mệt, nhạy cảm, có khi cô đơn, và rất dễ bị lãng quên. Chị chọn đứng cạnh, lắng nghe và chăm sóc, để mỗi mẹ cảm nhận được một điều rõ ràng: mình xứng đáng được trân trọng và yêu thương.</p>'
            . '</div><figure class="panel">' . thanh_lan_anh('doi-ngu-4.jpg', 'Đội ngũ Thanh Lan trong ngày khai trương', 'cover')
            . '<figcaption class="caption">Ngày khai trương, đội ngũ đứng cùng khách.</figcaption></figure></div>'
            . '<aside class="quote" style="margin-top:18px"><p>“Tôi không nhận mình là người giỏi nhất, nhưng tôi tự hào vì luôn làm nghề bằng tất cả sự tử tế. Thanh Lan không chỉ cung cấp dịch vụ chăm sóc mẹ và bé, mà là tổ ấm thứ hai, nơi phụ nữ được yêu thương đúng nghĩa.”</p><span>Tô Thị Lan, CEO và Founder</span></aside>'
            . '<div class="split" style="margin-top:22px"><div class="panel">'
            . '<h2>Đi lên từ năm 2018</h2>'
            . '<p>Khi đã rõ điều mình muốn làm, năm 2018 chị Tô Lan bắt đầu Thanh Lan Spa từ con số không. Chưa có vốn lớn, chưa có mối quan hệ, chưa có kinh nghiệm thương trường. Tài sản lúc ấy là đôi bàn tay tận tụy và một tấm lòng chân thành.</p>'
            . '<p>Chị tự tay chăm từng sản phụ, tự học và kiên trì sửa từ những buổi làm thật. Có ngày mệt đến muốn dừng. Nụ cười của mẹ sau liệu trình là lý do chị biết mình đang đi đúng đường.</p>'
            . '</div><div class="panel">'
            . '<h2>Thanh Lan hôm nay</h2>'
            . '<p>Thanh Lan không lớn từ một chiến lược hào nhoáng. Thương hiệu lớn lên từ sự tin của khách hàng, và đã trở thành người bạn đồng hành của hơn 20.000 em bé và bà mẹ, có mặt tại 4 chi nhánh Bệnh viện Đa khoa Xuyên Á.</p>'
            . '<p>Với chị Tô Lan, điều đáng kể không nằm ở con số. Đó là khoảnh khắc một người mẹ từng mệt mỏi trở nên vững hơn, em bé khỏe mạnh, và cả nhà có thêm sự bình yên.</p>'
            . '<p>Tên quốc tế: THANH LAN MOM BABY CARE.<br />Tên thương mại: Mẹ và Bé Thanh Lan.<br />CÔNG TY TNHH TM&amp;DV THANH LAN.<br />MST 0319180928, đăng ký ngày 25/9/2025.<br />Số 77, đường 537, Ấp Bàu Trăn, Xã Nhuận Đức, TPHCM.</p>'
            . '</div></div>'
            . '<div class="grid three" style="margin-top:22px">'
            . '<article class="panel"><h3>Sứ mệnh</h3><p>Đồng hành chăm sóc và nâng đỡ người mẹ từ gốc rễ, để ươm mầm thế hệ tương lai khỏe mạnh, trí tuệ và nhân ái.</p></article>'
            . '<article class="panel"><h3>Tầm nhìn</h3><p>Trở thành hệ sinh thái chăm sóc và giáo dục mẹ và bé hàng đầu Việt Nam, tiên phong nâng tầm người mẹ, góp phần kiến tạo một thế hệ tương lai ưu tú.</p></article>'
            . '<article class="panel"><h3>Cách chị làm nghề</h3><p>Tử tế trong từng buổi chăm. Không nhận mình giỏi nhất. Tự hào vì làm bằng sự chân thành.</p></article>'
            . '</div>'
            . '<h2 style="margin-top:28px">Giá trị cốt lõi</h2>'
            . '<div class="grid three">'
            . '<article class="panel"><h3>Tận tâm</h3><p>Chăm sóc bằng cả trái tim. Thấu hiểu, yêu thương, và đặt lợi ích của mẹ và bé làm trọng tâm.</p></article>'
            . '<article class="panel"><h3>Chất lượng</h3><p>Lấy an toàn làm nền. Giữ tiêu chuẩn chuyên môn trong từng dịch vụ.</p></article>'
            . '<article class="panel"><h3>Trí tuệ</h3><p>Trao kiến thức cho mẹ trên hành trình nuôi con, và không ngừng học thêm.</p></article>'
            . '<article class="panel"><h3>Chính trực</h3><p>Giữ niềm tin bằng sự trung thực, minh bạch, và lời nói đi cùng việc làm.</p></article>'
            . '<article class="panel"><h3>Đồng hành</h3><p>Ở cạnh mẹ, bé và gia đình trong những giai đoạn quan trọng của cuộc sống.</p></article>'
            . '<article class="panel"><h3>Khát vọng</h3><p>Làm tốt hơn mỗi ngày, để giá trị ấy bền với gia đình và cộng đồng.</p></article>'
            . '</div>'
            . '<div class="shots">'
            . $h('doi-ngu-1.jpg', 'Kỹ thuật viên Thanh Lan trong lễ tốt nghiệp', 'Lễ tốt nghiệp của kỹ thuật viên.')
            . $h('doi-ngu-3.jpg', 'Đội Ngũ Thanh Lan Spa', 'Đội ngũ trước biển hiệu Thanh Lan.')
            . $h('day-thang-be-2.jpg', 'Gia đình và kỹ thuật viên trong lễ đầy tháng', 'Một lễ đầy tháng đội ngũ có mặt cùng gia đình.')
            . '</div></div></section>';
    }
}

if (PHP_SAPI === 'cli' && isset($_SERVER['SCRIPT_FILENAME']) && realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__) {
    require '/home/giqisvhphosting/public_html/wp-load.php';
    kses_remove_filters();
    $log = WP_CONTENT_DIR . '/uploads/thanh-lan-cau-chuyen.log';
    $write = static function ($message) use ($log) {
        file_put_contents($log, $message . "\n", FILE_APPEND);
        fwrite(STDOUT, $message . "\n");
    };
    $home = home_url('/');
    $html = thanh_lan_cau_chuyen_html($home);
    $page = get_page_by_path('gioi-thieu');
    if (!$page) {
        $write('missing page');
        exit(1);
    }
    wp_update_post(array('ID' => $page->ID, 'post_content' => $html, 'post_title' => 'Giới thiệu'));
    $write('page ' . $page->ID);

    $front_id = (int) get_option('page_on_front');
    if ($front_id) {
        $front = get_post($front_id);
        $fixed = str_replace('từ 2019', 'từ 2018', $front->post_content);
        if ($fixed !== $front->post_content) {
            wp_update_post(array('ID' => $front_id, 'post_content' => $fixed));
            $write('front year');
        }
    }

    $post = get_page_by_path('dau-an-doi-ngu-va-le-day-thang', OBJECT, 'post');
    if ($post) {
        $body = '<p>Năm 2018, chị Tô Lan bắt đầu Thanh Lan Spa từ con số không: chưa có vốn lớn, chưa có mối quan hệ, chưa có kinh nghiệm thương trường. Tài sản lúc ấy là đôi bàn tay tận tụy và một tấm lòng chân thành. Chị tự chăm từng sản phụ, tự học và sửa từ những buổi làm thật.</p>'
            . '<p>Thanh Lan không lớn bằng một chiến dịch rầm rộ. Thương hiệu lớn lên từ sự tin của khách hàng, và đã trở thành người bạn đồng hành của hơn 20.000 em bé và bà mẹ, có mặt tại 4 chi nhánh Bệnh viện Đa khoa Xuyên Á.</p>'
            . '<p>Tên quốc tế là THANH LAN MOM BABY CARE, tên thương mại là Mẹ và Bé Thanh Lan. Công ty TNHH TM&amp;DV Thanh Lan, mã số thuế 0319180928, đăng ký ngày 25/9/2025. Câu chuyện đầy đủ của chị Tô Lan nằm ở trang <a href="' . esc_url(home_url('/gioi-thieu/')) . '">Giới thiệu</a>.</p>'
            . '<p>Ảnh đội ngũ trong thư viện gồm lễ tốt nghiệp kỹ thuật viên, ngày khai trương, và ảnh đứng trước biển hiệu Spa chăm sóc và thư giãn Thanh Lan.</p>'
            . '<div class="shots">'
            . '<figure>' . thanh_lan_anh('doi-ngu-1.jpg', 'Lễ tốt nghiệp kỹ thuật viên') . '<figcaption class="caption">Lễ tốt nghiệp.</figcaption></figure>'
            . '<figure>' . thanh_lan_anh('doi-ngu-4.jpg', 'Khai trương Thanh Lan') . '<figcaption class="caption">Ngày khai trương.</figcaption></figure>'
            . '<figure>' . thanh_lan_anh('doi-ngu-3.jpg', 'Đội Ngũ Thanh Lan Spa') . '<figcaption class="caption">Trước biển hiệu Thanh Lan.</figcaption></figure>'
            . '</div>'
            . '<p>Một phần việc hiện ra trong ảnh gia đình: kỹ thuật viên bế bé giữa mâm hoa quả và bánh trong lễ đầy tháng. Đó là cách đội ngũ có mặt sau tháng ở cữ, cạnh bố mẹ.</p>'
            . '<figure>' . thanh_lan_anh('day-thang-be-2.jpg', 'Kỹ thuật viên và gia đình trong lễ đầy tháng') . '<figcaption class="caption">Đầy tháng cùng gia đình.</figcaption></figure>'
            . '<p>Địa chỉ công ty: Số 77, đường 537, Ấp Bàu Trăn, Xã Nhuận Đức, TPHCM. Hotline Ms Tô Lan: 0975.399.319.</p>';
        wp_update_post(array(
            'ID' => $post->ID,
            'post_content' => $body,
            'post_excerpt' => 'Năm 2018 chị Tô Lan dựng Thanh Lan từ đôi bàn tay và sự tử tế. Hôm nay thương hiệu đồng hành cùng hơn 20.000 mẹ và bé.',
        ));
        $write('post ' . $post->ID);
    }
    do_action('litespeed_purge_all');
    $write('done');
    @unlink(__FILE__);
}
