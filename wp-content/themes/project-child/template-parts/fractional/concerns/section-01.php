<?php
/**
 * Skin Concerns & Scientific Solutions Section Template.
 * Redesigned: Tab-based interface for simplicity, clarity, and consistency.
 *
 * @package Project_Child
 */
defined( 'ABSPATH' ) || exit;

// Fetch products & combos from project-core
$product_mandelic   = function_exists( 'project_core_fcc_product' ) ? project_core_fcc_product( 28, true ) : array();
$product_needle_a   = function_exists( 'project_core_fcc_product' ) ? project_core_fcc_product( 40, true ) : array();
$product_needle_ax4 = function_exists( 'project_core_fcc_product' ) ? project_core_fcc_product( 47, true ) : array();
$product_needle_c   = function_exists( 'project_core_fcc_product' ) ? project_core_fcc_product( 60, true ) : array();
$product_needle_cx4 = function_exists( 'project_core_fcc_product' ) ? project_core_fcc_product( 65, true ) : array();
$product_pdrn       = function_exists( 'project_core_fcc_product' ) ? project_core_fcc_product( 72, true ) : array();

$combos      = function_exists( 'project_core_fcc_combos' ) ? project_core_fcc_combos() : array();
$combo_c_cx4 = ! empty( $combos[0] ) ? $combos[0] : null;

$hotline_display = function_exists( 'project_core_fcc_site' ) ? project_core_fcc_site( 'hotlineDisplay', '0985 561 862' ) : '0985 561 862';
$hotline         = function_exists( 'project_core_fcc_site' ) ? project_core_fcc_site( 'hotline', '0985561862' ) : '0985561862';
?>

<!-- BREADCRUMB & HERO HEADER -->
<section class="concerns-hero-header">
  <div class="container">
    <nav class="breadcrumb-nav" aria-label="Breadcrumb">
      <a href="<?php echo esc_url( project_child_fcc_link( 'index.html' ) ); ?>">Trang chủ</a>
      <span>›</span>
      <strong>Vấn đề làn da &amp; Giải pháp</strong>
    </nav>

    <div class="concerns-hero-content">
      <span class="badge-pill badge-gold" style="margin-bottom: 12px; display: inline-flex;">KHOA HỌC LÀN DA • CHUẨN SALON TOKYO</span>
      <h1 class="heading-serif concerns-hero-title">
        LÀN DA CỦA BẠN ĐANG GẶP NHỮNG VẤN ĐỀ GÌ?
      </h1>
      <p class="concerns-hero-desc">
        Bạn đã chăm sóc da mỗi ngày nhưng làn da vẫn chưa đạt được vẻ rạng rỡ như mong muốn? Khám phá nguyên nhân khoa học và phác đồ vi kim sinh học Fractional CC chuẩn Nhật Bản được thiết kế riêng biệt cho từng tình trạng da.
      </p>
    </div>
  </div>
</section>

