<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();
?>
<main class="section"><div class="wrap">
  <h1>Tìm kiếm</h1>
  <?php if (have_posts()) : ?>
    <div class="grid three">
      <?php while (have_posts()) : the_post(); ?>
        <article class="card"><div class="pad">
          <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
          <p><?php echo esc_html(wp_trim_words(wp_strip_all_tags(get_the_content()), 24)); ?></p>
          <a class="more" href="<?php the_permalink(); ?>">Xem trang này</a>
        </div></article>
      <?php endwhile; ?>
    </div>
  <?php else : ?>
    <p>Chưa có trang nào khớp “<?php echo esc_html(get_search_query()); ?>”.</p>
  <?php endif; ?>
</div></main>
<?php
get_footer();
