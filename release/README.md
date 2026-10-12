# Quy trình build và release

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
- Thao tác khởi tạo qua admin phải có quyền phù hợp và nonce. Không tạo tài khoản với mật khẩu mặc định. Activation không tự thay active theme, homepage, menu hoặc dữ liệu đang có.
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
