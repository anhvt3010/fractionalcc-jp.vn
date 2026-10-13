# Fractional CC 0.3.5 – Rà soát khởi tạo theme/plugin

Ngày 13/10/2026, XAMPP `C:\xampp\htdocs\fractionalcc-jp.vn`. Đã áp dụng source và chạy lại initializer trên site hiện tại; chưa deploy production.

## Lỗi đã sửa

1. Khóa khởi tạo không có hạn dùng: tiến trình bị ngắt có thể chặn mọi lần chạy tiếp theo. Khóa mới có token riêng; khóa quá 15 phút được thu hồi bằng compare-and-delete. Hỗ trợ cả khóa timestamp của bản cũ. Worker cũ không xóa được khóa của worker mới; khóa đang hiệu lực vẫn chặn chạy đồng thời.
2. Tra cứu source key dùng `post_status=any`, bỏ qua thùng rác: chạy lại có thể tạo lại sản phẩm/trang đã xóa. Tra cứu giờ bao gồm trạng thái trash, giữ nguyên bản ghi và trạng thái người dùng chọn.
3. Không kiểm tra đủ cấu trúc defaults/assets trước ghi dữ liệu: bổ sung preflight cho trang/template, sản phẩm, combo, liên kết sản phẩm, đường dẫn và MIME ảnh. Package thiếu trang, tham chiếu sai, thiếu ảnh hoặc file giả ảnh được trả về `WP_Error` trước khi tạo nội dung.
4. Ảnh sản phẩm trong defaults còn dùng đường dẫn legacy thay vì ảnh Excel đã duyệt: đồng bộ tham chiếu ảnh/gallery và ảnh dùng chung với `catalog-excel.json`. Site mới dùng ảnh Excel đúng hàng/hash. Khởi tạo thường giữ nguyên ảnh/nội dung đã lưu trên site hiện có.
5. Xử lý ảnh trước kiểm tra metadata đã tồn tại: chạy lại sau cập nhật defaults có thể nhập thêm ảnh mặc định không sử dụng. Các trường đã tồn tại giờ được bỏ qua trước xử lý media, trừ cập nhật catalog bằng CLI có chỉ định rõ.
6. Khi thiếu dependency, trạng thái chờ chưa có notice trên màn quản trị chung: bổ sung thông báo cài Kadence/kích hoạt ACF và Project Child, giữ queue để tự tiếp tục khi đủ dependency. Công cụ import Excel cũng không còn đặt lùi defaults version về `0.3.3`.

Không sửa WordPress core, Kadence hoặc ACF. Giữ layout, ảnh combo nền vàng và quyền/nonce của thao tác quản trị.

## Packages và cách cài

- `project-core-v0.3.5.zip`: một folder gốc `project-core/`, chứa defaults và initializer `0.3.5`.
- `project-child-v0.3.5.zip`: một folder gốc `project-child/`, chứa assets/default media và fields giao diện.
- Runtime kiểm tra: WordPress `7.1.3`, PHP `8.0.30`, MariaDB `10.4.32`, Kadence `1.5.2`, ACF Free `6.8.10`. Kadence/ACF cài riêng; không thêm dependency.

Site mới: upload/kích hoạt hai ZIP và dependencies, vào quản trị bằng administrator. Hệ thống tự khởi tạo khi ACF/child theme sẵn sàng, chọn homepage nếu chưa có lựa chọn riêng. Site đang dùng: cập nhật đúng cặp ZIP; reactivation/khởi tạo thường bảo toàn dữ liệu. ACF fields đăng ký bằng PHP, không cần Sync. Không có thao tác reset hoặc ghi đè catalog tự động. Nút khởi tạo vẫn yêu cầu quyền và nonce; cập nhật catalog có ghi đè chỉ qua CLI được chỉ định riêng.

## Kết quả kiểm tra

- Lint toàn bộ PHP trong hai ZIP, script kiểm thử và syntax JS importer; `git diff --check`: đạt.
- Kiểm tra cài/kích hoạt trên database MariaDB riêng với Core trước theme/ACF, và theme/ACF trước Core: đạt. Khi thiếu parent hoặc ACF, không nhập dữ liệu, queue còn chờ và có notice; khi đủ dependency, tiếp tục khởi tạo.
- Hai ZIP cuối được giải nén/cài thật vào content directory mới, database riêng: **53 kiểm tra khởi tạo đạt**, gồm package không hợp lệ không tạo dữ liệu dở dang, khóa đang chạy/khóa cũ hết hạn/khóa token hết hạn, chống worker cũ giải phóng khóa mới, đúng homepage/template, đúng ảnh nền vàng/hash, đủ 10 sản phẩm và đúng media, không tạo trùng, giữ title/ảnh bỏ chọn/homepage/blog, giữ mục trashed, không nhập ảnh mặc định thừa, reactivation và quyền anonymous.
- **305 kiểm tra Excel/defaults/database đạt** trên cài mới cuối: tên, thành phần, giá, nguồn/hash từng ảnh, gallery, thẻ chủ lực và combo. Không có debug log PHP phát sinh trong lần kiểm tra cuối.
- Site hiện tại: snapshot trước/sau chạy lại initializer giữ nguyên **53 bản ghi nội dung**, Reading và số attachment. Snapshot đã lưu/đọc lại trong backup riêng, ngoài repo/ZIP. Seed version cập nhật lên `0.3.5`, khóa được giải phóng. URL gốc HTTP `200`, có hero/ảnh nền vàng, không có PHP warning/fatal/notice trong HTML.
- Hai ZIP đúng folder gốc, không config/secrets/dump/log/private uploads; release có đúng hai ZIP.
- Script `scripts/verify-fractional-initialization.php` từ chối chạy khi không chỉ định root riêng hoặc site không có marker kiểm thử. Script chỉ dùng cho site test vì có các thao tác chỉnh dữ liệu/trash/defaults để thử bảo toàn và lỗi.

Chưa kiểm thử trực quan desktop/mobile/console vì không có trình duyệt kết nối. Không gửi đơn/email. Không thay ảnh sản phẩm cũ đã lưu trên site hiện tại; đối chiếu Excel toàn bộ áp dụng cho cài mới, không áp dụng như migration ghi đè site hiện có. Chưa thử ngắt tiến trình thật hoặc hai tiến trình đồng thời; kiểm tra khóa đã mô phỏng trạng thái worker/expiry bằng token và timestamps.

## Backup và rollback

Backup nội dung/Reading trước chạy lại nằm riêng tại `C:/Users/anhvt/.codex/backups/fractionalcc-jp.vn/`. Chưa phục hồi vào site thật. Backup database/uploads/config môi trường đích trước cập nhật. Rollback về cặp ZIP `0.3.4` nếu cần; code không tự đảo ngược dữ liệu. Site hiện tại đã được kiểm tra không đổi nội dung/media/Reading.

Database test đã xóa sau kiểm tra; nội dung cấu hình kết nối tạm được thay bằng nội dung không chứa credentials. Bản sao package và dependency junction trong TEMP được giữ ngoài repo/ZIP.

## SHA-256

- `project-core-v0.3.5.zip`: `9E00AA5A401A65F4B6515F1E9588274EBB56A4E9530DB899178705A56DBC3061`
- `project-child-v0.3.5.zip`: `62E8EA65E698DB147EE9828BA5DAE8AB3F78BEC9CCA718F42C6C6532520F3FF1`
