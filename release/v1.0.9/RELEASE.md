# Fractional CC 1.0.9 – Chốt code và dữ liệu mặc định tối 14–15/10/2026

Đóng gói source hiện tại cùng toàn bộ thay đổi đã hoàn tất trong buổi tối. Xuất lại dữ liệu mặc định từ CMS XAMPP đang dùng theo yêu cầu người dùng; giữ nguyên nội dung, nhãn thông tin và lựa chọn đã lưu. Core, Child và defaults cùng version **1.0.9**, trạng thái `approved`.

## Hai gói cài đặt

| Package | Số file | SHA-256 |
| --- | ---: | --- |
| [Project Core 1.0.9](project-core-v1.0.9.zip) | 25 | `db175a91ef7f6df60cde3fb927b729bae0b9d2835bc503d16a0407f6e69f16a5` |
| [Project Child 1.0.9](project-child-v1.0.9.zip) | 291 | `b1eef8a944cffa39e88b7cc0e75b22b443a5c07a543f7cb70b36efb5bab39911` |

ZIP có đúng root `project-core/` và `project-child/`. WordPress, Kadence và ACF cài riêng. Không chứa cấu hình máy, SMTP/password, tài khoản, đơn hàng, database dump, logs hoặc uploads riêng. Yêu cầu khai báo WordPress 6.3+, PHP 7.4+ và phiên bản PHP tương thích dependencies. Đã kiểm tra với XAMPP/PHP 8.2.12, WordPress 7.1.3, Kadence 1.5.2 và ACF Free 6.8.10.

## Code đã chốt

- Bộ ảnh từ `template/Thumbnail` dưới dạng WebP: ảnh cho 10 sản phẩm và 7 avatar; quản lý ảnh đại diện độc lập thứ tự gallery, kéo thả và nút lên/xuống trong editor.
- Trang chi tiết giữ bố cục hiện tại, ảnh tràn khung và chip thông tin dưới tiêu đề; giữ nguyên bốn nhãn thông tin đang lưu trong CMS.
- Bỏ hướng dẫn sử dụng dạng chữ đã có trong thumbnail. Phần “Bảng Thành Phần & Cơ Chế Hoạt Động” dùng các thẻ responsive và lưu ý bên dưới; bỏ hover của các thẻ.
- Ảnh lớn không phóng to khi hover; con trỏ kính lúp mở popup native có thumbnail, bộ đếm, trước/sau, phím mũi tên, Esc, nút đóng và backdrop. Có kiểm soát focus và cuộn nền.
- Bao gồm các sửa CSS mới nhất để ảnh sản phẩm/combo không zoom khi hover trong danh mục trang chủ và trang sản phẩm.
- Giữ các tối ưu hiệu năng và sửa email: ảnh dùng URL công khai, không nhúng CID hoặc tạo tệp đính kèm.

## Dữ liệu mặc định và thiết lập

Snapshot được xuất bằng `scripts/export-fractional-defaults.php --user=1 --version=1.0.9 --approve` trên site làm việc XAMPP. Lệnh xuất chỉ đọc nội dung/thiết lập được phép và ghi snapshot/asset công khai; không chạy initializer trên database làm việc.

- 6 bản ghi nội dung với **1.090 giá trị field**; **10 sản phẩm, 1 combo, 35 hoạt chất và 70 Media**.
- Giữ tên/giá/mô tả, lưu ý, quan hệ hoạt chất, ảnh đại diện, gallery và thứ tự, avatar, alt/caption, nội dung/SEO, liên hệ, sản phẩm nổi bật, danh mục trang chủ và phác đồ đã chọn.
- 8 thiết lập: tên website, mô tả, múi giờ, UTC offset, định dạng ngày/giờ, ngày đầu tuần và permalink. Không xuất domain máy nguồn hoặc thiết lập SMTP riêng.
- Ảnh và quan hệ được lưu bằng đường dẫn/source key để chuyển sang site khác; URL nội bộ dùng token thay thế khi khởi tạo.
- Exporter không đưa các field đã bỏ khỏi CMS vào snapshot. Hai nhãn khối cách dùng cũ và `howToUse` không quay lại trong cài mới; metadata lịch sử trên site cũ được giữ.
- Initializer có version, kiểm tra quyền và dependencies, chạy lại giữ các chỉnh sửa và giá trị đã xóa. Cài mới khôi phục snapshot; cập nhật site đang dùng chỉ bổ sung dữ liệu thiếu, không ép thay nội dung có sẵn.

