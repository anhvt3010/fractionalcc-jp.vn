# Project Child 3.0.2 – Popup video và giao diện combo

Ngày 17/10/2026. Phát hành **chỉ giao diện** theo yêu cầu, dùng cùng **Project Core/defaults 3.0.0**, Kadence và ACF hiện có. Không xuất lại CMS hoặc thay đổi dữ liệu mặc định.

## Gói cập nhật

[project-child-v3.0.2.zip](project-child-v3.0.2.zip) — 29.172.333 byte, 306 file, một root `project-child/`.

SHA-256: `22aee57d5a436e84332fc4c51efcb6a8602c7ad1af670da0e832cdfc2edbe89f`.

Đã kiểm tra CRC, đường dẫn an toàn, không chứa secrets/config/uploads/database và từng file khớp byte với source. [Báo cáo gói](qa/packages.json). So với ZIP 3.0.1: 9 file thay đổi nội dung, 15 file chỉ khác xuống dòng; không thêm/xóa file. [Đối chiếu](qa/changes.json).

## Thay đổi đã chốt

- Hai popup video mặc định khung dọc; video upload tự nhận tỷ lệ thực tế từ metadata, hỗ trợ cả video ngang. Khung co theo chiều rộng/chiều cao màn hình, giữ đầy đủ hình ảnh và thanh điều khiển. Đóng popup dừng và gỡ video; hỗ trợ Esc, bấm nền, phím Tab và trả focus về nút mở.
- Bỏ giá mua lẻ của từng sản phẩm trong combo tại danh mục trang chủ và combo nổi bật đầu trang Sản phẩm. Giữ tên sản phẩm, giá combo, quà tặng và nút mua.
- Hiển thị giá trị CMS **Thông tin vi kim theo hồ sơ** dưới tên từng sản phẩm trong combo đầu trang Sản phẩm, chữ vàng đậm `#8A641D`; trường trống tự ẩn, output được escape.
- Ảnh combo đầu trang Sản phẩm giữ tỷ lệ gốc và đủ nội dung bên phải. Khung ảnh không kéo cao theo cột chữ. Cân đối hai cột và khoảng cách thông tin ở màn hình từ 1180px; dưới 1180px chuyển một cột để ảnh không bị cắt hoặc hở dải trắng trên/dưới.
- Kế thừa nền trắng/be xen kẽ của giao diện 3.0.1.

## Kiểm tra

Build/lint bằng XAMPP PHP 8.2.12. Runtime nguồn thực tế: `D:/xampp/htdocs/fractionalcc-jp.vn`, WordPress 7.1.3, Project Child 3.0.2/Kadence 1.5.2 và Project Core 3.0.0/ACF 6.8.10 active. Hash nội dung/CMS trước và sau đóng gói giống nhau. [Runtime](qa/runtime.json).

Kiểm tra trên site/database riêng `fcc-release-qa-302`, cài plugin 3.0.0 và theme từ ZIP:

| Kiểm tra | Kết quả |
| --- | --- |
| Cài mới, approved defaults/media/quan hệ/nội dung | 3.057 đạt |
| Nâng theme 3.0.1 → 3.0.2, chạy initializer hai lần | 7.230 đạt; giữ 131 bản ghi, 3.472 metadata, 12 settings và các giá trị đã sửa/xóa |
| URL video, ưu tiên Media, field, quyền và template | 49 đạt |
| Thành phần combo, giá combo, escaping và rendering | 29 đạt |
| Popup browser | 232 đạt, không lỗi JavaScript; 7 kích thước 320–1440px, video dọc/ngang và xoay màn hình |
| Combo trang chủ/Sản phẩm | 19 đạt tại 1440, 768 và 375px |
| Ảnh combo | Kiểm tra 9 kích thước 320–1440px, gồm hai phía breakpoint 1180px; không crop hoặc hở khung ảnh |
| SEO HTTP | 290 đạt trên 15 trang, gồm sản phẩm có giá/ảnh cố ý xóa |

[Nâng cấp](qa/upgrade.json), [popup](qa/popup-browser.json), [combo](qa/combo-layout.json), [ảnh](qa/combo-images.json), [tổng hợp](qa/verification.json).

Video dọc/ngang trong kiểm tra trình duyệt bản ZIP là fixture WebM tạo tại local. Iframe bên ngoài được stub để kiểm tra cách chọn player; không xác nhận khả năng phục vụ Google Drive/YouTube từ nhà cung cấp. Hai MP4 1080×1920 người dùng upload đã được kiểm tra phát trên site làm việc trong lượt sửa trước. Không chạy lại toàn bộ kiểm thử form/email hoặc popup ảnh sản phẩm; hạn chế popup ảnh Mandelic 320–375px của bản trước chưa được xử lý trong bản giao diện này.

## Cập nhật hosting

1. Sao lưu giao diện hiện tại và dữ liệu hosting.
2. Vào **Giao diện → Thêm giao diện → Tải giao diện lên**, chọn ZIP 3.0.2 và xác nhận thay thế Project Child hiện tại. Giữ Kadence, Project Core 3.0.0 và ACF.
3. Không cần ACF Sync hoặc chạy lại khởi tạo dữ liệu. Gói này giữ nội dung, giá, ảnh và lựa chọn video đã lưu trên hosting. Video/ảnh riêng vừa upload ở local không được chuyển lên hosting qua ZIP theme; nếu chưa có trên hosting, upload và chọn chúng trong CMS của hosting.
4. Xóa cache hosting/CDN, tải lại và kiểm tra trang chủ, trang Sản phẩm cùng hai popup trên PC/mobile.

Chưa triển khai hosting. Nếu cần rollback giao diện, cài lại [ZIP 3.0.1](../v3.0.1/project-child-v3.0.1.zip); không reset hoặc khởi tạo lại dữ liệu.

Build: `powershell -NoProfile -ExecutionPolicy Bypass -File scripts/build-release.ps1 -Version 3.0.2 -Package project-child`. Kiểm tra: `node scripts/verify-release-packages.mjs 3.0.2 project-child`. Script build không ghi đè ZIP đã tồn tại; mặc định vẫn build đủ hai gói cho release đầy đủ.
