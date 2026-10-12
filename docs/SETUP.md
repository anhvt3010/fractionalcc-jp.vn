# Setup và workflow XAMPP

> Cập nhật chuyển template: source `0.3.0` đã có child theme Fractional CC, mô hình CMS/ACF và initializer/build scripts. Đã khởi tạo dữ liệu và đặt homepage trên WordPress hiện tại theo yêu cầu dùng thông tin index và danh sách sản phẩm làm mặc định. Database vẫn dùng chung với thư mục `wordpress` cũ, có bản sao lưu riêng trước thao tác. Xem [FRACTIONAL-WORDPRESS.md](FRACTIONAL-WORDPRESS.md) cho trạng thái hiện tại; phần mô tả chưa triển khai bên dưới là mốc trước chuyển đổi.

## Hiện trạng và phạm vi

Ngày 12/10/2026, người dùng xác nhận XAMPP đã cài và dự án tại `C:\xampp\htdocs\fractionalcc-jp.vn` đã chạy được. Root là WordPress installation; code dự án nằm trực tiếp trong `wp-content/`.

**Tạm thời không dùng Docker.** Không yêu cầu Docker Desktop, container hoặc Compose để chạy, kiểm thử hay build. [DOCKER.md](DOCKER.md), [VERIFICATION.md](VERIFICATION.md) và các release notes cũ là hồ sơ trước đây, không chứng minh runtime hiện tại hoặc thay thế workflow này.

Trên đĩa hiện có `project-child` (khai báo parent `kadence`) và `project-core`. Khi phát triển, kiểm tra runtime để biết theme/plugin nào active, phiên bản PHP/dependencies và URL thực tế; không suy ra activation từ file hoặc ghi chú cũ. Tác vụ cập nhật quy tắc chưa thay đổi runtime, dữ liệu hoặc thiết kế.

## 1. Dùng site đang chạy

Dùng Apache và MySQL/MariaDB của XAMPP. URL hiện đã kiểm tra là `http://localhost/fractionalcc-jp.vn/`, quản trị tại `http://localhost/fractionalcc-jp.vn/wp-admin/`.

Ngày 12/10/2026 đã sửa lỗi 404 do cấu hình kế thừa `/wordpress/`. File cấu hình riêng `wp-config.php` khai báo `WP_HOME` và `WP_SITEURL` là `http://localhost/fractionalcc-jp.vn`; `.htaccess` dùng `RewriteBase /fractionalcc-jp.vn/` và rewrite tới `/fractionalcc-jp.vn/index.php`. Đây là cấu hình local; khi đổi môi trường cần dùng URL và đường dẫn tương ứng. URL trong database dùng chung chưa được sửa. Homepage hiện là trang Fractional CC ID `30`, không còn danh sách bài viết starter; thay đổi homepage/nội dung có ảnh hưởng database của site cũ.

Giữ `wp-config.php` và credentials riêng trên máy; không in nội dung cấu hình có secrets. Không tự đổi URL, database, tài khoản, homepage hoặc active theme/plugin. Sao lưu riêng trước thao tác migration hay thay đổi dữ liệu có rủi ro.

Kiểm tra PHP bằng:

```powershell
& 'C:\xampp\php\php.exe' -v
```

Dùng PHP hiện tại để kiểm tra tương thích và lint các file PHP custom bị thay đổi. PHP 8.2+ là mục tiêu khi có tác vụ nâng môi trường riêng; không tự nâng XAMPP trong tác vụ khác.

## 2. Kiểm tra trước khi code

Đọc [skill dự án](../.agents/skills/wordpress-ai-development/SKILL.md) và policy theo phạm vi. Xác định yêu cầu, nội dung và thiết kế đã duyệt; kiểm tra hooks, templates, prefix, helpers, ACF fields, Gutenberg và dependencies hiện có.

- Giao diện: `wp-content/themes/project-child/`.
- Nghiệp vụ, API, CPT, tích hợp và khởi tạo dữ liệu mặc định: `wp-content/plugins/project-core/`.
- Nội dung chỉnh sửa được: Gutenberg/ACF; field definitions theo owner của chúng.
- Không sửa core, parent theme hoặc plugin bên thứ ba.

