# Kiến trúc code base

## Phân chia trách nhiệm

| Công việc | Vị trí |
| --- | --- |
| Layout, CSS, JS giao diện, template | `wp-content/themes/<project>-child/` |
| CPT, taxonomy, REST, cron, tích hợp, nghiệp vụ | `wp-content/plugins/<project>-core/` |
| Bài viết, trang đơn giản | Gutenberg |
| Hero, CTA, nội dung có cấu trúc | ACF / block / pattern; render trong child theme |
| Giá, checkout, workflow đơn hàng | WooCommerce hooks trong custom plugin |
| Môi trường local | XAMPP Apache/PHP + MySQL/MariaDB; Docker tạm thời không dùng |
| Hướng dẫn và quyết định kiến trúc | `docs/` |
| Hai ZIP plugin core + child theme và hồ sơ từng phiên bản | `release/vX.Y.Z/`; defaults/initializer nằm trong ZIP plugin |

## Cấu trúc mục tiêu

Root hiện tại là chính thư mục WordPress, nên dùng trực tiếp `wp-content/`. Cây dưới đây là mục tiêu, không phải danh sách thư mục đã tạo:

```text
fractionalcc-jp.vn/
├── AGENTS.md
├── README.md
├── .agents/skills/wordpress-ai-development/
├── docs/
├── release/vX.Y.Z/             # hai ZIP + RELEASE.md
├── .gitignore
└── wp-content/
    ├── themes/<project>-child/
    │   ├── style.css
    │   ├── functions.php
    │   ├── assets/css/
    │   ├── assets/js/
    │   ├── template-parts/
    │   ├── templates/
    │   └── acf-json/
    └── plugins/<project>-core/
        ├── <project>-core.php
        └── src/
```

Tên `<project>` phải được thay bằng slug dự án trước khi viết code. Chỉ tạo module và thư mục khi có chức năng sử dụng. `functions.php` chỉ bootstrap, enqueue và hook đơn giản; không chứa nghiệp vụ.

Field ACF phục vụ giao diện có thể nằm trong child theme; field mô hình nghiệp vụ nên thuộc plugin và cấu hình đường dẫn save/load JSON hoặc khai báo PHP. Tránh định nghĩa cùng field ở hai nơi. Schema field được version control, giá trị nội dung nằm trong database.

## Nguyên tắc vận hành

Không sửa core, parent theme hoặc plugin bên thứ ba. Không commit uploads, dữ liệu sản xuất hoặc secrets. Phải ghi lại phiên bản và cách khôi phục dependencies không nằm trong Git. Deploy code và đồng bộ database/media là hai việc riêng, không chép database local đè production.

Starter snapshot theo dõi core, parent themes, plugins bên thứ ba và bundled vendor; việc theo dõi không cho phép sửa source dependency. Dùng XAMPP tại `C:\xampp\htdocs\fractionalcc-jp.vn`; không tự chạy Docker hoặc chuyển môi trường. Build theo [release/README.md](../release/README.md), với hai ZIP cài được và dữ liệu mặc định đã duyệt, khởi tạo có version và không ghi đè nội dung đã sửa.

Theo WordPress Coding Standards, tái sử dụng code/component sẵn có, tránh code thừa và lặp logic/markup/query/assets. Kiểm tra quyền/nonce, validate/sanitize input, prepared SQL, escape output và giữ secrets riêng. Tối ưu SEO on-page trong UI đã duyệt, tránh metadata/schema trùng. Không tự bịa dữ kiện hoặc thay thiết kế UI ngoài phạm vi được cho phép.

Toàn bộ 43 nhóm quy tắc nằm trong [architecture-policy.md](../.agents/skills/wordpress-ai-development/references/architecture-policy.md).
