# Quy trình build và release

> Bản `0.3.21` cải thiện phản hồi/validation trên mọi form đặt hàng, bỏ gửi lại tự động khi AJAX lỗi và ghi trạng thái gửi mail trong đơn. Xem [hồ sơ release](v0.3.21/RELEASE.md), gồm kiểm tra 24 form và giới hạn chưa xác nhận inbox/browser.

> Bản `0.3.17` cho phép chọn tối đa 6 sản phẩm/combo trong danh mục trang chủ, có thứ tự riêng và độc lập với 2 sản phẩm chủ lực. Xem [hồ sơ release](v0.3.17/RELEASE.md).

> Bản `0.3.15` sắp xếp **Thông tin chung** theo vị trí hiển thị, thêm nhãn/hướng dẫn và tìm kiếm trường; giữ dữ liệu cùng cách sửa text ngoài giao diện. Xem [hồ sơ release](v0.3.15/RELEASE.md) và giới hạn kiểm tra tương tác trình duyệt.

> Bản `0.3.13` đồng bộ header/menu trên mọi trang Fractional CC, giữ logo giữa và nút Mua ngay, dùng cùng năm nhãn chỉnh được qua CMS cho desktop/mobile. Xem [hồ sơ release](v0.3.13/RELEASE.md).

> Bản `0.3.7` sửa warning `Undefined array key "type"` từ schema ảnh feedback khai báo trùng/thiếu thuộc tính. Kiểm tra frontend, ACF/Customizer và cài mới đạt. Xem [hồ sơ release](v0.3.7/RELEASE.md).

> Bản `0.3.5` đã rà soát activation/initializer, sửa khóa bị kẹt, bảo toàn mục trong thùng rác, kiểm tra package trước nhập và đồng bộ ảnh defaults với nguồn Excel. Hai ZIP cài mới đạt 53 kiểm tra khởi tạo và 305 kiểm tra catalog. Xem [hồ sơ release](v0.3.5/RELEASE.md).

> Bản `0.3.4` sửa lựa chọn homepage khi khởi tạo lần đầu và đóng gói ảnh combo nền vàng do người dùng cung cấp. Site XAMPP hiện tại đã áp dụng. Xem [hồ sơ release](v0.3.4/RELEASE.md). Các mô tả phiên bản cũ bên dưới là lịch sử.

> Bản `0.3.3` đã cập nhật catalog từ Excel, bổ sung tự khởi tạo defaults trên site mới và kiểm tra cài hai ZIP trên database riêng. Xem [hồ sơ release](v0.3.3/RELEASE.md) và [ánh xạ dữ liệu](../docs/PRODUCT-DATA-EXCEL.md). Các mô tả phiên bản cũ bên dưới là lịch sử.

Dùng XAMPP cho workflow hiện tại; Docker tạm thời không áp dụng. Mỗi bản build tạo một thư mục version trong `release/`, chứa **đúng hai file ZIP cài được qua WordPress Admin**, cùng hồ sơ phát hành nếu cần.

Source Fractional CC `0.3.1` có compiler, initializer, công cụ sửa text trong Customizer và `scripts/build-release.ps1`. Defaults/homepage đã áp dụng trên XAMPP theo yêu cầu; database vẫn dùng chung với site cũ. Xem [trạng thái triển khai](../docs/FRACTIONAL-WORDPRESS.md); các mô tả “chưa triển khai script/seed” bên dưới là mốc trước chuyển đổi. Luồng tiếp nhận đơn phía server và nghiệm thu giao diện/public còn cần hoàn thiện; chưa là release production đã nghiệm thu.

## Cấu trúc phiên bản

```text
release/
├── README.md
├── RELEASE-TEMPLATE.md
└── vX.Y.Z/
    ├── project-core-vX.Y.Z.zip
    ├── project-child-vX.Y.Z.zip
    └── RELEASE.md
```

Tên `project-core` và `project-child` theo slug source hiện có; đổi theo slug thực tế khi dự án đổi tên. Không chuyển ZIP sang `release/artifacts/`. Hồ sơ `v0.1.0`, `v0.2.0` trước đây không có nghĩa đã có gói ZIP theo quy trình mới. Tác vụ cập nhật quy tắc chưa triển khai script build hoặc seed.

## Nội dung hai ZIP

| Gói | Thư mục cấp đầu trong ZIP | Nội dung |
| --- | --- | --- |
| Plugin core | `project-core/` | Plugin header/bootstrap, nghiệp vụ, field definitions thuộc plugin, dữ liệu mặc định đã duyệt và initializer có version |
| Child theme | `project-child/` | `style.css` với `Template` đúng parent, bootstrap, templates, assets và field definitions thuộc giao diện |

