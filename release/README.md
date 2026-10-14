# Build và release Fractional CC

Bản cập nhật hiện hành là **[1.0.2](v1.0.2/RELEASE.md)**: đồng bộ chính xác sản phẩm mua và màn cảm ơn (ảnh, giá, tên, quà tặng) và bảo mật biên lai đơn hàng. [1.0.1](v1.0.1/RELEASE.md) khôi phục checkbox đồng ý; [1.0.0](v1.0.0/RELEASE.md) là bản đầu tiên. Xem [hướng dẫn cập nhật hosting](v1.0.2/RELEASE.md).

Mỗi `release/vX.Y.Z/` chứa đúng hai ZIP cài qua WordPress: `project-core-vX.Y.Z.zip` và `project-child-vX.Y.Z.zip`, cùng `RELEASE.md` và hồ sơ `qa/`. Root ZIP tương ứng là `project-core/` và `project-child/`. Plugin chứa nghiệp vụ/defaults/initializer; child theme chứa giao diện/schema/ảnh đã duyệt. WordPress, Kadence và ACF cài riêng. Không đóng gói config, secrets, database, tài khoản, đơn hàng, logs, backup hoặc uploads.

## Build lần tiếp theo

1. Dùng XAMPP tại `C:/xampp/htdocs/fractionalcc-jp.vn`; không yêu cầu Docker. Sao lưu riêng môi trường trước thao tác có rủi ro.
2. Khi người dùng duyệt defaults mới, chạy XAMPP PHP với `scripts/export-fractional-defaults.php --user=<administrator-id> --version=X.Y.Z --approve`. Exporter chỉ lấy nội dung/ảnh/settings được cho phép, không lấy dữ liệu vận hành riêng.
3. Cập nhật header version core và child theme khớp snapshot. Compiler HTML không được ghi đè defaults đã approved.
4. Chạy `powershell -NoProfile -ExecutionPolicy Bypass -File scripts/build-release.ps1 -Version X.Y.Z`. Script lint PHP và không ghi đè ZIP tồn tại.
5. Kiểm tra root, file count, CRC, SHA-256 và byte khớp source. Cài/kích hoạt từ ZIP trên site/database riêng; thử dependencies thiếu, cài mới và chạy lại giữ edits, kể cả giá trị đã xóa.
6. Kiểm tra frontend/admin và responsive; ghi kết quả thực tế, checksum, dependencies và giới hạn vào `RELEASE.md`. Không gửi form thật khi chưa được yêu cầu.

Các kiểm tra defaults/liên hệ dùng `PROJECT_WORDPRESS_ROOT` trỏ đến fixture có marker riêng, không dùng database làm việc.

## Cài đặt và khởi tạo

Cài Kadence và ACF, upload/kích hoạt plugin core và child theme cùng version. Kích hoạt xếp hàng khởi tạo; mở admin bằng administrator có quyền upload để thực hiện khi đủ dependencies. Có nút khởi tạo trong **Nội dung website**. Không cần ACF Sync.

Initializer ánh xạ source key, Media/gallery, URL nội bộ và quan hệ sang site đích. Cài mới chọn homepage khi chưa có lựa chọn; chạy lại giữ nội dung, ảnh, metadata, Reading và settings đã sửa hoặc đã xóa. Defaults có trạng thái `approved`; local/staging vẫn noindex, trang Cảm ơn luôn noindex.

Trước deploy, sao lưu môi trường đích và kiểm tra phục hồi. Cài đúng cặp ZIP/dependencies, cấu hình domain/secrets riêng rồi smoke-test. Không sao chép database thử nghiệm lên production. Rollback dùng backup môi trường đích và code đã kiểm chứng, bảo toàn dữ liệu phát sinh.