Tái sử dụng component/helper sẵn có; chỉ thêm module, thư mục, dependency khi có chức năng thật. Xem [ARCHITECTURE.md](ARCHITECTURE.md).

## 3. Bảo mật, SEO, nội dung và UI

Thực hiện validate/sanitize input, kiểm tra capability/ownership, nonce khi cần, prepared SQL và escape output theo ngữ cảnh. Không dùng nonce thay quyền truy cập hoặc tắt bảo mật để sửa lỗi. Không đưa dữ liệu nhạy cảm vào frontend/log/release.

Tối ưu semantic HTML, heading, metadata, internal links, alt ảnh và assets trong thiết kế đã duyệt. Kiểm tra cơ chế SEO hiện có để không tạo trùng canonical, metadata, sitemap hoặc schema. Không bỏ chặn index có chủ đích ở local/staging.

Theo WordPress Coding Standards và style hiện có. Không thêm code thừa, lặp logic/markup/query/assets hoặc tầng kiến trúc chưa dùng. Chỉ dùng nội dung được cung cấp/duyệt; thiếu thông tin thì hỏi hoặc để trống. Không tự bịa dữ kiện hay dùng placeholder như nội dung thật. Không đổi thiết kế UI ngoài phạm vi đã được cho phép.

## 4. Git cho starter snapshot

Theo dõi WordPress core, themes, plugins và bundled vendor của starter, cùng custom code, scripts, skill và tài liệu. Không khôi phục allowlist root hoặc ignore `vendor/` toàn cục. Việc theo dõi dependency không cho phép sửa source của chúng.

Trước khi stage/commit, kiểm tra các loại trừ secrets, `wp-config.php`, `.env`, uploads/cache/runtime, dump thật và file tạm. Không mặc định file đã an toàn chỉ vì có `.gitignore`; kiểm tra luật đang có hiệu lực. Git chưa được khởi tạo tại root trong lần kiểm tra này. Không tự init, tạo remote hoặc push trong tác vụ cập nhật quy tắc.

Xem [GIT-TEMPLATE.md](GIT-TEMPLATE.md) cho hướng dẫn snapshot; phần Docker trong tài liệu đó chỉ áp dụng khi người dùng yêu cầu dùng lại.

## 5. Kiểm tra thay đổi trên XAMPP

Lint PHP custom bằng `C:\xampp\php\php.exe -l <file>`. Build assets nếu dự án có pipeline cần dùng; không thêm Node/Composer/CI chỉ để có thêm công cụ.

Kiểm tra frontend/admin và workflow bị ảnh hưởng, PHP warnings/fatal, lỗi console, thao tác được phép/bị từ chối và hiệu năng liên quan. Khi thay đổi UI đã được cho phép, kiểm tra desktop/mobile, keyboard/focus và khả năng sửa nội dung của khách hàng. Kiểm tra SEO liên quan và giữ nội dung/thiết kế đã duyệt. Ghi rõ kiểm tra chưa chạy; không dùng kết quả Docker cũ để xác nhận XAMPP hiện tại.

## 6. Build và bàn giao

Theo [release/README.md](../release/README.md): tạo `release/vX.Y.Z/` với hai ZIP plugin core và child theme. Dữ liệu mặc định đã duyệt phải đi kèm initializer có version, tạo được trên site mới và bảo toàn nội dung đã sửa khi chạy lại/cập nhật. ACF JSON/PHP định nghĩa field không thay thế giá trị nội dung mặc định.

Thử cài hai ZIP và dependencies trên site kiểm thử riêng trước bàn giao. Không dùng database XAMPP đang làm việc để thử reset/seed phá dữ liệu. Ghi phiên bản, cách cài/activate, khởi tạo defaults, kiểm thử và rollback; backup thật nằm ở nơi riêng. Hiện việc đóng gói và seed là yêu cầu cho các bản build tiếp theo, chưa được triển khai trong tác vụ cập nhật tài liệu này.
