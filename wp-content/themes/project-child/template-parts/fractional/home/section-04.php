<?php 
defined( 'ABSPATH' ) || exit; 

$home_id = function_exists( 'project_core_fcc_page_id' ) ? project_core_fcc_page_id( 'home' ) : 0;

$breakthrough_img_url = project_child_fcc_image( 'fcc_ui_mechanism_breakthrough_image', 'home' );
$routine_img_url = project_child_fcc_image( 'fcc_ui_mechanism_routine_image', 'home' );

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
      <article class="feature-pillar-card is-active" data-pillar-index="1" role="button" tabindex="0" aria-expanded="true">
        <div class="feature-pillar-header">
          <span class="feature-pillar-number">01</span>
          <h3><?php echo project_child_fcc_text( 'fcc_ui_cec6ed03283ed4', 'home' ); ?></h3>
          <span class="feature-pillar-indicator" aria-hidden="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
          </span>
        </div>
        <div class="feature-pillar-body">
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
          <p><?php echo project_child_fcc_text( 'fcc_ui_m_03206086d152', 'home' ); ?></p>
        </div>
      </article>

      <!-- Trụ cột 2: Thành phần nổi bật (Mục 4.1) -->
      <article class="feature-pillar-card" data-pillar-index="2" role="button" tabindex="0" aria-expanded="false">
        <div class="feature-pillar-header">
          <span class="feature-pillar-number">02</span>
          <h3><?php echo project_child_fcc_text( 'fcc_ui_d59186701a2723', 'home' ); ?></h3>
          <span class="feature-pillar-indicator" aria-hidden="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
          </span>
        </div>
        <div class="feature-pillar-body">
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
          <p><?php echo project_child_fcc_text( 'fcc_ui_m_6fc8b85fa590', 'home' ); ?></p>
        </div>
      </article>

      <!-- Trụ cột 3: Trải nghiệm sử dụng (Mục 4.3) -->
      <article class="feature-pillar-card" data-pillar-index="3" role="button" tabindex="0" aria-expanded="false">
        <div class="feature-pillar-header">
          <span class="feature-pillar-number">03</span>
          <h3><?php echo project_child_fcc_text( 'fcc_ui_a2860f76a73615', 'home' ); ?></h3>
          <span class="feature-pillar-indicator" aria-hidden="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
          </span>
        </div>
        <div class="feature-pillar-body">
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
          <p><?php echo project_child_fcc_text( 'fcc_ui_m_1531ec996f1e', 'home' ); ?></p>
        </div>
      </article>

      <!-- Trụ cột 4: Danh mục chăm sóc da (Mục 4.4) -->
      <article class="feature-pillar-card" data-pillar-index="4" role="button" tabindex="0" aria-expanded="false">
        <div class="feature-pillar-header">
          <span class="feature-pillar-number">04</span>
          <h3><?php echo project_child_fcc_text( 'fcc_ui_809905e43e48c3', 'home' ); ?></h3>
          <span class="feature-pillar-indicator" aria-hidden="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
          </span>
        </div>
        <div class="feature-pillar-body">
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
          <p><?php echo project_child_fcc_text( 'fcc_ui_m_c2cd8def125e', 'home' ); ?></p>
        </div>
      </article>

    </div>

    <!-- TẦNG 2: CHI TIẾT CÔNG NGHỆ & THÀNH PHẦN NỔI BẬT (MỤC 4.1 & 4.2 TRONG BRIEF) -->
    <div class="fcc-deepdive-card fcc-deepdive-breakthrough">
      <div class="fcc-deepdive-grid">
        <div class="fcc-deepdive-visual">
          <div class="fcc-visual-frame">
            <?php
            if ( $breakthrough_img_url ) {
                echo project_child_fcc_picture_html(
                    $breakthrough_img_url,
                    project_child_fcc_raw_text( 'fcc_ui_m_7a2188a8a844', 'home' ),
                    'fcc-deepdive-img',
                    array( 'width' => 540, 'height' => 360, 'loading' => 'lazy' )
                );
            }
            ?>
          </div>
          <p class="fcc-visual-caption"><?php echo project_child_fcc_text( 'fcc_ui_m_fc2ea2b80d3f', 'home' ); ?></p>
        </div>

        <div class="fcc-deepdive-content">
          <div class="fcc-deepdive-badge-tag"><?php echo project_child_fcc_text( 'fcc_ui_m_cedd7a76f83f', 'home' ); ?></div>
          <h3 class="fcc-deepdive-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_m_dc1f79aa308a', 'home' ); ?></h3>
          <p class="fcc-deepdive-lead">
            <?php echo project_child_fcc_text( 'fcc_ui_m_f56226b10a9c', 'home' ); ?> <strong><?php echo project_child_fcc_text( 'fcc_ui_m_492d6c32126b', 'home' ); ?></strong><?php echo project_child_fcc_text( 'fcc_ui_m_6b4df3f10347', 'home' ); ?>
          </p>

          <div class="fcc-ingredient-pills-wrap">
            <div class="fcc-ing-pill">
              <div class="fcc-ing-pill-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path></svg>
              </div>
              <div class="fcc-ing-pill-text">
                <h4><?php echo project_child_fcc_text( 'fcc_ui_m_26fb250cfecd', 'home' ); ?></h4>
                <p><?php echo project_child_fcc_text( 'fcc_ui_m_6515f3a1f865', 'home' ); ?></p>
              </div>
            </div>

            <div class="fcc-ing-pill">
              <div class="fcc-ing-pill-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
              </div>
              <div class="fcc-ing-pill-text">
                <h4><?php echo project_child_fcc_text( 'fcc_ui_m_11f49cae9df5', 'home' ); ?></h4>
                <p><?php echo project_child_fcc_text( 'fcc_ui_m_5eba64a2dc1f', 'home' ); ?></p>
              </div>
            </div>

            <div class="fcc-ing-pill">
              <div class="fcc-ing-pill-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
              </div>
              <div class="fcc-ing-pill-text">
                <h4><?php echo project_child_fcc_text( 'fcc_ui_m_0bd5bb62a9bc', 'home' ); ?></h4>
                <p><?php echo project_child_fcc_text( 'fcc_ui_m_c4429fdee486', 'home' ); ?></p>
              </div>
            </div>
          </div>

          <div class="fcc-ax4-callout-card">
            <div class="fcc-ax4-callout-badge">
              <span class="fcc-ax4-tag"><?php echo project_child_fcc_text( 'fcc_ui_m_335f689dce84', 'home' ); ?></span>
              <span class="fcc-ax4-num"><?php echo project_child_fcc_text( 'fcc_ui_m_c193669975d2', 'home' ); ?></span>
            </div>
            <p class="fcc-ax4-callout-desc">
              <?php echo project_child_fcc_text( 'fcc_ui_m_643b64fca7c8', 'home' ); ?> <strong><?php echo project_child_fcc_text( 'fcc_ui_m_5cf4fd1cc7b3', 'home' ); ?></strong> <?php echo project_child_fcc_text( 'fcc_ui_m_1604c8058164', 'home' ); ?> <strong><?php echo project_child_fcc_text( 'fcc_ui_m_61782c8b11bb', 'home' ); ?></strong> <?php echo project_child_fcc_text( 'fcc_ui_m_44659c22eda2', 'home' ); ?> <strong><?php echo project_child_fcc_text( 'fcc_ui_m_8d4feacdb3fc', 'home' ); ?></strong> <?php echo project_child_fcc_text( 'fcc_ui_m_5eca3007bb75', 'home' ); ?>
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- TẦNG 3: TRẢI NGHIỆM SỬ DỤNG & LƯU Ý (MỤC 4.3 TRONG BRIEF) -->
    <div class="fcc-deepdive-card fcc-deepdive-routine">
      <div class="fcc-deepdive-grid fcc-deepdive-grid-reversed">
        <div class="fcc-deepdive-content">
          <div class="fcc-deepdive-badge-tag"><?php echo project_child_fcc_text( 'fcc_ui_m_ab30ba7fd2c0', 'home' ); ?></div>
          <h3 class="fcc-deepdive-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_m_3e4fdc742dfd', 'home' ); ?></h3>
          <p class="fcc-deepdive-lead">
            <?php echo project_child_fcc_text( 'fcc_ui_m_777a11e43efc', 'home' ); ?>
          </p>

          <div class="fcc-experience-steps-wrap">
            <!-- 01: Kết cấu sản phẩm -->
            <div class="fcc-exp-step-item">
              <div class="fcc-exp-step-num">01</div>
              <div class="fcc-exp-step-info">
                <h4><?php echo project_child_fcc_text( 'fcc_ui_m_exp_tex_title', 'home', false ) ?: 'Kết cấu đặc trưng'; ?></h4>
                <p><?php echo project_child_fcc_text( 'fcc_ui_m_exp_tex_desc', 'home', false ) ?: 'Tùy từng dòng serum, sản phẩm mang lại cảm giác đặc trưng khi thoa do chứa thành phần vi kim mỹ phẩm sinh học từ bọt biển thủy phân (Hydrolyzed Sponge).'; ?></p>
              </div>
            </div>

            <!-- 02: Cảm giác trên da -->
            <div class="fcc-exp-step-item">
              <div class="fcc-exp-step-num">02</div>
              <div class="fcc-exp-step-info">
                <h4><?php echo project_child_fcc_text( 'fcc_ui_m_9e881650e66d', 'home' ); ?></h4>
                <p><?php echo project_child_fcc_text( 'fcc_ui_m_46bc3f224820', 'home' ); ?></p>
              </div>
            </div>

            <!-- 03: Cách dùng & Vị trí -->
            <div class="fcc-exp-step-item">
              <div class="fcc-exp-step-num">03</div>
              <div class="fcc-exp-step-info">
                <h4><?php echo project_child_fcc_text( 'fcc_ui_m_90436c65261d', 'home' ); ?></h4>
                <p><?php echo project_child_fcc_text( 'fcc_ui_m_6f4b3c1262c8', 'home' ); ?></p>
              </div>
            </div>

            <!-- 04: Lưu ý khi sử dụng -->
            <div class="fcc-exp-step-item">
              <div class="fcc-exp-step-num">04</div>
              <div class="fcc-exp-step-info">
                <h4><?php echo project_child_fcc_text( 'fcc_ui_m_a573c3d5d316', 'home' ); ?></h4>
                <p><?php echo project_child_fcc_text( 'fcc_ui_m_8b213687c068', 'home' ); ?></p>
              </div>
            </div>
          </div>

          <!-- Lời khuyên thao tác từ chuyên gia -->
          <div class="fcc-exp-note-bar">
            <span class="fcc-exp-note-icon" aria-hidden="true">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
            </span>
            <p><strong>Thao tác chuẩn Nhật:</strong> Áp nhẹ và vỗ đều lòng bàn tay khắp mặt để hạt vi kim tự nhiên đi sâu vào lớp sừng, kích hoạt hiệu quả tái tạo tối đa.</p>
          </div>
        </div>

        <div class="fcc-deepdive-visual">
          <div class="fcc-visual-frame">
            <?php
            if ( $routine_img_url ) {
                echo project_child_fcc_picture_html(
                    $routine_img_url,
                    project_child_fcc_raw_text( 'fcc_ui_m_722d801f8aa1', 'home' ),
                    'fcc-deepdive-img',
                    array( 'width' => 540, 'height' => 540, 'loading' => 'lazy' )
                );
            }
            ?>
          </div>
          <p class="fcc-visual-caption"><?php echo project_child_fcc_text( 'fcc_ui_m_be68bc224ffb', 'home' ); ?></p>
        </div>
      </div>
    </div>

  </div>
</section>
