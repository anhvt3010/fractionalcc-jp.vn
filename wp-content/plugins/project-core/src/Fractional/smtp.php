<?php
/**
 * SMTP configuration and mail transport for Fractional CC.
 *
 * @package Project_Core
 */

defined( 'ABSPATH' ) || exit;

/** Return default SMTP configuration. */
function project_core_fcc_default_smtp_settings() {
	return array(
		'enabled'        => 0,
		'host'           => 'smtp.gmail.com',
		'port'           => 587,
		'encryption'     => 'tls', // 'tls', 'ssl', or 'none'
		'auth'           => 1,
		'username'       => '',
		'password'       => '',
		'from_email'     => get_option( 'admin_email' ),
		'from_name'      => 'Fractional CC Việt Nam – JP- BÙI ĐẶNG',
		'notify_email'   => get_option( 'admin_email' ),
		'notify_subject' => '[Đơn hàng mới] Yêu cầu từ {customer_name} – Fractional CC',
	);
}

/** Retrieve current SMTP settings with defaults. */
function project_core_fcc_get_smtp_settings() {
	$saved = get_option( 'project_core_fcc_smtp', array() );
	return wp_parse_args( is_array( $saved ) ? $saved : array(), project_core_fcc_default_smtp_settings() );
}

/** Hook into PHPMailer to route wp_mail() through SMTP when enabled. */
function project_core_fcc_configure_smtp( $phpmailer ) {
	$settings = project_core_fcc_get_smtp_settings();
	if ( empty( $settings['enabled'] ) || empty( $settings['host'] ) ) {
		return;
	}

	$phpmailer->isSMTP();
	$phpmailer->Host     = sanitize_text_field( $settings['host'] );
	$phpmailer->Port     = absint( $settings['port'] ) ?: 587;
	$phpmailer->SMTPAuth = ! empty( $settings['auth'] );

	if ( $phpmailer->SMTPAuth ) {
		$phpmailer->Username = sanitize_text_field( $settings['username'] );
		$phpmailer->Password = (string) $settings['password'];
	}

	$encryption = sanitize_text_field( $settings['encryption'] );
	if ( 'ssl' === $encryption || 'tls' === $encryption ) {
		$phpmailer->SMTPSecure = $encryption;
	} else {
		$phpmailer->SMTPSecure = '';
		$phpmailer->SMTPAutoTLS = false;
	}

	if ( ! empty( $settings['from_email'] ) && is_email( $settings['from_email'] ) ) {
		$phpmailer->From = sanitize_email( $settings['from_email'] );
	}
	if ( ! empty( $settings['from_name'] ) ) {
		$phpmailer->FromName = sanitize_text_field( $settings['from_name'] );
	}

	// 15 seconds timeout to avoid hanging requests
	$phpmailer->Timeout = 15;
}
add_action( 'phpmailer_init', 'project_core_fcc_configure_smtp', 10 );

/**
 * Attach brand logos to PHPMailer for embedded CID rendering in HTML emails.
 */
function project_core_fcc_attach_brand_logos( $phpmailer ) {
	$theme_dir = get_stylesheet_directory();
	$fcc_path  = $theme_dir . '/assets/fractional/assets/images/brand/fractional-cc-logo-white.png';
	$bd_path   = $theme_dir . '/assets/fractional/assets/images/brand/bui-dang-jp-logo.png';

	if ( file_exists( $fcc_path ) ) {
		$phpmailer->addEmbeddedImage( $fcc_path, 'fcc_brand_logo', 'fractional-cc-logo.png', 'base64', 'image/png' );
	}
	if ( file_exists( $bd_path ) ) {
		$phpmailer->addEmbeddedImage( $bd_path, 'buidang_brand_logo', 'bui-dang-jp-logo.png', 'base64', 'image/png' );
	}
}

/** Ensure From Email matches SMTP setting when active. */
function project_core_fcc_filter_mail_from( $from ) {
	$settings = project_core_fcc_get_smtp_settings();
	if ( ! empty( $settings['enabled'] ) && ! empty( $settings['from_email'] ) && is_email( $settings['from_email'] ) ) {
		return sanitize_email( $settings['from_email'] );
	}
	return $from;
}
add_filter( 'wp_mail_from', 'project_core_fcc_filter_mail_from', 20 );

