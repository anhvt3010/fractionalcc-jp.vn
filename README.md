# Website Fractional CC

Website giới thiệu Fractional CC và tiếp nhận yêu cầu tư vấn/đặt hàng theo brief của Công ty TNHH BUI DANG-JP. [Hồ sơ thông tin website ban đầu](docs/WEBSITE-BRIEF.md) tổng hợp nội dung từ `template/fractional-cc`, danh mục 10 sản phẩm, giá có nguồn, tài nguyên thiết kế và các thông tin chờ xác nhận.

Đã áp dụng [bản chuyển template sang child theme + ACF/CMS](docs/FRACTIONAL-WORDPRESS.md), source version hiện tại `0.3.1`, vào WordPress theo yêu cầu dùng thông tin trang index và danh sách sản phẩm làm mặc định. Trang chủ đã dùng template Fractional CC; CMS có 10 sản phẩm, 1 combo, 39 bản ghi hoạt chất và media. Tên website là **Fractional CC**, slogan lấy từ dữ liệu template. Chỉnh sửa tại [Nội dung website](http://localhost/fractionalcc-jp.vn/wp-admin/admin.php?page=project-website-content).

Database hiện vẫn dùng chung với thư mục `wordpress` cũ; việc khởi tạo và đặt homepage áp dụng vào database này, có bản sao lưu riêng trước thao tác. Các giá trị đã có được giữ nguyên. Site đang dùng chế độ local `noindex`; chưa nghiệm thu giao diện bằng trình duyệt hoặc hoàn thiện chức năng tiếp nhận đơn phía server.

Bản `0.3.1` bổ sung [sửa nội dung trong Tùy biến](http://localhost/fractionalcc-jp.vn/wp-admin/customize.php?autofocus%5Bpanel%5D=fcc_content): bút chì ở bản xem trước mở ô sửa, **Xuất bản** lưu vào cùng ACF/CMS. Text của nút/liên kết/biểu mẫu được sửa trong panel; ảnh và thông tin sản phẩm/giá vẫn quản lý trong CMS. Công cụ sửa chỉ có trong preview cho người có quyền, không đổi thiết kế khách xem. Xem [hướng dẫn](docs/FRACTIONAL-WORDPRESS.md#sửa-trong-màn-tùy-biến-031) và [release 0.3.1](release/v0.3.1/RELEASE.md).

Dự án theo kiến trúc developer-first: WordPress quản lý nội dung, child theme quản lý giao diện, custom plugin quản lý nghiệp vụ, Gutenberg/ACF cung cấp nội dung chỉnh sửa được.

## Môi trường hiện tại

Theo xác nhận của người dùng ngày 12/10/2026, XAMPP đã cài và dự án tại `C:\xampp\htdocs\fractionalcc-jp.vn` đã chạy được. **Tạm thời không dùng Docker** cho chạy local, phát triển, kiểm thử hoặc build.

Dùng Apache và MySQL/MariaDB của XAMPP. Site hiện truy cập tại [http://localhost/fractionalcc-jp.vn/](http://localhost/fractionalcc-jp.vn/), quản trị tại [wp-admin](http://localhost/fractionalcc-jp.vn/wp-admin/). Root chính là WordPress installation: dùng trực tiếp `wp-content/...`.

Ngày 12/10/2026 đã sửa lỗi 404 do URL và rewrite còn trỏ `/wordpress/`: cấu hình riêng `wp-config.php` dùng `WP_HOME`/`WP_SITEURL` theo thư mục hiện tại, `.htaccess` dùng `/fractionalcc-jp.vn/`. Sau đó đã khởi tạo nội dung mặc định và đặt trang chủ Fractional CC (page ID `30`). Năm loại trang và 29 tài nguyên local được kiểm tra HTTP 200, không thấy lỗi PHP trong response. URL lưu trong database chưa thay đổi. `wp-config.php` được giữ trên máy và bỏ theo dõi Git theo quy tắc bảo mật.

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
- [Bản chuyển WordPress, CMS và cách triển khai](docs/FRACTIONAL-WORDPRESS.md)
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
