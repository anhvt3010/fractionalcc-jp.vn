# Fractional CC 3.0.0

Ngày 16/10/2026. Người dùng chốt toàn bộ nội dung hiện tại và yêu cầu phiên bản lớn để cập nhật hosting. Core, Child và defaults cùng **3.0.0**. Snapshot được xuất lại từ CMS đang chạy tại `C:/xampp/htdocs/fractionalcc-jp.vn`, giữ giao diện đã duyệt.

## Hai gói cài đặt

| Gói | Dung lượng | SHA-256 |
| --- | --- | --- |
| [Project Core 3.0.0](project-core-v3.0.0.zip) | 142.396 byte | `a3c3af4387b76003ea9e54fc61091a012bacc15d7288ad0c83dd5cdf246c4c62` |
| [Project Child 3.0.0](project-child-v3.0.0.zip) | 29.171.196 byte | `36b800c774dea46eeafbe1e0118cf90e911bed338482f447b1749ac13f9b5295` |

Đúng hai ZIP, root `project-core/` và `project-child/`; lần lượt 28 và 305 file. Core gồm defaults và initializer; child theme gồm giao diện, schema và tài nguyên công khai. Không kèm WordPress, Kadence, ACF, config, credentials, tài khoản, đơn hàng, database, uploads hoặc QA. [Đối chiếu CRC/SHA-256/toàn bộ byte với source](qa/packages.json).

## Nội dung và thay đổi đã chốt

- 6 bản ghi nội dung với **1.104 giá trị field**, **10 sản phẩm**, **1 combo**, **35 hoạt chất**, **75 ảnh Media**; kèm giá/nội dung hiện tại, ảnh đại diện/hover/gallery, alt/caption, SEO, liên hệ, lựa chọn chủ lực/danh mục/phác đồ và 8 thiết lập công khai.
- Giữ các cập nhật trang chủ hiện tại: ảnh hover thẻ chủ lực; khối vấn đề làn da lấy ảnh và sản phẩm từ CMS; nội dung giới thiệu, đánh giá, liên hệ và footer. Nút “Video Triển Lãm 2025” riêng đã bỏ; nút video trên ảnh thương hiệu và thẻ đánh giá vẫn hoạt động.
- Hai popup video được quản lý tại **Nội dung website → Trang chủ → Video popup – thương hiệu và đánh giá**. Chọn/tải video Media hoặc nhập URL MP4/WebM/OGV, YouTube, Vimeo, Google Drive công khai cho phép nhúng. File được ưu tiên; bỏ chọn file để dùng URL. Hiện chưa chọn file upload, hai video dùng URL Drive cũ; không tải video bên ngoài vào ZIP.
- Popup phát file trực tiếp bằng video HTML, dùng iframe cho nhà cung cấp; đóng popup sẽ dừng và gỡ trình phát. Có tên trình phát, thao tác bàn phím và trả focus về nút mở.
- Quà tặng combo có công tắc bật/tắt và dùng một nguồn giữa các vị trí hiển thị. Giữ câu quà tặng hiện tại trong CMS ở lần chốt này: “Chọn 1 trong 2 quà tặng trong ảnh. Xác nhận khi đặt hàng”.
- Sửa nhãn/hướng dẫn tiếng Việt bị thay bằng dấu `?`; dọn CSS của nút đã bỏ. Bổ sung xuất/khởi tạo video bằng đường dẫn và SHA-256 để ánh xạ sang ID Media của site đích.

Initializer có version, chỉ bổ sung phần thiếu, tái sử dụng source key và giữ các chỉnh sửa/giá trị đã xóa. Snapshot mới là defaults cho cài mới; **update ZIP không ghi đè toàn bộ nội dung đang lưu trên hosting**.

## Cập nhật hosting

1. Sao lưu database, uploads và code đích; giữ cặp ZIP trước để khôi phục.
2. Giữ Kadence và ACF; upload/thay thế **cả hai ZIP 3.0.0**, duy trì Project Core và Project Child hoạt động. Theme khoảng 29,2 MB; hosting cần cho phép dung lượng upload tương ứng hoặc dùng SFTP để thay đúng thư mục.
3. Mở admin bằng administrator. Site mới khởi tạo khi đủ dependencies; site đã có seed không tự đặt lại trang chủ/nội dung. Khi cần dùng **Nội dung website → Bổ sung dữ liệu còn thiếu**. Không cần ACF Sync.
4. Kiểm tra Trang chủ, danh mục, vấn đề làn da, chi tiết sản phẩm/combo, ảnh hover/gallery, quà tặng, liên hệ và hai popup video. Sau khi sửa video trong Trang chủ, bấm **Cập nhật**. Upload Media chịu giới hạn của hosting.
5. Xóa cache hosting/CDN nếu có; kiểm tra domain, HTTPS, SMTP và indexing ở site đích. Local/staging tiếp tục noindex; Cảm ơn luôn noindex. SMTP secrets và cấu hình riêng không nằm trong ZIP.
6. Rollback bằng backup đích cùng code trước, bảo toàn dữ liệu/đơn phát sinh. Không dùng database QA để thay database hosting.

