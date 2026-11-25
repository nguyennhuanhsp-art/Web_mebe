<?php
/**
 * Plugin Name: Thanh Lan Báo cáo cửa hàng
 * Description: Giá vốn, lợi nhuận, số lượng bán, lịch sử đơn và email báo cáo mỗi thứ Hai.
 * Author: Thanh Lan Spa
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('woocommerce_product_options_pricing', function () {
    woocommerce_wp_text_input(array(
        'id' => '_thanh_lan_cost',
        'label' => 'Giá vốn (đ)',
        'type' => 'number',
        'custom_attributes' => array('step' => '1', 'min' => '0'),
        'desc_tip' => true,
        'description' => 'Lợi nhuận = doanh thu trừ giá vốn nhân số lượng. Để trống thì khoản đó chưa tính vào giá vốn.',
    ));
});

add_action('woocommerce_admin_process_product_object', function ($product) {
    if (isset($_POST['_thanh_lan_cost'])) {
        $product->update_meta_data('_thanh_lan_cost', wc_clean(wp_unslash($_POST['_thanh_lan_cost'])));
    }
});

function thanh_lan_money($amount) {
    return number_format((float) $amount, 0, ',', '.') . 'đ';
}

function thanh_lan_order_stats($after_gmt = null) {
    if (!function_exists('wc_get_orders')) {
        return array('revenue' => 0, 'qty' => 0, 'cost' => 0, 'profit' => 0, 'orders' => array(), 'missing_cost' => 0);
    }
    $args = array(
        'status' => array('wc-completed', 'wc-processing'),
        'limit' => -1,
        'orderby' => 'date',
        'order' => 'DESC',
        'return' => 'objects',
    );
    if ($after_gmt) {
        $args['date_created'] = '>=' . $after_gmt;
    }
    $orders = wc_get_orders($args);
    $revenue = 0;
    $qty = 0;
    $cost = 0;
    $missing = 0;
    foreach ($orders as $order) {
        $revenue += (float) $order->get_total();
        foreach ($order->get_items() as $item) {
            $q = (int) $item->get_quantity();
            $qty += $q;
            $product = $item->get_product();
            $unit = $product ? $product->get_meta('_thanh_lan_cost') : '';
            if ($unit === '' || $unit === null) {
                $missing += $q;
                continue;
            }
            $cost += (float) $unit * $q;
        }
    }
    return array(
        'revenue' => $revenue,
        'qty' => $qty,
        'cost' => $cost,
        'profit' => $revenue - $cost,
        'orders' => $orders,
        'missing_cost' => $missing,
    );
}

function thanh_lan_history_url() {
    return admin_url('admin.php?page=wc-orders');
}

function thanh_lan_render_stats($stats, $heading) {
    echo '<h2>' . esc_html($heading) . '</h2>';
    echo '<table class="widefat striped" style="max-width:720px"><tbody>';
    echo '<tr><td>Doanh thu</td><td><strong>' . esc_html(thanh_lan_money($stats['revenue'])) . '</strong></td></tr>';
    echo '<tr><td>Giá vốn đã nhập</td><td>' . esc_html(thanh_lan_money($stats['cost'])) . '</td></tr>';
    echo '<tr><td>Lợi nhuận</td><td><strong>' . esc_html(thanh_lan_money($stats['profit'])) . '</strong></td></tr>';
    echo '<tr><td>Số lượng đã bán</td><td><strong>' . esc_html((string) $stats['qty']) . '</strong></td></tr>';
    echo '<tr><td>Số đơn</td><td>' . esc_html((string) count($stats['orders'])) . '</td></tr>';
    echo '</tbody></table>';
    if ($stats['missing_cost'] > 0) {
        echo '<p>Có ' . esc_html((string) $stats['missing_cost']) . ' sản phẩm trong đơn chưa có giá vốn, nên phần lợi nhuận chưa trừ các món đó. Vào Sản phẩm, mục Giá vốn, để nhập.</p>';
    }
}

add_action('wp_dashboard_setup', function () {
    wp_add_dashboard_widget('thanh_lan_profit', 'Thanh Lan — lợi nhuận và số lượng bán', function () {
        $week = gmdate('Y-m-d', time() - 7 * DAY_IN_SECONDS);
        $stats = thanh_lan_order_stats($week);
        thanh_lan_render_stats($stats, '7 ngày gần nhất');
        echo '<p><a class="button button-primary" href="' . esc_url(admin_url('admin.php?page=thanh-lan-bao-cao')) . '">Mở báo cáo</a> ';
        echo '<a class="button" href="' . esc_url(thanh_lan_history_url()) . '">Lịch sử bán hàng</a></p>';
    });
});

add_action('admin_menu', function () {
    $cap = current_user_can('manage_woocommerce') ? 'manage_woocommerce' : 'manage_options';
    add_menu_page('Báo cáo Thanh Lan', 'Báo cáo cửa hàng', $cap, 'thanh-lan-bao-cao', 'thanh_lan_report_page', 'dashicons-chart-line', 56);
});

function thanh_lan_report_page() {
    if (!current_user_can('manage_woocommerce') && !current_user_can('manage_options')) {
        return;
    }
    if (isset($_POST['thanh_lan_send_now']) && check_admin_referer('thanh_lan_send_now')) {
        $sent = thanh_lan_send_weekly_report();
        echo $sent
            ? '<div class="updated"><p>Đã gửi email báo cáo tới ' . esc_html(get_option('admin_email')) . '.</p></div>'
            : '<div class="error"><p>Chưa gửi được email. Báo cáo bên dưới vẫn xem được.</p></div>';
    }
    $week = gmdate('Y-m-d', time() - 7 * DAY_IN_SECONDS);
    echo '<div class="wrap">';
    echo '<h1>Báo cáo cửa hàng Thanh Lan Spa</h1>';
    echo '<p>Sản phẩm đăng, sửa và xóa tại <a href="' . esc_url(admin_url('edit.php?post_type=product')) . '">Sản phẩm</a>. Đơn hàng nằm ở <a href="' . esc_url(thanh_lan_history_url()) . '">lịch sử bán hàng</a>.</p>';
    thanh_lan_render_stats(thanh_lan_order_stats($week), '7 ngày gần nhất');
    thanh_lan_render_stats(thanh_lan_order_stats(), 'Từ trước tới nay');
    echo '<h2>Lịch sử bán hàng</h2>';
    $all = thanh_lan_order_stats();
    if (!$all['orders']) {
        echo '<p>Chưa có đơn hoàn tất hoặc đang xử lý.</p>';
    } else {
        echo '<table class="widefat striped"><thead><tr><th>Đơn</th><th>Ngày</th><th>Khách</th><th>Số lượng</th><th>Doanh thu</th><th></th></tr></thead><tbody>';
        foreach ($all['orders'] as $order) {
            $q = 0;
            foreach ($order->get_items() as $item) {
                $q += (int) $item->get_quantity();
            }
            $date = $order->get_date_created();
            echo '<tr>';
            echo '<td>#' . esc_html($order->get_order_number()) . '</td>';
            echo '<td>' . esc_html($date ? $date->date_i18n('d/m/Y H:i') : '') . '</td>';
            echo '<td>' . esc_html($order->get_formatted_billing_full_name()) . '</td>';
            echo '<td>' . esc_html((string) $q) . '</td>';
            echo '<td>' . esc_html(thanh_lan_money($order->get_total())) . '</td>';
            echo '<td><a href="' . esc_url($order->get_edit_order_url()) . '">Xem đơn</a></td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    }
    echo '<h2>Email mỗi tuần</h2>';
    echo '<p>Mỗi thứ Hai lúc 08:00 (giờ Việt Nam) hệ thống gửi báo cáo 7 ngày tới ' . esc_html(get_option('admin_email')) . '.</p>';
    echo '<form method="post">';
    wp_nonce_field('thanh_lan_send_now');
    echo '<button class="button" name="thanh_lan_send_now" value="1">Gửi email báo cáo ngay</button>';
    echo '</form></div>';
}

function thanh_lan_send_weekly_report() {
    $week = gmdate('Y-m-d', time() - 7 * DAY_IN_SECONDS);
    $stats = thanh_lan_order_stats($week);
    $to = get_option('admin_email');
    $subject = 'Thanh Lan Spa — báo cáo cửa hàng tuần ' . wp_date('d/m/Y');
    $lines = array(
        'Báo cáo 7 ngày gần nhất của cửa hàng Thanh Lan Spa.',
        'Doanh thu: ' . thanh_lan_money($stats['revenue']),
        'Giá vốn đã nhập: ' . thanh_lan_money($stats['cost']),
        'Lợi nhuận: ' . thanh_lan_money($stats['profit']),
        'Số lượng đã bán: ' . $stats['qty'],
        'Số đơn: ' . count($stats['orders']),
        '',
        'Đơn trong tuần:',
    );
    if (!$stats['orders']) {
        $lines[] = 'Chưa có đơn hoàn tất hoặc đang xử lý.';
    }
    foreach ($stats['orders'] as $order) {
        $q = 0;
        foreach ($order->get_items() as $item) {
            $q += (int) $item->get_quantity();
        }
        $date = $order->get_date_created();
        $lines[] = sprintf(
            '#%s · %s · %s · %d món · %s',
            $order->get_order_number(),
            $date ? $date->date_i18n('d/m/Y') : '',
            $order->get_formatted_billing_full_name(),
            $q,
            thanh_lan_money($order->get_total())
        );
    }
    $lines[] = '';
    $lines[] = 'Xem đầy đủ trong quản trị: ' . admin_url('admin.php?page=thanh-lan-bao-cao');
    $lines[] = 'Lịch sử bán hàng: ' . thanh_lan_history_url();
    return wp_mail($to, $subject, implode("\n", $lines));
}

add_action('thanh_lan_weekly_report', 'thanh_lan_send_weekly_report');

add_action('init', function () {
    if (wp_next_scheduled('thanh_lan_weekly_report')) {
        return;
    }
    $next = new DateTime('next monday 08:00', wp_timezone());
    wp_schedule_event($next->getTimestamp(), 'weekly', 'thanh_lan_weekly_report');
});
