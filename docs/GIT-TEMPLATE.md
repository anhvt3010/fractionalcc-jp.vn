# Đưa base mẫu lên Git và dùng cho dự án mới

> Workflow hiện tại (12/10/2026) dùng XAMPP; xem [SETUP.md](SETUP.md). Phần Docker bên dưới chỉ là tham khảo khi được yêu cầu dùng lại. Trước khi commit, kiểm tra luật ignore thực sự có hiệu lực để loại secrets/config local/runtime; không suy ra từ mô tả lịch sử.

## Code nào đi theo base?

Base này lưu snapshot WordPress, Kadence, ACF, các theme/plugin đang có, bundled vendor, child theme, custom plugin, Docker, scripts, skill và tài liệu. `.gitignore` không còn chặn toàn bộ root hoặc chỉ cho phép vài folder. Khi thêm custom theme/plugin mới, Git tự nhận diện chúng.

Theo dõi core/dependencies không đồng nghĩa sửa trực tiếp chúng. Giao diện vẫn sửa trong child theme, nghiệp vụ trong custom plugin. Khi cập nhật dependency, ghi phiên bản và kiểm tra tương thích.

Không đưa `.env`, `wp-config.php`, uploads/cache, database dump, backups, logs hoặc cấu hình IDE cá nhân lên Git. `.env.example` và `wp-config-sample.php` được giữ. Không ignore `vendor/` toàn cục vì ACF có vendor được đóng gói cùng plugin.

## Commit base lần đầu

Git chưa được khởi tạo hoặc push tự động. Chạy tại root khi bạn sẵn sàng:

```powershell
git init
git branch -M main
git add --dry-run .
git add .
git diff --cached --stat
git status --short
git commit -m "Initialize WordPress starter"
```

Trước commit, kiểm tra không có `.env`, `wp-config.php` hoặc dữ liệu thật. Tạo repository remote của bạn rồi chạy, thay URL mẫu:

```powershell
git remote add origin <url-repository-base>
git push -u origin main
```

## Bắt đầu dự án riêng từ base

```powershell
git clone <url-repository-base> <thu-muc-du-an-moi>
cd <thu-muc-du-an-moi>
git remote set-url origin <url-repository-du-an-moi>
./scripts/init-local-env.ps1
```

Lệnh đổi remote giúp các commit tiếp theo được push tới repository dự án mới. Mỗi dự án có `.env`, credentials và nội dung riêng; đổi tên/prefix theo thương hiệu trước khi thêm nghiệp vụ.

**Nếu chạy nhiều dự án Docker đồng thời:** đổi `name:` trong `docker-compose.yml` thành tên riêng và `WORDPRESS_PORT` trong `.env` thành cổng chưa dùng. Compose project name quyết định tên network/volumes; dùng tên khác để không dùng chung database với base hoặc dự án khác. Thực hiện trước lần khởi động đầu tiên.

Sau đó làm theo [DOCKER.md](DOCKER.md). Dependencies đã nằm trong snapshot; script restore dùng để kiểm tra/khôi phục khi cần. Database không nằm trong Git: script setup tạo WordPress, tài khoản local và trang mẫu mới. XAMPP cần tạo `wp-config.php` riêng từ mẫu nếu dùng thay Docker.

WordPress core snapshot trên host hiện là 7.1.3; Docker vẫn dùng image đã khóa digest với WordPress 7.1.2. Nếu muốn chuẩn hóa cùng phiên bản, thực hiện một thay đổi version có kiểm thử riêng; tác vụ sửa ignore không nâng core hay image.
