# Dọn ảnh uploads – 14/10/2026

Theo yêu cầu người dùng, đã gỡ **30 file ảnh không còn được dùng** khỏi `wp-content/uploads/2026/10/`: **20 bản trùng byte** với ảnh đang giữ và **10 ảnh sản phẩm cũ** không còn được đăng ký/tham chiếu.

Uploads giảm từ **60 xuống 30 file ảnh**, giảm **14,19 MB** trong thư mục phục vụ website. Thư viện Media vẫn có 30 attachment; ID, đường dẫn, byte ảnh, metadata, alt/caption và gallery được giữ nguyên. Dọn uploads không đổi nội dung defaults. Sau đó dữ liệu được chốt lại trong [release đầu tiên 1.0.0](../release/v1.0.0/RELEASE.md).

## Cách xác định

- Giữ file gốc của mọi attachment, các kích thước sinh ra, `original_image` và ảnh backup của trình sửa ảnh WordPress theo metadata.
- Kiểm tra 3.480 giá trị trong nội dung, metadata, options, user/term/comment metadata; bao gồm các trạng thái bản ghi hiện có.
- Kiểm tra URL uploads trong 118 file source hiện hành và HTML/JSON/style của 15 trang đang dùng.
- Chỉ gỡ file không thuộc Media/metadata và không có tham chiếu. Không dựa riêng vào hậu tố `-1`, `-3`, `-4` hoặc tên giống nhau.

## Sao lưu và kiểm tra

Các file được chuyển ra ngoài dự án vào backup riêng tại:

`C:/Users/anhvt/.codex/backups/fractionalcc-jp.vn/uploads-cleanup-20261014-112154/`

`cleanup-manifest.json` ghi file, SHA-256 và trạng thái trước thao tác. Mọi bản lưu đã được đối chiếu hash; có thể khôi phục đúng đường dẫn tương đối khi cần.

Sau dọn: 30 ảnh đang giữ khớp byte/metadata trước thao tác và trả HTTP 200; 15 trang trả HTTP 200, không có PHP warning/fatal. Rà soát lại còn **0 file ảnh thừa**, **0 file Media bị thiếu**. Không thay đổi source runtime, dữ liệu mặc định hoặc byte ảnh được đóng gói.
