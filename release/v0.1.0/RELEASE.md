# Local setup v0.1.0 — Trang giới thiệu mẫu

Hồ sơ triển khai local, chưa phải release production. Git chưa khởi tạo, chưa có commit/tag hoặc build artifact.

## Thay đổi

- Activate Project Child, sao chép thiết lập Kadence parent khi chưa có thiết lập child riêng.
- Cài ACF miễn phí 6.8.10 từ WordPress.org và Project Core 0.1.0 với menu Nội dung website.
- Thêm template trang giới thiệu, ba reusable parts và CSS chỉ nạp cho template này.
- ACF fields khai báo PHP trong child theme, không cần sync JSON.
- Tạo Trang giới thiệu mẫu (ID 6 trên local), slug `gioi-thieu-mau`; không sửa trang cũ/không đổi homepage.
- Script CLI tạo một lần, không ghi đè trang đã có khi chạy lại.

## Runtime và kiểm tra

WordPress 7.1.3, PHP 8.0.30, Kadence 1.5.2, ACF 6.8.10, custom theme/plugin 0.1.0. PHP 8.2+ vẫn là mục tiêu tiếp theo.

PHP lint và HTTP checks đạt; kiểm tra ACF scoping, email validation, quyền admin guide, escaping, URL nguy hiểm và fallback khi ACF tắt. Chưa có browser kết nối để kiểm tra trực quan desktop/mobile, console và save/upload thực tế trong editor.

## Khôi phục và triển khai khác

Khôi phục dependencies phiên bản tương thích; activate ACF, Project Core và child theme; chạy script tạo trang trên database mới hoặc migrate nội dung có kiểm soát. Script không tự activate dependencies, không đổi homepage và không xóa/ghi đè trang.

Để bỏ demo, đưa trang vào Thùng rác trong admin. Để trở lại giao diện trước, activate Kadence parent; giữ post meta để không mất nội dung. Không xóa ACF nếu chưa đánh giá tác động tới các trang dùng fields. Production cần backup/restore, staging và nghiệm thu giao diện trước deploy.
