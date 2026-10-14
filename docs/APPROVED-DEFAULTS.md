# Dữ liệu đã chốt – release đầu tiên 1.0.0

> Hiện hành: **[1.0.1](../release/v1.0.1/RELEASE.md)**. Giữ nội dung đã duyệt, liên hệ và 30 Media; cập nhật bảo mật/hiệu năng và version defaults. Chuyển seed active sang 1.0.1 giữ nguyên 54 bản ghi nội dung và 30 Media.

Ngày 14/10/2026, người dùng yêu cầu dọn release cũ và đóng gói site hiện tại thành bản đầu tiên **1.0.0**. Defaults và hai package cùng version, trạng thái `approved`. [Hồ sơ release](../release/v1.0.0/RELEASE.md) ghi kết quả kiểm tra thực tế.

Snapshot chứa **6 bản ghi nội dung với 1.085 giá trị trường**, **10 sản phẩm, 1 combo, 35 hoạt chất và 30 ảnh Media**. Giữ nội dung, giá, SEO, chính sách, gallery, alt/caption, giá trị trống, thứ tự 2 mục chủ lực, 6 mục danh mục trang chủ và 4 danh sách phác đồ đã chọn.

Thông tin liên hệ dùng nguồn chung ở **Nội dung website → Quản lý liên hệ**. Hai URL do người dùng cung cấp đã lưu active/defaults:

- Zalo: `https://zalo.me/84985561862`
- Facebook: `https://web.facebook.com/hangnhatchomoinha.vn`

10 ảnh sản phẩm dùng file có sẵn trực tiếp trong `wp-content/themes/project-child/assets/fractional/assets/images/products/`; thư mục `excel-20261013` đã gỡ. [Dọn uploads](UPLOADS-CLEANUP-2026-10-14.md) giữ 30 ảnh đang dùng và loại 30 file thừa. Bộ cài không chứa các ảnh uploads dư.

Defaults thuộc `wp-content/plugins/project-core/defaults/fractional.json`; schema và ảnh nguồn thuộc child theme. Exporter đối chiếu SHA-256, dùng lại asset khớp byte hoặc đóng gói bản đã duyệt theo hash. Source key ánh xạ quan hệ, gallery và URL sang ID/domain site đích.

Initializer chọn homepage khi cài mới chưa có lựa chọn. Chạy lại giữ nội dung, Media metadata, ảnh đã xóa, phác đồ đã bỏ chọn, settings và Reading đã chỉnh; không tạo bản ghi hoặc ảnh trùng. Trên site đang dùng, chuyển seed version sang 1.0.0 giữ nguyên 54 bản ghi nội dung và 30 Media.

Không export tài khoản, credentials, SMTP secrets, đơn hàng, khách hàng, transient, revision, URL môi trường hoặc demo WordPress không thuộc website. Local/development/staging noindex độc lập với trạng thái approved; trang Cảm ơn luôn noindex. Domain public và indexing production cấu hình riêng.

Lần chốt tiếp theo dùng `scripts/export-fractional-defaults.php --user=<administrator-id> --version=<version> --approve`, tăng version hai package rồi build và thử cài mới. Exporter yêu cầu quyền quản trị/upload. Compiler HTML cũ bị chặn khi defaults đã approved để bảo vệ dữ liệu đã chốt.
