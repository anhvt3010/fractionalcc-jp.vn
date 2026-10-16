<?php
defined( 'ABSPATH' ) || exit;

$company_name = project_child_fcc_raw_text( 'fcc_ui_e28520e695bc79', 'shared' );

$address = project_core_fcc_contact( 'addressHN' );

$hotline_display = project_core_fcc_contact( 'hotlineDisplay' );
$hotline         = project_core_fcc_contact( 'hotline' );

$phone_display   = project_core_fcc_contact( 'phoneDisplay' );
$phone           = project_core_fcc_contact( 'phone' );

$email           = project_core_fcc_contact( 'email' );
?>
<footer class="site-footer" id="fcc-footer">
  <div class="container">
    <div class="footer-grid">
      <!-- CỘT 1: THƯƠNG HIỆU & ĐƠN VỊ PHÂN PHỐI -->
      <div class="footer-col">
        <div class="brand-logo-duo"<?php echo project_child_fcc_image_edit_attributes( 'fcc_ui_73ad1fb64a971c' ); ?>>
          <?php
          echo project_child_fcc_picture_html(
              project_child_fcc_image( 'fcc_ui_73ad1fb64a971c' ),
              project_child_fcc_image_alt( 'fcc_ui_73ad1fb64a971c', 'fcc_ui_e23f070eed474c' ),
              'brand-logo-main-img',
              array( 'width' => 166, 'height' => 40 )
          );
          ?>
          <span class="logo-divider"></span>
          <?php
          echo project_child_fcc_picture_html(
              project_child_fcc_image( 'fcc_ui_be08f7c57f78ec' ),
              project_child_fcc_image_alt( 'fcc_ui_be08f7c57f78ec', 'fcc_ui_435f1980942f04' ),
              'distributor-logo-img',
              array( 'width' => 40, 'height' => 40 )
          );
          ?>
        </div>
        <p>
          <strong><?php echo project_child_fcc_text( 'fcc_ui_8c686c3909c590', 'shared' ); ?></strong><br>
          <span class="footer-company-name"><?php echo esc_html( $company_name ); ?></span>
        </p>
      </div>

      <!-- CỘT 2: VĂN PHÒNG & LIÊN HỆ -->
      <div class="footer-col">
        <h2><?php echo project_child_fcc_text( 'fcc_ui_0f36c14dea884e', 'shared' ); ?></h2>
        <?php if ( $address ) : ?><p><strong>Địa chỉ:</strong> <?php echo esc_html( $address ); ?></p><?php endif; ?>
        <?php if ( $hotline ) : ?><p><strong><?php echo project_child_fcc_text( 'fcc_ui_984783bd913b86', 'shared' ); ?></strong> <a <?php project_child_fcc_contact_attributes( 'hotline' ); ?> style="color: var(--color-accent-gold); font-weight: 700;"><?php echo esc_html( $hotline_display ); ?></a></p><?php endif; ?>
        <?php if ( $phone ) : ?><p><strong>Điện thoại:</strong> <a <?php project_child_fcc_contact_attributes( 'phone' ); ?> style="color: #FFFFFF; font-weight: 600;"><?php echo esc_html( $phone_display ); ?></a></p><?php endif; ?>
        <?php if ( $email ) : ?><p><strong><?php echo project_child_fcc_text( 'fcc_ui_7374e1f6e37820', 'shared' ); ?></strong> <a <?php project_child_fcc_contact_attributes( 'email' ); ?>><?php echo project_child_fcc_text( 'fcc_ui_73944c222e59fa', 'shared', false ); ?></a></p><?php endif; ?>
      </div>

      <!-- CỘT 3: LIÊN KẾT NHANH -->
      <div class="footer-col">
        <h2>Liên Kết Nhanh</h2>
        <ul class="footer-links">
          <li><a href="<?php echo esc_url( project_child_fcc_link( 'index.html' ) ); ?>"><?php echo project_child_fcc_text( 'fcc_nav_home', 'shared', false ); ?></a></li>
          <li><a href="<?php echo esc_url( project_child_fcc_link( 'san-pham.html' ) ); ?>"><?php echo project_child_fcc_text( 'fcc_ui_13f874efa5726a', 'shared', false ); ?></a></li>
          <li><a href="<?php echo esc_url( project_child_fcc_link( 'van-de-lan-da.html' ) ); ?>"><?php echo project_child_fcc_text( 'fcc_ui_m_a613489f0d9c', 'shared', false ); ?></a></li>
          <?php $footer_combo = project_core_fcc_featured_combo(); if ( $footer_combo ) : ?><li><a href="<?php echo esc_url( project_child_fcc_link( 'index.html#san-pham-chu-luc' ) ); ?>"><?php echo esc_html( $footer_combo['shortName'] ?: $footer_combo['name'] ); ?></a></li><?php endif; ?>
        </ul>
      </div>

      <!-- CỘT 4: CHÍNH SÁCH & THÔNG TIN -->
      <div class="footer-col">
        <h2><?php echo project_child_fcc_text( 'fcc_ui_337affe9cef2fd', 'shared' ); ?></h2>
        <ul class="footer-links">
          <li><a href="<?php echo esc_url( project_child_fcc_link( 'chinh-sach.html#doi-tra' ) ); ?>"><?php echo project_child_fcc_text( 'fcc_ui_8aa241ea23c189', 'shared', false ); ?></a></li>
          <li><a href="<?php echo esc_url( project_child_fcc_link( 'chinh-sach.html#giao-hang' ) ); ?>"><?php echo project_child_fcc_text( 'fcc_ui_369ace7ad7eef2', 'shared', false ); ?></a></li>
          <li><a href="<?php echo esc_url( project_child_fcc_link( 'chinh-sach.html#bao-mat' ) ); ?>"><?php echo project_child_fcc_text( 'fcc_ui_9e1d7490e3a45a', 'shared', false ); ?></a></li>
          <li><a href="<?php echo esc_url( project_child_fcc_link( 'chinh-sach.html#thanh-toan' ) ); ?>"><?php echo project_child_fcc_text( 'fcc_ui_a33080e0eed912', 'policies', false ); ?></a></li>
          <li><a href="<?php echo esc_url( project_child_fcc_link( 'chinh-sach.html#chinh-hang' ) ); ?>"><?php echo project_child_fcc_text( 'fcc_ui_0a8730dc13bdc0', 'shared', false ); ?></a></li>
          <li><a href="<?php echo esc_url( project_child_fcc_link( 'chinh-sach.html#khieu-nai' ) ); ?>"><?php echo project_child_fcc_text( 'fcc_ui_26c96d0e06946b', 'policies', false ); ?></a></li>
        </ul>
      </div>
    </div>

    <!-- PHẦN DƯỚI CÙNG -->
    <div class="footer-bottom">
      <p style="margin: 0;"><?php echo project_child_fcc_text( 'fcc_ui_3e12c24e7634b2', 'shared' ); ?></p>
      <p style="margin: 0; font-size: 12px;"><?php echo project_child_fcc_text( 'fcc_ui_f8b498e754b279', 'shared' ); ?></p>
    </div>
  </div>
</footer>
