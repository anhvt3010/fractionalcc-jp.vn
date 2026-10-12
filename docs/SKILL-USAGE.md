# Sử dụng WordPress AI Development skill

## Vị trí

Bản nguồn trong dự án: `.agents/skills/wordpress-ai-development/`. Bản cài cá nhân mặc định trên máy này: `C:\Users\anhvt\.codex\skills\wordpress-ai-development\`. Nếu sử dụng `CODEX_HOME` riêng, vị trí cài là `<CODEX_HOME>/skills/wordpress-ai-development/`.

Skill gồm `SKILL.md`, metadata `agents/openai.yaml` và `references/architecture-policy.md` giữ đủ 43 nhóm chính sách. `AGENTS.md` tại root yêu cầu đọc skill trước khi phát triển trong repository này.

Quy tắc hiện tại của dự án: XAMPP đang chạy, tạm thời không dùng Docker; build hai ZIP plugin core và child theme trong `release/vX.Y.Z/`, kèm defaults đã duyệt; code bảo mật, SEO on-page và tái sử dụng; không bịa nội dung hoặc tự đổi UI. Bản trong dự án là nguồn áp dụng cho repository này, kể cả khi bản skill cá nhân còn quy tắc cũ. Lần cập nhật này chỉ sửa bản trong dự án, không thay skill dùng chung ở thư mục cá nhân.

## Gọi skill

Trong phiên mới có skill được nhận diện, dùng:

```text
$wordpress-ai-development Kiểm tra kiến trúc và setup nền tảng cho dự án này.
```

```text
$wordpress-ai-development Tạo Homepage với Hero và CTA chỉnh sửa bằng ACF,
render qua child theme. Tái sử dụng component hiện có và kiểm tra mobile.
```

Nếu skill chưa xuất hiện trong phiên hiện tại, mở phiên mới và kiểm tra danh sách skill. Có thể yêu cầu agent đọc trực tiếp `.agents/skills/wordpress-ai-development/SKILL.md`; bản nguồn luôn nằm trong dự án.

## Cài lại hoặc đồng bộ bản cá nhân

Sau khi cập nhật bản nguồn, kiểm tra thay đổi rồi chạy từ root repository bằng PowerShell:

```powershell
$skillRoot = if ($env:CODEX_HOME) { Join-Path $env:CODEX_HOME 'skills' } else { Join-Path $env:USERPROFILE '.codex/skills' }
$skillDestination = Join-Path $skillRoot 'wordpress-ai-development'
New-Item -ItemType Directory -Path $skillDestination -Force | Out-Null
Copy-Item -Path '.agents/skills/wordpress-ai-development/*' -Destination $skillDestination -Recurse -Force
```

Lệnh ghi đè các file cùng tên trong skill cá nhân; không tự xóa file cũ bị bỏ khỏi bản nguồn. Review riêng nếu một phiên bản mới loại bỏ tài nguyên. Giữ bản trong Git là nguồn chính để tránh hai bản có quy tắc khác nhau.

## Kiểm tra skill

Khi có Python và PyYAML, dùng validator của `skill-creator` theo đường dẫn cài thực tế. Trên máy hiện tại:

```powershell
python 'C:\Users\anhvt\.codex\skills\.system\skill-creator\scripts\quick_validate.py' '.agents/skills/wordpress-ai-development'
```

Lần cập nhật 12/10/2026 chưa chạy được validator Python vì máy chưa có Python executable sử dụng được. Đã kiểm tra bản dự án bằng PowerShell: frontmatter/name/description, liên kết tài liệu, UTF-8, code fences và đủ 43 mục policy đều đạt. Chưa chạy thử hành vi skill trong phiên Codex mới; bản skill cá nhân không được đồng bộ trong lần này.
