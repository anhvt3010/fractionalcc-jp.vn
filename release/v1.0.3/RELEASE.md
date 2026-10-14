# Fractional CC 1.0.3 – Ảnh trong email và hướng dẫn thư rác

Email đơn hàng và email thử SMTP sử dụng URL ảnh tuyệt đối thay cho CID. Mã dự án không đính kèm logo hoặc ảnh sản phẩm vào email. Giữ bố cục email hiện có; ảnh sản phẩm lấy từ snapshot lúc đặt hàng, không lấy lại ảnh danh mục đã thay đổi sau đó.

## Bộ cài đặt

| Package | Số file | SHA-256 |
| --- | ---: | --- |
| [Project Core 1.0.3](project-core-v1.0.3.zip) | 21 | `07b99774507b701de0fbca56a7db67281d980c6ac236ca8f114e7408221180c6` |
| [Project Child 1.0.3](project-child-v1.0.3.zip) | 172 | `41365dc3372768d447e6f79f3f47bbbfc4b40ccb1633064abaa1176bd51d4b36` |

Đúng hai ZIP, root tương ứng `project-core/` và `project-child/`. Dependencies: WordPress 6.3+, PHP 7.4+, Kadence và ACF (cài riêng). Dùng PHP tương thích với phiên bản dependencies thực tế. Child theme chỉ tăng version để phát hành cùng cặp; giao diện website không thay đổi. Defaults đã duyệt vẫn ở seed version 1.0.2, giữ nguyên initializer có version và dữ liệu đã sửa/xóa khi chạy lại. Không có migration database cho bản sửa email.

## Cập nhật hosting

1. Sao lưu riêng database, code và uploads trên hosting.
2. Trong **Plugin → Cài mới → Tải plugin lên**, chọn ZIP core 1.0.3 và **Thay thế bản hiện tại bằng bản tải lên**.
3. Trong **Giao diện → Thêm mới → Tải theme lên**, chọn ZIP child 1.0.3 và thay thế bản hiện tại. Giữ Project Child đang kích hoạt cùng Kadence.
4. Xóa cache hosting/CDN nếu có. Giữ nguyên cấu hình SMTP đang dùng; không cần chạy lại khởi tạo mặc định hoặc ACF Sync để sửa email.
5. Kiểm tra **Cài đặt → Tổng quan**: URL WordPress/website phải là domain HTTPS thật. Nếu dùng hằng `WP_HOME`/`WP_SITEURL`, kiểm tra cấu hình riêng trên hosting. Khi chuyển từ local, xử lý URL media cũ bằng công cụ migration hiểu serialized data sau khi sao lưu; không thay chuỗi SQL hàng loạt.
6. Vào **Đơn hàng → Cài đặt SMTP**, gửi email thử rồi thử đặt một sản phẩm và một combo. Kiểm tra email mới; thư cũ không thay đổi sau khi cập nhật.

Logo được tải từ theme đang kích hoạt:

- `/wp-content/themes/project-child/assets/fractional/assets/images/brand/fractional-cc-logo-white.png`
- `/wp-content/themes/project-child/assets/fractional/assets/images/brand/bui-dang-jp-logo.png`

Ảnh sản phẩm được tải từ URL Media đã lưu lúc đặt hàng. Mở các URL thực tế bằng cửa sổ ẩn danh: phải trả về ảnh, HTTP 200, HTTPS hợp lệ, không yêu cầu đăng nhập. Nếu có WAF/CDN/chống hotlink, kiểm tra việc tải ảnh không bị chặn khi không có Referer của website. Không tắt bảo mật toàn site. `.gitignore` không điều khiển hiển thị email; uploads được loại khỏi Git có chủ đích và phải được chuyển theo quy trình migration/backup riêng.

