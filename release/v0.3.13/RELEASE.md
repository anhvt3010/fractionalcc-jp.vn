# Fractional CC 0.3.13 – Header/menu dùng chung

Ngày 13/10/2026. Đã áp dụng trên site XAMPP `C:\xampp\htdocs\fractionalcc-jp.vn`; chưa deploy production.

## Thay đổi theo yêu cầu

- Mọi màn Fractional CC dùng một header chung: trang chủ, danh mục, chi tiết sản phẩm, vấn đề làn da, chính sách và cảm ơn. Thanh nhận diện trên cùng dùng lại bản của trang chủ.
- Bên trái: **Trang chủ**, **Sản phẩm**, **Về Thương hiệu**. Bên phải: **Vấn đề làn da**, **Bảo chứng pháp lý**, nút **Mua ngay** hiện có. Logo Fractional CC/BUI DANG-JP giữ giữa.
- Liên kết dẫn tới trang chủ, danh mục, khối `#gioi-thieu`, trang vấn đề làn da và khối `#cam-ket-phap-ly`. Dùng URL WordPress nên hoạt động từ mọi trang.
- Menu mobile dùng cùng năm mục và cùng thứ tự. Dưới 1280px, header dùng logo giữa, nút menu ở trái và Mua ngay ở phải; desktop dùng menu chia hai bên. Trạng thái menu có ARIA, vị trí logo dựa trên hai cột bên bằng nhau.
- Nút mua giữ style và handler hiện có; trang sản phẩm chọn đúng sản phẩm đang xem. Trang không có modal dùng fallback hiện có về form `#dat-hang` trên trang chủ. Trang vấn đề làn da tải đủ dữ liệu sản phẩm/combo cho modal đã có.
- Năm nhãn dùng chung được quản lý tại **Thông tin chung → Menu điều hướng** trong ACF/Customizer. Site hiện tại chỉ bổ sung năm trường mới; không ghi đè nội dung/ảnh khác, không chạy reset hoặc initializer toàn bộ.
- Loại bỏ năm header cũ và ba thanh nhận diện không còn được dùng. Compiler tham chiếu header chung, bảo toàn nhãn menu/trang quản lý ngoài compiler và giữ file thanh nhận diện được component chung sử dụng. Không chạy compiler tái sinh giao diện trong tác vụ này.

## Package/cài đặt

- `project-core-v0.3.13.zip`: folder gốc `project-core/`, defaults version `0.3.13` với năm nhãn mới và dữ liệu hiện có.
- `project-child-v0.3.13.zip`: folder gốc `project-child/`, component header/links/buy dùng chung, schema và assets responsive.
- Dependencies kiểm tra: WordPress `7.1.3`, PHP `8.0.30`, MariaDB `10.4.32`, Kadence `1.5.2`, ACF Free `6.8.10`. Kadence/ACF cài riêng. Không thêm dependency.

Cập nhật đúng cặp ZIP. ACF fields đăng ký bằng PHP, không cần Sync. Site mới tự khởi tạo khi đủ dependencies. Site đã dùng chạy khởi tạo thường để bổ sung năm nhãn nếu chưa có; thao tác này giữ các giá trị đã chỉnh. Site local hiện tại đã có năm nhãn, không cần thao tác thêm. Khi chỉnh tên menu trong CMS, desktop/mobile và mọi trang cùng cập nhật.

## Kiểm tra

- Lint PHP runtime toàn bộ hai ZIP và compiler: đạt.
- **233 kiểm tra HTTP/DOM trên 15 trang**: trang chủ, danh mục, chính sách, cảm ơn, vấn đề làn da và 10 trang chi tiết. Các trang `200`, không PHP warning/fatal, một header chung, đủ/thống nhất nhãn/thứ tự/đích desktop/mobile, logo/CTA, chọn đúng sản phẩm và ARIA menu. Hai anchor trên trang chủ tồn tại; trang vấn đề làn da có dữ liệu 10 sản phẩm/combo cho modal.
- Hai ZIP được cài/kích hoạt thật trong content directory mới và database MariaDB riêng: **53 kiểm tra khởi tạo và 305 kiểm tra catalog đạt**, không phát sinh PHP debug log.
- Render header trên 15 ngữ cảnh trang của site test với PHP error handler nghiêm ngặt: đạt. Sửa thử nhãn menu, chạy lại initializer: giá trị được giữ và xuất hiện cùng nhau trên desktop/mobile.
- ZIP đúng slug, không secrets/config/dump/log/private uploads; từng file trong ZIP khớp hash source. Release có đúng hai ZIP.

Chưa nghiệm thu trực quan các viewport, browser console hoặc click menu/modal bằng trình duyệt vì không có browser kết nối. CSS dùng breakpoint 1280px và lưới đối xứng; kiểm tra DOM/HTTP không thay thế nghiệm thu render/interaction. Không gửi đơn/email. Database kiểm thử đã xóa; config tạm đã được thay bằng nội dung không chứa credentials. Bản sao package/dependency junction trong TEMP được giữ ngoài repo/ZIP.

## Rollback

Backup database/uploads/config môi trường đích trước cập nhật. Đổi lại cặp ZIP trước nếu cần; năm trường menu mới không tự xóa khỏi database khi rollback. Tác vụ không reset dữ liệu, thay credentials hoặc cấu hình môi trường. Chưa thử rollback vào site đang dùng.

## SHA-256

- `project-core-v0.3.13.zip`: `D3BEC838B9A647774FEBC9275E57F5A8B6449A6BEEA65BC381D4C4DC397E9174`
- `project-child-v0.3.13.zip`: `7A87EC5C677143A63D48160EEED651184783CBE00D18FF7477D3A63E1C48DCDE`
