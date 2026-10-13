# Fractional CC 0.3.7

Ngày 13/10/2026, XAMPP `C:\xampp\htdocs\fractionalcc-jp.vn`. Sửa lỗi trong ảnh người dùng gửi: năm dòng warning `Undefined array key "type"` tại `inc/fractional.php:51` trước header. Đã áp dụng source trên site local; chưa deploy production.

## Sửa lỗi

- Năm trường feedback 1–5 đã có định nghĩa ảnh đầy đủ nhưng lại được thêm lần nữa trong mục “Media & Nhận diện” với mỗi thuộc tính `label`. Xóa năm khai báo trùng thiếu cấu trúc và đổi nhãn của các trường gốc sang PNG. Giữ key/name/type, giá trị Media hiện có và hai trường feedback 6–7.
- Hàm prime images kiểm tra `type`/`name` trước đọc. Customizer kiểm tra đủ `type`/`key`/`name`/`label` trước tạo control, để xử lý an toàn mục thiếu thuộc tính.
- Phiên bản hai package/defaults/compiler là `0.3.7`. Không chạy compiler tái sinh giao diện hoặc thay nội dung/template/CSS mới có trong workspace.

## Packages và cài đặt

- `project-core-v0.3.7.zip`: folder gốc `project-core/`, defaults và initializer hiện tại.
- `project-child-v0.3.7.zip`: folder gốc `project-child/`, schema sửa lỗi và runtime/assets hiện tại.
- Dependencies kiểm tra: WordPress `7.1.3`, PHP `8.0.30`, MariaDB `10.4.32`, Kadence `1.5.2`, ACF Free `6.8.10`. Cài riêng Kadence/ACF.

Cập nhật đúng cặp ZIP, giữ Project Child và Project Core active. ACF fields đăng ký bằng PHP, không cần Sync hoặc chạy lại initializer để sửa warning này. Site hiện tại không đổi dữ liệu database trong tác vụ; seed version vẫn `0.3.6`, source/package là `0.3.7`. Cài mới tự khởi tạo khi dependencies sẵn sàng theo quy trình đang có.

## Kiểm tra

- Trước sửa, HTTP trang chủ có đúng 5 warning như ảnh. Sau sửa, trang chủ/danh mục/chính sách/cảm ơn đều HTTP `200`, không có PHP warning/fatal/notice trong HTML; ảnh feedback vẫn được render.
- 797 field definitions đầy đủ và duy nhất trong từng scope. Cả bảy trường ảnh feedback có ACF type `image` và native Customizer media control. Prime images/đăng ký Customizer chạy với PHP error handler nghiêm ngặt, không warning.
- Lint PHP custom trong hai ZIP, PHP thay đổi và kiểm tra whitespace ở các file sửa lỗi: đạt. Whitespace có sẵn trong CSS/template feedback ngoài phạm vi sửa lỗi không được chỉnh.
- Hai ZIP được giải nén/cài thật cùng Kadence/ACF trên database MariaDB riêng: 53 kiểm tra initializer và 305 kiểm tra catalog đạt. Kiểm tra schema/Customizer như trên cũng đạt trên cài mới; không phát sinh PHP debug log.
- Cấu trúc ZIP đúng slug, không config/secrets/dump/log/private uploads; thư mục release có đúng hai ZIP.

Chưa kiểm tra trực quan desktop/mobile/console vì không có trình duyệt kết nối. Không gửi đơn/email. Database test đã xóa; cấu hình kết nối tạm đã được thay bằng nội dung không chứa credentials. Bản sao package/dependency junction trong TEMP được giữ ngoài repo/ZIP.

## Rollback

Đổi code về cặp ZIP trước nếu cần; bản `0.3.6` có warning đã mô tả nên không phải bản khôi phục lỗi này. Tác vụ không migration/ghi đè dữ liệu hiện tại. Backup database/uploads/config của môi trường đích trước cập nhật; chưa thực hiện rollback trên site thật.

## SHA-256

- `project-core-v0.3.7.zip`: `7D1D2D86832AF1D7200A7EB5ACBA5BCF367B566AA1F37FA9FFA4187CE49F7B69`
- `project-child-v0.3.7.zip`: `7D0A3F42DA744D808B5B767A854594C4B9BBE6E4462D648C333A87EB52C99E09`
