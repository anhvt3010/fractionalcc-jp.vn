# Chuyển template Fractional CC sang WordPress

> Ch?t 14/10/2026: to?n b? n?i dung/s?n ph?m/media/ph?c ?? hi?n t?i ?? ???c duy?t l?m [defaults 1.0.0](APPROVED-DEFAULTS.md). C?c release c? ?? x?a; [release ??u ti?n](../release/v1.0.0/RELEASE.md) l? h? s? hi?n h?nh. C?c m? t? ch? duy?t/local_sample c?a b?n tr??c d??i ??y l? l?ch s?.

## Trạng thái ngày 12/10/2026

Đã chuyển 5 mẫu giao diện trong `template/fractional-cc` sang PHP trong child theme hiện có, bổ sung mô hình nội dung và khởi tạo dữ liệu mặc định trong plugin hiện có. Parent Kadence, WordPress core và ACF không bị sửa source. Dùng ACF miễn phí, không thêm page builder, ACF PRO hoặc dependency frontend.

CSS và tài nguyên thiết kế được lấy từ template. HTML được tách thành template parts; các đoạn nội dung được đọc từ CMS và escape theo ngữ cảnh. Đây là chuyển cấu trúc/rendering, không thiết kế lại giao diện. Chưa kiểm tra bằng ảnh desktop/mobile hoặc console trình duyệt vì không có kết nối trình duyệt trong phiên thực hiện.

**Đã áp dụng dữ liệu và homepage vào WordPress hiện tại ngày 12/10/2026.** Người dùng chỉ định thông tin trang index và danh sách sản phẩm trong template làm mặc định active. Đã sao lưu riêng, chạy initializer có sẵn với `--set-homepage`, giữ các giá trị đã có và không xóa nội dung cũ. Trang chủ ID `30` dùng `templates/fractional-home.php`; danh mục/chính sách/cảm ơn lần lượt ID `31`/`32`/`33`, thông tin chung ID `14`. Có 10 sản phẩm, 1 combo và 39 bản ghi hoạt chất; tên website/slogan lấy từ defaults.

Hai thư mục vẫn trỏ cùng database; các thay đổi nội dung và homepage áp dụng vào database dùng chung này. URL local hoạt động qua `WP_HOME`/`WP_SITEURL` và rewrite riêng `/fractionalcc-jp.vn/`; URL lưu trong database vẫn là `/wordpress/`. Không tách database hoặc đổi tài khoản. Runtime giữ `local_sample` để các trang local tiếp tục `noindex`, không ngăn hiển thị giao diện. Luồng tiếp nhận đơn phía server và nghiệm thu bằng trình duyệt vẫn chưa hoàn tất.

## Cấu trúc triển khai

| Phần | Nơi quản lý |
| --- | --- |
| Bootstrap giao diện, asset loading và SEO | `wp-content/themes/project-child/inc/fractional.php` |
| Đọc nội dung, URL, media và format để trình bày | `inc/fractional-render.php` trong child theme |
| Components danh mục, gallery, hoạt chất và combo | `template-parts/fractional/components/` trong child theme |
| Trang chủ, danh mục, chính sách, cảm ơn | `templates/fractional-*.php` và `template-parts/fractional/` trong child theme |
| Chi tiết sản phẩm | `single-fcc_product.php` trong child theme |
| Schema ACF phục vụ giao diện | `inc/fractional-fields.json`, đăng ký qua PHP trong child theme |
| CSS, hình ảnh và JavaScript tương tác | `assets/fractional/` trong child theme |
| Mô hình sản phẩm, combo, hoạt chất, thông tin chung | `wp-content/plugins/project-core/src/Fractional/content.php` |
| Schema ACF thuộc dữ liệu nghiệp vụ | `src/Fractional/fields.php` trong plugin |
| Khởi tạo nội dung/media có version | `src/Fractional/setup.php`, `defaults/fractional.json` trong plugin |
| Hub quản lý nội dung | Menu **Nội dung website** do plugin cung cấp |

Giữ slug `project-child` và `project-core` để bám cấu trúc starter. Tên hiển thị có thương hiệu Fractional CC; version mã nguồn hiện tại là `0.3.1`. Defaults nội dung vẫn dùng version `0.3.0` vì bản cập nhật này chỉ thêm công cụ sửa, không thay nội dung mặc định. Native page/sample template trước đây được giữ lại, không xóa dữ liệu cũ.

## CMS và cách chỉnh sửa

### Sửa trong màn Tùy biến (0.3.1)

