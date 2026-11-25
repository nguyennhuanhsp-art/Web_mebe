<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();
while (have_posts()) {
    the_post();
    ?>
<main><section class="page-hero"><div class="wrap split">
  <article>
    <p class="crumbs"><a href="<?php echo esc_url(home_url('/')); ?>">Trang chủ</a> / <a href="<?php echo esc_url(home_url('/kien-thuc/')); ?>">Kiến thức</a> / <?php the_title(); ?></p>
    <p class="kicker">Kiến thức</p>
    <h1><?php the_title(); ?></h1>
    <?php the_content(); ?>
  </article>
  <?php if (has_post_thumbnail()) : ?>
    <figure class="panel"><?php the_post_thumbnail('large', array('class' => 'cover')); ?></figure>
  <?php endif; ?>
</div></section></main>
    <?php
}
get_footer();