Nếu cần hosting khớp mọi câu chữ trong snapshot mới, cần chuyển các giá trị nội dung tương ứng bằng một đợt cập nhật dữ liệu riêng. Nút bổ sung phần thiếu bảo vệ nội dung đang lưu và không thực hiện việc ghi đè này.

## Kiểm tra thực tế

XAMPP PHP **8.0.30**, WordPress **7.1.3**, Kadence **1.5.2**, ACF **6.8.10**. Cài mới từ ZIP tại `C:/xampp/htdocs/fcc-release-qa-300`, database riêng `fcc_release_qa_300`; nâng từ ZIP 2.2.1 tại `C:/xampp/htdocs/fcc-release-qa-300-upgrade`, database riêng `fcc_release_qa_300_upgrade`. Các fixture có marker và chặn email thật.

| Kiểm tra | Kết quả |
| --- | --- |
| CMS nguồn đối chiếu snapshot/Media/gallery/hoạt chất/settings, chỉ đọc | [1.894 đạt](qa/export-parity.txt) |
| Cài mới, nội dung/defaults/quan hệ/Media và bảo toàn khi chạy lại | [3.057 đạt](qa/fresh-install.txt) |
| Nâng 2.2.1 → 3.0.0, chạy lại và giữ 131 bản ghi cũ cùng metadata/settings | [7.158 đạt](qa/upgrade.txt) |
| Khởi tạo lặp, activation callback, hàng đợi/khóa worker/quyền | [22 đạt, không trùng dữ liệu](qa/idempotency.json) |
| Thiếu ACF / child theme | [Chặn trước khi ghi posts/meta/seed](qa/dependencies.txt) |
| Chi tiết combo / ảnh sản phẩm / chọn combo chủ lực / editor combo | [42](qa/verify-fractional-combo-detail.txt) / [84](qa/verify-fractional-product-media.txt) / [36](qa/verify-fractional-featured-combo.txt) / [11 đạt](qa/native-combo-editor.txt) |
| Phác đồ / liên hệ / quản lý nội dung qua editor, quyền và nonce | [104](qa/verify-fractional-regimens.txt) / [291](qa/verify-fractional-contacts.txt) / [944 đạt](qa/verify-fractional-content-management.txt) |
| SEO 15 trang / ảnh, HTML, cache và bảo vệ đường dẫn | [290](qa/verify-fractional-seo.txt) / [153 đạt](qa/verify-fractional-performance.txt) |
| Bảo mật / form AJAX / snapshot đơn và biên nhận; email bị chặn | [82](qa/verify-fractional-security.txt) / [44](qa/verify-fractional-order-server.txt) / [181 đạt](qa/verify-fractional-order-receipt.txt) |
| Schema/quyền/URL/chọn nguồn video / nhập file thật, hash và ánh xạ ID | [49](qa/verify-fractional-popup-videos.txt) / [11 đạt](qa/video-import.txt) |
| Hai popup video desktop/mobile, MP4 phát thực tế và dừng khi đóng | [27 đạt](qa/video-browser.json) |
| ZIP đối chiếu nguồn trên 6 trang tại 320/375/1440 px | [55 đạt, không có lỗi script](qa/browser-parity.json) |
| PHP lint toàn bộ hai package, WebP/CSS/JS build, JS syntax và whitespace | Đạt |
| ZIP root/count/CRC/SHA-256, byte khớp source và loại config/local URLs | [Đạt](qa/packages.json) |

Đã xem ảnh chụp desktop/mobile của Trang chủ, chi tiết combo và video. Client đã minify đạt kiểm tra chọn sản phẩm/combo, gallery và trạng thái đồng ý gửi form: [kết quả](qa/client.txt). Hai assertion lịch sử được sửa theo chức năng hiện hành (combo có trang riêng, favicon dùng WebP), chạy lại đạt. Test chỉnh sửa dữ liệu chỉ chạy ở fixture, không gửi đơn/email thật.

CMS nguồn không chạy initializer hoặc sửa nội dung khi build. [Audit trước](qa/source-audit-before.json) và [sau](qa/source-audit-after.json) có cùng fingerprint `94feef59eb6bf5525b564197763e7ad4ab66b02116a81b33cbc046d24710ec10`; seed giữ **2.1.0**, code/defaults **3.0.0**. Không phát hiện trùng source key, đường dẫn/byte Media, ảnh thiếu hoặc lỗi ký tự trong nội dung đã lưu.

## Giới hạn

- Popup ảnh sản phẩm Mandelic còn tràn ảnh ở **320–375 px**, giống site nguồn và các bản trước; giữ giao diện đã duyệt. Hai popup video mới đã được kiểm tra riêng.
- Iframe nhà cung cấp được thay bằng fixture trong bài kiểm tra video. Quyền chia sẻ/nhúng và việc phát thực tế của từng URL Drive/YouTube/Vimeo cần kiểm tra khi gắn video và trên hosting đích.
- Chưa triển khai/kiểm tra hosting, chưa gửi SMTP thật. PHP được kiểm tra thực tế là 8.0.30.
