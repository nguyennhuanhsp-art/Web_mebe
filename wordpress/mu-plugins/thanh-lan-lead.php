<?php
/**
 * Plugin Name: Thanh Lan Lead tư vấn
 * Description: Form nhận tư vấn, lưu lead, gửi email, và gửi Telegram hoặc Zalo OA khi đã cấu hình.
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

const THANH_LAN_LEAD_EMAIL = 'thanhlanspa238@gmail.com';

add_action('init', function () {
    register_post_type('tl_lead', array(
        'labels' => array(
            'name' => 'Lead tư vấn',
            'singular_name' => 'Lead',
            'menu_name' => 'Lead tư vấn',
            'add_new_item' => 'Thêm lead',
            'edit_item' => 'Xem lead',
            'search_items' => 'Tìm lead',
            'not_found' => 'Chưa có lead',
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-email',
        'supports' => array('title'),
        'capability_type' => 'post',
        'map_meta_cap' => true,
    ));
});

add_shortcode('thanh_lan_tu_van', function () {
    ob_start();
    ?>
    <section class="section band"><div class="wrap band-box">
      <div>
        <p class="kicker">Nhận tư vấn</p>
        <h2 style="text-align:left">Để lại số, Tô Lan gọi lại</h2>
        <p>Chị điền tên, email và số Zalo. Ngay khi gửi, hộp thư của chị nhận một thư cảm ơn.</p>
        <p>Hotline: <a href="tel:0975399319"><strong>0975.399.319</strong></a></p>
      </div>
      <form id="leadForm">
        <div class="field hp" aria-hidden="true"><label for="leadWebsite">Website</label><input id="leadWebsite" name="website" tabindex="-1" autocomplete="off" /></div>
        <div class="field"><label for="leadName">Họ và tên</label><input id="leadName" name="name" required /></div>
        <div class="field"><label for="leadPhone">Số điện thoại hoặc Zalo</label><input id="leadPhone" name="phone" required inputmode="tel" /><div class="error" id="leadPhoneError"></div></div>
        <div class="field"><label for="leadEmail">Email nhận thư cảm ơn</label><input id="leadEmail" name="email" type="email" required autocomplete="email" /><div class="error" id="leadEmailError"></div></div>
        <div class="field"><label for="leadStage">Giai đoạn</label>
          <select id="leadStage" name="stage">
            <option>Mẹ bầu</option>
            <option>Mẹ sau sinh</option>
            <option>Bé sơ sinh</option>
            <option>Muốn hỏi gói</option>
          </select>
        </div>
        <div class="field"><label for="leadService">Dịch vụ quan tâm</label>
          <select id="leadService" name="service">
            <option>Chưa chọn</option>
            <option>Tắm và massage bé</option>
            <option>Chăm sóc mẹ sau sinh</option>
            <option>Combo mẹ và bé</option>
            <option>Chăm sóc bầu tại nhà</option>
            <option>Thông tắc tia sữa</option>
            <option>Giảm eo sau sinh</option>
          </select>
        </div>
        <div class="field"><label for="leadDistrict">Quận đang ở</label><input id="leadDistrict" name="district" placeholder="Ví dụ Củ Chi" /></div>
        <div class="field"><label for="leadMessage">Lời nhắn</label><textarea id="leadMessage" name="message"></textarea></div>
        <button class="btn block" type="submit">Gửi để được tư vấn</button>
        <p class="note" id="leadNote"></p>
      </form>
    </div></section>
    <?php
    return ob_get_clean();
});

function thanh_lan_lead_text($data) {
    $lines = array(
        'Có lead mới trên Thanh Lan Spa.',
        'Tên: ' . $data['name'],
        'Email khách: ' . $data['customer_email'],
        'Điện thoại / Zalo: ' . $data['phone'],
        'Giai đoạn: ' . $data['stage'],
        'Dịch vụ: ' . $data['service'],
        'Quận: ' . $data['district'],
        'Ngày mong muốn: ' . $data['date'],
        'Lời nhắn: ' . $data['message'],
        'Nguồn: ' . $data['source'],
        'Lúc: ' . wp_date('H:i d/m/Y'),
    );
    return implode("\n", $lines);
}

add_action('phpmailer_init', function ($phpmailer) {
    $host = trim((string) get_option('thanh_lan_smtp_host', ''));
    if ($host === '') {
        return;
    }
    $phpmailer->isSMTP();
    $phpmailer->Host = $host;
    $phpmailer->Port = (int) get_option('thanh_lan_smtp_port', 587);
    $phpmailer->SMTPAuth = true;
    $phpmailer->Username = (string) get_option('thanh_lan_smtp_user', '');
    $phpmailer->Password = (string) get_option('thanh_lan_smtp_pass', '');
    $secure = (string) get_option('thanh_lan_smtp_secure', 'tls');
    if ($secure !== '') {
        $phpmailer->SMTPSecure = $secure;
    }
    $from = (string) get_option('thanh_lan_smtp_user', '');
    if ($from !== '') {
        $phpmailer->From = $from;
        $phpmailer->FromName = 'Thanh Lan Spa';
    }
});

function thanh_lan_lead_thanks($data) {
    $to = sanitize_email($data['customer_email']);
    if ($to === '' || !is_email($to)) {
        return 'Khách chưa để email';
    }
    $service = ($data['service'] !== '' && $data['service'] !== 'Chưa chọn') ? $data['service'] : '';
    $detail = 'Em đã ghi nhận số Zalo ' . $data['phone'];
    if ($service !== '') {
        $detail .= ', dịch vụ ' . $service;
    }
    $detail .= '.';
    $body = "Chào chị " . $data['name'] . ",\n\n"
        . "Cảm ơn chị đã đăng ký nhận tư vấn tại Thanh Lan Spa.\n\n"
        . $detail . "\n"
        . "Ms Tô Lan sẽ gọi hoặc nhắn Zalo để hẹn lịch phù hợp.\n"
        . "Nếu chị cần gấp, gọi 0975.399.319.\n\n"
        . "Thanh Lan Spa\n"
        . "Mẹ Đẹp Bé Khỏe Vui Vẻ Cả Nhà\n";
    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        'Reply-To: Thanh Lan Spa <' . THANH_LAN_LEAD_EMAIL . '>',
    );
    $sent = wp_mail($to, 'Thanh Lan Spa đã nhận đăng ký tư vấn của chị', $body, $headers);
    if ($sent) {
        return 'Đã gửi cảm ơn tới ' . $to;
    }
    return 'Thư cảm ơn chưa đi';
}

function thanh_lan_lead_mail($text) {
    $to = sanitize_email(get_option('thanh_lan_lead_email', THANH_LAN_LEAD_EMAIL));
    if (!$to) {
        $to = THANH_LAN_LEAD_EMAIL;
    }
    $subject = '[Thanh Lan] Lead tư vấn mới';
    $headers = array('Content-Type: text/plain; charset=UTF-8');
    $sent = wp_mail($to, $subject, $text, $headers);
    if ($sent) {
        return 'Đã gửi ' . $to;
    }
    return 'Mail chưa đi. Lead vẫn nằm trong quản trị.';
}

function thanh_lan_lead_telegram($text) {
    $token = trim((string) get_option('thanh_lan_telegram_token', ''));
    $chat = trim((string) get_option('thanh_lan_telegram_chat', ''));
    if ($token === '' || $chat === '') {
        return 'Chưa gắn bot';
    }
    $response = wp_remote_post('https://api.telegram.org/bot' . rawurlencode($token) . '/sendMessage', array(
        'timeout' => 12,
        'body' => array('chat_id' => $chat, 'text' => $text),
    ));
    if (is_wp_error($response)) {
        return 'Lỗi mạng Telegram';
    }
    $body = json_decode(wp_remote_retrieve_body($response), true);
    if (!empty($body['ok'])) {
        return 'Đã gửi Telegram';
    }
    $detail = isset($body['description']) ? $body['description'] : 'Telegram từ chối';
    return $detail;
}

function thanh_lan_lead_zalo($text) {
    $token = trim((string) get_option('thanh_lan_zalo_token', ''));
    $user = trim((string) get_option('thanh_lan_zalo_user', ''));
    if ($token === '' || $user === '') {
        return 'Chưa gắn Zalo OA';
    }
    $response = wp_remote_post('https://openapi.zalo.me/v3.0/oa/message/cs', array(
        'timeout' => 12,
        'headers' => array(
            'access_token' => $token,
            'Content-Type' => 'application/json',
        ),
        'body' => wp_json_encode(array(
            'recipient' => array('user_id' => $user),
            'message' => array('text' => $text),
        )),
    ));
    if (is_wp_error($response)) {
        return 'Lỗi mạng Zalo';
    }
    $body = json_decode(wp_remote_retrieve_body($response), true);
    if (isset($body['error']) && (int) $body['error'] === 0) {
        return 'Đã gửi Zalo OA';
    }
    $detail = isset($body['message']) ? $body['message'] : 'Zalo từ chối';
    return $detail;
}

function thanh_lan_lead_store($data) {
    $id = wp_insert_post(array(
        'post_type' => 'tl_lead',
        'post_status' => 'publish',
        'post_title' => $data['name'] . ' · ' . $data['phone'],
    ), true);
    if (is_wp_error($id)) {
        return $id;
    }
    foreach (array('phone', 'customer_email', 'stage', 'service', 'district', 'message', 'date', 'source') as $key) {
        update_post_meta($id, '_tl_' . $key, $data[$key]);
    }
    $text = thanh_lan_lead_text($data);
    $thanks = thanh_lan_lead_thanks($data);
    $mail = thanh_lan_lead_mail($text);
    $telegram = thanh_lan_lead_telegram($text);
    $zalo = thanh_lan_lead_zalo($text);
    update_post_meta($id, '_tl_thanks', $thanks);
    update_post_meta($id, '_tl_email', $mail);
    update_post_meta($id, '_tl_telegram', $telegram);
    update_post_meta($id, '_tl_zalo', $zalo);
    return array('id' => $id, 'thanks' => $thanks, 'email' => $mail, 'telegram' => $telegram, 'zalo' => $zalo);
}

function thanh_lan_lead_from_request() {
    $phone = isset($_POST['phone']) ? preg_replace('/\D+/', '', wp_unslash($_POST['phone'])) : '';
    if (strlen($phone) < 9 || strlen($phone) > 12) {
        return new WP_Error('phone', 'Nhập số từ 9 đến 12 chữ số.');
    }
    $name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
    if ($name === '') {
        return new WP_Error('name', 'Nhập họ và tên.');
    }
    $pick = function ($key, $fallback) {
        $value = isset($_POST[$key]) ? sanitize_text_field(wp_unslash($_POST[$key])) : '';
        return $value === '' ? $fallback : $value;
    };
    $source = $pick('source', 'Form tư vấn');
    $customer_email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    if ($source === 'Form tư vấn' && !is_email($customer_email)) {
        return new WP_Error('email', 'Nhập email để nhận thư cảm ơn.');
    }
    if ($customer_email !== '' && !is_email($customer_email)) {
        return new WP_Error('email', 'Email chưa đúng.');
    }
    return array(
        'name' => $name,
        'phone' => $phone,
        'customer_email' => $customer_email,
        'stage' => $pick('stage', 'Chưa chọn'),
        'service' => $pick('service', 'Chưa chọn'),
        'district' => $pick('district', 'Chưa ghi'),
        'date' => $pick('date', 'Chưa chọn'),
        'message' => isset($_POST['message']) ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '',
        'source' => $source,
    );
}

function thanh_lan_lead_ajax() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'thanh_lan_lead')) {
        wp_send_json(array('ok' => false, 'message' => 'Trang đã cũ. Chị tải lại rồi gửi tiếp.'));
    }
    $trap = isset($_POST['website']) ? trim(wp_unslash($_POST['website'])) : '';
    if ($trap !== '') {
        wp_send_json(array('ok' => true, 'message' => 'Đã nhận. Thanh Lan sẽ gọi hoặc nhắn Zalo.'));
    }
    $ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : 'na';
    $key = 'tl_lead_' . md5($ip);
    $count = (int) get_transient($key);
    if ($count >= 8) {
        wp_send_json(array('ok' => false, 'message' => 'Chị gửi hơi nhiều. Gọi 0975.399.319 giúp em.'));
    }
    $data = thanh_lan_lead_from_request();
    if (is_wp_error($data)) {
        wp_send_json(array('ok' => false, 'message' => $data->get_error_message()));
    }
    $saved = thanh_lan_lead_store($data);
    if (is_wp_error($saved)) {
        wp_send_json(array('ok' => false, 'message' => 'Chưa lưu được. Chị gọi 0975.399.319 giúp em.'));
    }
    set_transient($key, $count + 1, HOUR_IN_SECONDS);
    if (strpos($saved['thanks'], 'Đã gửi cảm ơn') === 0) {
        $message = 'Đã nhận đăng ký. Thư cảm ơn đã gửi tới ' . $data['customer_email'] . '.';
    } elseif ($data['customer_email'] !== '') {
        $message = 'Đã nhận đăng ký. Thư cảm ơn chưa gửi được lúc này. Thanh Lan sẽ gọi số ' . $data['phone'] . '.';
    } else {
        $message = 'Đã nhận. Thanh Lan sẽ gọi hoặc nhắn Zalo số ' . $data['phone'] . '.';
    }
    wp_send_json(array('ok' => true, 'message' => $message));
}
add_action('wp_ajax_thanh_lan_lead', 'thanh_lan_lead_ajax');
add_action('wp_ajax_nopriv_thanh_lan_lead', 'thanh_lan_lead_ajax');

add_filter('manage_tl_lead_posts_columns', function ($columns) {
    return array(
        'title' => 'Khách',
        'phone' => 'Điện thoại',
        'service' => 'Dịch vụ',
        'mail' => 'Email',
        'chat' => 'Telegram / Zalo',
        'date' => 'Lúc nhận',
    );
});

add_action('manage_tl_lead_posts_custom_column', function ($column, $post_id) {
    if ($column === 'phone') {
        echo esc_html(get_post_meta($post_id, '_tl_phone', true));
    } elseif ($column === 'service') {
        echo esc_html(get_post_meta($post_id, '_tl_service', true) . ' · ' . get_post_meta($post_id, '_tl_stage', true));
    } elseif ($column === 'mail') {
        echo esc_html(get_post_meta($post_id, '_tl_thanks', true) . ' · ' . get_post_meta($post_id, '_tl_email', true));
    } elseif ($column === 'chat') {
        echo esc_html(get_post_meta($post_id, '_tl_telegram', true) . ' / ' . get_post_meta($post_id, '_tl_zalo', true));
    }
}, 10, 2);

add_action('admin_menu', function () {
    add_submenu_page(
        'edit.php?post_type=tl_lead',
        'Kênh báo lead',
        'Kênh báo lead',
        'manage_options',
        'thanh-lan-lead-channels',
        'thanh_lan_lead_settings_page'
    );
});

function thanh_lan_lead_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    $notice = '';
    if (isset($_POST['thanh_lan_lead_settings']) && check_admin_referer('thanh_lan_lead_settings')) {
        update_option('thanh_lan_lead_email', sanitize_email(wp_unslash($_POST['lead_email'] ?? '')));
        update_option('thanh_lan_telegram_token', trim(sanitize_text_field(wp_unslash($_POST['telegram_token'] ?? ''))));
        update_option('thanh_lan_telegram_chat', trim(sanitize_text_field(wp_unslash($_POST['telegram_chat'] ?? ''))));
        update_option('thanh_lan_zalo_token', trim(sanitize_text_field(wp_unslash($_POST['zalo_token'] ?? ''))));
        update_option('thanh_lan_zalo_user', trim(sanitize_text_field(wp_unslash($_POST['zalo_user'] ?? ''))));
        update_option('thanh_lan_smtp_host', trim(sanitize_text_field(wp_unslash($_POST['smtp_host'] ?? ''))));
        update_option('thanh_lan_smtp_port', (int) ($_POST['smtp_port'] ?? 587));
        update_option('thanh_lan_smtp_user', trim(sanitize_email(wp_unslash($_POST['smtp_user'] ?? ''))));
        $smtp_pass = (string) wp_unslash($_POST['smtp_pass'] ?? '');
        if ($smtp_pass !== '') {
            update_option('thanh_lan_smtp_pass', $smtp_pass);
        }
        update_option('thanh_lan_smtp_secure', trim(sanitize_text_field(wp_unslash($_POST['smtp_secure'] ?? 'tls'))));
        $notice = 'Đã lưu.';
        if (isset($_POST['send_test'])) {
            $sample = array(
                'name' => 'Tin thử',
                'phone' => '0975399319',
                'customer_email' => THANH_LAN_LEAD_EMAIL,
                'stage' => 'Thử kênh',
                'service' => 'Không phải khách',
                'district' => 'Quản trị',
                'date' => 'Không',
                'message' => 'Tin kiểm tra kênh báo lead.',
                'source' => 'Nút gửi thử',
            );
            $text = thanh_lan_lead_text($sample);
            $notice = 'Email: ' . thanh_lan_lead_mail($text)
                . '. Telegram: ' . thanh_lan_lead_telegram($text)
                . '. Zalo: ' . thanh_lan_lead_zalo($text) . '.';
        }
    }
    $email = get_option('thanh_lan_lead_email', THANH_LAN_LEAD_EMAIL);
    ?>
    <div class="wrap">
      <h1>Kênh báo lead</h1>
      <?php if ($notice) : ?><div class="notice notice-success"><p><?php echo esc_html($notice); ?></p></div><?php endif; ?>
      <p>Mỗi lead luôn được lưu ở menu Lead tư vấn và gửi email. Telegram và Zalo chỉ chạy khi đã điền mã bên dưới.</p>
      <form method="post">
        <?php wp_nonce_field('thanh_lan_lead_settings'); ?>
        <input type="hidden" name="thanh_lan_lead_settings" value="1" />
        <table class="form-table">
          <tr><th>Email nhận lead</th><td><input name="lead_email" type="email" class="regular-text" value="<?php echo esc_attr($email); ?>" /></td></tr>
          <tr><th>SMTP host</th><td><input name="smtp_host" type="text" class="regular-text" value="<?php echo esc_attr(get_option('thanh_lan_smtp_host', '')); ?>" placeholder="smtp.gmail.com" /><p class="description">Host này chưa có chương trình gửi mail. Điền SMTP thì lead mới tới hộp thư. Gmail dùng smtp.gmail.com, cổng 587, mã bảo mật ứng dụng.</p></td></tr>
          <tr><th>SMTP cổng</th><td><input name="smtp_port" type="number" value="<?php echo esc_attr(get_option('thanh_lan_smtp_port', 587)); ?>" /></td></tr>
          <tr><th>SMTP tài khoản</th><td><input name="smtp_user" type="email" class="regular-text" value="<?php echo esc_attr(get_option('thanh_lan_smtp_user', '')); ?>" /></td></tr>
          <tr><th>SMTP mật khẩu</th><td><input name="smtp_pass" type="password" class="regular-text" value="" autocomplete="new-password" placeholder="Để trống nếu giữ mật khẩu đã lưu" /></td></tr>
          <tr><th>SMTP mã hóa</th><td><input name="smtp_secure" type="text" value="<?php echo esc_attr(get_option('thanh_lan_smtp_secure', 'tls')); ?>" /></td></tr>
          <tr><th>Telegram bot token</th><td><input name="telegram_token" type="password" class="regular-text" value="<?php echo esc_attr(get_option('thanh_lan_telegram_token', '')); ?>" autocomplete="off" /><p class="description">Trong Telegram, nhắn @BotFather, gõ /newbot, rồi dán token vào đây.</p></td></tr>
          <tr><th>Telegram chat id</th><td><input name="telegram_chat" type="text" class="regular-text" value="<?php echo esc_attr(get_option('thanh_lan_telegram_chat', '')); ?>" /><p class="description">Nhắn một câu cho bot, mở <code>api.telegram.org/bot&lt;token&gt;/getUpdates</code> và lấy số <code>chat.id</code>.</p></td></tr>
          <tr><th>Zalo OA access token</th><td><input name="zalo_token" type="password" class="regular-text" value="<?php echo esc_attr(get_option('thanh_lan_zalo_token', '')); ?>" autocomplete="off" /><p class="description">Zalo cá nhân không nhận tin tự động. Cần Official Account. Token lấy trong Zalo for Developers.</p></td></tr>
          <tr><th>Zalo user id</th><td><input name="zalo_user" type="text" class="regular-text" value="<?php echo esc_attr(get_option('thanh_lan_zalo_user', '')); ?>" /><p class="description">Là user id của người đã nhắn OA trong 7 ngày gần đây, không phải số 0975.399.319.</p></td></tr>
        </table>
        <p><button class="button button-primary" type="submit">Lưu</button> <button class="button" name="send_test" value="1" type="submit">Lưu và gửi tin thử</button></p>
      </form>
    </div>
    <?php
}