/** Ensure From Name matches SMTP setting when active. */
function project_core_fcc_filter_mail_from_name( $name ) {
	$settings = project_core_fcc_get_smtp_settings();
	if ( ! empty( $settings['enabled'] ) && ! empty( $settings['from_name'] ) ) {
		return sanitize_text_field( $settings['from_name'] );
	}
	return $name;
}
add_filter( 'wp_mail_from_name', 'project_core_fcc_filter_mail_from_name', 20 );

/** Register SMTP settings submenu. */
function project_core_fcc_register_smtp_menu() {
	add_submenu_page(
		'project-website-content',
		'Cài đặt SMTP & Email thông báo',
		'Cài đặt SMTP',
		'manage_options',
		'fcc-smtp-settings',
		'project_core_fcc_render_smtp_page'
	);
}
add_action( 'admin_menu', 'project_core_fcc_register_smtp_menu', 25 );

/** Validate the whole admin form before changing settings or the saved password. */
function project_core_fcc_validate_smtp_settings( $input, $current ) {
	$invalid = new WP_Error( 'fcc_smtp_invalid', 'Cấu hình SMTP không hợp lệ. Kiểm tra host, cổng, email và kiểu dữ liệu.' );
	$limits  = array( 'smtp_host' => 253, 'smtp_port' => 5, 'smtp_encryption' => 4, 'smtp_username' => 320, 'smtp_password' => 1024, 'smtp_from_email' => 254, 'smtp_from_name' => 160, 'notify_email' => 254, 'notify_subject' => 300, 'smtp_enabled' => 2, 'smtp_auth' => 2 );
	foreach ( $limits as $field => $limit ) {
		if ( isset( $input[ $field ] ) && ( ! is_string( $input[ $field ] ) || mb_strlen( wp_unslash( $input[ $field ] ) ) > $limit ) ) {
			return $invalid;
		}
	}
	$host       = trim( wp_unslash( $input['smtp_host'] ?? '' ) );
	$port       = $input['smtp_port'] ?? '587';
	$encryption = $input['smtp_encryption'] ?? 'tls';
	if ( ( '' !== $host && ! filter_var( $host, FILTER_VALIDATE_IP ) && ! filter_var( $host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME ) ) || ! ctype_digit( $port ) || (int) $port < 1 || (int) $port > 65535 || ! in_array( $encryption, array( 'tls', 'ssl', 'none' ), true ) ) {
		return $invalid;
	}
	foreach ( array( 'smtp_from_email', 'notify_email' ) as $field ) {
		if ( ! empty( $input[ $field ] ) && ! is_email( wp_unslash( $input[ $field ] ) ) ) { return $invalid; }
	}
	$enabled = ! empty( $input['smtp_enabled'] );
	$auth    = ! empty( $input['smtp_auth'] );
	if ( $enabled && ( '' === $host || ( $auth && 'none' === $encryption ) ) ) {
		return new WP_Error( 'fcc_smtp_transport', 'SMTP cần host hợp lệ và mã hóa TLS/SSL khi dùng tài khoản, mật khẩu.' );
	}
	$password = trim( wp_unslash( $input['smtp_password'] ?? '' ) );
	return array(
		'enabled'        => $enabled ? 1 : 0,
		'host'           => $host,
		'port'           => (int) $port,
		'encryption'     => $encryption,
		'auth'           => $auth ? 1 : 0,
		'username'       => sanitize_text_field( wp_unslash( $input['smtp_username'] ?? '' ) ),
		'password'       => '' !== $password ? $password : $current['password'],
		'from_email'     => sanitize_email( wp_unslash( $input['smtp_from_email'] ?? '' ) ),
		'from_name'      => sanitize_text_field( wp_unslash( $input['smtp_from_name'] ?? '' ) ),
		'notify_email'   => sanitize_email( wp_unslash( $input['notify_email'] ?? '' ) ),
		'notify_subject' => sanitize_text_field( wp_unslash( $input['notify_subject'] ?? '' ) ),
	);
}

