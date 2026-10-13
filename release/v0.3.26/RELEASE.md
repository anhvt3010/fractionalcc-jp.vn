# Fractional CC 0.3.26 – Dọn code và tài nguyên không dùng

Ngày 13/10/2026. Đã áp dụng trên XAMPP `C:\xampp\htdocs\fractionalcc-jp.vn`; chưa deploy production, chưa tạo commit/tag. Thực hiện theo yêu cầu xóa các phần dư đã được [rà soát](../../docs/CODE-ASSET-AUDIT-2026-10-13.md), đối chiếu lại source/runtime trước khi xóa.

## Phần đã dọn

- Bỏ `renderOrderHandoff()` và handler xóa khối handoff, là luồng sao chép/gửi Zalo cũ không còn được gọi. Bỏ `captureMarketingTracking()` và lời gọi: dữ liệu chỉ được ghi vào sessionStorage/biến global nhưng không có consumer trong runtime/đơn hàng hiện tại. Giữ cơ chế submit, xử lý lỗi, chống bấm liên tiếp và dữ liệu xác nhận đơn đang dùng.
- Bỏ nhánh fragment `usp`/`targets`, helper `project_child_fcc_check_icon()` chỉ phục vụ nhánh này và mapping ID compiler lỗi thời. Giữ helper làm sạch bullet và các fragment/tab đang được template gọi.
- Bỏ adapter `scripts/adapt-fractional-interactions.mjs` ghi đè JavaScript bằng logic form cũ; cập nhật workflow trong tài liệu. JavaScript WordPress được duy trì tại theme, compiler không sinh lại JS.
- Bỏ **162 rule CSS, 135 nhóm selector**, giảm **24.244 byte** CSS runtime. Xét selector nhóm/class/id và tham chiếu trong PHP/JS/JSON cùng markup template nguồn; giữ các pseudo selector không đủ chứng cứ, `.admin-bar`, styles editor/Customizer, keyframes và các thành phần đang dùng. Dọn rule tương ứng trong CSS nguồn để compiler không tạo lại.
- Bỏ **18 ảnh trong theme**, **4.151.349 byte** (3,96 MiB), gồm 10 bản `imageN.png` trùng byte với bản tên sản phẩm. Giữ ảnh còn dùng cho runtime/defaults, thư viện ảnh và nguồn brief/Excel. Manifest `scripts/fractional-asset-exclusions.json` ngăn compiler chép lại 18 bản bỏ khỏi runtime. Các ảnh cơ chế mới ngoài danh sách khảo sát ban đầu được giữ vì thuộc cập nhật khác đang chuẩn bị trong workspace, chưa xác nhận là dư.
- Đối chiếu và xóa qua `wp_delete_attachment` **5 attachment cũ: 9, 16, 17, 18, 19**, tổng file sao lưu/xóa **1.506.659 byte** (1,44 MiB). Guard kiểm tra tham chiếu từ metadata, nội dung và options, bảo vệ gallery/relationship; xác nhận các đường dẫn thuộc uploads. Không xóa file thủ công để lại metadata mồ côi. Dữ liệu field đang sử dụng không đổi.
- Khi chạy compiler trên bản sao riêng, phát hiện biến nguồn template bị form ghi đè thành `modal`; đổi sang `$form_source` để compiler đọc đúng nguồn qua nhiều trang. Không chạy compiler tái sinh site đang dùng.

Không xóa WordPress core, Kadence, ACF, dữ liệu khách/đơn hàng, fields/SEO đọc bằng key động, bộ starter sample hoặc lịch sử release. Không thay thiết kế được duyệt hay thông tin SMTP. Phần sửa nằm trong child theme, công cụ developer và metadata media không còn tham chiếu.

## Sao lưu và dữ liệu

Trước khi dọn, lưu bản sao riêng tại `C:\Users\anhvt\AppData\Local\Temp\fcc-cleanup-20261013164758`: theme/plugin, compiler/adapter, danh sách khảo sát, files và post/meta của 5 attachment. Không gộp bản sao vào repo/ZIP. Thử phục hồi cả 5 file/attachment qua WordPress trên database riêng: đạt; không chèn lại vào site đang dùng.

