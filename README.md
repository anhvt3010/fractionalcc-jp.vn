# Website Fractional CC

Website giới thiệu Fractional CC và tiếp nhận yêu cầu tư vấn/đặt hàng theo brief của Công ty TNHH BUI DANG-JP. [Hồ sơ thông tin website ban đầu](docs/WEBSITE-BRIEF.md) tổng hợp nội dung từ `template/fractional-cc`, danh mục 10 sản phẩm, giá có nguồn, tài nguyên thiết kế và các thông tin chờ xác nhận. Nội dung hồ sơ chưa được nhập vào WordPress trong lần thiết lập tài liệu này.

Dự án theo kiến trúc developer-first: WordPress quản lý nội dung, child theme quản lý giao diện, custom plugin quản lý nghiệp vụ, Gutenberg/ACF cung cấp nội dung chỉnh sửa được.

## Môi trường hiện tại

Theo xác nhận của người dùng ngày 12/10/2026, XAMPP đã cài và dự án tại `C:\xampp\htdocs\fractionalcc-jp.vn` đã chạy được. **Tạm thời không dùng Docker** cho chạy local, phát triển, kiểm thử hoặc build.

Dùng Apache và MySQL/MariaDB của XAMPP. Mở site bằng URL XAMPP/virtual host đang cấu hình; nếu dùng ánh xạ thư mục mặc định thì URL là `http://localhost/fractionalcc-jp.vn/`, admin thêm `wp-admin/`. Root chính là WordPress installation: dùng trực tiếp `wp-content/...`.

Cấu hình và tài liệu Docker cũ được giữ để tham khảo khi có yêu cầu dùng lại. Không tự khởi động Docker hoặc chuyển database/môi trường.

## Quy trình build/release

Mỗi bản build tạo thư mục `release/vX.Y.Z/` với **hai file ZIP cài được qua WordPress Admin**:

- `project-core-vX.Y.Z.zip`: plugin core của dự án, kèm dữ liệu mặc định đã duyệt và cơ chế khởi tạo có version.
- `project-child-vX.Y.Z.zip`: child theme để cài và activate, kèm assets và field definitions thuộc giao diện.

Tên ZIP theo slug thực tế khi dự án đổi tên. Dữ liệu mặc định phải tạo được trên site mới, không tạo trùng hoặc ghi đè nội dung đã sửa khi chạy lại/cập nhật. Không đóng gói database thật, credentials hoặc uploads riêng tư. Parent theme và plugin bên thứ ba là dependency được ghi rõ trong hướng dẫn cài.

Chi tiết tại [release/README.md](release/README.md). Đây là quy trình yêu cầu; lần cập nhật tài liệu này chưa triển khai script build, cơ chế seed hoặc tạo ZIP.

## Quy tắc phát triển

- **Bảo mật:** kiểm tra quyền và nonce khi cần; validate/sanitize input, escape output theo ngữ cảnh, chuẩn bị SQL động; giữ secrets riêng và kiểm tra cả thao tác hợp lệ lẫn bị từ chối.
- **SEO on-page:** HTML semantic, heading đúng cấp, title/description chỉnh sửa được, liên kết và alt ảnh phù hợp, assets tối ưu; tái sử dụng cơ chế SEO hiện có, tránh metadata/schema trùng.
- **Code chuẩn, gọn, tái sử dụng:** theo WordPress Coding Standards và quy ước dự án; kiểm tra code sẵn có trước khi thêm, không để code thừa hoặc lặp logic/component/query/assets.
- **Nội dung có thật:** chỉ dùng thông tin người dùng cung cấp hoặc duyệt; không tự bịa dịch vụ, giá, số liệu, khách hàng, testimonial, liên hệ hay tuyên bố SEO. Nội dung thiếu cần hỏi hoặc để trống; placeholder phải được cho phép và ghi rõ.
- **Giữ thiết kế UI:** không tự đổi layout, màu, font, khoảng cách, hình ảnh hoặc tương tác khi chưa được cho phép. Tối ưu SEO/bảo mật không mặc nhiên cho phép đổi thiết kế.
- **Giữ kiến trúc:** không sửa WordPress core, parent theme, plugin bên thứ ba; giao diện ở child theme, nghiệp vụ ở project plugin, nội dung ở Gutenberg/ACF.

## Tài liệu

- [Thông tin website và nội dung ban đầu](docs/WEBSITE-BRIEF.md)
- [Workflow XAMPP và setup](docs/SETUP.md)
- [Kiến trúc và quy ước](docs/ARCHITECTURE.md)
- [Skill của dự án](.agents/skills/wordpress-ai-development/SKILL.md)
- [Cách sử dụng skill](docs/SKILL-USAGE.md)
- [Quy trình release](release/README.md)
- [Đưa base mẫu lên Git](docs/GIT-TEMPLATE.md)
- [Kadence child theme](docs/CHILD-THEME.md)
- [Thực hành với trang mẫu](docs/FIRST-PAGE.md)
- [Danh mục tài liệu](docs/README.md)
- [Docker — tham khảo cũ, tạm thời không áp dụng](docs/DOCKER.md)
- [Kết quả kiểm tra trước đây](docs/VERIFICATION.md)
