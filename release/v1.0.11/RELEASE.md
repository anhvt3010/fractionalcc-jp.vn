# Fractional CC 1.0.11 – Bấm ảnh mở chi tiết sản phẩm

Ngày 15/10/2026. Đóng gói code hiện tại, gồm SEO 1.0.10 và liên kết trên ảnh sản phẩm ở trang chủ/danh sách. Core, Child và defaults cùng version **1.0.11**. Chưa triển khai hosting.

## Hai gói cài đặt

| Package | Số file | SHA-256 |
| --- | ---: | --- |
| [Project Core 1.0.11](project-core-v1.0.11.zip) | 27 | `d047d250c617041e232aef1a572b99f5fbd29d2d4c17800be77c0c23d7eefe83` |
| [Project Child 1.0.11](project-child-v1.0.11.zip) | 292 | `52d7bca4ee5341348bbcc3e4fb65e5e6758e1318de271c9cbd983db1f53bf3eb` |

Đúng hai ZIP, root `project-core/` và `project-child/`. Không chứa WordPress core, parent theme, ACF, config, credentials, database, logs hoặc uploads riêng. Cài WordPress/Kadence/ACF riêng. Header khai báo WordPress 6.3+, PHP 7.4+; phiên bản PHP cần đáp ứng yêu cầu của dependencies.

Runtime đã xác nhận tại C:/xampp/htdocs/fractionalcc-jp.vn, URL `http://localhost/fractionalcc-jp.vn`: PHP 8.0.30, WordPress 7.1.3, Kadence 1.5.2, ACF Free 6.8.10, Project Child và Project Core đang active. Xem [runtime](qa/runtime.json).

## Thay đổi so với 1.0.10

- Ảnh trên thẻ sản phẩm ở danh mục trang chủ và trang danh sách mở đúng permalink chi tiết, giống liên kết Xem chi tiết hiện có.
- Ảnh ở mục sản phẩm chủ lực có liên kết tương ứng; ảnh combo dùng đích xem combo hiện có.
- Dùng thẻ liên kết HTML, tên truy cập lấy từ tên sản phẩm và URL được escape. Giữ bố cục, kích thước, nội dung, hình ảnh, bộ lọc và nút mua.
- Không thêm CSS/JS hoặc dependency. Giữ toàn bộ SEO và defaults đã duyệt của 1.0.10; snapshot chỉ đổi version. Không xuất lại CMS, thay ảnh/giá/nội dung hoặc chạy initializer trên database làm việc.
- Script kiểm tra SEO đọc version của defaults thay vì cố định 1.0.10 để dùng lại cho các release sau.

## Cài mới / cập nhật hosting

1. Sao lưu riêng database, code và uploads trên môi trường đích.
2. Upload ZIP Core trong Plugin và ZIP Child trong Giao diện, chọn thay thế bản hiện tại; giữ ACF, Kadence và Project Child active.
3. Vào **Nội dung website → Bổ sung dữ liệu còn thiếu** để ghi nhận defaults 1.0.11. Site mới nhận bộ nội dung đã duyệt; site đang dùng giữ nội dung/metadata/ảnh/settings đã sửa hoặc đã xóa. Không cần ACF Sync.
4. Xóa cache hosting/CDN/trình duyệt. Bấm ảnh ở trang chủ, sản phẩm chủ lực, danh sách và combo để kiểm tra điều hướng; thử bộ lọc và nút mua theo workflow hiện tại.

Không cần nhập lại dữ liệu hoặc reset catalog. Metadata SEO đã có vẫn được giữ theo cơ chế của [1.0.10](../v1.0.10/RELEASE.md). Rollback bằng code và backup môi trường đích đã kiểm chứng; không sao chép database QA lên hosting.

## Kết quả kiểm tra

Kiểm tra bằng hai ZIP trên site/database riêng, có marker bảo vệ và chặn toàn bộ mail ra ngoài:

| Kiểm tra | Kết quả |
| --- | ---: |
| Cài mới defaults, Media, nội dung, quan hệ và chạy lại giữ edits | 2.764 đạt |
| SEO/HTTP trên 15 trang, escaping, quyền, giá và metadata | 301 đạt |
| Nâng ZIP 1.0.10 → 1.0.11 và chạy lại | Giữ nguyên 124 bản ghi cùng metadata và Reading |
| Title đã sửa và description đã xóa trên site nâng cấp | Giữ nguyên |
| Thiếu child theme khi khởi tạo | Bị từ chối trước khi ghi dữ liệu |
| Browser: trang chủ/danh sách tại 375 px và 1.440 px | 38 liên kết ảnh trỏ đúng, bấm ảnh mở chi tiết, kích thước giữ nguyên |
| Browser console/runtime và tràn ngang | Không phát hiện lỗi |
| PHP lint toàn bộ code trong hai ZIP và script SEO | Đạt |
| ZIP root, file count, CRC, SHA-256, byte khớp source, loại file riêng | Đạt |
| Defaults so với 1.0.10 | Chỉ đổi version |

Fixture cài mới: `C:/xampp/htdocs/fcc-release-qa-111`; fixture nâng cấp: `C:/xampp/htdocs/fcc-seo-qa-110`. Site cài mới không phát sinh debug log. Việc kiểm tra điều hướng không gửi form/đơn/email thật. Không thay theme/plugin activation, homepage, URL, Reading hoặc seed version trên site làm việc; seed làm việc vẫn 1.0.10.

QA: [packages](qa/package-report.json), [defaults](qa/defaults.txt), [SEO](qa/seo-fixture.txt), [upgrade](qa/upgrade.txt), [image links](qa/image-links.json). Chưa triển khai hosting hoặc chạy Google Search Console/Rich Results trên domain public. Kiểm tra ZIP thực hiện bằng giải nén và chạy trên XAMPP riêng, chưa thao tác upload qua wp-admin của hosting.
