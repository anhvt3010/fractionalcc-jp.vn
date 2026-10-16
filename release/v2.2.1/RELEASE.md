# Fractional CC 2.2.1

Ngày 16/10/2026. Build phiên bản kế tiếp theo yêu cầu người dùng, giữ giao diện và chức năng của 2.2.0: trang chi tiết combo theo bố cục sản phẩm, nhiều thumbnail, popup ảnh và quản lý đại diện/hover/thứ tự. Combo C–CX4 có ảnh đại diện cũ cùng 3 ảnh vừa bổ sung.

Đóng gói lại **cùng version 2.2.1** theo yêu cầu sửa câu quà tặng thành: **QUÀ TẶNG: Serum dưỡng môi PDRN Shot theo ảnh combo; xác nhận khi đặt hàng.** Đã sửa `fcc_gift` của combo trong CMS làm việc và defaults. Câu cũ không còn trên trang chủ, danh mục, chi tiết combo hoặc sản phẩm liên quan đã kiểm tra qua HTTP. Không chạy initializer trên site làm việc.

## Gói cài đặt

| Gói | Dung lượng | SHA-256 |
| --- | --- | --- |
| [Giao diện Project Child](project-child-v2.2.1.zip) | 26.487.768 byte | `e954f28d46aeee3b0ddbad6080404feb37ba922f8d7e0f0ba006e7c2b6a25954` |
| [Plugin Project Core](project-core-v2.2.1.zip) | 141.074 byte | `a77d548ce24d82038bfc4101bcf9dcdb9b3a6cb1713de794b08fe5d20325c61a` |

Đúng hai ZIP, root `project-child/` và `project-core/`. Plugin gồm defaults và initializer; theme gồm giao diện, schema và tài nguyên ảnh. Không kèm core WordPress, parent theme, plugin bên thứ ba, cấu hình riêng, uploads hoặc dữ liệu vận hành.

So với lần đóng gói 2.2.1 trước, chỉ sửa đúng câu quà tặng trong `defaults/fractional.json`; toàn bộ file bên trong theme và code chức năng giữ nguyên. Không xuất lại CMS. Defaults gồm 10 sản phẩm, 1 combo, 35 hoạt chất và 74 Media. [Báo cáo CRC/SHA-256/đối chiếu source và gói 2.2.1 trước](qa/packages.json). Các ZIP trước khi đóng gói lại được lưu riêng ngoài repository.

## Cài đặt / cập nhật

1. Sao lưu code, database và uploads của site đích.
2. Cài Kadence và ACF riêng; tải lên/thay thế **cả hai ZIP 2.2.1**, kích hoạt Project Core và Project Child.
3. Mở quản trị bằng administrator để xử lý khởi tạo đang chờ. Khi cần thêm dữ liệu mặc định còn thiếu, dùng **Nội dung website → Bổ sung dữ liệu còn thiếu**. Không cần ACF Sync.
4. Kiểm tra trang combo `/combo/{slug}/`, thumbnail, popup ảnh và nút mua. Quản lý ảnh tại **Nội dung website → Combo → Sửa → Ảnh sản phẩm / combo – đại diện & thứ tự**, rồi nhấn **Cập nhật**.

Với hosting đã có dữ liệu cũ, ZIP giữ nội dung đã lưu theo chính sách bảo toàn edits. Để đổi câu trên hosting, mở **Nội dung website → Combo → Sửa → Quà tặng đã xác nhận**, nhập `Serum dưỡng môi PDRN Shot theo ảnh combo; xác nhận khi đặt hàng.` rồi nhấn **Cập nhật**. Site cài mới dùng câu mới từ defaults.

Initializer có version và có thể chạy lại; giữ nội dung, ảnh, gallery/thứ tự, giá trị trống và lựa chọn khách hàng đã sửa/xóa. Site mới nhập gallery combo; site có gallery tùy chỉnh giữ lựa chọn hiện tại. Rollback bằng bản sao lưu đích và cặp ZIP trước, bảo toàn dữ liệu phát sinh.

## Kiểm tra thực tế

