# Fractional CC 1.0.2 – Đồng bộ sản phẩm màn cảm ơn & bảo mật đơn hàng

Bản phát hành 1.0.2 khắc phục lỗi màn cảm ơn hiển thị sai lệch so với sản phẩm thực tế khách hàng đã mua (ảnh sản phẩm, tên, giá, quà tặng), lưu snapshot đơn hàng bất biến tại thời điểm đặt và bổ sung khóa bảo mật URL biên lai (receipt key).

## Bộ cài đặt

| Package | Số file | SHA-256 |
| --- | --- | --- |
| [Project Core 1.0.2](project-core-v1.0.2.zip) | 21 | `203fc7ffd701e80d7179e47c05d10334ab1fa074b211847473f5fc77447ac8c6` |
| [Project Child 1.0.2](project-child-v1.0.2.zip) | 172 | `e7e788d729cb42d5733c487c60b34161ce8f5060371838e0c58933fa31b4e08d` |

Đúng hai file ZIP với thư mục gốc tương ứng là `project-core/` và `project-child/`. Không chứa file tạm, file môi trường `.env`, `wp-config.php`, secrets, cơ sở dữ liệu hay thư mục uploads. Báo cáo xác thực tại [qa/package-report.json](qa/package-report.json).

## Các thay đổi chính

1. **Khắc phục lệch thông tin trên màn cảm ơn (`/cam-on/`):**
   - **Ảnh sản phẩm:** Hiển thị chính xác ảnh của sản phẩm hoặc combo khách hàng đã chọn mua, không còn bị cố định vào ảnh bộ Combo C–CX4 Gold như trước.
   - **Quà tặng:** Thẻ quà tặng được bọc điều kiện kiểm tra; tự động ẩn hoàn toàn khi khách mua sản phẩm lẻ không kèm quà tặng và chỉ hiển thị khi mua combo có quà tặng thực tế.
   - **Tên & giá:** Định dạng đồng bộ, chuẩn xác theo dữ liệu đã chốt khi đặt hàng (`project_core_fcc_format_order_item`).

2. **Snapshot đơn hàng bất biến & Bảo mật biên lai:**
   - Khi đặt hàng thành công, hệ thống lưu snapshot độc lập (`_fcc_order_item_snapshot`) gồm: ID, loại, tên, giá bán, ảnh, mô tả ảnh và quà tặng. Các thay đổi về giá hoặc ảnh trong danh mục sau này không làm thay đổi biên lai đã chốt của khách.
   - Sinh chuỗi ngẫu nhiên 48 ký tự không thể đoán trước (`receipt_key`), lưu mã băm SHA-256 trên server và gắn vào URL chuyển hướng: `/cam-on/?order=FCC-xxxxxx&key=...`.
   - Ngăn chặn triệt để việc khách lạ đoán mã đơn hàng để xem trộm thông tin cá nhân của người khác; truy cập không kèm `key` hợp lệ sẽ ẩn toàn bộ hộp biên lai (chỉ quản trị viên có quyền `edit_post` mới có thể tra cứu đơn cũ).
   - Thêm tiêu đề HTTP `no-cache`, `no-store` và `Referrer-Policy: no-referrer` cho trang cảm ơn để chống lưu cache trang cá nhân và rò rỉ mã khóa.

3. **Tối ưu luồng chuyển hướng Client-side:**
   - Gỡ bỏ việc lưu trữ `sessionStorage` cũ dễ gây lệch bộ nhớ đệm trình duyệt, chuyển hướng mượt mà bằng `redirectUrl` bảo mật từ phản hồi AJAX.

4. **Tối ưu hiệu năng Core Web Vitals & PageSpeed Insights:**
   - **Định dạng ảnh thế hệ mới (Next-Gen WebP):** Tự động sinh và tích hợp thẻ `<picture>` đa định dạng kèm WebP cho toàn bộ ảnh giao diện (ảnh chủ lực Hero Combo C–CX4 Gold giảm từ 2.75 MB xuống chỉ còn ~99 KB — tiết kiệm hơn 96% dung lượng; ảnh đánh giá review/feedback giảm từ 8.6 MB xuống còn ~350 KB).
   - **Chống lỗi NO_LCP & Tối ưu LCP:** Bổ sung `<link rel="preload" as="image" fetchpriority="high">` và `loading="eager"` cho ảnh Hero Banner trong `<head>`, giúp ảnh LCP tải ngay từ giai đoạn parse HTML.
   - **Xóa bỏ Render-Blocking Font & Preconnect:** Gỡ bỏ `@import` font chặn render trong CSS; chuyển sang `wp_enqueue_style` chuẩn WordPress kết hợp thẻ `<link rel="preconnect">` tới `fonts.googleapis.com` và `fonts.gstatic.com`.
   - **Minify CSS & JS:** Bổ sung bản nén `style.min.css`, `responsive.min.css` và `main.min.js`, tự động ưu tiên load bản minified.
   - **Khai báo kích thước ảnh (Unsized Images):** Bổ sung thuộc tính `width` và `height` rõ ràng cho tất cả logo, trust bar, và dải logo watermark Section 11, đảm bảo CLS = 0.
   - **Khả năng tiếp cận (Accessibility WCAG):** Cải thiện độ tương phản màu (contrast ratio) cho chữ phụ, nhãn vàng Gold và mở rộng diện tích bấm (touch target >= 28px) cho các chấm tròn review slider.

## Hướng dẫn cập nhật lên Hosting

1. **Sao lưu:** Thực hiện sao lưu database, mã nguồn và thư mục `wp-content/uploads/` trên hosting trước khi cập nhật.
2. **Cập nhật Plugin nghiệp vụ:**
   - Truy cập trang quản trị WordPress hosting: **Plugin** → **Cài mới** → **Tải plugin lên**.
   - Chọn file `project-core-v1.0.2.zip`, bấm **Cài đặt ngay** và chọn **Thay thế bản hiện tại bằng bản tải lên**.
3. **Cập nhật Child Theme:**
   - Vào **Giao diện** → **Thêm mới** → **Tải theme lên**.
   - Chọn file `project-child-v1.0.2.zip`, bấm **Cài đặt ngay** và chọn **Thay thế bản hiện tại bằng bản tải lên**.
   - Đảm bảo Theme con `Project Child` đang kích hoạt (Theme mẹ `Kadence` vẫn được giữ nguyên).
4. **Xóa bộ nhớ đệm (Cache):**
   - Xóa toàn bộ cache trên hosting (LiteSpeed Cache / WP Rocket / Cloudflare / Nginx FastCGI nếu có).
   - Mở trình duyệt ẩn danh (Incognito) để kiểm tra luồng đặt hàng và màn cảm ơn.
