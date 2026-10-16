<?php
defined( 'ABSPATH' ) || exit;
$review_video = project_child_fcc_home_video( 'review' );
?>
<section class="section section-soft" id="danh-gia">
      <div class="container">
        <div class="section-header" style="margin-bottom: 24px;">
          <span class="section-tag"><?php echo project_child_fcc_text( 'fcc_ui_120d1c810750c2', 'home' ); ?></span>
          <h2 class="section-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_d5a34567c9c8c0', 'home' ); ?></h2>
          <p class="section-desc"><?php echo project_child_fcc_text( 'fcc_ui_ae9582f3595962', 'home' ); ?></p>
        </div>


        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid var(--color-border);">
          <div style="display: flex; align-items: center; gap: 12px;">
            <div style="font-size: 28px; font-weight: 800; color: var(--color-primary); line-height: 1;"><?php echo project_child_fcc_text( 'fcc_ui_d372f01686d8a2', 'home' ); ?></div>
            <div>
              <div style="color: #FAAD14; font-size: 16px; letter-spacing: 2px;">★★★★★</div>
              <span style="font-size: 12px; color: var(--color-text-muted);"><?php echo project_child_fcc_text( 'fcc_ui_f3a639284cdbeb', 'home' ); ?></span>
            </div>
          </div>

          <div style="display: flex; align-items: center; gap: 14px;">
            <div style="font-size: 13px; font-weight: 700; color: var(--color-text-muted);">
              <span id="review-counter-current">01</span> / <span id="review-counter-total">08</span>
            </div>
            <div style="display: flex; gap: 8px;">
              <button id="review-prev-btn" class="review-nav-btn" aria-label="Đánh giá trước" style="width: 38px; height: 38px; border-radius: 50%; border: 1px solid var(--color-border); background: #FFF; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 16px;"><?php echo project_child_fcc_text( 'fcc_ui_856500a8b8ce5b', 'home', false ); ?></button>
              <button id="review-next-btn" class="review-nav-btn" aria-label="Đánh giá kế tiếp" style="width: 38px; height: 38px; border-radius: 50%; border: 1px solid var(--color-border); background: #FFF; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 16px;"><?php echo project_child_fcc_text( 'fcc_ui_f16d289b324e34', 'home', false ); ?></button>
            </div>
          </div>
        </div>


        <div class="reviews-slider-wrap">
          <div class="reviews-slider-track" id="reviews-slider-track">

            <!-- Thẻ 01: Video Sự Kiện Quốc Tế - Beauty Istanbul 2025 -->
            <article class="review-card review-video-card">
              <div class="review-card-top">
                <div class="review-author-info">
                  <div class="review-avatar-wrap review-avatar-event">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                  </div>
                  <div class="review-author-names">
                    <h3>Beauty Istanbul 2025</h3>
                    <span>Triển Lãm Thẩm Mỹ &amp; Mỹ Phẩm Quốc Tế</span>
                  </div>
                </div>
                <span class="review-badge badge-event">Sự Kiện Quốc Tế</span>
              </div>
              <div class="review-rating-row">
                <span class="review-stars">★★★★★</span>
                <span class="review-rating-score">Trải Nghiệm Thực Tế</span>
              </div>
              <span class="review-skin-concern">Gian Hàng Fractional CC Tokyo</span>
              <div class="review-product-tag">Trải Nghiệm Dòng Serum &amp; Peel Salon</div>
              <p class="review-text">
                Khách tham quan và đối tác quốc tế trực tiếp trải nghiệm dòng tinh chất Fractional CC và công nghệ phục hồi da chuẩn salon Nhật Bản tại hội chợ Beauty Istanbul 2025.
              </p>
              <div class="review-ugc-photo review-video-preview-stage" data-fcc-video-modal="<?php echo esc_url( $review_video['url'] ?? '' ); ?>" data-video-type="<?php echo esc_attr( $review_video['type'] ?? '' ); ?>" data-video-title="Khách Quốc Tế Trải Nghiệm Tại Beauty Istanbul 2025" data-video-tag="Beauty Istanbul 2025 · Trải Nghiệm Thực Tế" role="button" tabindex="0" aria-label="Bấm xem video trải nghiệm tại Beauty Istanbul 2025">
                <div class="review-video-cover-bg">
                  <div class="review-play-pulse-btn">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><polygon points="6,3 20,12 6,21"></polygon></svg>
                  </div>
                  <div class="review-video-tag-pill">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polygon points="10 8 16 12 10 16 10 8"></polygon></svg>
                    <span>Xem video thực tế</span>
                  </div>
                </div>
              </div>
              <div class="review-meta">
                <span class="verified-badge">Đã xác minh sự kiện</span>
                <span>Beauty Istanbul, Thổ Nhĩ Kỳ</span>
              </div>
            </article>

            <article class="review-card">
              <div class="review-card-top">
                <div class="review-author-info">
                  <div class="review-avatar-wrap"<?php echo project_child_fcc_image_edit_attributes( 'fcc_review_avatar_1' ); ?>>
                    <?php echo project_child_fcc_picture_html( ( project_child_fcc_image( 'fcc_review_avatar_1' ) ?: project_child_fcc_image( 'fcc_ui_2d8d0e4cc814fb' ) ), project_child_fcc_image_alt( 'fcc_review_avatar_1', 'fcc_ui_4fe975230c2416' ), '', array( 'width' => 48, 'height' => 48, 'loading' => 'lazy' ) ); ?>
                  </div>
                  <div class="review-author-names">
                    <h3><?php echo project_child_fcc_text( 'fcc_ui_20f5bca50308fb', 'home' ); ?></h3>
                    <span><?php echo project_child_fcc_text( 'fcc_ui_6bc65a7c21f809', 'home' ); ?></span>
                  </div>
                </div>
                <span class="review-badge badge-kol"><?php echo project_child_fcc_text( 'fcc_ui_45ad92ee9727f9', 'home' ); ?></span>
              </div>
              <div class="review-rating-row">
                <span class="review-stars">★★★★★</span>
                <span class="review-rating-score"><?php echo project_child_fcc_text( 'fcc_ui_ebae7f23b23740', 'home' ); ?></span>
              </div>
              <span class="review-skin-concern"><?php echo project_child_fcc_text( 'fcc_ui_83e9a0f8927e14', 'home' ); ?></span>
              <div class="review-product-tag"><?php echo project_child_fcc_text( 'fcc_ui_5b450847aa849d', 'home' ); ?></div>
              <p class="review-text">
                <?php echo project_child_fcc_text( 'fcc_ui_4a5a5878a1651b', 'home' ); ?>
              </p>
              <div class="review-ugc-photo"<?php echo project_child_fcc_image_edit_attributes( 'fcc_ui_2d8d0e4cc814fb' ); ?>>
                <?php echo project_child_fcc_picture_html( project_child_fcc_image( 'fcc_ui_2d8d0e4cc814fb' ), project_child_fcc_image_alt( 'fcc_ui_2d8d0e4cc814fb', 'fcc_ui_424bf7d26ffd10' ), '', array( 'width' => 320, 'height' => 220, 'loading' => 'lazy' ) ); ?>
              </div>
              <div class="review-meta">
                <span class="verified-badge"><?php echo project_child_fcc_text( 'fcc_ui_b87712fd5f7219', 'home' ); ?></span>
                <span><?php echo project_child_fcc_text( 'fcc_ui_601a57d8bbdd68', 'home' ); ?></span>
              </div>
            </article>


            <article class="review-card">
              <div class="review-card-top">
                <div class="review-author-info">
                  <div class="review-avatar-wrap"<?php echo project_child_fcc_image_edit_attributes( 'fcc_review_avatar_2' ); ?>>
                    <?php echo project_child_fcc_picture_html( ( project_child_fcc_image( 'fcc_review_avatar_2' ) ?: project_child_fcc_image( 'fcc_ui_5e28ca19d6c1a6' ) ), project_child_fcc_image_alt( 'fcc_review_avatar_2', 'fcc_ui_582af86c2cd2b7' ), '', array( 'width' => 48, 'height' => 48, 'loading' => 'lazy' ) ); ?>
                  </div>
                  <div class="review-author-names">
                    <h3><?php echo project_child_fcc_text( 'fcc_ui_abc7f8c203e574', 'home' ); ?></h3>
                    <span><?php echo project_child_fcc_text( 'fcc_ui_51a538e556b295', 'home' ); ?></span>
                  </div>
                </div>
                <span class="review-badge badge-verified"><?php echo project_child_fcc_text( 'fcc_ui_510d2126ca66e3', 'home' ); ?></span>
              </div>
              <div class="review-rating-row">
                <span class="review-stars">★★★★★</span>
                <span class="review-rating-score"><?php echo project_child_fcc_text( 'fcc_ui_ebae7f23b23740', 'home' ); ?></span>
              </div>
              <span class="review-skin-concern"><?php echo project_child_fcc_text( 'fcc_ui_c479ee1ddb85b4', 'home' ); ?></span>
              <div class="review-product-tag"><?php echo project_child_fcc_text( 'fcc_ui_d7787cde62b9c7', 'home' ); ?></div>
              <p class="review-text">
                <?php echo project_child_fcc_text( 'fcc_ui_dbc46d0507416e', 'home' ); ?>
              </p>
              <div class="review-ugc-photo"<?php echo project_child_fcc_image_edit_attributes( 'fcc_ui_5e28ca19d6c1a6' ); ?>>
                <?php echo project_child_fcc_picture_html( project_child_fcc_image( 'fcc_ui_5e28ca19d6c1a6' ), project_child_fcc_image_alt( 'fcc_ui_5e28ca19d6c1a6', 'fcc_ui_8a11ba251693bf' ), '', array( 'width' => 320, 'height' => 220, 'loading' => 'lazy' ) ); ?>
              </div>
              <div class="review-meta">
                <span class="verified-badge"><?php echo project_child_fcc_text( 'fcc_ui_b87712fd5f7219', 'home' ); ?></span>
                <span><?php echo project_child_fcc_text( 'fcc_ui_05614b50bf95cc', 'home' ); ?></span>
              </div>
            </article>


            <article class="review-card">
              <div class="review-card-top">
                <div class="review-author-info">
                  <div class="review-avatar-wrap"<?php echo project_child_fcc_image_edit_attributes( 'fcc_review_avatar_3' ); ?>>
                    <?php echo project_child_fcc_picture_html( ( project_child_fcc_image( 'fcc_review_avatar_3' ) ?: project_child_fcc_image( 'fcc_ui_0db797a330bea5' ) ), project_child_fcc_image_alt( 'fcc_review_avatar_3', 'fcc_ui_d78a914ad8029f' ), '', array( 'width' => 48, 'height' => 48, 'loading' => 'lazy' ) ); ?>
                  </div>
                  <div class="review-author-names">
                    <h3><?php echo project_child_fcc_text( 'fcc_ui_9a5b4a75039b72', 'home' ); ?></h3>
                    <span><?php echo project_child_fcc_text( 'fcc_ui_ab50cade3c4d54', 'home' ); ?></span>
                  </div>
                </div>
                <span class="review-badge badge-koc"><?php echo project_child_fcc_text( 'fcc_ui_49aeb8079a90bb', 'home' ); ?></span>
              </div>
              <div class="review-rating-row">
                <span class="review-stars">★★★★★</span>
                <span class="review-rating-score"><?php echo project_child_fcc_text( 'fcc_ui_ca99894f68667f', 'home' ); ?></span>
              </div>
              <span class="review-skin-concern"><?php echo project_child_fcc_text( 'fcc_ui_e06ce21cb42c3e', 'home' ); ?></span>
              <div class="review-product-tag"><?php echo project_child_fcc_text( 'fcc_ui_2bf87779931e23', 'home' ); ?></div>
              <p class="review-text">
                <?php echo project_child_fcc_text( 'fcc_ui_aae96066552a69', 'home' ); ?>
              </p>
              <div class="review-ugc-photo"<?php echo project_child_fcc_image_edit_attributes( 'fcc_ui_0db797a330bea5' ); ?>>
                <?php echo project_child_fcc_picture_html( project_child_fcc_image( 'fcc_ui_0db797a330bea5' ), project_child_fcc_image_alt( 'fcc_ui_0db797a330bea5', 'fcc_ui_9dc56fa56ef628' ), '', array( 'width' => 320, 'height' => 220, 'loading' => 'lazy' ) ); ?>
              </div>
              <div class="review-meta">
                <span class="verified-badge"><?php echo project_child_fcc_text( 'fcc_ui_b87712fd5f7219', 'home' ); ?></span>
                <span><?php echo project_child_fcc_text( 'fcc_ui_e30fb068af857c', 'home' ); ?></span>
              </div>
            </article>


            <article class="review-card">
              <div class="review-card-top">
                <div class="review-author-info">
                  <div class="review-avatar-wrap"<?php echo project_child_fcc_image_edit_attributes( 'fcc_review_avatar_4' ); ?>>
                    <?php echo project_child_fcc_picture_html( ( project_child_fcc_image( 'fcc_review_avatar_4' ) ?: project_child_fcc_image( 'fcc_ui_86f10657c932c8' ) ), project_child_fcc_image_alt( 'fcc_review_avatar_4', 'fcc_ui_b89e4c5a14dd5f' ), '', array( 'width' => 48, 'height' => 48, 'loading' => 'lazy' ) ); ?>
                  </div>
                  <div class="review-author-names">
                    <h3><?php echo project_child_fcc_text( 'fcc_ui_e371a859bab35c', 'home' ); ?></h3>
                    <span><?php echo project_child_fcc_text( 'fcc_ui_fc8fab90b7ab74', 'home' ); ?></span>
                  </div>
                </div>
                <span class="review-badge badge-verified"><?php echo project_child_fcc_text( 'fcc_ui_510d2126ca66e3', 'home' ); ?></span>
              </div>
              <div class="review-rating-row">
                <span class="review-stars">★★★★★</span>
                <span class="review-rating-score"><?php echo project_child_fcc_text( 'fcc_ui_ebae7f23b23740', 'home' ); ?></span>
              </div>
              <span class="review-skin-concern"><?php echo project_child_fcc_text( 'fcc_ui_cfddc9ae95b931', 'home' ); ?></span>
              <div class="review-product-tag"><?php echo project_child_fcc_text( 'fcc_ui_aa7b6b83722208', 'home' ); ?></div>
              <p class="review-text">
                <?php echo project_child_fcc_text( 'fcc_ui_50561c424b1d41', 'home' ); ?>
              </p>
              <div class="review-ugc-photo"<?php echo project_child_fcc_image_edit_attributes( 'fcc_ui_86f10657c932c8' ); ?>>
                <?php echo project_child_fcc_picture_html( project_child_fcc_image( 'fcc_ui_86f10657c932c8' ), project_child_fcc_image_alt( 'fcc_ui_86f10657c932c8', 'fcc_ui_49056b4ced1219' ), '', array( 'width' => 320, 'height' => 220, 'loading' => 'lazy' ) ); ?>
              </div>
              <div class="review-meta">
                <span class="verified-badge"><?php echo project_child_fcc_text( 'fcc_ui_b87712fd5f7219', 'home' ); ?></span>
                <span><?php echo project_child_fcc_text( 'fcc_ui_26385de9767f04', 'home' ); ?></span>
              </div>
            </article>


            <article class="review-card">
              <div class="review-card-top">
                <div class="review-author-info">
                  <div class="review-avatar-wrap"<?php echo project_child_fcc_image_edit_attributes( 'fcc_review_avatar_5' ); ?>>
                    <?php echo project_child_fcc_picture_html( ( project_child_fcc_image( 'fcc_review_avatar_5' ) ?: project_child_fcc_image( 'fcc_ui_da1eaea273e1e5' ) ), project_child_fcc_image_alt( 'fcc_review_avatar_5', 'fcc_ui_c70eeb1112b97e' ), '', array( 'width' => 48, 'height' => 48, 'loading' => 'lazy' ) ); ?>
                  </div>
                  <div class="review-author-names">
                    <h3><?php echo project_child_fcc_text( 'fcc_ui_eeab6c7deedef8', 'home' ); ?></h3>
                    <span><?php echo project_child_fcc_text( 'fcc_ui_d2ddc5756c7268', 'home' ); ?></span>
                  </div>
                </div>
                <span class="review-badge badge-koc"><?php echo project_child_fcc_text( 'fcc_ui_badge_5', 'home' ); ?></span>
              </div>
              <div class="review-rating-row">
                <span class="review-stars">★★★★★</span>
                <span class="review-rating-score"><?php echo project_child_fcc_text( 'fcc_ui_ca99894f68667f', 'home' ); ?></span>
              </div>
              <span class="review-skin-concern"><?php echo project_child_fcc_text( 'fcc_ui_b944e00dd9af5e', 'home' ); ?></span>
              <div class="review-product-tag"><?php echo project_child_fcc_text( 'fcc_ui_0ba24cdcc58c3e', 'home' ); ?></div>
              <p class="review-text">
                <?php echo project_child_fcc_text( 'fcc_ui_50e2751288982c', 'home' ); ?>
              </p>
              <div class="review-ugc-photo"<?php echo project_child_fcc_image_edit_attributes( 'fcc_ui_da1eaea273e1e5' ); ?>>
                <?php echo project_child_fcc_picture_html( project_child_fcc_image( 'fcc_ui_da1eaea273e1e5' ), project_child_fcc_image_alt( 'fcc_ui_da1eaea273e1e5', 'fcc_ui_02342e69ec8261' ), '', array( 'width' => 320, 'height' => 220, 'loading' => 'lazy' ) ); ?>
              </div>
              <div class="review-meta">
                <span class="verified-badge"><?php echo project_child_fcc_text( 'fcc_ui_b87712fd5f7219', 'home' ); ?></span>
                <span><?php echo project_child_fcc_text( 'fcc_ui_790e2513613da8', 'home' ); ?></span>
              </div>
            </article>


            <article class="review-card">
              <div class="review-card-top">
                <div class="review-author-info">
                  <div class="review-avatar-wrap"<?php echo project_child_fcc_image_edit_attributes( 'fcc_review_avatar_6' ); ?>>
                    <?php echo project_child_fcc_picture_html( ( project_child_fcc_image( 'fcc_review_avatar_6' ) ?: project_child_fcc_image( 'fcc_ui_feedback_img_6' ) ), project_child_fcc_image_alt( 'fcc_review_avatar_6', 'fcc_ui_67e344a449c6ea' ), '', array( 'width' => 48, 'height' => 48, 'loading' => 'lazy' ) ); ?>
                  </div>
                  <div class="review-author-names">
                    <h3><?php echo project_child_fcc_text( 'fcc_ui_0cd0b40d7ab747', 'home' ); ?></h3>
                    <span><?php echo project_child_fcc_text( 'fcc_ui_4a2e3b716720af', 'home' ); ?></span>
                  </div>
                </div>
                <span class="review-badge badge-verified"><?php echo project_child_fcc_text( 'fcc_ui_510d2126ca66e3', 'home' ); ?></span>
              </div>
              <div class="review-rating-row">
                <span class="review-stars">★★★★★</span>
                <span class="review-rating-score"><?php echo project_child_fcc_text( 'fcc_ui_ebae7f23b23740', 'home' ); ?></span>
              </div>
              <span class="review-skin-concern"><?php echo project_child_fcc_text( 'fcc_ui_7c482827e5e757', 'home' ); ?></span>
              <div class="review-product-tag"><?php echo project_child_fcc_text( 'fcc_ui_b9e236e944c903', 'home' ); ?></div>
              <p class="review-text">
                <?php echo project_child_fcc_text( 'fcc_ui_11e28e296727ba', 'home' ); ?>
              </p>
              <div class="review-ugc-photo"<?php echo project_child_fcc_image_edit_attributes( 'fcc_ui_feedback_img_6' ); ?>>
                <?php echo project_child_fcc_picture_html( project_child_fcc_image( 'fcc_ui_feedback_img_6' ), project_child_fcc_image_alt( 'fcc_ui_feedback_img_6', 'fcc_ui_890c34255cfbfd' ), '', array( 'width' => 320, 'height' => 220, 'loading' => 'lazy' ) ); ?>
              </div>
              <div class="review-meta">
                <span class="verified-badge"><?php echo project_child_fcc_text( 'fcc_ui_b87712fd5f7219', 'home' ); ?></span>
                <span><?php echo project_child_fcc_text( 'fcc_ui_41d95cdb237b18', 'home' ); ?></span>
              </div>
            </article>


            <article class="review-card">
              <div class="review-card-top">
                <div class="review-author-info">
                  <div class="review-avatar-wrap"<?php echo project_child_fcc_image_edit_attributes( 'fcc_review_avatar_7' ); ?>>
                    <?php echo project_child_fcc_picture_html( ( project_child_fcc_image( 'fcc_review_avatar_7' ) ?: project_child_fcc_image( 'fcc_ui_feedback_img_7' ) ), project_child_fcc_image_alt( 'fcc_review_avatar_7', 'fcc_ui_feedback_alt_7' ), '', array( 'width' => 48, 'height' => 48, 'loading' => 'lazy' ) ); ?>
                  </div>
                  <div class="review-author-names">
                    <h3><?php echo project_child_fcc_text( 'fcc_ui_feedback_name_7', 'home' ); ?></h3>
                    <span><?php echo project_child_fcc_text( 'fcc_ui_feedback_sub_7', 'home' ); ?></span>
                  </div>
                </div>
                <span class="review-badge badge-verified"><?php echo project_child_fcc_text( 'fcc_ui_feedback_badge_7', 'home' ); ?></span>
              </div>
              <div class="review-rating-row">
                <span class="review-stars">★★★★★</span>
                <span class="review-rating-score"><?php echo project_child_fcc_text( 'fcc_ui_feedback_rating_7', 'home' ); ?></span>
              </div>
              <span class="review-skin-concern"><?php echo project_child_fcc_text( 'fcc_ui_feedback_concern_7', 'home' ); ?></span>
              <div class="review-product-tag"><?php echo project_child_fcc_text( 'fcc_ui_feedback_product_7', 'home' ); ?></div>
              <p class="review-text">
                <?php echo project_child_fcc_text( 'fcc_ui_feedback_text_7', 'home' ); ?>
              </p>
              <div class="review-ugc-photo"<?php echo project_child_fcc_image_edit_attributes( 'fcc_ui_feedback_img_7' ); ?>>
                <?php echo project_child_fcc_picture_html( project_child_fcc_image( 'fcc_ui_feedback_img_7' ), project_child_fcc_image_alt( 'fcc_ui_feedback_img_7', 'fcc_ui_feedback_ugc_alt_7' ), '', array( 'width' => 320, 'height' => 220, 'loading' => 'lazy' ) ); ?>
              </div>
              <div class="review-meta">
                <span class="verified-badge"><?php echo project_child_fcc_text( 'fcc_ui_b87712fd5f7219', 'home' ); ?></span>
                <span><?php echo project_child_fcc_text( 'fcc_ui_feedback_time_7', 'home' ); ?></span>
              </div>
            </article>
          </div>
        </div>


        <div class="reviews-slider-dots" id="reviews-slider-dots">

        </div>

        <div style="text-align: center; margin-top: 24px;">
          <a class="btn btn-outline" <?php project_child_fcc_contact_attributes( 'zalo' ); ?> target="_blank" rel="noopener noreferrer">
            <?php echo project_child_fcc_text( 'fcc_ui_db1c1b95dea8f4', 'home', false ); ?>
          </a>
        </div>
      </div>
    </section>