Nội dung CMS được đối chiếu bằng hash, loại trừ khóa editor WordPress. Không chạy reset hoặc initializer trên site đang dùng. Xóa attachment chỉ thực hiện trên site local được kiểm tra, không có migration tự xóa media khi cài ZIP ở site khác. Source defaults chỉ đổi version; các giá trị kinh doanh vẫn giữ.

## Packages và cập nhật

- `project-core-v0.3.26.zip`: folder gốc `project-core/`, 16 file runtime/defaults.
- `project-child-v0.3.26.zip`: folder gốc `project-child/`, 131 file runtime/assets/schema.
- SHA-256 core: `27BF612B78494C354A69E2896C78FDB82181904D15CC87FFDB8E90ABBCCB333D`
- SHA-256 theme: `07938E201DEA5CABC7F3F09572054F3D98103BCA9F3C229FF1E00A7CCD8A88A0`
- Dependencies kiểm tra: WordPress 7.1.3, PHP 8.0.30, MariaDB 10.4.32, Kadence 1.5.2, ACF Free 6.8.10. Cài dependency riêng từ nguồn chính thức; không đổi license, không thêm dependency/biến môi trường.

Backup riêng của môi trường đích trước cập nhật. Cập nhật đúng cặp ZIP, giữ child theme/core/ACF hoạt động, clear cache/tải lại trang để nhận assets mới. Không cần ACF Sync hoặc migration nội dung. WordPress updater có thể giữ file cũ tùy phương thức deploy: nếu deploy bằng copy chồng, dùng manifest và danh sách thay đổi để bỏ đúng ảnh/file đã loại khỏi package; không xóa uploads hàng loạt. Site mới vẫn dùng initializer versioned, chạy lại giữ dữ liệu đã sửa.

## Kiểm tra

- PHP runtime toàn bộ hai ZIP được lint khi build; compiler và các PHP sửa: đạt. JavaScript syntax và test luồng submit: đạt.
- **15 trang trước/sau**: nội dung chữ, link, URL ảnh/alt/srcset và hash dữ liệu CMS giữ nguyên. Các trang HTTP 200. Kiểm tra lại sau xóa ảnh: đạt.
- **24 form trên 15 trang**: trường không trùng, nonce/action/nguồn form/options hợp lệ. Editor native có xác thực: đủ 218 trường/16 tab, finder/assets vẫn hoạt động.
- Cài/kích hoạt hai ZIP trên database/content directory riêng: **53 kiểm tra khởi tạo, 305 catalog, 32 lựa chọn danh mục trang chủ đạt**.
- **44 kiểm tra server AJAX và 44 POST thường đạt**: lưu đơn, product/combo, chống dữ liệu không hợp lệ, lỗi lưu/mail và metadata chẩn đoán. Mail được chặn trong kiểm thử, không gửi thư hoặc đơn thử vào site thật.
- Compiler chạy hoàn tất 5 thiết kế trên bản sao riêng, không warning; 18 ảnh và các CSS cũ được kiểm tra không quay lại. Bản sao này không được dùng để ghi đè giao diện live.
- Hai ZIP đúng root, đủ runtime/defaults/assets, không config/secrets/dump/log/private uploads; toàn bộ file khớp source tại kiểm tra. Không PHP debug log trong fixture.
- Phục hồi 5 backup attachment trên database riêng và dọn fixture: đạt.

Chưa có browser kết nối để nghiệm thu trực quan viewport, console và click trạng thái tương tác. Việc dọn CSS dựa trên tham chiếu source/markup và bảo toàn cấu trúc các rule còn lại; HTTP/DOM không thay thế browser coverage. Database kiểm thử đã xóa, config tạm được thay bằng nội dung không chứa credentials; artifacts/junction TEMP ngoài repo/ZIP được giữ.

## Rollback

Khôi phục cặp code trước hoặc bản sao theme/plugin riêng để lấy lại JS/CSS/ảnh runtime nếu cần. Với 5 attachment đã xóa local, phục hồi file/post/meta từ backup qua WordPress, kiểm tra ID còn trống trước khi dùng lại ID cũ; không ghi đè ID đã được sử dụng và không chép toàn bộ database test vào site thật. Giữ nội dung/đơn hàng phát sinh sau khi dọn. Không tự gửi lại email hoặc reset nội dung.
