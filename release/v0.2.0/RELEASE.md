# Local setup v0.2.0 — Docker với database riêng

Triển khai local; chưa có Git commit/tag và chưa deploy production.

## Kết quả

- Website Docker: `http://localhost:8088`; admin: `/wp-admin/`; sample: `/gioi-thieu-mau/`.
- Runtime đã kiểm tra: WordPress 7.1.2, PHP 8.3.35, MariaDB 11.4.13, Kadence 1.5.2, ACF 6.8.10, Project Child/Project Core 0.1.0.
- Database Docker `wordpress_local` trên service `db:3306`, named volume `project-wordpress-local_db_data`; không publish database port.
- WordPress runtime/config/uploads ở `project-wordpress-local_wordpress_data`; không mount `wp-config.php` XAMPP.
- Custom code bind mount dùng chung source với XAMPP; dữ liệu, uploads và tài khoản độc lập. Không migrate nội dung XAMPP.
- Local admin `localadmin`, password sinh ngẫu nhiên trong `.env` bị Git ignore; không ghi password vào tài liệu này.

## Files và initialization

Thêm `docker-compose.yml`, `.env.example`, `.gitignore`, `.gitattributes`, `docker/setup-local.sh`, `scripts/init-local-env.ps1`, `scripts/restore-dependencies.ps1` và `docs/DOCKER.md`. Điều chỉnh script tạo sample để nhận runtime root từ environment.

Image WordPress/PHP, MariaDB và WP-CLI khóa digest trong `.env.example`. Healthcheck database được dùng trước khi khởi động WordPress. Script setup cài site mới, activate dependencies/theme, tạo sample và giữ dữ liệu/tài khoản khi chạy lại.

## Kiểm tra

- Compose config cả profile tools hợp lệ; Docker Engine Linux chạy được.
- WordPress và database healthy; frontend/sample/CSS kiểm tra HTTP.
- Password admin khớp với password được sinh, không in giá trị ra output.
- Restart containers giữ đúng sample ID 4, ACF content và một bản sample duy nhất.
- Setup chạy lại không ghi đè dữ liệu, không tạo sample trùng.
- XAMPP sample vẫn HTTP 200.
- Ignore rules bảo vệ `.env`, core, uploads và third-party dependencies; custom code/config template vẫn được phép theo dõi.
- Không có browser kết nối cho nghiệm thu trực quan; tác vụ này không thay đổi bố cục trang mẫu.

## Vận hành và rollback

Xem [DOCKER.md](../../docs/DOCKER.md) cho start/stop, khôi phục dependencies và tài khoản local. Dừng Docker bằng `docker compose stop` để giữ volumes. Nếu quay lại XAMPP, dùng URL XAMPP; không cần khôi phục database vì không sửa nó trong setup Docker.

Không dùng `docker compose down -v` nếu muốn giữ dữ liệu Docker. Trước thay đổi database/image lớn phải backup/restore và kiểm thử tương thích. Đây là local setup, không phải cấu hình production.
