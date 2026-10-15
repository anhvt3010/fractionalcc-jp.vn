<?php
defined( 'ABSPATH' ) || exit;
$fcc_combo = project_core_fcc_featured_combo();
if ( ! $fcc_combo ) { echo '<div id="danh-sach-combo"></div>'; return; }
$fcc_combo_products = project_core_fcc_combo_contents( $fcc_combo )['products'];
$fcc_combo_description = (string) get_post_meta( $fcc_combo['id'], 'fcc_spotlightDescription', true );
if ( '' === $fcc_combo_description ) { $fcc_combo_description = $fcc_combo['idealFor']; }
?>
<section class="section" id="danh-sach-combo">
      <div class="container">
        <div class="section-header">
          <span class="section-tag"><?php echo project_child_fcc_text( 'fcc_ui_ecbbe6f52087ca', 'catalog' ); ?></span>
          <h2 class="section-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_e5c68acce9a1e4', 'catalog' ); ?></h2>
          <p class="section-desc"><?php echo project_child_fcc_text( 'fcc_ui_9feb578383a97d', 'catalog' ); ?></p>
        </div>

        <div class="brief-brand">
          <div class="brand-visual-wrap">
            <?php echo project_child_fcc_picture_html( $fcc_combo['image'], project_child_fcc_hero_product_image_alt(), '', array( 'loading' => 'lazy' ) ); ?>
          </div>
          <div class="brand-content-wrap">
            <h3 class="brand-content-quote combo-spotlight-title"><?php echo esc_html( $fcc_combo['name'] ); ?></h3>
            <p class="brand-content-desc"><?php echo esc_html( $fcc_combo_description ); ?></p>
            <?php if ( $fcc_combo_products ) : ?>
              <div class="combo-included-products">
                <p class="combo-included-heading">Sản phẩm trong combo</p>
                <ul class="combo-included-list">
                  <?php foreach ( $fcc_combo_products as $fcc_product ) : ?>
                    <li>
                      <a class="combo-included-product" href="<?php echo esc_url( $fcc_product['url'] ); ?>">
                        <span class="combo-included-thumb"><?php echo project_child_fcc_picture_html( $fcc_product['image'], $fcc_product['imageAlt'], '', array( 'loading' => 'lazy', 'sizes' => '64px' ) ); ?></span>
                        <span class="combo-included-info">
                          <span class="combo-included-name"><?php echo esc_html( $fcc_product['name'] ); ?></span>
                          <span class="combo-included-price">Giá mua lẻ: <strong><?php echo esc_html( project_child_fcc_price( $fcc_product['salePrice'] ) ); ?></strong></span>
                        </span>
                      </a>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>
            <?php if ( $fcc_combo['gift'] ) : ?><div class="combo-gift-box">
              <svg width="15" height="15" viewbox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px; margin-right: 4px;"><path d="M20 12v10H4V12"></path><path d="M2 7h20v5H2z"></path><path d="M12 22V7"></path><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path></svg><strong><?php echo project_child_fcc_text( 'fcc_ui_61773f36920e41', 'catalog' ); ?></strong> <?php echo esc_html( $fcc_combo['gift'] ); ?>
            </div>
            <?php endif; ?>
            <div class="combo-price-wrap" style="border: none; padding: 0; margin-bottom: 8px;">
              <div class="combo-sale-price-row">
                <span class="combo-sale-price"><?php echo esc_html( project_child_fcc_price( $fcc_combo['salePrice'] ) ); ?></span>
                <?php if ( null !== $fcc_combo['salePrice'] && $fcc_combo['originalPrice'] > $fcc_combo['salePrice'] ) : ?><span class="combo-original-price"><?php echo esc_html( project_child_fcc_price( $fcc_combo['originalPrice'] ) ); ?></span><?php endif; ?>
              </div>
            </div>
            <?php if ( null !== $fcc_combo['salePrice'] && $fcc_combo['saveAmount'] > 0 ) : ?><p class="combo-spotlight-benefit-text">Tiết kiệm <strong><?php echo esc_html( project_child_fcc_price( $fcc_combo['saveAmount'] ) ); ?></strong></p><?php endif; ?>
            <div>
              <button class="btn btn-primary" data-open-order data-product-select="<?php echo esc_attr( project_child_fcc_featured_combo_order_code() ); ?>" style="padding: 14px 28px;">
                <?php echo esc_html( 'MUA ' . ( $fcc_combo['shortName'] ?: $fcc_combo['name'] ) ); ?>
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>