Mở [Tùy biến nội dung Fractional CC](http://localhost/fractionalcc-jp.vn/wp-admin/customize.php?autofocus%5Bpanel%5D=fcc_content). Panel **Nội dung Fractional CC** chia các ô theo trang/khu vực; chọn **Trang chủ · Khối đầu trang (Hero)** để sửa tiêu đề và mô tả. Có 799 trường text/textarea dùng cùng dữ liệu ACF của các trang và nội dung chung.

Trong bản xem trước, bấm bút chì cạnh đoạn chữ để mở đúng ô sửa, xem thay đổi rồi nhấn **Xuất bản** để lưu. Bản xem trước dùng Selective Refresh và cùng PHP renderer với website; thay đổi chưa xuất bản không ghi metadata hoặc xuất hiện trên trang công khai. Không tạo bản nội dung thứ hai trong theme mods/options. Sửa bằng CMS hay Tùy biến đều dùng cùng post metadata và field key ACF; các giá trị đã sửa được giữ nguyên.

Nội dung nút, liên kết, nhãn/option biểu mẫu và SEO được sửa trong panel bên trái; không chèn nút bút chì vào bên trong phần tử tương tác/biểu mẫu. Các trường cần cập nhật thuộc tính hoặc nhiều ngữ cảnh dùng refresh preview đầy đủ. Ảnh, hồ sơ sản phẩm, giá và combo tiếp tục quản lý trong CMS.

Chỉ người có quyền dùng Tùy biến và sửa bản ghi liên quan được truy cập. Native Customizer quản lý nonce/changeset; input được kiểm tra và sanitize, output được escape. Span và CSS công cụ sửa chỉ có trong preview được xác thực, không thay HTML/CSS của website công khai. `functions.php` chỉ thêm include; logic nằm trong `inc/fractional-customizer.php` và `inc/class-fcc-customize-setting.php`. Compiler ghi metadata shortcut trong schema và giữ nguyên field key/dữ liệu/thiết kế nguồn.

Đã kiểm tra qua HTTP trên database riêng: mở Customizer, preview không ghi dữ liệu, response Selective Refresh, Xuất bản và đồng bộ ACF, nonce sai, input không phải text, field giả, script injection và khách chưa đăng nhập. Đã chạy lại kiểm thử CMS/SEO trước đây và xác nhận binding trang chủ ID `30` trên site chính bằng preview trong bộ nhớ không ghi database. Chưa kiểm tra click bút chì/keyboard hoặc desktop/mobile bằng trình duyệt vì không có browser kết nối.

Sau khi khởi tạo trên đúng database được chọn, mở **Nội dung website**:

1. **Thông tin chung:** liên hệ, logo, hình ảnh, các nhãn dùng chung và trạng thái duyệt nội dung.
2. **Trang chủ / Danh mục / Chính sách / Cảm ơn:** chỉnh chữ và hình trong các tab theo section. Bố cục, class CSS, SVG và cấu trúc PHP thuộc developer.
3. **Sản phẩm:** thêm/sửa sản phẩm; ACF quản lý tên ngắn, giá, loại sản phẩm, nhu cầu, số CBMP, mô tả và các danh sách. Để giá trống khi chưa có dữ liệu, không dùng 0 để thay giá chưa có.
4. **Ảnh chi tiết:** dùng block Gallery trong editor sản phẩm; renderer lấy các attachment đã chọn. Trường ảnh ACF phục vụ ảnh chính.
5. **Hoạt chất:** là bản ghi riêng; chọn và kéo sắp xếp trong trường liên kết của sản phẩm. Không cần ACF Repeater hoặc PRO.
6. **Combo:** liên kết sản phẩm có thật và chỉnh giá, ảnh, nội dung/quà tặng đã được duyệt.

ACF dùng các trường primitive và relationship. Trường trình bày được sinh từ các đoạn text/thuộc tính của thiết kế, không yêu cầu khách sửa raw HTML. Trang dùng layout cố định có editor ACF; sản phẩm dùng Gutenberg cho Gallery. Không dùng nội dung Gutenberg tự do để thay toàn bộ cấu trúc landing page.

## Luồng đặt hàng còn cần chốt

Bản chuyển đổi hiện giữ luồng handoff của template: khách điền thông tin, kiểm tra nội dung, sao chép rồi gửi qua Zalo. JavaScript không còn lưu thông tin cá nhân của yêu cầu vào `localStorage`, không tạo mã đơn để giả lập đơn đã tiếp nhận.

**Chưa triển khai lưu yêu cầu trong CMS, thông báo email hoặc WooCommerce.** Không coi trang cảm ơn mẫu là bằng chứng doanh nghiệp đã nhận yêu cầu. Nội dung xác nhận/receipt và handler phía server sẽ được hoàn thiện theo lựa chọn của người dùng; form trong bản chuẩn bị chưa phải chức năng tiếp nhận đơn trên server.

## Nội dung và SEO

Dữ liệu mặc định lấy từ template và hồ sơ [WEBSITE-BRIEF.md](WEBSITE-BRIEF.md). Các đánh giá, giá gốc, ưu đãi, chính sách bổ sung và quyền dùng media chưa được tự xác nhận. Chế độ mặc định là **dữ liệu mẫu local – chờ duyệt**; các trang Fractional CC được `noindex` khi chưa duyệt. Không tự gỡ chặn index của môi trường kiểm thử. Trang cảm ơn luôn `noindex`.

Title/description có trường CMS; canonical, các URL nội bộ và URL sản phẩm dùng WordPress hiện tại. Không sao chép domain `fractional-cc.vn` từ HTML vào cấu hình website. Danh mục và chi tiết sản phẩm có HTML từ server; lọc danh mục hoạt động trên các card đã render, không tạo danh mục chỉ bằng JavaScript. Dữ liệu JSON phía frontend chỉ chứa các thuộc tính phục vụ tương tác, không gửi toàn bộ nội dung hoạt chất/hồ sơ.

## Công cụ developer

Chạy từ root bằng PowerShell. Các lệnh compile chỉ cập nhật source/gói defaults; không nhập database:

```powershell
& 'C:\xampp\php\php.exe' scripts/compile-fractional-template.php
```

JavaScript WordPress được duy trì trực tiếp tại `wp-content/themes/project-child/assets/fractional/main.js`; compiler không sinh lại file này. Adapter tương tác cũ đã bỏ vì ghi đè các sửa lỗi form bằng logic lỗi thời. JavaScript trong template gốc chỉ phục vụ bản mẫu tham chiếu.

`scripts/fractional-bindings.json` giữ ánh xạ vị trí nội dung sang field key để đổi default text mà không tự đổi khóa metadata. Giữ file này trong Git. Khi thay cấu trúc DOM/di chuyển nội dung, review bindings và lập migration nếu cần; không tự gán dữ liệu cũ vào ý nghĩa mới. Các template parts được sinh từ compiler; sửa thiết kế nguồn và biên dịch theo phạm vi được duyệt, không chỉnh đồng thời hai bản.

Chỉ chạy khởi tạo sau khi đã chọn đúng database và có ACF + child theme + plugin core đang active:

```powershell
& 'C:\xampp\php\php.exe' scripts/setup-fractional-site.php
```

Hoặc dùng nút khởi tạo ở hub CMS với tài khoản administrator. Initializer không tự đổi URL, homepage hoặc activate theme; chỉ tạo bản ghi/trường còn thiếu, giữ các giá trị đã có và lưu seed version. CLI có cờ `--set-homepage` cho lần áp dụng được chỉ định; cờ `--content-status=approved` chỉ dùng khi đã có phê duyệt nội dung cho lần khởi tạo mới. Nội dung đã khởi tạo còn chờ duyệt được đổi trạng thái trong CMS sau khi review.

Nếu đổi URL local sau khi tách database, phải cập nhật rewrite base đúng URL mới bằng WordPress và kiểm tra `.htaccess`; không dùng nguyên rewrite `/wordpress/` cho một URL khác. Không chạy lệnh khởi tạo/reset thử nghiệm trên database đang dùng chung.

Build theo [release/README.md](../release/README.md):

```powershell
.\scripts\build-release.ps1 -Version 0.3.0
```

Script kiểm tra version và lint PHP, tạo hai ZIP trong `release/v0.3.0/`, mỗi ZIP có một folder gốc đúng slug. Không ghi đè ZIP đã có; review/rebuild artifact theo version được chốt. Parent theme và ACF là dependency cài riêng. Defaults nằm trong ZIP plugin, schema/assets giao diện nằm trong ZIP theme. ZIP build được Git ignore; source dependencies của starter vẫn được theo dõi. Không thêm core/parent/plugin bên thứ ba vào hai ZIP.

## Kiểm thử đã thực hiện

Trên WordPress kiểm thử có database riêng, PHP XAMPP `8.0.30`, Kadence và ACF miễn phí `6.8.10`:

- Khởi tạo đủ 10 sản phẩm, 1 combo, 39 bản ghi hoạt chất và media được tham chiếu.
- Chạy lại không tạo trùng và bảo toàn nội dung đã sửa; không có quyền bị từ chối khởi tạo.
- Đăng nhập administrator thật qua HTTP và mở hub/editor ACF.
- Lưu ACF qua form editor WordPress cập nhật được nội dung render; nonce ACF sai không ghi đè dữ liệu.
- Input của editor trang chủ được kiểm tra ở mức 503 controls, dưới `max_input_vars=1000` của XAMPP.
- Nội dung CMS được escape; khách chưa đăng nhập không vào CMS.
- Trang chủ, danh mục, chính sách, cảm ơn và chi tiết sản phẩm trả HTTP 200; một H1, một canonical; danh mục có đủ 10 card trong HTML server.
- PHP lint và JavaScript syntax checks đã chạy; response được kiểm tra không lộ warning/fatal.

Sau khi áp dụng vào site chính đã kiểm tra homepage/template, đủ 10 sản phẩm, 1 combo và 39 hoạt chất; năm loại trang có HTTP 200, một H1, CSS thiết kế, không có field chưa thay hoặc warning/fatal trong response. Có 29 tài nguyên local được tham chiếu trong các trang kiểm tra, tất cả trả HTTP 200.

Chưa kiểm tra console, thao tác JavaScript trong trình duyệt, desktop/mobile bằng ảnh, editor Gallery bằng UI hoặc email delivery vì chưa có kết nối trình duyệt. Luồng tiếp nhận yêu cầu phía server vẫn cần hoàn thiện. Chưa nghiệm thu production.
