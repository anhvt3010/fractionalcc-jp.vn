<?php
/**
 * Order processing, storage and email dispatch for Fractional CC.
 *
 * @package Project_Core
 */

defined( 'ABSPATH' ) || exit;

/** Register Order Custom Post Type. */
function project_core_fcc_register_order_cpt() {
	register_post_type(
		'fcc_order',
		array(
			'labels' => array(
				'name'               => 'Đơn hàng',
				'singular_name'      => 'Đơn hàng',
				'menu_name'          => 'Đơn hàng',
				'all_items'          => 'Tất cả đơn hàng',
				'add_new'            => 'Thêm đơn mới',
				'add_new_item'       => 'Thêm đơn hàng mới',
				'edit_item'          => 'Chi tiết đơn hàng',
				'view_item'          => 'Xem đơn hàng',
				'search_items'       => 'Tìm đơn hàng',
				'not_found'          => 'Không có đơn hàng nào',
				'not_found_in_trash' => 'Không có đơn hàng nào trong thùng rác',
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true, // Standalone top-level menu
			'menu_position'   => 22,   // Directly under "Nội dung website" (21)
			'menu_icon'       => 'dashicons-cart',
			'supports'        => array( 'title' ),
			'capability_type' => 'page',
			'map_meta_cap'    => true,
		)
	);
}
add_action( 'init', 'project_core_fcc_register_order_cpt' );

/**
 * Count total pending orders for the admin notification badge.
 *
 * @return int Number of pending orders.
 */
function project_core_fcc_count_pending_orders() {
	static $cached_count = null;
	if ( null !== $cached_count ) {
		return $cached_count;
	}

	global $wpdb;
	$res = $wpdb->get_var(
		"SELECT COUNT(p.ID) 
		 FROM {$wpdb->posts} p
		 LEFT JOIN {$wpdb->postmeta} pm ON (p.ID = pm.post_id AND pm.meta_key = '_fcc_order_status')
		 WHERE p.post_type = 'fcc_order'
		   AND p.post_status = 'publish'
		   AND (pm.meta_value = 'pending' OR pm.meta_value IS NULL OR pm.meta_value = '')"
	);

	$cached_count = absint( $res );
	return $cached_count;
}

/**
 * Append live notification badge to "Đơn hàng" in WordPress admin menu.
 */
function project_core_fcc_add_order_menu_badge() {
	global $menu, $submenu;

	// Remove "Thêm mới" from submenu so clicking "Đơn hàng" goes straight to order list
	remove_submenu_page( 'edit.php?post_type=fcc_order', 'post-new.php?post_type=fcc_order' );

	$pending = project_core_fcc_count_pending_orders();
	if ( $pending <= 0 ) {
		return;
	}

	$badge_html = sprintf(
		' <span class="awaiting-mod count-%1$d"><span class="pending-count" aria-hidden="true">%1$d</span><span class="screen-reader-text">%2$s</span></span>',
		$pending,
		/* translators: %d: number of new orders */
		sprintf( esc_html__( '%d đơn hàng mới', 'project-core' ), $pending )
	);

	// 1. Top-level menu item
	if ( is_array( $menu ) ) {
		foreach ( $menu as $key => $item ) {
			if ( isset( $item[2] ) && 'edit.php?post_type=fcc_order' === $item[2] ) {
				$menu[ $key ][0] .= $badge_html;
				break;
			}
		}
	}

	// 2. "Tất cả đơn hàng" submenu item
	if ( isset( $submenu['edit.php?post_type=fcc_order'] ) && is_array( $submenu['edit.php?post_type=fcc_order'] ) ) {
		foreach ( $submenu['edit.php?post_type=fcc_order'] as $s_key => $s_item ) {
			if ( isset( $s_item[2] ) && 'edit.php?post_type=fcc_order' === $s_item[2] ) {
				$submenu['edit.php?post_type=fcc_order'][ $s_key ][0] .= $badge_html;
				break;
			}
		}
	}
}
add_action( 'admin_menu', 'project_core_fcc_add_order_menu_badge', 999 );

/** Add custom styling for the order notification badge in admin. */
function project_core_fcc_order_badge_style() {
	echo '<style>
		#adminmenu .menu-top.menu-icon-fcc_order .awaiting-mod,
		#adminmenu .menu-top.menu-icon-fcc_order .awaiting-mod .pending-count {
			background-color: #d63638 !important;
			color: #ffffff !important;
			font-weight: 700 !important;
		}
		#adminmenu .menu-top.menu-icon-fcc_order:hover .awaiting-mod {
			background-color: #b32d2e !important;
		}
	</style>';
}
add_action( 'admin_head', 'project_core_fcc_order_badge_style' );

/** Customize admin columns for fcc_order list. */
function project_core_fcc_order_columns( $columns ) {
	return array(
		'cb'       => '<input type="checkbox" />',
		'title'    => 'Mã & Khách hàng',
		'phone'    => 'Số điện thoại',
		'product'  => 'Sản phẩm / Combo',
		'address'  => 'Địa chỉ',
		'status'   => 'Trạng thái',
		'date'     => 'Thời gian đặt',
	);
}
add_filter( 'manage_fcc_order_posts_columns', 'project_core_fcc_order_columns' );

