# Release Fractional CC <version>

Đây là mẫu hồ sơ; thay các trường trong ngoặc khi có release thật.

## Thông tin website

- Thương hiệu: Fractional CC; nội dung tiếng Việt, mỹ phẩm chăm sóc da Nhật Bản theo brief.
- Đơn vị phân phối theo brief: Công ty TNHH BUI DANG-JP, phân phối độc quyền tại Việt Nam.
- Hotline: 0985 561 862; email: jpbuidangco.ltd@gmail.com.
- Nguồn nội dung/UI: `template/fractional-cc/`; chi tiết và trạng thái dữ liệu tại [WEBSITE-BRIEF.md](../docs/WEBSITE-BRIEF.md).
- Nội dung ban đầu: 10 sản phẩm trong Excel, combo C–CX4 và Mandelic Shot theo brief; giá thiếu/domain/cam kết bổ sung cần xác nhận theo hồ sơ.
- Môi trường local: XAMPP tại `C:\xampp\htdocs\fractionalcc-jp.vn`; tạm thời không dùng Docker.
- Domain public: chưa chốt; không mặc định lấy canonical `fractional-cc.vn` trong template.

Thông tin trên là hồ sơ khởi đầu, chưa chứng minh dữ liệu đã được nhập, code đã triển khai, ZIP đã build hoặc website đã deploy. Khi copy mẫu sang `release/vX.Y.Z/RELEASE.md`, sửa đường dẫn hồ sơ thành `../../docs/WEBSITE-BRIEF.md`.

## Thông tin

- Ngày: <YYYY-MM-DD>
- Git commit/tag: <commit hoặc tag>
- Môi trường: <staging/production>
- Người thực hiện: <tên>
- Phạm vi và lý do: <mô tả>

## Thay đổi

- Tính năng/sửa lỗi: <chi tiết>
- Custom theme/plugin versions: <phiên bản>
- WordPress/PHP/database/parent theme/ACF/WooCommerce: <phiên bản có sử dụng>
- Nguồn phục hồi dependency/license: <nguồn và quy trình, không có key>
- Biến môi trường mới: <tên biến, không có giá trị secrets>
- Thư mục build: `release/v<version>/`
- ZIP plugin core + SHA-256: <tên file và checksum>
- ZIP child theme + SHA-256: <tên file và checksum>
- Môi trường build/kiểm thử: <XAMPP PHP và các công cụ thực sự dùng; không yêu cầu Docker>

## Database và nội dung

- Migration: <id, lệnh/thao tác, thứ tự, tính idempotent; hoặc không có>
- ACF sync: <field groups và thao tác; hoặc không có>
- Defaults đã duyệt: <nguồn nội dung, seed version, dữ liệu/media có trong ZIP plugin>
- Cách khởi tạo defaults: <thao tác, quyền, dependencies, xử lý lỗi>
- Chạy lại/cập nhật: <cách tránh trùng và bảo toàn nội dung đã sửa>
- Thay đổi content/media: <nếu có>
- Backup/restore đã kiểm tra: <mã backup, ngày và kết quả>
- Khả năng tương thích code cũ với schema mới: <đánh giá>

## Kiểm thử

- [ ] Lint/build và checks liên quan đạt.
- [ ] Frontend/admin không có lỗi PHP/console liên quan.
- [ ] Desktop/mobile, accessibility và customer editing được kiểm tra nếu ảnh hưởng UI.
- [ ] Validation, escaping, authorization, nonce được kiểm tra khi áp dụng.
- [ ] Hai ZIP có đúng folder gốc và đủ file runtime/assets/defaults; không có secrets/dump/private uploads.
- [ ] Site kiểm thử mới cài/activate hai ZIP với dependencies và nhận đủ defaults.
- [ ] Chạy lại initializer/cập nhật không tạo trùng hoặc ghi đè nội dung đã sửa.
- [ ] SEO liên quan được kiểm tra, không trùng metadata/schema; nội dung có nguồn được duyệt.
- [ ] Thiết kế UI được giữ đúng phạm vi đã được cho phép.
- [ ] Cart/checkout/pricing/order/email được kiểm tra khi ảnh hưởng WooCommerce.
- [ ] Staging và quy trình khôi phục được kiểm tra theo phạm vi.

Ghi trường không áp dụng và giới hạn kiểm thử: <chi tiết>.

## Thứ tự deploy

1. <Chuẩn bị môi trường và backup>
2. <Cài dependencies, upload/activate ZIP plugin core và ZIP child theme đúng version>
3. <Cấu hình riêng, migration, field sync, khởi tạo defaults theo hướng dẫn>
4. <Cache và smoke tests cụ thể>

## Rollback

- Trigger rollback: <điều kiện>
- Code/dependency trước đó: <commit và version>
- Database: <cách rollback/forward-fix và giữ dữ liệu phát sinh>
- Người quyết định và thao tác: <tên/quy trình>
- Kiểm tra sau rollback: <các bước>

## Kết quả triển khai

- Thời điểm: <ngày giờ>
- Kết quả smoke-test/log: <kết quả>
- Vấn đề còn lại: <nếu có>
