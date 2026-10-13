<?php
/**
 * Skin Concerns & Scientific Solutions Section Template.
 * Redesigned: Tab-based interface for simplicity, clarity, and consistency.
 *
 * @package Project_Child
 */
defined( 'ABSPATH' ) || exit;

$hotline_display = function_exists( 'project_core_fcc_site' ) ? project_core_fcc_site( 'hotlineDisplay', '0985 561 862' ) : '0985 561 862';
$hotline         = function_exists( 'project_core_fcc_site' ) ? project_core_fcc_site( 'hotline', '0985561862' ) : '0985561862';
?>

<!-- BREADCRUMB & HERO HEADER -->
<section class="concerns-hero-header">
  <div class="container">
    <nav class="breadcrumb-nav" aria-label="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_m_14b8ce911f21', 'concerns' ) ); ?>">
      <a href="<?php echo esc_url( project_child_fcc_link( 'index.html' ) ); ?>"><?php echo project_child_fcc_text( 'fcc_ui_m_ab99a1cbbda7', 'concerns' ); ?></a>
      <span>›</span>
      <strong><?php echo project_child_fcc_text( 'fcc_ui_m_b660b6828a87', 'concerns' ); ?></strong>
    </nav>

    <div class="concerns-hero-content">
      <span class="badge-pill badge-gold" style="margin-bottom: 12px; display: inline-flex;"><?php echo project_child_fcc_text( 'fcc_ui_m_649eb3367b80', 'concerns' ); ?></span>
      <h1 class="heading-serif concerns-hero-title">
        <?php echo project_child_fcc_text( 'fcc_ui_m_cfab75cee336', 'concerns' ); ?>
      </h1>
      <p class="concerns-hero-desc">
        <?php echo project_child_fcc_text( 'fcc_ui_m_c8c79f67f1e0', 'concerns' ); ?>
      </p>
    </div>
  </div>
</section>

