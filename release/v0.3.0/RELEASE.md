# Fractional CC 0.3.0 — bản chuẩn bị để review

- Ngày: 12/10/2026.
- Trạng thái: **draft/local review**, đã khởi tạo dữ liệu và homepage trên site XAMPP chính; chưa nghiệm thu production.
- Git commit/tag: chưa có, repository root chưa khởi tạo Git.
- Source: child theme `project-child` và plugin `project-core`, version `0.3.0`.
- Hướng dẫn: [FRACTIONAL-WORDPRESS.md](../../docs/FRACTIONAL-WORDPRESS.md).
- Nguồn nội dung: [WEBSITE-BRIEF.md](../../docs/WEBSITE-BRIEF.md) và `template/fractional-cc`.

## Phạm vi

Chuyển các mẫu trang chủ, danh mục, chi tiết sản phẩm, chính sách và cảm ơn sang child theme hiện có. Bổ sung CMS bằng ACF miễn phí và các mô hình sản phẩm, combo, hoạt chất, thông tin chung trong plugin core. Giữ CSS/asset thiết kế nguồn, tách PHP thành template parts và đọc nội dung từ CMS có escaping.

Khởi tạo defaults có version, quyền và nonce cho thao tác admin; CLI chỉ chạy từ command line. Không tự kích hoạt theme, đổi URL hoặc homepage. Chạy lại bảo toàn nội dung đã sửa và không tạo trùng. `.gitignore` loại secrets/config local/runtime và ZIP build; vẫn theo dõi source core, parent theme, plugin bên thứ ba và bundled vendor của starter.

## Hai gói ZIP

| Gói | Dung lượng | Folder gốc | SHA-256 |
| --- | ---: | --- | --- |
| `project-core-v0.3.0.zip` | 44.657 bytes | `project-core/` | `372BED7B61E4CA32B89974CE66A8B8A16B58E7E20673989AB11B41B0FBBB1143` |
| `project-child-v0.3.0.zip` | 8.691.046 bytes | `project-child/` | `4F756B04F5E3A67B16EC0DCFCBF3C523FE0CDA9B9145B5AB601CA97A95318B8F` |

Đã kiểm tra đúng hai ZIP, một folder gốc/ZIP và không có core, parent theme, third-party plugin, secrets/config local, uploads, cache hoặc dump/log. ZIP plugin có defaults; ZIP theme có schema/asset/template runtime. Các gói này là bản chuẩn bị để review, không phải bằng chứng chức năng tiếp nhận đơn hay website chính đã được triển khai.

## Dependencies và cài trên site kiểm thử

Dependencies riêng: WordPress tương thích, Kadence parent, ACF miễn phí. Môi trường đã kiểm thử: WordPress trên đĩa `7.1.3`, XAMPP PHP `8.0.30`, ACF `6.8.10`; không dùng Docker hoặc thêm dependency frontend. Phiên bản parent thực tế cần kiểm tra tại môi trường cài, không coi ghi chú starter cũ là xác nhận runtime mới.

1. Chọn database/site kiểm thử riêng; chuẩn bị dependencies và cấu hình riêng.
2. Cài/activate ZIP plugin core, cài/activate ZIP child theme; Kadence phải có sẵn.
3. Mở **Nội dung website** bằng administrator, khởi tạo dữ liệu mặc định hoặc dùng CLI theo hướng dẫn.
4. Chỉ đặt trang chủ/đổi URL khi đã được chỉ định cho đúng site. Kiểm tra permalink và rewrite base theo URL thực tế.
5. Sửa thử nội dung, chạy lại initializer để kiểm tra không ghi đè; kiểm tra tất cả trang và editor.

ACF definitions không phải nội dung mặc định; dữ liệu đã đóng gói trong `defaults/fractional.json`. Media được nhập từ assets của child theme vào Media Library. Bản ghi/trường đã có không bị ghi đè, kể cả giá trị rỗng có chủ đích. Cần backup/migration riêng khi muốn thay nội dung đã tồn tại.

## Kiểm thử

- [x] PHP lint trong build và JavaScript syntax check.
- [x] 10 sản phẩm, 1 combo và 39 hoạt chất khởi tạo trên database riêng.
- [x] Chạy lại không tạo trùng; giữ nội dung đã sửa; từ chối khởi tạo không có quyền.
- [x] Administrator đăng nhập HTTP và mở CMS/editor ACF.
- [x] Lưu qua form editor WordPress thay đổi được nội dung hiển thị; nonce ACF sai không ghi đè.
- [x] Editor trang chủ có 503 controls, nằm dưới `max_input_vars=1000`.
- [x] Escaping nội dung và quyền truy cập CMS đã kiểm tra qua HTTP.
- [x] Năm loại trang trả HTTP 200, một H1/một canonical; danh mục có 10 card server-rendered.
- [x] Hai CSS thiết kế chính khớp SHA-256 với source; 66 file trong template nguồn không bị sửa.
- [x] Cấu trúc/checksum hai ZIP đã kiểm tra.
- [ ] Cài đặt lại từ ZIP trên một site sạch khác.
- [ ] Desktop/mobile bằng ảnh, console và thao tác JavaScript/editor Gallery trong trình duyệt.
- [ ] Luồng tiếp nhận yêu cầu/receipt/email theo lựa chọn cuối cùng.
- [x] URL site chính và homepage Fractional CC hoạt động; defaults đã khởi tạo trên database hiện tại sau backup.
- [ ] Nội dung/ưu đãi/đánh giá/chính sách/media được duyệt để public.

Không có kết nối trình duyệt trong phiên thực hiện; kiểm thử HTTP không thay thế nghiệm thu giao diện hoặc thao tác JavaScript. Chưa gửi email hoặc chạy workflow WooCommerce. Bản này giữ luồng handoff Zalo của template và chưa có handler lưu yêu cầu trên server.

## Điều kiện còn cần chốt

Cấu hình `fractionalcc-jp.vn` và thư mục `wordpress` cũ vẫn trỏ cùng database. URL local đã được sửa bằng cấu hình riêng và rewrite `/fractionalcc-jp.vn/`. Theo yêu cầu dùng nội dung index và danh sách sản phẩm làm mặc định active, defaults và homepage đã được áp dụng vào database hiện tại sau backup riêng; tên website/slogan lấy từ dữ liệu template. Các thay đổi nội dung và homepage có ảnh hưởng database dùng chung. Luồng tiếp nhận đơn phía server và nội dung public ngoài phạm vi đã chỉ định vẫn cần hoàn thiện.

Runtime giữ chế độ local `noindex`; không dùng trang cảm ơn hoặc mã mẫu để khẳng định doanh nghiệp đã tiếp nhận đơn. Việc khởi tạo nội dung không thay đổi source hoặc checksum hai ZIP. Khi hoàn thiện luồng tiếp nhận và nghiệm thu giao diện cần chốt artifact phù hợp; không deploy draft này như bản production đã nghiệm thu.

## Rollback

Database hiện tại đã được khởi tạo nội dung, đặt homepage và cập nhật tên/slogan; đã tạo backup SQL/config riêng ngoài web root trước thao tác. Source chỉ sửa child theme và plugin dự án; parent/core/vendor giữ nguyên. Quay lại code cũ không tự hoàn tác dữ liệu đã tạo. Trước rollback cần chọn backup/migration phù hợp cho database dùng chung, bảo toàn nội dung phát sinh sau thời điểm sao lưu và không tự xóa media.
