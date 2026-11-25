<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
require '/home/giqisvhphosting/public_html/wp-load.php';
update_option('home', 'https://www.tolan.vn');
update_option('siteurl', 'https://www.tolan.vn');
file_put_contents(WP_CONTENT_DIR . '/uploads/thanh-lan-url.log', get_option('home') . "\n" . get_option('siteurl') . "\n");
echo get_option('home') . "\n" . get_option('siteurl') . "\n";
@unlink(__FILE__);
