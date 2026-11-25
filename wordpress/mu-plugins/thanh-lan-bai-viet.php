<?php
/**
 * Plugin Name: Thanh Lan Bài viết
 * Description: Liệt kê bài Kiến thức theo chuyên mục, ảnh lấy từ Thư viện.
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

add_shortcode('thanh_lan_bai_viet', function () {
    $order = array(
        'huong-dan-cham-soc-be' => 'Hướng dẫn chăm sóc bé',
        'huong-dan-me' => 'Hướng dẫn mẹ',
        'me-bau-can-biet' => 'Mẹ bầu cần biết',
        'me-sau-sinh-can-biet' => 'Mẹ sau sinh cần biết',
        'dau-an-thanh-lan' => 'Dấu ấn Thanh Lan Spa',
    );
    ob_start();
    echo '<div class="grid three">';
    foreach ($order as $slug => $label) {
        $term = get_term_by('slug', $slug, 'category');
        $link = ($term && !is_wp_error($term)) ? get_term_link($term) : home_url('/category/' . $slug . '/');
        $query = new WP_Query(array(
            'post_type' => 'post',
            'posts_per_page' => 1,
            'category_name' => $slug,
        ));
        echo '<article class="card">';
        if ($query->have_posts()) {
            $query->the_post();
            if (has_post_thumbnail()) {
                the_post_thumbnail('medium_large');
            }
            echo '<div class="pad"><h3><a href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . '</a></h3>';
            echo '<p>' . esc_html($label) . '</p>';
            echo '<a class="more" href="' . esc_url($link) . '">Mở chuyên mục</a></div>';
            wp_reset_postdata();
        } else {
            echo '<div class="pad"><h3>' . esc_html($label) . '</h3><p>Chưa có bài.</p>';
            echo '<a class="more" href="' . esc_url($link) . '">Mở chuyên mục</a></div>';
        }
        echo '</article>';
    }
    echo '</div>';
    return ob_get_clean();
});
