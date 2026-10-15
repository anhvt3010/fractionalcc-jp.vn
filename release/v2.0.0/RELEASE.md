# Fractional CC 2.0.0 – Chốt code và dữ liệu hiện tại

Ngày 15/10/2026. Theo yêu cầu chốt hiện trạng, đóng gói toàn bộ code dự án và xuất lại nội dung, ảnh, giá, SEO, nhãn và lựa chọn hiện tại trong CMS XAMPP. Core, Child và defaults cùng version **2.0.0**, trạng thái `approved`. Giữ nguyên giao diện đã chốt. Chưa triển khai hosting.

## Hai gói cài đặt

| Package | Số file | SHA-256 |
| --- | ---: | --- |
| [Project Core 2.0.0](project-core-v2.0.0.zip) | 27 | `8d6d72f2636b2b2ef929df595f06148a82f2d6e2d16643ee385ece05018ea589` |
| [Project Child 2.0.0](project-child-v2.0.0.zip) | 294 | `42397e09a039dc04f6618ec07403d1e90d8121d2a4d9bdce45d365ac0e791364` |

Đúng hai ZIP; root `project-core/` và `project-child/`. Plugin chứa defaults và initializer có version; child theme chứa giao diện, schema và ảnh nguồn. Không chứa WordPress core, Kadence, ACF, cấu hình, credentials, database, tài khoản, đơn hàng, logs hoặc uploads riêng.

Cài WordPress, Kadence và ACF riêng. Header yêu cầu WordPress 6.3+, PHP 7.4+; PHP thực tế phải đáp ứng dependencies. Runtime đã kiểm tra: PHP **8.0.30**, WordPress **7.1.3**, Kadence **1.5.2**, ACF Free **6.8.10**, Core/Child active trên XAMPP. Xem [runtime](qa/runtime.json).

## Nội dung đã chốt

- 6 bản ghi nội dung với **1.090 giá trị field**, **10 sản phẩm**, **1 combo**, **35 hoạt chất**, **71 Media** và 8 thiết lập website công khai.
- Giữ nội dung/giá/SEO/liên hệ, gallery/thứ tự/ảnh đại diện/ảnh hover, alt/caption, nhãn sản phẩm và combo, lựa chọn sản phẩm chủ lực, danh mục trang chủ và phác đồ hiện có.
- Gồm giao diện thẻ/nhãn hiện tại, đổi ảnh khi hover, quản lý ảnh hover trong editor, danh sách sản phẩm thành phần combo và liên kết ảnh mở chi tiết đang có trong source.
- Mandelic có 5 ảnh trong gallery, gồm `mandelic-shot-peel-essence-hover.webp`; 9 sản phẩm còn lại có 4 ảnh. Nhãn và ảnh hover lấy đúng giá trị CMS đã được người dùng chốt, không tự gán mới.
- Exporter bổ sung xuất `fcc_hoverImage` của sản phẩm dưới dạng image field có đường dẫn portable. Trường này thuộc khung Media native, không đăng ký ACF; bản exporter trước bỏ sót. Dùng initializer hiện có để ánh xạ ảnh sang ID site đích và giữ edits khi chạy lại. Không thay đổi giao diện hoặc logic frontend để đóng gói.

Không chạy initializer, reset catalog hoặc thay Reading/URL/activation trên site làm việc; seed tại đó vẫn **1.0.10**. Code/header và defaults trên đĩa đã là 2.0.0.

## Cài mới / cập nhật hosting

1. Sao lưu riêng database, code và uploads của môi trường đích; xác nhận có thể khôi phục.
2. Cài/kích hoạt Kadence và ACF. Upload ZIP Core trong Plugin và ZIP Child trong Giao diện; khi cập nhật chọn thay thế bản hiện có. Kích hoạt Project Core và Project Child.
3. Mở admin bằng administrator có quyền upload; cài mới sẽ khởi tạo khi đủ dependencies. Có thể dùng **Nội dung website → Bổ sung dữ liệu còn thiếu** để ghi nhận defaults 2.0.0. Không cần ACF Sync.
4. Xóa cache và kiểm tra trang chủ, danh mục, nhãn, đổi ảnh hover, chi tiết, popup ảnh và form theo workflow hiện tại.

