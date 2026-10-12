<?php
defined( 'ABSPATH' ) || exit;

$order_code_param = isset( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : '';
$order_data = null;
if ( $order_code_param ) {
	$orders = get_posts(
		array(
			'post_type'      => 'fcc_order',
			'meta_key'       => '_fcc_order_code',
			'meta_value'     => $order_code_param,
			'posts_per_page' => 1,
			'post_status'    => 'publish',
		)
	);
	if ( $orders ) {
		$order_id   = $orders[0]->ID;
		$order_data = array(
			'code'    => get_post_meta( $order_id, '_fcc_order_code', true ) ?: $order_code_param,
			'name'    => get_post_meta( $order_id, '_fcc_customer_name', true ),
			'phone'   => get_post_meta( $order_id, '_fcc_customer_phone', true ),
			'product' => get_post_meta( $order_id, '_fcc_selected_item_name', true ),
		);
	}
}

$display_code  = ! empty( $order_data['code'] ) ? '#' . $order_data['code'] : project_child_fcc_raw_text( 'fcc_ui_a65907ab27fa76', 'thankyou' );
$display_name  = ! empty( $order_data['name'] ) ? $order_data['name'] : project_child_fcc_raw_text( 'fcc_ui_d2b5a409c75e1d', 'thankyou' );
$display_phone = '0985 ••• •••';
if ( ! empty( $order_data['phone'] ) ) {
	$p = $order_data['phone'];
	$display_phone = mb_strlen( $p ) >= 7 ? mb_substr( $p, 0, 4 ) . ' ••• ' . mb_substr( $p, -3 ) : $p;
}
$display_product = ! empty( $order_data['product'] ) ? $order_data['product'] : project_child_fcc_raw_text( 'fcc_ui_928a1a1b41ef6f', 'thankyou' );
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

        
        <div class="receipt-box" id="receipt-container">
          <div class="receipt-head">
            <span class="receipt-id-tag"><?php echo project_child_fcc_text( 'fcc_ui_1d9342e03ca91a', 'thankyou' ); ?> <strong id="order-id-val"><?php echo esc_html( $display_code ); ?></strong></span>
            <span class="receipt-status-badge"><?php echo project_child_fcc_text( 'fcc_ui_c65a1c5fa53533', 'thankyou' ); ?></span>
          </div>

          <div class="receipt-grid">
            <div class="receipt-item">
              <span class="receipt-label"><?php echo project_child_fcc_text( 'fcc_ui_516fb55969ee2b', 'thankyou' ); ?></span>
              <span class="receipt-value" id="order-name-val"><?php echo esc_html( $display_name ); ?></span>
            </div>
            <div class="receipt-item">
              <span class="receipt-label"><?php echo project_child_fcc_text( 'fcc_ui_60ae7f2bd5a911', 'thankyou' ); ?></span>
              <span class="receipt-value" id="order-phone-val"><?php echo esc_html( $display_phone ); ?></span>
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
            <img src="<?php echo esc_url( project_child_fcc_image( 'fcc_ui_453bd451ef0751' ) ); ?>" alt="<?php echo esc_attr( project_child_fcc_image_alt( 'fcc_ui_453bd451ef0751', 'fcc_ui_963cafcaa053a1' ) ); ?>" class="receipt-product-thumb" id="order-img-val">
            <div class="receipt-product-info">
              <div class="receipt-product-title" id="order-product-val"><?php echo esc_html( $display_product ); ?></div>
              <div class="receipt-product-gift"><?php echo project_child_fcc_text( 'fcc_ui_7cb383d4ca10a5', 'thankyou' ); ?></div>
            </div>
          </div>
        </div>

        
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
          <a href="<?php echo esc_url( project_child_fcc_link( 'https://zalo.me/0985561862' ) ); ?>" target="_blank" rel="noopener noreferrer" class="btn-zalo-priority">
            <svg width="20" height="20" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 2C6.477 2 2 6.03 2 11C2 13.565 3.195 15.864 5.12 17.472L4.2 21.2C4.085 21.664 4.545 22.052 4.965 21.848L9.29 19.742C10.158 19.911 11.063 20 12 20C17.523 20 22 15.97 22 11C22 6.03 17.523 2 12 2Z" fill="#FFFFFF"></path>
            </svg>
            <span><?php echo project_child_fcc_text( 'fcc_ui_a69dd64e68dae5', 'thankyou', false ); ?></span>
          </a>
          <a href="<?php echo esc_url( project_child_fcc_link( 'tel:0985561862' ) ); ?>" class="btn btn-outline" style="font-weight: 700;">
            <span><?php echo project_child_fcc_text( 'fcc_ui_613f7131411222', 'thankyou', false ); ?></span>
          </a>
          <a href="<?php echo esc_url( project_child_fcc_link( 'san-pham.html' ) ); ?>" class="btn btn-outline">
            <span><?php echo project_child_fcc_text( 'fcc_ui_ca97c6451400a2', 'thankyou', false ); ?></span>
          </a>
        </div>

      </div>

    </div>
