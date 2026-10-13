# Rà soát ảnh và nội dung chưa được quản lý

> Ch?t 14/10/2026: to?n b? n?i dung/s?n ph?m/media/ph?c ?? hi?n t?i ?? ???c duy?t l?m [defaults 1.0.0](APPROVED-DEFAULTS.md). C?c release c? ?? x?a; [release ??u ti?n](../release/v1.0.0/RELEASE.md) l? h? s? hi?n h?nh. C?c m? t? ch? duy?t/local_sample c?a b?n tr??c d??i ??y l? l?ch s?.

> Cập nhật 14/10/2026: các khoảng trống quản lý ảnh/nội dung trong bảng dưới đã được xử lý ở bản 0.3.29. Xem [hướng dẫn quản lý](CONTENT-MANAGEMENT.md) và [release/kiểm thử](../release/v1.0.0/RELEASE.md). Báo cáo này giữ lại hiện trạng trước cập nhật; các giới hạn về cấu trúc/số lượng khối vẫn được ghi trong hướng dẫn.

Ngày 13/10/2026, source/runtime 0.3.28 tại `D:\xampp\htdocs\fractionalcc-jp.vn`. Phạm vi: trang chủ, danh mục, chi tiết sản phẩm, Vấn đề làn da, chính sách, cảm ơn và các phần dùng chung. Đây là báo cáo kiểm tra; không chuyển nội dung vào CMS hoặc sửa dữ liệu mặc định trong tác vụ này.

## Kết quả chính

Website **chưa quản lý toàn bộ ảnh và nội dung qua CMS**. Các thiếu sót lớn nhất là nội dung/ảnh của trang Vấn đề làn da, phần Công nghệ mới trên trang chủ, thông tin giao hàng cạnh form và một số thông tin chung chưa đồng bộ.

| Ưu tiên | Vị trí | Phần còn thiếu | Bằng chứng và ảnh hưởng |
| --- | --- | --- | --- |
| Cao | Vấn đề làn da | 4 ảnh tình trạng da và alt | `template-parts/fractional/concerns/section-01.php:76`, `:139`, `:202`, `:265` gọi file ảnh trong theme trực tiếp. Không có trường ảnh của 4 tình trạng trong schema/ACF. Upload ảnh khác vào Media không thay ảnh đang hiển thị. |
| Cao | Vấn đề làn da | Hero, tên tab, mô tả tình trạng, dấu hiệu, nguyên nhân, giải pháp, tiêu đề/mô tả danh sách, nguyên tắc và khối tư vấn | Cùng template viết text trực tiếp. Editor hiện chỉ có nhóm **Gợi ý phác đồ – sản phẩm theo tình trạng da**, quản lý lựa chọn sản phẩm/combo. Việc chọn được sản phẩm không có nghĩa các đoạn nội dung/ảnh còn lại đã được quản lý. |
| Cao | Trang chủ → Công nghệ / Điểm nổi bật | 2 ảnh minh họa công nghệ và chu trình | `home/section-04.php:6` đọc `fcc_ui_mechanism_breakthrough_image`, `:12` đọc `fcc_ui_mechanism_routine_image`. Cả 2 khóa chưa có field definition, chưa có metadata trên trang chủ và không có control ở editor. Hiện dùng fallback `mechanism-breakthrough.jpg`, `mechanism-skincare-routine.jpg`. Alt/caption cũng viết cố định. |
| Cao | Trang chủ → Công nghệ / Điểm nổi bật | Mô tả của 4 thẻ và phần giải thích mở rộng | `home/section-04.php:50`, `:71`, `:88`, `:112`, phần từ `:131` trở đi. Header của section và tiêu đề 4 thẻ đã lấy ACF, nhưng mô tả và các khối PDRN/Retinol/NMN, thông số AX4, trải nghiệm sử dụng còn viết cố định. Không đồng bộ với bản ghi Hoạt chất/Sản phẩm. |
| Cao | Trang chủ và chi tiết sản phẩm → Cạnh form đặt hàng | Chính sách giao hàng/thanh toán tóm tắt và một số tiêu đề/nhãn | `home/section-10.php:62`, `:109`, `:119` trở đi. Tiêu đề sidebar, thời gian 2–5 ngày, phí vận chuyển, phương thức thanh toán, lời nhắc kiểm tra hàng và các badge chưa đọc field quản lý. Sửa trang Chính sách không tự thay phần tóm tắt này. Template được dùng lại trên trang sản phẩm. |
| Cao | Footer, danh mục, hồ sơ sản phẩm và sidebar form | Hotline hiển thị chưa đồng bộ hoàn toàn | Link `tel:` dùng cấu hình chung, nhưng một số text vẫn là `0985 561 862`: footer `shared/part-dca07e40c97c.php:20`, `part-8aa9a75ff5cb.php:18`, `part-545c08e749ed.php:18`; `catalog/section-04.php:20`; `product/section-02.php:244`; `home/section-10.php:100`; trang Chính sách tại `policies/section-01.php:156`, `:213`, `:223`. Đổi số liên hệ có thể làm số bấm gọi và số nhìn thấy khác nhau. |
| Cao | Danh mục → Thẻ combo | Dòng đánh giá combo | `components/combo-card.php:45` dùng chuỗi cố định **5.0 (1.200+ đánh giá)** khi hiển thị trên danh mục. Bản trang chủ có đọc `fcc_spotlightRating`, nhưng không được dùng cho nhánh danh mục. Đã thấy chuỗi này trong HTML thật của danh mục. |
| Vừa | Vấn đề làn da → SEO | Tiêu đề và mô tả SEO | `fcc_concerns_seo_title`, `fcc_concerns_seo_description` có metadata và đang được renderer đọc, nhưng không có ACF field/control. Vì vậy có dữ liệu, chưa có cách chỉnh bình thường trong editor. |
| Vừa | Chia sẻ mạng xã hội và favicon | Ảnh chia sẻ cho các trang không phải sản phẩm; biểu tượng website | `inc/fractional.php:125` luôn dùng `combo-c-cx4-gold.png` cho các trang không phải sản phẩm; `:129` in favicon `bui-dang-jp-logo.png` trực tiếp. Không nối với field ảnh Hero/logo đang quản lý. Chọn ảnh/logo khác trong CMS không thay 2 đầu ra này. Trang sản phẩm lấy ảnh chia sẻ từ ảnh chính của sản phẩm. |
| Thấp | Danh mục, thẻ sản phẩm/combo và giao diện tương tác | Một số nhãn nút, placeholder, thông báo | Ví dụ `catalog/section-03.php:22`, `:30`, `:38`, `:45`, `:46`; chữ Chi tiết/Đặt mua trong card; các thông báo validation trong `assets/fractional/main.js`. Đây chủ yếu là nhãn giao diện do developer quản lý, cần bổ sung field nếu yêu cầu khách tự sửa toàn bộ chữ. |
| Thấp | Ảnh và CTA chi tiết sản phẩm | Chữ callout công nghệ và tiền tố nút mua | `inc/fractional-render.php:160`, `:163`, `:164` sinh các nhãn cố định như MUA NGAY, CÔNG NGHỆ CHUẨN NHẬT, CHUẨN TOKYO. Giá/thông tin vi kim lấy từ sản phẩm, nhưng các nhãn này chưa có ô sửa riêng. |

