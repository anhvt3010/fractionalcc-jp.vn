<?php
defined( 'ABSPATH' ) || exit;
$fcc_combo_products = array();
foreach ( project_core_fcc_combos() as $fcc_combo ) {
	if ( 'C-CX4' !== $fcc_combo['code'] ) { continue; }
	foreach ( array_unique( $fcc_combo['productIds'] ) as $fcc_product_id ) {
		$fcc_product = project_core_fcc_product( $fcc_product_id, false );
		if ( $fcc_product ) { $fcc_combo_products[] = $fcc_product; }
	}
	break;
}
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
            <img src="<?php echo esc_url( project_child_fcc_image( 'fcc_ui_453bd451ef0751' ) ); ?>" alt="<?php echo esc_attr( project_child_fcc_image_alt( 'fcc_ui_453bd451ef0751', 'fcc_ui_b9912a532ed31f' ) ); ?>" loading="lazy">
          </div>
          <div class="brand-content-wrap">
            <h3 class="brand-content-quote combo-spotlight-title"><?php echo project_child_fcc_text( 'fcc_ui_f742617136c8f0', 'catalog' ); ?></h3>
            <p class="brand-content-desc">
              <?php echo project_child_fcc_text( 'fcc_ui_071145cc904429', 'catalog' ); ?> <strong><?php echo project_child_fcc_text( 'fcc_ui_481ddc3afebd12', 'catalog' ); ?></strong> <?php echo project_child_fcc_text( 'fcc_ui_99ce1d649bf94a', 'catalog' ); ?> <strong><?php echo project_child_fcc_text( 'fcc_ui_201efb207d823a', 'catalog' ); ?></strong> <?php echo project_child_fcc_text( 'fcc_ui_389ffc845e3eda', 'catalog' ); ?>
            </p>
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
            <div class="combo-gift-box">
              <svg width="15" height="15" viewbox="0 0 24 24" fill="none" stroke="#C5A059" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px; margin-right: 4px;"><path d="M20 12v10H4V12"></path><path d="M2 7h20v5H2z"></path><path d="M12 22V7"></path><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path></svg><strong><?php echo project_child_fcc_text( 'fcc_ui_61773f36920e41', 'catalog' ); ?></strong> <?php echo project_child_fcc_text( 'fcc_ui_c6968594a69f05', 'catalog' ); ?>
            </div>
            <div class="combo-price-wrap" style="border: none; padding: 0; margin-bottom: 8px;">
              <div class="combo-sale-price-row">
                <span class="combo-sale-price"><?php echo esc_html( project_child_fcc_reference_text( '1.237.500đ', 'combo_C-CX4' ) ); ?></span>
                <span class="combo-original-price"><?php echo esc_html( project_child_fcc_reference_text( '1.275.600đ', 'combo_C-CX4' ) ); ?></span>
              </div>
            </div>
            <p class="combo-spotlight-benefit-text" style="font-size: 13.5px; color: var(--color-text-body, #333333); line-height: 1.6; margin: 0 0 18px;">
              <?php
              $fcc_benefit_text = project_child_fcc_text( 'fcc_ui_cb726a38e532ec', 'catalog' );
              $fcc_benefit_html = str_replace(
                array( '462.500đ', '462.500d', 'tặng kèm 01 Son dưỡng vi kim PDRN Shot', 'Tặng kèm 01 Son dưỡng vi kim PDRN Shot' ),
                array( '<strong>462.500đ</strong>', '<strong>462.500đ</strong>', '<strong>tặng kèm 01 Son dưỡng vi kim PDRN Shot</strong>', '<strong>Tặng kèm 01 Son dưỡng vi kim PDRN Shot</strong>' ),
                $fcc_benefit_text
              );
              echo $fcc_benefit_html;
              ?>
            </p>
            <div>
              <button class="btn btn-primary" data-open-order data-product-select="<?php echo esc_attr( project_child_fcc_order_code( 'combo_C-CX4' ) ); ?>" style="padding: 14px 28px;">
                <?php echo project_child_fcc_text( 'fcc_ui_0726567f5d6c24', 'catalog', false ); ?>
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>
