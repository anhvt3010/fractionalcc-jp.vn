# Thông tin ban đầu website Fractional CC

> Ch?t 14/10/2026: to?n b? n?i dung/s?n ph?m/media/ph?c ?? hi?n t?i ?? ???c duy?t l?m [defaults 1.0.0](APPROVED-DEFAULTS.md). C?c release c? ?? x?a; [release ??u ti?n](../release/v1.0.0/RELEASE.md) l? h? s? hi?n h?nh. C?c m? t? ch? duy?t/local_sample c?a b?n tr??c d??i ??y l? l?ch s?.

> Cập nhật 13/10/2026: người dùng đã duyệt [Excel sản phẩm mới](<../template/fractional-cc/List sản phẩm fractional CC.xlsx>) làm nguồn dữ liệu và defaults. Giá/nội dung mới thay bảng giá cũ dưới đây; xem [ánh xạ và trạng thái kiểm tra từng trường](PRODUCT-DATA-EXCEL.md). Các mục thiếu và khác biệt lịch sử vẫn được giữ để tra cứu.

Hồ sơ lập ngày 12/10/2026 từ `template/fractional-cc`, dùng làm nguồn nội dung và phạm vi cho website WordPress. Lần lập hồ sơ ban đầu chỉ cập nhật tài liệu. Sau đó người dùng chỉ định dùng nội dung đã chuẩn bị ở trang index và danh sách sản phẩm làm mặc định active; nội dung đã được khởi tạo vào CMS và đặt trang chủ Fractional CC trên WordPress hiện tại. Các thông tin thiếu vẫn giữ dấu vết nguồn, không tự điền thêm. Xem [FRACTIONAL-WORDPRESS.md](FRACTIONAL-WORDPRESS.md) cho runtime và kiểm thử.

Thông tin có nguồn được ghi lại theo tài liệu cung cấp. Các mục thiếu dữ liệu, khác nhau giữa các nguồn hoặc chỉ xuất hiện trong bản HTML được ghi riêng để xác nhận trước khi đưa lên website.

## 1. Nguồn thông tin

| Nguồn | Nội dung sử dụng |
| --- | --- |
| [brief.docx](../template/fractional-cc/brief.docx) | Thương hiệu, doanh nghiệp, thông điệp, trọng tâm landing page, hotline/email, FAQ, giao hàng/thanh toán, giá đã cung cấp |
| [Danh sách sản phẩm Excel](../template/fractional-cc/List_gioi_thieu_san_pham_doc_quyen_updated.xlsx) | Sheet `01_Tong quan`, hàng 4–13: 10 sản phẩm, xuất xứ, thành phần/công dụng trong nguồn và số CBMP |
| [index.html](../template/fractional-cc/index.html) | Giao diện trang chủ, menu, vị trí nội dung, metadata đang có trong mẫu |
| [san-pham.html](../template/fractional-cc/san-pham.html), [chi-tiet-san-pham.html](../template/fractional-cc/chi-tiet-san-pham.html) | Mẫu danh mục và chi tiết sản phẩm |
| [chinh-sach.html](../template/fractional-cc/chinh-sach.html), [cam-on.html](../template/fractional-cc/cam-on.html) | Mẫu trang chính sách và cảm ơn; một số cam kết cần xác nhận lại |
| [data.js](../template/fractional-cc/assets/js/data.js), [products_parsed.json](../template/fractional-cc/products_parsed.json) | Đối chiếu dữ liệu đã chuyển từ nguồn và ánh xạ ảnh; không dùng để tự xác nhận đánh giá, giá gốc hay cam kết bổ sung |
| [main.js](../template/fractional-cc/assets/js/main.js) | Đối chiếu luồng yêu cầu đặt hàng hiện có trong template |

Ưu tiên brief cho yêu cầu/nội dung thương hiệu và Excel cho hồ sơ sản phẩm. Khi dữ liệu khác nhau, giữ dấu vết nguồn và chờ xác nhận; không tự biến nội dung thêm trong HTML/JS thành dữ kiện đã duyệt. Các liên kết Google Sheets/Drive trong brief được giữ làm đầu mối; chưa truy cập hoặc xác minh nội dung bên ngoài trong lần này.