Các file template trong bảng nằm dưới `wp-content/themes/project-child/`, trừ khi đã ghi rõ thư mục khác. Line numbers là vị trí source tại thời điểm rà soát.

## Đã quản lý, nhưng cần lưu ý cách sử dụng

- **Đánh giá trang chi tiết sản phẩm:** đang đọc một dòng dùng chung `fcc_ui_2bd71effeb0585` trong Thông tin chung, hiện là “4.95 / 5.0 (470+ đánh giá người dùng)”. Các field `fcc_rating`/`fcc_reviewCount` của từng sản phẩm được card danh mục sử dụng, nhưng không phải nguồn cho dòng đánh giá ở trang chi tiết. Đây là vấn đề binding theo ngữ cảnh, không phải thiếu toàn bộ field đánh giá.
- **Ảnh sản phẩm/gallery:** đổi được ảnh chính bằng ACF và ảnh chi tiết bằng block Gallery. Alt của phần lớn card/ảnh chi tiết được tạo từ tên/tên ngắn sản phẩm, chưa dùng alt Media riêng; thẻ chủ lực có đọc alt Media. Nội dung Gutenberg dạng paragraph thêm vào sản phẩm hiện không được render tự do: API chỉ trích block `core/gallery` từ `post_content`. Các mô tả/công dụng/cách dùng vẫn quản lý bằng ACF.
- **FAQ, feedback và chu trình trang chủ:** nội dung/ảnh hiện có phần lớn đã có field, nhưng số lượng/thứ tự khối vẫn do template quyết định. Chưa có công cụ tùy ý thêm/xóa/sắp xếp mục như một collection. Ảnh demo chu trình đã quản lý, nhưng nút play đang mở Zalo thay vì một video URL riêng (`home/section-06.php:325`).
- **Menu:** nhãn đã có field `fcc_nav_*`; tuyến URL, thứ tự và số mục nằm trong `shared/header.php`. Không dùng menu WordPress để thêm/xóa/đổi đích tự do. Đây phù hợp với bố cục do developer kiểm soát hiện tại; chỉ cần mở rộng nếu khách cần thao tác đó.
- **SEO sản phẩm:** title lấy tên sản phẩm + tên thương hiệu, description lấy tagline, ảnh chia sẻ lấy ảnh sản phẩm. Nội dung nguồn chỉnh được nhưng chưa có các giá trị SEO riêng để ghi đè theo từng sản phẩm.
- **Một số trường tham chiếu/lưu trữ:** đã có ACF nhưng có hướng dẫn không hiển thị; không coi mọi field chưa xuất hiện ngoài website là lỗi. Cần tránh nhầm chúng với các đoạn viết cố định đang hiển thị.