<!-- MAIN CONTENT WRAPPER -->
<div class="concerns-body-wrap">
  <div class="container">

    <!-- TAB NAVIGATION -->
    <div class="concerns-tab-nav" role="tablist" aria-label="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_m_1aac9f5963a3', 'concerns' ) ); ?>">
      <button class="concern-tab-btn active" data-tab="dom-nau" role="tab" aria-selected="true" aria-controls="pane-dom-nau">
        <span class="tab-btn-num">01</span>
        <span class="tab-btn-title"><?php echo project_child_fcc_text( 'fcc_ui_m_f06dd395969c', 'concerns' ); ?></span>
      </button>
      <button class="concern-tab-btn" data-tab="lo-chan-long" role="tab" aria-selected="false" aria-controls="pane-lo-chan-long">
        <span class="tab-btn-num">02</span>
        <span class="tab-btn-title"><?php echo project_child_fcc_text( 'fcc_ui_m_401a2fca51b8', 'concerns' ); ?></span>
      </button>
      <button class="concern-tab-btn" data-tab="lao-hoa" role="tab" aria-selected="false" aria-controls="pane-lao-hoa">
        <span class="tab-btn-num">03</span>
        <span class="tab-btn-title"><?php echo project_child_fcc_text( 'fcc_ui_m_e420d47dc084', 'concerns' ); ?></span>
      </button>
      <button class="concern-tab-btn" data-tab="tham-mun" role="tab" aria-selected="false" aria-controls="pane-tham-mun">
        <span class="tab-btn-num">04</span>
        <span class="tab-btn-title"><?php echo project_child_fcc_text( 'fcc_ui_m_5b5204b02fc1', 'concerns' ); ?></span>
      </button>
    </div>

    <!-- TAB PANES -->
    <div class="concerns-tab-content">

      <!-- PANE 01: ĐỐM NÂU & KHÔNG ĐỀU MÀU -->
      <div class="concern-tab-pane active" id="pane-dom-nau" role="tabpanel">
        <div class="concern-pane-card">
          <!-- PHẦN 1: TỔNG QUAN TÌNH TRẠNG & CƠ CHẾ KHOA HỌC (2 CỘT CÂN ĐỐI) -->
          <div class="concern-overview-grid">
            <!-- Cột Trái: Thông tin tình trạng, mô tả nỗi đau & ảnh dấu hiệu -->
            <div class="concern-visual-col">
              <div class="concern-info-header">
                <span class="concern-tag"><?php echo project_child_fcc_text( 'fcc_ui_m_f5bbdaaf31e5', 'concerns' ); ?></span>
                <h2 class="heading-serif concern-pane-heading"><?php echo project_child_fcc_text( 'fcc_ui_m_0e55facbf019', 'concerns' ); ?></h2>
                <p class="concern-pain-quote"><?php echo project_child_fcc_text( 'fcc_ui_m_beda867aa60c', 'concerns' ); ?></p>
              </div>
              <div class="concern-visual-card">
                <div class="concern-img-frame">
                  <?php if ( project_child_fcc_image( 'fcc_concerns_dom_nau_image', 'concerns' ) ) : ?><img src="<?php echo esc_url( project_child_fcc_image( 'fcc_concerns_dom_nau_image', 'concerns' ) ); ?>" alt="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_m_7f9a48bcc23a', 'concerns' ) ); ?>" class="concern-skin-img" width="526" height="806" loading="lazy"><?php endif; ?>
                </div>
                <div class="concern-visual-caption">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12.01" y2="8"></line><line x1="12" y1="12" x2="12" y2="16"></line></svg>
                  <span><?php echo project_child_fcc_text( 'fcc_ui_m_034411885516', 'concerns' ); ?></span>
                </div>
              </div>
            </div>

            <!-- Cột Phải: Phân tích nguyên nhân & Giải pháp khoa học -->
            <div class="concern-science-col">
              <div class="concern-science-header">
                <span class="section-tag"><?php echo project_child_fcc_text( 'fcc_ui_m_ffa7ee5fe96b', 'concerns' ); ?></span>
                <h3 class="concern-science-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_m_455c002eaf62', 'concerns' ); ?></h3>
                <p class="concern-science-desc"><?php echo project_child_fcc_text( 'fcc_ui_m_17a6eb86cfe3', 'concerns' ); ?></p>
              </div>

              <div class="concern-detail-cards">
                <div class="concern-detail-card">
                  <h4 class="detail-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    <?php echo project_child_fcc_text( 'fcc_ui_m_451a423cf5c2', 'concerns' ); ?>
                  </h4>
                  <p><?php echo project_child_fcc_text( 'fcc_ui_m_ee317c6cecd4', 'concerns' ); ?></p>
                </div>

                <div class="concern-detail-card highlight-card">
                  <h4 class="detail-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    <?php echo project_child_fcc_text( 'fcc_ui_m_15ab5f794c38', 'concerns' ); ?>
                  </h4>
                  <p><?php echo project_child_fcc_text( 'fcc_ui_m_b914dd02a446', 'concerns' ); ?></p>
                </div>
              </div>
            </div>
          </div>

          <!-- PHẦN 2: GỢI Ý PHÁC ĐỒ & SẢN PHẨM CHỦ LỰC (TRẢI NGANG BÊN DƯỚI) -->
          <div class="concern-regimen-section">
            <div class="pane-products-header">
              <span class="section-tag"><?php echo project_child_fcc_text( 'fcc_ui_m_9758ca3fb681', 'concerns' ); ?></span>
              <h3 class="pane-products-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_m_6ca8ac0a58fa', 'concerns' ); ?></h3>
              <p class="pane-products-desc"><?php echo project_child_fcc_text( 'fcc_ui_m_d50ac8254e8c', 'concerns' ); ?></p>
            </div>
            <?php get_template_part( 'template-parts/fractional/components/concern-regimen', null, array( 'concern' => 'dom-nau' ) ); ?>
          </div>
        </div>
      </div>

      <!-- PANE 02: LỖ CHÂN LÔNG TO & DA SẦN -->
      <div class="concern-tab-pane" id="pane-lo-chan-long" role="tabpanel">
        <div class="concern-pane-card">
          <!-- PHẦN 1: TỔNG QUAN TÌNH TRẠNG & CƠ CHẾ KHOA HỌC (2 CỘT CÂN ĐỐI) -->
          <div class="concern-overview-grid">
            <!-- Cột Trái: Thông tin tình trạng, mô tả nỗi đau & ảnh dấu hiệu -->
            <div class="concern-visual-col">
              <div class="concern-info-header">
                <span class="concern-tag"><?php echo project_child_fcc_text( 'fcc_ui_m_2744e5e9d152', 'concerns' ); ?></span>
                <h2 class="heading-serif concern-pane-heading"><?php echo project_child_fcc_text( 'fcc_ui_m_5d147807e782', 'concerns' ); ?></h2>
                <p class="concern-pain-quote"><?php echo project_child_fcc_text( 'fcc_ui_m_808bf3619735', 'concerns' ); ?></p>
              </div>
              <div class="concern-visual-card">
                <div class="concern-img-frame">
                  <?php if ( project_child_fcc_image( 'fcc_concerns_lo_chan_long_image', 'concerns' ) ) : ?><img src="<?php echo esc_url( project_child_fcc_image( 'fcc_concerns_lo_chan_long_image', 'concerns' ) ); ?>" alt="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_m_522cb40cc5a1', 'concerns' ) ); ?>" class="concern-skin-img" width="722" height="960" loading="lazy"><?php endif; ?>
                </div>
                <div class="concern-visual-caption">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12.01" y2="8"></line><line x1="12" y1="12" x2="12" y2="16"></line></svg>
                  <span><?php echo project_child_fcc_text( 'fcc_ui_m_e71b65eb752e', 'concerns' ); ?></span>
                </div>
              </div>
            </div>

            <!-- Cột Phải: Phân tích nguyên nhân & Giải pháp khoa học -->
            <div class="concern-science-col">
              <div class="concern-science-header">
                <span class="section-tag"><?php echo project_child_fcc_text( 'fcc_ui_m_20801d2b44e3', 'concerns' ); ?></span>
                <h3 class="concern-science-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_m_a1d45865fa01', 'concerns' ); ?></h3>
                <p class="concern-science-desc"><?php echo project_child_fcc_text( 'fcc_ui_m_70b6625c50ea', 'concerns' ); ?></p>
              </div>

              <div class="concern-detail-cards">
                <div class="concern-detail-card">
                  <h4 class="detail-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    <?php echo project_child_fcc_text( 'fcc_ui_m_305f2711c20c', 'concerns' ); ?>
                  </h4>
                  <p><?php echo project_child_fcc_text( 'fcc_ui_m_73519e2fc8b1', 'concerns' ); ?></p>
                </div>

                <div class="concern-detail-card highlight-card">
                  <h4 class="detail-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    <?php echo project_child_fcc_text( 'fcc_ui_m_d064bb5fcb12', 'concerns' ); ?>
                  </h4>
                  <p><?php echo project_child_fcc_text( 'fcc_ui_m_cfc5818a1f4e', 'concerns' ); ?></p>
                </div>
              </div>
            </div>
          </div>

          <!-- PHẦN 2: GỢI Ý PHÁC ĐỒ & SẢN PHẨM CHỦ LỰC (TRẢI NGANG BÊN DƯỚI) -->
          <div class="concern-regimen-section">
            <div class="pane-products-header">
              <span class="section-tag"><?php echo project_child_fcc_text( 'fcc_ui_m_dcdcfc792794', 'concerns' ); ?></span>
              <h3 class="pane-products-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_m_7b8f8f082a7a', 'concerns' ); ?></h3>
              <p class="pane-products-desc"><?php echo project_child_fcc_text( 'fcc_ui_m_94aa9ca1ca92', 'concerns' ); ?></p>
            </div>
            <?php get_template_part( 'template-parts/fractional/components/concern-regimen', null, array( 'concern' => 'lo-chan-long' ) ); ?>
          </div>
        </div>
      </div>

      <!-- PANE 03: LÃO HÓA & NẾP NHĂN -->
      <div class="concern-tab-pane" id="pane-lao-hoa" role="tabpanel">
        <div class="concern-pane-card">
          <!-- PHẦN 1: TỔNG QUAN TÌNH TRẠNG & CƠ CHẾ KHOA HỌC (2 CỘT CÂN ĐỐI) -->
          <div class="concern-overview-grid">
            <!-- Cột Trái: Thông tin tình trạng, mô tả nỗi đau & ảnh dấu hiệu -->
            <div class="concern-visual-col">
              <div class="concern-info-header">
                <span class="concern-tag"><?php echo project_child_fcc_text( 'fcc_ui_m_512462f7367e', 'concerns' ); ?></span>
                <h2 class="heading-serif concern-pane-heading"><?php echo project_child_fcc_text( 'fcc_ui_m_5d389bb25f5c', 'concerns' ); ?></h2>
                <p class="concern-pain-quote"><?php echo project_child_fcc_text( 'fcc_ui_m_249a0482da6f', 'concerns' ); ?></p>
              </div>
              <div class="concern-visual-card">
                <div class="concern-img-frame">
                  <?php if ( project_child_fcc_image( 'fcc_concerns_lao_hoa_image', 'concerns' ) ) : ?><img src="<?php echo esc_url( project_child_fcc_image( 'fcc_concerns_lao_hoa_image', 'concerns' ) ); ?>" alt="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_m_dc5e405bfc31', 'concerns' ) ); ?>" class="concern-skin-img" width="519" height="640" loading="lazy"><?php endif; ?>
                </div>
                <div class="concern-visual-caption">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12.01" y2="8"></line><line x1="12" y1="12" x2="12" y2="16"></line></svg>
                  <span><?php echo project_child_fcc_text( 'fcc_ui_m_261246e91302', 'concerns' ); ?></span>
                </div>
              </div>
            </div>

            <!-- Cột Phải: Phân tích nguyên nhân & Giải pháp khoa học -->
            <div class="concern-science-col">
              <div class="concern-science-header">
                <span class="section-tag"><?php echo project_child_fcc_text( 'fcc_ui_m_76c6f45b84b1', 'concerns' ); ?></span>
                <h3 class="concern-science-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_m_d3dcadafdded', 'concerns' ); ?></h3>
                <p class="concern-science-desc"><?php echo project_child_fcc_text( 'fcc_ui_m_3eda3804b1c1', 'concerns' ); ?></p>
              </div>

              <div class="concern-detail-cards">
                <div class="concern-detail-card">
                  <h4 class="detail-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    <?php echo project_child_fcc_text( 'fcc_ui_m_75b59cceb48d', 'concerns' ); ?>
                  </h4>
                  <p><?php echo project_child_fcc_text( 'fcc_ui_m_3b83ce4eb80b', 'concerns' ); ?></p>
                </div>

                <div class="concern-detail-card highlight-card">
                  <h4 class="detail-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    <?php echo project_child_fcc_text( 'fcc_ui_m_653b87d4dfd4', 'concerns' ); ?>
                  </h4>
                  <p><?php echo project_child_fcc_text( 'fcc_ui_m_abb850c5218b', 'concerns' ); ?></p>
                </div>
              </div>
            </div>
          </div>

          <!-- PHẦN 2: GỢI Ý PHÁC ĐỒ & SẢN PHẨM CHỦ LỰC (TRẢI NGANG BÊN DƯỚI) -->
          <div class="concern-regimen-section">
            <div class="pane-products-header">
              <span class="section-tag"><?php echo project_child_fcc_text( 'fcc_ui_m_561888c8085f', 'concerns' ); ?></span>
              <h3 class="pane-products-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_m_69872d42c4cb', 'concerns' ); ?></h3>
              <p class="pane-products-desc"><?php echo project_child_fcc_text( 'fcc_ui_m_765a23fcf221', 'concerns' ); ?></p>
            </div>
            <?php get_template_part( 'template-parts/fractional/components/concern-regimen', null, array( 'concern' => 'lao-hoa' ) ); ?>
          </div>
        </div>
      </div>

      <!-- PANE 04: DA THÂM MỤN SAU VIÊM -->
      <div class="concern-tab-pane" id="pane-tham-mun" role="tabpanel">
        <div class="concern-pane-card">
          <!-- PHẦN 1: TỔNG QUAN TÌNH TRẠNG & CƠ CHẾ KHOA HỌC (2 CỘT CÂN ĐỐI) -->
          <div class="concern-overview-grid">
            <!-- Cột Trái: Thông tin tình trạng, mô tả nỗi đau & ảnh dấu hiệu -->
            <div class="concern-visual-col">
              <div class="concern-info-header">
                <span class="concern-tag"><?php echo project_child_fcc_text( 'fcc_ui_m_b9f7d946a2ec', 'concerns' ); ?></span>
                <h2 class="heading-serif concern-pane-heading"><?php echo project_child_fcc_text( 'fcc_ui_m_a04a8019b757', 'concerns' ); ?></h2>
                <p class="concern-pain-quote"><?php echo project_child_fcc_text( 'fcc_ui_m_32e2a12f43fa', 'concerns' ); ?></p>
              </div>
              <div class="concern-visual-card">
                <div class="concern-img-frame">
                  <?php if ( project_child_fcc_image( 'fcc_concerns_tham_mun_image', 'concerns' ) ) : ?><img src="<?php echo esc_url( project_child_fcc_image( 'fcc_concerns_tham_mun_image', 'concerns' ) ); ?>" alt="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_m_f1bf77b4dc2b', 'concerns' ) ); ?>" class="concern-skin-img" width="526" height="701" loading="lazy"><?php endif; ?>
                </div>
                <div class="concern-visual-caption">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12.01" y2="8"></line><line x1="12" y1="12" x2="12" y2="16"></line></svg>
                  <span><?php echo project_child_fcc_text( 'fcc_ui_m_3fa36d29fe26', 'concerns' ); ?></span>
                </div>
              </div>
            </div>

            <!-- Cột Phải: Phân tích nguyên nhân & Giải pháp khoa học -->
            <div class="concern-science-col">
              <div class="concern-science-header">
                <span class="section-tag"><?php echo project_child_fcc_text( 'fcc_ui_m_46a4e3691cea', 'concerns' ); ?></span>
                <h3 class="concern-science-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_m_8b887d541a3f', 'concerns' ); ?></h3>
                <p class="concern-science-desc"><?php echo project_child_fcc_text( 'fcc_ui_m_c9fd8e80cc6b', 'concerns' ); ?></p>
              </div>

              <div class="concern-detail-cards">
                <div class="concern-detail-card">
                  <h4 class="detail-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    <?php echo project_child_fcc_text( 'fcc_ui_m_442fda7a3914', 'concerns' ); ?>
                  </h4>
                  <p><?php echo project_child_fcc_text( 'fcc_ui_m_78efcb0d16cf', 'concerns' ); ?></p>
                </div>

                <div class="concern-detail-card highlight-card">
                  <h4 class="detail-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    <?php echo project_child_fcc_text( 'fcc_ui_m_6613aa307a5d', 'concerns' ); ?>
                  </h4>
                  <p><?php echo project_child_fcc_text( 'fcc_ui_m_8bdc88a333b9', 'concerns' ); ?></p>
                </div>
              </div>
            </div>
          </div>

          <!-- PHẦN 2: GỢI Ý PHÁC ĐỒ & SẢN PHẨM CHỦ LỰC (TRẢI NGANG BÊN DƯỚI) -->
          <div class="concern-regimen-section">
            <div class="pane-products-header">
              <span class="section-tag"><?php echo project_child_fcc_text( 'fcc_ui_m_be3346100dcf', 'concerns' ); ?></span>
              <h3 class="pane-products-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_m_cf4ae9d38b97', 'concerns' ); ?></h3>
              <p class="pane-products-desc"><?php echo project_child_fcc_text( 'fcc_ui_m_89120f97d182', 'concerns' ); ?></p>
            </div>



            <?php get_template_part( 'template-parts/fractional/components/concern-regimen', null, array( 'concern' => 'tham-mun' ) ); ?>
          </div>
        </div>
      </div>

    </div>

    <!-- 05. NGUYÊN TẮC CHĂM SÓC DA ĐÚNG CÁCH -->
    <section class="concerns-principles-section">
      <div class="principles-header">
        <span class="section-tag"><?php echo project_child_fcc_text( 'fcc_ui_m_02f1df1d5599', 'concerns' ); ?></span>
        <h2 class="heading-serif principles-title"><?php echo project_child_fcc_text( 'fcc_ui_m_acdc2bebc18e', 'concerns' ); ?></h2>
        <p class="principles-sub">
          <?php echo project_child_fcc_text( 'fcc_ui_m_bd7a8e23590b', 'concerns' ); ?>
        </p>
      </div>

      <div class="principles-grid">
        <div class="principle-item">
          <div class="principle-num">01</div>
          <h4 class="principle-item-title"><?php echo project_child_fcc_text( 'fcc_ui_m_dd8055ab472e', 'concerns' ); ?></h4>
          <p class="principle-item-desc">
            <?php echo project_child_fcc_text( 'fcc_ui_m_6f7efa3c3475', 'concerns' ); ?>
          </p>
        </div>

        <div class="principle-item">
          <div class="principle-num">02</div>
          <h4 class="principle-item-title"><?php echo project_child_fcc_text( 'fcc_ui_m_c7586281721f', 'concerns' ); ?></h4>
          <p class="principle-item-desc">
            <?php echo project_child_fcc_text( 'fcc_ui_m_a1c0ddeffd83', 'concerns' ); ?>
          </p>
        </div>

        <div class="principle-item">
          <div class="principle-num">03</div>
          <h4 class="principle-item-title"><?php echo project_child_fcc_text( 'fcc_ui_m_a9b77c4c9196', 'concerns' ); ?></h4>
          <p class="principle-item-desc">
            <?php echo project_child_fcc_text( 'fcc_ui_m_43e1f28e55cf', 'concerns' ); ?>
          </p>
        </div>
      </div>
    </section>

    <!-- 06. KHỐI TƯ VẤN & HỖ TRỢ TRỰC TIẾP -->
    <section class="concerns-consultation-banner">
      <div class="consultation-banner-inner">
        <span class="badge-pill badge-gold" style="margin-bottom: 12px; background: rgba(223, 186, 115, 0.15); color: #DFBA73;"><?php echo project_child_fcc_text( 'fcc_ui_m_24517189950b', 'concerns' ); ?></span>
        <h2 class="heading-serif consultation-banner-title">
          <?php echo project_child_fcc_text( 'fcc_ui_m_11c8950d9cde', 'concerns' ); ?>
        </h2>
        <p class="consultation-banner-desc">
          <?php echo project_child_fcc_text( 'fcc_ui_m_74f09950bc89', 'concerns' ); ?>
        </p>

        <div class="consultation-banner-actions">
          <button class="btn btn-primary" data-open-order>
            <span><?php echo project_child_fcc_text( 'fcc_ui_m_1e850dd54933', 'concerns' ); ?></span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
          </button>
          <a href="<?php echo esc_url( project_child_fcc_link( 'https://zalo.me/' . $hotline ) ); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-consult-zalo">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.477 2 2 6.03 2 11C2 13.565 3.195 15.864 5.12 17.472L4.2 21.2C4.085 21.664 4.545 22.052 4.965 21.848L9.29 19.742C10.158 19.911 11.063 20 12 20C17.523 20 22 15.97 22 11C22 6.03 17.523 2 12 2Z"></path></svg>
            <span><?php echo project_child_fcc_text( 'fcc_ui_m_32e4397e39fe', 'concerns' ); ?></span>
          </a>
          <a href="<?php echo esc_url( project_child_fcc_link( 'tel:' . $hotline ) ); ?>" class="btn btn-consult-phone">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
            <span><?php echo project_child_fcc_text( 'fcc_ui_m_1fd1da925638', 'concerns' ); ?> <?php echo esc_html( $hotline_display ); ?></span>
          </a>
        </div>
      </div>
    </section>

  </div>
</div>