Ảnh qua URL cần website truy cập công khai. Gmail có thể chặn ảnh khi thư đáng ngờ hoặc người nhận tắt hiển thị ảnh; khi đó có thể cần **Hiển thị hình ảnh**. Điều này không có nghĩa ảnh bị đính kèm trở lại. Xem [hướng dẫn hiển thị ảnh của Gmail](https://support.google.com/mail/answer/145919?hl=vi).

## Xác định và xử lý thư rác

Ảnh chụp màn hình chưa có header email nên chưa kết luận được nguyên nhân thực tế trên hosting. Bỏ CID giải quyết phần ảnh/tệp đính kèm; không bảo đảm thư vào Hộp thư đến. SMTP kết nối thành công chỉ chứng minh hệ thống gửi mail chấp nhận thư.

Trong Gmail mở email → dấu ba chấm → **Hiển thị thư gốc**. Kiểm tra SPF, DKIM, DMARC và domain người gửi. Chỉ chia sẻ kết quả xác thực/domain cần thiết; không gửi mật khẩu SMTP hoặc toàn bộ nội dung khách hàng.

| Kết quả / cấu hình | Cách xử lý |
| --- | --- |
| Gửi bằng Gmail cá nhân | Dùng SMTP Gmail với địa chỉ From đúng tài khoản được xác thực (hoặc alias hợp lệ đã cấu hình tại Gmail). Mật khẩu ứng dụng lưu riêng trên hosting. Không thêm SPF Google vào domain website chỉ vì website gửi bằng tài khoản `@gmail.com`. |
| Gửi bằng email tên miền qua hosting/dịch vụ email | Dùng From đã được nhà cung cấp xác minh. Lấy bản ghi SPF, khóa/selector DKIM và hướng dẫn DMARC từ chính nhà cung cấp SMTP; không đoán giá trị DNS. |
| SPF FAIL/SOFTFAIL hoặc không có | Kiểm tra SPF cho domain envelope sender/Return-Path có cấp quyền cho máy chủ gửi thực tế. Chỉ có một bản ghi SPF trên mỗi tên DNS; gộp các nguồn gửi đúng theo hướng dẫn nhà cung cấp. |
| DKIM FAIL hoặc không có | Bật ký DKIM trên dịch vụ gửi; kiểm tra đúng selector và public key DNS. Chỉ thêm bản ghi DNS mà không bật ký phía gửi chưa đủ. |
| DMARC FAIL | Kiểm tra domain trong From khớp theo quy tắc alignment với SPF hoặc chữ ký DKIM đã PASS. DNS của website không sửa được việc mạo danh From của một domain khác. |
| SPF/DKIM/DMARC đều PASS nhưng vẫn Spam | Kiểm tra danh tiếng domain/IP gửi và phản hồi người nhận. Nếu gửi qua IP hosting dùng chung, yêu cầu nhà cung cấp kiểm tra blacklist và PTR/DNS thuận-ngược của máy chủ gửi. |

Google khuyến nghị thiết lập SPF, DKIM và DMARC cho domain gửi; xác thực đúng giảm nguy cơ bị chặn/spam nhưng không bảo đảm vị trí thư. Tham khảo [nguyên tắc người gửi của Gmail](https://support.google.com/mail/answer/81126?hl=vi). Với thư thử hợp lệ trong hộp thư quản trị, có thể bấm **Không phải spam**; đây là thao tác cho hộp thư đó, không thay thế việc sửa xác thực.

## Kiểm tra đã chạy

Kiểm tra trên XAMPP ổ D: vì đường dẫn C: trong tài liệu hiện không tồn tại. Root workspace hiện thiếu `wp-config.php`; không xác nhận runtime site làm việc hoặc hosting đang kích hoạt phiên bản mới. Tạo WordPress/database riêng có marker, giải nén đúng hai ZIP vào fixture, kích hoạt ACF + core + child/Kadence. Không gửi email ra ngoài.

- Build lint toàn bộ PHP trong hai package; lint script kiểm tra email: đạt.
- 2.644 kiểm tra defaults: cài mới, Media, nội dung, quan hệ; chạy lại giữ thiết lập đã sửa và trường đã xóa, không nhân đôi Media: đạt.
- 219 kiểm tra email qua WordPress/PHPMailer: 10 sản phẩm, 1 combo, ảnh thiếu/URL không an toàn/URL chứa query string và nút thử SMTP; MIME không có attachment/CID; các URL ảnh trả HTTP 200 với Content-Type ảnh: đạt.
- 181 kiểm tra đơn hàng/biên lai/render/quyền riêng tư: đạt.
- 82 kiểm tra bảo mật, gồm quyền và nonce SMTP: đạt.
- Hai ZIP: CRC hợp lệ, root/file count đúng và từng byte khớp source. Xem [package-report.json](qa/package-report.json).

Chưa kiểm tra nhận thư thực tế trong Gmail/Outlook, truy cập ảnh từ Google image proxy, DNS hoặc uy tín IP hosting. Cần domain thật, nhà cung cấp SMTP và kết quả header thư thật để chẩn đoán chính xác Spam. Nếu cần rollback, khôi phục code 1.0.2 và backup phù hợp, bảo toàn đơn hàng/dữ liệu phát sinh.