## 2. Thương hiệu và liên hệ

| Trường | Giá trị ban đầu | Nguồn / trạng thái |
| --- | --- | --- |
| Thương hiệu | Fractional CC | Brief và Excel |
| Tên website dự kiến | Fractional CC Nhật Bản | Theo title/nhận diện trong template; chưa áp dụng vào WordPress |
| Nhóm sản phẩm | Mỹ phẩm chăm sóc da | Excel: `Mỹ Phẩm`; brief mô tả chăm sóc da |
| Xuất xứ | Nhật Bản | Theo brief và cột F của Excel |
| Đơn vị phân phối | Công ty TNHH BUI DANG-JP | Brief |
| Vai trò phân phối | Phân phối độc quyền tại Việt Nam | Theo nội dung brief; giấy tờ chứng minh chưa được đối chiếu |
| Thông điệp | Chăm sóc làn da khoa học – nâng niu vẻ đẹp tự nhiên. | Brief |
| Ngôn ngữ nội dung | Tiếng Việt | Brief và `lang="vi"` trong HTML |
| Đơn vị tiền tệ hiển thị | VNĐ | Bảng giá trong brief |
| Hotline dạng dữ liệu | 0985561862 | Brief |
| Hotline hiển thị | 0985 561 862 | Template |
| Email liên hệ | jpbuidangco.ltd@gmail.com | Brief |
| Kênh Zalo | `https://zalo.me/0985561862` | Template; cùng số hotline trong brief |
| Địa chỉ doanh nghiệp/văn phòng | Chưa có dữ liệu | Không tự điền Hà Nội/TP.HCM hoặc địa chỉ giả |
| Mã số thuế / đăng ký doanh nghiệp | Chưa có dữ liệu | Cần bổ sung nếu sử dụng trên website |
| Messenger / Facebook | Chưa có URL chính thức | Brief yêu cầu kênh liên hệ; `messengerLink` trong dữ liệu mẫu đang trống |
| Tài khoản ngân hàng | Chưa có dữ liệu | Không tự điền số tài khoản/chủ tài khoản |
| Domain public | Chưa chốt | HTML dùng `fractional-cc.vn`; tên thư mục dự án là `fractionalcc-jp.vn`, chưa đủ để xác nhận domain |

Môi trường hiện tại là XAMPP tại `C:\xampp\htdocs\fractionalcc-jp.vn`; tạm thời không dùng Docker. URL local lấy theo cấu hình thực tế, không dùng domain trong template để tự đổi Site URL hoặc canonical.

## 3. Mục tiêu và nội dung trang chủ

Theo brief, website giới thiệu thương hiệu và danh mục Fractional CC, làm nổi bật sản phẩm chủ lực, hỗ trợ khách chọn sản phẩm và gửi yêu cầu tư vấn/đặt hàng tới BUI DANG-JP. Phạm vi này chưa xác lập yêu cầu giỏ hàng, cổng thanh toán hoặc WooCommerce.

### Nội dung nhận diện đầu trang

- Tiêu đề chính: **Đánh thức vẻ đẹp làn da với công nghệ chăm sóc da Nhật Bản**.
- Tiêu đề phụ: **Khám phá giải pháp chăm sóc da cùng Fractional CC – thương hiệu mỹ phẩm Nhật Bản**.
- Logo: Fractional CC và logo công ty BUI DANG-JP.
- Hình ảnh chủ đạo theo brief: bộ sản phẩm, serum PDRN, kết cấu sản phẩm và hình ảnh làn da.
- CTA: **MUA NGAY**.

### Trọng tâm sản phẩm

Giữ hai điểm nhấn theo brief: **Mandelic Shot Peel Essence** và **combo Needle Serum C–CX4**. Combo được yêu cầu làm nổi bật; không tự thay bằng một sản phẩm khác. Bảng giá ở mục 5 chỉ ghi các giá đã có trong brief.

### Các nhóm nội dung trong brief

