# Sản phẩm gợi ý theo tình trạng da – chờ duyệt

> Ch?t 14/10/2026: to?n b? n?i dung/s?n ph?m/media/ph?c ?? hi?n t?i ?? ???c duy?t l?m [defaults 1.0.0](APPROVED-DEFAULTS.md). C?c release c? ?? x?a; [release ??u ti?n](../release/v1.0.0/RELEASE.md) l? h? s? hi?n h?nh. C?c m? t? ch? duy?t/local_sample c?a b?n tr??c d??i ??y l? l?ch s?.

Ngày 13/10/2026, bản 0.3.27. Đã lưu đề xuất trên site XAMPP thực tế tại `D:\xampp\htdocs\fractionalcc-jp.vn`. **Chưa đưa các lựa chọn vào dữ liệu mặc định hoặc initializer.** Nguồn đối chiếu là công dụng (`fcc_tagline`) và đối tượng phù hợp (`fcc_targetUsers`) đã nhập từ [Excel được duyệt](PRODUCT-DATA-EXCEL.md), không dùng ID cũ trong template.

| Tình trạng | Danh sách theo thứ tự hiển thị | Căn cứ lựa chọn trong hồ sơ |
| --- | --- | --- |
| Đốm nâu & không đều màu | Needle Serum C; Needle Serum CX4; Needle Serum C Face Mask; PDRN Shot 1000+ Toning Essence | C/CX4 có công dụng dưỡng sáng và hỗ trợ làm mờ các vết sắc tố theo từng sản phẩm. Mask C dành cho da không đều màu, xỉn màu và thiếu ẩm. PDRN có đối tượng da không đều màu, thâm mụn và cần phục hồi. |
| Lỗ chân lông to & da sần | Mandelic Shot Peel Essence; Needle Serum A; Needle Serum C Face Mask; PDRN Shot 1000+ Toning Essence | Mandelic dành cho da sần, dầu/bít tắc và lỗ chân lông to. Serum A và Mask C có công dụng/đối tượng liên quan lỗ chân lông. PDRN là lựa chọn bổ sung cho nhu cầu độ ẩm/phục hồi, không tự gán thêm công dụng thu nhỏ lỗ chân lông. |
| Lão hóa & nếp nhăn | Needle Serum A; Needle Serum AX4; Needle Serum A Face Mask; PDRN Shot 1000+ Toning Essence | A/AX4 có công dụng hỗ trợ giảm sự xuất hiện nếp nhăn theo hồ sơ. Mask A có đối tượng nếp nhăn li ti, thiếu đàn hồi và thiếu ẩm. PDRN có đối tượng da bắt đầu có dấu hiệu lão hóa. |
| Thâm mụn sau viêm | Combo C–CX4; Needle Serum C; Needle Serum CX4; PDRN Shot 1000+ Toning Essence | CX4 và PDRN ghi đối tượng có thâm sau mụn/không đều màu. C là lựa chọn dưỡng sáng, chăm sóc da không đều màu; combo là cách trình bày bộ sản phẩm C + CX4 đã có trong catalog. |

Các mục trong cùng danh sách là lựa chọn theo nhu cầu, không phải yêu cầu dùng tất cả, dùng cùng buổi hoặc lịch tăng nồng độ. CX4/AX4 có đối tượng đã quen serum vi kim trong hồ sơ; cần đối chiếu hướng dẫn riêng trước khi tư vấn sử dụng. Không tạo lịch dùng, nồng độ hoặc cam kết kết quả mới. Những đoạn giải thích khoa học/nguyên tắc chăm sóc có sẵn ngoài phần danh sách sản phẩm không được thẩm định lại trong lần cập nhật này.

## Cách quản lý

Mở **Nội dung website → Phác đồ theo tình trạng da → Chỉnh sửa**, hoặc [editor trang hiện tại](http://localhost/fractionalcc-jp.vn/wp-admin/post.php?post=30&action=edit). Trong **Gợi ý phác đồ – sản phẩm theo tình trạng da**, chọn tab tương ứng:

1. Tìm và chọn sản phẩm/combo đã xuất bản ở bên trái.
2. Kéo các mục đã chọn ở bên phải để đổi thứ tự; bấm dấu trừ để bỏ chọn.
3. Nhấn **Cập nhật**, mở trang ngoài website để xem. Tối đa 6 mục mỗi tình trạng, có thể để trống.

Ảnh, tên, giá, công dụng và nút mua dùng catalog hiện tại. Sản phẩm/combo chuyển về nháp sẽ không xuất hiện ngoài website. Lựa chọn độc lập với bộ lọc nhu cầu, danh mục và sản phẩm chủ lực trên trang chủ. Quyền chỉnh sửa và nonce dùng luồng WordPress/ACF có sẵn; không thêm endpoint ghi công khai.

## Duyệt và mặc định

Các danh sách chỉ nằm trong metadata của trang local. `defaults/fractional.json`, `defaults/catalog-excel.json` và initializer không nhận các đề xuất này; hash defaults được đối chiếu trước/sau và giữ nguyên. Cài mới từ ZIP có phần quản lý nhưng danh sách còn trống. Khởi tạo lại giữ lựa chọn đã sửa, kể cả bỏ chọn toàn bộ.

Sau khi người dùng duyệt danh sách cụ thể, mới đưa vào defaults bằng khóa nguồn ổn định như `product_6`, `combo_C-CX4`, cùng migration có phiên bản, bảo toàn lựa chọn site đã chỉnh. Không dùng attachment/post ID của site local làm dữ liệu mặc định.

Backup trang trước khi lưu: `C:/Users/anhvt/.codex/backups/fractionalcc-jp.vn/regimens-before-20261013-122808.json`, ngoài repo và ZIP. Xem kiểm tra/package tại [release 0.3.27](../release/v1.0.0/RELEASE.md).
