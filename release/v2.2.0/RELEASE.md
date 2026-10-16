# Fractional CC 2.2.0 — Chi tiết combo và thư viện ảnh

Ngày 16/10/2026. Runtime làm việc: XAMPP `C:/xampp/htdocs/fractionalcc-jp.vn`, PHP 8.0.30, Project Child/Kadence, Project Core và ACF active. Chưa triển khai hosting.

## Thay đổi

- Combo có URL riêng `/combo/{slug}/`, dùng chung template chi tiết, CSS, thumbnail, popup phóng to và tương tác của sản phẩm. Nút mua/header/form chọn combo đang xem.
- Các thẻ combo ở trang chủ, danh mục, sản phẩm liên quan và gợi ý chăm sóc dẫn tới trang chi tiết. Trang combo hiển thị nhu cầu phù hợp, sản phẩm bên trong, hướng dẫn/quà tặng và thẻ từng sản phẩm; hồ sơ riêng nằm tại trang của từng sản phẩm.
- Trong **Nội dung website → Combo → Sửa**, khung **Ảnh sản phẩm / combo – đại diện & thứ tự** chọn tối đa 50 ảnh JPG/PNG/WebP, đại diện, hover và thứ tự. Hai trường ảnh ACF cũ được thay bằng khung quản lý chung, giữ nguyên schema/dữ liệu để hỗ trợ cập nhật và xuất defaults.
- Giữ ảnh đại diện combo C–CX4; thêm đúng 3 ảnh người dùng gửi dưới dạng WebP, tổng 4 thumbnail. Defaults chỉ đổi gallery combo, 3 Media mới và version; các nội dung khác kế thừa 2.1.0, tổng 74 Media. Không chép thêm câu chữ từ hình vào nội dung sản phẩm.
- Dùng chung cơ chế SEO hiện có, thêm title/description/canonical/ảnh chia sẻ cho trang combo. Local tiếp tục noindex.

## Hai gói cài đặt

| Gói | SHA-256 |
| --- | --- |
| [Project Core](project-core-v2.2.0.zip) | `798ea1a2a78ebe60359fe8900fba515738a061cc4347844330435fdc51cb7e9c` |
| [Project Child](project-child-v2.2.0.zip) | `4f8809051e6203c6ad9a125021556b8d7fe50b14a021bc07902045588dc1c266` |

Root ZIP là `project-core/` và `project-child/`. Không kèm WordPress, Kadence, ACF, config, tài khoản hay uploads. [Báo cáo CRC, hash và đối chiếu source](qa/packages.json): 28 file plugin, 300 file theme, tất cả byte khớp source.

## Cài mới / nâng cấp

1. Sao lưu code và database đích. Cài Kadence và ACF riêng.
2. Cài hoặc thay thế cả hai ZIP 2.2.0, kích hoạt Project Core và Project Child.
3. Mở quản trị bằng administrator để xử lý khởi tạo đang chờ, hoặc vào **Nội dung website → Bổ sung dữ liệu còn thiếu**. Không cần ACF Sync. Route combo tự cập nhật một lần.
4. Cài mới nhập đầy đủ defaults và gallery. Nâng cấp chỉ điền gallery combo cũ chưa có nội dung và chưa đánh dấu khởi tạo. Gallery/nội dung đã sửa, ảnh đại diện/hover đã chọn hoặc đã xóa được giữ. Nếu combo có nội dung tùy chỉnh trước đây, dùng khung ảnh để thêm các ảnh mới trong Media.
5. Kiểm tra trang combo, thumbnail, popup, các liên kết và nút mua trên hosting. Rollback dùng bản sao lưu đích và cặp ZIP trước; giữ dữ liệu phát sinh.

Site làm việc đã nhập 3 ảnh và lưu gallery theo yêu cầu. Ảnh đại diện ID 74 giữ nguyên, gallery gồm 74/129/130/131. Không chạy initializer trên database làm việc; seed vẫn 2.1.0. Bản sao trước thao tác nằm riêng trong thư mục tạm của máy, không đóng gói.

## Kiểm tra

Fixture `C:/xampp/htdocs/fcc-combo-qa-220`, database riêng `fcc_combo_qa_220`, WordPress/Kadence/ACF sao chép và plugin/theme giải nén từ ZIP.

| Kiểm tra | Kết quả |
| --- | --- |
| Khởi tạo mới defaults, nội dung, ảnh, quan hệ và chạy lại | 3.027 đạt; đủ 74 Media |
| Route combo, khung quản trị, lưu POST đăng nhập, nonce/quyền, thứ tự, đại diện/hover, nâng gallery rỗng và giữ edits/xóa, HTML/SEO/ảnh HTTP | [42 đạt](qa/combo-detail.txt) |
| Client đã minify: chọn đúng combo/product đang xem và khởi tạo gallery | [18 đạt](qa/combo-client.txt) |
| Hồi quy ảnh sản phẩm, thứ tự, quyền, xóa và chạy lại | [84 đạt](qa/product-media.txt) |
| Nội dung/giá danh mục 10 sản phẩm và combo | [305 đạt](qa/catalog.txt) |
| PHP lint plugin/theme và syntax JavaScript source/minify | Đạt |
| Site làm việc: trang chủ, danh mục, vấn đề da, sản phẩm, chi tiết combo | HTTP 200, không phát hiện warning/fatal trong HTML |

Chạy lại: đặt `PROJECT_WORDPRESS_ROOT` tới fixture có marker `project_core_fcc_test_install`, rồi chạy `C:/xampp/php/php.exe -d extension=gd scripts/verify-fractional-combo-detail.php`. Kiểm tra client: `node scripts/verify-fractional-combo-detail-client.cjs`. Không chạy các kiểm tra có ghi dữ liệu trên site làm việc.

## Giới hạn

Công cụ trình duyệt không có kết nối Chrome/IAB nên chưa kiểm tra trực quan desktop/mobile, focus hoặc thao tác popup thực tế. Đã kiểm tra HTML, asset HTTP, mã client, POST quản trị và dùng lại giao diện/tương tác sản phẩm. Hạn chế popup Mandelic 320–375 px đã ghi ở 2.1.0 vẫn còn; không tuyên bố responsive đạt hoàn toàn. Chưa gửi đơn/email thật hoặc triển khai hosting.