1. Banner nhận diện thương hiệu.
2. Hai sản phẩm/combo chủ lực.
3. Giới thiệu Fractional CC và thông điệp thương hiệu.
4. Điểm nổi bật về thành phần, công nghệ, trải nghiệm sử dụng và danh mục chăm sóc da; phải theo đúng hồ sơ từng sản phẩm.
5. Danh mục sản phẩm.
6. Hướng dẫn sử dụng và chu trình chăm sóc da theo nguồn nhà sản xuất.
7. Feedback khách hàng và review KOL/KOC có nguồn thật, có quyền sử dụng.
8. Thông tin doanh nghiệp, phân phối, nguồn gốc và giấy tờ liên quan.
9. FAQ.
10. Form yêu cầu đặt hàng, thông tin liên hệ, giao hàng/thanh toán và CTA cuối trang.

Danh sách này là bản đồ nội dung, không yêu cầu đổi thứ tự hoặc layout template hiện có. Chưa có tài liệu xác minh cho feedback, video, kết quả sử dụng hoặc các số liệu marketing; giữ chúng ở trạng thái chờ bổ sung, không đưa vào dữ liệu mặc định như dữ kiện thật.

CTA cuối trang theo brief: **KHÁM PHÁ FRACTIONAL CC – CHĂM SÓC LÀN DA THEO CÁCH CỦA BẠN.**

## 4. Các trang và menu tham chiếu

| Trang | Template nguồn | Vai trò |
| --- | --- | --- |
| Trang chủ | `index.html` | Landing page thương hiệu và sản phẩm/combo chủ lực |
| Sản phẩm | `san-pham.html` | Danh mục 10 sản phẩm đơn lẻ và combo C–CX4 |
| Chi tiết sản phẩm | `chi-tiet-san-pham.html` | Mẫu trình bày cho từng sản phẩm; không coi là một sản phẩm riêng |
| Chính sách | `chinh-sach.html` | Các nhóm giao hàng, thanh toán, đổi trả, bảo mật và hỗ trợ; nội dung cần đối chiếu mục 6 và 10 |
| Cảm ơn | `cam-on.html` | Mẫu bước tiếp theo sau yêu cầu đặt hàng; chỉ xác nhận tiếp nhận khi có cơ chế tiếp nhận thật |

Menu/đích liên kết hiện có trong trang chủ: sản phẩm & combo, công nghệ, thương hiệu, danh mục, chu trình chăm sóc da, đánh giá, thông tin phân phối/pháp lý, FAQ, danh mục đầy đủ và chính sách. Giữ cấu trúc UI hiện có; tên nhãn chứa tuyên bố chưa có hồ sơ cần được duyệt trước khi dùng. URL/permalink WordPress chưa được thiết lập trong lần này.

## 5. Danh mục và giá có nguồn

Tên trong bảng dưới rút gọn để tra cứu. Tên sản phẩm đầy đủ lấy từ cột D của Excel; các tên bắt đầu bằng Serum/Mặt nạ FractionalCC. Số CBMP chép từ cột L, chỉ ghi nhận theo file cung cấp, chưa xác minh với hồ sơ gốc hoặc cơ quan cấp. Bảng Excel không có cột giá; giá ở đây lấy từ phần Giá trong brief.

| Sản phẩm | Giá theo brief (VNĐ) | CBMP theo Excel | Dòng Excel |
| --- | ---: | --- | ---: |
| Mandelic Shot Peel Essence | Chưa có | 312953/26/CBMP-QLD | 4 |
| Needle Serum A Face Mask | Chưa có | 312932/26/CBMP-QLD | 5 |
| Needle Serum A | Chưa có | 312955/26/CBMP-QLD | 6 |
| Needle Serum AX4 | Chưa có | 312956/26/CBMP-QLD | 7 |
| Needle Serum C Face Mask | Chưa có | 312931/26/CBMP-QLD | 8 |
| Needle Serum C | 593.100 | 312954/26/CBMP-QLD | 9 |
| Needle Serum CX4 | 682.500 | 312957/26/CBMP-QLD | 10 |
| PDRN Shot 1000+ Toning Essence | 506.800 | 312958/26/CBMP-QLD | 11 |
| PDRN Shot Lip Essence | 379.776 | 312950/26/CBMP-QLD | 12 |
| PDRN Shot Lip Plumper | 379.776 | 312959/26/CBMP-QLD | 13 |

