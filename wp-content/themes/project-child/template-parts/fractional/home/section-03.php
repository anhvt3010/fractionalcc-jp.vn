<?php defined( 'ABSPATH' ) || exit; ?>
<section class="section section-brand-story" id="gioi-thieu">
      <div class="container">
        <div class="section-header">
          <span class="section-tag"><?php echo project_child_fcc_text( 'fcc_ui_0b97909fa7b3d5', 'home' ); ?></span>
          <h2 class="section-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_9be413a8b6650e', 'home' ); ?></h2>
          <?php 
          $s03_desc = project_child_fcc_text( 'fcc_ui_72e21cf4e1c4e6', 'home' );
          if ( empty( trim( wp_strip_all_tags( $s03_desc ) ) ) ) {
              $s03_desc = project_child_fcc_text( 'fcc_ui_69055d77ba802f', 'home' );
          }
          if ( empty( trim( wp_strip_all_tags( $s03_desc ) ) ) ) {
              $s03_desc = 'Fractional CC mang đến các giải pháp chăm sóc da chuyên sâu, tập trung vào dưỡng sáng, cải thiện kết cấu da và chăm sóc các dấu hiệu lão hóa thông qua sự kết hợp giữa công nghệ vi kim và các thành phần chăm sóc da nổi bật. Tại Việt Nam, Fractional CC được phân phối độc quyền bởi BUI DANG-JP, hướng đến việc đưa các sản phẩm chăm sóc da Nhật Bản đến gần hơn với người tiêu dùng Việt.';
          }
          ?>
          <p class="section-desc"><?php echo $s03_desc; ?></p>
        </div>

        <div class="brief-brand">
          <div class="brand-visual-wrap">
            <?php 
            $home_id       = project_core_fcc_page_id( 'home' );
            $brand_img_id  = absint( get_post_meta( $home_id, 'fcc_ui_brand_image', true ) );
            $brand_img_url = $brand_img_id ? wp_get_attachment_image_url( $brand_img_id, 'full' ) : '';
            if ( ! $brand_img_url ) {
                $brand_img_url = project_child_fcc_asset( 'assets/images/brand/fractional-lineup-full.jpg' );
            }
            $brand_img_alt = project_child_fcc_text( 'fcc_ui_brand_image_alt', 'home', false );
            if ( empty( trim( $brand_img_alt ) ) ) {
                $brand_img_alt = 'Dòng sản phẩm Fractional CC';
            }
            $brand_caption = project_child_fcc_text( 'fcc_ui_brand_caption', 'home' );
            if ( empty( trim( wp_strip_all_tags( $brand_caption ) ) ) ) {
                $brand_caption = 'Hình ảnh sản phẩm, bao bì và phong cách thương hiệu.';
            }
            ?>
            <div class="brand-image-stage">
              <img src="<?php echo esc_url( $brand_img_url ); ?>" alt="<?php echo esc_attr( $brand_img_alt ); ?>" class="brand-lineup-img" loading="lazy">
            </div>
            <p class="brand-visual-caption"><?php echo esc_html( $brand_caption ); ?></p>
          </div>
          <div class="brand-content-wrap">
            <?php 
            $s03_quote = project_child_fcc_text( 'fcc_ui_89f836dd883298', 'home' );
            if ( ! empty( trim( wp_strip_all_tags( $s03_quote ) ) ) ) : 
            ?>
              <h3 class="brand-content-quote"><?php echo $s03_quote; ?></h3>
            <?php endif; ?>
            <?php 
            $s03_content_desc = project_child_fcc_text( 'fcc_ui_69055d77ba802f', 'home', false );
            if ( ! empty( trim( wp_strip_all_tags( $s03_content_desc ) ) ) && false === strpos( $s03_content_desc, 'Fractional CC mang đến các giải pháp' ) ) : 
            ?>
              <p class="brand-content-desc"><?php echo $s03_content_desc; ?></p>
            <?php endif; ?>
            <ul class="brand-trust-points">
              <li>
                <svg width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"></path></svg>
                <span><strong><?php echo project_child_fcc_text( 'fcc_ui_ea7adcd84f1a70', 'home' ); ?></strong> <?php echo project_child_fcc_text( 'fcc_ui_7a09e2d30042ba', 'home' ); ?></span>
              </li>
              <li>
                <svg width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"></path></svg>
                <span><strong><?php echo project_child_fcc_text( 'fcc_ui_ec032b4fc66f48', 'home' ); ?></strong> <?php echo project_child_fcc_text( 'fcc_ui_b40c9e76d5c677', 'home' ); ?></span>
              </li>
              <li>
                <svg width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"></path></svg>
                <span><strong><?php echo project_child_fcc_text( 'fcc_ui_a8a99c5a9a8606', 'home' ); ?></strong> <?php echo project_child_fcc_text( 'fcc_ui_4163196532ab03', 'home' ); ?></span>
              </li>
              <?php 
              $s03_feat4_strong = project_child_fcc_text( 'fcc_ui_brand_feat4_strong', 'home' );
              $s03_feat4_text   = project_child_fcc_text( 'fcc_ui_brand_feat4_text', 'home' );
              if ( ! empty( trim( wp_strip_all_tags( $s03_feat4_strong . $s03_feat4_text ) ) ) ) : 
              ?>
              <li>
                <svg width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"></path></svg>
                <span><strong><?php echo $s03_feat4_strong; ?></strong> <?php echo $s03_feat4_text; ?></span>
              </li>
              <?php endif; ?>
            </ul>

            <?php 
            $s03_disclaimer = project_child_fcc_text( 'fcc_ui_brand_disclaimer', 'home' );
            if ( ! empty( trim( wp_strip_all_tags( $s03_disclaimer ) ) ) ) : 
            ?>
              <p class="brand-disclaimer"><?php echo $s03_disclaimer; ?></p>
            <?php endif; ?>

            <?php 
            $s03_msg_tag   = project_child_fcc_text( 'fcc_ui_brand_message_tag', 'home' );
            $s03_msg_line1 = project_child_fcc_text( 'fcc_ui_brand_msg_line1', 'home' );
            $s03_msg_line2 = project_child_fcc_text( 'fcc_ui_brand_msg_line2', 'home' );
            if ( ! empty( trim( wp_strip_all_tags( $s03_msg_line1 . $s03_msg_line2 ) ) ) ) : 
            ?>
              <div class="brand-message-card">
                <?php if ( ! empty( trim( wp_strip_all_tags( $s03_msg_tag ) ) ) ) : ?>
                  <div class="brand-message-header">
                    <span class="brand-message-badge">
                      <svg width="12" height="12" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                      <?php echo $s03_msg_tag; ?>
                    </span>
                  </div>
                <?php endif; ?>
                <div class="brand-message-content">
                  <?php if ( ! empty( trim( wp_strip_all_tags( $s03_msg_line1 ) ) ) ) : ?>
                    <div class="brand-message-line">
                      <span class="brand-message-dot" aria-hidden="true"></span>
                      <div>
                        <?php 
                        $parts1 = explode( ' – ', $s03_msg_line1, 2 );
                        if ( count( $parts1 ) === 2 ) : ?>
                          <strong><?php echo esc_html( $parts1[0] ); ?></strong> – <?php echo esc_html( $parts1[1] ); ?>
                        <?php else : ?>
                          <?php echo $s03_msg_line1; ?>
                        <?php endif; ?>
                      </div>
                    </div>
                  <?php endif; ?>
                  <?php if ( ! empty( trim( wp_strip_all_tags( $s03_msg_line2 ) ) ) ) : ?>
                    <div class="brand-message-line">
                      <span class="brand-message-dot" aria-hidden="true"></span>
                      <div>
                        <?php 
                        $parts2 = explode( ' – ', $s03_msg_line2, 2 );
                        if ( count( $parts2 ) === 2 ) : ?>
                          <strong><?php echo esc_html( $parts2[0] ); ?></strong> – <?php echo esc_html( $parts2[1] ); ?>
                        <?php else : ?>
                          <?php echo $s03_msg_line2; ?>
                        <?php endif; ?>
                      </div>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            <?php endif; ?>

            <div class="brand-actions-row">
              <a <?php project_child_fcc_contact_attributes( 'zalo' ); ?> target="_blank" rel="noopener noreferrer" class="btn btn-gold brand-zalo-btn">
                <svg width="18" height="18" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                <?php echo project_child_fcc_text( 'fcc_ui_7b13a0466f4818', 'home', false ); ?>
              </a>
              <a href="<?php echo esc_url( project_child_fcc_link( 'san-pham.html' ) ); ?>" class="btn btn-outline" style="font-size: 13.5px; padding: 12px 22px;">
                <?php echo project_child_fcc_text( 'fcc_ui_5e0215bda5a3b2', 'home', false ); ?>
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>
