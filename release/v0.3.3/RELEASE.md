# Fractional CC 0.3.3

Ngày 13/10/2026. Bản build và cập nhật site local XAMPP `D:\xampp\htdocs\fractionalcc-jp.vn`, chưa deploy production. Cập nhật catalog theo Excel đã được người dùng xác nhận làm dữ liệu mặc định. Bản build chứa snapshot source custom hiện tại, gồm các chỉnh sửa giao diện được làm trước tác vụ này.

## Nội dung và package

- `project-core-v0.3.3.zip`: một folder gốc `project-core/`, gồm model/ACF, initializer version `0.3.3`, dữ liệu runtime `defaults/fractional.json` và nguồn đối chiếu từng trường `defaults/catalog-excel.json`.
- `project-child-v0.3.3.zip`: một folder gốc `project-child/`, gồm giao diện hiện tại, field presentation và 10 ảnh PNG nhúng từ Excel.
- Thêm trường thương hiệu và nội dung thành phần đầy đủ; cập nhật tên, phân loại, xuất xứ, công dụng, thành phần, đối tượng, USP, trạng thái/số CBMP, ghi chú, giá và ảnh từng sản phẩm. Giá combo C–CX4 là 1.237.500đ, giá gốc 1.700.000đ. Chi tiết từng cột, giá và nguồn: [PRODUCT-DATA-EXCEL.md](../../docs/PRODUCT-DATA-EXCEL.md).
- Không thay nội dung không có trong Excel như hướng dẫn dùng, dung tích, đánh giá hoặc hồ sơ/quà tặng combo; giữ lựa chọn sản phẩm chủ lực đang dùng.
- Dependencies đã kiểm tra: WordPress 7.1.3, PHP 8.2.12, MariaDB 10.4.32, Kadence 1.5.2 và ACF Free 6.8.10. Cài riêng Kadence/ACF; không có WooCommerce hoặc dependency mới.

## Khởi tạo và cập nhật

1. Site mới: cài WordPress, Kadence và ACF; upload/kích hoạt hai ZIP. Khi Project Core, Project Child và ACF đã sẵn sàng, lần vào quản trị sẽ tự khởi tạo trang/nội dung, 10 sản phẩm, combo, hoạt chất và media mặc định. Không tự đổi homepage/URL/menu. Chọn homepage riêng theo nhu cầu.
2. Site đã khởi tạo: cài đúng cặp phiên bản. Reactivation và nút khởi tạo trong Nội dung website chỉ bổ sung phần còn thiếu, giữ chỉnh sửa hiện có. ACF fields đăng ký bằng PHP, không cần Sync.
3. Nếu được phép cập nhật hồ sơ hiện có theo bộ Excel, dùng CLI `php scripts/setup-fractional-site.php --update-catalog` sau backup. Cờ này chỉ cập nhật trường có nguồn của sản phẩm/giá combo và gallery do initializer tạo; không thay homepage/permalink/featured selection. Không có endpoint web ghi đè catalog.
4. Compiler giao diện giữ catalog đã duyệt, tránh đưa giá/nội dung cũ trong JS template trở lại defaults. Không có biến môi trường mới cho runtime.

## Kiểm tra đã thực hiện

- Lint PHP custom trong hai package và các script sửa đổi: đạt.
- 305 kiểm tra giữa Excel/defaults/database trên site local: đạt; gồm từng trường, giá bán/giá gốc, giá combo, từng ảnh đúng hàng và đúng hash.
- Hai ZIP đã cài/kích hoạt thật trong một WordPress sạch trên database MariaDB riêng, cùng Kadence/ACF; tự khởi tạo đủ dữ liệu khi kích hoạt theme/plugin: đạt. Chạy lại 305 kiểm tra trên site này: đạt.
- Sửa title, Gutenberg, giá 0, giá gốc trống, ảnh bỏ chọn, ghi chú, thành phần, homepage, combo và featured selection trên site kiểm thử, rồi khởi tạo lại: các chỉnh sửa được giữ; không thêm sản phẩm/combo/hoạt chất/media trùng. Reactivation không reset hoặc đổi homepage. Khởi tạo ẩn danh bị từ chối: đạt.
- HTTP của 10 trang sản phẩm: 200, giá và thành phần mới được render, không có PHP warning/fatal. Trang chủ/danh mục hiển thị giá mới cho các mục thực tế đang chọn; dữ liệu biểu mẫu đặt mua lấy giá mới: đạt.
- Lựa chọn homepage, featured selection/order của site làm việc giữ nguyên sau cập nhật, đối chiếu backup: đạt.
- Cấu trúc/đủ defaults và assets/hai folder gốc đúng slug, không config/secrets/dump/private uploads: đạt.

Không gửi đơn/email thử và không thay luồng SMTP/tiếp nhận đơn. Chưa nghiệm thu trực quan desktop/mobile hoặc console trong tác vụ cập nhật nội dung này. Không reset database site làm việc để kiểm thử. Database kiểm thử, bootstrap chứa thông tin kết nối và hai junction dependency đã được xóa sau nghiệm thu. Thư mục tạm còn bản sao mã/ảnh vì công cụ từ chối xóa đệ quy với thông báo “blocked by policy”; không còn cấu hình kết nối hoặc liên kết đến dependency trong đó.

## Backup và rollback

Catalog trước cập nhật đã lưu riêng tại `C:/Users/anhvt/.codex/backups/fractionalcc-jp.vn/`, đọc lại đủ 50 record và kiểm tra SHA-256. Không nằm trong repo/ZIP. Chưa chạy phục hồi vào site thật.

Backup database/uploads/config của môi trường đích trước cài. Nếu rollback, quay lại cặp plugin/theme trước; dữ liệu mới không tự hoàn tác khi đổi code. Phục hồi catalog từ backup riêng hoặc áp dụng chỉnh sửa có kiểm soát; giữ thay đổi người dùng sau cập nhật. Kiểm tra trang chủ, danh mục, 10 trang chi tiết và lựa chọn giá trong form sau rollback. Chưa có deploy/rollback production.

## SHA-256

- `project-child-v0.3.3.zip`: `F68EA002159711E352FF7F0F4C751202AD155C6D4AF443C7424D6A2D553C6847`
- `project-core-v0.3.3.zip`: `FFDDEFA49F3C72DE7FF9A140D5EFEA4A88C8FA128DF52DB0BFFBC15B1B3D01E8`