**Combo chủ lực:** brief ghi “Combo Siêu tinh chất vikim giảm thâm Fractional CC | Serum C- CX4a”, giá **1.237.500 VNĐ**. Template dùng tên C–CX4, gồm 01 Needle Serum C và 01 Needle Serum CX4. Cần xác nhận cách viết `CX4a` trong brief trước khi chốt tên hiển thị; không tạo thêm sản phẩm CX4a từ khác biệt này.

Không đặt giá chưa có thành 0. Chưa dùng các giá gốc gạch ngang, tỷ lệ giảm giá, quà tặng hoặc nhãn “bán chạy” trong JS làm dữ kiện đã được xác nhận. Dung tích, hướng dẫn dùng và các mô tả hoạt chất mở rộng trong template cần đối chiếu nhãn/hồ sơ sản phẩm trước khi sử dụng; không suy diễn hiệu quả điều trị.

## 6. Giao hàng, thanh toán và tiếp nhận yêu cầu

Nội dung có trong brief:

- Giao hàng toàn quốc, thời gian dự kiến **2–5 ngày làm việc**.
- Phí vận chuyển **thông báo khi xác nhận đơn hàng**.
- Thanh toán bằng **chuyển khoản ngân hàng hoặc COD**.
- Khách hàng kiểm tra sản phẩm khi nhận hàng.
- Form: họ tên, số điện thoại, sản phẩm quan tâm; kênh liên hệ Zalo, Messenger hoặc hotline.

Template có thêm địa chỉ nhận hàng và ghi chú dạng tùy chọn, cùng ô đồng ý nhận tư vấn/xác nhận đơn. Messenger chưa có URL chính thức. Cần xác nhận kênh nhận yêu cầu, người xử lý và nội dung đồng ý trước triển khai.

Luồng JS hiện có chỉ chuẩn bị nội dung yêu cầu, lưu thông tin cục bộ trên trình duyệt và hướng dẫn khách sao chép/gửi qua Zalo. Chưa thấy cơ chế gửi đơn đến server trong handler đã đối chiếu. Vì vậy trang cảm ơn không chứng minh doanh nghiệp đã nhận đơn; các câu “đã ghi nhận trên hệ thống” hoặc “liên hệ trong 15–30 phút” chưa được dùng làm cam kết ban đầu.

Chính sách đổi trả, hoàn tiền, miễn phí vận chuyển, thời gian phản hồi, phạm vi đồng kiểm và chính sách dữ liệu cá nhân chi tiết chưa được brief xác lập. Không tự lấy toàn bộ nội dung `chinh-sach.html` làm chính sách chính thức; xem các khác biệt ở mục 10.

## 7. FAQ có sẵn trong brief

Giữ 6 nhóm câu hỏi và nội dung nguồn sau khi nhập CMS; không thêm cam kết về hiệu quả hoặc an toàn. Dưới đây là tóm tắt để xác định nội dung, bản đầy đủ nằm trong `brief.docx`.

| Câu hỏi | Nội dung nguồn |
| --- | --- |
| Fractional CC là thương hiệu gì? | Thương hiệu chăm sóc da có dòng sản phẩm ứng dụng công nghệ vi kim; giới thiệu nhu cầu dưỡng sáng, chăm sóc và hỗ trợ tái tạo theo brief |
| Sản phẩm có nguồn gốc từ đâu? | Nhật Bản; BUI DANG-JP phân phối độc quyền tại Việt Nam theo brief |
| Nên chọn sản phẩm nào theo nhu cầu chăm sóc da? | Theo công dụng từng sản phẩm và nhu cầu cá nhân; liên hệ BUI DANG-JP để được tư vấn |
| Cách sử dụng sản phẩm như thế nào? | Theo hướng dẫn nhà sản xuất và bao bì; giữ nguyên nội dung lưu ý trong brief, không tự xây lịch dùng |
| Có thể sử dụng kết hợp các sản phẩm không? | Theo hướng dẫn của từng sản phẩm; giữ lưu ý về lựa chọn sản phẩm kết hợp trong brief |
| Làm thế nào để mua hàng chính hãng từ BUI DANG-JP? | Qua kênh bán hàng chính thức, được tư vấn lựa chọn và cung cấp thông tin nguồn gốc theo brief |

