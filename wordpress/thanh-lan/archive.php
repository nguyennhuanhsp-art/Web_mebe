<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();
?>
<main class="section"><div class="wrap">
  <p class="crumbs"><a href="<?php echo esc_url(home_url('/')); ?>">Trang chủ</a> / <a href="<?php echo esc_url(home_url('/kien-thuc/')); ?>">Kiến thức</a> / <?php echo esc_html(single_cat_title('', false) ?: wp_title('', false)); ?></p>
  <h1><?php echo esc_html(single_cat_title('', false) ?: 'Kiến thức'); ?></h1>
  <p class="intro">Nhân viên thêm bài trong mục Bài viết và chọn đúng chuyên mục. Ảnh đại diện lấy từ Thư viện.</p>
  <?php if (have_posts()) : ?>
    <div class="grid three">
      <?php while (have_posts()) : the_post(); ?>
        <article class="card">
          <?php if (has_post_thumbnail()) {
              the_post_thumbnail('medium_large');
          } ?>
          <div class="pad">
            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            <p><?php echo esc_html(wp_trim_words(wp_strip_all_tags(get_the_excerpt() ?: get_the_content()), 28)); ?></p>
            <a class="more" href="<?php the_permalink(); ?>">Đọc bài</a>
          </div>
        </article>
      <?php endwhile; ?>
    </div>
  <?php else : ?>
    <p>Chuyên mục này chưa có bài. Vào Bài viết trong quản trị để đăng.</p>
  <?php endif; ?>
</div></main>
<?php
get_footer();