## Cập nhật hosting

1. Sao lưu riêng database, code và uploads trên hosting.
2. Vào **Plugin → Cài mới → Tải plugin lên**, tải ZIP Core và chọn thay thế bản hiện tại. Vào **Giao diện → Thêm mới → Tải theme lên**, tải ZIP Child và thay thế bản hiện tại. Giữ Project Child/Kadence và ACF hoạt động.
3. Vào **Nội dung website → Bổ sung dữ liệu còn thiếu** để ghi nhận defaults 1.0.9 và bổ sung dữ liệu còn thiếu. Không cần ACF Sync, không reset catalog hoặc nhập database thử nghiệm.
4. Nội dung đã có, kể cả đã sửa/xóa, được giữ. Nếu site cũ cần đổi bốn nhãn theo bản chốt mới, chỉnh tại **Thông tin chung → 12. Sản phẩm – nhãn thông tin**; initializer không ghi đè các nhãn đã lưu.
5. Xóa cache hosting/CDN/trình duyệt. Kiểm tra avatar, danh mục, trang chi tiết, gallery/popup và email theo quy trình thử nghiệm của bạn.

## Kết quả kiểm tra

Kiểm tra cài mới từ ZIP và cập nhật trên site/database riêng, có marker bảo vệ và chặn mail ra ngoài:

| Kiểm tra | Kết quả |
| --- | ---: |
| Defaults, Media, nội dung, quan hệ và chạy lại giữ edits trên cài mới | 2.724 đạt |
| Nâng cấp từ 1.0.8, giữ nội dung/metadata của 52 bản ghi và field đã bỏ | 67 đạt |
| Ảnh sản phẩm, avatar, thứ tự, quyền/nonce, xóa lựa chọn | 83 đạt |
| Ảnh/HTML/cache/security và giữ ảnh gốc | 149 đạt |
| Snapshot đơn hàng, biên lai, hiển thị và quyền riêng tư | 181 đạt |
| MIME email, ảnh HTTP 200 và không có attachment | 219 đạt |
| Security, tất cả mail được bắt trong fixture | 82 đạt |
| Browser: 10 sản phẩm × 13 chiều rộng, gallery/popup/tabs/modal | 2.106 đạt |

Trang chủ/danh mục được kiểm tra thêm tại 375 px và 1.440 px: ảnh tải được, hover không zoom, không tràn ngang hoặc lỗi console/runtime. Có kiểm tra popup với gallery một ảnh và màn hình ngang cao 320 px. Đã xem ảnh chụp desktop/mobile; fixture cài mới không có debug log.

Toàn bộ PHP trong hai gói qua lint; JS source/minified và script browser qua kiểm tra cú pháp. ZIP CRC, root, inventory và byte khớp source. Xem [package report](qa/package-report.json), [defaults report](qa/defaults-report.json), [browser report](qa/browser-report.json), [home/catalog report](qa/pages-report.json) và [verification](qa/verification.txt).

Site làm việc đã được xác nhận chạy tại `D:/xampp/htdocs/fractionalcc-jp.vn` với Project Child/Kadence và Project Core/ACF. Database làm việc vẫn ghi seed 1.0.5; việc xuất snapshot không thay dữ liệu site đó. **Chưa triển khai lên hosting** và không cấu hình SMTP/DNS trong lần build này.
