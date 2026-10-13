# Fractional CC 0.3.21 – Kiểm tra và sửa luồng form đặt hàng

Ngày 13/10/2026. Áp dụng trên XAMPP `C:\xampp\htdocs\fractionalcc-jp.vn`; chưa deploy production, không tạo commit/tag. Source có các cập nhật tiếp theo ở phiên bản 0.3.22; ZIP này là snapshot 0.3.21 đã kiểm tra.

## Phạm vi và kết quả khảo sát

Người dùng báo popup gửi được mail qua SMTP, còn form trực tiếp trang chủ không gửi được, yêu cầu kiểm tra các màn khác. Kiểm tra runtime xác nhận SMTP đang bật và địa chỉ nhận được cấu hình hợp lệ; không in credentials hoặc thay cấu hình SMTP.

Hai kiểu form dùng cùng bộ xử lý server. Khảo sát phát hiện mỗi form có hai trường chống spam cùng tên, form trực tiếp có checkbox bắt buộc nhưng chưa có thông báo rõ tại form khi browser chặn submit, và AJAX lỗi tự gọi `form.submit()` có nguy cơ tạo đơn/mail trùng khi server đã xử lý yêu cầu trước đó. Không có browser kết nối và chưa nhận mô tả lỗi cụ thể từ người dùng, nên chưa xác nhận được thao tác gây lỗi ban đầu bằng browser.

## Thay đổi

- Giữ một trường chống spam dùng chung, action và nonce trên tất cả form. Thêm nguồn `home_inline`, `product_inline`, `modal` để chẩn đoán trong đơn hàng.
- Khi thiếu thông tin hoặc chưa tick đồng ý, hiển thị thông báo ngay trước nút gửi. Lắng nghe native `invalid` nên checkbox bắt buộc chặn submit vẫn có hướng dẫn rõ. Giữ validation/checkbox và các trường hiện có.
- Dùng nonce của chính form trước khi dùng cấu hình chung. Khóa form trong lúc gửi để tránh bấm liên tiếp. Thành công hoặc lỗi server đều được phản hồi tại form.
- Khi kết quả mạng chưa xác định, mở lại nút và báo người dùng kiểm tra kết nối/liên hệ trước khi gửi lại; không tự gửi yêu cầu lần thứ hai. Gửi POST thông thường vẫn được hỗ trợ khi JavaScript không hoạt động.
- Server chỉ nhận ID sản phẩm/combo đang xuất bản và tự xác định tên/giá; không dùng tên từ client làm dữ liệu chính thức. Không gửi mail hoặc báo thành công nếu lưu đơn thất bại.
- Lưu `_fcc_mail_status=sent|failed`. Chi tiết đơn cho biết form nguồn và mail transport đã chấp nhận hay gửi thất bại. `sent` không chứng minh mail đã tới inbox. Khi mail thất bại, đơn đã lưu vẫn được giữ cho quản trị xử lý. Đơn cũ ghi rõ chưa có trạng thái, không suy diễn hoặc tự gửi lại.
- Compiler loại trường chống spam cũ trước khi thêm helper dùng chung và ghi đúng nguồn form. Không chạy compiler tái sinh website trong tác vụ.

JavaScript, markup và helper form thuộc child theme; validation/lưu đơn/mail thuộc core plugin. Không sửa WordPress core, ACF hoặc Kadence, không thêm dependencies. Các điều chỉnh trình bày email/trang vấn đề làn da của tác vụ khác trong workspace được giữ; không ghi đè source mới bằng snapshot package.

## Cài đặt và dữ liệu

