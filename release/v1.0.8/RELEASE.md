# Fractional CC 1.0.8 – Popup xem ảnh sản phẩm

Trên trang chi tiết, ảnh lớn dùng con trỏ kính lúp và không còn phóng to khi hover. Bấm ảnh mở popup giữa màn hình, hiển thị đúng ảnh đang chọn và dãy thumbnail cùng thứ tự của gallery. Thay đổi ảnh trong popup đồng bộ về gallery ngoài trang.

## Bộ cài

| Package | Số file | SHA-256 |
| --- | ---: | --- |
| [Project Core 1.0.8](project-core-v1.0.8.zip) | 25 | `e46f655fca6bb6e38277e964fe2ce829ab0d420bda925f89797cf5db480a572d` |
| [Project Child 1.0.8](project-child-v1.0.8.zip) | 291 | `3c4f5e972cbd8b5239217699268e48486dcdfceb7646cfe92fc857905492d974` |

ZIP có root `project-core/` và `project-child/`; không chứa WordPress core, dependencies, cấu hình hoặc uploads riêng. Cần WordPress, Kadence và ACF hoạt động. Môi trường kiểm tra: XAMPP/PHP 8.2.12, WordPress 7.1.3, Kadence 1.5.2 và ACF Free 6.8.10.

## Hiển thị và tương tác

- Popup dùng `dialog` native, không thêm thư viện. Ảnh lớn chỉ nhận URL khi mở popup; thumbnail dùng lại ảnh nhỏ hiện có, tạo một lần khi mở đầu tiên.
- Có nút trước/sau, thumbnail, bộ đếm ảnh, phím mũi tên; hỗ trợ đóng bằng nút X, Esc hoặc bấm vùng nền ngoài popup. Gallery một ảnh ẩn hai nút chuyển không cần thiết.
- Hỗ trợ mở bằng Enter, giữ Tab/Shift+Tab trong popup, khóa cuộn trang nền và khôi phục focus/cuộn khi đóng. Ảnh giữ tỉ lệ trong popup, các nút tối thiểu 44 px.
- Nhãn và alt text được escape; URL/alt/thứ tự lấy từ gallery WordPress hiện có. Không tạo thêm dữ liệu ảnh, không thay đổi cơ chế quản lý đại diện/thứ tự.
- Xóa scale/transition hover của ảnh ở cả stylesheet gốc và stylesheet trang sản phẩm; build lại CSS/JS tối ưu. Giữ các chỉnh sửa ảnh tràn khung và chip thông tin dưới tiêu đề đã có trong workspace lúc đóng gói.
- Core và defaults chỉ tăng version 1.0.8; nội dung mặc định khớp 1.0.7 ngoài version. Các sửa email, hiệu năng và thẻ hoạt chất từ bản trước được giữ.

## Cập nhật hosting

1. Sao lưu riêng database, code và uploads.
2. Tải ZIP Core trong **Plugin → Cài mới → Tải plugin lên**, thay thế bản hiện tại. Tải ZIP Child trong **Giao diện → Thêm mới → Tải theme lên**, thay thế bản hiện tại.
3. Không cần sửa từng sản phẩm hoặc ACF Sync. Nếu muốn ghi nhận defaults mới, dùng **Nội dung website → Bổ sung dữ liệu còn thiếu**; dữ liệu đã sửa vẫn được giữ.
4. Xóa cache hosting/CDN/trình duyệt. Kiểm tra hover ảnh, mở popup từ ảnh đang chọn và đổi thumbnail trên điện thoại/máy tính.

Trình duyệt cần hỗ trợ native `dialog` để xem popup. Nếu không hỗ trợ, liên kết ảnh vẫn mở ảnh đầy đủ.

## Kiểm tra

- Cài mới từ ZIP trên WordPress/database XAMPP riêng: 2.724 kiểm tra defaults, Media, nội dung, quan hệ và chạy lại giữ edits đạt; đủ 10 sản phẩm và 70 Media.
- 2.106 kiểm tra browser: 10 sản phẩm trên 13 chiều rộng 320–1.920 px; popup tại 320/375/768/1.440 px, mở đúng ảnh, đồng bộ thumbnail, trước/sau, bàn phím, focus, Esc, nút đóng, backdrop và cuộn trang. Có thêm màn hình ngang cao 320 px và gallery một ảnh. Không có lỗi console/runtime.
- Kiểm tra ảnh chụp popup desktop/mobile, cùng bố cục trang chi tiết, tabs, gallery và modal mua hàng. Không gửi form thật; toàn bộ mail ra ngoài bị chặn trong fixture.
- PHP trong hai ZIP qua lint, JavaScript qua kiểm tra cú pháp; ZIP CRC, root, inventory và bytes khớp source. Xem [package report](qa/package-report.json), [browser report](qa/browser-report.json) và [verification](qa/verification.txt).

Chưa triển khai hosting; workspace không có `wp-config.php` của site làm việc. Không cấu hình hoặc kiểm tra SMTP/DNS trong bản sửa này.
