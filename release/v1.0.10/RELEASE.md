# Fractional CC 1.0.10 – Tích hợp SEO và bản đồ từ khóa

Ngày 15/10/2026. Tích hợp `seo.php`, cập nhật SEO cho 13 trang thương mại/nội dung hiện có và thêm hướng dẫn từ khóa trong quản trị. Giữ giao diện, URL, nội dung hiển thị và workflow đặt hàng. Chưa triển khai lên hosting.

## Hai gói cài đặt

| Gói | Số file | SHA-256 |
| --- | ---: | --- |
| [Project Core](project-core-v1.0.10.zip) | 27 | `adda7a8b24f93ff5d54c52e074f99bb1b7f904bb8922b83ce0b73742b0fe1a1e` |
| [Project Child](project-child-v1.0.10.zip) | 292 | `75e2b319e8c6953c8688b9980f055e3f178bfdeb54d78d6bebe277c598a17c8b` |

Đúng hai ZIP, root `project-core/` và `project-child/`; không chứa config, credentials, tài khoản, database dump, logs hoặc uploads. Cần WordPress, Kadence và ACF cài riêng. Header yêu cầu WordPress 6.3+, PHP 7.4+; dependencies có yêu cầu PHP riêng.

Đã kiểm tra thực tế tại **C:/xampp/htdocs/fractionalcc-jp.vn**, URL `http://localhost/fractionalcc-jp.vn`, PHP **8.0.30**, WordPress **7.1.3**, Project Child/Kadence và Project Core/ACF đang active. Không dùng môi trường D: được ghi trong hồ sơ 1.0.9 để suy trạng thái hiện tại.

## Thay đổi

- Child theme `inc/seo.php` quản lý title/description/canonical, Open Graph/Twitter và JSON-LD website/trang/sản phẩm/breadcrumb. Dùng cùng metadata và giá/ảnh trong CMS.
- Không tạo review, sao đánh giá, tồn kho, địa chỉ showroom hoặc claim điều trị. Không thêm CSS/JS frontend hoặc thay giao diện.
- Nhường metadata cho các SEO plugin phổ biến khi được phát hiện; kiểm thử filter chuyển quyền sở hữu, chưa cài plugin SEO bên thứ ba để thử tích hợp hoàn chỉnh.
- Giữ noindex local/staging và trang Cảm ơn; dùng sitemap native, loại trang Cảm ơn.
- **Nội dung website → SEO & từ khóa**: 7 nhóm, 62 truy vấn gồm vấn đề da, hoạt chất, nhu cầu sản phẩm, so sánh, mỹ phẩm xanh và mua hàng. Có trạng thái áp dụng/chờ biên tập cùng nguồn tham khảo. Không phải số liệu volume hoặc bảng xếp hạng xu hướng hiện tại.
- 26 title/description mới cho trang chủ, danh mục, vấn đề làn da và 10 sản phẩm. Defaults 1.0.10 chỉ thay các giá trị SEO này và version; toàn bộ phần còn lại của snapshot 1.0.9 giữ nguyên.

Xem [hướng dẫn và nguồn SEO](../../docs/SEO.md).

## Cài đặt / cập nhật

1. Sao lưu database, code và uploads riêng trên hosting; cài WordPress/Kadence/ACF tương thích.
2. Upload/thay Project Core và Project Child bằng hai ZIP cùng version; giữ Child/Kadence và ACF active.
3. Vào **Nội dung website → Bổ sung dữ liệu còn thiếu** để chạy initializer có quyền quản trị. Site mới nhận defaults; site cũ giữ nội dung và metadata đã có, kể cả đã sửa hoặc xóa. Không cần ACF Sync.
4. Trên site cũ, nếu muốn dùng bộ title/description mới cho trang đã có metadata, sửa trong ACF. Initializer không ghi đè các giá trị cũ. Giá trị thiếu trên sản phẩm được bổ sung từ defaults.
5. Xóa cache và kiểm tra nguồn HTML/sitemap trên domain đích. Giữ noindex ở staging; chỉ cho phép index production khi domain và nội dung đã được chốt.

Rollback bằng code và backup môi trường đích đã kiểm chứng; giữ metadata trước thay đổi để phục hồi nếu cần. Không nhập database thử nghiệm lên hosting.

## Kiểm tra

| Kiểm tra | Kết quả |
| --- | ---: |
| HTTP/SEO trên 15 trang site làm việc | 290 đạt |
| HTTP/SEO, metadata, quyền và seed upgrade trên fixture cài từ ZIP | 301 đạt |
| Cài mới defaults, Media, quan hệ và chạy lại giữ edits | 2.764 đạt |
| ZIP root, inventory, CRC, SHA-256, byte khớp source và loại file riêng | Đạt |
| PHP lint toàn bộ code hai gói và script SEO | Đạt |
| So sánh phần body trang chủ trước/sau cập nhật metadata | Không đổi |
| So sánh JSON defaults, loại 26 trường SEO và version | Không đổi |

Fixture riêng: `C:/xampp/htdocs/fcc-seo-qa-110`, database riêng và marker bảo vệ; chặn toàn bộ mail ra ngoài. Thử seed version 1.0.9 → 1.0.10 trên fixture giữ title đã sửa và description đã xóa; đây là kiểm tra đường nâng version dữ liệu, không phải toàn bộ luồng upload ZIP qua wp-admin của hosting.

Không gửi đơn/email thật. Không thay giá, ảnh, thiết lập URL/homepage/Reading hoặc seed version của site làm việc. Có backup 26 metadata trước cập nhật ngoài repository. Bộ test từng lỗi do chưa đặt quyền administrator trong harness trước khi thử initializer; đã sửa harness và chạy lại đạt. Không có PHP diagnostics trong HTML được kiểm tra.

Chưa chạy kiểm tra trực quan desktop/mobile mới vì không đổi markup/CSS frontend; body trang chủ và asset giữ nội dung tương đương. Chưa dùng Google Rich Results Test/Search Console trên domain public hoặc đo thứ hạng. QA: [package report](qa/package-report.json), [SEO local](qa/seo-local.txt), [SEO fixture](qa/seo-fixture.txt).
