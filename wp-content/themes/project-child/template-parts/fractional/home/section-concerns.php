<?php
/**
 * Skin Concerns Section on Homepage (Mục 11 trong brief.docx - Nỗi đau khách hàng).
 * Connects to the selected featured combo and details on /van-de-lan-da/.
 *
 * @package Project_Child
 */
defined( 'ABSPATH' ) || exit;

$concerns_page_url = project_child_fcc_link( 'van-de-lan-da.html' );
$combo_order_code  = project_child_fcc_featured_combo_order_code();
$featured_combo   = project_core_fcc_featured_combo();

$dom_nau_img    = project_child_fcc_asset( 'assets/images/concerns/concern-dom-nau.webp' );
$tham_mun_img   = project_child_fcc_asset( 'assets/images/concerns/concern-tham-mun.webp' );
$lo_chan_long_img = project_child_fcc_asset( 'assets/images/concerns/concern-lo-chan-long.webp' );
$lao_hoa_img    = project_child_fcc_asset( 'assets/images/concerns/concern-lao-hoa.webp' );
?>
<section class="section section-skin-concerns" id="van-de-lan-da">
  <div class="container">
    <div class="section-header">
      <span class="section-tag"><?php echo project_child_fcc_text( 'fcc_ui_m_649eb3367b80', 'concerns', false ) ?: 'VẤN ĐỀ LÀN DA'; ?></span>
      <h2 class="section-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_m_cfab75cee336', 'concerns', false ) ?: 'LÀN DA CỦA BẠN ĐANG GẶP NHỮNG VẤN ĐỀ GÌ?'; ?></h2>
      <p class="section-desc"><?php echo project_child_fcc_text( 'fcc_ui_m_c8c79f67f1e0', 'concerns', false ) ?: 'Tìm hiểu các nhu cầu chăm sóc da thường gặp và tham khảo sản phẩm Fractional CC phù hợp với nhu cầu của bạn.'; ?></p>
    </div>

    <!-- LƯỚI 4 NỖI ĐAU LÀN DA BÁM SÁT BRIEF -->
    <div class="concerns-home-grid">
      
      <!-- Thẻ 1: Đốm nâu & không đều màu (Dẫn vào Combo C-CX4) -->
      <article class="concern-home-card concern-highlight-card">
        <div class="concern-card-badge">Vấn đề sắc tố #01</div>
        <div class="concern-card-thumb">
          <?php if ( $dom_nau_img ) : ?>
            <img src="<?php echo esc_url( $dom_nau_img ); ?>" alt="Đốm nâu, da không đều màu" loading="lazy" width="400" height="400" class="concern-card-img">
          <?php endif; ?>
        </div>
        <div class="concern-card-body">
          <h3 class="concern-card-title"><?php echo project_child_fcc_text( 'fcc_ui_m_f06dd395969c', 'concerns', false ) ?: 'Đốm Nâu & Không Đều Màu'; ?></h3>
          <p class="concern-card-desc"><?php echo project_child_fcc_text( 'fcc_ui_m_beda867aa60c', 'concerns', false ) ?: 'Đốm nâu và các vùng da sậm màu khiến màu da trông không đồng đều.'; ?></p>
          <?php if ( $featured_combo ) : ?><div class="concern-solution-box">
            <span class="solution-label">Khuyên dùng</span>
            <div class="solution-product-name"><?php echo esc_html( $featured_combo['shortName'] ?: $featured_combo['name'] ); ?></div>
            <button class="btn-concern-action" data-open-order data-product-select="<?php echo esc_attr( $combo_order_code ); ?>">
              <span>Chọn combo này</span>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </button>
          </div><?php endif; ?>
        </div>
      </article>

      <!-- Thẻ 2: Da thâm mụn sau viêm (Dẫn vào Combo C-CX4) -->
      <article class="concern-home-card concern-highlight-card">
        <div class="concern-card-badge">Vết thâm dai dẳng #02</div>
        <div class="concern-card-thumb">
          <?php if ( $tham_mun_img ) : ?>
            <img src="<?php echo esc_url( $tham_mun_img ); ?>" alt="Da thâm mụn sau viêm" loading="lazy" width="400" height="400" class="concern-card-img">
          <?php endif; ?>
        </div>
        <div class="concern-card-body">
          <h3 class="concern-card-title"><?php echo project_child_fcc_text( 'fcc_ui_m_5b5204b02fc1', 'concerns', false ) ?: 'Da Thâm Mụn Sau Viêm'; ?></h3>
          <p class="concern-card-desc"><?php echo project_child_fcc_text( 'fcc_ui_m_c98495147be1', 'concerns', false ) ?: 'Vết thâm có thể còn lại sau mụn, khiến màu da trông không đồng đều.'; ?></p>
          <?php if ( $featured_combo ) : ?><div class="concern-solution-box">
            <span class="solution-label">Khuyên dùng</span>
            <div class="solution-product-name"><?php echo esc_html( $featured_combo['shortName'] ?: $featured_combo['name'] ); ?></div>
            <button class="btn-concern-action" data-open-order data-product-select="<?php echo esc_attr( $combo_order_code ); ?>">
              <span>Chọn combo này</span>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </button>
          </div><?php endif; ?>
        </div>
      </article>

      <!-- Thẻ 3: Lỗ chân lông to & da sần (Dẫn vào Mandelic Peel) -->
      <article class="concern-home-card">
        <div class="concern-card-badge">Bề mặt & Bã nhờn #03</div>
        <div class="concern-card-thumb">
          <?php if ( $lo_chan_long_img ) : ?>
            <img src="<?php echo esc_url( $lo_chan_long_img ); ?>" alt="Lỗ chân lông to và da sần sùi" loading="lazy" width="400" height="400" class="concern-card-img">
          <?php endif; ?>
        </div>
        <div class="concern-card-body">
          <h3 class="concern-card-title"><?php echo project_child_fcc_text( 'fcc_ui_m_401a2fca51b8', 'concerns', false ) ?: 'Lỗ Chân Lông To & Da Sần'; ?></h3>
          <p class="concern-card-desc"><?php echo project_child_fcc_text( 'fcc_ui_m_a9e9a444a956', 'concerns', false ) ?: 'Lỗ chân lông dễ thấy và bề mặt da sần là những nhu cầu chăm sóc thường gặp.'; ?></p>
          <div class="concern-solution-box">
            <span class="solution-label">Khuyên dùng</span>
            <div class="solution-product-name">Serum Tẩy Da Chết Mandelic Shot</div>
            <a href="<?php echo esc_url( project_child_fcc_link( 'san-pham.html' ) ); ?>#danh-sach-san-pham" class="btn-concern-action">
              <span>Xem chi tiết</span>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </a>
          </div>
        </div>
      </article>

      <!-- Thẻ 4: Lão hóa, nếp nhăn & chảy xệ -->
      <article class="concern-home-card">
        <div class="concern-card-badge">Tuổi tác & Độ săn chắc #04</div>
        <div class="concern-card-thumb">
          <?php if ( $lao_hoa_img ) : ?>
            <img src="<?php echo esc_url( $lao_hoa_img ); ?>" alt="Da lão hóa, nếp nhăn, chảy xệ" loading="lazy" width="400" height="400" class="concern-card-img">
          <?php endif; ?>
        </div>
        <div class="concern-card-body">
          <h3 class="concern-card-title"><?php echo project_child_fcc_text( 'fcc_ui_m_e420d47dc084', 'concerns', false ) ?: 'Da Lão Hóa & Nếp Nhăn'; ?></h3>
          <p class="concern-card-desc"><?php echo project_child_fcc_text( 'fcc_ui_m_73b5443ca20d', 'concerns', false ) ?: 'Nếp nhăn và thay đổi về độ đàn hồi là những dấu hiệu da có thể gặp theo thời gian.'; ?></p>
          <div class="concern-solution-box">
            <span class="solution-label">Khuyên dùng</span>
            <div class="solution-product-name">Needle Serum AX4 &amp; PDRN Shot</div>
            <a href="<?php echo esc_url( project_child_fcc_link( 'san-pham.html' ) ); ?>#danh-sach-san-pham" class="btn-concern-action">
              <span>Xem chi tiết</span>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </a>
          </div>
        </div>
      </article>

    </div>

    <!-- THANH ĐIỀU HƯỚNG XUỐNG DANH MỤC & TRANG VẤN ĐỀ LÀN DA -->
    <div class="concerns-transition-bar">
      <div class="concerns-transition-tip">
        <span class="tip-icon">💡</span>
        <div class="tip-content">
          <strong><?php echo project_child_fcc_text( 'fcc_home_concerns_advice_title', 'concerns' ); ?></strong>
          <?php echo project_child_fcc_text( 'fcc_home_concerns_advice_text', 'concerns' ); ?>
        </div>
      </div>
      <div class="concerns-transition-actions">
        <a href="#danh-muc" class="btn btn-transition-catalog">
          <span><?php echo esc_html( $featured_combo ? 'Xem ' . ( $featured_combo['shortName'] ?: $featured_combo['name'] ) . ' bên dưới' : 'Xem danh mục sản phẩm bên dưới' ); ?></span>
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
        </a>
        <a href="<?php echo esc_url( $concerns_page_url ); ?>" class="btn-transition-more">
          <span>Xem chi tiết 4 vấn đề da</span>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </a>
      </div>
    </div>

  </div>
</section>
