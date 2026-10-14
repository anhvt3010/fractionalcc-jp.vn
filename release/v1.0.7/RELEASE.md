# Fractional CC 1.0.7 – Bỏ hover thẻ hoạt chất

Theo yêu cầu, bỏ toàn bộ hiệu ứng hover của `.pd-active-card` tại “Bảng Thành Phần & Cơ Chế Hoạt Động”: không dịch chuyển lên, đổi viền, tăng bóng hay hiện vạch vàng. Xóa CSS hover, pseudo-element trang trí và transition không còn dùng; tạo lại CSS minified và bundle. Giữ kiểu hiển thị tĩnh, nội dung và bố cục của 1.0.6.

## Bộ cài và cập nhật

- [Project Core 1.0.7](project-core-v1.0.7.zip), root `project-core/`.
- [Project Child 1.0.7](project-child-v1.0.7.zip), root `project-child/`.

Giữ WordPress, Kadence và ACF hoạt động. Upload hai ZIP qua quản trị và chọn thay thế bản hiện tại, rồi xóa cache hosting/CDN/trình duyệt. Không cần sửa từng sản phẩm hoặc ACF Sync. Core và defaults chỉ tăng version; nội dung mặc định giữ nguyên. Xem quy trình sao lưu/cài đặt tại [1.0.6](../v1.0.6/RELEASE.md).

## Kiểm tra

- Đã cài gói mới trên fixture XAMPP riêng, mail bị chặn. Kiểm tra 10 sản phẩm tại 375 px và 1.440 px: computed style trước/sau ép trạng thái `:hover` giống nhau, không transform/transition/pseudo-element; không có lỗi console/runtime. Xem [browser report](qa/browser-report.json).
- Chạy initializer 1.0.7 giữ nguyên nội dung và metadata của 52 bản ghi. Defaults khớp 1.0.6 ngoài version; không chạy lại toàn bộ bộ kiểm tra cài mới/đơn hàng vì không thay đổi nghiệp vụ hoặc dữ liệu.
- Toàn bộ PHP trong hai ZIP qua lint. ZIP CRC, root, inventory và bytes khớp source; checksum trong [package report](qa/package-report.json).

Đã kiểm tra trên XAMPP/PHP 8.2.12 với WordPress 7.1.3, Kadence 1.5.2 và ACF Free 6.8.10. Chưa triển khai hosting; workspace không có `wp-config.php` của site làm việc.
