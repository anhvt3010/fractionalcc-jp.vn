# Project Child 3.0.1 – Nền section trang chủ

Ngày 16/10/2026. Theo yêu cầu, chỉ phát hành **giao diện**; tiếp tục dùng **Project Core/defaults 3.0.0**. Thư mục này chỉ có một ZIP theme, không build lại plugin hoặc xuất lại dữ liệu.

## Gói cập nhật

[project-child-v3.0.1.zip](project-child-v3.0.1.zip) — 29.171.554 byte, 306 file, root `project-child/`.

SHA-256: `dc435ae842642dfe9b266be7b1cf01be9ed619ea4eea407aa9f9464516317faa`.

So với theme 3.0.0, chỉ thêm `assets/fractional/home.css` và tăng version trong `style.css`. CSS trang chủ được nạp qua cơ chế asset theo loại trang đã có. [Đối chiếu file trong ZIP](qa/package.json).

## Thay đổi

Các section nội dung trang chủ xen kẽ nền trắng `#FFFFFF` và be nhạt `#F3F1EC`, dùng bảng màu hiện có:

| Section theo thứ tự | Nền |
| --- | --- |
| Sản phẩm chủ lực | Trắng |
| Giới thiệu thương hiệu | Be nhạt |
| Vấn đề làn da | Trắng |
| Điểm nổi bật | Be nhạt |
| Danh mục sản phẩm & combo | Trắng |
| Chu trình chăm sóc sáng & tối | Be nhạt |
| Đánh giá khách hàng | Trắng |
| Cam kết pháp lý | Be nhạt |
| FAQ | Trắng |
| Đặt hàng | Be nhạt |

Hero và CTA cuối giữ nền riêng. Nội dung, bố cục, chức năng và dữ liệu giữ nguyên.

Đã kiểm tra màu nền thực tế trên trình duyệt tại 1440 và 375 px: cả 10 section nội dung không có cặp liền nhau trùng nền; Danh mục/Chu trình/Đánh giá lần lượt trắng/be/trắng. [Kết quả kiểm tra màu](qa/backgrounds.json). Không chạy lại bộ regression/cài mới/nâng cấp theo yêu cầu người dùng; build vẫn thực hiện lint PHP sẵn có trong pipeline.

## Cập nhật hosting

Upload và thay thế **chỉ ZIP Project Child 3.0.1** trong Giao diện. Giữ Project Core 3.0.0 hiện tại; không khởi tạo dữ liệu hoặc ACF Sync. Sau cập nhật, xóa cache hosting/CDN và tải lại trang chủ để nhận CSS mới. Chưa triển khai lên hosting.

Build lại bằng `powershell -NoProfile -ExecutionPolicy Bypass -File scripts/build-release.ps1 -Version 3.0.1 -Package project-child` ở một đích release chưa có ZIP. Tham số `-Package` mặc định là `all`, giữ quy trình hai ZIP của bản phát hành đầy đủ.