ZIP phải có một thư mục gốc đúng slug, không thêm lớp `wp-content/`, `plugins/`, `themes/` hoặc thư mục version bên trong. Bao gồm assets đã build và các file runtime cần thiết. Không gộp WordPress core, parent theme hay plugin bên thứ ba vào hai gói này; ghi rõ nguồn cài, phiên bản tương thích và license của dependencies.

Không đóng gói `wp-config.php`, `.env`, secrets, tài khoản/mật khẩu, database dump, dữ liệu khách hàng, uploads riêng tư, backup, logs, caches, `node_modules` hoặc công cụ dev không cần cho runtime. Chỉ đóng gói media mặc định đã được duyệt và có quyền sử dụng.

## Dữ liệu mặc định

- Đóng gói defaults có version cùng plugin core, bao gồm giá trị nội dung/cài đặt cần thiết và tham chiếu media đã duyệt. Field definitions của ACF JSON/PHP chỉ là schema, không chứa giá trị nội dung.
- Chỉ dùng nội dung người dùng cung cấp hoặc duyệt. Thiếu dữ kiện thì hỏi hoặc để trống; không tự tạo dịch vụ, giá, số liệu, testimonial, khách hàng, thông tin liên hệ hoặc tuyên bố quảng cáo. Placeholder phải được cho phép và ghi rõ.
- Initializer dùng WordPress APIs, kiểm tra dependencies, xác định dữ liệu bằng khóa ổn định và lưu version đã khởi tạo. Chạy lại không tạo trùng và không ghi đè trang, field, menu hoặc settings đã được sửa; migration nâng cấp phải mô tả riêng.
- Thao tác khởi tạo qua admin phải có quyền phù hợp và nonce. Không tạo tài khoản với mật khẩu mặc định. Theo yêu cầu người dùng, từ `0.3.4`, khởi tạo lần đầu đặt trang Fractional CC làm homepage nếu Reading vẫn là danh sách bài viết và chưa chọn trang chủ. Các lần chạy lại/update giữ lựa chọn Reading đã có. Activation không tự thay active theme, menu hoặc dữ liệu đang có.
- Ghi rõ cách chạy khởi tạo trong hồ sơ release. Nếu cần đặt homepage/menu, hướng dẫn thao tác cụ thể theo phạm vi được duyệt; không tự áp dụng vào site đang dùng.

## Quy trình build và xác minh

1. Chốt version và phạm vi; review custom code, dependencies, nội dung mặc định và thiết kế đã duyệt. Ghi commit/tag khi Git đã có; không bịa mã commit.
2. Lint PHP bằng XAMPP PHP, chạy build assets nếu cần và các kiểm tra phù hợp. Review validation/sanitization, quyền, nonce, SQL, escaping, secrets; kiểm tra SEO liên quan, code trùng/thừa và hiệu năng.
3. Tạo `release/vX.Y.Z/`, đóng gói hai ZIP từ source custom tương ứng. Ghi version plugin/theme thực tế và dependencies; không sửa source dependency để đóng gói.
4. Kiểm tra danh sách file và cấu trúc từng ZIP, loại bỏ dữ liệu riêng tư và bảo đảm file runtime/asset/defaults không thiếu.
5. Trên site kiểm thử riêng, cài dependencies, upload/activate plugin core, upload/activate child theme, rồi chạy khởi tạo defaults theo hướng dẫn của release. Kiểm tra dependency thiếu được xử lý rõ ràng.
6. Kiểm tra site mới nhận đủ defaults; sửa thử nội dung rồi chạy lại initializer/cập nhật để xác minh không trùng hoặc mất nội dung. Kiểm tra frontend/admin, editor, lỗi PHP/console và desktop/mobile nếu có UI trong phạm vi.
7. Copy [RELEASE-TEMPLATE.md](RELEASE-TEMPLATE.md) thành `vX.Y.Z/RELEASE.md`; ghi hai ZIP, checksum SHA-256, dependencies, cách cài/activate/seed, kết quả kiểm thử, giới hạn và rollback. Không đánh dấu đạt kiểm tra chưa chạy.

## Deploy và rollback

Sao lưu database/uploads/config vào nơi riêng và kiểm tra khả năng khôi phục trước deploy có rủi ro. Cài đúng hai ZIP và dependencies đã kiểm thử; xử lý migration/field sync/default initialization theo hồ sơ release, xóa cache liên quan và smoke-test frontend/admin.

Rollback về plugin/theme và dependencies đã biết tốt. Migration/seed dữ liệu không nhất thiết rollback bằng code; phải có hướng dẫn khôi phục hoặc forward-fix bảo toàn dữ liệu phát sinh. Không chép database local đè production. Ghi kết quả deploy, lỗi và kiểm tra sau rollback vào hồ sơ phiên bản.