Site mới nhận snapshot hiện tại. Site đã có dữ liệu chỉ bổ sung giá trị còn thiếu; dữ liệu đã sửa hoặc đã để trống được giữ. Vì vậy cập nhật ZIP **không ép nhãn/ảnh/nội dung của snapshot lên giá trị đã tồn tại trên hosting**. Không reset catalog để đồng bộ dữ liệu. Domain, secrets và cấu hình gửi mail của hosting quản lý riêng.

Rollback dùng backup môi trường đích cùng cặp code đã kiểm chứng, bảo toàn dữ liệu mới phát sinh. Không sao chép database QA lên hosting.

## Kiểm tra thực tế

Kiểm tra bằng hai ZIP cuối cùng trên hai site/database mới riêng biệt, có marker bảo vệ và chặn mail ra ngoài:

| Kiểm tra | Kết quả |
| --- | --- |
| Cài mới defaults, Media, nội dung, quan hệ, chạy lại và bảo toàn giá trị trống | **3.016 đạt** |
| Đối chiếu CMS nguồn với snapshot: tên, nhãn, ảnh hover của toàn bộ sản phẩm/combo | **33 đạt**, seed/Reading site làm việc giữ nguyên |
| SEO/HTTP, metadata, escaping và quyền trên 15 trang | **301 đạt** |
| Nâng từ ZIP 1.0.11 lên 2.0.0 và chạy lại | **6.664 đạt**, giữ 127 bản ghi hiện có và metadata |
| Edits khi nâng cấp | Giữ title, SEO đã sửa/xóa, ảnh đại diện/hover đã xóa, gallery đã sửa, nhãn đã chọn/bỏ chọn và Reading/settings |
| Thiếu ACF / thiếu child theme | Từ chối khởi tạo trước khi ghi nội dung |
| Browser đối chiếu site làm việc và site cài từ ZIP | **28 đạt**, trang chủ/danh mục/Mandelic tại 320, 375, 1.440 px khớp nội dung, nhãn, ảnh hover, liên kết, gallery và kích thước |
| Browser console/runtime trong đối chiếu | Không phát hiện lỗi |
| Bộ responsive toàn bộ sản phẩm | Đạt 1.846 kiểm tra trước khi dừng tại popup Mandelic 320 px; xem hạn chế bên dưới |
| PHP lint toàn bộ PHP của hai package và exporter | Đạt |
| Hai ZIP: root, số file, CRC, SHA-256, byte khớp source, loại file riêng | Đạt |

Fixture cài mới: `C:/xampp/htdocs/fcc-release-qa-200-final`; nâng cấp: `C:/xampp/htdocs/fcc-release-qa-200-upgrade`. Không gửi form/đơn/email thật. Fixture cuối cài mới không phát sinh debug log. Kiểm tra ZIP bằng giải nén/cài/kích hoạt trên XAMPP riêng; chưa thử upload qua wp-admin trên hosting.

Hồ sơ: [packages](qa/package-report.json), [defaults](qa/defaults.txt), [CMS nguồn](qa/source-snapshot.txt), [SEO](qa/seo-fixture.txt), [dependencies](qa/dependencies.txt), [upgrade](qa/upgrade.txt), [đối chiếu browser](qa/smoke/report.json), [responsive](qa/browser/report.json). Chạy lại kiểm tra archive bằng `C:/xampp/php/php.exe release/v2.0.0/qa/verify-packages.php` từ root dự án.

## Hạn chế giữ nguyên theo hiện trạng

**Popup gallery Mandelic bị cắt phần trái của ảnh, tiêu đề và nút trước tại 320–375 px khi gallery có 5 ảnh.** Đã xác nhận cả site làm việc lẫn site cài từ ZIP đều có cùng hiện tượng; desktop 1.440 px hiển thị trong khung. Không tự sửa CSS/giao diện vì yêu cầu là chốt code/data hiện tại. Bộ responsive chưa hoàn tất toàn bộ do dừng tại kiểm tra này; không coi đây là kết quả responsive đạt hoàn toàn.

Chưa triển khai hosting, xác nhận domain public, kiểm tra gửi mail thật hoặc chạy Google Search Console/Rich Results trên domain public. Local/staging tiếp tục noindex; trang Cảm ơn luôn noindex.
