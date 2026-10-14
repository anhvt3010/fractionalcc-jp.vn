<?php
/**
 * Fractional CC Contact Settings Management.
 * Provides a dedicated admin page to manage Hotline, Zalo, Messenger/Facebook, and Email.
 *
 * @package Project_Core
 */

defined( 'ABSPATH' ) || exit;

/** Normalize one public contact field without inventing a replacement value. */
function project_core_fcc_normalize_contact( $name, $value ) {
	if ( ! is_string( $value ) && ! is_numeric( $value ) ) {
		return new WP_Error( 'fcc_contact_invalid', 'Thông tin liên hệ phải là văn bản.' );
	}
	$value = trim( (string) $value );
	if ( '' === $value ) { return ''; }
	if ( strlen( $value ) > 2000 ) { return new WP_Error( 'fcc_contact_invalid', 'Thông tin liên hệ quá dài.' ); }
	if ( in_array( $name, array( 'hotline', 'phone' ), true ) ) {
		$value = preg_replace( '/[\s().-]+/', '', $value );
		return preg_match( '/^\+?[0-9]{9,15}$/', $value ) ? $value : new WP_Error( 'fcc_contact_phone', 'Số điện thoại phải gồm 9–15 chữ số, có thể bắt đầu bằng +.' );
	}
	if ( 'email' === $name ) {
		return is_email( $value ) ? sanitize_email( $value ) : new WP_Error( 'fcc_contact_email', 'Email liên hệ không hợp lệ.' );
	}
	if ( in_array( $name, array( 'zaloLink', 'messengerLink' ), true ) ) {
		if ( 'zaloLink' === $name && preg_match( '/^\+?[0-9]{9,15}$/', $value ) ) {
			$value = 'https://zalo.me/' . ltrim( $value, '+' );
		} elseif ( ! preg_match( '#^https?://#i', $value ) ) {
			$value = 'messengerLink' === $name && preg_match( '/^@?[a-zA-Z0-9._-]+$/', $value )
				? 'https://m.me/' . ltrim( $value, '@' ) : 'https://' . $value;
		}
		$parts = wp_parse_url( $value );
		$hosts = 'zaloLink' === $name ? array( 'zalo.me' ) : array( 'm.me', 'facebook.com', 'www.facebook.com', 'web.facebook.com', 'm.facebook.com', 'messenger.com', 'www.messenger.com' );
		if ( ! $parts || ! in_array( strtolower( $parts['scheme'] ?? '' ), array( 'http', 'https' ), true ) || ! in_array( strtolower( $parts['host'] ?? '' ), $hosts, true ) || empty( $parts['path'] ) || '/' === $parts['path'] || isset( $parts['user'] ) || isset( $parts['pass'] ) || isset( $parts['port'] ) ) {
			return new WP_Error( 'fcc_contact_url', 'Nhập đường dẫn Zalo hoặc Facebook/Messenger hợp lệ.' );
		}
		return esc_url_raw( $value, array( 'http', 'https' ) );
	}
	return sanitize_text_field( $value );
}

/** Read public contacts consistently, including intentionally empty values. */
function project_core_fcc_contact( $name ) {
	$value = project_core_fcc_normalize_contact( $name, project_core_fcc_site( $name ) );
	if ( is_wp_error( $value ) ) { return ''; }
	if ( in_array( $name, array( 'hotlineDisplay', 'phoneDisplay' ), true ) ) {
		$phone = project_core_fcc_contact( 'hotlineDisplay' === $name ? 'hotline' : 'phone' );
		return $phone ? ( $value ?: $phone ) : '';
	}
	return $value;
}

