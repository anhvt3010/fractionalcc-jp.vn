# Fractional CC 0.3.2

Ngày 12/10/2026. Bản build local XAMPP, chưa triển khai production. Thêm lựa chọn sản phẩm/combo chủ lực trên trang chủ, mặc định Mandelic và combo C–CX4; dùng bố cục duo hiện có và quản lý nội dung thẻ trong hồ sơ tương ứng. Build chứa snapshot source custom hiện tại, bao gồm các chỉnh sửa Hero có sẵn trong workspace.

## Cài đặt và dữ liệu

- Hai package: `project-core-v0.3.2.zip` và `project-child-v0.3.2.zip`, mỗi ZIP có một folder gốc đúng slug. Plugin/theme version `0.3.2`.
- Đã kiểm tra trên WordPress 7.1.3, PHP 8.2.12, MariaDB 10.4.32, Kadence 1.5.2 và ACF Free 6.8.10. Không dùng WooCommerce. Dependency được cài riêng, nguồn phục hồi theo [SETUP.md](../../docs/SETUP.md) và tài liệu dependency của dự án.
- Site mới: cài WordPress, Kadence và ACF, cài/kích hoạt hai package; quản trị viên chạy khởi tạo mặc định trong Nội dung website. Khởi tạo yêu cầu quyền quản trị/upload và nonce. Chọn homepage riêng theo hướng dẫn hiện có.
- Site đã khởi tạo: cập nhật cả plugin và child theme rồi mở quản trị bằng tài khoản quản trị viên. Migration lựa chọn chủ lực version `1` thêm field còn thiếu, chuyển nội dung thẻ từ trang chủ và giữ nội dung đã sửa. Không cần ACF Sync, không đổi theme hoặc homepage tự động. Không có credentials/cấu hình môi trường mới.
- Cách chọn/sửa sản phẩm: [FEATURED-PRODUCTS.md](../../docs/FEATURED-PRODUCTS.md). Defaults có version, nguồn và các dữ kiện cần xác nhận theo [WEBSITE-BRIEF.md](../../docs/WEBSITE-BRIEF.md). Trạng thái nội dung mẫu local không tự chuyển thành nội dung đã xác minh.

## Kiểm tra

- Lint PHP toàn bộ hai package và compiler: đạt.
- Trang chủ HTTP 200, hai thẻ mặc định, mã đặt mua/đường dẫn tương ứng, không có cảnh báo PHP trong HTML: đạt.
- Chọn thay thế, thứ tự, chặn mục thứ ba, loại bản nháp, ẩn khối khi không chọn, escaping nội dung: đạt kiểm tra runtime.
- Initializer chạy lại giữ lựa chọn đã bỏ đánh dấu và nội dung đã chỉnh sửa: đạt.
- HTML biểu mẫu quản trị sản phẩm/combo có các field và nonce; lưu qua HTTP bằng biểu mẫu WordPress thật thay đổi thứ tự trên trang chủ: đạt. Nonce sai và cập nhật ẩn danh bị từ chối. Dữ liệu thử đã khôi phục về bộ đôi mặc định.
- ZIP: đúng hai package, folder gốc, đủ component/defaults/assets; không có config/secrets/dump/private uploads: đạt.
- Chưa kiểm tra trực quan desktop/mobile, thao tác Gutenberg JavaScript hoặc console vì không có trình duyệt kết nối. Bố cục/CSS duo hiện có được tái sử dụng.
- Chưa cài hai ZIP trên một site sạch hoặc kiểm tra backup/restore. Các kiểm tra dữ liệu chạy trên XAMPP hiện tại, không reset database. Chưa kiểm tra luồng gửi đơn/email vì không thay đổi luồng này.

## Rollback

Backup riêng database/uploads/config trước khi triển khai môi trường khác. Quay lại cặp plugin/theme trước đó nếu cần; metadata nội dung trang chủ cũ vẫn còn. Các field mới có thể giữ lại trong database; không cần xóa dữ liệu để rollback giao diện. Kiểm tra trang chủ, danh mục và nút đặt mua sau rollback. Chưa có deploy hoặc rollback production.

## SHA-256

- `project-child-v0.3.2.zip`: `CE96314FF9B5D78B5A38DF987D2B03F172B2A3C40E19E66B070C24547413E6DF`
- `project-core-v0.3.2.zip`: `2309A004106D86DDB5211E625FC81D6ACED7A47165A187EE4123C4E085F177E0`