/** Render custom column contents. */
function project_core_fcc_order_custom_columns( $column, $post_id ) {
	switch ( $column ) {
		case 'phone':
			$phone = get_post_meta( $post_id, '_fcc_customer_phone', true );
			if ( $phone ) {
				echo '<a href="tel:' . esc_attr( $phone ) . '"><strong>' . esc_html( $phone ) . '</strong></a>';
				echo ' <a href="https://zalo.me/' . esc_attr( preg_replace( '/[^0-9]/', '', $phone ) ) . '" target="_blank" rel="noopener noreferrer" style="color:#0068FF;font-size:12px;margin-left:4px;">[Zalo]</a>';
			} else {
				echo '—';
			}
			break;

		case 'product':
			$item = get_post_meta( $post_id, '_fcc_selected_item_name', true );
			echo $item ? esc_html( $item ) : '—';
			break;

		case 'address':
			$addr = get_post_meta( $post_id, '_fcc_customer_address', true );
			echo $addr ? esc_html( wp_trim_words( $addr, 10 ) ) : '<span style="color:#888;">(Cung cấp khi gọi)</span>';
			break;

		case 'status':
			$status = get_post_meta( $post_id, '_fcc_order_status', true ) ?: 'pending';
			$badges = array(
				'pending'   => array( 'label' => 'Chờ xử lý', 'bg' => '#FFF3CD', 'color' => '#856404' ),
				'contacted' => array( 'label' => 'Đã liên hệ', 'bg' => '#CCE5FF', 'color' => '#004085' ),
				'completed' => array( 'label' => 'Hoàn thành', 'bg' => '#D4EDDA', 'color' => '#155724' ),
				'cancelled' => array( 'label' => 'Đã hủy', 'bg' => '#F8D7DA', 'color' => '#721C24' ),
			);
			$info = isset( $badges[ $status ] ) ? $badges[ $status ] : $badges['pending'];
			echo '<span style="display:inline-block;padding:3px 8px;border-radius:4px;font-size:12px;font-weight:600;background:' . esc_attr( $info['bg'] ) . ';color:' . esc_attr( $info['color'] ) . ';">' . esc_html( $info['label'] ) . '</span>';
			break;
	}
}
add_action( 'manage_fcc_order_posts_custom_column', 'project_core_fcc_order_custom_columns', 10, 2 );

/**
 * Add custom status filter tabs (Tất cả, Chờ xử lý, Đã liên hệ, Hoàn thành, Đã hủy) above order list table.
 */
function project_core_fcc_order_status_views( $views ) {
	global $wpdb;

	$counts = array(
		'all'       => (int) wp_count_posts( 'fcc_order' )->publish,
		'pending'   => 0,
		'contacted' => 0,
		'completed' => 0,
		'cancelled' => 0,
	);

	$statuses = $wpdb->get_results(
		"SELECT pm.meta_value AS status, COUNT(p.ID) AS total
		 FROM {$wpdb->posts} p
		 LEFT JOIN {$wpdb->postmeta} pm ON (p.ID = pm.post_id AND pm.meta_key = '_fcc_order_status')
		 WHERE p.post_type = 'fcc_order' AND p.post_status = 'publish'
		 GROUP BY pm.meta_value"
	);

	foreach ( $statuses as $row ) {
		$st = $row->status ?: 'pending';
		if ( isset( $counts[ $st ] ) ) {
			$counts[ $st ] += (int) $row->total;
		}
	}

	$current_status = isset( $_GET['order_status'] ) ? sanitize_text_field( wp_unslash( $_GET['order_status'] ) ) : '';
	$base_url       = admin_url( 'edit.php?post_type=fcc_order' );

	$labels = array(
		''          => sprintf( 'Tất cả (%d)', $counts['all'] ),
		'pending'   => sprintf( 'Chờ xử lý (Mới) (%d)', $counts['pending'] ),
		'contacted' => sprintf( 'Đã liên hệ (%d)', $counts['contacted'] ),
		'completed' => sprintf( 'Hoàn thành (%d)', $counts['completed'] ),
		'cancelled' => sprintf( 'Đã hủy (%d)', $counts['cancelled'] ),
	);

	$new_views = array();
	foreach ( $labels as $st_key => $st_label ) {
		$class = ( $current_status === $st_key ) ? 'class="current"' : '';
		$url   = $st_key ? add_query_arg( 'order_status', $st_key, $base_url ) : $base_url;
		$new_views[ $st_key ?: 'all' ] = sprintf( '<a href="%1$s" %2$s>%3$s</a>', esc_url( $url ), $class, esc_html( $st_label ) );
	}

	return $new_views;
}
add_filter( 'views_edit-fcc_order', 'project_core_fcc_order_status_views' );