## 8. Tài nguyên và thiết kế tham chiếu

Toàn bộ đường dẫn ảnh dưới đây nằm trong `template/fractional-cc/`. Giữ template làm nguồn tham chiếu thiết kế; sự tồn tại của file không thay thế việc xác nhận quyền sử dụng hoặc độ chính xác của hình.

| Tài nguyên | Đường dẫn tương đối |
| --- | --- |
| Logo thương hiệu | `assets/images/brand/fractional-cc-logo.png` |
| Logo thương hiệu nền tối | `assets/images/brand/fractional-cc-logo-white.png` |
| Logo đơn vị phân phối | `assets/images/brand/bui-dang-jp-logo.png` |
| File favicon sẵn có | `assets/images/brand/logo-favicon.png`; HTML hiện đang dùng logo BUI DANG-JP, chưa đổi lựa chọn |
| Ảnh combo chủ lực | `assets/images/banner/combo-c-cx4.png` |
| Banner và hình phụ trợ | `assets/images/banner/` |
| Ảnh sản phẩm | `assets/images/products/` |
| Ảnh cơ chế | `assets/images/mechanism/mechanism-infographic.jpg` |
| Ảnh feedback tham chiếu | `assets/images/feedback/`; chưa có hồ sơ nguồn/đồng ý sử dụng được đối chiếu |

Ánh xạ ảnh sản phẩm hiện có:

| Sản phẩm | Tên file trong `assets/images/products/` |
| --- | --- |
| Mandelic Shot Peel Essence | `mandelic-peel-essence.png` |
| Needle Serum A Face Mask | `needle-mask-a.png` |
| Needle Serum A | `needle-serum-a.png` |
| Needle Serum AX4 | `needle-serum-ax4.png` |
| Needle Serum C Face Mask | `needle-mask-c.png` |
| Needle Serum C | `needle-serum-c.png` |
| Needle Serum CX4 | `needle-serum-cx4.png` |
| PDRN Shot 1000+ Toning Essence | `pdrn-toning-essence.png` |
| PDRN Shot Lip Essence | `pdrn-lip-essence.png` |
| PDRN Shot Lip Plumper | `pdrn-lip-plumper.png` |

Theo `assets/css/style.css`, template dùng Plus Jakarta Sans cho chữ sans, Playfair Display cho chữ serif; màu nền trắng `#FFFFFF`, nền phụ `#F9F8F6`, màu chính `#121212`, màu nhấn vàng `#C5A059`. Đây là ghi nhận thiết kế hiện có, không phải thiết kế mới. Không tự đổi font, màu, layout, khoảng cách hoặc tương tác.

## 9. Thông tin SEO ban đầu

- Title trang chủ theo mẫu: **Fractional CC Nhật Bản | Phân Phối Độc Quyền Bởi BUI DANG-JP**.
- Description theo mẫu: **Đánh thức vẻ đẹp làn da với công nghệ chăm sóc da Nhật Bản. Khám phá giải pháp chăm sóc da cùng Fractional CC – thương hiệu mỹ phẩm Nhật Bản được phân phối độc quyền bởi BUI DANG-JP tại Việt Nam.**
- H1 nội dung theo brief: **Đánh thức vẻ đẹp làn da với công nghệ chăm sóc da Nhật Bản**.
- Ảnh chia sẻ đang tham chiếu trong HTML: `assets/images/banner/combo-c-cx4.png`.
- Domain/canonical/URL ảnh tuyệt đối: chờ xác nhận domain public; không sao chép mặc định `fractional-cc.vn` sang WordPress.
- Metadata/schema phải phản ánh nội dung thực tế đã duyệt; không nhập rating, reviewCount, cam kết pháp lý hoặc số liệu chưa có nguồn chứng minh. Không bật index public cho môi trường local/staging chỉ vì HTML mẫu có `index, follow`.

