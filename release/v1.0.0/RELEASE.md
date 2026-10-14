# Fractional CC 1.0.0

Ngày 14/10/2026. Bản đầu tiên được build lại theo yêu cầu người dùng, bao gồm các sửa bảo mật và tối ưu ACF đã duyệt; giữ nội dung, giao diện, liên hệ và 30 Media. Các bản trước đã được dọn khỏi release/, lưu riêng ngoài dự án để phục hồi. Không deploy production, cập nhật dependency hoặc gửi email thật.

## Bộ cài

| Package | Số file | SHA-256 |
| --- | --- | --- |
| [Project Core 1.0.0](project-core-v1.0.0.zip) | 21 | `7b09271186dc73e72a2fb58ccdae529ff6fe6c8ada55f0ac06784245ba8a3303` |
| [Project Child 1.0.0](project-child-v1.0.0.zip) | 129 | `58489796de3d164d4610a015bc33bf2497d2d7e87bd9b12b22cd8307c5cdf5d8` |

Đúng hai ZIP, root project-core/project-child, byte/CRC khớp source. Không có config, secrets, database, uploads, orders, logs hoặc profiles. [Báo cáo package](qa/package-report.json).

Dependencies đã thử: WordPress 7.1.3, Kadence 1.5.2, ACF 6.8.10, PHP XAMPP 8.0.30; cài riêng. Header WordPress 6.3+, PHP 7.4+ không chứng minh PHP cũ phù hợp production.

## Thay đổi

- REST users dùng route đã phân giải, kể cả rest_route/batch; chặn XML-RPC/pingback trong plugin; author redirect trước canonical; thêm Permissions-Policy.
- Form yêu cầu POST/nonce/đồng ý điều khoản, kiểm tra kiểu/độ dài/phone/catalog; giới hạn 10 yêu cầu hợp lệ mỗi IP/10 phút, khóa nguyên tử cho concurrency.
- Validate SMTP trước lưu, giữ settings/password khi sai; không bật SMTP tài khoản thiếu TLS/SSL. Nonce/status order từ chối array.
- Schema trình bày ACF chỉ đăng ký tại admin/CLI/Customizer; public giảm 1.160 xuống 94 field, giữ editor/preview/UI. Thời gian local dao động, chưa kết luận tăng tốc đồng đều.
- .env/wp-config.php ngừng được Git theo dõi, local giữ nguyên. Không xóa credentials trong commit cũ.

[Báo cáo bảo mật/hiệu năng](../../docs/SECURITY-AUDIT-2026-10-14.md) ghi phạm vi và giới hạn.

Defaults approved 1.0.0 gồm 6 bản ghi/1.085 trường, 10 sản phẩm, 1 combo, 35 hoạt chất, 30 Media. Zalo `https://zalo.me/84985561862`, Facebook `https://web.facebook.com/hangnhatchomoinha.vn`; không phục hồi ảnh Excel/uploads dư. Seed active 1.0.0 giữ nguyên 54 bản ghi và 30 Media.

## Kiểm tra và cài đặt

PHP lint/build và hai ZIP đạt. Cài/kích hoạt từ ZIP trên database riêng; thiếu dependency không ghi nội dung. Đạt 2.644 defaults, 291 contacts, 82 security, 56 HTTP security, 44 AJAX order và 44 native POST checks; mail được capture/chặn. Editor/Customizer/preview hoạt động. [HTTP](qa/security-http-report.json), [admin](qa/admin-report.json).

Đạt 1.033 browser checks, 10 sản phẩm/13 widths 320–1.920px, không lỗi console; 15 trang và byte ảnh khớp active. [Responsive](qa/report.json), [public](qa/public-comparison.json), [hiệu năng](qa/performance-report.json). Database QA đã gỡ, fixture/sessions/profiles lưu riêng ngoài htdocs/release.

Cài Kadence/ACF, upload/kích hoạt đúng hai ZIP 1.0.0, mở admin bằng administrator có upload_files hoặc dùng nút khởi tạo trong Nội dung website. Không cần ACF Sync. Initializer giữ edits/giá trị xóa/Reading/settings, không nhân bản Media. Local/staging noindex, Cảm ơn luôn noindex.

Trước production sao lưu môi trường đích, cấu hình domain/secrets/HTTPS/server và chọn PHP còn hỗ trợ. .htaccess local không thuộc ZIP, cần luật chặn file nhạy cảm tương đương. Chưa kiểm tra hosting/Safari/email delivery/load lớn/pentest độc lập. Rollback dùng backup môi trường đích và code đã kiểm chứng, giữ dữ liệu phát sinh. Các bộ cài trước reset được lưu riêng ngoài dự án, không nằm trong release phân phối.

Báo cáo hiệu năng trước/sau được kế thừa từ lần rà soát code vừa duyệt; lần reset này đổi version và đóng gói lại, không đo lại hiệu năng trước/sau.
