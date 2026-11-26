<?php
/**
 * Viết lại trang và bài Kiến thức từ ảnh chị đã đưa vào Thư viện, rồi tự xóa.
 * Chỉ chạy bằng PHP dòng lệnh.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require '/home/giqisvhphosting/public_html/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
require __DIR__ . '/thanh-lan-cau-chuyen.php';

$admin = get_user_by('login', 'tolan');
if ($admin) {
    wp_set_current_user($admin->ID);
}
kses_remove_filters();
@set_time_limit(0);

$log = WP_CONTENT_DIR . '/uploads/thanh-lan-bai.log';
$write = function ($message) use ($log) {
    file_put_contents($log, $message . "\n", FILE_APPEND);
    fwrite(STDOUT, $message . "\n");
};
$write('start ' . gmdate('c'));

$names = array(
    'doi-ngu-3.jpg' => array('Đội ngũ Thanh Lan trước biển hiệu', 'Đội ngũ Thanh Lan Spa đứng trước biển hiệu spa chăm sóc và thư giãn'),
    'doi-ngu-1.jpg' => array('Lễ tốt nghiệp kỹ thuật viên', 'Kỹ thuật viên Thanh Lan trong lễ tốt nghiệp'),
    'doi-ngu-4.jpg' => array('Khai trương Thanh Lan', 'Đội ngũ và khách trong ngày khai trương Thanh Lan'),
    'doi-ngu-6.jpg' => array('Đội ngũ trước cửa spa', 'Đội ngũ Thanh Lan trước cửa spa'),
    'day-thang-be-2.jpg' => array('Gia đình và kỹ thuật viên trong lễ đầy tháng', 'Kỹ thuật viên Thanh Lan bế bé cùng gia đình trong lễ đầy tháng'),
    'day-thang.jpg' => array('Mâm cỗ đầy tháng', 'Mâm cỗ và bé trong lễ đầy tháng'),
    'day-thang-be-1.jpg' => array('Bé trong lễ đầy tháng', 'Bé mặc váy hồng trong lễ đầy tháng'),
    'cham-me-tai-nha-1.jpg' => array('Massage cho mẹ tại nhà', 'Kỹ thuật viên massage lưng cho mẹ tại nhà'),
    'cham-me-tai-nha-2.jpg' => array('Mẹ và bé trong bếp', 'Mẹ bế bé trong bếp những ngày ở nhà'),
    'cham-me-tai-nha-3.jpg' => array('Lều xông sau sinh', 'Mẹ ngồi trong lều xông tại nhà'),
    'tam-cho-be.jpg' => array('Tắm cho bé', 'Bé được tắm trong chậu'),
    'tam-be.jpg' => array('Bế bé sau buổi chăm', 'Kỹ thuật viên bế bé sau buổi chăm'),
    'tam-be-1.jpg' => array('Bé trong chậu tắm', 'Bé ngồi trong chậu tắm'),
    'tam-be-2.jpg' => array('Chơi bóng trong buổi massage', 'Bé nắm bóng trong buổi massage'),
    'matxa-cho-be.jpg' => array('Massage cho bé', 'Kỹ thuật viên massage bụng cho bé'),
    'ho-la-trau-2.jpg' => array('Lá trầu trên bụng bé', 'Lá trầu đặt trên bụng bé'),
    'ho-trau.jpg' => array('Bé quấn lá trầu', 'Bé sơ sinh được đặt lá trầu'),
    'thu-gian.jpg' => array('Xoa đầu cho bé', 'Hai bàn tay xoa đầu bé'),
    '1795509085758_1798074000091021887_1703208304016546408_b96a03220d7c5d5dee560fe832565c91.jpg' => array('Bánh đầy tháng', 'Bánh đầy tháng của spa trong bệnh viện Xuyên Á'),
    '1795509085782_1798074000091021887_1703208304016546408_944b76675bb4f13ce622ba185a00f11c.jpg' => array('Mâm quà và hoa', 'Mâm quà và hoa trong buổi lễ'),
);

function tl_path($file) {
    return WP_CONTENT_DIR . '/uploads/2026/11/' . $file;
}

function tl_id($file) {
    global $wpdb;
    return (int) $wpdb->get_var($wpdb->prepare(
        "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value = %s LIMIT 1",
        '2026/11/' . $file
    ));
}

function tl_web($file) {
    $src = tl_path($file);
    $dir = WP_CONTENT_DIR . '/uploads/2026/11/web';
    $dest = $dir . '/' . $file;
    if (!file_exists($src)) {
        return '';
    }
    if (!file_exists($dest)) {
        wp_mkdir_p($dir);
        $editor = wp_get_image_editor($src);
        if (!is_wp_error($editor)) {
            $size = $editor->get_size();
            if ($size['width'] > 1400 || $size['height'] > 1400) {
                $editor->resize(1400, 1400, false);
            }
            $editor->set_quality(82);
            $editor->save($dest, 'image/jpeg');
        }
    }
    if (!file_exists($dest)) {
        return content_url('uploads/2026/11/' . $file);
    }
    return content_url('uploads/2026/11/web/' . rawurlencode($file));
}

function tl_img($file, $alt, $class = '') {
    $src = tl_web($file);
    $class_attr = $class ? ' class="' . esc_attr($class) . '"' : '';
    return '<img src="' . esc_url($src) . '" alt="' . esc_attr($alt) . '"' . $class_attr . ' />';
}

function tl_fig($file, $alt, $caption) {
    return '<figure>' . tl_img($file, $alt) . '<figcaption class="caption">' . esc_html($caption) . '</figcaption></figure>';
}

foreach ($names as $file => $meta) {
    $id = tl_id($file);
    if (!$id || !file_exists(tl_path($file))) {
        $write('missing ' . $file);
        continue;
    }
    wp_update_post(array('ID' => $id, 'post_title' => $meta[0], 'post_excerpt' => $meta[1]));
    update_post_meta($id, '_wp_attachment_image_alt', $meta[1]);
    $path = tl_path($file);
    $editor_meta = wp_generate_attachment_metadata($id, $path);
    if ($editor_meta) {
        wp_update_attachment_metadata($id, $editor_meta);
    }
    tl_web($file);
    $write('image ' . $file . ' ' . $id);
}

$home = home_url('/');
$u = function ($path) use ($home) {
    return trailingslashit($home) . ltrim($path, '/');
};

$services = array(
    array('tam-cho-be.jpg', 'Bé được tắm trong chậu', 'Tắm bé sơ sinh', 'Tắm lá, massage và chăm rốn cho bé tại nhà.', '250.000đ / buổi', $u('tam-be/')),
    array('cham-me-tai-nha-1.jpg', 'Kỹ thuật viên massage lưng cho mẹ tại nhà', 'Chăm sóc mẹ bé', 'Xông, massage và hỗ trợ phục hồi sau sinh.', '4.500.000đ / 10 buổi', $u('cham-soc-me-be/')),
    array('cham-me-tai-nha-2.jpg', 'Mẹ bế bé trong bếp những ngày ở nhà', 'Ở cữ tại nhà', 'Đồng hành những tuần đầu ngay trong nhà mẹ.', 'Báo giá theo số ngày', $u('o-cu-tai-nha/')),
    array('doi-ngu-6.jpg', 'Đội ngũ Thanh Lan trước cửa spa', 'Chăm sóc bầu tại nhà', 'Massage nhẹ và hướng dẫn nghỉ những tuần cuối.', 'Báo giá khi đặt lịch', $u('cham-soc-bau/')),
    array('cham-me-tai-nha-1.jpg', 'Buổi chăm mẹ tại nhà', 'Thông tắc tia sữa', 'Khai thông nhẹ, không nặn đến bầm.', '450.000đ / buổi', $u('thong-tac-tia-sua/')),
    array('cham-me-tai-nha-3.jpg', 'Mẹ ngồi trong lều xông tại nhà', 'Giảm eo sau sinh', 'Lấy lại dáng bằng động tác nhẹ, không xâm lấn.', 'Báo giá khi tư vấn', $u('giam-eo/')),
);

$cards = '';
foreach ($services as $item) {
    $cards .= '<article class="card">' . tl_img($item[0], $item[1])
        . '<div class="pad"><h3>' . esc_html($item[2]) . '</h3><p>' . esc_html($item[3]) . '</p><div class="price">' . esc_html($item[4]) . '</div><a class="more" href="' . esc_url($item[5]) . '">Xem trang này</a></div></article>';
}

$front = '<section class="hero"><div class="wrap hero-grid"><div><p class="kicker">Thanh Lan Spa</p><h1>Chăm mẹ và bé trước và sau sinh</h1>'
    . '<p class="lead">Ms. Tô Lan và đội ngũ đồng hành cùng mẹ bầu, mẹ sau sinh và bé. Cơ sở có mặt tại 4 chi nhánh Bệnh viện Đa khoa Xuyên Á.</p>'
    . '<div class="actions"><a class="btn" href="' . esc_url($u('dich-vu/')) . '">Xem Dịch Vụ</a><a class="btn light" href="' . esc_url($u('tu-van/')) . '">Nhận tư vấn</a></div>'
    . '<div class="facts"><article><strong>Gần 10 năm</strong><span>Kinh nghiệm mẹ và bé, từ 2018</span></article><article><strong>20.000+</strong><span>Em bé và bà mẹ đã đồng hành</span></article><article><strong>4 chi nhánh</strong><span>Trong bệnh viện Xuyên Á</span></article></div></div>'
    . '<figure class="frame"><div class="hero-slides">'
    . tl_img('doi-ngu-1.jpg', 'Đội Ngũ Thanh Lan Spa')
    . tl_img('doi-ngu-2.jpg', 'Đội Ngũ Thanh Lan Spa')
    . tl_img('doi-ngu-3.jpg', 'Đội Ngũ Thanh Lan Spa')
    . tl_img('doi-ngu-4.jpg', 'Đội Ngũ Thanh Lan Spa')
    . tl_img('doi-ngu-5.jpg', 'Đội Ngũ Thanh Lan Spa')
    . tl_img('doi-ngu-6.jpg', 'Đội Ngũ Thanh Lan Spa')
    . '</div><figcaption class="caption">Đội Ngũ Thanh Lan Spa</figcaption></figure></div></section>'
    . '<section class="section"><div class="wrap"><h2>Dịch Vụ</h2><p class="intro">Mẹ chọn một dịch vụ để xem nội dung và đặt lịch.</p><div class="grid services">' . $cards . '</div></div></section>'
    . '<section class="section" style="background:#fff"><div class="wrap"><h2>Trong thư viện ảnh</h2><p class="intro">Ảnh chị đã tải lên, gắn đúng việc đang làm.</p><div class="shots">'
    . tl_fig('day-thang-be-2.jpg', 'Kỹ thuật viên bế bé cùng gia đình', 'Lễ đầy tháng, kỹ thuật viên đứng cùng gia đình.')
    . tl_fig('tam-be.jpg', 'Kỹ thuật viên bế bé sau buổi chăm', 'Sau buổi tắm và massage.')
    . tl_fig('ho-la-trau-2.jpg', 'Lá trầu trên bụng bé', 'Lá trầu trong buổi chăm bé.')
    . '</div></div></section>';

$pages = array();
$pages['__front__'] = $front;

$pages['gioi-thieu'] = thanh_lan_cau_chuyen_html($home);

$pages['dich-vu'] = '<section class="section"><div class="wrap"><p class="crumbs"><a href="' . esc_url($home) . '">Trang chủ</a> / Dịch Vụ</p><h2>Dịch Vụ</h2>'
    . '<p class="intro">Mỗi dịch vụ một trang. Bảng gói nằm ở <a href="https://canva.link/evxg6qiebmgc9hh" target="_blank" rel="noopener">Canva</a>. Giá dưới đây là mức đang ghi trên website.</p>'
    . '<div class="grid services">' . $cards . '</div></div></section>';

$pages['tam-be'] = '<section class="page-hero"><div class="wrap split"><div><p class="crumbs"><a href="' . esc_url($home) . '">Trang chủ</a> / <a href="' . esc_url($u('dich-vu/')) . '">Dịch Vụ</a> / Tắm bé sơ sinh</p>'
    . '<p class="kicker">Dịch Vụ</p><h1>Tắm bé sơ sinh</h1><p class="lead">Một buổi tại nhà gồm massage, tắm và chăm rốn. Bé sạch, được giữ ấm và thường ngủ sau đó.</p><p class="price">250.000đ / buổi</p>'
    . '<p>Kỹ thuật viên xem rốn, da và cách mẹ đang tắm trước khi bắt đầu. Massage đi từ tay, chân, lưng rồi bụng theo chiều kim đồng hồ, lực vừa với cân nặng của bé. Tắm bằng nước ấm. Mắt, mũi, miệng và rốn được lau riêng, không ngâm rốn còn chưa rụng.</p>'
    . '<p>Mẹ đứng cạnh để học tư thế đỡ đầu và cách quấn khăn. Buổi làm xong, mẹ biết lặp lại những động tác nhẹ trong những lần tắm sau.</p>'
    . '<ul class="clean"><li>Massage kích thích giác quan</li><li>Tắm nước ấm, có thể thêm lá thảo dược khi mẹ muốn</li><li>Vệ sinh mắt, mũi, miệng và chăm rốn</li><li>Giữ ấm và xem lại các mốc của bé</li></ul>'
    . '<button class="btn" type="button" data-book="Tắm và massage bé sơ sinh (250.000đ/buổi)">Đặt lịch dịch vụ này</button></div>'
    . '<figure class="panel">' . tl_img('tam-cho-be.jpg', 'Bé được tắm trong chậu', 'cover') . '<figcaption class="caption">Bé trong chậu tắm, ảnh trong thư viện.</figcaption></figure></div>'
    . '<div class="wrap shots">'
    . tl_fig('tam-be.jpg', 'Kỹ thuật viên bế bé sau buổi chăm', 'Bế và giữ ấm sau buổi tắm.')
    . tl_fig('matxa-cho-be.jpg', 'Massage bụng cho bé', 'Massage bụng trước khi tắm.')
    . tl_fig('tam-be-2.jpg', 'Bé nắm bóng trong buổi chăm', 'Bóng nhỏ để bé nắm trong lúc xoa người.')
    . '</div></section>';

$pages['cham-soc-me-be'] = '<section class="page-hero"><div class="wrap split"><div><p class="crumbs"><a href="' . esc_url($home) . '">Trang chủ</a> / <a href="' . esc_url($u('dich-vu/')) . '">Dịch Vụ</a> / Chăm sóc mẹ bé</p>'
    . '<p class="kicker">Dịch Vụ</p><h1>Chăm sóc mẹ bé</h1><p class="lead">Gói 10 buổi cho mẹ sau sinh: xông, massage vai lưng và hướng dẫn những việc trong ngày cùng bé.</p><p class="price">4.500.000đ / 10 buổi</p>'
    . '<p>Buổi đầu kỹ thuật viên hỏi ngày sinh, vết mổ hoặc sinh thường, và chỗ mẹ đang đau. Massage tập trung vai, lưng và chân. Bụng chỉ làm khi mẹ đã được người nhà hoặc bác sĩ cho phép, không đè lên vết mổ còn đau.</p>'
    . '<p>Lều xông dùng khi mẹ muốn ấm người và ra mồ hôi nhẹ. Mẹ ngồi được, có người ở nhà, và bước ra ngay nếu chóng mặt. Ảnh lều xông trong thư viện là buổi làm tại nhà, không phải phòng xông công cộng.</p>'
    . '<button class="btn" type="button" data-book="Chăm sóc mẹ bé (4.500.000đ/10 buổi)">Đặt lịch dịch vụ này</button></div>'
    . '<figure class="panel">' . tl_img('cham-me-tai-nha-1.jpg', 'Kỹ thuật viên massage lưng cho mẹ', 'cover') . '<figcaption class="caption">Massage lưng cho mẹ tại nhà.</figcaption></figure></div>'
    . '<div class="wrap shots">'
    . tl_fig('cham-me-tai-nha-3.jpg', 'Mẹ trong lều xông', 'Lều xông tại nhà.')
    . tl_fig('cham-me-tai-nha-2.jpg', 'Mẹ và bé trong bếp', 'Những ngày mẹ vẫn ở nhà với bé.')
    . tl_fig('doi-ngu-6.jpg', 'Đội ngũ Thanh Lan', 'Đội ngũ phụ trách các buổi chăm mẹ.')
    . '</div></section>';

$pages['o-cu-tai-nha'] = '<section class="page-hero"><div class="wrap split"><div><p class="crumbs"><a href="' . esc_url($home) . '">Trang chủ</a> / <a href="' . esc_url($u('dich-vu/')) . '">Dịch Vụ</a> / Ở cữ tại nhà</p>'
    . '<p class="kicker">Dịch Vụ</p><h1>Ở cữ tại nhà</h1><p class="lead">Kỹ thuật viên đến nhà trong những tuần đầu, làm cùng mẹ thay vì mẹ phải đi ra ngoài.</p><p class="price">Báo giá theo số ngày</p>'
    . '<p>Một ngày ở cữ thường có ba việc: mẹ được nghỉ một lúc, bé được tắm hoặc lau người, và nhà có người nhắc lịch bú. Thanh Lan không thay người thân. Kỹ thuật viên làm phần chăm sóc, rồi để lại cách mẹ tự làm khi buổi kết thúc.</p>'
    . '<p>Lễ đầy tháng trong ảnh là mốc cuối của tháng đầu, khi gia đình mời người thân. Đó là một buổi đội ngũ có mặt cùng mẹ, không phải gói ở cữ kéo dài cả tháng lễ.</p>'
    . '<button class="btn" type="button" data-book="Ở cữ tại nhà">Đặt lịch dịch vụ này</button></div>'
    . '<figure class="panel">' . tl_img('cham-me-tai-nha-2.jpg', 'Mẹ bế bé trong bếp', 'cover') . '<figcaption class="caption">Mẹ và bé ở nhà.</figcaption></figure></div>'
    . '<div class="wrap shots">'
    . tl_fig('day-thang.jpg', 'Mâm cỗ đầy tháng', 'Mâm cỗ đầy tháng.')
    . tl_fig('day-thang-be-1.jpg', 'Bé trong lễ đầy tháng', 'Bé trong ngày đầy tháng.')
    . tl_fig('day-thang-be-2.jpg', 'Gia đình và kỹ thuật viên', 'Gia đình và kỹ thuật viên trong lễ.')
    . '</div></section>';

$pages['cham-soc-bau'] = '<section class="page-hero"><div class="wrap split"><div><p class="crumbs"><a href="' . esc_url($home) . '">Trang chủ</a> / <a href="' . esc_url($u('dich-vu/')) . '">Dịch Vụ</a> / Chăm sóc bầu tại nhà</p>'
    . '<p class="kicker">Dịch Vụ</p><h1>Chăm sóc bầu tại nhà</h1><p class="lead">Massage nhẹ và cách nghỉ cho những tuần cuối. Buổi spa không thay lần khám thai.</p><p class="price">Báo giá khi đặt lịch</p>'
    . '<p>Mẹ nằm nghiêng, một gối giữa hai đầu gối và một gối mỏng sau lưng. Kỹ thuật viên massage vai, lưng và chân ở thế nằm nghiêng hoặc ngồi. Không đè bụng. Không nằm ngửa lâu nếu mẹ chóng mặt.</p>'
    . '<p>Ra nước ối, ra máu, thai máy ít hẳn hoặc đau đầu kèm mắt mờ thì mẹ đi khám. Thư viện hiện chưa có ảnh mẹ bầu, nên trang này dùng ảnh đội ngũ sẽ đến nhà.</p>'
    . '<button class="btn" type="button" data-book="Chăm sóc bầu tại nhà">Đặt lịch dịch vụ này</button></div>'
    . '<figure class="panel">' . tl_img('doi-ngu-6.jpg', 'Đội ngũ Thanh Lan trước cửa spa', 'cover') . '<figcaption class="caption">Đội ngũ đến nhà. Ảnh mẹ bầu chưa có trong thư viện.</figcaption></figure></div></section>';

$pages['thong-tac-tia-sua'] = '<section class="page-hero"><div class="wrap split"><div><p class="crumbs"><a href="' . esc_url($home) . '">Trang chủ</a> / <a href="' . esc_url($u('dich-vu/')) . '">Dịch Vụ</a> / Thông tắc tia sữa</p>'
    . '<p class="kicker">Dịch Vụ</p><h1>Thông tắc tia sữa</h1><p class="lead">Khai thông bằng tay, lực tăng dần. Mục tiêu là ngực mềm hơn, không nặn đến bầm.</p><p class="price">450.000đ / buổi</p>'
    . '<p>Mẹ cho bú hoặc hút sữa trước khi kỹ thuật viên đến, nếu bé đang đói thì để bé bú trước. Người làm hỏi bên nào cứng, có sốt hay không, và dừng nếu mẹ đau quá mức chịu được. Sau buổi, mẹ được chỉ tư thế ngậm sao cho quầng vú không bị kéo lệch.</p>'
    . '<p>Sốt cao, vết đỏ lan nhanh hoặc ngực vẫn cứng sau nhiều giờ thì mẹ cần gặp bác sĩ. Thư viện chưa có ảnh cận cảnh bầu ngực, nên ảnh minh họa là buổi chăm mẹ tại nhà.</p>'
    . '<button class="btn" type="button" data-book="Thông tắc tia sữa (450.000đ/buổi)">Đặt lịch dịch vụ này</button></div>'
    . '<figure class="panel">' . tl_img('cham-me-tai-nha-1.jpg', 'Kỹ thuật viên chăm mẹ tại nhà', 'cover') . '<figcaption class="caption">Buổi chăm mẹ tại nhà. Chưa có ảnh cận cảnh thông tắc.</figcaption></figure></div></section>';

$pages['giam-eo'] = '<section class="page-hero"><div class="wrap split"><div><p class="crumbs"><a href="' . esc_url($home) . '">Trang chủ</a> / <a href="' . esc_url($u('dich-vu/')) . '">Dịch Vụ</a> / Giảm eo sau sinh</p>'
    . '<p class="kicker">Dịch Vụ</p><h1>Giảm eo sau sinh</h1><p class="lead">Lấy lại dáng bằng động tác nhẹ và massage vùng eo, không xâm lấn, không hứa một số đo sau vài buổi.</p><p class="price">Báo giá khi tư vấn</p>'
    . '<p>Mẹ bắt đầu khi vết mổ đã liền và người nhà thấy mẹ đi lại thoải mái. Kỹ thuật viên xem eo, lưng và cách mẹ đang thở trước khi chọn động tác. Bụng mổ chưa được đè.</p>'
    . '<p>Thư viện chưa có ảnh liệu trình giảm eo. Ảnh lều xông bên cạnh là một buổi phục hồi sau sinh, không phải máy giảm béo.</p>'
    . '<button class="btn" type="button" data-book="Giảm eo sau sinh">Đặt lịch dịch vụ này</button></div>'
    . '<figure class="panel">' . tl_img('cham-me-tai-nha-3.jpg', 'Lều xông sau sinh tại nhà', 'cover') . '<figcaption class="caption">Lều xông sau sinh. Ảnh giảm eo chưa có trong thư viện.</figcaption></figure></div></section>';

$pages['feedback'] = '<section class="section"><div class="wrap"><p class="crumbs"><a href="' . esc_url($home) . '">Trang chủ</a> / Feedback</p><h2>Feedback</h2>'
    . '<p class="intro">Ảnh buổi làm việc và lễ gia đình đã gửi vào thư viện. Lời mẹ gửi qua Zalo hoặc fanpage sẽ được đăng thêm khi chị chọn.</p><div class="shots">'
    . tl_fig('tam-be.jpg', 'Kỹ thuật viên bế bé', 'Sau buổi tắm, bé được bế và quấn khăn.')
    . tl_fig('matxa-cho-be.jpg', 'Massage cho bé', 'Massage bụng trong buổi chăm bé.')
    . tl_fig('day-thang-be-2.jpg', 'Lễ đầy tháng', 'Gia đình và kỹ thuật viên trong lễ đầy tháng.')
    . tl_fig('1795509085758_1798074000091021887_1703208304016546408_b96a03220d7c5d5dee560fe832565c91.jpg', 'Bánh đầy tháng', 'Bánh đầy tháng ghi spa trong bệnh viện Xuyên Á.')
    . tl_fig('cham-me-tai-nha-1.jpg', 'Massage cho mẹ', 'Massage lưng trong buổi chăm mẹ.')
    . tl_fig('doi-ngu-3.jpg', 'Đội ngũ Thanh Lan', 'Đội ngũ trước biển hiệu.')
    . '</div></div></section>';

$pages['kien-thuc'] = '<section class="section"><div class="wrap"><p class="crumbs"><a href="' . esc_url($home) . '">Trang chủ</a> / Kiến thức</p><h2>Kiến thức</h2>'
    . '<p class="intro">Năm chuyên mục trong menu. Nhân viên thêm bài ở mục Bài viết và chọn đúng chuyên mục. Ảnh đại diện lấy từ Thư viện.</p>[thanh_lan_bai_viet]</div></section>';

$pages['tu-van'] = '<section class="page-hero"><div class="wrap split"><div><p class="crumbs"><a href="' . esc_url($home) . '">Trang chủ</a> / Nhận tư vấn</p><p class="kicker">Liên hệ</p><h1>Nhận tư vấn</h1>'
    . '<p class="lead">Gọi hoặc nhắn Zalo 0975.399.319, gặp Ms Tô Lan. Mail thanhlanspa238@gmail.com.</p>'
    . '<p>Khi nhắn, mẹ cho biết ngày dự sinh hoặc ngày bé chào đời, dịch vụ đang cần và quận mẹ đang ở. Nhân viên trả lời khung giờ có người đến nhà.</p>'
    . '<p><a class="btn" href="tel:0975399319">Gọi 0975.399.319</a></p></div>'
    . '<figure class="panel">' . tl_img('doi-ngu-3.jpg', 'Đội ngũ Thanh Lan', 'cover') . '</figure></div></section>';

function tl_save_page($slug, $html, $title) {
    $page = get_page_by_path($slug);
    $data = array(
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_title' => $title,
        'post_name' => $slug,
        'post_content' => $html,
    );
    if ($page) {
        $data['ID'] = $page->ID;
        $id = wp_update_post($data, true);
    } else {
        $id = wp_insert_post($data, true);
    }
    return is_wp_error($id) ? $id : (int) $id;
}

$front_id = (int) get_option('page_on_front');
if ($front_id) {
    wp_update_post(array('ID' => $front_id, 'post_content' => $pages['__front__']));
    $write('front ' . $front_id);
} else {
    $front_id = tl_save_page('trang-chu', $pages['__front__'], 'Trang chủ');
    update_option('show_on_front', 'page');
    update_option('page_on_front', $front_id);
    $write('front created ' . $front_id);
}

$titles = array(
    'gioi-thieu' => 'Giới thiệu',
    'dich-vu' => 'Dịch Vụ',
    'tam-be' => 'Tắm bé sơ sinh',
    'cham-soc-me-be' => 'Chăm sóc mẹ bé',
    'o-cu-tai-nha' => 'Ở cữ tại nhà',
    'cham-soc-bau' => 'Chăm sóc bầu tại nhà',
    'thong-tac-tia-sua' => 'Thông tắc tia sữa',
    'giam-eo' => 'Giảm eo sau sinh',
    'feedback' => 'Feedback',
    'kien-thuc' => 'Kiến thức',
    'tu-van' => 'Nhận tư vấn',
);
foreach ($titles as $slug => $title) {
    $id = tl_save_page($slug, $pages[$slug], $title);
    if (is_wp_error($id)) {
        $write('page error ' . $slug . ' ' . $id->get_error_message());
    } else {
        $write('page ' . $slug . ' ' . $id);
    }
}

$posts = array(
    array(
        'slug' => 'tam-massage-va-la-trau-cho-be',
        'title' => 'Tắm, massage và lá trầu cho bé sơ sinh',
        'cat' => 'huong-dan-cham-soc-be',
        'thumb' => 'matxa-cho-be.jpg',
        'excerpt' => 'Thứ tự một buổi chăm bé tại nhà: massage, tắm, rồi lá trầu nếu mẹ muốn dùng.',
        'html' => '<p>Buổi chăm bé tại Thanh Lan đi theo một vòng: nhìn da và rốn, massage, tắm, giữ ấm. Lá trầu chỉ thêm khi mẹ đang dùng và bé không dị ứng với lần trước.</p>'
            . '<p>Massage bắt đầu khi bé tỉnh, không quấy ngay sau cữ bú. Tay kỹ thuật viên có dầu mỏng hoặc chỉ nước ấm. Lòng bàn tay đi từ vai xuống tay, từ đùi xuống bàn chân, rồi vòng bụng theo chiều kim đồng hồ. Ảnh trong thư viện là bé nắm bóng hồng trong lúc được xoa ngực và bụng.</p>'
            . tl_fig('matxa-cho-be.jpg', 'Massage bụng cho bé', 'Massage bụng, bé nằm ngửa trên đệm.')
            . '<p>Nước tắm ấm, thử bằng khuỷu tay hoặc nhiệt kế, không đổ thẳng lên mặt. Mẹ đỡ đầu và cổ. Rốn chưa rụng thì lau khô, không ngâm. Ảnh chậu tắm và ảnh bế bé sau buổi là hai bước cuối: tắm xong, quấn khăn, bế thẳng người cho đến khi bé ngủ.</p>'
            . '<div class="shots">' . tl_fig('tam-cho-be.jpg', 'Bé trong chậu tắm', 'Tắm trong chậu.') . tl_fig('tam-be.jpg', 'Bế bé sau buổi tắm', 'Bế và quấn khăn sau tắm.') . tl_fig('tam-be-2.jpg', 'Bé nắm bóng', 'Bóng nhỏ trong lúc massage.') . '</div>'
            . '<p>Lá trầu trong ảnh được đặt lên bụng, trán hoặc quấn nhẹ quanh người bé. Lá rửa sạch, không để nước lá chảy vào mắt. Nếu da đỏ sau lần đặt lá, mẹ ngừng và lau lại bằng nước ấm. Đây là cách gia đình đang làm trong ảnh Thanh Lan lưu, không phải đơn thuốc.</p>'
            . '<div class="shots">' . tl_fig('ho-la-trau-2.jpg', 'Lá trầu trên bụng bé', 'Lá trên bụng.') . tl_fig('ho-trau.jpg', 'Bé và lá trầu', 'Lá trầu đặt quanh người bé.') . tl_fig('thu-gian.jpg', 'Xoa đầu bé', 'Xoa đầu khi bé đã bú và nằm yên.') . '</div>'
            . '<p>Mẹ xem thêm trang <a href="' . esc_url($u('tam-be/')) . '">Tắm bé sơ sinh</a> để đặt lịch. Giá đang ghi là 250.000đ một buổi.</p>',
    ),
    array(
        'slug' => 'nhip-ngay-me-o-cu',
        'title' => 'Nhịp một ngày của mẹ ở cữ',
        'cat' => 'huong-dan-me',
        'thumb' => 'cham-me-tai-nha-2.jpg',
        'excerpt' => 'Buổi sáng cho bé bú, một khoảng mẹ được nằm, và việc xông hoặc massage khi nhà có người.',
        'html' => '<p>Tuần đầu mẹ ít khi có một giờ trống đúng nghĩa. Nhịp Thanh Lan gợi ý là chia ngày thành những đoạn ngắn, không xếp một lịch dài.</p>'
            . '<p>Sáng, mẹ cho bú hoặc hút bên nào đang căng trước. Ăn một bữa ấm. Nếu có kỹ thuật viên, massage vai và lưng làm lúc bé vừa ngủ, như ảnh mẹ nằm sấp trên giường tại nhà. Buổi đó dừng nếu vết mổ đau hoặc mẹ chóng mặt.</p>'
            . tl_fig('cham-me-tai-nha-1.jpg', 'Massage lưng cho mẹ tại nhà', 'Massage lưng trong phòng mẹ.')
            . '<p>Lều xông là một khoảng ngắn, mẹ ngồi, cửa lều hé đủ để nói chuyện với người trong nhà. Mẹ bước ra khi mặt đỏ hoặc đầu nhẹ. Ảnh lều trong thư viện là buổi tại nhà, cạnh đồ dùng của gia đình.</p>'
            . tl_fig('cham-me-tai-nha-3.jpg', 'Mẹ trong lều xông', 'Lều xông tại nhà.')
            . '<p>Giữa ngày mẹ vẫn bế bé đi lại trong nhà, kể cả ra bếp lấy nước. Ảnh mẹ đứng trong bếp với bé trên tay là nhịp đó: việc nhà rút ngắn, không phải mẹ phải nằm một chỗ cả tháng. Tối, mẹ xem lại bên ngực nào cứng để xử lý trước cữ bú đêm.</p>'
            . tl_fig('cham-me-tai-nha-2.jpg', 'Mẹ bế bé trong bếp', 'Mẹ và bé trong bếp.')
            . '<p>Gói chăm mẹ bé trên website là 4.500.000đ cho 10 buổi. Ở cữ theo ngày thì báo giá khi mẹ nhắn số ngày cần người đến.</p>',
    ),
    array(
        'slug' => 'me-bau-nghi-nhung-tuan-cuoi',
        'title' => 'Mẹ bầu nghỉ thế nào những tuần cuối',
        'cat' => 'me-bau-can-biet',
        'thumb' => 'doi-ngu-6.jpg',
        'excerpt' => 'Nằm nghiêng, kê gối, đi lại nhẹ. Buổi massage không thay lần khám thai.',
        'html' => '<p>Những tuần cuối mẹ mệt vì nặng bụng và khó trở mình. Cách nghỉ Thanh Lan đang dùng là nằm nghiêng, một gối giữa hai đầu gối và một gối mỏng sau lưng. Tránh nằm ngửa lâu nếu chóng mặt.</p>'
            . '<p>Đi lại trong nhà, dừng khi có cơn gò. Massage chỉ ở thế ngồi hoặc nằm nghiêng, trên vai, lưng và chân, không đè bụng. Mẹ uống nước trước buổi và nói ngay khi tức bụng khác với những lần trước.</p>'
            . '<p>Ra nước ối, ra máu, thai máy ít hẳn hoặc đau đầu kèm mắt mờ thì mẹ đi khám. Buổi spa không thay lần khám đó.</p>'
            . '<p>Thư viện ảnh hiện có đội ngũ và các buổi sau sinh, chưa có ảnh mẹ bầu. Trang dịch vụ vẫn nhận đặt lịch, giá báo khi mẹ nhắn tuần thai và quận đang ở.</p>'
            . tl_fig('doi-ngu-6.jpg', 'Đội ngũ Thanh Lan', 'Đội ngũ phụ trách buổi đến nhà.')
            . '<p><a href="' . esc_url($u('cham-soc-bau/')) . '">Xem trang Chăm sóc bầu tại nhà</a></p>',
    ),
    array(
        'slug' => 'ngay-dau-sau-sinh-va-day-thang',
        'title' => 'Ngày đầu sau sinh và mốc đầy tháng',
        'cat' => 'me-sau-sinh-can-biet',
        'thumb' => 'day-thang.jpg',
        'excerpt' => 'Việc mẹ làm khi ngực căng trong tuần đầu, và lễ đầy tháng là mốc cuối tháng đó.',
        'html' => '<p>Ngày đầu sau sinh mẹ vừa đau người vừa lo cữ bú. Nếu một bên ngực cứng, mẹ cho bé ngậm bên đó trước, cằm bé chạm ngực, môi dưới chờm ra ngoài. Không kéo đầu bé vào quầng vú. Kỹ thuật viên thông tắc chỉ dùng khi mẹ đã thử bú hoặc hút mà cục cứng không mềm, giá đang ghi 450.000đ một buổi, và không nặn đến bầm.</p>'
            . '<p>Sốt, vết đỏ trên ngực hoặc mệt nhiều hơn sau mỗi giờ thì gọi cơ sở y tế, không chờ hết một buổi spa.</p>'
            . '<p>Hết tháng đầu, nhiều gia đình làm lễ đầy tháng tại nhà hoặc tại bệnh viện. Ảnh Thanh Lan lưu là mâm cỗ, bé mặc váy hồng, bánh có tên spa trong bệnh viện Xuyên Á, và kỹ thuật viên đứng cùng bố mẹ. Đó là dấu mốc mẹ đã đi qua tháng ở cữ, không phải dịch vụ thay cho việc khám sau sinh.</p>'
            . '<div class="shots">'
            . tl_fig('day-thang.jpg', 'Mâm cỗ đầy tháng', 'Mâm cỗ đầy tháng.')
            . tl_fig('day-thang-be-1.jpg', 'Bé trong ngày đầy tháng', 'Bé trong ngày lễ.')
            . tl_fig('1795509085758_1798074000091021887_1703208304016546408_b96a03220d7c5d5dee560fe832565c91.jpg', 'Bánh đầy tháng', 'Bánh đầy tháng.')
            . '</div>'
            . '<p><a href="' . esc_url($u('o-cu-tai-nha/')) . '">Ở cữ tại nhà</a> · <a href="' . esc_url($u('thong-tac-tia-sua/')) . '">Thông tắc tia sữa</a></p>',
    ),
    array(
        'slug' => 'dau-an-doi-ngu-va-le-day-thang',
        'title' => 'Dấu ấn đội ngũ Thanh Lan',
        'cat' => 'dau-an-thanh-lan',
        'thumb' => 'doi-ngu-3.jpg',
        'excerpt' => 'Năm 2018 chị Tô Lan dựng Thanh Lan từ đôi bàn tay và sự tử tế. Hôm nay thương hiệu đồng hành cùng hơn 20.000 mẹ và bé.',
        'html' => '<p>Năm 2018, chị Tô Lan bắt đầu Thanh Lan Spa từ con số không: chưa có vốn lớn, chưa có mối quan hệ, chưa có kinh nghiệm thương trường. Tài sản lúc ấy là đôi bàn tay tận tụy và một tấm lòng chân thành. Chị tự chăm từng sản phụ, tự học và sửa từ những buổi làm thật.</p>'
            . '<p>Thanh Lan không lớn bằng một chiến dịch rầm rộ. Thương hiệu lớn lên từ sự tin của khách hàng, và đã trở thành người bạn đồng hành của hơn 20.000 em bé và bà mẹ, có mặt tại 4 chi nhánh Bệnh viện Đa khoa Xuyên Á.</p>'
            . '<p>Tên quốc tế là THANH LAN MOM BABY CARE, tên thương mại là Mẹ và Bé Thanh Lan. Công ty TNHH TM&amp;DV Thanh Lan, mã số thuế 0319180928, đăng ký ngày 25/9/2025. Câu chuyện đầy đủ của chị Tô Lan nằm ở trang <a href="' . esc_url($u('gioi-thieu/')) . '">Giới thiệu</a>.</p>'
            . '<p>Ảnh đội ngũ trong thư viện gồm lễ tốt nghiệp kỹ thuật viên, ngày khai trương, và ảnh đứng trước biển hiệu Spa chăm sóc và thư giãn Thanh Lan.</p>'
            . '<div class="shots">'
            . tl_fig('doi-ngu-1.jpg', 'Lễ tốt nghiệp kỹ thuật viên', 'Lễ tốt nghiệp.')
            . tl_fig('doi-ngu-4.jpg', 'Khai trương Thanh Lan', 'Ngày khai trương.')
            . tl_fig('doi-ngu-3.jpg', 'Đội ngũ trước biển hiệu', 'Trước biển hiệu Thanh Lan.')
            . '</div>'
            . '<p>Một phần việc hiện ra trong ảnh gia đình: kỹ thuật viên bế bé giữa mâm hoa quả và bánh trong lễ đầy tháng. Đó là cách đội ngũ có mặt sau tháng ở cữ, cạnh bố mẹ, không chỉ trong phòng massage.</p>'
            . tl_fig('day-thang-be-2.jpg', 'Kỹ thuật viên và gia đình trong lễ đầy tháng', 'Đầy tháng cùng gia đình.')
            . '<p>Địa chỉ công ty: Số 77, đường 537, Ấp Bàu Trăn, Xã Nhuận Đức, TPHCM. Hotline Ms Tô Lan: 0975.399.319.</p>',
    ),
);

foreach ($posts as $post) {
    $term = term_exists($post['cat'], 'category');
    if (!$term) {
        $created = wp_insert_term($post['cat'], 'category', array('slug' => $post['cat']));
        $term_id = is_wp_error($created) ? 0 : (int) $created['term_id'];
    } else {
        $term_id = (int) (is_array($term) ? $term['term_id'] : $term);
    }
    $existing = get_page_by_path($post['slug'], OBJECT, 'post');
    $data = array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'post_title' => $post['title'],
        'post_name' => $post['slug'],
        'post_content' => $post['html'],
        'post_excerpt' => $post['excerpt'],
        'post_category' => $term_id ? array($term_id) : array(),
    );
    if ($existing) {
        $data['ID'] = $existing->ID;
        $id = wp_update_post($data, true);
    } else {
        $id = wp_insert_post($data, true);
    }
    if (is_wp_error($id)) {
        $write('post error ' . $post['slug'] . ' ' . $id->get_error_message());
        continue;
    }
    $thumb = tl_id($post['thumb']);
    if ($thumb) {
        set_post_thumbnail($id, $thumb);
    }
    $write('post ' . $post['slug'] . ' ' . $id);
}

$write('done');
@unlink(__FILE__);