/** Filter order list query by order_status meta. */
function project_core_fcc_filter_orders_by_status( $query ) {
	global $pagenow;
	if ( ! is_admin() || 'edit.php' !== $pagenow || ! $query->is_main_query() || 'fcc_order' !== $query->get( 'post_type' ) ) {
		return;
	}

	if ( ! empty( $_GET['order_status'] ) ) {
		$status = sanitize_text_field( wp_unslash( $_GET['order_status'] ) );
		$valid  = array( 'pending', 'contacted', 'completed', 'cancelled' );
		if ( in_array( $status, $valid, true ) ) {
			$meta_query = $query->get( 'meta_query' ) ?: array();
			$meta_query[] = array(
				'key'     => '_fcc_order_status',
				'value'   => $status,
				'compare' => '=',
			);
			$query->set( 'meta_query', $meta_query );
		}
	}
}
add_action( 'pre_get_posts', 'project_core_fcc_filter_orders_by_status' );

/** Add order details metabox. */
function project_core_fcc_order_metaboxes() {
	add_meta_box(
		'fcc_order_details',
		'Chi tiết thông tin đơn hàng',
		'project_core_fcc_render_order_metabox',
		'fcc_order',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'project_core_fcc_order_metaboxes' );

/** Render order details metabox. */
function project_core_fcc_render_order_metabox( $post ) {
	wp_nonce_field( 'fcc_order_status_nonce', 'fcc_order_status_token' );

	$code    = get_post_meta( $post->ID, '_fcc_order_code', true );
	$name    = get_post_meta( $post->ID, '_fcc_customer_name', true );
	$phone   = get_post_meta( $post->ID, '_fcc_customer_phone', true );
	$address = get_post_meta( $post->ID, '_fcc_customer_address', true );
	$note    = get_post_meta( $post->ID, '_fcc_customer_note', true );
	$item    = get_post_meta( $post->ID, '_fcc_selected_item_name', true );
	$ip      = get_post_meta( $post->ID, '_fcc_customer_ip', true );
	$source  = get_post_meta( $post->ID, '_fcc_source_url', true );
	$status  = get_post_meta( $post->ID, '_fcc_order_status', true ) ?: 'pending';
	?>
	<table class="form-table" role="presentation">
		<tr>
			<th scope="row">Mã đơn hàng</th>
			<td><strong style="font-size:16px;color:#9E6B0D;"><?php echo esc_html( $code ?: $post->post_title ); ?></strong></td>
		</tr>
		<tr>
			<th scope="row">Trạng thái xử lý</th>
			<td>
				<select name="fcc_order_status" id="fcc_order_status" style="font-weight:600;">
					<option value="pending" <?php selected( $status, 'pending' ); ?>>Chờ xử lý (Mới)</option>
					<option value="contacted" <?php selected( $status, 'contacted' ); ?>>Đã liên hệ tư vấn</option>
					<option value="completed" <?php selected( $status, 'completed' ); ?>>Hoàn thành (Đã gửi hàng / Chốt đơn)</option>
					<option value="cancelled" <?php selected( $status, 'cancelled' ); ?>>Đã hủy</option>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row">Họ và tên khách</th>
			<td><strong style="font-size:15px;"><?php echo esc_html( $name ); ?></strong></td>
		</tr>
		<tr>
			<th scope="row">Số điện thoại</th>
			<td>
				<a href="tel:<?php echo esc_attr( $phone ); ?>" class="button button-secondary" style="font-size:14px;font-weight:bold;">📞 Gọi: <?php echo esc_html( $phone ); ?></a>
				<a href="https://zalo.me/<?php echo esc_attr( preg_replace( '/[^0-9]/', '', $phone ) ); ?>" target="_blank" rel="noopener noreferrer" class="button button-primary" style="margin-left:8px;">Nhắn tin qua Zalo</a>
			</td>
		</tr>
		<tr>
			<th scope="row">Sản phẩm / Combo</th>
			<td><strong style="color:#2B1E16;"><?php echo esc_html( $item ); ?></strong></td>
		</tr>
		<tr>
			<th scope="row">Địa chỉ nhận hàng</th>
			<td><?php echo esc_html( $address ?: 'Chưa nhập (xác nhận lại qua điện thoại)' ); ?></td>
		</tr>
		<tr>
			<th scope="row">Lời nhắn / Tình trạng da</th>
			<td><?php echo nl2br( esc_html( $note ?: 'Không có' ) ); ?></td>
		</tr>
		<tr>
			<th scope="row">Thông tin kỹ thuật</th>
			<td style="color:#777;font-size:12px;">
				IP: <code><?php echo esc_html( $ip ?: '—' ); ?></code> | Trang đặt: <code><?php echo esc_html( $source ?: '—' ); ?></code>
			</td>
		</tr>
	</table>
	<?php
}

/** Save status update from order edit screen. */
function project_core_fcc_save_order_metabox( $post_id ) {
	if ( ! isset( $_POST['fcc_order_status_token'] ) || ! wp_verify_nonce( $_POST['fcc_order_status_token'], 'fcc_order_status_nonce' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_page', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['fcc_order_status'] ) ) {
		$valid_statuses = array( 'pending', 'contacted', 'completed', 'cancelled' );
		$status = sanitize_text_field( wp_unslash( $_POST['fcc_order_status'] ) );
		if ( in_array( $status, $valid_statuses, true ) ) {
			update_post_meta( $post_id, '_fcc_order_status', $status );
		}
	}
}
add_action( 'save_post_fcc_order', 'project_core_fcc_save_order_metabox' );

/**
 * Handle order submissions from both AJAX and standard POST.
 */
function project_core_fcc_handle_order_submission() {
	// 1. Anti-spam honeypot
	if ( ! empty( $_POST['website_url'] ) ) {
		if ( wp_doing_ajax() ) {
			wp_send_json_success( array( 'orderCode' => 'FCC-000000', 'message' => 'Yêu cầu đã được tiếp nhận.' ) );
		} else {
			wp_safe_redirect( project_core_fcc_url( 'thankyou' ) );
			exit;
		}
	}

	// 2. Nonce verification
	$nonce = isset( $_POST['_fcc_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_fcc_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'fcc_order_action' ) && ! wp_verify_nonce( $nonce, 'project_core_fcc_lead' ) ) {
		$error_msg = 'Phiên làm việc đã hết hạn hoặc không hợp lệ. Vui lòng tải lại trang.';
		if ( wp_doing_ajax() ) {
			wp_send_json_error( array( 'message' => $error_msg ) );
		} else {
			wp_die( esc_html( $error_msg ), '', array( 'response' => 403 ) );
		}
	}

	// 3. Sanitize inputs
	$name    = isset( $_POST['customer_name'] ) ? sanitize_text_field( wp_unslash( $_POST['customer_name'] ) ) : '';
	$phone   = isset( $_POST['customer_phone'] ) ? preg_replace( '/\s+/', '', sanitize_text_field( wp_unslash( $_POST['customer_phone'] ) ) ) : '';
	$item    = isset( $_POST['selected_item'] ) ? sanitize_text_field( wp_unslash( $_POST['selected_item'] ) ) : '';
	$address = isset( $_POST['customer_address'] ) ? sanitize_text_field( wp_unslash( $_POST['customer_address'] ) ) : '';
	$note    = isset( $_POST['customer_note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['customer_note'] ) ) : '';
	$item_name_client = isset( $_POST['selected_item_name'] ) ? sanitize_text_field( wp_unslash( $_POST['selected_item_name'] ) ) : '';

	// 4. Validate requirements
	if ( mb_strlen( $name ) < 2 ) {
		$error = 'Vui lòng nhập họ và tên của bạn (tối thiểu 2 ký tự).';
		if ( wp_doing_ajax() ) { wp_send_json_error( array( 'message' => $error ) ); }
		else { wp_die( esc_html( $error ) ); }
	}

	$phone_clean = preg_replace( '/[^0-9+]/', '', $phone );
	if ( ! preg_match( '/^(0|\+84)(3|5|7|8|9)[0-9]{8}$/', $phone_clean ) ) {
		$error = 'Số điện thoại không đúng định dạng Việt Nam (10 số, bắt đầu bằng 0 hoặc +84).';
		if ( wp_doing_ajax() ) { wp_send_json_error( array( 'message' => $error ) ); }
		else { wp_die( esc_html( $error ) ); }
	}

	if ( empty( $item ) ) {
		$error = 'Vui lòng chọn Combo hoặc Sản phẩm bạn muốn đặt.';
		if ( wp_doing_ajax() ) { wp_send_json_error( array( 'message' => $error ) ); }
		else { wp_die( esc_html( $error ) ); }
	}

	// 5. Resolve Product/Combo Name
	$resolved_item_name = $item_name_client;
	if ( 0 === strpos( $item, 'combo_' ) ) {
		$combo_id = absint( substr( $item, 6 ) );
		foreach ( project_core_fcc_combos() as $c ) {
			if ( $c['id'] === $combo_id ) {
				$resolved_item_name = $c['name'] . ( $c['salePrice'] ? ' (' . number_format( $c['salePrice'], 0, ',', '.' ) . ' đ)' : '' );
				break;
			}
		}
	} elseif ( 0 === strpos( $item, 'prod_' ) ) {
		$prod_id = absint( substr( $item, 5 ) );
		$product = project_core_fcc_product( $prod_id, false );
		if ( ! empty( $product['name'] ) ) {
			$resolved_item_name = $product['name'] . ( ! empty( $product['salePrice'] ) ? ' (' . number_format( $product['salePrice'], 0, ',', '.' ) . ' đ)' : '' );
		}
	}
	if ( empty( $resolved_item_name ) ) {
		$resolved_item_name = $item;
	}

	// 6. Generate order code and save to DB
	$order_code = 'FCC-' . wp_rand( 100000, 999999 );
	$post_id    = wp_insert_post(
		array(
			'post_type'   => 'fcc_order',
			'post_status' => 'publish',
			'post_title'  => sprintf( '%s – %s', $name, $order_code ),
		),
		true
	);

	if ( ! is_wp_error( $post_id ) ) {
		update_post_meta( $post_id, '_fcc_order_code', $order_code );
		update_post_meta( $post_id, '_fcc_customer_name', $name );
		update_post_meta( $post_id, '_fcc_customer_phone', $phone_clean );
		update_post_meta( $post_id, '_fcc_customer_address', $address );
		update_post_meta( $post_id, '_fcc_customer_note', $note );
		update_post_meta( $post_id, '_fcc_selected_item', $item );
		update_post_meta( $post_id, '_fcc_selected_item_name', $resolved_item_name );
		update_post_meta( $post_id, '_fcc_customer_ip', sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? '' ) );
		update_post_meta( $post_id, '_fcc_source_url', esc_url_raw( wp_get_referer() ?: '' ) );
		update_post_meta( $post_id, '_fcc_order_status', 'pending' );
	}

	// 7. Dispatch Email Notification via SMTP/wp_mail()
	$mail_sent = project_core_fcc_send_order_email(
		array(
			'order_code' => $order_code,
			'name'       => $name,
			'phone'      => $phone_clean,
			'address'    => $address,
			'note'       => $note,
			'product'    => $resolved_item_name,
			'post_id'    => is_numeric( $post_id ) ? $post_id : 0,
		)
	);

	// 8. Respond to client
	$thankyou_url = add_query_arg(
		array(
			'order' => $order_code,
		),
		project_core_fcc_url( 'thankyou' )
	);

	if ( wp_doing_ajax() ) {
		wp_send_json_success(
			array(
				'orderCode'     => $order_code,
				'customerName'  => $name,
				'customerPhone' => $phone_clean,
				'selectedItem'  => $resolved_item_name,
				'mailSent'      => $mail_sent,
				'redirectUrl'   => $thankyou_url,
				'message'       => 'Đặt hàng thành công! BUI DANG-JP sẽ liên hệ với bạn trong thời gian sớm nhất.',
			)
		);
	} else {
		wp_safe_redirect( $thankyou_url );
		exit;
	}
}
add_action( 'wp_ajax_fcc_submit_order', 'project_core_fcc_handle_order_submission' );
add_action( 'wp_ajax_nopriv_fcc_submit_order', 'project_core_fcc_handle_order_submission' );
add_action( 'admin_post_fcc_submit_order', 'project_core_fcc_handle_order_submission' );
add_action( 'admin_post_nopriv_fcc_submit_order', 'project_core_fcc_handle_order_submission' );
add_action( 'wp_ajax_project_core_fcc_lead', 'project_core_fcc_handle_order_submission' );
add_action( 'wp_ajax_nopriv_project_core_fcc_lead', 'project_core_fcc_handle_order_submission' );
add_action( 'admin_post_project_core_fcc_lead', 'project_core_fcc_handle_order_submission' );
add_action( 'admin_post_nopriv_project_core_fcc_lead', 'project_core_fcc_handle_order_submission' );

/**
 * Build and dispatch the HTML notification email to the admin.
 */
function project_core_fcc_send_order_email( $order ) {
	$settings  = function_exists( 'project_core_fcc_get_smtp_settings' ) ? project_core_fcc_get_smtp_settings() : array();
	$recipient = ! empty( $settings['notify_email'] ) ? $settings['notify_email'] : get_option( 'admin_email' );

	if ( ! is_email( $recipient ) ) {
		return false;
	}

	$subject_template = ! empty( $settings['notify_subject'] ) ? $settings['notify_subject'] : '[Đơn hàng mới] Yêu cầu từ {customer_name} – Fractional CC';
	$subject = str_replace(
		array( '{customer_name}', '{order_code}', '{product_name}' ),
		array( $order['name'], $order['order_code'], $order['product'] ),
		$subject_template
	);

	$admin_order_url  = $order['post_id'] ? admin_url( 'post.php?post=' . $order['post_id'] . '&action=edit' ) : admin_url( 'edit.php?post_type=fcc_order' );
	$current_time_str = current_time( 'd/m/Y • H:i' );
	$zalo_phone       = preg_replace( '/[^0-9]/', '', $order['phone'] );

	$html = '<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>' . esc_html( $subject ) . '</title>
<style>
  body, table, td, th, p, a, li, blockquote, span, div, h1, h2, h3 { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; font-family: Arial, Helvetica, sans-serif !important; }
  table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-family: Arial, Helvetica, sans-serif; }
  img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
  body { margin: 0; padding: 0; width: 100% !important; background-color: #F4EFE6; font-family: Arial, Helvetica, sans-serif; color: #2B1E16; -webkit-font-smoothing: antialiased; }
  @media only screen and (max-width: 620px) {
    .email-container { width: 100% !important; }
    .email-content { padding: 24px 16px !important; }
    .email-header-inner { padding: 26px 16px 22px !important; }
    .meta-col { display: block !important; width: 100% !important; text-align: left !important; padding: 10px 14px !important; }
  }
</style>
</head>
<body style="margin:0;padding:0;background-color:#F4EFE6;font-family:Arial,Helvetica,sans-serif;-webkit-font-smoothing:antialiased;">
  <!-- Outer Wrapper Table -->
  <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#F4EFE6" style="background-color:#F4EFE6;padding:32px 12px;font-family:Arial,Helvetica,sans-serif;">
    <tr>
      <td align="center" style="font-family:Arial,Helvetica,sans-serif;">
        <!-- Main Card Container (Width 680px) -->
        <table width="100%" cellpadding="0" cellspacing="0" border="0" class="email-container" style="max-width:680px;width:100%;background-color:#FFFFFF;border-radius:14px;border:1px solid #DFCEB2;box-shadow:0 8px 30px rgba(43,30,22,0.08);overflow:hidden;font-family:Arial,Helvetica,sans-serif;">
          
          <!-- Header: Obsidian Black + Luxury Gold Border -->
          <tr>
            <td class="email-header-inner" style="background-color:#18110D;background:linear-gradient(145deg,#251912 0%,#140E0A 100%);padding:36px 32px 30px;text-align:center;border-bottom:3px solid #C5A059;font-family:Arial,Helvetica,sans-serif;">
              
              <!-- Brand Logo Duo: Fractional CC x BUI DANG-JP -->
              <table align="center" cellpadding="0" cellspacing="0" border="0" style="margin:0 auto 20px auto;font-family:Arial,Helvetica,sans-serif;">
                <tr>
                  <!-- Fractional CC Logo -->
                  <td align="center" valign="middle" style="padding-right:18px;font-family:Arial,Helvetica,sans-serif;">
                    <img src="cid:fcc_brand_logo" alt="Fractional CC Nhật Bản" width="165" style="display:block;width:165px;height:auto;max-width:165px;border:0;">
                  </td>
                  
                  <!-- Divider Line -->
                  <td align="center" valign="middle" style="padding:0 4px;">
                    <div style="width:1px;height:38px;background-color:rgba(197,160,89,0.4);line-height:38px;">&nbsp;</div>
                  </td>
                  
                  <!-- BUI DANG-JP Distributor Badge -->
                  <td align="left" valign="middle" style="padding-left:18px;font-family:Arial,Helvetica,sans-serif;">
                    <table cellpadding="0" cellspacing="0" border="0" style="font-family:Arial,Helvetica,sans-serif;">
                      <tr>
                        <td align="center" valign="middle" style="background-color:#FFFFFF;border-radius:50%;width:38px;height:38px;text-align:center;box-shadow:0 2px 6px rgba(0,0,0,0.3);padding:0;">
                          <img src="cid:buidang_brand_logo" alt="BUI DANG-JP" width="30" height="30" style="display:block;margin:auto;width:30px;height:30px;border-radius:50%;border:0;">
                        </td>
                        <td valign="middle" style="padding-left:10px;text-align:left;font-family:Arial,Helvetica,sans-serif;">
                          <span style="display:block;font-size:9px;color:#C5A059;letter-spacing:1.5px;font-weight:700;text-transform:uppercase;line-height:1.2;font-family:Arial,Helvetica,sans-serif;">PHÂN PHỐI ĐỘC QUYỀN</span>
                          <span style="display:block;font-size:13px;color:#FFFFFF;font-weight:700;letter-spacing:0.8px;line-height:1.2;font-family:Arial,Helvetica,sans-serif;">BUI DANG-JP</span>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>

              <!-- Badge Tag -->
              <table align="center" cellpadding="0" cellspacing="0" border="0" style="margin:0 auto 12px auto;font-family:Arial,Helvetica,sans-serif;">
                <tr>
                  <td style="background-color:rgba(197,160,89,0.15);border:1px solid rgba(197,160,89,0.35);border-radius:20px;padding:5px 18px;text-align:center;font-family:Arial,Helvetica,sans-serif;">
                    <span style="color:#E5C178;font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;font-family:Arial,Helvetica,sans-serif;">ĐƠN ĐẶT HÀNG TRỰC TUYẾN</span>
                  </td>
                </tr>
              </table>

              <!-- Main Header Title -->
              <h1 style="margin:0;font-size:23px;font-weight:800;color:#FFFFFF;letter-spacing:0.5px;line-height:1.3;text-transform:uppercase;font-family:Arial,Helvetica,sans-serif;">
                THÔNG BÁO ĐƠN HÀNG MỚI
              </h1>
            </td>
          </tr>

          <!-- Main Content Body -->
          <tr>
            <td class="email-content" style="padding:32px 36px 36px;font-family:Arial,Helvetica,sans-serif;">

              <!-- Order Meta Banner: 2-column Table layout -->
              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#FAF5EC;border:1px solid #ECDDC5;border-radius:8px;margin-bottom:28px;font-family:Arial,Helvetica,sans-serif;">
                <tr>
                  <td class="meta-col" align="left" valign="middle" style="padding:16px 20px;font-family:Arial,Helvetica,sans-serif;">
                    <span style="display:block;font-size:10px;color:#8A7561;text-transform:uppercase;letter-spacing:1px;font-weight:700;margin-bottom:3px;font-family:Arial,Helvetica,sans-serif;">MÃ ĐƠN HÀNG</span>
                    <span style="font-size:22px;font-weight:800;color:#9E6B0D;letter-spacing:0.5px;font-family:Arial,Helvetica,sans-serif;">#' . esc_html( $order['order_code'] ) . '</span>
                  </td>
                  <td class="meta-col" align="right" valign="middle" style="padding:16px 20px;text-align:right;font-family:Arial,Helvetica,sans-serif;">
                    <span style="display:block;font-size:10px;color:#8A7561;text-transform:uppercase;letter-spacing:1px;font-weight:700;margin-bottom:3px;font-family:Arial,Helvetica,sans-serif;">THỜI GIAN ĐẶT HÀNG</span>
                    <span style="font-size:14px;font-weight:700;color:#2B1E16;font-family:Arial,Helvetica,sans-serif;">' . esc_html( $current_time_str ) . '</span>
                  </td>
                </tr>
              </table>

              <!-- Section Title: Customer Info -->
              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:14px;font-family:Arial,Helvetica,sans-serif;">
                <tr>
                  <td style="border-left:4px solid #C5A059;padding-left:12px;font-family:Arial,Helvetica,sans-serif;">
                    <span style="font-size:14px;font-weight:800;color:#241812;text-transform:uppercase;letter-spacing:0.5px;font-family:Arial,Helvetica,sans-serif;">THÔNG TIN KHÁCH HÀNG &amp; CHI TIẾT ĐƠN HÀNG</span>
                  </td>
                </tr>
              </table>

              <!-- Information Table -->
              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;margin-bottom:28px;border:1px solid #EFE8DD;border-radius:8px;overflow:hidden;font-family:Arial,Helvetica,sans-serif;">
                <!-- Họ và tên -->
                <tr>
                  <td width="32%" valign="top" style="padding:14px 18px;background-color:#FBF9F6;border-bottom:1px solid #EFE8DD;color:#6A5C52;font-size:13px;font-weight:700;font-family:Arial,Helvetica,sans-serif;">
                    Họ và tên khách hàng
                  </td>
                  <td width="68%" valign="top" style="padding:14px 18px;background-color:#FFFFFF;border-bottom:1px solid #EFE8DD;color:#18110D;font-size:16px;font-weight:700;font-family:Arial,Helvetica,sans-serif;">
                    ' . esc_html( $order['name'] ) . '
                  </td>
                </tr>

                <!-- Số điện thoại + Quick actions -->
                <tr>
                  <td valign="top" style="padding:14px 18px;background-color:#FBF9F6;border-bottom:1px solid #EFE8DD;color:#6A5C52;font-size:13px;font-weight:700;font-family:Arial,Helvetica,sans-serif;">
                    Số điện thoại
                  </td>
                  <td valign="middle" style="padding:14px 18px;background-color:#FFFFFF;border-bottom:1px solid #EFE8DD;font-family:Arial,Helvetica,sans-serif;">
                    <table cellpadding="0" cellspacing="0" border="0" style="font-family:Arial,Helvetica,sans-serif;">
                      <tr>
                        <td valign="middle" style="font-family:Arial,Helvetica,sans-serif;">
                          <a href="tel:' . esc_attr( $order['phone'] ) . '" style="color:#0056b3;font-size:18px;font-weight:800;text-decoration:none;letter-spacing:0.5px;font-family:Arial,Helvetica,sans-serif;">' . esc_html( $order['phone'] ) . '</a>
                        </td>
                        <td valign="middle" style="padding-left:14px;font-family:Arial,Helvetica,sans-serif;">
                          <a href="tel:' . esc_attr( $order['phone'] ) . '" style="display:inline-block;background-color:#EBF3FC;border:1px solid #BED8F6;color:#0056b3;font-size:12px;font-weight:700;padding:5px 12px;border-radius:4px;text-decoration:none;font-family:Arial,Helvetica,sans-serif;">Gọi điện</a>
                        </td>
                        <td valign="middle" style="padding-left:8px;font-family:Arial,Helvetica,sans-serif;">
                          <a href="https://zalo.me/' . esc_attr( $zalo_phone ) . '" target="_blank" style="display:inline-block;background-color:#0068FF;color:#FFFFFF;font-size:12px;font-weight:700;padding:5px 12px;border-radius:4px;text-decoration:none;font-family:Arial,Helvetica,sans-serif;">Chat Zalo</a>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>

                <!-- Sản phẩm / Combo -->
                <tr>
                  <td valign="top" style="padding:14px 18px;background-color:#FBF9F6;border-bottom:1px solid #EFE8DD;color:#6A5C52;font-size:13px;font-weight:700;font-family:Arial,Helvetica,sans-serif;">
                    Sản phẩm / Combo chọn
                  </td>
                  <td valign="top" style="padding:14px 18px;background-color:#FFFFFF;border-bottom:1px solid #EFE8DD;font-family:Arial,Helvetica,sans-serif;">
                    <div style="background-color:#FFF9EE;border:1px solid #F0DFBF;border-left:4px solid #C5A059;padding:12px 14px;border-radius:6px;font-family:Arial,Helvetica,sans-serif;">
                      <span style="display:block;font-size:15px;font-weight:700;color:#8A5800;line-height:1.4;font-family:Arial,Helvetica,sans-serif;">' . esc_html( $order['product'] ) . '</span>
                    </div>
                  </td>
                </tr>

                <!-- Địa chỉ giao hàng -->
                <tr>
                  <td valign="top" style="padding:14px 18px;background-color:#FBF9F6;border-bottom:1px solid #EFE8DD;color:#6A5C52;font-size:13px;font-weight:700;font-family:Arial,Helvetica,sans-serif;">
                    Địa chỉ nhận hàng
                  </td>
                  <td valign="top" style="padding:14px 18px;background-color:#FFFFFF;border-bottom:1px solid #EFE8DD;color:#2B1E16;font-size:14px;line-height:1.5;font-family:Arial,Helvetica,sans-serif;">
                    ' . esc_html( $order['address'] ?: 'Chưa nhập (xác nhận lại qua điện thoại khi gọi)' ) . '
                  </td>
                </tr>

                <!-- Lời nhắn / Tình trạng da -->
                <tr>
                  <td valign="top" style="padding:14px 18px;background-color:#FBF9F6;color:#6A5C52;font-size:13px;font-weight:700;font-family:Arial,Helvetica,sans-serif;">
                    Lời nhắn / Tình trạng da
                  </td>
                  <td valign="top" style="padding:14px 18px;background-color:#FFFFFF;font-family:Arial,Helvetica,sans-serif;">
                    <div style="background-color:#FBFBFB;border:1px dashed #D9CEBF;padding:12px 14px;border-radius:6px;font-size:13px;color:#4A4038;line-height:1.5;font-family:Arial,Helvetica,sans-serif;">
                      ' . ( $order['note'] ? nl2br( esc_html( $order['note'] ) ) : 'Không có ghi chú' ) . '
                    </div>
                  </td>
                </tr>
              </table>

              <!-- Call to Action Button: Direct to Order edit screen -->
              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:28px 0 10px;font-family:Arial,Helvetica,sans-serif;">
                <tr>
                  <td align="center" style="font-family:Arial,Helvetica,sans-serif;">
                    <a href="' . esc_url( $admin_order_url ) . '" target="_blank" style="display:inline-block;background-color:#C5A059;background:linear-gradient(135deg,#D4AF67 0%,#A67C2E 100%);color:#FFFFFF !important;text-decoration:none;font-weight:800;font-size:14px;letter-spacing:0.5px;padding:15px 36px;border-radius:30px;box-shadow:0 6px 18px rgba(166,124,46,0.35);text-transform:uppercase;font-family:Arial,Helvetica,sans-serif;">
                      XEM &amp; XỬ LÝ ĐƠN HÀNG TRONG ADMIN &rarr;
                    </a>
                  </td>
                </tr>
              </table>

            </td>
          </tr>

          <!-- Footer Information -->
          <tr>
            <td style="background-color:#FAF8F5;border-top:1px solid #EFE8DD;padding:24px 32px;text-align:center;font-family:Arial,Helvetica,sans-serif;">
              <p style="margin:0 0 6px 0;font-size:12px;color:#4A4038;line-height:1.6;font-family:Arial,Helvetica,sans-serif;">
                <strong style="color:#2B1E16;font-family:Arial,Helvetica,sans-serif;">FRACTIONAL CC NHẬT BẢN</strong> &bull; Phân phối độc quyền bởi <strong style="color:#2B1E16;font-family:Arial,Helvetica,sans-serif;">CÔNG TY TNHH BUI DANG-JP</strong>
              </p>
              <p style="margin:0 0 6px 0;font-size:12px;color:#7A6E65;line-height:1.6;font-family:Arial,Helvetica,sans-serif;">
                Hotline tư vấn: <strong style="color:#9E6B0D;font-family:Arial,Helvetica,sans-serif;">0985 561 862</strong> &bull; Văn phòng: Tầng 6, Số 35 Phố Trần Thái Tông, Cầu Giấy, Hà Nội
              </p>
              <p style="margin:0;font-size:11px;color:#A39990;line-height:1.5;font-family:Arial,Helvetica,sans-serif;">
                Email thông báo tự động từ hệ thống quản trị Fractional CC Vietnam.
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>';

	$headers = array(
		'Content-Type: text/html; charset=UTF-8',
	);

	if ( function_exists( 'project_core_fcc_attach_brand_logos' ) ) {
		add_action( 'phpmailer_init', 'project_core_fcc_attach_brand_logos' );
	}

	$sent = wp_mail( $recipient, $subject, $html, $headers );

	if ( function_exists( 'project_core_fcc_attach_brand_logos' ) ) {
		remove_action( 'phpmailer_init', 'project_core_fcc_attach_brand_logos' );
	}

	return $sent;
}