<!-- MAIN CONTENT WRAPPER -->
<div class="concerns-body-wrap">
  <div class="container">

    <!-- TAB NAVIGATION -->
    <div class="concerns-tab-nav" role="tablist" aria-label="Chọn tình trạng da của bạn">
      <button class="concern-tab-btn active" data-tab="dom-nau" role="tab" aria-selected="true" aria-controls="pane-dom-nau">
        <span class="tab-btn-num">01</span>
        <span class="tab-btn-title">Đốm Nâu &amp; Không Đều Màu</span>
      </button>
      <button class="concern-tab-btn" data-tab="lo-chan-long" role="tab" aria-selected="false" aria-controls="pane-lo-chan-long">
        <span class="tab-btn-num">02</span>
        <span class="tab-btn-title">Lỗ Chân Lông To &amp; Da Sần</span>
      </button>
      <button class="concern-tab-btn" data-tab="lao-hoa" role="tab" aria-selected="false" aria-controls="pane-lao-hoa">
        <span class="tab-btn-num">03</span>
        <span class="tab-btn-title">Lão Hóa &amp; Nếp Nhăn</span>
      </button>
      <button class="concern-tab-btn" data-tab="tham-mun" role="tab" aria-selected="false" aria-controls="pane-tham-mun">
        <span class="tab-btn-num">04</span>
        <span class="tab-btn-title">Da Thâm Mụn Sau Viêm</span>
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
                <span class="concern-tag">SẮC TỐ &amp; ĐỘ RẠNG RỠ</span>
                <h2 class="heading-serif concern-pane-heading">Đốm Nâu, Nám Nông &amp; Da Không Đều Màu</h2>
                <p class="concern-pain-quote">“Những vùng da sậm màu khiến tổng thể gương mặt thiếu tươi tắn, bạn ngại để mặt mộc và phải phụ thuộc nhiều hơn vào lớp trang điểm.”</p>
              </div>
              <div class="concern-visual-card">
                <div class="concern-img-frame">
                  <img src="<?php echo esc_url( project_child_fcc_asset( 'assets/images/concerns/concern-dom-nau.png' ) ); ?>" alt="Đốm nâu, da không đều màu" class="concern-skin-img" width="526" height="806" loading="lazy">
                </div>
                <div class="concern-visual-caption">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12.01" y2="8"></line><line x1="12" y1="12" x2="12" y2="16"></line></svg>
                  <span>Dấu hiệu: Vùng sậm màu phân tán, da xỉn màu, thiếu vẻ tươi tắn</span>
                </div>
              </div>
            </div>

            <!-- Cột Phải: Phân tích nguyên nhân & Giải pháp khoa học -->
            <div class="concern-science-col">
              <div class="concern-science-header">
                <span class="section-tag">CƠ CHẾ KHOA HỌC</span>
                <h3 class="concern-science-title heading-serif">Nguyên Nhân Sinh Học &amp; Giải Pháp Chuẩn Nhật</h3>
                <p class="concern-science-desc">Hiểu rõ cơ chế ở tầng tế bào để lựa chọn phác đồ điều hòa biểu bì tối ưu.</p>
              </div>

              <div class="concern-detail-cards">
                <div class="concern-detail-card">
                  <h4 class="detail-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    Nguyên nhân từ góc nhìn khoa học
                  </h4>
                  <p>Tia UV và các gốc tự do kích hoạt tế bào hắc tố melanocyte sản sinh melanin phân tán không đều. Các serum Vitamin C dạng bôi thoa ngoài rất dễ bị oxy hóa trên bề mặt trước khi kịp tiếp cận tầng sinh sắc tố, khiến việc cải thiện màu da diễn ra chậm chạp.</p>
                </div>

                <div class="concern-detail-card highlight-card">
                  <h4 class="detail-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    Giải pháp vi kim Fractional CC
                  </h4>
                  <p>Hệ vi kim sinh học Hydrolyzed Sponge tạo hàng ngàn đường dẫn siêu vi đưa trực tiếp dẫn xuất Vitamin C nồng độ cao vào biểu bì, phân tán nhẹ nhàng mảng sẫm màu và hỗ trợ làm sáng đều tông da mà không gây lột tẩy bào mòn.</p>
                </div>
              </div>
            </div>
          </div>

          <!-- PHẦN 2: GỢI Ý PHÁC ĐỒ & SẢN PHẨM CHỦ LỰC (TRẢI NGANG BÊN DƯỚI) -->
          <div class="concern-regimen-section">
            <div class="pane-products-header">
              <span class="section-tag">GỢI Ý PHÁC ĐỒ</span>
              <h3 class="pane-products-title heading-serif">Sản Phẩm Chủ Lực Cho Da Đốm Nâu</h3>
              <p class="pane-products-desc">Liệu trình làm quen sáng da kết hợp tăng cường nồng độ vi kim chuyên sâu.</p>
            </div>
            <div class="concern-products-grid">
              <?php
              if ( ! empty( $product_needle_cx4 ) ) {
                get_template_part( 'template-parts/fractional/components/product-card', null, array( 'product' => $product_needle_cx4 ) );
              }
              if ( ! empty( $product_needle_c ) ) {
                get_template_part( 'template-parts/fractional/components/product-card', null, array( 'product' => $product_needle_c ) );
              }
              ?>
            </div>
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
                <span class="concern-tag">BỀ MẶT &amp; ĐỘ MỊN MÀNG</span>
                <h2 class="heading-serif concern-pane-heading">Lỗ Chân Lông To &amp; Bề Mặt Da Kém Mịn</h2>
                <p class="concern-pain-quote">“Lỗ chân lông to khiến bề mặt da kém mịn, lớp nền dễ lộ khuyết điểm, khiến làn da trông kém rạng rỡ dù bạn vẫn chăm sóc da đều đặn.”</p>
              </div>
              <div class="concern-visual-card">
                <div class="concern-img-frame">
                  <img src="<?php echo esc_url( project_child_fcc_asset( 'assets/images/concerns/concern-lo-chan-long.png' ) ); ?>" alt="Lỗ chân lông to và bề mặt da kém mịn" class="concern-skin-img" width="722" height="960" loading="lazy">
                </div>
                <div class="concern-visual-caption">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12.01" y2="8"></line><line x1="12" y1="12" x2="12" y2="16"></line></svg>
                  <span>Dấu hiệu: Bề mặt sần sùi, cổ nang lông giãn nở, lớp nền dễ mốc phấn</span>
                </div>
              </div>
            </div>

            <!-- Cột Phải: Phân tích nguyên nhân & Giải pháp khoa học -->
            <div class="concern-science-col">
              <div class="concern-science-header">
                <span class="section-tag">CƠ CHẾ KHOA HỌC</span>
                <h3 class="concern-science-title heading-serif">Nguyên Nhân Sinh Học &amp; Giải Pháp Chuẩn Nhật</h3>
                <p class="concern-science-desc">Hiểu rõ cơ chế ở tầng tế bào để lựa chọn phác đồ điều hòa biểu bì tối ưu.</p>
              </div>

              <div class="concern-detail-cards">
                <div class="concern-detail-card">
                  <h4 class="detail-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    Nguyên nhân từ góc nhìn khoa học
                  </h4>
                  <p>Bã nhờn tích tụ kết hợp tế bào chết ứ đọng tạo thành các nút sừng làm cổ nang lông giãn rộng cơ học. Khi bề mặt sần sùi, ánh sáng bị tán xạ hỗn loạn khiến làn da trông xỉn màu và lớp nền trang điểm dễ bị mốc hoặc đọng phấn.</p>
                </div>

                <div class="concern-detail-card highlight-card">
                  <h4 class="detail-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    Giải pháp vi kim Fractional CC
                  </h4>
                  <p>Quy trình làm sạch sâu cổ nang lông với acid phân tử lớn dịu nhẹ (Mandelic Acid) giải phóng bã nhờn bít tắc, phối hợp cùng tinh chất PDRN phục hồi giúp lấp đầy độ ẩm, thu nhỏ vẻ ngoài của lỗ chân lông.</p>
                </div>
              </div>
            </div>
          </div>

          <!-- PHẦN 2: GỢI Ý PHÁC ĐỒ & SẢN PHẨM CHỦ LỰC (TRẢI NGANG BÊN DƯỚI) -->
          <div class="concern-regimen-section">
            <div class="pane-products-header">
              <span class="section-tag">GỢI Ý PHÁC ĐỒ</span>
              <h3 class="pane-products-title heading-serif">Sản Phẩm Cho Lỗ Chân Lông &amp; Da Sần</h3>
              <p class="pane-products-desc">Quy trình làm sạch tế bào chết dịu nhẹ và cấp ẩm phục hồi căng bóng.</p>
            </div>
            <div class="concern-products-grid">
              <?php
              if ( ! empty( $product_mandelic ) ) {
                get_template_part( 'template-parts/fractional/components/product-card', null, array( 'product' => $product_mandelic ) );
              }
              if ( ! empty( $product_pdrn ) ) {
                get_template_part( 'template-parts/fractional/components/product-card', null, array( 'product' => $product_pdrn ) );
              }
              ?>
            </div>
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
                <span class="concern-tag">ĐỘ ĐÀN HỒI &amp; SĂN CHẮC</span>
                <h2 class="heading-serif concern-pane-heading">Da Lão Hóa, Nếp Nhăn &amp; Chùng Nhão</h2>
                <p class="concern-pain-quote">“Các dấu hiệu lão hóa dần xuất hiện, làn da không còn giữ được vẻ căng mịn, đàn hồi như trước.”</p>
              </div>
              <div class="concern-visual-card">
                <div class="concern-img-frame">
                  <img src="<?php echo esc_url( project_child_fcc_asset( 'assets/images/concerns/concern-lao-hoa.png' ) ); ?>" alt="Da lão hóa, nếp nhăn, chảy xệ" class="concern-skin-img" width="519" height="640" loading="lazy">
                </div>
                <div class="concern-visual-caption">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12.01" y2="8"></line><line x1="12" y1="12" x2="12" y2="16"></line></svg>
                  <span>Dấu hiệu: Rãnh nhăn nông quanh mắt &amp; khóe miệng, da giảm độ đàn hồi</span>
                </div>
              </div>
            </div>

            <!-- Cột Phải: Phân tích nguyên nhân & Giải pháp khoa học -->
            <div class="concern-science-col">
              <div class="concern-science-header">
                <span class="section-tag">CƠ CHẾ KHOA HỌC</span>
                <h3 class="concern-science-title heading-serif">Nguyên Nhân Sinh Học &amp; Giải Pháp Chuẩn Nhật</h3>
                <p class="concern-science-desc">Hiểu rõ cơ chế ở tầng tế bào để lựa chọn phác đồ điều hòa biểu bì tối ưu.</p>
              </div>

              <div class="concern-detail-cards">
                <div class="concern-detail-card">
                  <h4 class="detail-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    Nguyên nhân từ góc nhìn khoa học
                  </h4>
                  <p>Từ sau tuổi 25, mạng lưới collagen và elastin tự nhiên suy giảm dần đều. Chu kỳ thay mới tế bào da kéo dài hơn khiến các nếp nhăn biểu cảm ở khóe mắt, khóe miệng dần định hình rõ rệt, cấu trúc nâng đỡ dưới da chùng nhão.</p>
                </div>

                <div class="concern-detail-card highlight-card">
                  <h4 class="detail-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    Giải pháp vi kim Fractional CC
                  </h4>
                  <p>Sức mạnh từ công nghệ vi kim sinh học bọc hoạt chất vàng Retinol: Dòng Needle Serum AX4 sở hữu khoảng 600.000 vi kim (gấp 4 lần dòng A) kích hoạt phản xạ phục hồi nội sinh, hỗ trợ cải thiện độ săn chắc và làm mờ vẻ ngoài nếp nhăn.</p>
                </div>
              </div>
            </div>
          </div>

          <!-- PHẦN 2: GỢI Ý PHÁC ĐỒ & SẢN PHẨM CHỦ LỰC (TRẢI NGANG BÊN DƯỚI) -->
          <div class="concern-regimen-section">
            <div class="pane-products-header">
              <span class="section-tag">GỢI Ý PHÁC ĐỒ</span>
              <h3 class="pane-products-title heading-serif">Sản Phẩm Chủ Lực Chống Lão Hóa</h3>
              <p class="pane-products-desc">Liệu trình làm quen và tăng cường trẻ hóa da chuyên sâu với vi kim Retinol.</p>
            </div>
            <div class="concern-products-grid">
              <?php
              if ( ! empty( $product_needle_ax4 ) ) {
                get_template_part( 'template-parts/fractional/components/product-card', null, array( 'product' => $product_needle_ax4 ) );
              }
              if ( ! empty( $product_needle_a ) ) {
                get_template_part( 'template-parts/fractional/components/product-card', null, array( 'product' => $product_needle_a ) );
              }
              ?>
            </div>
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
                <span class="concern-tag">PHỤC HỒI &amp; MỜ VẾT THÂM</span>
                <h2 class="heading-serif concern-pane-heading">Da Thâm Mụn, Tổn Thương Sau Mụn</h2>
                <p class="concern-pain-quote">“Da thâm mụn, không đều màu khiến lớp nền khó tiệp vào da, dễ lộ khuyết điểm và kém tự nhiên, khiến bạn mất thêm thời gian che phủ mỗi khi trang điểm.”</p>
              </div>
              <div class="concern-visual-card">
                <div class="concern-img-frame">
                  <img src="<?php echo esc_url( project_child_fcc_asset( 'assets/images/concerns/concern-tham-mun.png' ) ); ?>" alt="Da thâm mụn, tổn thương sau mụn" class="concern-skin-img" width="526" height="701" loading="lazy">
                </div>
                <div class="concern-visual-caption">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12.01" y2="8"></line><line x1="12" y1="12" x2="12" y2="16"></line></svg>
                  <span>Dấu hiệu: Vết thâm sau mụn dai dẳng, da không đều màu, nền da mỏng yếu</span>
                </div>
              </div>
            </div>

            <!-- Cột Phải: Phân tích nguyên nhân & Giải pháp khoa học -->
            <div class="concern-science-col">
              <div class="concern-science-header">
                <span class="section-tag">CƠ CHẾ KHOA HỌC</span>
                <h3 class="concern-science-title heading-serif">Nguyên Nhân Sinh Học &amp; Giải Pháp Chuẩn Nhật</h3>
                <p class="concern-science-desc">Hiểu rõ cơ chế ở tầng tế bào để lựa chọn phác đồ điều hòa biểu bì tối ưu.</p>
              </div>

              <div class="concern-detail-cards">
                <div class="concern-detail-card">
                  <h4 class="detail-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    Nguyên nhân từ góc nhìn khoa học
                  </h4>
                  <p>Tổn thương sau phản ứng viêm mụn kích thích tích tụ sắc tố melanin và giãn mao mạch cục bộ. Nền da sau mụn rất mỏng yếu; nếu nôn nóng dùng sản phẩm lột tẩy nồng độ cao có thể gây kích ứng ngược, làm vết thâm sậm màu hơn và kéo dài dai dẳng.</p>
                </div>

                <div class="concern-detail-card highlight-card">
                  <h4 class="detail-card-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    Giải pháp vi kim Fractional CC
                  </h4>
                  <p>Phác đồ hiệp đồng bước đệm và bước tăng cường: Bắt đầu làm quen dịu nhẹ với Serum C để củng cố nền da, sau đó nâng cấp lên Serum CX4 để tác động tầng sâu vào các gốc thâm mụn, giúp đẩy nhanh chu trình mờ thâm một cách an toàn và bền vững.</p>
                </div>
              </div>
            </div>
          </div>

          <!-- PHẦN 2: GỢI Ý PHÁC ĐỒ & SẢN PHẨM CHỦ LỰC (TRẢI NGANG BÊN DƯỚI) -->
          <div class="concern-regimen-section">
            <div class="pane-products-header">
              <span class="section-tag">GỢI Ý PHÁC ĐỒ</span>
              <h3 class="pane-products-title heading-serif">Sản Phẩm &amp; Combo Giảm Thâm Mụn</h3>
              <p class="pane-products-desc">Liệu trình chuẩn kép kết hợp Serum C và Serum CX4 xóa mờ thâm tầng sâu.</p>
            </div>

            <?php if ( ! empty( $combo_c_cx4 ) ) : ?>
            <div class="concern-combo-card-compact">
              <div class="combo-compact-thumb">
                <img src="<?php echo esc_url( $combo_c_cx4['image'] ); ?>" alt="<?php echo esc_attr( $combo_c_cx4['name'] ); ?>" loading="lazy">
                <span class="combo-compact-badge">COMBO KHUYÊN DÙNG</span>
              </div>
              <div class="combo-compact-info">
                <h4 class="combo-compact-title"><?php echo esc_html( $combo_c_cx4['name'] ); ?></h4>
                <p class="combo-compact-desc">Bộ đôi chuẩn kép: Serum C làm quen sáng da + Serum CX4 tăng cường mờ thâm mụn tầng sâu.</p>
                <div class="combo-compact-price-row">
                  <span class="combo-price-now"><?php echo esc_html( project_child_fcc_price( $combo_c_cx4['salePrice'] ) ); ?></span>
                  <?php if ( ! empty( $combo_c_cx4['originalPrice'] ) ) : ?>
                    <span class="combo-price-was"><?php echo esc_html( project_child_fcc_price( $combo_c_cx4['originalPrice'] ) ); ?></span>
                  <?php endif; ?>
                </div>
                <div class="combo-compact-actions">
                  <button class="btn-card-buy" data-open-order data-product-select="<?php echo esc_attr( project_child_fcc_order_code( 'combo_C-CX4' ) ); ?>">Đặt mua Combo</button>
                  <a href="<?php echo esc_url( project_child_fcc_link( 'san-pham.html#danh-sach-combo' ) ); ?>" class="btn-card-view">Xem chi tiết</a>
                </div>
              </div>
            </div>
            <?php endif; ?>

            <div class="concern-products-grid">
              <?php
              if ( ! empty( $product_needle_cx4 ) ) {
                get_template_part( 'template-parts/fractional/components/product-card', null, array( 'product' => $product_needle_cx4 ) );
              }
              if ( ! empty( $product_needle_c ) ) {
                get_template_part( 'template-parts/fractional/components/product-card', null, array( 'product' => $product_needle_c ) );
              }
              ?>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- 05. NGUYÊN TẮC CHĂM SÓC DA ĐÚNG CÁCH -->
    <section class="concerns-principles-section">
      <div class="principles-header">
        <span class="section-tag">TRIẾT LÝ DƯỠNG DA TOKYO</span>
        <h2 class="heading-serif principles-title">3 NGUYÊN TẮC VÀNG ĐỂ CHĂM SÓC DA HIỆU QUẢ</h2>
        <p class="principles-sub">
          Làn da không thể thay đổi sau một đêm. Hiểu đúng và kiên nhẫn đồng hành cùng chu trình sinh học là chìa khóa để đạt kết quả bền lâu.
        </p>
      </div>

      <div class="principles-grid">
        <div class="principle-item">
          <div class="principle-num">01</div>
          <h4 class="principle-item-title">Làm quen từ tốn &amp; Lắng nghe phản hồi</h4>
          <p class="principle-item-desc">
            Với serum vi kim, cảm giác châm chích nhẹ là phản ứng tự nhiên khi vi kim sinh học tiếp xúc biểu bì. Hãy bắt đầu với tần suất 2–3 lần/tuần trước khi dùng hàng ngày.
          </p>
        </div>

        <div class="principle-item">
          <div class="principle-num">02</div>
          <h4 class="principle-item-title">Cấp ẩm &amp; Phục hồi đầy đủ</h4>
          <p class="principle-item-desc">
            Khi vi kim kích thích quá trình thay mới tế bào, da rất cần độ ẩm để tăng sinh. Hãy kết hợp kem dưỡng ẩm hoặc serum phục hồi (như PDRN) để duy trì màng ẩm mượt mà.
          </p>
        </div>

        <div class="principle-item">
          <div class="principle-num">03</div>
          <h4 class="principle-item-title">Bảo vệ quang phổ rộng ban ngày</h4>
          <p class="principle-item-desc">
            Bảo vệ thành quả dưỡng da bằng kem chống nắng SPF50+ PA++++ mỗi sáng để ngăn chặn tia UV kích hoạt lại hắc tố melanin trên nền da đang tái tạo.
          </p>
        </div>
      </div>
    </section>

    <!-- 06. KHỐI TƯ VẤN & HỖ TRỢ TRỰC TIẾP -->
    <section class="concerns-consultation-banner">
      <div class="consultation-banner-inner">
        <span class="badge-pill badge-gold" style="margin-bottom: 12px; background: rgba(223, 186, 115, 0.15); color: #DFBA73;">TƯ VẤN 1:1 MIỄN PHÍ TỪ BUI DANG-JP</span>
        <h2 class="heading-serif consultation-banner-title">
          BẠN CHƯA BIẾT LÀN DA MÌNH CẦN SẢN PHẨM NÀO?
        </h2>
        <p class="consultation-banner-desc">
          Mỗi làn da có cấu trúc và lịch sử chăm sóc riêng biệt. Hãy liên hệ với chuyên viên BUI DANG-JP để được soi xét tình trạng da và tư vấn phác đồ Fractional CC phù hợp nhất với bạn.
        </p>

        <div class="consultation-banner-actions">
          <button class="btn btn-primary" data-open-order>
            <span>ĐĂNG KÝ NHẬN TƯ VẤN DA</span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
          </button>
          <a href="<?php echo esc_url( project_child_fcc_link( 'https://zalo.me/' . $hotline ) ); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-consult-zalo">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.477 2 2 6.03 2 11C2 13.565 3.195 15.864 5.12 17.472L4.2 21.2C4.085 21.664 4.545 22.052 4.965 21.848L9.29 19.742C10.158 19.911 11.063 20 12 20C17.523 20 22 15.97 22 11C22 6.03 17.523 2 12 2Z"></path></svg>
            <span>Nhắn Zalo chuyên viên</span>
          </a>
          <a href="<?php echo esc_url( project_child_fcc_link( 'tel:' . $hotline ) ); ?>" class="btn btn-consult-phone">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
            <span>Hotline: <?php echo esc_html( $hotline_display ); ?></span>
          </a>
        </div>
      </div>
    </section>

  </div>
</div>
