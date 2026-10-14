# Rà soát bảo mật và hiệu năng ngày 14/10/2026

Đã rà soát code custom Project Core/Project Child và runtime XAMPP. Các sửa lỗi nằm trong [release 1.0.0](../release/v1.0.0/RELEASE.md), giữ giao diện, nội dung và ảnh đã duyệt. Đây là kiểm tra ứng dụng local, không phải chứng nhận an toàn tuyệt đối hoặc pentest hạ tầng production.

## Vấn đề đã xử lý

| Vấn đề | Thay đổi và kiểm tra |
| --- | --- |
| REST users kiểm tra REQUEST_URI, không bao quát rest_route/batch | Dùng route đã phân giải của WP_REST_Request; người có list_users vẫn truy cập. Thử anonymous/subscriber/admin, query và batch. Trả WP_REST_Response để tương thích filter core trong batch |
| Chặn author có thể chạy sau canonical | Chạy trước canonical, không chuyển hướng qua URL có tên tác giả |
| xmlrpc_enabled chỉ tắt phương thức cần đăng nhập | Chặn XMLRPC_REQUEST tại init với HTTP 403, kể cả pingback, độc lập với Apache |
| Form thiếu kiểm tra điều khoản ở server và giới hạn gửi | Yêu cầu POST, nonce, đồng ý điều khoản; từ chối array/giá trị dài/số điện thoại sai. Giới hạn 10 yêu cầu hợp lệ mỗi IP/10 phút trước lưu và mail |
| Bộ đếm thông thường có thể bị vượt khi gửi đồng thời | Khóa add_option nguyên tử và transient; phục hồi khóa quá hạn, compare-and-delete không xóa khóa worker mới. Thử 8 tiến trình đồng thời |
| SMTP chưa validate toàn bộ trước lưu | Validate host/cổng/email/type/độ dài, từ chối SMTP bật dùng tài khoản không TLS/SSL. Request sai không đổi settings/password; password trống giữ credential cũ |
| Nonce/status đơn hàng có thể nhận array | Kiểm tra string trước xác minh nonce/lưu trạng thái |
| .env/wp-config.php được Git theo dõi dù có ignore | Bỏ khỏi index, giữ nguyên byte file local và luật ignore; không commit/push/sửa lịch sử |

Liên hệ, sửa nhanh sản phẩm, khởi tạo defaults và Customizer tiếp tục dùng quyền/nonce WordPress. Item/giá lấy từ catalog publish, không tin tên/giá client. Đơn hàng không public và không có REST endpoint công khai. Email sanitize/escape; SMTP chỉ mở cho manage_options, password không xuất ra HTML/defaults/ZIP.

HTTP local đã xác nhận .env, .git/HEAD, backup wp-config và readme không được đọc; security headers hiện diện. Luật Apache thuộc .htaccess local, không cài tự động bằng hai ZIP; môi trường đích cần cấu hình tương đương.

## Tối ưu có đo lường

Public đọc metadata trực tiếp, nên không đăng ký nhóm ACF dành cho editor. Admin/CLI/Customizer vẫn có schema đầy đủ. Kiểm tra HTTP authenticated xác nhận editor, controls và preview hoạt động.

Số field ACF public giảm **1.160 xuống 94**, giảm 1.066 field/request. Memory peak trang chủ giảm khoảng **2 MiB**; queries giữ ở 45/43/35 cho home/catalog/product. Đo 4 request/trang thấy thời gian dao động, chưa chứng minh tăng tốc đồng đều. [Số liệu trước/sau](../release/v1.0.0/qa/performance-report.json).

Tái sử dụng hàm lỗi AJAX/POST, bỏ nhánh lặp của form. Không đổi ảnh, bố cục, dependencies, thêm framework/cache layer hoặc cache nội dung khiến cập nhật của khách hàng bị chậm hiển thị.

## Kiểm tra thực tế

- Cài/kích hoạt từ hai ZIP trên database riêng; PHP lint, file count/root/CRC/SHA-256 khớp source.
- 2.644 defaults, 291 contacts và 82 security checks: quyền, nonce, route/batch, malformed input, email HTML injection, throttle và concurrency.
- 56 HTTP security checks trên endpoint local, fixture có wp-admin riêng.
- 44 order AJAX, 44 native POST và client duplicate/network handling; mail được chặn/capture, không gửi thật.
- HTTP authenticated editor/Customizer/preview; 1.033 responsive checks trên 10 sản phẩm/13 widths. 15 trang và byte ảnh khớp active.
- Seed active 1.0.0 giữ nguyên 54 bản ghi nội dung và 30 Media.

Scripts từ chối root làm việc/fixture thiếu marker. Sau kiểm tra, database QA đã gỡ; fixture/test sessions/profiles lưu riêng ngoài htdocs/release, chỉ giữ báo cáo và ảnh chụp công khai.

## Giới hạn và production

XAMPP chạy **PHP 8.0.30**, thuộc nhánh đã hết hỗ trợ. Chọn PHP còn được hỗ trợ và kiểm tra tương thích trên môi trường đích; tác vụ này không nâng cấp/đổi XAMPP. Nguồn: [PHP unsupported branches](https://www.php.net/eol.php), [supported versions](https://www.php.net/supported-versions.php).

Untrack không xóa credentials trong commit cũ. Nếu repository đã chia sẻ, cần thay credentials liên quan và xử lý lịch sử Git theo kế hoạch riêng; không rewrite history trong tác vụ này.

Chưa audit CVE toàn bộ dependency/vendor, scanner/pentest độc lập, hosting, TLS/HTTPS, quyền filesystem production, email delivery thật, load quy mô lớn hoặc Safari. Không thêm HSTS trên HTTP local hoặc CSP có thể chặn inline script/style đang dùng. Form dùng REMOTE_ADDR; nếu có CDN/reverse proxy, cấu hình IP khách hàng ở server đáng tin cậy, không tin X-Forwarded-For client. Throttle ứng dụng không thay chống DDoS ở hạ tầng.

Nguồn cơ chế: [WordPress xmlrpc_enabled](https://developer.wordpress.org/reference/hooks/xmlrpc_enabled/), [WP_REST_Request](https://developer.wordpress.org/reference/classes/wp_rest_request/) và code core đang chạy cho batch. Không sửa WordPress core, Kadence hoặc ACF.