## Những phần đã quản lý đúng

| Nội dung | Nơi chỉnh |
| --- | --- |
| Hero: chữ, nền, ảnh sản phẩm, vị trí nền | Trang chủ → Khối đầu trang; Customizer dùng cùng metadata |
| Logo và phần lớn chữ header/footer/modal | Thông tin chung → Nội dung theo vị trí |
| Ảnh giới thiệu thương hiệu / caption | Trang chủ → Giới thiệu Fractional CC |
| 7 ảnh feedback và nội dung tương ứng hiện có | Thông tin chung / Trang chủ theo field tương ứng |
| Tên, ảnh chính, giá, quy cách, công dụng, đối tượng, thành phần, cách dùng, lưu ý, CBMP | Bản ghi Sản phẩm |
| Gallery sản phẩm | Block Gallery trong editor sản phẩm |
| Ảnh, sản phẩm thành phần, giá và thông tin combo | Bản ghi Combo |
| Thẻ thành phần / hoạt chất | Bản ghi Hoạt chất + relationship của sản phẩm |
| 2 thẻ chủ lực và 6 mục danh mục trang chủ | Thiết lập trên từng Sản phẩm/Combo |
| Sản phẩm/combo cho 4 tình trạng da | Trang Vấn đề làn da → Gợi ý phác đồ |
| Phần lớn nội dung chính sách và cảm ơn | Trang Chính sách / Cảm ơn qua ACF |

## Cách xác minh

- Kiểm tra active Project Child/Kadence, Project Core và ACF trên XAMPP PHP 8.2.12.
- Đối chiếu template/component/helper, schema JSON và ACF registry runtime: có 14 nhóm, 841 field có tên trên cấu hình frontend; con số này không chứng minh toàn bộ nội dung đã editable.
- Mở editor WordPress có xác thực qua HTTP cho Trang chủ, Thông tin chung và Vấn đề làn da; cả 3 HTTP 200. Confirm 2 field ảnh Công nghệ và 2 field SEO Vấn đề làn da không có control; 4 relationship phác đồ có control.
- Đối chiếu HTML public của 5 trang: tất cả HTTP 200; xác nhận 2 ảnh Công nghệ, 4 ảnh tình trạng, ảnh chia sẻ/favicon cố định và rating combo danh mục.
- Mô phỏng thay số liên hệ bằng filter metadata trong **một tiến trình PHP**, không ghi database: footer/trang danh mục/khối form đã đổi `tel:` nhưng vẫn còn nhãn số cũ. Kết thúc tiến trình và bỏ filter; không thay cấu hình liên hệ thật.
- Phiên admin tạm dùng để đọc editor được hủy sau kiểm tra. Không gửi form, email hoặc đơn thử. Không đọc/in credentials.

## Thứ tự bổ sung hợp lý

1. Quản lý đầy đủ ảnh/nội dung/SEO của trang Vấn đề làn da; giữ 4 lựa chọn sản phẩm đã có.
2. Đăng ký 2 field ảnh và chuyển phần nội dung Công nghệ đang cố định sang ACF, tái sử dụng dữ liệu sản phẩm/hoạt chất khi cùng ý nghĩa.
3. Đồng bộ hotline và thông tin giao hàng, xử lý binding đánh giá sản phẩm/combo để không còn nguồn cố định khác nhau giữa các trang.
4. Thêm ảnh chia sẻ/favicon, rồi các nhãn giao diện tùy theo nhu cầu chỉnh sửa của khách.

Giữ giao diện và giá trị đang hiển thị khi chuyển nguồn dữ liệu; không tự tạo claims, rating hoặc nội dung mới. Việc có field quản lý không đồng nghĩa nội dung đã được duyệt. Các đề xuất phác đồ local vẫn chưa được đưa vào dữ liệu mặc định khởi tạo.