Các giá trị này là hồ sơ cấu hình dự kiến; chưa thay Site Title, Tagline, SEO plugin, permalink hoặc robots của WordPress.

## 10. Thông tin còn thiếu và khác biệt cần xác nhận

| Mục | Đối chiếu nguồn | Cách xử lý ban đầu |
| --- | --- | --- |
| Domain | Template: `fractional-cc.vn`; thư mục: `fractionalcc-jp.vn` | Chờ chốt domain chính thức |
| 5 sản phẩm chưa có giá | Mandelic, Mask A, Serum A, AX4, Mask C | Để thiếu dữ liệu; không tạo giá 0 hoặc giá ước lượng |
| Tên combo | Brief ghi C–CX4a; Excel có CX4, template ghi C–CX4 | Chờ xác nhận tên, không tự thêm SKU |
| Giá gốc và khuyến mại | `originalPrice`, quà tặng và `isBestSeller` có trong JS, chưa đủ căn cứ từ phần giá brief | Chưa dùng làm dữ liệu mặc định |
| Đánh giá và feedback | Template có rating/reviewCount, tên KOL/KOC, lời nhận xét và ảnh | Chờ nguồn thật và quyền sử dụng; không xác lập các số này là thật |
| Tuyên bố hoạt chất/hiệu quả | Template có nồng độ, số vi kim, dung tích và mô tả mở rộng | Đối chiếu từng sản phẩm với nhãn/hồ sơ; không tự tổng quát sang toàn dòng |
| CBMP và phân phối độc quyền | Excel ghi số CBMP; brief ghi vai trò phân phối, dẫn ảnh chứng nhận bên ngoài | Giữ thông tin theo nguồn, chờ đối chiếu chứng từ trước tuyên bố xác minh |
| Giao hàng | Brief: 2–5 ngày; HTML chính sách: 1–2 hoặc 2–4 ngày, miễn phí combo | Dùng mốc brief cho hồ sơ; phần bổ sung chờ xác nhận |
| Đổi trả/hoàn tiền | HTML có 7 ngày, chịu phí hai chiều, hoàn tiền 24–48 giờ | Chưa dùng làm chính sách chính thức |
| Tiếp nhận đơn | JS yêu cầu khách tự gửi Zalo; trang cảm ơn nói hệ thống đã nhận và phản hồi 15–30 phút | Chờ chốt cơ chế thực tế và lời xác nhận phù hợp |
| Thông tin doanh nghiệp/kênh bán | Thiếu địa chỉ, mã số thuế, Messenger/Facebook và ngân hàng | Chờ dữ liệu chính thức, không tự điền |
| Video và giấy tờ/media | Brief có liên kết Drive và yêu cầu feedback/video | Chờ file/quyền sử dụng và thông tin đã đối chiếu |

## 11. Phạm vi dữ liệu mặc định cho release sau này

Ứng viên dữ liệu ban đầu gồm nhận diện/liên hệ có nguồn, hero/thông điệp/CTA từ brief, 10 sản phẩm từ Excel, giá đã có trong brief, combo chủ lực, FAQ và thông tin giao hàng/thanh toán từ brief. Các mục chờ xác nhận ở trên chưa được đánh dấu đã duyệt để đưa vào bản phát hành.

Khi người dùng yêu cầu triển khai, nội dung chỉnh sửa được thuộc Gutenberg/ACF; dữ liệu/initializer thuộc project core, giao diện thuộc child theme. Build theo [release/README.md](../release/README.md): hai ZIP trong thư mục version, có dữ liệu mặc định đã duyệt và cơ chế khởi tạo không ghi đè nội dung đã sửa. Lần này chưa tạo seed, migration, field groups, trang WordPress hoặc gói build.
