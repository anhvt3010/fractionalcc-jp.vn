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

$dom_nau_img      = project_child_fcc_image( 'fcc_concerns_dom_nau_image', 'concerns' );
$tham_mun_img     = project_child_fcc_image( 'fcc_concerns_tham_mun_image', 'concerns' );
$lo_chan_long_img = project_child_fcc_image( 'fcc_concerns_lo_chan_long_image', 'concerns' );
$lao_hoa_img      = project_child_fcc_image( 'fcc_concerns_lao_hoa_image', 'concerns' );
$concerns_page_id = project_core_fcc_page_id( 'concerns' );
$pore_posts       = project_core_fcc_regimen_posts( 'lo-chan-long', $concerns_page_id );
$aging_posts      = project_core_fcc_regimen_posts( 'lao-hoa', $concerns_page_id );
$pore_item        = $pore_posts ? $pore_posts[0] : null;
$aging_item       = $aging_posts ? $aging_posts[0] : null;
$combos_by_id     = array_column( project_core_fcc_combos(), null, 'id' );
$pore_product     = $pore_item ? ( 'fcc_combo' === $pore_item->post_type ? ( $combos_by_id[ $pore_item->ID ] ?? null ) : project_core_fcc_product( $pore_item->ID, false ) ) : null;
$aging_product    = $aging_item ? ( 'fcc_combo' === $aging_item->post_type ? ( $combos_by_id[ $aging_item->ID ] ?? null ) : project_core_fcc_product( $aging_item->ID, false ) ) : null;
?>
<section class="section section-skin-concerns" id="van-de-lan-da">
  <div class="container">
    <div class="section-header">
      <span class="section-tag"><?php echo project_child_fcc_text( 'fcc_ui_m_649eb3367b80', 'concerns' ); ?></span>
      <h2 class="section-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_m_cfab75cee336', 'concerns' ); ?></h2>
      <p class="section-desc"><?php echo project_child_fcc_text( 'fcc_ui_m_c8c79f67f1e0', 'concerns' ); ?></p>
    </div>

    <!-- LƯỚI 4 NỖI ĐAU LÀN DA BÁM SÁT BRIEF -->
    <div class="concerns-home-grid">
      
      <!-- Thẻ 1: Đốm nâu & không đều màu (Dẫn vào Combo C-CX4) -->
      <article class="concern-home-card">
        <div class="concern-card-thumb"<?php echo project_child_fcc_image_edit_attributes( 'fcc_concerns_dom_nau_image', 'concerns' ); ?>>
          <?php if ( $dom_nau_img ) : ?>
            <?php echo project_child_fcc_picture_html( $dom_nau_img, project_child_fcc_raw_text( 'fcc_ui_m_f06dd395969c', 'concerns' ), 'concern-card-img', array( 'loading' => 'lazy', 'width' => 480, 'height' => 480 ) ); ?>
          <?php endif; ?>
        </div>
        <div class="concern-card-body">
          <div class="concern-card-eyebrow">
            <span class="concern-card-index">01</span>
            <span class="concern-card-sep" aria-hidden="true">&mdash;</span>
            <span class="concern-card-category"><?php echo project_child_fcc_text( 'fcc_ui_m_f5bbdaaf31e5', 'concerns' ); ?></span>
          </div>
          <h3 class="concern-card-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_m_f06dd395969c', 'concerns' ); ?></h3>
          <p class="concern-card-desc"><?php echo project_child_fcc_text( 'fcc_ui_m_beda867aa60c', 'concerns' ); ?></p>
          <?php if ( $featured_combo ) : ?><div class="concern-solution-box">
            <div class="concern-solution-product">
              <?php if ( ! empty( $featured_combo['image'] ) ) : ?>
                <div class="concern-solution-thumb">
                  <img src="<?php echo esc_url( $featured_combo['image'] ); ?>" alt="<?php echo esc_attr( $featured_combo['shortName'] ?: $featured_combo['name'] ); ?>" width="44" height="44" loading="lazy">
                </div>
              <?php endif; ?>
              <div class="concern-solution-meta">
                <span class="solution-label"><?php echo project_child_fcc_text( 'fcc_ui_m_9758ca3fb681', 'concerns' ); ?></span>
                <div class="solution-product-name"><?php echo esc_html( $featured_combo['shortName'] ?: $featured_combo['name'] ); ?></div>
              </div>
            </div>
            <button type="button" class="btn-concern-action" data-open-order data-product-select="<?php echo esc_attr( $combo_order_code ); ?>">
              <span><?php echo project_child_fcc_text( 'fcc_ui_m_13f5d50dabf4', 'shared' ); ?></span>
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </button>
          </div><?php endif; ?>
        </div>
      </article>

      <!-- Thẻ 2: Da thâm mụn sau viêm (Dẫn vào Combo C-CX4) -->
      <article class="concern-home-card">
        <div class="concern-card-thumb"<?php echo project_child_fcc_image_edit_attributes( 'fcc_concerns_tham_mun_image', 'concerns' ); ?>>
          <?php if ( $tham_mun_img ) : ?>
            <?php echo project_child_fcc_picture_html( $tham_mun_img, project_child_fcc_raw_text( 'fcc_ui_m_5b5204b02fc1', 'concerns' ), 'concern-card-img', array( 'loading' => 'lazy', 'width' => 480, 'height' => 480 ) ); ?>
          <?php endif; ?>
        </div>
        <div class="concern-card-body">
          <div class="concern-card-eyebrow">
            <span class="concern-card-index">02</span>
            <span class="concern-card-sep" aria-hidden="true">&mdash;</span>
            <span class="concern-card-category"><?php echo project_child_fcc_text( 'fcc_ui_m_b9f7d946a2ec', 'concerns' ); ?></span>
          </div>
          <h3 class="concern-card-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_m_5b5204b02fc1', 'concerns' ); ?></h3>
          <p class="concern-card-desc"><?php echo project_child_fcc_text( 'fcc_ui_m_c98495147be1', 'concerns' ); ?></p>
          <?php if ( $featured_combo ) : ?><div class="concern-solution-box">
            <div class="concern-solution-product">
              <?php if ( ! empty( $featured_combo['image'] ) ) : ?>
                <div class="concern-solution-thumb">
                  <img src="<?php echo esc_url( $featured_combo['image'] ); ?>" alt="<?php echo esc_attr( $featured_combo['shortName'] ?: $featured_combo['name'] ); ?>" width="44" height="44" loading="lazy">
                </div>
              <?php endif; ?>
              <div class="concern-solution-meta">
                <span class="solution-label"><?php echo project_child_fcc_text( 'fcc_ui_m_9758ca3fb681', 'concerns' ); ?></span>
                <div class="solution-product-name"><?php echo esc_html( $featured_combo['shortName'] ?: $featured_combo['name'] ); ?></div>
              </div>
            </div>
            <button type="button" class="btn-concern-action" data-open-order data-product-select="<?php echo esc_attr( $combo_order_code ); ?>">
              <span><?php echo project_child_fcc_text( 'fcc_ui_m_13f5d50dabf4', 'shared' ); ?></span>
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </button>
          </div><?php endif; ?>
        </div>
      </article>

      <!-- Thẻ 3: Lỗ chân lông to & da sần (Dẫn vào Mandelic Peel) -->
      <article class="concern-home-card">
        <div class="concern-card-thumb"<?php echo project_child_fcc_image_edit_attributes( 'fcc_concerns_lo_chan_long_image', 'concerns' ); ?>>
          <?php if ( $lo_chan_long_img ) : ?>
            <?php echo project_child_fcc_picture_html( $lo_chan_long_img, project_child_fcc_raw_text( 'fcc_ui_m_401a2fca51b8', 'concerns' ), 'concern-card-img', array( 'loading' => 'lazy', 'width' => 480, 'height' => 480 ) ); ?>
          <?php endif; ?>
        </div>
        <div class="concern-card-body">
          <div class="concern-card-eyebrow">
            <span class="concern-card-index">03</span>
            <span class="concern-card-sep" aria-hidden="true">&mdash;</span>
            <span class="concern-card-category"><?php echo project_child_fcc_text( 'fcc_ui_m_2744e5e9d152', 'concerns' ); ?></span>
          </div>
          <h3 class="concern-card-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_m_401a2fca51b8', 'concerns' ); ?></h3>
          <p class="concern-card-desc"><?php echo project_child_fcc_text( 'fcc_ui_m_a9e9a444a956', 'concerns' ); ?></p>
          <?php if ( $pore_product ) : ?><div class="concern-solution-box">
            <div class="concern-solution-product">
              <?php if ( ! empty( $pore_product['image'] ) ) : ?>
                <div class="concern-solution-thumb">
                  <img src="<?php echo esc_url( $pore_product['image'] ); ?>" alt="<?php echo esc_attr( $pore_product['shortName'] ?: $pore_product['name'] ); ?>" width="44" height="44" loading="lazy">
                </div>
              <?php endif; ?>
              <div class="concern-solution-meta">
                <span class="solution-label"><?php echo project_child_fcc_text( 'fcc_ui_m_9758ca3fb681', 'concerns' ); ?></span>
                <div class="solution-product-name"><?php echo esc_html( $pore_product['shortName'] ?: $pore_product['name'] ); ?></div>
              </div>
            </div>
            <a href="<?php echo esc_url( $pore_product['url'] ); ?>" class="btn-concern-action">
              <span><?php echo project_child_fcc_text( 'fcc_ui_m_d377ac936d0f', 'shared' ); ?></span>
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </a>
          </div><?php endif; ?>
        </div>
      </article>

      <!-- Thẻ 4: Lão hóa, nếp nhăn & chảy xệ -->
      <article class="concern-home-card">
        <div class="concern-card-thumb"<?php echo project_child_fcc_image_edit_attributes( 'fcc_concerns_lao_hoa_image', 'concerns' ); ?>>
          <?php if ( $lao_hoa_img ) : ?>
            <?php echo project_child_fcc_picture_html( $lao_hoa_img, project_child_fcc_raw_text( 'fcc_ui_m_e420d47dc084', 'concerns' ), 'concern-card-img', array( 'loading' => 'lazy', 'width' => 480, 'height' => 480 ) ); ?>
          <?php endif; ?>
        </div>
        <div class="concern-card-body">
          <div class="concern-card-eyebrow">
            <span class="concern-card-index">04</span>
            <span class="concern-card-sep" aria-hidden="true">&mdash;</span>
            <span class="concern-card-category"><?php echo project_child_fcc_text( 'fcc_ui_m_512462f7367e', 'concerns' ); ?></span>
          </div>
          <h3 class="concern-card-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_m_e420d47dc084', 'concerns' ); ?></h3>
          <p class="concern-card-desc"><?php echo project_child_fcc_text( 'fcc_ui_m_73b5443ca20d', 'concerns' ); ?></p>
          <?php if ( $aging_product ) : ?><div class="concern-solution-box">
            <div class="concern-solution-product">
              <?php if ( ! empty( $aging_product['image'] ) ) : ?>
                <div class="concern-solution-thumb">
                  <img src="<?php echo esc_url( $aging_product['image'] ); ?>" alt="<?php echo esc_attr( $aging_product['shortName'] ?: $aging_product['name'] ); ?>" width="44" height="44" loading="lazy">
                </div>
              <?php endif; ?>
              <div class="concern-solution-meta">
                <span class="solution-label"><?php echo project_child_fcc_text( 'fcc_ui_m_9758ca3fb681', 'concerns' ); ?></span>
                <div class="solution-product-name"><?php echo esc_html( $aging_product['shortName'] ?: $aging_product['name'] ); ?></div>
              </div>
            </div>
            <a href="<?php echo esc_url( $aging_product['url'] ); ?>" class="btn-concern-action">
              <span><?php echo project_child_fcc_text( 'fcc_ui_m_d377ac936d0f', 'shared' ); ?></span>
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </a>
          </div><?php endif; ?>
        </div>
      </article>

    </div>

    <!-- THANH ĐIỀU HƯỚNG XUỐNG DANH MỤC & TRANG VẤN ĐỀ LÀN DA -->
    <div class="concerns-transition-bar">
      <div class="concerns-transition-tip">
        <span class="tip-icon" aria-hidden="true">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
        </span>
        <div class="tip-content">
          <strong><?php echo project_child_fcc_text( 'fcc_home_concerns_advice_title', 'concerns' ); ?></strong>
          <?php echo project_child_fcc_text( 'fcc_home_concerns_advice_text', 'concerns' ); ?>
        </div>
      </div>
      <div class="concerns-transition-actions">
        <a href="#danh-muc" class="btn btn-transition-catalog">
          <span><?php echo project_child_fcc_text( 'fcc_ui_8b675966c60cc0', 'shared' ); ?></span>
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"></line><polyline points="19 12 12 19 5 12"></polyline></svg>
        </a>
        <a href="<?php echo esc_url( $concerns_page_url ); ?>" class="btn-transition-more">
          <span><?php echo project_child_fcc_text( 'fcc_ui_m_a613489f0d9c', 'shared' ); ?></span>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </a>
      </div>
    </div>

  </div>
</section>
