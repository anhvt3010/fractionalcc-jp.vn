# Fractional CC 1.0.0 – Release đầu tiên

Ngày 14/10/2026. Người dùng đã duyệt toàn bộ nội dung hiện tại làm dữ liệu mặc định. Các thư mục release trước đã xóa; thư mục version duy nhất là `release/v1.0.0/`. Đã áp dụng trên XAMPP `D:\xampp\htdocs\fractionalcc-jp.vn`, chưa deploy production hoặc tạo commit/tag.

## Nội dung

[Snapshot đã chốt](../../docs/APPROVED-DEFAULTS.md): 6 bản ghi nội dung, 1.083 giá trị trường, 10 sản phẩm, 1 combo, 35 hoạt chất, 30 ảnh và metadata Media; giữ nội dung/gallery Gutenberg, giá, SEO, liên hệ/chính sách, nhãn, bộ lọc, settings giao diện, thứ tự 2 mục chủ lực/6 mục danh mục trang chủ và 4 danh sách phác đồ. Không có tài khoản, secrets, đơn hàng, khách hàng hoặc uploads dư trong defaults/ZIP.

Core plugin quản lý domain, media mapping, phác đồ và initializer; child theme giữ giao diện, responsive, schema và asset đã chốt. Các tham chiếu dùng source key/token, được chuyển sang ID/URL tại site cài mới. Snapshot có version và hash media; initializer từ chối package thiếu/sai ảnh hoặc quan hệ trước khi ghi. Lần chạy lại bổ sung dữ liệu thiếu, không ghi đè edits, gallery, Media metadata, ảnh đã bỏ, lựa chọn phác đồ đã xóa hoặc settings đã thay.

## Cài đặt

1. Sao lưu riêng database/uploads/config môi trường đích. Cài WordPress, Kadence và ACF Free từ nguồn chính thức; hai ZIP không chứa các dependency này.
2. Cài/kích hoạt `project-core-v1.0.0.zip`, cài/kích hoạt `project-child-v1.0.0.zip`, giữ ACF hoạt động. Mỗi ZIP có đúng một root `project-core/` hoặc `project-child/`.
3. Mở một trang admin bằng administrator có quyền upload. Initializer tự chạy khi đủ dependency trên site mới; nếu cần dùng nút khởi tạo tại **Nội dung website**. Không cần ACF Sync. Dependency thiếu sẽ báo và chờ, không tự đổi active theme.
4. Site mới nhận homepage/nội dung/settings đã chốt. Site đã dùng giữ các chỉnh sửa hiện có. Kiểm tra giao diện, liên hệ, permalink và xóa cache nếu môi trường có cache. Cấu hình SMTP/URL/tài khoản riêng cho môi trường đích, không lấy credentials local.

Dependencies đã kiểm tra: WordPress **7.1.3**, XAMPP PHP **8.2.12**, Kadence **1.5.2**, ACF Free **6.8.10**. Trạng thái nội dung mặc định **approved**; localhost và môi trường không phải production vẫn noindex; trang Cảm ơn luôn noindex. Không thay setting indexing hoặc xác định domain production trong tác vụ này.

## Packages

```text
1116A92A6AAB7BE2E116BA5EA73C052B517DDE69CDC806360D6BCA4B88CE2937  project-core-v1.0.0.zip
FE1CF236E6629AE64D930378AF4219DBBCBC2C71891221227845B2464F887364  project-child-v1.0.0.zip
```

[Báo cáo file/root/hash/CRC](qa/package-report.json): plugin 19 file, theme 135 file, mọi entry khớp source và CRC hợp lệ. ZIP chỉ chứa custom runtime/defaults/schema/assets; không chứa core, parent theme, ACF, config, DB dump, logs, backup hoặc uploads riêng. Source là snapshot workspace đã kiểm tra, không có commit/tag mới.

## Kiểm tra

- Cài/kích hoạt từ hai ZIP trên WordPress/database riêng: **53 kiểm tra initializer** và **2.640 kiểm tra snapshot** đạt. Kiểm tra toàn bộ field, tiêu đề/slug/thứ tự, block gallery, byte/hash/alt/caption Media, combo, hoạt chất, phác đồ, settings và bảo toàn edits khi chạy lại. Package sai hash, quan hệ không tồn tại, setting ngoài allowlist và khởi tạo ẩn danh bị từ chối.
- **15 trang cài mới** khớp chữ hiển thị, byte và thứ tự ảnh của site đang dùng; tất cả trang/ảnh trả HTTP 200, HTML không có PHP warning. [Báo cáo đối chiếu](qa/public-comparison.json).
- **104 kiểm tra phác đồ**, **904 kiểm tra editor WordPress/ACF, ảnh, SEO, quyền và nonce** đạt trên fixture riêng.
- **1.033 kiểm tra Chromium** trên 10 sản phẩm × 13 viewport: 320, 375, 390, 480, 600, 768, 820, 991, 992, 1024, 1280, 1440, 1920px. Layout, gallery, tabs, modal chọn sản phẩm và console đạt. [Báo cáo](qa/product-responsive-report.json), [mobile Mandelic](qa/mandelic-375-top.png), [tablet](qa/mandelic-768-top.png), [desktop](qa/mandelic-1440-top.png).
- PHP runtime được lint khi build; cú pháp JS và kiểm tra luồng client đặt hàng đạt. Compiler mẫu cũ dừng trước khi ghi khi thấy approval đã chốt.
- Site đang dùng cập nhật version/trạng thái approved: mọi tiêu đề, nội dung, slug, thứ tự và metadata đã có khác trạng thái duyệt giữ nguyên; uploads vẫn 30 ảnh. Không gửi đơn/email/tin nhắn thật trong QA.

Browser dùng profile headless riêng. Chưa kiểm tra Safari/iOS hoặc thiết bị thật. Một assertion test ban đầu so sánh title qua filter HTML thay vì giá trị lưu thô đã được sửa; kiểm tra lại đạt. Các kiểm tra ghi dữ liệu chỉ chạy trên fixture có marker; không chạy script kiểm thử phá dữ liệu trên site làm việc. Hai database fixture đã xóa sau khi kiểm tra marker và site URL; credentials và thư mục fixture đã được dọn, giữ nguyên targets của junction Kadence/ACF.

## Khôi phục

Snapshot source/CMS trước chốt lưu riêng trong `first-release-*` dưới `C:\Users\anhvt\.codex\backups\fractionalcc-jp.vn\`, không đóng gói. Đây không thay thế full backup database/uploads/config trước deploy. Các ZIP release cũ đã xóa theo yêu cầu; không dùng đường dẫn v0.x để rollback.

Khi cần khôi phục, dùng backup riêng của môi trường đích hoặc snapshot code trước chốt, giữ database/uploads và edits phát sinh. Không reset toàn bộ initializer hoặc chép database fixture/local đè production. Trạng thái duyệt và seed version có thể đối chiếu snapshot private; không tự hạ dữ liệu đã được người dùng chốt.