/** Render the SMTP settings and test console. */
function project_core_fcc_render_smtp_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Bạn không có quyền truy cập trang này.', 'project-core' ) );
	}

	$settings = project_core_fcc_get_smtp_settings();
	$notice   = '';
	$notice_type = 'info';

	// Handle saving settings
	if ( isset( $_POST['fcc_save_smtp'] ) && check_admin_referer( 'fcc_smtp_settings_nonce', 'fcc_smtp_nonce' ) ) {
		$validated = project_core_fcc_validate_smtp_settings( $_POST, $settings );
		if ( is_wp_error( $validated ) ) {
			$notice = $validated->get_error_message();
			$notice_type = 'error';
		} else {
			$settings = $validated;
			update_option( 'project_core_fcc_smtp', $settings, false );
			$notice = 'Đã lưu cấu hình SMTP thành công!';
			$notice_type = 'success';
		}
	}

	// Handle sending test email
	if ( isset( $_POST['fcc_test_smtp'] ) && check_admin_referer( 'fcc_smtp_settings_nonce', 'fcc_smtp_nonce' ) ) {
		$test_recipient = isset( $_POST['test_email'] ) && is_string( $_POST['test_email'] ) ? sanitize_email( wp_unslash( $_POST['test_email'] ) ) : '';
		if ( ! is_email( $test_recipient ) ) {
			$test_recipient = $settings['notify_email'] ?: get_option( 'admin_email' );
		}

		$mail_error = '';
		$error_catcher = static function( $wp_error ) use ( &$mail_error ) {
			if ( is_wp_error( $wp_error ) ) {
				$mail_error = $wp_error->get_error_message();
			}
		};

		add_action( 'wp_mail_failed', $error_catcher );
		add_action( 'phpmailer_init', 'project_core_fcc_attach_brand_logos' );

		$subject = '[Fractional CC] Thử nghiệm gửi email qua SMTP thành công';
		$current_time_str = current_time( 'd/m/Y • H:i:s' );
		$body = '<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>' . esc_html( $subject ) . '</title>
<style>
  body, table, td, th, p, a, li, blockquote, span, div, h1, h2, h3 { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif !important; }
  table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif; }
  img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
  body { margin: 0; padding: 0; width: 100% !important; background-color: #F4EFE6; font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif; color: #2B1E16; -webkit-font-smoothing: antialiased; }
</style>
</head>
<body style="margin:0;padding:0;background-color:#F4EFE6;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;-webkit-font-smoothing:antialiased;">
  <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#F4EFE6" style="background-color:#F4EFE6;padding:32px 12px;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">
    <tr>
      <td align="center" style="font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:680px;width:100%;background-color:#FFFFFF;border-radius:14px;border:1px solid #DFCEB2;box-shadow:0 8px 30px rgba(43,30,22,0.08);overflow:hidden;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">
          <tr>
            <td style="background-color:#18110D;background:linear-gradient(145deg, #251912 0%, #140E0A 100%);padding:36px 32px 30px;text-align:center;border-bottom:3px solid #C5A059;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">
              <table align="center" cellpadding="0" cellspacing="0" border="0" style="margin:0 auto 20px auto;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">
                <tr>
                  <td align="center" valign="middle" style="padding-right:18px;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">
                    <img src="cid:fcc_brand_logo" alt="Fractional CC Nhật Bản" width="165" style="display:block;width:165px;height:auto;max-width:165px;border:0;">
                  </td>
                  <td align="center" valign="middle" style="padding:0 4px;">
                    <div style="width:1px;height:38px;background-color:rgba(197,160,89,0.4);line-height:38px;">&nbsp;</div>
                  </td>
                  <td align="left" valign="middle" style="padding-left:18px;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">
                    <table cellpadding="0" cellspacing="0" border="0" style="font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">
                      <tr>
                        <td align="center" valign="middle" style="background-color:#FFFFFF;border-radius:50%;width:38px;height:38px;text-align:center;box-shadow:0 2px 6px rgba(0,0,0,0.3);padding:0;">
                          <img src="cid:buidang_brand_logo" alt="JP- BÙI ĐẶNG" width="30" height="30" style="display:block;margin:auto;width:30px;height:30px;border-radius:50%;border:0;">
                        </td>
                        <td valign="middle" style="padding-left:10px;text-align:left;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">
                          <span style="display:block;font-size:9px;color:#C5A059;letter-spacing:1.5px;font-weight:700;text-transform:uppercase;line-height:1.2;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">PHÂN PHỐI ĐỘC QUYỀN</span>
                          <span style="display:block;font-size:13px;color:#FFFFFF;font-weight:700;letter-spacing:0.8px;line-height:1.2;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">JP- BÙI ĐẶNG</span>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
              <table align="center" cellpadding="0" cellspacing="0" border="0" style="margin:0 auto 12px auto;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">
                <tr>
                  <td style="background-color:rgba(197,160,89,0.15);border:1px solid rgba(197,160,89,0.35);border-radius:20px;padding:5px 18px;text-align:center;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">
                    <span style="color:#E5C178;font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">KIỂM TRA HỆ THỐNG SMTP</span>
                  </td>
                </tr>
              </table>
              <h1 style="margin:0;font-size:22px;font-weight:800;color:#FFFFFF;letter-spacing:0.5px;line-height:1.3;text-transform:uppercase;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">
                KẾT NỐI SMTP THÀNH CÔNG
              </h1>
            </td>
          </tr>
          <tr>
            <td style="padding:32px 36px 36px;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">
              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#FAF5EC;border:1px solid #ECDDC5;border-radius:8px;margin-bottom:28px;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">
                <tr>
                  <td align="left" valign="middle" style="padding:16px 20px;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">
                    <span style="display:block;font-size:10px;color:#8A7561;text-transform:uppercase;letter-spacing:1px;font-weight:700;margin-bottom:3px;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">TRẠNG THÁI KẾT NỐI</span>
                    <span style="font-size:18px;font-weight:800;color:#1B5E20;letter-spacing:0.5px;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">✓ ĐÃ XÁC THỰC THÀNH CÔNG</span>
                  </td>
                  <td align="right" valign="middle" style="padding:16px 20px;text-align:right;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">
                    <span style="display:block;font-size:10px;color:#8A7561;text-transform:uppercase;letter-spacing:1px;font-weight:700;margin-bottom:3px;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">THỜI GIAN KIỂM TRA</span>
                    <span style="font-size:14px;font-weight:700;color:#2B1E16;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">' . esc_html( $current_time_str ) . '</span>
                  </td>
                </tr>
              </table>

              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:14px;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">
                <tr>
                  <td style="border-left:4px solid #C5A059;padding-left:12px;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">
                    <span style="font-size:14px;font-weight:800;color:#241812;text-transform:uppercase;letter-spacing:0.5px;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">THÔNG SỐ MÁY CHỦ SMTP</span>
                  </td>
                </tr>
              </table>

              <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;margin-bottom:28px;border:1px solid #EFE8DD;border-radius:8px;overflow:hidden;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">
                <tr>
                  <td width="35%" style="padding:12px 18px;background-color:#FBF9F6;border-bottom:1px solid #EFE8DD;color:#6A5C52;font-size:13px;font-weight:700;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">Máy chủ (Host)</td>
                  <td width="65%" style="padding:12px 18px;background-color:#FFFFFF;border-bottom:1px solid #EFE8DD;color:#18110D;font-size:14px;font-weight:700;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">' . esc_html( $settings['host'] ) . '</td>
                </tr>
                <tr>
                  <td style="padding:12px 18px;background-color:#FBF9F6;border-bottom:1px solid #EFE8DD;color:#6A5C52;font-size:13px;font-weight:700;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">Cổng &amp; Mã hóa</td>
                  <td style="padding:12px 18px;background-color:#FFFFFF;border-bottom:1px solid #EFE8DD;color:#18110D;font-size:14px;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">' . esc_html( $settings['port'] ) . ' (' . strtoupper( esc_html( $settings['encryption'] ) ) . ')</td>
                </tr>
                <tr>
                  <td style="padding:12px 18px;background-color:#FBF9F6;border-bottom:1px solid #EFE8DD;color:#6A5C52;font-size:13px;font-weight:700;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">Người gửi thiết lập</td>
                  <td style="padding:12px 18px;background-color:#FFFFFF;border-bottom:1px solid #EFE8DD;color:#18110D;font-size:14px;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">' . esc_html( $settings['from_name'] ) . ' &lt;' . esc_html( $settings['from_email'] ) . '&gt;</td>
                </tr>
                <tr>
                  <td style="padding:12px 18px;background-color:#FBF9F6;color:#6A5C52;font-size:13px;font-weight:700;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">Hộp thư nhận thông báo</td>
                  <td style="padding:12px 18px;background-color:#FFFFFF;color:#0056b3;font-size:14px;font-weight:700;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">' . esc_html( $test_recipient ) . '</td>
                </tr>
              </table>

              <p style="color:#5C5046;font-size:13px;line-height:1.6;margin:0;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">
                Hệ thống website đã sẵn sàng tiếp nhận đơn đặt hàng trực tuyến và gửi email thông báo kèm đầy đủ thông tin khách hàng tới hộp thư này.
              </p>
            </td>
          </tr>
          <tr>
            <td style="background-color:#FAF8F5;border-top:1px solid #EFE8DD;padding:22px 32px;text-align:center;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">
              <p style="margin:0;font-size:11px;color:#A39990;line-height:1.5;font-family: \'Segoe UI\', -apple-system, BlinkMacSystemFont, Roboto, \'Helvetica Neue\', Arial, sans-serif;">
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
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );

		$sent = wp_mail( $test_recipient, $subject, $body, $headers );
		remove_action( 'phpmailer_init', 'project_core_fcc_attach_brand_logos' );
		remove_action( 'wp_mail_failed', $error_catcher );

		if ( $sent ) {
			$notice = 'Gửi email thử nghiệm thành công tới <strong>' . esc_html( $test_recipient ) . '</strong>! Vui lòng kiểm tra Hộp thư đến (hoặc Hòm thư Spam).';
			$notice_type = 'success';
		} else {
			$notice = 'Không thể gửi email thử nghiệm. ' . ( $mail_error ? 'Lỗi PHPMailer: <code>' . esc_html( $mail_error ) . '</code>' : 'Vui lòng kiểm tra lại Host, Cổng, Username và Mật khẩu ứng dụng (App Password).' );
			$notice_type = 'error';
		}
	}
	?>
	<div class="wrap" style="max-width: 960px;">
		<h1>Cài đặt SMTP & Email Thông Báo Đơn Hàng</h1>
		<p>Cấu hình máy chủ SMTP (Gmail, Brevo, Outlook, Mailgun, v.v.) để hệ thống gửi thông báo ngay khi có khách đặt hàng hoặc điền form liên hệ.</p>

		<?php if ( $notice ) : ?>
			<div class="notice notice-<?php echo esc_attr( $notice_type ); ?> is-dismissible">
				<p><?php echo wp_kses_post( $notice ); ?></p>
			</div>
		<?php endif; ?>

		<div style="background:#FFF;padding:24px 28px;border-radius:8px;border:1px solid #CCD0D4;margin-top:16px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
			<form method="post" action="">
				<?php wp_nonce_field( 'fcc_smtp_settings_nonce', 'fcc_smtp_nonce' ); ?>

				<h2 style="margin-top:0;border-bottom:1px solid #EEE;padding-bottom:10px;">1. Trạng thái gửi thư</h2>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row">Kích hoạt SMTP</th>
						<td>
							<label>
								<input type="checkbox" name="smtp_enabled" value="1" <?php checked( $settings['enabled'], 1 ); ?>>
								<strong>Bật gửi email qua máy chủ SMTP</strong> (Khuyến nghị bật để tránh email bị đưa vào spam)
							</label>
						</td>
					</tr>
				</table>

				<h2 style="border-bottom:1px solid #EEE;padding-bottom:10px;margin-top:24px;">2. Cấu hình máy chủ SMTP</h2>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="smtp_host">Máy chủ SMTP (Host)</label></th>
						<td>
							<input name="smtp_host" type="text" id="smtp_host" value="<?php echo esc_attr( $settings['host'] ); ?>" class="regular-text" placeholder="smtp.gmail.com" required>
							<p class="description">Ví dụ: <code>smtp.gmail.com</code> (Gmail), <code>smtp-relay.brevo.com</code> (Brevo), <code>smtp.office365.com</code> (Outlook).</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="smtp_port">Cổng SMTP (Port)</label></th>
						<td>
							<input name="smtp_port" type="number" id="smtp_port" value="<?php echo esc_attr( $settings['port'] ); ?>" class="small-text" placeholder="587" required>
							<p class="description">Cổng thông dụng: <code>587</code> (cho TLS), <code>465</code> (cho SSL).</p>
						</td>
					</tr>
					<tr>
						<th scope="row">Phương thức mã hóa</th>
						<td>
							<fieldset>
								<label><input type="radio" name="smtp_encryption" value="tls" <?php checked( $settings['encryption'], 'tls' ); ?>> TLS (Khuyến nghị, cổng 587)</label><br>
								<label><input type="radio" name="smtp_encryption" value="ssl" <?php checked( $settings['encryption'], 'ssl' ); ?>> SSL (Cổng 465)</label><br>
								<label><input type="radio" name="smtp_encryption" value="none" <?php checked( $settings['encryption'], 'none' ); ?>> Không mã hóa</label>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">Xác thực SMTP</th>
						<td>
							<label>
								<input type="checkbox" name="smtp_auth" value="1" <?php checked( $settings['auth'], 1 ); ?>>
								Yêu cầu xác thực tài khoản (Bật cho hầu hết các dịch vụ SMTP)
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="smtp_username">Tài khoản / Email đăng nhập</label></th>
						<td>
							<input name="smtp_username" type="text" id="smtp_username" value="<?php echo esc_attr( $settings['username'] ); ?>" class="regular-text" placeholder="your-email@gmail.com">
							<p class="description">Địa chỉ email hoặc username tài khoản SMTP của bạn.</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="smtp_password">Mật khẩu ứng dụng (App Password)</label></th>
						<td>
							<input name="smtp_password" type="password" id="smtp_password" value="" class="regular-text" placeholder="<?php echo $settings['password'] ? '•••••••••••••••• (Đã lưu mật khẩu)' : 'Nhập mật khẩu ứng dụng'; ?>">
							<p class="description">
								<?php if ( $settings['password'] ) : ?>
									<span style="color:#2E7D32;">✓ Đã lưu mật khẩu.</span> Để nguyên ô này nếu không muốn đổi.
								<?php else : ?>
									Với Gmail: Tạo tại <a href="https://myaccount.google.com/apppasswords" target="_blank" rel="noopener noreferrer">Mật khẩu ứng dụng Google (App Password)</a>.
								<?php endif; ?>
							</p>
						</td>
					</tr>
				</table>

				<h2 style="border-bottom:1px solid #EEE;padding-bottom:10px;margin-top:24px;">3. Thông tin người gửi & Nhận thông báo</h2>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="smtp_from_email">Email người gửi (From Email)</label></th>
						<td>
							<input name="smtp_from_email" type="email" id="smtp_from_email" value="<?php echo esc_attr( $settings['from_email'] ); ?>" class="regular-text" placeholder="jpbuidangco.ltd@gmail.com" required>
							<p class="description">Nên trùng với tài khoản email SMTP để không bị từ chối gửi thư.</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="smtp_from_name">Tên người gửi (From Name)</label></th>
						<td>
							<input name="smtp_from_name" type="text" id="smtp_from_name" value="<?php echo esc_attr( $settings['from_name'] ); ?>" class="regular-text" placeholder="Fractional CC Việt Nam – JP- BÙI ĐẶNG">
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="notify_email">Email nhận thông báo đơn hàng</label></th>
						<td>
							<input name="notify_email" type="email" id="notify_email" value="<?php echo esc_attr( $settings['notify_email'] ); ?>" class="regular-text" placeholder="jpbuidangco.ltd@gmail.com" required>
							<p class="description"><strong>Hệ thống sẽ gửi thông báo đơn hàng mới tới email này ngay khi khách bấm đặt hàng.</strong></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="notify_subject">Tiêu đề email đơn hàng</label></th>
						<td>
							<input name="notify_subject" type="text" id="notify_subject" value="<?php echo esc_attr( $settings['notify_subject'] ); ?>" class="large-text">
							<p class="description">Có thể dùng <code>{customer_name}</code>, <code>{order_code}</code>, <code>{product_name}</code> để hiển thị linh hoạt.</p>
						</td>
					</tr>
				</table>

				<p class="submit">
					<button type="submit" name="fcc_save_smtp" class="button button-primary button-large">Lưu Cấu Hình SMTP</button>
				</p>
			</form>
		</div>

		<!-- Test Email Console -->
		<div style="background:#FFF;padding:24px 28px;border-radius:8px;border:1px solid #CCD0D4;margin-top:24px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
			<h2 style="margin-top:0;border-bottom:1px solid #EEE;padding-bottom:10px;">4. Thử nghiệm gửi Email</h2>
			<p>Gửi một email kiểm tra tới hòm thư của bạn để xác nhận kết nối SMTP hoạt động tốt trước khi nhận đơn hàng từ khách.</p>
			<form method="post" action="">
				<?php wp_nonce_field( 'fcc_smtp_settings_nonce', 'fcc_smtp_nonce' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="test_email">Gửi thử tới email</label></th>
						<td>
							<input name="test_email" type="email" id="test_email" value="<?php echo esc_attr( $settings['notify_email'] ?: get_option( 'admin_email' ) ); ?>" class="regular-text" required>
							<button type="submit" name="fcc_test_smtp" class="button button-secondary" style="margin-left:8px;">Gửi Email Thử Nghiệm</button>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
	<?php
}
