# Fractional CC 1.0.6 – Thành phần và lưu ý trên trang sản phẩm

Bỏ cột “Hướng Dẫn Sử Dụng” dạng chữ vì mỗi sản phẩm đã có ảnh hướng dẫn trong thumbnail. Phần còn lại dùng tiêu đề đã có “Bảng Thành Phần & Cơ Chế Hoạt Động”, các thẻ hoạt chất trải toàn chiều rộng: một cột trên điện thoại, hai cột trên tablet và tối đa ba cột trên desktop. Sản phẩm có bốn thẻ dùng bố cục hai hàng, mỗi hàng hai thẻ trên desktop. Lưu ý riêng của sản phẩm nằm bên dưới, giữ xuống dòng và ẩn khi trống.

## Bộ cài

| Package | Số file | SHA-256 |
| --- | ---: | --- |
| [Project Core 1.0.6](project-core-v1.0.6.zip) | 25 | `0965a579e46ea0ec684ed97f4df1df30552fd2bf5ee93653d4b2eb6e8394056a` |
| [Project Child 1.0.6](project-child-v1.0.6.zip) | 291 | `29f35ea0075ef8e9713e42f6c9c86d3f8d00294a380cc8e1c4d35ae21feda7e2` |

Đúng hai ZIP với root `project-core/` và `project-child/`. Cần WordPress, Kadence và ACF cài riêng; yêu cầu khai báo WordPress 6.3+, PHP 7.4+ và phiên bản PHP tương thích các dependencies. Môi trường kiểm tra: XAMPP/PHP 8.2.12, WordPress 7.1.3, Kadence 1.5.2, ACF Free 6.8.10.

## Dọn code và dữ liệu mặc định

- Bỏ fragment `steps`, vòng đọc `howToUse`, field ACF hướng dẫn và hai nhãn không còn dùng. Tab sản phẩm đổi thành **Công dụng & lưu ý**; cập nhật mô tả trong quản trị.
- Bỏ wrapper hai cột, panel lồng nhau, icon sách và toàn bộ CSS cho danh sách bước. Tạo lại CSS minified/bundle từ source.
- Defaults 1.0.6 không nhập nội dung hướng dẫn dạng chữ đã bỏ; giữ nguyên ảnh, gallery, hoạt chất, lưu ý, giá và nội dung khác. Không xóa metadata cũ trong database khi nâng cấp.
- Tiêu đề phần mới dùng field thành phần hiện có, nên site đã cài nhận giao diện mới ngay khi thay theme và vẫn giữ chỉnh sửa tiêu đề đó. Không cần migration ghi đè nhãn.
- Giữ sửa ảnh email, hiệu năng và quản lý ảnh từ các bản trước. Template HTML gốc trong `template/` là tài liệu tham chiếu lịch sử; compiler legacy vẫn bị chặn khi defaults đã approved.

## Cập nhật hosting

1. Sao lưu riêng database, code và uploads.
2. Upload ZIP core trong **Plugin → Cài mới → Tải plugin lên**, chọn thay thế bản hiện tại. Upload ZIP child trong **Giao diện → Thêm mới → Tải theme lên**, thay thế bản hiện tại. Giữ Project Child/Kadence và ACF hoạt động.
3. Không cần ACF Sync hoặc sửa từng sản phẩm. Nếu cần ghi nhận defaults 1.0.6 hoặc bổ sung ảnh từ bản cũ, vào **Nội dung website → Bổ sung dữ liệu còn thiếu**. Chạy lại giữ nội dung, ảnh và thứ tự đã chỉnh/xóa.
4. Xóa cache hosting/CDN/trình duyệt; kiểm tra phần thành phần, lưu ý, gallery và ảnh hướng dẫn trên trang chi tiết.

## Kiểm tra

Đã cài từ ZIP trên site/database XAMPP riêng, chặn toàn bộ mail ra ngoài. Chưa triển khai hosting; workspace hiện không có `wp-config.php` của site làm việc.

- 2.724 kiểm tra cài mới defaults, Media, nội dung, quan hệ và chạy lại giữ chỉnh sửa: đạt. Có đủ 10 sản phẩm và 70 Media.
- 67 kiểm tra nâng cấp từ 1.0.5: nội dung và metadata của 52 bản ghi giữ nguyên, gồm gallery, ảnh đại diện, lưu ý và dữ liệu hướng dẫn lịch sử; field bị bỏ không còn đăng ký hoặc đọc vào dữ liệu frontend.
- 1.601 kiểm tra browser cho 10 sản phẩm tại 13 chiều rộng từ 320 đến 1.920 px: không tràn ngang; bố cục thành phần và lưu ý đúng; không còn panel hướng dẫn; tabs, gallery và modal mua hàng hoạt động. Không có lỗi console/runtime.
- Kiểm tra ảnh chụp desktop/mobile cho sản phẩm có 3, 4 và 5 hoạt chất, có hoặc không có lưu ý. Fixture cài mới không phát sinh debug log.
- Toàn bộ PHP trong hai ZIP qua lint; kiểm tra CRC, root, inventory và byte khớp source. Xem [package report](qa/package-report.json), [browser report](qa/browser-report.json) và [verification](qa/verification.txt).

Không gửi thử đơn/mail thật hoặc thay đổi SMTP/DNS trong bản sửa giao diện này.
