# Bộ ảnh Thumbnail – 1.0.5

> Bản chốt **[2.0.0](../release/v2.0.0/RELEASE.md)** xuất lại dữ liệu hiện tại: 71 Media; Mandelic có 5 ảnh gồm ảnh hover mới, các sản phẩm còn lại có 4 ảnh. Khung quản lý ảnh cho phép chọn ảnh hover độc lập với ảnh đại diện/thứ tự. Ảnh hover lưu ở `fcc_hoverImage`, được exporter đóng gói và initializer ánh xạ sang ID trên site mới; giữ lựa chọn đã sửa/xóa khi chạy lại. Nhãn và ảnh hover combo chỉnh trong ACF của combo. Các số liệu 1.0.5 bên dưới là lịch sử. Xem hạn chế popup gallery tại 320–375 px trong release notes.

Nguồn do người dùng cung cấp ngày 15/10/2026: `template/Thumbnail/AVT KH` và `template/Thumbnail/ẢNH SẢN PHẨM`. Thay avatar nhỏ của 7 thẻ feedback; dùng 40 ảnh sản phẩm cho đại diện và thư viện thumbnail. Nội dung đánh giá, tên, giá, combo và ảnh lớn bên trong thẻ feedback giữ nguyên.

## Ảnh đại diện mặc định

| Sản phẩm | File nguồn làm đại diện |
| --- | --- |
| Mandelic Shot Peel Essence | `JP (11).png` |
| Needle Serum A Face Mask | `JP (8).png` |
| Needle Serum A | `JP (12).png` |
| Needle Serum AX4 | `JP (14).png` |
| Needle Serum C Face Mask | `JP (18).png` |
| Needle Serum C | `JP (16).png` |
| Needle Serum CX4 | `JP (17).png` |
| PDRN Shot 1000+ Toning Essence | `JP (9).png` |
| PDRN Shot Lip Essence | `JP (10).png` |
| PDRN Shot Lip Plumper | `JP (13).png` |

Mỗi sản phẩm có 4 ảnh, mặc định bắt đầu bằng ảnh chụp sản phẩm/bao bì. Các ảnh còn lại giữ trong đúng nhóm sản phẩm; có thể đổi ảnh đại diện và thứ tự sau khi cài đặt. Ánh xạ từng file và dung lượng trước/sau ở [THUMBNAIL-MEDIA-MAP.json](THUMBNAIL-MEDIA-MAP.json).

## Quản lý trên WordPress

Vào **Nội dung website → Sản phẩm → Sửa sản phẩm**. Trong tab **Thông tin & giá**, liên kết ở dòng **Ảnh sản phẩm** đưa tới khung **Ảnh sản phẩm – đại diện & thứ tự**.

- **Chọn / thêm ảnh sản phẩm** mở thư viện Media WordPress; chọn tab Media Library để dùng ảnh đã có hoặc Upload files để tải ảnh mới.
- **Đặt đại diện** chọn ảnh dùng trên thẻ, trang chi tiết và đơn hàng mới. Ảnh đại diện có thể nằm ở bất kỳ vị trí nào trong dải thumbnail.
- Kéo thả hoặc dùng **↑ / ↓** để đổi thứ tự dải thumbnail.
- **Bỏ ảnh** chỉ loại ảnh khỏi sản phẩm. File Media không bị xóa. Bỏ toàn bộ ảnh sẽ giữ sản phẩm ở trạng thái không có ảnh.
- Nhấn **Cập nhật** để lưu. Hỗ trợ tối đa 50 ảnh JPG/PNG/WebP mỗi sản phẩm.

Ảnh đại diện tiếp tục lưu ở `fcc_image`; thứ tự tiếp tục lưu bằng block `core/gallery` trong `post_content`. Bộ quản lý mới dùng dữ liệu này làm nguồn duy nhất, thay phần quản lý gallery trực tiếp trong editor sản phẩm. Các trường nội dung ACF và các block khác đã có được giữ. Thao tác ảnh được đánh dấu đã tự chỉnh để các lần bổ sung mặc định/cập nhật catalog không đặt lại lựa chọn của người dùng.

Avatar nằm tại **Nội dung website → Thông tin chung → Trang chủ – ảnh đánh giá**, với 7 trường **Avatar nhỏ – thẻ đánh giá 1…7**. Ảnh lớn của thẻ được quản lý riêng.

## Cài mới và cập nhật site đang dùng

Site mới dùng defaults 1.0.5: nhập ảnh WebP, ảnh đại diện và gallery đã sắp xếp, cùng 7 avatar. Trên site đã có dữ liệu, sau khi cập nhật đủ plugin và theme, vào **Nội dung website → Bổ sung dữ liệu còn thiếu**.

Initializer chỉ thay ảnh/gallery cũ khi `_fcc_asset_source` và SHA-256 vẫn khớp bản mặc định trước đó, và không có đánh dấu lựa chọn ảnh do người dùng sửa. Ảnh, thứ tự đã chỉnh hoặc đã xóa được giữ nguyên. Bộ ảnh mới vẫn được nhập vào Media để chọn thủ công. Chạy lại không nhân đôi ảnh hoặc đặt lại thứ tự; không sửa ảnh trong snapshot đơn hàng đã tạo.

## Định dạng và build

`py -3 scripts/import-thumbnail-media.py` tạo ảnh tại `wp-content/themes/project-child/assets/fractional/assets/images/thumbnail/`, cập nhật defaults/schemas và bảng ánh xạ. Chỉ chạy khi chủ động nhập lại bộ ảnh đã được duyệt. Ảnh sản phẩm giữ tỷ lệ, tối đa 1280px; avatar cắt vuông theo vị trí chủ thể, tối đa 512px. Không mang metadata EXIF vào WebP phát hành.

47 file đầu vào (25.249.106 bytes) tạo thành 45 WebP duy nhất (2.696.906 bytes), giảm khoảng 89%. Hai ảnh giống nhau ở nhóm chăm sóc môi dùng chung file. WordPress tạo ảnh nhỏ cho thumbnail khi máy chủ có image editor tương thích; nếu không, ảnh WebP đầy đủ vẫn hiển thị.

Chạy build thông thường theo [release/README.md](../release/README.md). Không cần import lại thư mục Thumbnail trên hosting; ảnh WebP và dữ liệu khởi tạo đã có trong hai ZIP.
