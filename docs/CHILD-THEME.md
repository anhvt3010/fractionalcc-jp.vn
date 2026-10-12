# Kadence child theme

> Bản chuyển Fractional CC hiện là source version `0.3.0`, giữ slug `project-child` và parent `kadence`. Xem [FRACTIONAL-WORDPRESS.md](FRACTIONAL-WORDPRESS.md) cho template/CMS mới và trạng thái chưa áp dụng vào database chính. Phần bên dưới ghi lại starter/trang mẫu `0.1.0` và không xác nhận runtime hiện tại.

## Đã tạo

Child theme: **Project Child**, slug `project-child`, version `0.1.0`, parent `kadence`. Đây là tên tạm theo cấu trúc dự án; có thể đổi tên hiển thị trong `style.css` khi chốt tên thương hiệu.

```text
wp-content/themes/project-child/
├── style.css                 # metadata WordPress
├── functions.php             # enqueue CSS
├── inc/sample-page.php       # schema ACF và dữ liệu trình bày
├── templates/sample-page.php
├── template-parts/sample/    # Hero, Giới thiệu, Liên hệ
└── assets/css/
    ├── main.css              # CSS dùng chung
    └── sample-page.css       # CSS chỉ cho trang mẫu
```

Kadence tự nạp assets của parent. Child nạp `main.css` với dependency `kadence-global`, thêm `sample-page.css` khi dùng template mẫu và version theo thời gian sửa file để trình duyệt lấy CSS mới. Không nạp lại parent `style.css`, không thêm JavaScript chưa cần thiết.

## Kích hoạt

1. Mở `http://localhost/wordpress/wp-admin/` và đăng nhập.
2. Vào **Appearance → Themes** (Giao diện → Giao diện).
3. Chọn **Project Child**, dùng Live Preview nếu muốn xem trước rồi nhấn **Activate**.
4. Kiểm tra homepage, một trang nội dung, header/footer, menu và mobile; cấu hình Kadence tại **Appearance → Customize**.

**Trạng thái hiện tại:** Project Child đã kích hoạt để dùng trang mẫu. Thiết lập Kadence parent được sao chép sang child khi child chưa có cấu hình riêng. Nội dung bài viết/trang vẫn nằm trong database; kiểm tra menu và Customizer khi chuyển theme. Xem [FIRST-PAGE.md](FIRST-PAGE.md) để chỉnh sửa trang mẫu bằng ACF.

Kadence parent phải tiếp tục được cài đặt. Không xóa hoặc sửa parent. Nếu cần quay lại giao diện trước, activate Twenty Twenty-Five trong admin.

## Phát triển tiếp

- CSS: sửa `assets/css/main.css`; không đặt CSS nghiệp vụ hoặc override hàng loạt parent khi có thiết lập Customizer phù hợp.
- PHP trình bày: thêm template/template part khi có yêu cầu cụ thể; giữ `functions.php` nhỏ.
- Nội dung có cấu trúc: cấu hình ACF khi cần, sau đó version field definitions.
- CPT, API, pricing và nghiệp vụ: custom plugin dự án, không đặt trong child theme.
- JS: chỉ tạo và enqueue khi có tương tác cần thiết.

Môi trường XAMPP hiện chạy PHP **8.0.30**. Code child theme hiện tại tương thích với môi trường này; header `Requires PHP: 7.4` phản ánh mức tối thiểu của bootstrap đơn giản, không thay đổi mục tiêu PHP **8.2+** trong kiến trúc dự án. Lên kế hoạch nâng PHP và kiểm tra tương thích dependencies trước khi phát triển hoặc deploy theo stack mục tiêu.

Chưa thêm screenshot hoặc thư mục rỗng. Child theme được WordPress nhận diện qua `style.css` và kế thừa template của Kadence.
