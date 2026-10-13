# Fractional CC 0.3.17 – Chọn danh mục trang chủ

Ngày 13/10/2026. Đã áp dụng trên XAMPP tại `C:\xampp\htdocs\fractionalcc-jp.vn`; chưa deploy production, chưa tạo commit/tag.

## Thay đổi

- Mục **Danh Mục Sản Phẩm & Các Combo Tối Ưu** đọc danh sách đã chọn trong WordPress thay cho sáu thẻ cố định.
- Trong editor từng **Sản phẩm** hoặc **Combo**, nhóm **Danh Mục Sản Phẩm & Các Combo Tối Ưu – Trang chủ** có nút **Hiển thị trong danh mục trang chủ** và ô thứ tự. Số nhỏ hơn đứng trước; khi bằng nhau sắp theo ID. Giới hạn **6 mục tổng cộng**, chỉ hiển thị bản ghi đã xuất bản. ACF chặn lựa chọn thứ bảy; frontend luôn giới hạn sáu.
- Lựa chọn độc lập với hai sản phẩm chủ lực. Danh sách sản phẩm/combo có cột cho biết đã chọn và thứ tự. Editor Trang chủ có hướng dẫn và liên kết tới danh sách cần chỉnh.
- Tái sử dụng card sản phẩm/combo hiện có, lấy tên/ảnh/mô tả/giá từ bản ghi. Link combo trên trang chủ dẫn tới khối combo trong trang danh mục; nút đặt mua chọn đúng ID hiện tại. Không áp dụng đánh giá cố định của catalog cho combo mới trên trang chủ; dùng đánh giá đã nhập trong bản ghi nếu có.
- Giữ heading, mô tả, CTA, lưới và CSS của khối. Nội dung các thẻ cũ vẫn lưu trong metadata, nhưng không còn hiện như trường chỉnh thẻ trong editor Trang chủ/Customizer. Quản lý nội dung thẻ tại sản phẩm hoặc combo được chọn. Các chức năng sửa text còn dùng tiếp tục hoạt động.
- Compiler giữ fragment mới và cấu hình defaults khi tái sinh; không chạy compiler trong tác vụ.

Logic chọn/giới hạn/khởi tạo thuộc project plugin; render/card/schema giao diện thuộc child theme. Không sửa WordPress core, parent theme hoặc ACF. Không thêm dependencies, credentials hay thông tin kinh doanh.

## Khởi tạo và cập nhật

Defaults version `0.3.17` có danh sách nguồn `combo_C-CX4`, `product_1`, `product_6`, `product_7`, `product_8`, `product_9`, đúng sáu thẻ trước thay đổi. Site local đã bổ sung lựa chọn này; không chạy reset/initializer toàn bộ, không đổi hai sản phẩm chủ lực.

Initializer mới có marker `project_core_fcc_home_catalog_version=1`. Chỉ khởi tạo lựa chọn nếu chưa từng cấu hình; kiểm tra đủ bản ghi trước khi ghi. Bản ghi bị ẩn/xóa không được thay bằng mục khác. Giá trị tắt hoặc danh sách rỗng chủ động được giữ khi chạy lại. Nếu đã có metadata cấu hình, upgrade giữ nguyên lựa chọn. Không đổi tên, ảnh, giá hay dữ liệu sản phẩm.

1. Backup database/uploads/config riêng trước cập nhật môi trường đích.
2. Cập nhật đúng cặp ZIP, giữ Project Child và Project Core hoạt động cùng ACF/Kadence.
3. Administrator mở quản trị để thực hiện bổ sung cấu hình một lần. Site mới dùng initializer hiện có khi đủ dependencies.
4. Vào **Nội dung website → Sản phẩm** hoặc **Combo**, mở từng mục và bật/tắt lựa chọn. Bỏ chọn mục cũ rồi Cập nhật trước khi chọn mục thay thế nếu đang đủ sáu. Nhập thứ tự và Cập nhật.

Không cần ACF Sync hoặc migration cấu trúc database. Các trường đăng ký bằng PHP. Backup/rollback trên môi trường đích chưa được thử trong tác vụ này.

## Package và kiểm tra

- `project-core-v0.3.17.zip`: folder gốc `project-core/`, 16 file runtime/defaults.
- `project-child-v0.3.17.zip`: folder gốc `project-child/`, 144 file runtime/assets/schema.
- Dependencies kiểm tra: WordPress 7.1.3, PHP 8.0.30, MariaDB 10.4.32, Kadence 1.5.2, ACF Free 6.8.10. Cài dependency riêng từ nguồn chính thức; không thay license. Nội dung theo [brief](../../docs/WEBSITE-BRIEF.md) và Excel đã duyệt, giữ các dữ kiện thiếu/xung đột.
- Lint toàn bộ PHP runtime qua build, compiler và script kiểm tra mới: đạt.
- Hai ZIP cài/kích hoạt trong content directory và database MariaDB riêng: **53 kiểm tra khởi tạo, 305 kiểm tra catalog đạt**, không phát sinh PHP debug log.
- **32 kiểm tra lựa chọn/lưu/render** trên database riêng: thứ tự mặc định, chặn mục thứ bảy, cho phép sửa mục đã chọn/bỏ chọn, giữ lỗi validation, thay mục bằng native ACF save, loại draft, giới hạn frontend khi metadata vượt sáu, giữ lựa chọn rỗng sau rerun/upgrade, không đổi duo, render sáu card, đích link và ID đặt hàng đúng.
- HTTP thực trên site local: trang chủ trả 200 và sáu card đúng thứ tự; danh mục vẫn đủ toàn bộ sản phẩm/combo. Editor native của cả product và combo có hai trường mới và vẫn có controls chủ lực; editor Trang chủ có hướng dẫn chọn. Không PHP warning/fatal liên quan.
- ZIP đúng slug, đủ source runtime và không secrets/config/dump/log/private uploads. Toàn bộ plugin và 141 file theme khớp source lúc đối chiếu; ba file của trang vấn đề làn da (`concerns.css`, CSS chung và `concerns/section-01.php`) có cập nhật khác sau snapshot build, không được ghi đè hoặc đưa thêm vào gói này. Các file thay đổi của tác vụ khớp package đã kiểm tra.

Chưa kiểm tra trực quan viewport, browser console hoặc bấm nút/toggle trong browser vì chưa có trình duyệt kết nối. Kiểm tra HTML native/API không thay thế nghiệm thu tương tác thực tế. Không gửi đơn/email, không liên quan WooCommerce. Database kiểm thử đã xóa; config tạm được thay bằng nội dung không chứa credentials. Artifacts/junction dependency trong TEMP nằm ngoài repo/ZIP.

## Rollback

Khôi phục cặp code trước. Các metadata lựa chọn mới được giữ và không ảnh hưởng code cũ; sáu thẻ cũ vẫn có dữ liệu trong Trang chủ. Không chép database kiểm thử đè site đang dùng; nếu phục hồi database thật, xét nội dung phát sinh sau backup.

## SHA-256

- `project-core-v0.3.17.zip`: `538AF563023459B2A8911A3848D888788FB6D41A982C7B83F9B7FFE6D0D63B0E`
- `project-child-v0.3.17.zip`: `F515DBF0E79A6F3BA448AC9088538FC478ECAE665A76E9D2E44978400F593A30`
