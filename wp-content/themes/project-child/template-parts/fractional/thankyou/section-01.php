<?php
defined( 'ABSPATH' ) || exit;

$order_code_param = isset( $_GET['order'] ) && is_string( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : '';
$receipt_key_param = isset( $_GET['key'] ) && is_string( $_GET['key'] ) ? wp_unslash( $_GET['key'] ) : '';
$order_data = project_core_fcc_order_receipt( $order_code_param, $receipt_key_param );
$order_item = $order_data ? $order_data['item'] : array();
?>
<div class="container">
      
      <div class="thankyou-card">
        
        
        <div class="thankyou-badge-icon">✓</div>

        
        <div class="thankyou-header">
          <span class="section-tag"><?php echo project_child_fcc_text( 'fcc_ui_81d7044ccd69d4', 'thankyou' ); ?></span>
          <h1 class="thankyou-title heading-serif"><?php echo project_child_fcc_text( 'fcc_ui_f4da326b346871', 'thankyou' ); ?></h1>
          <p class="thankyou-subtitle">
            <?php echo project_child_fcc_text( 'fcc_ui_f29eb752d936c8', 'thankyou' ); ?> <strong><?php echo project_child_fcc_text( 'fcc_ui_2386e32559e6dd', 'thankyou' ); ?></strong><?php echo project_child_fcc_text( 'fcc_ui_f5f79bb78ebd83', 'thankyou' ); ?> <strong><?php echo project_child_fcc_text( 'fcc_ui_5fdd213ef3d34f', 'thankyou' ); ?></strong> <?php echo project_child_fcc_text( 'fcc_ui_479f22ecf29015', 'thankyou' ); ?>
          </p>
        </div>

        
        <?php if ( $order_data ) : ?>
        <div class="receipt-box" id="receipt-container" data-order-code="<?php echo esc_attr( $order_data['code'] ); ?>">
          <div class="receipt-head">
            <span class="receipt-id-tag"><?php echo project_child_fcc_text( 'fcc_ui_1d9342e03ca91a', 'thankyou' ); ?> <strong id="order-id-val"><?php echo esc_html( '#' . $order_data['code'] ); ?></strong></span>
            <span class="receipt-status-badge"><?php echo project_child_fcc_text( 'fcc_ui_c65a1c5fa53533', 'thankyou' ); ?></span>
          </div>

          <div class="receipt-grid">
            <div class="receipt-item">
              <span class="receipt-label"><?php echo project_child_fcc_text( 'fcc_ui_516fb55969ee2b', 'thankyou' ); ?></span>
              <span class="receipt-value" id="order-name-val"><?php echo esc_html( $order_data['name'] ); ?></span>
            </div>
            <div class="receipt-item">
              <span class="receipt-label"><?php echo project_child_fcc_text( 'fcc_ui_60ae7f2bd5a911', 'thankyou' ); ?></span>
              <span class="receipt-value" id="order-phone-val"><?php echo esc_html( $order_data['phone'] ); ?></span>
            </div>
            <div class="receipt-item">
              <span class="receipt-label"><?php echo project_child_fcc_text( 'fcc_ui_7e24e0a28466d6', 'thankyou' ); ?></span>
              <span class="receipt-value" style="color: #2E7D32;"><?php echo project_child_fcc_text( 'fcc_ui_ff1531cb9a54f7', 'thankyou' ); ?></span>
            </div>
            <div class="receipt-item">
              <span class="receipt-label"><?php echo project_child_fcc_text( 'fcc_ui_9b57c6d8113f08', 'thankyou' ); ?></span>
              <span class="receipt-value" style="color: #8C6A28;"><?php echo project_child_fcc_text( 'fcc_ui_5332afe033d6ba', 'thankyou' ); ?></span>
            </div>
          </div>

          
          <div class="receipt-product-row">
            <?php if ( $order_item['image'] ) : ?><img src="<?php echo esc_url( $order_item['image'] ); ?>" alt="<?php echo esc_attr( $order_item['imageAlt'] ); ?>" class="receipt-product-thumb" id="order-img-val"><?php endif; ?>
            <div class="receipt-product-info">
              <div class="receipt-product-title" id="order-product-val"><?php echo esc_html( project_core_fcc_format_order_item( $order_item ) ); ?></div>
              <?php if ( $order_item['gift'] ) : ?><div class="receipt-product-gift" id="order-gift-val"><?php echo esc_html( $order_item['gift'] ); ?></div><?php endif; ?>
            </div>
          </div>
        </div>

        
        <?php endif; ?>
        <div class="timeline-wrap">
          <div class="timeline-title"><?php echo project_child_fcc_text( 'fcc_ui_8033b6b0a67bf5', 'thankyou' ); ?></div>
          <div class="timeline-steps">
            
            <div class="fulfillment-card">
              <div class="step-icon-wrap step-icon-done">✓</div>
              <div class="step-card-body">
                <div class="step-name"><?php echo project_child_fcc_text( 'fcc_ui_8c28cb3fc4869f', 'thankyou' ); ?></div>
                <div class="step-desc"><?php echo project_child_fcc_text( 'fcc_ui_ba7738affbfff7', 'thankyou' ); ?></div>
              </div>
            </div>
            
            <div class="fulfillment-card">
              <div class="step-icon-wrap step-icon-active">2</div>
              <div class="step-card-body">
                <div class="step-name"><?php echo project_child_fcc_text( 'fcc_ui_69f67c79919755', 'thankyou' ); ?></div>
                <div class="step-desc"><?php echo project_child_fcc_text( 'fcc_ui_2a19f8ca20ae96', 'thankyou' ); ?></div>
              </div>
            </div>
            
            <div class="fulfillment-card">
              <div class="step-icon-wrap step-icon-next">3</div>
              <div class="step-card-body">
                <div class="step-name"><?php echo project_child_fcc_text( 'fcc_ui_ceeef7c260db14', 'thankyou' ); ?></div>
                <div class="step-desc"><?php echo project_child_fcc_text( 'fcc_ui_1588ca34b56160', 'thankyou' ); ?></div>
              </div>
            </div>
          </div>
        </div>

        
        <div class="thankyou-actions">
          <a <?php project_child_fcc_contact_attributes( 'zalo' ); ?> target="_blank" rel="noopener noreferrer" class="btn-zalo-priority">
            <svg width="20" height="20" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 2C6.477 2 2 6.03 2 11C2 13.565 3.195 15.864 5.12 17.472L4.2 21.2C4.085 21.664 4.545 22.052 4.965 21.848L9.29 19.742C10.158 19.911 11.063 20 12 20C17.523 20 22 15.97 22 11C22 6.03 17.523 2 12 2Z" fill="#FFFFFF"></path>
            </svg>
            <span><?php echo project_child_fcc_text( 'fcc_ui_a69dd64e68dae5', 'thankyou', false ); ?></span>
          </a>
          <a <?php project_child_fcc_contact_attributes( 'hotline' ); ?> class="btn btn-outline" style="font-weight: 700;">
            <span><?php echo project_child_fcc_text( 'fcc_ui_613f7131411222', 'thankyou', false ); ?></span>
          </a>
          <a href="<?php echo esc_url( project_child_fcc_link( 'san-pham.html' ) ); ?>" class="btn btn-outline">
            <span><?php echo project_child_fcc_text( 'fcc_ui_ca97c6451400a2', 'thankyou', false ); ?></span>
          </a>
        </div>

      </div>

    </div>
