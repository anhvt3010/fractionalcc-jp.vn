# Fractional CC 1.0.5 – Avatar và thư viện ảnh sản phẩm

Nhập bộ ảnh người dùng cung cấp trong `template/Thumbnail`: 7 avatar nhỏ của feedback và 40 ảnh cho 10 sản phẩm. Mỗi sản phẩm có ảnh đại diện mặc định và 4 thumbnail được sắp xếp. Thêm khung quản lý ảnh trong editor sản phẩm, có chọn Media, đổi đại diện, kéo thả hoặc nút lên/xuống để sắp xếp.

## Bộ cài

| Package | Số file | SHA-256 |
| --- | ---: | --- |
| [Project Core 1.0.5](project-core-v1.0.5.zip) | 25 | `e0cdf128469d968a0516e1f2b7fbe7e052e2be35240882ccfd117e28e2fd70a9` |
| [Project Child 1.0.5](project-child-v1.0.5.zip) | 291 | `21cfd10b439d8528f1ec6c6e274facb6587db21816ad030ba730e4fd8bbfcb8d` |

Đúng hai ZIP với root `project-core/` và `project-child/`. Cài riêng WordPress, Kadence và ACF Free. Yêu cầu khai báo WordPress 6.3+, PHP 7.4+ và PHP phù hợp phiên bản dependencies đang dùng. Hỗ trợ image editor WebP trên hosting để tạo thêm ảnh nhỏ; các ảnh WebP đầy đủ đã nằm trong ZIP. Không cần ACF Pro hoặc plugin gallery mới.

## Nội dung và quản lý

- 47 ảnh đầu vào khoảng 25,25 MB → 45 file WebP duy nhất khoảng 2,70 MB, giảm khoảng 89%; dùng chung hai file trùng ở nhóm chăm sóc môi.
- Ảnh đại diện được chọn từ ảnh chụp sản phẩm và bao bì; mỗi thư mục đi đúng sản phẩm. [Bảng ánh xạ chi tiết](../../docs/THUMBNAIL-MEDIA-MAP.json).
- Tách avatar nhỏ khỏi ảnh lớn trong thẻ feedback. Giá, tên, nội dung đánh giá, combo và bố cục frontend giữ nguyên.
- Trong **Sản phẩm → Sửa**, mở liên kết **Ảnh sản phẩm** tại tab **Thông tin & giá**, rồi dùng khung **Ảnh sản phẩm – đại diện & thứ tự**. Thứ tự thumbnail và ảnh đại diện độc lập với nhau. Nhấn **Cập nhật** để lưu.
- Bỏ ảnh khỏi sản phẩm không xóa file Media. Hỗ trợ xóa toàn bộ lựa chọn và giữ trạng thái đó khi khởi tạo lại.
- Tiếp tục dùng `fcc_image` và block `core/gallery`; không tạo một kho thứ tự khác song song. Nội dung sản phẩm được chỉnh qua các trường ACF và bộ quản lý ảnh; các block khác có sẵn được giữ.
- Snapshot đơn hàng cũ giữ ảnh đã chốt; đơn mới lấy ảnh đại diện đang chọn. Giữ các sửa email và PageSpeed của 1.0.3/1.0.4.

## Cập nhật hosting

1. Sao lưu riêng database, code và uploads trên hosting.
2. Tải ZIP core qua **Plugin → Cài mới → Tải plugin lên** và chọn **Thay thế bản hiện tại**. Tải ZIP child qua **Giao diện → Thêm mới → Tải theme lên** và thay thế bản hiện tại. Giữ Project Child/Kadence và ACF đang hoạt động.
3. Khi đã cập nhật đủ hai gói, vào **Nội dung website**, bấm **Bổ sung dữ liệu còn thiếu**. Đây là bước nhập bộ ảnh mới và áp dụng cho site đang dùng; không cần ACF Sync.
4. Với site còn ảnh mặc định cũ, initializer kiểm tra source và SHA-256 trước khi đổi ảnh/gallery. Những ảnh hoặc thứ tự đã tự chỉnh/xóa được giữ; toàn bộ bộ ảnh mới vẫn có trong Media để chọn thủ công. Chạy lại không nhân đôi hoặc ghi đè lựa chọn đã sửa.
5. Xóa cache hosting/CDN rồi kiểm tra feedback, thẻ sản phẩm, ảnh lớn/thumbnails, modal đặt hàng và email. Kiểm tra trang cảm ơn bằng một đơn thử mới theo quy trình của bạn.

Avatar chỉnh tại **Thông tin chung → Trang chủ – ảnh đánh giá → Avatar nhỏ – thẻ đánh giá 1…7**. [Hướng dẫn đầy đủ](../../docs/THUMBNAIL-MEDIA.md).

## Kiểm tra

Đã kiểm tra trên WordPress/database XAMPP riêng ở ổ D:, không gửi email ra ngoài. Chưa triển khai lên hosting; workspace không có `wp-config.php` của site làm việc.

- 2.738 kiểm tra defaults: cài mới từ ZIP, nội dung/Media/quan hệ và chạy lại giữ edits.
- 25 kiểm tra nâng cấp ảnh trên fixture 1.0.4: cập nhật defaults cũ, giữ ảnh đã xóa/lựa chọn đã sửa và snapshot đơn cũ, không nhân đôi Media.
- 83 kiểm tra lưu ảnh: đúng 4 ảnh cho mỗi sản phẩm, 7 avatar, ảnh đại diện độc lập thứ tự, nonce/quyền/ID sai bị từ chối, xóa toàn bộ và giữ block khác.
- 149 kiểm tra hiệu năng/HTML/cache/security, 181 kiểm tra đơn hàng/biên lai, 219 kiểm tra MIME email: đạt.
- 1.081 kiểm tra browser responsive cho 10 trang sản phẩm trên 13 chiều rộng, tabs, gallery và modal mua hàng: đạt.
- Kiểm tra quản trị đã xác thực bằng browser: đổi đại diện/thứ tự, lưu thật qua WordPress, hiển thị đúng trên frontend; kiểm tra chọn ảnh từ Media và avatar desktop/mobile. Kết quả cuối tại [qa/verification.txt](qa/verification.txt).
- PHP lint toàn bộ PHP trong ZIP; ZIP CRC, root, inventory và bytes khớp source: [qa/package-report.json](qa/package-report.json).

Rollback dùng bộ 1.0.4 và backup phù hợp. Nếu đã áp dụng bộ ảnh mới, cần khôi phục các lựa chọn ảnh từ backup riêng khi muốn quay lại ảnh cũ; không xóa đơn hàng hoặc ghi đè database phát sinh. Không xóa các file Media đang được snapshot đơn hàng tham chiếu.
