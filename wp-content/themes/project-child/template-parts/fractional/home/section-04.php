<?php 
defined( 'ABSPATH' ) || exit; 

$home_id = function_exists( 'project_core_fcc_page_id' ) ? project_core_fcc_page_id( 'home' ) : 0;

$breakthrough_img_id  = $home_id ? absint( get_post_meta( $home_id, 'fcc_ui_mechanism_breakthrough_image', true ) ) : 0;
$breakthrough_img_url = $breakthrough_img_id ? wp_get_attachment_image_url( $breakthrough_img_id, 'full' ) : '';
if ( ! $breakthrough_img_url ) {
    $breakthrough_img_url = project_child_fcc_asset( 'assets/images/mechanism/mechanism-breakthrough.jpg' );
}

$routine_img_id  = $home_id ? absint( get_post_meta( $home_id, 'fcc_ui_mechanism_routine_image', true ) ) : 0;
$routine_img_url = $routine_img_id ? wp_get_attachment_image_url( $routine_img_id, 'full' ) : '';
if ( ! $routine_img_url ) {
    $routine_img_url = project_child_fcc_asset( 'assets/images/mechanism/mechanism-skincare-routine.jpg' );
}

$s04_tag   = project_child_fcc_text( 'fcc_ui_ec3420476525fd', 'home' );
$s04_title = project_child_fcc_text( 'fcc_ui_e72702f2e64e16', 'home' );
$s04_desc  = project_child_fcc_text( 'fcc_ui_80ae9607363601', 'home' );
?>
<section class="section" id="diem-noi-bat">
  <div class="container">
    <div class="section-header">
      <span class="section-tag"><?php echo $s04_tag; ?></span>
      <h2 class="section-title heading-serif"><?php echo $s04_title; ?></h2>
      <p class="section-desc"><?php echo $s04_desc; ?></p>
    </div>

    <!-- TẦNG 1: 4 TRỤ CỘT BÁM SÁT MỤC 4.1 - 4.4 TRONG TÀI LIỆU -->
    <div class="brief-feature-grid">
      
      <!-- Trụ cột 1: Công nghệ vi kim (Mục 4.2) -->
      <article class="feature-pillar-card">
        <span class="feature-pillar-number">01</span>
        <div class="feature-pillar-thumb">
          <svg width="58" height="58" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="pillar-svg-icon" aria-hidden="true">
            <path d="M12 44C18 42.5 26 43 32 44C38 45 46 44.5 52 44" stroke="#121212" stroke-width="2" stroke-linecap="round" stroke-dasharray="2 3"></path>
            <path d="M32 10L36 32L32 50L28 32L32 10Z" fill="rgba(197, 160, 89, 0.15)" stroke="#C5A059" stroke-width="2.2" stroke-linejoin="round"></path>
            <line x1="32" y1="10" x2="32" y2="50" stroke="#C5A059" stroke-width="1.5" stroke-linecap="round"></line>
            <path d="M20 22L23 35L20 45L17 35L20 22Z" fill="rgba(197, 160, 89, 0.08)" stroke="#C5A059" stroke-width="1.8" stroke-linejoin="round"></path>
            <path d="M44 22L47 35L44 45L41 35L44 22Z" fill="rgba(197, 160, 89, 0.08)" stroke="#C5A059" stroke-width="1.8" stroke-linejoin="round"></path>
            <circle cx="32" cy="54" r="2" fill="#C5A059"></circle>
            <circle cx="20" cy="49" r="1.5" fill="#C5A059"></circle>
            <circle cx="44" cy="49" r="1.5" fill="#C5A059"></circle>
            <path d="M32 6L33 8.5L35.5 9.5L33 10.5L32 13L31 10.5L28.5 9.5L31 8.5L32 6Z" fill="#C5A059"></path>
          </svg>
        </div>
        <h3><?php echo project_child_fcc_text( 'fcc_ui_cec6ed03283ed4', 'home' ); ?></h3>
        <p>Ứng dụng công nghệ vi kim mỹ phẩm với các vi cấu trúc từ bọt biển thủy phân (Hydrolyzed Sponge), kết hợp cùng các thành phần dưỡng da trong công thức riêng của từng sản phẩm.</p>
      </article>

      <!-- Trụ cột 2: Thành phần nổi bật (Mục 4.1) -->
      <article class="feature-pillar-card">
        <span class="feature-pillar-number">02</span>
        <div class="feature-pillar-thumb">
          <svg width="58" height="58" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="pillar-svg-icon" aria-hidden="true">
            <path d="M32 8C32 8 16 26.5 16 38C16 46.837 23.163 54 32 54C40.837 54 48 46.837 48 38C48 26.5 32 8 32 8Z" stroke="#C5A059" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" fill="rgba(197, 160, 89, 0.08)"></path>
            <circle cx="32" cy="38" r="7" stroke="#121212" stroke-width="2" fill="#FFFFFF"></circle>
            <circle cx="32" cy="38" r="3" fill="#C5A059"></circle>
            <circle cx="24" cy="30" r="2.5" fill="#C5A059"></circle>
            <circle cx="40" cy="30" r="2.5" fill="#C5A059"></circle>
            <circle cx="32" cy="48" r="2.5" fill="#C5A059"></circle>
            <line x1="26" y1="32" x2="28" y2="34" stroke="#C5A059" stroke-width="1.5" stroke-linecap="round"></line>
            <line x1="38" y1="32" x2="36" y2="34" stroke="#C5A059" stroke-width="1.5" stroke-linecap="round"></line>
            <line x1="32" y1="41" x2="32" y2="45.5" stroke="#C5A059" stroke-width="1.5" stroke-linecap="round"></line>
            <path d="M48 16L49.5 20.5L54 22L49.5 23.5L48 28L46.5 23.5L42 22L46.5 20.5L48 16Z" fill="#C5A059"></path>
          </svg>
        </div>
        <h3><?php echo project_child_fcc_text( 'fcc_ui_d59186701a2723', 'home' ); ?></h3>
        <p>Kết hợp PDRN (DNA-Na từ cá hồi), Retinol và NMN theo công thức riêng của từng dòng sản phẩm nhằm hỗ trợ chăm sóc độ ẩm, độ mịn và các dấu hiệu lão hóa.</p>
      </article>

      <!-- Trụ cột 3: Trải nghiệm sử dụng (Mục 4.3) -->
      <article class="feature-pillar-card">
        <span class="feature-pillar-number">03</span>
        <div class="feature-pillar-thumb">
          <svg width="58" height="58" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="pillar-svg-icon" aria-hidden="true">
            <ellipse cx="32" cy="42" rx="20" ry="8" stroke="#E5DDCB" stroke-width="1.5" stroke-linecap="round"></ellipse>
            <ellipse cx="32" cy="42" rx="13" ry="5.2" stroke="#C5A059" stroke-width="1.8" stroke-linecap="round" stroke-dasharray="3 2"></ellipse>
            <path d="M16 26C24 20 38 18 48 24C42 30 32 34 22 32" stroke="#121212" stroke-width="2" stroke-linecap="round" fill="none"></path>
            <path d="M32 14C32 14 26 23 26 27.5C26 30.8 28.7 33.5 32 33.5C35.3 33.5 38 30.8 38 27.5C38 23 32 14 32 14Z" fill="rgba(197, 160, 89, 0.15)" stroke="#C5A059" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            <circle cx="33.5" cy="26" r="1.5" fill="#FFFFFF"></circle>
            <path d="M46 12L47.2 15L50.2 16.2L47.2 17.4L46 20.4L44.8 17.4L41.8 16.2L44.8 15L46 12Z" fill="#C5A059"></path>
          </svg>
        </div>
        <h3><?php echo project_child_fcc_text( 'fcc_ui_a2860f76a73615', 'home' ); ?></h3>
        <p>Có thể xuất hiện cảm giác châm chích nhẹ khi sử dụng; thoa trên da sạch trước bước dưỡng ẩm, làm quen từ từ và kết hợp kem chống nắng vào ban ngày.</p>
      </article>

      <!-- Trụ cột 4: Danh mục chăm sóc da (Mục 4.4) -->
      <article class="feature-pillar-card">
        <span class="feature-pillar-number">04</span>
        <div class="feature-pillar-thumb">
          <svg width="58" height="58" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="pillar-svg-icon" aria-hidden="true">
            <circle cx="32" cy="32" r="22" stroke="#E5DDCB" stroke-width="1.5" stroke-dasharray="3 3"></circle>
            <rect x="23" y="15" width="18" height="18" rx="4" stroke="#C5A059" stroke-width="2" transform="rotate(45 32 24)" fill="rgba(197, 160, 89, 0.1)"></rect>
            <rect x="23" y="31" width="18" height="18" rx="4" stroke="#121212" stroke-width="2" transform="rotate(45 32 40)" fill="rgba(18, 18, 18, 0.05)"></rect>
            <circle cx="32" cy="32" r="4.5" fill="#FFFFFF" stroke="#C5A059" stroke-width="2"></circle>
            <circle cx="32" cy="32" r="2" fill="#C5A059"></circle>
            <circle cx="32" cy="10" r="2.5" fill="#C5A059"></circle>
            <circle cx="54" cy="32" r="2.5" fill="#C5A059"></circle>
            <circle cx="32" cy="54" r="2.5" fill="#C5A059"></circle>
            <circle cx="10" cy="32" r="2.5" fill="#C5A059"></circle>
            <line x1="32" y1="13" x2="32" y2="17" stroke="#C5A059" stroke-width="1.5" stroke-linecap="round"></line>
            <line x1="51" y1="32" x2="47" y2="32" stroke="#C5A059" stroke-width="1.5" stroke-linecap="round"></line>
            <line x1="32" y1="51" x2="32" y2="47" stroke="#C5A059" stroke-width="1.5" stroke-linecap="round"></line>
            <line x1="13" y1="32" x2="17" y2="32" stroke="#C5A059" stroke-width="1.5" stroke-linecap="round"></line>
          </svg>
        </div>
        <h3><?php echo project_child_fcc_text( 'fcc_ui_809905e43e48c3', 'home' ); ?></h3>
        <p>Các dòng tẩy da chết Mandelic, Needle Serum A/C/AX4/CX4, mặt nạ, PDRN Shot và dưỡng môi phục vụ những nhu cầu chăm sóc da khác nhau.</p>
      </article>

    </div>

    <!-- TẦNG 2: CHI TIẾT CÔNG NGHỆ & THÀNH PHẦN NỔI BẬT (MỤC 4.1 & 4.2 TRONG BRIEF) -->
    <div class="fcc-deepdive-card fcc-deepdive-breakthrough">
      <div class="fcc-deepdive-grid">
        <div class="fcc-deepdive-visual">
          <div class="fcc-visual-frame">
            <img src="<?php echo esc_url( $breakthrough_img_url ); ?>" 
                 alt="Công nghệ vi kim mỹ phẩm và các thành phần chăm sóc da Fractional CC" 
                 loading="lazy" 
                 class="fcc-deepdive-img">
            <div class="fcc-visual-badge">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
              <span>Tài liệu tham khảo: Công nghệ &amp; Thành phần</span>
            </div>
          </div>
          <p class="fcc-visual-caption">Hình ảnh minh họa: Công nghệ vi kim và các thành phần chăm sóc da theo tài liệu thương hiệu</p>
        </div>

        <div class="fcc-deepdive-content">
          <div class="fcc-deepdive-badge-tag">CÔNG NGHỆ &amp; THÀNH PHẦN NỔI BẬT</div>
          <h3 class="fcc-deepdive-title heading-serif">Công Nghệ Vi Kim Mỹ Phẩm &amp; Thành Phần Theo Từng Nhu Cầu</h3>
          <p class="fcc-deepdive-lead">
            Fractional CC ứng dụng công nghệ vi kim mỹ phẩm với các vi cấu trúc có nguồn gốc từ <strong>bọt biển thủy phân (Hydrolyzed Sponge)</strong>, kết hợp cùng các thành phần dưỡng da trong công thức riêng của từng sản phẩm. Mang đến trải nghiệm chăm sóc da khác biệt so với serum thông thường, hỗ trợ cải thiện vẻ ngoài của làn da, từ độ sáng, độ mịn đến độ đàn hồi và dấu hiệu lão hóa.
          </p>

          <div class="fcc-ingredient-pills-wrap">
            <div class="fcc-ing-pill">
              <div class="fcc-ing-pill-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path></svg>
              </div>
              <div class="fcc-ing-pill-text">
                <h4>PDRN – Chăm sóc da chuyên sâu</h4>
                <p>Hỗ trợ duy trì độ ẩm, cải thiện vẻ ngoài khỏe khoắn và độ mịn màng của làn da. Nổi bật trong dòng Needle Serum AX4 với thành phần DNA-Na, được thương hiệu giới thiệu là Salmon PDRN.</p>
              </div>
            </div>

            <div class="fcc-ing-pill">
              <div class="fcc-ing-pill-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
              </div>
              <div class="fcc-ing-pill-text">
                <h4>Retinol – Chăm sóc da lão hóa</h4>
                <p>Hỗ trợ cải thiện bề mặt da, độ mịn và các dấu hiệu lão hóa. Phù hợp với chu trình chăm sóc da hướng đến làn da trông săn chắc, tươi trẻ hơn.</p>
              </div>
            </div>

            <div class="fcc-ing-pill">
              <div class="fcc-ing-pill-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
              </div>
              <div class="fcc-ing-pill-text">
                <h4>NMN – Bổ sung vào chu trình chăm sóc da</h4>
                <p>Thành phần NMN có mặt trong công thức Needle Serum A và AX4, kết hợp cùng các thành phần dưỡng da khác nhằm hỗ trợ chăm sóc làn da có dấu hiệu tuổi tác.</p>
              </div>
            </div>
          </div>

          <div class="fcc-ax4-callout-card">
            <div class="fcc-ax4-callout-badge">
              <span class="fcc-ax4-tag">THÔNG TIN SẢN PHẨM AX4</span>
              <span class="fcc-ax4-num">600.000 VI KIM</span>
            </div>
            <p class="fcc-ax4-callout-desc">
              Điểm nổi bật là dòng <strong>Needle Serum AX4</strong> được hãng giới thiệu có lượng vi kim gấp <strong>4 lần</strong> Needle Serum A, tương đương khoảng <strong>600.000 vi kim</strong> theo thông tin sản phẩm.
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- TẦNG 3: TRẢI NGHIỆM SỬ DỤNG & LƯU Ý (MỤC 4.3 TRONG BRIEF) -->
    <div class="fcc-deepdive-card fcc-deepdive-routine">
      <div class="fcc-deepdive-grid fcc-deepdive-grid-reversed">
        <div class="fcc-deepdive-content">
          <div class="fcc-deepdive-badge-tag">TRẢI NGHIỆM SỬ DỤNG</div>
          <h3 class="fcc-deepdive-title heading-serif">Trải Nghiệm Sử Dụng, Cảm Giác Trên Da &amp; Vị Trí Skincare</h3>
          <p class="fcc-deepdive-lead">
            Tùy từng dòng serum, sản phẩm có thể mang lại cảm giác đặc trưng khi thoa do chứa thành phần vi kim mỹ phẩm. Dưới đây là những lưu ý và hướng dẫn theo tài liệu sản phẩm:
          </p>

          <div class="fcc-experience-steps-wrap">
            <div class="fcc-exp-step-item">
              <div class="fcc-exp-step-num">01</div>
              <div class="fcc-exp-step-info">
                <h4>Cảm giác trên da</h4>
                <p>Có thể xuất hiện cảm giác châm chích nhẹ khi sử dụng. Mức độ cảm nhận tùy thuộc vào sản phẩm và tình trạng da của mỗi người.</p>
              </div>
            </div>

            <div class="fcc-exp-step-item">
              <div class="fcc-exp-step-num">02</div>
              <div class="fcc-exp-step-info">
                <h4>Cách dùng</h4>
                <p>Thực hiện theo hướng dẫn riêng của từng sản phẩm; với serum vi kim, nên thoa trên da sạch trước bước dưỡng ẩm nếu hướng dẫn của hãng quy định như vậy.</p>
              </div>
            </div>

            <div class="fcc-exp-step-item">
              <div class="fcc-exp-step-num">03</div>
              <div class="fcc-exp-step-info">
                <h4>Lưu ý khi sử dụng</h4>
                <p>Làm quen từ từ, tránh vùng mắt và không sử dụng trên vùng da tổn thương. Kết hợp kem chống nắng vào ban ngày; ngừng sử dụng nếu kích ứng kéo dài.</p>
              </div>
            </div>
          </div>
        </div>

        <div class="fcc-deepdive-visual">
          <div class="fcc-visual-frame">
            <img src="<?php echo esc_url( $routine_img_url ); ?>" 
                 alt="Hướng dẫn sử dụng và chu trình chăm sóc da Fractional CC" 
                 loading="lazy" 
                 class="fcc-deepdive-img">
            <div class="fcc-visual-badge">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
              <span>Tài liệu tham khảo: Hướng dẫn sử dụng</span>
            </div>
          </div>
          <p class="fcc-visual-caption">Hình ảnh hướng dẫn: Chu trình chăm sóc da và thứ tự sử dụng theo tài liệu sản phẩm</p>
        </div>
      </div>
    </div>

    <!-- TẦNG 4: CHUYỂN TIẾP SANG DANH MỤC (MỤC 4.4 TRONG BRIEF) -->
    <div class="fcc-highlights-bottom-guide">
      <div class="fcc-bottom-guide-inner">
        <div class="fcc-bottom-guide-text">
          <span class="fcc-bottom-guide-tag">DANH MỤC CHĂM SÓC DA</span>
          <h4 class="fcc-bottom-guide-title">Các Dòng Sản Phẩm Phục Vụ Nhu Cầu Chăm Sóc Da Khác Nhau</h4>
          <p class="fcc-bottom-guide-desc">Từ tẩy tế bào chết Mandelic, các dòng Needle Serum A/C/AX4/CX4, mặt nạ vi kim, PDRN Shot đến tinh chất dưỡng môi phục vụ các nhu cầu chăm sóc da riêng biệt.</p>
        </div>
        <div class="fcc-bottom-guide-actions">
          <a href="#danh-muc" class="btn btn-gold fcc-scroll-btn">
            <span>Xem Danh Mục Sản Phẩm &amp; Combo</span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M7 13l5 5 5-5M12 4v14"/></svg>
          </a>
        </div>
      </div>
    </div>

  </div>
</section>
