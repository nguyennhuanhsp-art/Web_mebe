<?php if (!defined('ABSPATH')) { exit; } ?>
  <footer>
    <div class="wrap grid">
      <div><h2>Thanh Lan Spa</h2><p class="company">CÔNG TY TNHH TM&amp;DV THANH LAN.</p><p class="company">MST 0319180928, đăng ký ngày 25/9/2025.</p><p class="company">Số 77, đường 537, Ấp Bàu Trăn, Xã Nhuận Đức, TPHCM.</p><p>CEO &amp; Founder: Tô Thị Lan.</p></div>
      <div><h3>Menu</h3><ul>
        <li><a href="<?php echo esc_url(home_url('/gioi-thieu/')); ?>">Giới thiệu</a></li>
        <li><a href="<?php echo esc_url(home_url('/dich-vu/')); ?>">Dịch Vụ</a></li>
        <li><a href="<?php echo esc_url(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/cua-hang/')); ?>">Cửa hàng</a></li>
        <li><a href="<?php echo esc_url(home_url('/feedback/')); ?>">Feedback</a></li>
        <li><a href="<?php echo esc_url(home_url('/kien-thuc/')); ?>">Kiến thức</a></li>
      </ul></div>
      <div><h3>Liên hệ</h3><p>Hotline / Zalo: <a href="tel:0975399319"><strong>0975.399.319</strong></a> (Ms Tô Lan)</p><p>Mail: <a href="mailto:thanhlanspa238@gmail.com">thanhlanspa238@gmail.com</a></p><p><a href="https://www.facebook.com/photo/?fbid=910401714429198&amp;set=a.481696227299751" target="_blank" rel="noopener">Fanpage</a> · <a href="https://www.tiktok.com/@anhxuan.mkt" target="_blank" rel="noopener">TikTok</a></p></div>
    </div>
  </footer>
  <div class="dock">
    <a class="zalo" href="https://zalo.me/0975399319" target="_blank" rel="noopener">Zalo <span>Tư Vấn</span></a>
    <a class="fb" href="https://www.facebook.com/photo/?fbid=910401714429198&amp;set=a.481696227299751" target="_blank" rel="noopener">FB <span>Fanpage</span></a>
    <a class="call" href="tel:0975399319">Hotline: <span>0975.399.319</span></a>
  </div>
  <div class="overlay" id="booking">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="bookTitle">
      <button class="close" id="closeBooking" type="button" aria-label="Đóng">×</button>
      <h3 id="bookTitle">Đặt lịch Thanh Lan Spa</h3>
      <form id="bookingForm">
        <div class="field hp" aria-hidden="true"><label for="bookWebsite">Website</label><input id="bookWebsite" name="website" tabindex="-1" autocomplete="off" /></div>
        <div class="field"><label for="bookService">Dịch vụ</label><input id="bookService" readonly /></div>
        <div class="field"><label for="bookName">Họ và tên</label><input id="bookName" required /></div>
        <div class="field"><label for="bookPhone">Số điện thoại hoặc Zalo</label><input id="bookPhone" required inputmode="tel" /><div class="error" id="bookPhoneError"></div></div>
        <div class="field"><label for="bookEmail">Email nhận thư cảm ơn</label><input id="bookEmail" type="email" autocomplete="email" /></div>
        <div class="field"><label for="bookDate">Ngày mong muốn</label><input id="bookDate" type="date" /></div>
        <button class="btn block" type="submit">Xác nhận</button>
        <p class="note" id="bookNote"></p>
      </form>
    </div>
  </div>
  <?php wp_footer(); ?>
</body>
</html>
