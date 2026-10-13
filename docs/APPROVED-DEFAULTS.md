# Dữ liệu đã chốt – release đầu tiên 1.0.0

Ngày 14/10/2026, người dùng duyệt toàn bộ nội dung đang hiển thị, sản phẩm và dữ liệu liên quan làm dữ liệu khởi tạo. `1.0.0` mở đầu lịch sử release mới; các release cũ đã xóa theo yêu cầu.

Đóng gói 6 bản ghi nội dung website với **1.083 giá trị trường**, **10 sản phẩm, 1 combo, 35 hoạt chất và 30 ảnh**. Giữ chữ, ảnh, SEO, liên hệ, chính sách, giá, đánh giá, hồ sơ, nội dung Gutenberg/gallery, alt/caption Media và các giá trị để trống. Bao gồm thứ tự **2 mục chủ lực**, **6 mục danh mục trang chủ** và **4 danh sách phác đồ đã duyệt**.

Defaults tại `wp-content/plugins/project-core/defaults/fractional.json`; schema, giao diện và ảnh nguồn thuộc child theme. Media được đối chiếu theo SHA-256 để dùng lại asset giống byte; ảnh khác được đóng gói bằng tên theo hash. Không khôi phục 81 ảnh uploads dư đã xóa.

Tham chiếu sản phẩm/combo/hoạt chất dùng source key; gallery và URL nội bộ được ánh xạ sang ID/domain site đích. Khởi tạo giữ tiêu đề/slug/thứ tự, tên website, mô tả, múi giờ/offset, định dạng ngày/giờ, đầu tuần và cấu trúc permalink. Homepage được chọn khi cài mới theo initializer; chạy lại bảo toàn nội dung, Media metadata, gallery, ảnh đã xóa, phác đồ đã bỏ chọn, settings và lựa chọn Reading đã chỉnh.

Không export tài khoản, mật khẩu, SMTP secrets, đơn hàng, khách hàng, transient, URL môi trường, revision hoặc demo WordPress không thuộc website Fractional CC. Trạng thái nội dung mặc định là **approved**; local/development/staging vẫn noindex độc lập với trạng thái này, trang Cảm ơn luôn noindex. Không tự thay domain hoặc settings indexing production.

Site đang dùng đã ghi seed version **1.0.0**, trạng thái **approved**, bảo toàn nội dung cũ và vẫn có 30 ảnh uploads. [Hồ sơ release và kiểm tra](../release/v1.0.0/RELEASE.md) là tài liệu hiện hành.

Khi người dùng duyệt một lần chốt tiếp theo, dùng XAMPP PHP chạy `scripts/export-fractional-defaults.php --user=<administrator-id> --version=<version> --approve`, tăng version hai package, build và kiểm tra cài mới. Exporter yêu cầu quyền quản trị/upload; không lấy dữ liệu riêng. Compiler HTML cũ bị chặn khi defaults đã approved để tránh ghi đè snapshot, CSS và ảnh đã chốt.
