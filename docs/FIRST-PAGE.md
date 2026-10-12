# Hướng dẫn trang mẫu cho người mới

## Đã thiết lập

Các URL bên dưới dành cho XAMPP. Nếu đang dùng Docker, mở `http://localhost:8088/wp-admin/`, đăng nhập tài khoản Docker rồi dùng **Nội dung website** để sửa trang; nội dung và ID trang của hai database độc lập. Xem [DOCKER.md](DOCKER.md).

- Theme **Project Child 0.1.0** đã active, kế thừa Kadence 1.5.2.
- Plugin **Project Core 0.1.0** đã active, bổ sung menu **Nội dung website**.
- **Advanced Custom Fields (ACF) miễn phí 6.8.10** đã cài từ WordPress.org và active. Trang này không cần ACF PRO.
- Trang **Trang giới thiệu mẫu** đã xuất bản: [Xem trang](http://localhost/wordpress/gioi-thieu-mau/).
- Trang mẫu là trang riêng; nội dung cũ và cấu hình trang chủ không bị thay thế.

Hiểu đơn giản: **theme quyết định cách hiển thị; ACF cung cấp ô nhập nội dung; plugin bổ sung chức năng và hướng dẫn quản trị**.

## 1. Mở trang để sửa

1. Vào [WordPress Admin](http://localhost/wordpress/wp-admin/) và đăng nhập.
2. Nhấn **Nội dung website** trong menu bên trái.
3. Nhấn **Sửa trang mẫu**.

Hoặc mở [màn hình sửa trang](http://localhost/wordpress/wp-admin/post.php?post=6&action=edit). ID 6 chỉ áp dụng database hiện tại; trên máy khác dùng menu Nội dung website để lấy link đúng.

## 2. Sửa phần Mở đầu

Cuộn xuống nhóm **Nội dung trang giới thiệu**, chọn tab **Mở đầu**:

| Ô nhập | Bạn cần làm gì? |
| --- | --- |
| Dòng chữ nhỏ phía trên | Điền tên thương hiệu hoặc lời chào |
| Tiêu đề chính | Câu quan trọng nhất của trang; không được bỏ trống |
| Mô tả ngắn | Vài câu giới thiệu |
| Ảnh mở đầu | Chọn/tải ảnh từ Thư viện; có thể bỏ trống |
| Chữ trên nút | Ví dụ: Tìm hiểu thêm |
| Đường dẫn nút | URL đầy đủ; bỏ trống sẽ ẩn nút |

Demo chưa có ảnh. Khi chọn ảnh, điền **Văn bản thay thế/Alt text** nếu ảnh mang ý nghĩa. Tối ưu dung lượng trước khi tải lên; WordPress hiển thị kích thước ảnh phù hợp và `srcset`.

Nút demo dẫn tới phần Giới thiệu trên cùng trang. Có thể thay bằng URL của một trang khác đã tạo.

## 3. Sửa Giới thiệu và Liên hệ

Tab **Giới thiệu**: sửa tiêu đề, nội dung và công tắc **Hiện phần giới thiệu**.

Tab **Liên hệ**: sửa tiêu đề, lời mời liên hệ, chữ trên nút email, email nhận liên hệ và công tắc **Hiện phần liên hệ**.

**Thay `hello@example.com` bằng email thật của bạn.** Đây là dữ liệu demo. Nút Gửi email mở ứng dụng email trên máy khách bằng `mailto:`; chưa có form gửi email trên website và chưa cần SMTP cho nút này.

## 4. Nội dung bổ sung bằng Gutenberg

Phần soạn thảo chính phía trên ACF dành cho nội dung bổ sung. Demo có sẵn heading và đoạn văn. Nhấn vào chữ để sửa hoặc nhấn **+** để thêm Paragraph, List, Image…

Nội dung Gutenberg xuất hiện **sau Giới thiệu, trước Liên hệ**. Dùng heading H2/H3; H1 đã được tạo từ field Tiêu đề chính, không cần thêm H1 khác.

## 5. Lưu và kiểm tra

1. Nhấn **Lưu/Cập nhật** ở góc trên bên phải.
2. Nhấn **Xem trang** hoặc tải lại [trang mẫu](http://localhost/wordpress/gioi-thieu-mau/).
3. Kiểm tra tiêu đề, ảnh, các nút và nội dung mới.
4. Mở trên điện thoại để kiểm tra xuống dòng, ảnh và menu.

Bài tập đầu tiên: đổi tiêu đề chính, sửa giới thiệu, thay email, lưu rồi xem kết quả. Không cần sửa PHP/CSS.

Nếu không thấy nhóm ACF: kiểm tra ACF active, trang dùng template **Trang giới thiệu mẫu**, và nhóm không bị ẩn trong tùy chọn màn hình soạn thảo. Tải lại editor sau khi chọn template nếu cần.

## Khi muốn làm trang chủ

Vào **Cài đặt → Đọc → Trang chủ hiển thị → Một trang tĩnh**, chọn **Trang giới thiệu mẫu**, nhấn **Lưu thay đổi**. Bước này chưa được thực hiện tự động. Nếu có blog, tạo/chọn trang Bài viết riêng khi cần.

## Tạo trang khác cùng bố cục

Vào **Trang → Thêm trang mới**, đặt tiêu đề và chọn template **Trang giới thiệu mẫu** trong cài đặt trang. Lưu nháp/tải lại nếu editor chưa hiện field, nhập các field rồi xuất bản. Mỗi trang có nội dung riêng, dùng chung cấu trúc.

## Dành cho developer

- Plugin bootstrap: `wp-content/plugins/project-core/project-core.php`; hướng dẫn admin: `src/Admin/website-guide.php`. Menu yêu cầu `edit_pages`; link sửa kiểm tra quyền từng trang. Không thêm endpoint ghi dữ liệu riêng, save/nonce do WordPress và ACF xử lý.
- Schema ACF thuộc giao diện: `project-child/inc/sample-page.php`, đăng ký tại `acf/init` bằng PHP; không cần import/sync JSON.
- Template: `project-child/templates/sample-page.php`; reusable parts: `template-parts/sample/`.
- CSS: `project-child/assets/css/sample-page.css`, chỉ nạp trên template mẫu.
- Giá trị nằm trong post meta từng trang. Native meta đã cache được dùng khi render, nên nội dung đã lưu vẫn hiển thị khi ACF tắt; muốn sửa field cần bật lại ACF.
- Field groups PHP không hiển thị để sửa schema trong danh sách ACF Field Groups, theo [tài liệu ACF](https://www.advancedcustomfields.com/resources/register-fields-via-php/). Khách sửa **giá trị trong trang**, developer sửa schema trong code.
- `scripts/create-sample-page.php` chỉ chạy CLI; tạo một lần, không ghi đè trang đã có. Activate theme/ACF trước khi dùng trên database mới.
- Restore ACF từ [WordPress.org](https://wordpress.org/plugins/advanced-custom-fields/) phiên bản tương thích; không cần commit plugin bên thứ ba. Runtime setup: WordPress 7.1.3, PHP 8.0.30, ACF 6.8.10. Mục tiêu PHP vẫn là 8.2+.

## Kiểm tra và giới hạn

Đã kiểm tra PHP syntax, trang/CSS HTTP 200, trang WordPress cũ vẫn phản hồi, CSS mẫu không nạp toàn site, field location rules, email validation, quyền admin guide, HTML escaping, URL nguy hiểm bị bỏ, fallback khi ACF không hoạt động và script không ghi đè khi chạy lại.

Chưa nghiệm thu trực quan desktop/mobile, console và save/upload thực tế trong editor vì công cụ trình duyệt chưa có kết nối. CSS dùng mobile-first media query; cần kiểm tra trên thiết bị trước production. Chưa triển khai form gửi email, commerce hoặc nghiệp vụ riêng.