/** Validate the complete form before saving; ACF and the frontend share fcc_* metadata. */
function project_core_fcc_save_contacts( $input ) {
	$id = project_core_fcc_page_id( 'shared' );
	if ( ! $id || ! current_user_can( 'edit_pages' ) || ! current_user_can( 'edit_post', $id ) ) {
		return new WP_Error( 'fcc_contact_permission', 'Bạn không có quyền sửa thông tin liên hệ.' );
	}
	$values = array();
	foreach ( array( 'hotline', 'hotlineDisplay', 'phone', 'phoneDisplay', 'addressHN', 'zaloLink', 'messengerLink', 'email' ) as $name ) {
		$values[ $name ] = project_core_fcc_normalize_contact( $name, $input[ $name ] ?? '' );
		if ( is_wp_error( $values[ $name ] ) ) { return $values[ $name ]; }
	}
	foreach ( array( 'hotline', 'phone' ) as $name ) {
		if ( '' === $values[ $name ] ) { $values[ $name . 'Display' ] = ''; }
		elseif ( '' === $values[ $name . 'Display' ] ) {
			$values[ $name . 'Display' ] = preg_match( '/^0[0-9]{9}$/', $values[ $name ] ) ? preg_replace( '/^(\d{3})(\d{3})(\d{4})$/', '$1 $2 $3', $values[ $name ] ) : $values[ $name ];
		}
	}
	foreach ( $values as $name => $value ) {
		if ( function_exists( 'update_field' ) && acf_get_field( 'field_fcc_' . $name ) ) {
			update_field( 'field_fcc_' . $name, $value, $id );
		} else {
			update_post_meta( $id, 'fcc_' . $name, $value );
		}
	}
	return true;
}

/** Apply the same validation when contacts are edited in the existing ACF screen. */
function project_core_fcc_validate_contact_field( $valid, $value, $field ) {
	if ( true !== $valid || ! in_array( $field['name'] ?? '', array( 'fcc_hotline', 'fcc_phone', 'fcc_hotlineDisplay', 'fcc_phoneDisplay', 'fcc_addressHN', 'fcc_email', 'fcc_zaloLink', 'fcc_messengerLink' ), true ) ) { return $valid; }
	$normalized = project_core_fcc_normalize_contact( substr( $field['name'], 4 ), $value );
	return is_wp_error( $normalized ) ? $normalized->get_error_message() : $valid;
}
add_filter( 'acf/validate_value', 'project_core_fcc_validate_contact_field', 10, 3 );

function project_core_fcc_update_contact_field( $value, $id, $field ) {
	if ( ! in_array( $field['name'] ?? '', array( 'fcc_hotline', 'fcc_phone', 'fcc_hotlineDisplay', 'fcc_phoneDisplay', 'fcc_addressHN', 'fcc_email', 'fcc_zaloLink', 'fcc_messengerLink' ), true ) ) { return $value; }
	$normalized = project_core_fcc_normalize_contact( substr( $field['name'], 4 ), $value );
	return is_wp_error( $normalized ) ? get_post_meta( $id, $field['name'], true ) : $normalized;
}
add_filter( 'acf/update_value', 'project_core_fcc_update_contact_field', 10, 3 );

/**
 * Register contact settings submenu under "Nội dung website".
 */
function project_core_fcc_register_contact_menu() {
	add_submenu_page(
		'project-website-content',
		'Quản lý thông tin liên hệ',
		'Quản lý liên hệ',
		'edit_pages',
		'fcc-contact-settings',
		'project_core_fcc_render_contact_page'
	);
}
add_action( 'admin_menu', 'project_core_fcc_register_contact_menu', 22 );

/**
 * Render the Contact Settings management page.
 */
