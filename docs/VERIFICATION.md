# Kiểm tra clone sạch và editor — 07/10/2026

## Kết quả

**Clone sạch: đạt. Editor ở tầng HTTP/server: 17 kiểm tra đạt.**

Chưa thực thi JavaScript Gutenberg trong trình duyệt, chưa kiểm tra console hoặc nghiệm thu trực quan desktop/mobile vì công cụ không có browser kết nối. Đây là giới hạn còn lại trước khi xác nhận toàn bộ trải nghiệm editor.

## Clone sạch đã được kiểm tra như thế nào?

1. Lấy 4.742 file theo đúng `.gitignore` tại thời điểm bắt đầu kiểm tra.
2. Copy source vào repository Git tạm, commit và chạy **git clone local thật** sang thư mục mới. Không khởi tạo Git, stage hoặc commit trong workspace gốc.
3. Xác nhận clone không có `.env`, `wp-config.php`, uploads hoặc database của môi trường trước.
4. Sinh `.env` và passwords mới; đổi Compose project thành `wordpress-starter-check-585efdda03`, port 8090 để tách network/volumes khỏi base.
5. Chạy Compose config, khởi động containers, chạy script setup trên database mới. Không phải restore database XAMPP hoặc database Docker base.
6. WordPress, Project Child, Project Core, ACF và trang mẫu được khởi tạo thành công. Sample ID 4, URL `http://localhost:8090/gioi-thieu-mau/`.

Snapshot commit tạm: `00b581dd375d71c5b7298db0c81fcd8c3c260558`. Repository remote của người dùng chưa có, nên việc clone dùng repository local tạm; chưa kiểm tra quyền truy cập/push/pull remote.

## Editor và media đã kiểm tra

Các kiểm tra dùng **đăng nhập WordPress thật**, cookie session, nonce từ HTML editor và endpoint WordPress/ACF. Không dùng ghi SQL trực tiếp hoặc bỏ qua nonce để mô phỏng save.

| Nhóm | Kết quả |
| --- | --- |
| Admin login và menu Nội dung website | Đạt |
| Link sửa dẫn tới sample trong database mới | Đạt |
| HTML editor có các field ACF và input assets | Đạt |
| REST nonce và quyền truy cập page endpoint | Đạt |
| Lưu block Gutenberg qua REST, hiển thị ngoài frontend | Đạt |
| REST update khi chưa đăng nhập | Bị từ chối đúng |
| Lưu title/email ACF qua form editor có nonce | Đạt |
| Tắt và bật lại phần Giới thiệu | Đạt |
| Nonce ACF sai | Không ghi giá trị field |
| Email sai | Không ghi giá trị field |
| Upload PNG bằng Media endpoint có quyền | Đạt |
| Chọn ảnh qua ACF, render ảnh và alt text | Đạt |
| Truy cập ảnh upload ngoài frontend | HTTP 200 |
| Response frontend và debug log | Không thấy PHP warning; không tạo debug log |

Đã restart containers và chạy lại setup: field ACF, block Gutenberg, ảnh upload vẫn tồn tại; script không ghi đè nội dung mẫu. Docker base vẫn ở trạng thái dừng như trước kiểm tra, không bị kích hoạt hoặc thay đổi nội dung.

## Tài nguyên kiểm tra

Thư mục tạm: `C:\Users\anhvt\AppData\Local\Temp\wordpress-clean-clone-639d250323a84597a9020512993495a1`.

- `snapshot/`: repository dùng để clone.
- `clone/`: bản clone có `.env` riêng, chỉ phục vụ kiểm tra.
- `editor-report.json`: kết quả HTTP, không chứa password/cookie/nonce.
- `.env` trong clone chứa credentials riêng; không copy lên Git.

Containers kiểm tra đã dừng sau khi hoàn tất. Giữ thư mục/volumes để có thể xem lại; không xóa dữ liệu Docker base. Nếu muốn mở lại bản kiểm tra:

```powershell
docker compose --project-directory 'C:\Users\anhvt\AppData\Local\Temp\wordpress-clean-clone-639d250323a84597a9020512993495a1\clone' up -d --wait
```

Tài khoản là `localadmin`, lấy password trong `.env` của **clone**. Nội dung clone có marker `EDITOR_CHECK_` và ảnh nhỏ phục vụ kiểm thử; không dùng làm website thực tế.

## Script kiểm tra có thể tái sử dụng

`scripts/verify-editor-http.mjs` dùng Node.js built-in, không có npm dependencies. Nó thay đổi nội dung và upload fixture trong site kiểm tra, nên chỉ cho chạy với Compose project có prefix `wordpress-starter-check-`, đồng thời từ chối `.env` của workspace gốc.

```powershell
node scripts/verify-editor-http.mjs '<duong-dan-clone>/.env' '<duong-dan-report.json>'
```

Node chỉ cần cho script kiểm tra này; WordPress/PHP runtime không phụ thuộc Node. Script không kiểm tra click/type, thực thi React/Gutenberg, console, keyboard, bố cục hoặc thao tác Media Library trên trình duyệt.

## Các bước còn lại

- Kết nối browser hoặc kiểm tra thủ công: sửa/lưu ACF, thêm block Gutenberg, mở Media Library, upload/chọn ảnh và xem frontend desktop/mobile.
- Kiểm tra console, focus/keyboard và giao diện khi field dài hoặc ảnh lớn.
- Khởi tạo Git repository thật và kiểm tra remote khi đã có URL.
- Đồng bộ phiên bản WordPress host/Docker qua một thay đổi riêng nếu chọn chuẩn hóa chúng.

Không cần thay `.gitignore` sau kiểm tra; nó đã cho phép clone code base và loại trừ dữ liệu/credentials local đúng yêu cầu.