Các kiểm tra cài mới/nâng cấp toàn bộ bên dưới thuộc lần build 2.2.1 trước khi sửa câu. Sau lần đóng gói lại đã kiểm tra CRC/SHA-256/toàn bộ byte khớp source, xác nhận chỉ một câu defaults thay đổi, PHP lint qua build, nội dung hiển thị trên 4 trang HTTP và chạy lại 22 kiểm tra chống dữ liệu trùng từ ZIP mới.

XAMPP PHP 8.0.30, WordPress 7.1.3, Kadence 1.5.2, ACF 6.8.10. Cài mới tại `C:/xampp/htdocs/fcc-release-qa-221`, database riêng `fcc_release_qa_221`, dùng plugin/theme giải nén từ ZIP. Nâng cấp tại fixture có marker `C:/xampp/htdocs/fcc-combo-qa-220`, từ gói 2.2.0 sang gói 2.2.1; dữ liệu kiểm tra được khôi phục sau khi xác minh.

| Kiểm tra | Kết quả |
| --- | --- |
| Khởi tạo mới, nội dung/defaults/quan hệ/Media và chạy lại | [3.027 đạt](qa/fresh-install.txt) |
| Kiểm tra bổ sung chống dữ liệu trùng: 3 lần khởi tạo liên tiếp, callback kích hoạt/tự động gọi lặp, hàng đợi cũ, khóa worker và quyền | [22 đạt; ID, 138 hàng posts và 3.442 hàng metadata giữ nguyên](qa/idempotency.json) |
| Nâng từ 2.2.0 và chạy lại: 126 bản ghi, settings, đổi tên/thứ tự và xóa ảnh/gallery/giá | [280 đạt](qa/upgrade.txt) |
| Chi tiết combo, lưu POST quản trị, nonce/quyền, gallery, SEO/HTTP và nâng gallery cũ | [42 đạt](qa/combo-detail.txt) |
| Client đã minify: chọn đúng combo/sản phẩm và khởi tạo gallery | [18 đạt](qa/combo-client.txt) |
| Hồi quy ảnh sản phẩm, quyền, xóa và khởi tạo lại | [84 đạt](qa/product-media.txt) |
| Thiếu ACF / thiếu child theme | [Từ chối khởi tạo, giữ nội dung và seed](qa/dependencies.txt) |
| Root ZIP, CRC, SHA-256 và byte khớp source | [Đạt: 28 file plugin / 300 file theme](qa/packages.json) |
| Build WebP, CSS/JS và PHP lint trong hai gói; syntax JS đã minify | Đạt |

Không chạy initializer hoặc thay đổi nội dung/database site làm việc khi đóng gói; seed nguồn giữ 2.1.0. Chưa triển khai hosting, chưa gửi đơn/email thật. Local/staging tiếp tục noindex.

Rà soát bổ sung theo yêu cầu người dùng: [site làm việc không có bản ghi trùng mã nguồn, đường dẫn Media hoặc byte ảnh](qa/working-site-duplicate-audit.json); hiện có 10 sản phẩm, 1 combo, 35 hoạt chất và 74 Media. Các lần gọi khởi tạo lặp chạy trên fixture riêng. Kích hoạt plugin/theme trên site đã có seed không xếp hàng khởi tạo mới; lệnh bổ sung thủ công có thể chạy lại để điền dữ liệu còn thiếu và tái sử dụng ID theo source key. Mã khởi tạo giữ nguyên. Đã chạy lại 22 kiểm tra idempotency từ hai ZIP sau sửa câu quà tặng; số hàng/ID/dữ liệu fixture không đổi. Có thể chạy lại bằng `PROJECT_WORDPRESS_ROOT` trỏ tới fixture có marker và `C:/xampp/php/php.exe release/v2.2.1/qa/verify-idempotency.php`.

## Giới hạn

Công cụ máy tính không có app/trình duyệt kết nối trong lượt build này; chưa xác minh trực quan desktop/mobile, focus và popup thực tế. Giữ hạn chế popup Mandelic tại 320–375 px đã ghi ở 2.2.0. Bản build không thay giao diện hoặc sửa giới hạn này.
