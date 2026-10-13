# Fractional CC 0.3.15 – Sắp xếp quản trị Thông tin chung

Ngày 13/10/2026. Áp dụng phần quản trị trên XAMPP tại `C:\xampp\htdocs\fractionalcc-jp.vn`; chưa deploy production. Source đang có những cập nhật tiếp theo ở phiên bản khác; hai ZIP này là snapshot 0.3.15. Không có commit/tag riêng cho tác vụ.

## Thay đổi

- Chia 218 trường giao diện dùng chung thành 16 tab theo vị trí: đầu trang/logo, menu, chân trang, nút liên hệ, cửa sổ đặt hàng, gợi ý nhập liệu, các khối trang chủ và các khối trang sản phẩm. Nhãn mô tả vai trò của trường; hướng dẫn ghi nơi hiển thị và liên kết xem website.
- 48 trường của bố cục cũ nằm trong tab lưu trữ, ghi rõ chưa được giao diện hiện tại sử dụng. Giữ toàn bộ giá trị để không mất nội dung đã nhập. Trường mới chưa có trong bản đồ sẽ vào mục Nội dung bổ sung.
- Các thông tin nghiệp vụ nằm trong bốn tab riêng: liên hệ dùng chung, thương hiệu/phân phối, trạng thái nội dung và hồ sơ tham chiếu chưa hiển thị. Giữ nguyên 13 trường dữ liệu, lựa chọn và cách lưu.
- Thêm tìm kiếm theo khu vực, nhãn, mô tả hoặc chữ đang được nhập. Tìm không dấu, ưu tiên nội dung đang hiển thị; chọn kết quả mở tab ACF hiện có và đưa con trỏ tới trường. Tìm kiếm chạy trong trình duyệt, không gửi hoặc lưu dữ liệu.
- Thêm hai định nghĩa alt của ảnh đánh giá thứ bảy vốn đã có giá trị mặc định và tham chiếu runtime. Không sửa giá trị ảnh/alt đã lưu.
- Giữ schema gốc cho Customizer và cách sửa text ngoài giao diện. Các nhãn, tab mới chỉ áp dụng trong quản trị. Assets mới chỉ tải ở màn chỉnh sửa `fcc_site`.
- Thêm anchor vào khối chân trang và hồ sơ pháp lý sản phẩm để liên kết hướng dẫn dẫn tới đúng khu vực. Compiler giữ các anchor này; không chạy tái sinh giao diện trong tác vụ.

Trình bày quản trị/bản đồ trường/assets thuộc child theme; trường nghiệp vụ và hướng dẫn quản trị thuộc project plugin. Không sửa WordPress core, ACF hoặc Kadence. Không thêm dependencies, biến môi trường hay nội dung kinh doanh.

## Package và dependencies

- `project-core-v0.3.15.zip`: một folder gốc `project-core/`, gồm defaults có version và initializer hiện có.
- `project-child-v0.3.15.zip`: một folder gốc `project-child/`, gồm bản đồ quản trị, field definitions, templates và assets.
- Kiểm tra với WordPress 7.1.3, PHP 8.0.30, MariaDB 10.4.32, Kadence 1.5.2 và ACF Free 6.8.10. Cài Kadence và ACF riêng từ nguồn chính thức; không gộp dependency vào ZIP, không đổi license.
- Nội dung mặc định tiếp tục theo [brief](../../docs/WEBSITE-BRIEF.md) và nguồn hiện có. Không xác nhận thêm các dữ kiện còn thiếu/xung đột trong brief.

## Cập nhật và dữ liệu

1. Lưu backup database/uploads/config riêng của môi trường đích trước khi cập nhật.
2. Cập nhật đúng cặp ZIP và giữ child theme cùng plugin đang hoạt động; dependencies cài riêng như trên.
3. Tải lại **Nội dung website → Thông tin chung**. Không cần ACF Sync, migration, reset dữ liệu hay chạy initializer chỉ để đổi bố cục quản trị. Nhấn Cập nhật khi sửa nội dung bằng các trường ACF như trước.

Site mới sử dụng initializer versioned hiện có khi đủ dependencies; chạy lại chỉ bổ sung dữ liệu còn thiếu và giữ các giá trị đã sửa. Tác vụ không chạy initializer toàn bộ trên site đang dùng. Khóa chỉnh sửa `_edit_lock` được WordPress làm mới khi đọc trang editor; nội dung đã lưu được đối chiếu riêng và giữ nguyên. Không gửi đơn/email hay thay thông tin liên hệ.

## Kiểm tra đã thực hiện

- Lint PHP runtime hai ZIP, compiler và syntax JavaScript: đạt.
- HTTP có xác thực tới editor native của bản ghi Thông tin chung: 200, hai nhóm ACF, 16 tab giao diện, đủ 218 trường và assets tìm kiếm; không PHP warning. Giữ key/name/type/return format/choices/mime types của trường gốc.
- Đối chiếu nội dung đã lưu sau khi đọc editor, loại trừ khóa chỉnh sửa WordPress: không thay đổi.
- So sánh header/main/footer, liên kết, ảnh và alt trên 15 trang trước/sau thay đổi quản trị: không thay đổi tại thời điểm kiểm tra.
- Cài/kích hoạt hai ZIP trong content directory và database MariaDB kiểm thử riêng: **53 kiểm tra khởi tạo, 305 kiểm tra catalog đạt**, không phát sinh PHP debug log.
- Bootstrap tại thời điểm `acf/init` thật: đủ 218 trường/16 tab. Lưu thử bằng `acf_save_post` trên database riêng và chạy lại initializer: giá trị sửa được giữ. Đủ **218 control Customizer gốc, 75 partial sửa text trực tiếp**.
- ZIP đúng slug, đủ runtime/defaults/assets, không config/secrets/dump/log/private uploads. Bản đồ, helper quản trị, JS/CSS và code nghiệp vụ khớp source đã kiểm tra. Thư mục release có đúng hai ZIP.

Chưa kiểm tra trực quan desktop/mobile, browser console hoặc bấm tìm kiếm/chuyển tab bằng trình duyệt vì không có browser kết nối. Kiểm tra HTML native và API ACF không thay thế nghiệm thu tương tác thực tế. Chưa kiểm tra rollback trên site đang dùng; không liên quan WooCommerce. Database kiểm thử được xóa sau kiểm tra; configs tạm được thay bằng nội dung không chứa credentials. Bản sao package và junction dependency trong TEMP nằm ngoài repo/ZIP.

## Rollback

Khôi phục cặp plugin/theme trước nếu cần. Field keys và kiểu lưu không thay đổi nên nội dung đã sửa tiếp tục được giữ; không xóa các trường lưu trữ hoặc chép database kiểm thử vào site thật. Chỉ khôi phục database từ backup riêng khi cần phục hồi dữ liệu, có xét nội dung phát sinh sau backup.

## SHA-256

- `project-core-v0.3.15.zip`: `79B95BE4902DCD96741FCC2A4F80D70AF9D3FC0C15E24171EB3CCA95EDD48EE4`
- `project-child-v0.3.15.zip`: `194E6B07B593E392DF6AAF1B2415ECD819C66FF06BF9E157570596D24A8FEB27`
