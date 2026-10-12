# Docker local — hướng dẫn cho người mới

> Cập nhật 12/10/2026: tạm thời không dùng Docker. Workflow hiện tại là XAMPP tại `C:\xampp\htdocs\fractionalcc-jp.vn`, xem [SETUP.md](SETUP.md). Phần dưới là hướng dẫn môi trường cũ; URL, đường dẫn và kết quả kiểm tra cũ không xác nhận runtime hiện tại. Chỉ dùng lại khi người dùng yêu cầu.

## Môi trường riêng

| Thành phần | Docker | XAMPP hiện tại |
| --- | --- | --- |
| Website | `http://localhost:8088` | `http://localhost/wordpress` |
| WordPress runtime/config | Named volume `project-wordpress-local_wordpress_data` | Root WordPress trên máy |
| Database | MariaDB container `db`, database `wordpress_local` | Database XAMPP hiện có |
| Database storage | Named volume `project-wordpress-local_db_data` | Dữ liệu MySQL/MariaDB XAMPP |
| PHP | 8.3 trong image Docker | 8.0.30 trên XAMPP |
| Nội dung và tài khoản | Khởi tạo mới | Nội dung/tài khoản cũ |
| Child theme/plugin custom | Bind mount code trong dự án | Cùng code trên đĩa |
| Uploads | Trong volume Docker | Uploads XAMPP |

Database, tài khoản và uploads độc lập. **Sửa code custom trong thư mục dự án sẽ ảnh hưởng cả hai môi trường**, vì dùng chung source. Docker không mount `wp-config.php` của XAMPP và không kết nối database XAMPP. Chưa migrate nội dung đã chỉnh trong XAMPP sang Docker; Docker tạo dữ liệu mẫu mới.