- `project-core-v0.3.21.zip`: folder gốc `project-core/`, defaults version 0.3.21 và initializer hiện có.
- `project-child-v0.3.21.zip`: folder gốc `project-child/`, JavaScript và các form đã sửa.
- Dependencies đã kiểm tra: WordPress 7.1.3, PHP 8.0.30, MariaDB 10.4.32, Kadence 1.5.2, ACF Free 6.8.10. Cài dependency riêng từ nguồn chính thức, không thay license.
- Không migration database, ACF Sync, biến môi trường mới hay reset nội dung. Metadata chẩn đoán chỉ được thêm vào đơn gửi mới. Giữ cấu hình SMTP và dữ liệu hiện có. Defaults theo [brief](../../docs/WEBSITE-BRIEF.md)/Excel đã duyệt; không xác nhận các dữ kiện còn thiếu/xung đột.

Backup database/uploads/config riêng trước cập nhật môi trường đích. Cập nhật đúng cặp ZIP với dependencies đang hoạt động, xóa cache/tải lại trang để nhận JavaScript mới. Form trực tiếp cần tick ô đồng ý trước gửi. Xem **Đơn hàng → Chi tiết** để kiểm tra nguồn form và trạng thái email của đơn mới. Nếu source đã lên phiên bản mới hơn, dùng cặp release tương ứng có chứa bản sửa này để giữ các cập nhật tiếp theo.

## Kiểm tra đã thực hiện

- Lint PHP runtime toàn bộ hai ZIP, syntax JavaScript và compiler: đạt.
- **15 trang HTTP, 24 form**: trang chủ, danh mục, vấn đề làn da, 10 trang sản phẩm, chính sách/cảm ơn. Hai trang cuối không có form; nút mua dùng đường dẫn hiện có. Các trang 200, không PHP warning liên quan; từng form đủ và không trùng trường, nonce hợp lệ, action POST đúng, nguồn đúng, options là sản phẩm/combo đã xuất bản.
- Unit test JavaScript thực: native-invalid/checkbox feedback, nonce, submit thành công, ngăn gửi liên tiếp, lỗi server và phản hồi mạng không xác định; không fallback gửi lại.
- Database/content directory riêng từ ZIP: **44 kiểm tra AJAX và 44 kiểm tra POST thường đạt**. Form trang chủ, sản phẩm và popup đều lưu được lựa chọn product/combo, gọi mail một lần, giữ nội dung, xác định tên từ server, ghi nguồn/trạng thái. Nonce/name/phone/item sai bị từ chối, honeypot không tạo đơn/gọi mail, lỗi lưu đơn không báo thành công, lỗi mail giữ đơn và ghi trạng thái failed.
- Cài/kích hoạt final ZIP từ database mới: **53 kiểm tra khởi tạo và 305 kiểm tra catalog đạt**, không PHP debug log. Rerun giữ dữ liệu đã sửa.
- ZIP đúng slug, đủ runtime/defaults/assets, không secrets/config/dump/log/private uploads. Các file runtime khớp snapshot tại build; đối chiếu sau đó ghi nhận metadata version, trình bày email và trang vấn đề làn da đã có cập nhật khác trong source 0.3.22. Không hoàn tác các cập nhật này.

Email trong kiểm tra được chặn bằng `pre_wp_mail` trên database riêng, không gửi thư thật, không tạo đơn thử trên site đang dùng. Chưa xác nhận mail tới inbox hoặc click form/viewport/console bằng browser. Kiểm tra SMTP thật từ popup trước đó là thông tin người dùng cung cấp. Database kiểm thử đã xóa và config tạm được thay bằng nội dung không chứa credentials; bản sao package/junction TEMP nằm ngoài repo/ZIP.

## Rollback

Khôi phục cặp code trước nếu cần; các đơn đã lưu và metadata chẩn đoán được giữ. Không tự gửi lại mail hoặc xóa đơn khi rollback. Nếu phục hồi database riêng của môi trường đích, xét dữ liệu phát sinh sau backup. Chưa thử rollback trên site đang dùng.

## SHA-256

- `project-core-v0.3.21.zip`: `A9BD8BFEF82CC54281127A15CBECA6179ECFDD928AC03529F83645ABCAFFBF32`
- `project-child-v0.3.21.zip`: `0546D6260368A25894437BC0EA0C961020C3A518C740609DC46E97C8A320BA18`
