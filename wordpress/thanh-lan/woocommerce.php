<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();
echo '<main class="section"><div class="wrap">';
woocommerce_content();
echo '</div></main>';
get_footer();
