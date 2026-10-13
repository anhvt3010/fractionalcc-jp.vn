# Fractional CC 0.3.4

Ngày 13/10/2026. Đã áp dụng trên XAMPP tại `C:\xampp\htdocs\fractionalcc-jp.vn`; chưa deploy production.

## Thay đổi

- Lần khởi tạo đầu tiên trước đây chỉ tạo trang “Trang chủ”, còn Reading vẫn là `posts`, `page_on_front=0`. Từ bản này, initializer chọn trang vừa tạo làm homepage khi chưa có phiên bản seed và Reading chưa chọn trang chủ. Chạy lại, update hoặc reactivation giữ lựa chọn Reading và nội dung khách hàng đã sửa.
- Thay ảnh combo nền hồng bằng PNG nền vàng người dùng cung cấp, giữ nguyên byte ảnh. Ảnh được đóng gói trong child theme, khai báo trong defaults cho hero, ảnh chung và combo; template tham chiếu/compiler cũng dùng ảnh mới. Alt và ảnh vẫn chỉnh được qua CMS. Không thay layout hoặc ảnh các sản phẩm đơn lẻ.
- Sửa đường dẫn fallback ảnh hero và dùng ảnh mới cho social metadata. Build ưu tiên PHP của XAMPP tại `C:` phù hợp workspace hiện tại.
- Site đang làm việc được đặt homepage ID `22` theo yêu cầu; ba trường ảnh được chuyển sang attachment mới. Attachment cũ vẫn được giữ. Bản sao các lựa chọn Reading/trường ảnh trước sửa đã lưu và đọc đối chiếu trong thư mục backup riêng, ngoài repo/ZIP.

## Packages và dependencies

- `project-core-v0.3.4.zip`: một folder gốc `project-core/`, initializer/defaults version `0.3.4`.
- `project-child-v0.3.4.zip`: một folder gốc `project-child/`, gồm `assets/fractional/assets/images/banner/combo-c-cx4-gold.png`.
- Runtime kiểm tra: WordPress `7.1.3`, PHP `8.0.30`, MariaDB `10.4.32`, Kadence `1.5.2`, ACF Free `6.8.10`. Kadence và ACF cài riêng. Không có dependency mới.

## Cài đặt và khởi tạo

1. Site mới: cài WordPress, Kadence và ACF; upload/kích hoạt plugin core và child theme. Khi vào quản trị với tài khoản có quyền quản trị/upload, hệ thống tự khởi tạo defaults và chọn trang chủ nếu Reading chưa có trang chủ riêng.
2. Site đã dùng: cập nhật đúng cặp ZIP. Initializer không ghi đè nội dung, ảnh hoặc lựa chọn homepage đã lưu. Để áp dụng thay đổi ảnh trên site khác đã khởi tạo, chọn ảnh PNG mới trong trường ảnh hero/ảnh combo của CMS và chọn Trang chủ trong Cài đặt → Đọc nếu cần. Không có migration tự ghi đè dữ liệu khách hàng.
3. ACF fields đăng ký bằng PHP, không cần Sync. Không chạy `--update-catalog` cho tác vụ này.

## Kiểm tra

- Lint tất cả PHP trong hai package, compiler và `git diff --check`: đạt.
- Giải nén hai ZIP vào content directory riêng, cài/kích hoạt thật cùng Kadence/ACF trên database MariaDB riêng: đạt. 20 kiểm tra khởi tạo/bảo toàn dữ liệu đạt, gồm homepage ở lần đầu, đúng template/version, ảnh vàng đúng SHA-256, 10 sản phẩm, ảnh chung/combo dùng cùng attachment, chạy lại không tạo trùng, giữ title/ảnh bỏ chọn/homepage đã sửa, giữ lựa chọn blog sau khởi tạo, giữ homepage đã chọn trước khởi tạo, reactivation không reset và khởi tạo ẩn danh bị từ chối.
- HTTP site làm việc: URL gốc và danh mục trả `200`, có ảnh vàng, không có PHP warning/fatal/notice trong HTML; `/trang-chu/` chuyển về URL gốc. Canonical homepage là URL gốc. Ảnh tải qua HTTP trùng hash với file người dùng gửi.
- Hai ZIP đúng folder gốc và không chứa config, secrets, uploads riêng, dump hoặc logs. Thư mục release chứa đúng hai ZIP.
- Công cụ đối chiếu Excel/catalog đầy đủ chưa đạt do defaults hiện có tham chiếu ảnh sản phẩm legacy trong khi `catalog-excel.json` yêu cầu đường dẫn `excel-20261013/`. Đây là tình trạng có sẵn, thấy cả trên site đang dùng và cài mới; bản này giữ ảnh/nội dung sản phẩm đơn lẻ theo phạm vi yêu cầu.
- Chưa kiểm tra trực quan desktop/mobile hoặc console: không có trình duyệt kết nối trong phiên này. Không thử gửi đơn/email, không thay SMTP.
- Database kiểm thử đã xóa; cấu hình kết nối tạm đã được thay bằng nội dung không chứa credentials. Bản sao package/dependency junction trong thư mục tạm được giữ, ngoài repo/ZIP.

## Rollback

Quay lại cặp plugin/theme trước khi cần; lựa chọn Reading và ảnh Media không tự hoàn tác khi đổi code. Phục hồi các lựa chọn từ backup riêng hoặc chọn lại trang/attachment cũ trong CMS; giữ chỉnh sửa phát sinh sau cập nhật. Backup database/uploads/config của môi trường đích trước cài. Chưa chạy rollback vào site đang dùng.

## SHA-256

- `project-core-v0.3.4.zip`: `A8ABC54736A0B81121A8BA56A61570C67D72B63B40FC65673E50064452874B31`
- `project-child-v0.3.4.zip`: `AC2D119C424C301829FC515AB41F8E03C3B9F62D90A72451912A1CDD8FC1CCF5`
- PNG nền vàng: `B65161DC5CAFED72FF899677C75CC64EF11251BA5811546F2AE499630F1F9042`