WordPress dùng Apache và PHP 8.3; MariaDB dùng dòng 11.4. Cả ba image WordPress/MariaDB/WP-CLI được khóa digest trong `.env.example` để khởi động lặp lại với cùng image. Cấu hình dựa trên [WordPress official image](https://hub.docker.com/_/wordpress), [MariaDB official image](https://hub.docker.com/_/mariadb) và [Compose healthcheck dependency](https://docs.docker.com/compose/how-tos/startup-order/).

Runtime đã chạy thử: **WordPress 7.1.2, PHP 8.3.35, MariaDB 11.4.13**. XAMPP vẫn giữ WordPress 7.1.3/PHP 8.0.30. Đã kiểm tra containers healthy, trang mẫu HTTP 200, tài khoản local hợp lệ, restart giữ nội dung ACF và script chạy lại không tạo trùng sample.

Website chỉ bind `127.0.0.1:8088`; database không publish port ra máy host. Cổng 8080 đã có chương trình khác sử dụng nên chọn 8088. Đây là cấu hình local, không dùng trực tiếp cho production.

## Mở website đã setup

1. Mở Docker Desktop, chờ Engine chạy với Linux containers.
2. Trong terminal của PhpStorm/PowerShell, chuyển tới `C:\xampp\htdocs\wordpress`.
3. Chạy:

```powershell
docker compose up -d --wait
```

Mở [website Docker](http://localhost:8088) hoặc [admin Docker](http://localhost:8088/wp-admin/).

Tài khoản Docker mặc định là **localadmin**. Mở file **`.env` trên máy bạn**, tìm dòng **`WP_ADMIN_PASSWORD`** để lấy mật khẩu. Không chia sẻ/commit file này. `admin@example.test` là địa chỉ mẫu cho môi trường local; không phải hộp thư thật. Có thể đổi thông tin tài khoản trong admin khi cần.

Sau khi đăng nhập, chọn **Nội dung website → Sửa trang mẫu**. Link mẫu: `http://localhost:8088/gioi-thieu-mau/`. ID trang và user trong Docker khác XAMPP, nên dùng menu để mở đúng trang. Chưa đặt mẫu làm homepage; muốn đổi, dùng **Cài đặt → Đọc**.

## Setup từ checkout mới

Cần Docker Desktop và PowerShell. Từ root dự án:

```powershell
./scripts/init-local-env.ps1
./scripts/restore-dependencies.ps1
docker compose config --quiet
docker compose up -d --wait
docker compose run --rm cli /opt/project/setup-local.sh
```

`init-local-env.ps1` sinh ba mật khẩu ngẫu nhiên riêng trong `.env`, không ghi đè file đang có. Dependency restore lấy Kadence 1.5.2 và ACF miễn phí 6.8.10 từ WordPress.org; không ghi đè dependency đang có và dừng nếu sai version/layout. Giữ các phiên bản này cho lần phục hồi hiện tại; review nâng cấp theo quy trình staging.

Script setup cài WordPress mới nếu chưa có, không gửi email cài đặt, activate ACF/Project Core/Project Child, cấu hình permalink nếu còn trống rồi tạo trang mẫu. Chạy lại không đổi tài khoản/password hoặc ghi đè nội dung mẫu đã chỉnh. Script có thể activate lại các dependency của dự án.

`.env.example` là mẫu được version control; `.env` thật bị Git ignore. Thay `.env` **không tự thay mật khẩu đã lưu trong database hoặc tài khoản WordPress**. Không đổi các password của database đang có chỉ bằng cách sửa file rồi restart; cần quy trình đổi mật khẩu tương ứng.

## Các lệnh thường dùng

```powershell
# Trạng thái và health của container
docker compose ps

# Dừng, vẫn giữ dữ liệu
docker compose stop

# Khởi động lại
docker compose up -d --wait

# Restart dịch vụ
docker compose restart

# Log gần nhất
docker compose logs --tail=80 wordpress db

# Kiểm tra phiên bản và plugin
docker compose run --rm --entrypoint wp cli core version
docker compose run --rm --entrypoint wp cli plugin list
```

`docker compose down` xóa container/network nhưng giữ named volumes. **Không thêm `-v` nếu muốn giữ dữ liệu**; tùy chọn này xóa volume database và uploads/runtime Docker. Không dùng xóa volume như cách sửa lỗi thông thường.

## Source và dependency

- Child theme và custom plugin được mount vào runtime; chỉnh code bằng PhpStorm như hiện tại.
- Kadence/ACF mount read-only để tránh chỉnh hoặc cập nhật dependency từ admin Docker làm thay đổi source XAMPP. Review/cập nhật dependency trên host theo version trước khi restart và kiểm thử.
- Core, runtime config và uploads Docker ở named volume; không đưa vào Git.
- Không thêm phpMyAdmin/Redis/SMTP khi chưa có nhu cầu. DB tools về sau có thể dùng `docker compose exec db mariadb` với thông tin local phù hợp.
- Git chưa được khởi tạo. Repository này là base mẫu, nên `.gitignore` cho phép theo dõi WordPress core trên host, themes, plugins và bundled vendor. `.env`, `wp-config.php`, uploads/cache/dumps và file tạm vẫn bị loại trừ. Runtime core/config trong named volume Docker độc lập với core snapshot trên host.

## Khi có lỗi

- `Cannot connect`/named pipe: mở Docker Desktop, kiểm tra `docker info`.
- Port bị chiếm: đổi `WORDPRESS_PORT` trong `.env`, chạy `docker compose up -d --wait`. URL runtime sẽ theo port mới; link tuyệt đối đã lưu trong nội dung có thể cần cập nhật riêng bằng công cụ hỗ trợ serialized data.
- Missing bind source: chạy restore dependencies, kiểm tra custom source. Compose không tự tạo thư mục thiếu để tránh che giấu lỗi hoặc lồng plugin.
- Database unhealthy: xem log, kiểm tra password/config đã dùng khi tạo volume; không xóa volume trước khi backup.
- Lỗi template/plugin: xem `wp-content/debug.log` trong runtime Docker hoặc log container; debug display đã tắt nên không lộ lỗi ra frontend.

Không in/share toàn bộ `docker compose config`, `.env`, hoặc `docker inspect` có environment vì có thể chứa passwords. Dùng `docker compose config --quiet` để kiểm tra cấu hình.
