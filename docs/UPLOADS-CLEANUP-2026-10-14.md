# Dọn ảnh dư trong uploads

Ngày 14/10/2026, theo yêu cầu người dùng xóa ảnh thừa/lặp trong `wp-content/uploads` của site XAMPP `D:\xampp\htdocs\fractionalcc-jp.vn`.

## Kết quả

| Chỉ số | Trước | Sau |
| --- | --- | --- |
| File ảnh trong uploads | 111 | 30 |
| Dung lượng | 43.037.644 byte | 14.880.124 byte |
| Bản ghi Media | 30 | 30 |

Đã xóa **81 file dư**, giảm **28.157.520 byte**, tương đương khoảng **28,2 MB / 26,9 MiB / 65,4%** dung lượng uploads. Đây là các file cũ không còn bản ghi attachment, bao gồm ảnh trùng byte và các ảnh mẫu cũ không được sử dụng. Không xóa theo hậu tố tên file đơn thuần: nhiều ảnh đang dùng có hậu tố `-1`, `-3`, `-4` và vẫn được giữ nguyên.

## Điều kiện và kiểm tra

- Đối chiếu tất cả attachment, `_wp_attached_file`, metadata kích thước/original và ảnh backup của WordPress. Không xóa file thuộc bất kỳ bản ghi Media nào, kể cả biến thể kích thước.
- Kiểm tra đường dẫn trong nội dung/revision, postmeta, options không phải transient, usermeta, termmeta, commentmeta, comments, code runtime child theme/core plugin và HTML của 18 trang public. Nhãn ACF chỉ chứa tên file không được coi là đường dẫn ảnh uploads; ảnh mặc định trong assets của theme không bị xóa.
- Trước khi xóa kiểm tra lại references trong database, đường dẫn thực nằm trong uploads, không phải symlink, đúng phần mở rộng ảnh và SHA-256 vẫn khớp inventory. Chỉ xóa từng file xác định qua WordPress `wp_delete_file_from_directory`; không xóa thư mục đệ quy.
- Sau khi xóa: 30 ảnh giữ nguyên byte/SHA-256; 30 bản ghi Media, metadata và source mapping giữ nguyên; HTML của cả 18 trang giống hoàn toàn trước khi dọn; cả 30 URL ảnh trả HTTP 200 với content type ảnh.

Không đổi PHP/CSS/JS, defaults, nội dung CMS hoặc lựa chọn phác đồ. Tác vụ chỉ dọn dữ liệu uploads thực tế, không cần tạo phiên bản plugin/theme mới.

## Sao lưu và khôi phục

Bản sao riêng nằm ngoài website/repo tại:

```text
C:\Users\anhvt\.codex\backups\fractionalcc-jp.vn\uploads-cleanup-20261014-012744\
```

`removed-upload-images.zip` chứa đúng 81 ảnh đã xóa với đường dẫn tương đối; CRC và SHA-256 của từng file giải nén đã kiểm tra trước khi xóa. `manifest.json` và `deleted.json` ghi danh sách/hash/dung lượng, `audit-before.json` lưu inventory/references, `verification.json` ghi kết quả kiểm tra. Backup chiếm dung lượng riêng trên ổ C, không tính trong dung lượng uploads sau dọn.

Nếu cần khôi phục, kiểm tra manifest, giải nén các file về đúng đường dẫn bên dưới `wp-content/uploads`, không ghi đè ảnh mới có cùng tên. Không cần phục hồi database vì không có bản ghi Media bị xóa hoặc sửa trong tác vụ này.