function project_core_fcc_render_contact_page() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		wp_die( esc_html__( 'Bạn không có quyền truy cập trang này.', 'project-core' ) );
	}

	$notice      = '';
	$notice_type = 'success';

	// Native admin nonce protects the form; the saver also checks the shared post capability.
	if ( isset( $_POST['fcc_save_contact'] ) && check_admin_referer( 'fcc_contact_settings_nonce', 'fcc_contact_nonce' ) ) {
		$input = array();
		foreach ( array( 'hotline' => 'hotline', 'hotlineDisplay' => 'hotline_display', 'phone' => 'phone', 'phoneDisplay' => 'phone_display', 'addressHN' => 'address', 'zaloLink' => 'zalo_link', 'messengerLink' => 'messenger_link', 'email' => 'email' ) as $name => $posted ) {
			$input[ $name ] = isset( $_POST[ $posted ] ) ? wp_unslash( $_POST[ $posted ] ) : '';
		}
		$saved = project_core_fcc_save_contacts( $input );
		$notice = is_wp_error( $saved ) ? $saved->get_error_message() : 'Đã lưu thông tin liên hệ. Các nút và thông tin liên hệ trên website sử dụng dữ liệu này; kênh để trống sẽ được ẩn.';
		$notice_type = is_wp_error( $saved ) ? 'error' : 'success';
	}

	$current_hotline         = project_core_fcc_contact( 'hotline' );
	$current_hotline_display = project_core_fcc_contact( 'hotlineDisplay' );
	$current_phone           = project_core_fcc_contact( 'phone' );
	$current_phone_display   = project_core_fcc_contact( 'phoneDisplay' );
	$current_address         = project_core_fcc_contact( 'addressHN' );
	$current_zalo            = project_core_fcc_contact( 'zaloLink' );
	$current_messenger       = project_core_fcc_contact( 'messengerLink' );
	$current_email           = project_core_fcc_contact( 'email' );
	?>
	<div class="wrap" style="max-width: 960px;">
		<h1 class="wp-heading-inline" style="margin-bottom: 8px;">Quản Lý Thông Tin Liên Hệ</h1>
		<p class="description" style="margin-bottom: 20px; font-size: 13.5px; color: #50575e;">
			Cấu hình số điện thoại hotline, kênh chat Zalo, Messenger Facebook và email hỗ trợ. Thông tin được dùng chung trên toàn website. Để trống để ẩn kênh liên hệ. Email nhận đơn hàng và email người gửi được cấu hình riêng tại màn SMTP.
		</p>

		<?php if ( ! empty( $notice ) ) : ?>
			<div class="notice notice-<?php echo esc_attr( $notice_type ); ?> is-dismissible" style="margin-bottom: 20px;">
				<p><strong><?php echo esc_html( $notice ); ?></strong></p>
			</div>
		<?php endif; ?>

		<div style="display: grid; grid-template-columns: 1fr 320px; gap: 24px; align-items: start;">
			<!-- CỘT TRÁI: FORM CẤU HÌNH -->
			<div class="card" style="padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-top: 0; max-width: 100%;">
				<form method="post" action="">
					<?php wp_nonce_field( 'fcc_contact_settings_nonce', 'fcc_contact_nonce' ); ?>

					<h2 style="font-size: 16px; margin-top: 0; padding-bottom: 12px; border-bottom: 1px solid #E2E8F0;">
						📞 Đường dây nóng (Hotline)
					</h2>

					<table class="form-table" role="presentation" style="margin-bottom: 20px;">
						<tr>
							<th scope="row"><label for="fcc_hotline">Hotline gọi (tel:)</label></th>
							<td>
								<input type="text" name="hotline" id="fcc_hotline" value="<?php echo esc_attr( $current_hotline ); ?>" class="regular-text" placeholder="0985561862">
								<p class="description">Dùng cho liên kết bấm gọi trực tiếp hotline. Nhập liền các chữ số, ví dụ: <code>0985561862</code>.</p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="fcc_hotline_display">Hotline hiển thị</label></th>
							<td>
								<input type="text" name="hotline_display" id="fcc_hotline_display" value="<?php echo esc_attr( $current_hotline_display ); ?>" class="regular-text" placeholder="098 556 1862">
								<p class="description">Văn bản hotline hiển thị cho người xem, ví dụ: <code>098 556 1862</code>.</p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="fcc_phone">Điện thoại gọi (tel:)</label></th>
							<td>
								<input type="text" name="phone" id="fcc_phone" value="<?php echo esc_attr( $current_phone ); ?>" class="regular-text" placeholder="0965180859">
								<p class="description">Dùng cho liên kết bấm gọi điện thoại liên hệ. Nhập liền các chữ số, ví dụ: <code>0965180859</code>.</p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="fcc_phone_display">Điện thoại hiển thị</label></th>
							<td>
								<input type="text" name="phone_display" id="fcc_phone_display" value="<?php echo esc_attr( $current_phone_display ); ?>" class="regular-text" placeholder="096 518 0859">
								<p class="description">Văn bản điện thoại hiển thị cho người xem, ví dụ: <code>096 518 0859</code>.</p>
							</td>
						</tr>
					</table>

					<h2 style="font-size: 16px; padding-bottom: 12px; border-bottom: 1px solid #E2E8F0;">
						🏢 Địa Chỉ Văn Phòng / Trụ Sở
					</h2>

					<table class="form-table" role="presentation" style="margin-bottom: 20px;">
						<tr>
							<th scope="row"><label for="fcc_address">Địa chỉ hiển thị</label></th>
							<td>
								<input type="text" name="address" id="fcc_address" value="<?php echo esc_attr( $current_address ); ?>" class="large-text" placeholder="Tầng 1, CT2, Mễ Trì Thượng, Nam Từ Liêm, Hà Nội">
								<p class="description">Địa chỉ văn phòng hiển thị tại chân trang. Để trống để ẩn địa chỉ.</p>
							</td>
						</tr>
					</table>

					<h2 style="font-size: 16px; padding-bottom: 12px; border-bottom: 1px solid #E2E8F0;">
						💬 Kênh Tư Vấn Trực Tuyến (Zalo & Messenger)
					</h2>

					<table class="form-table" role="presentation" style="margin-bottom: 20px;">
						<tr>
							<th scope="row"><label for="fcc_zalo_link">Đường dẫn Chat Zalo</label></th>
							<td>
								<input type="text" name="zalo_link" id="fcc_zalo_link" value="<?php echo esc_attr( $current_zalo ); ?>" class="regular-text" placeholder="https://zalo.me/84985561862">
								<p class="description">Link mở chat Zalo (ví dụ: <code>https://zalo.me/84985561862</code> hoặc nhập trực tiếp số điện thoại <code>0985561862</code>).</p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="fcc_messenger_link">Đường dẫn Messenger / Facebook</label></th>
							<td>
								<input type="text" name="messenger_link" id="fcc_messenger_link" value="<?php echo esc_attr( $current_messenger ); ?>" class="regular-text" placeholder="https://web.facebook.com/hangnhatchomoinha.vn">
								<p class="description">Link mở chat Facebook Messenger hoặc trang Fanpage (ví dụ: <code>https://web.facebook.com/hangnhatchomoinha.vn</code> hoặc <code>https://www.facebook.com/buidangjp</code>).</p>
							</td>
						</tr>
					</table>

					<h2 style="font-size: 16px; padding-bottom: 12px; border-bottom: 1px solid #E2E8F0;">
						✉️ Hộp Thư Hỗ Trợ (Email)
					</h2>

					<table class="form-table" role="presentation" style="margin-bottom: 10px;">
						<tr>
							<th scope="row"><label for="fcc_email">Email liên hệ</label></th>
							<td>
								<input type="email" name="email" id="fcc_email" value="<?php echo esc_attr( $current_email ); ?>" class="regular-text" placeholder="jpbuidangco.ltd@gmail.com">
								<p class="description">Email giải đáp khiếu nại, dịch vụ hiển thị ở mục hỗ trợ và chân trang website.</p>
							</td>
						</tr>
					</table>

					<div style="padding-top: 16px; border-top: 1px solid #E2E8F0;">
						<button type="submit" name="fcc_save_contact" class="button button-primary button-hero" style="font-weight: 700;">
							Lưu Thay Đổi Thông Tin Liên Hệ
						</button>
					</div>
				</form>
			</div>

			<!-- CỘT PHẢI: XEM TRƯỚC (PREVIEW) -->
			<div>
				<div class="card" style="padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-top: 0; background: #FAF8F2; border: 1px solid #E2D7BE;">
					<h3 style="margin-top: 0; font-size: 14px; text-transform: uppercase; color: #1E293B; letter-spacing: 0.05em; text-align: center;">
						Xem Trước Nút Liên Hệ
					</h3>
					<p style="font-size: 12px; color: #64748B; text-align: center; margin-bottom: 16px;">
						3 nút liên hệ hàng dọc tại Mục Lục Chính Sách:
					</p>

					<div style="display: flex; flex-direction: column; gap: 10px;">
						<!-- Zalo -->
						<?php if ( $current_zalo ) : ?><a href="<?php echo esc_url( $current_zalo ); ?>" target="_blank" rel="noopener noreferrer" style="display: flex; align-items: center; justify-content: center; gap: 8px; background: #0068FF; color: #fff; text-decoration: none; padding: 10px 16px; border-radius: 9999px; font-size: 13.5px; font-weight: 700; box-shadow: 0 2px 6px rgba(0,104,255,0.25);">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2C6.477 2 2 6.03 2 11C2 13.565 3.195 15.864 5.12 17.472L4.2 21.2C4.085 21.664 4.545 22.052 4.965 21.848L9.29 19.742C10.158 19.911 11.063 20 12 20C17.523 20 22 15.97 22 11C22 6.03 17.523 2 12 2Z" fill="#FFFFFF"></path><text x="12" y="14" font-family="'Plus Jakarta Sans', Arial, sans-serif" font-size="7.2" font-weight="800" fill="#0068FF" text-anchor="middle" letter-spacing="-0.3">Zalo</text></svg>
							<span>Chat Zalo</span>
						</a><?php endif; ?>

						<!-- Messenger -->
						<?php if ( $current_messenger ) : ?><a href="<?php echo esc_url( $current_messenger ); ?>" target="_blank" rel="noopener noreferrer" style="display: flex; align-items: center; justify-content: center; gap: 8px; background: linear-gradient(135deg, #00B2FF 0%, #006AFF 100%); color: #fff; text-decoration: none; padding: 10px 16px; border-radius: 9999px; font-size: 13.5px; font-weight: 700; box-shadow: 0 2px 6px rgba(0,106,255,0.25);">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.36 2 2 6.13 2 11.7c0 2.91 1.19 5.44 3.12 7.15.16.14.26.35.26.57l-.37 2.21c-.08.47.38.86.82.7l2.58-1.02c.18-.07.38-.07.56-.01.95.26 1.97.4 3.03.4 5.64 0 10-4.13 10-9.7S17.64 2 12 2zm1 10.5-2.5-2.5-3.5 3.5 3-4.5 2.5 2.5 3.5-3.5-3 4.5z"/></svg>
							<span>Facebook / Messenger</span>
						</a><?php endif; ?>

						<!-- Hotline -->
						<?php if ( $current_hotline ) : ?><a href="tel:<?php echo esc_attr( $current_hotline ); ?>" style="display: flex; align-items: center; justify-content: center; gap: 8px; background: #2E7D32; color: #fff; text-decoration: none; padding: 10px 16px; border-radius: 9999px; font-size: 13.5px; font-weight: 700; box-shadow: 0 2px 6px rgba(46,125,50,0.25);">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
							<span>Hotline: <?php echo esc_html( $current_hotline_display ); ?></span>
						</a><?php endif; ?>
					</div>

					<div style="font-size: 11.5px; color: #64748B; margin-top: 12px; text-align: center;">
						Email: <strong><?php echo esc_html( $current_email ); ?></strong>
					</div>
				</div>

				<div class="card" style="padding: 16px 20px; border-radius: 8px; margin-top: 16px;">
					<h4 style="margin-top: 0; font-size: 13px; color: #1E293B;">Liên kết nhanh</h4>
					<ul style="margin: 0; padding-left: 18px; font-size: 13px; line-height: 1.8;">
						<li><a href="<?php echo esc_url( project_core_fcc_url( 'policies' ) ); ?>" target="_blank">Xem trang Chính Sách</a></li>
						<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank">Xem Trang Chủ</a></li>
						<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=project-website-content' ) ); ?>">Quay lại Hướng dẫn nội dung</a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<?php
}
