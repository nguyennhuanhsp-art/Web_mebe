<?php
if (!defined('ABSPATH')) {
    exit;
}
$shop = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/cua-hang/');
$cat = function ($slug) {
    $term = get_term_by('slug', $slug, 'product_cat');
    if ($term && !is_wp_error($term)) {
        $link = get_term_link($term);
        if (!is_wp_error($link)) {
            return $link;
        }
    }
    return home_url('/product-category/' . $slug . '/');
};
$topic = function ($slug) {
    $term = get_term_by('slug', $slug, 'category');
    if ($term && !is_wp_error($term)) {
        $link = get_term_link($term);
        if (!is_wp_error($link)) {
            return $link;
        }
    }
    return home_url('/category/' . $slug . '/');
};
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
  <div class="topbar"><div class="wrap"><span>CÔNG TY TNHH TM&amp;DV THANH LAN</span><span>Ms Tô Lan · <a href="tel:0975399319">0975.399.319</a></span></div></div>
  <header class="site">
    <div class="wrap nav-row">
      <a class="brand" href="<?php echo esc_url(home_url('/')); ?>"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/logo-thanh-lan.jpg'); ?>" alt="Logo Thanh Lan Spa" /><div><strong>Thanh Lan Spa</strong><span>Mẹ và bé, trước và sau sinh</span></div></a>
      <button class="menu-btn" type="button" aria-expanded="false" aria-controls="menu">Menu</button>
      <ul class="menu" id="menu">
        <li><a href="<?php echo esc_url(home_url('/gioi-thieu/')); ?>">Giới thiệu</a></li>
        <li>
          <a href="<?php echo esc_url(home_url('/dich-vu/')); ?>">Dịch Vụ</a>
          <button class="sub-btn" type="button" aria-expanded="false" aria-label="Mở menu Dịch Vụ">+</button>
          <ul class="sub">
            <li><a href="<?php echo esc_url(home_url('/tam-be/')); ?>">Tắm bé sơ sinh</a></li>
            <li><a href="<?php echo esc_url(home_url('/cham-soc-me-be/')); ?>">Chăm sóc mẹ bé</a></li>
            <li><a href="<?php echo esc_url(home_url('/o-cu-tai-nha/')); ?>">Ở cữ tại nhà</a></li>
            <li><a href="<?php echo esc_url(home_url('/cham-soc-bau/')); ?>">Chăm sóc bầu tại nhà</a></li>
            <li><a href="<?php echo esc_url(home_url('/thong-tac-tia-sua/')); ?>">Thông tắc tia sữa</a></li>
            <li><a href="<?php echo esc_url(home_url('/giam-eo/')); ?>">Giảm eo sau sinh</a></li>
          </ul>
        </li>
        <li>
          <a href="<?php echo esc_url($shop); ?>">Cửa hàng</a>
          <button class="sub-btn" type="button" aria-expanded="false" aria-label="Mở menu Cửa hàng">+</button>
          <ul class="sub">
            <li><a href="<?php echo esc_url($cat('cham-soc-bau')); ?>">Chăm sóc bầu</a></li>
            <li><a href="<?php echo esc_url($cat('cham-soc-be')); ?>">Chăm sóc bé</a></li>
            <li><a href="<?php echo esc_url($cat('cham-soc-me')); ?>">Chăm sóc mẹ</a></li>
            <li><a href="<?php echo esc_url($cat('cham-soc-toc')); ?>">Chăm sóc tóc</a></li>
          </ul>
        </li>
        <li><a href="<?php echo esc_url(home_url('/feedback/')); ?>">Feedback</a></li>
        <li>
          <a href="<?php echo esc_url(home_url('/kien-thuc/')); ?>">Kiến thức</a>
          <button class="sub-btn" type="button" aria-expanded="false" aria-label="Mở menu Kiến thức">+</button>
          <ul class="sub">
            <li><a href="<?php echo esc_url($topic('huong-dan-cham-soc-be')); ?>">Hướng dẫn chăm sóc bé</a></li>
            <li><a href="<?php echo esc_url($topic('huong-dan-me')); ?>">Hướng dẫn mẹ</a></li>
            <li><a href="<?php echo esc_url($topic('me-bau-can-biet')); ?>">Mẹ bầu cần biết</a></li>
            <li><a href="<?php echo esc_url($topic('me-sau-sinh-can-biet')); ?>">Mẹ sau sinh cần biết</a></li>
            <li><a href="<?php echo esc_url($topic('dau-an-thanh-lan')); ?>">Dấu ấn Thanh Lan Spa</a></li>
          </ul>
        </li>
        <li><a href="<?php echo esc_url(home_url('/tu-van/')); ?>">Nhận tư vấn</a></li>
      </ul>
      <form class="search" action="<?php echo esc_url(home_url('/')); ?>" method="get"><input name="s" type="search" placeholder="Tìm trang" aria-label="Tìm kiếm" value="<?php echo esc_attr(get_search_query()); ?>" /><button type="submit">Tìm</button></form>
      <a class="btn nav-cta" href="<?php echo esc_url(home_url('/tu-van/')); ?>">Nhận tư vấn</a>
    </div>
  </header>
