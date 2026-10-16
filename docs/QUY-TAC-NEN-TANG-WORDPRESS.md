# Quy tắc xây dựng nền tảng WordPress dùng lại cho nhiều dự án

Ngày tổng hợp: 16/10/2026. Ngôn ngữ: tiếng Việt.

Tài liệu tổng hợp các quy định của chủ dự án từ dự án Fractional CC, dùng làm yêu cầu nền tảng cho theme, plugin và cách quản lý nội dung ở những dự án tiếp theo. Có thể sao chép nguyên file sang repository mới và yêu cầu developer/AI đọc trước khi làm việc.

Đây là bộ quy tắc để phát triển và nghiệm thu; không phải xác nhận mọi chức năng đã được triển khai hoặc kiểm thử trên mọi site. Những ví dụ về trang, section và tên trường minh họa cách tổ chức, không phải nội dung kinh doanh mặc định.

## 1. Mục tiêu và nguyên tắc chủ đạo

- WordPress đảm nhiệm CMS và quản trị; developer kiểm soát cấu trúc, giao diện và hành vi ứng dụng.
- Khách hàng sửa nội dung, ảnh và các thiết lập được cho phép mà không phải sửa PHP, HTML, CSS, JavaScript hoặc thao tác với cấu trúc page builder.
- Nội dung có cấu trúc phải có màn quản lý rõ ràng theo **website → trang → section → trường nội dung**.
- Thông tin dùng chung được quản lý tập trung, có một nguồn dữ liệu chính và được dùng thống nhất ở các vị trí hiển thị.
- Giao diện thuộc child theme; nghiệp vụ thuộc plugin core của dự án; giá trị nội dung thuộc database WordPress qua Gutenberg/ACF và các API phù hợp.
- Xây giải pháp nhỏ nhất đủ dùng, tận dụng hệ sinh thái WordPress, dễ cập nhật và dễ bàn giao.
- Giữ thiết kế và nội dung đã duyệt. Việc bảo trì, tối ưu SEO, bảo mật hoặc accessibility không tự cho phép thiết kế lại.

## 2. Hiểu đúng “core theme” và “core plugin”

Trong bộ nền này, phần giao diện tùy chỉnh là **child theme**, kế thừa parent theme có thể nâng cấp. “Core theme” không có nghĩa là sửa WordPress core hoặc sửa source parent theme.

| Thành phần | Trách nhiệm |
| --- | --- |
| WordPress core | CMS, tài khoản, phân quyền, Media, API và cơ chế nền |
| Parent theme | Nền giao diện ổn định, ưu tiên Kadence hoặc Blocksy |
| Child theme | Layout, template, section, component, CSS, JS giao diện, responsive và render nội dung |
| Plugin core dự án | CPT, taxonomy, nghiệp vụ, API, tích hợp, quản trị, dữ liệu mặc định và khởi tạo/nâng cấp dữ liệu |
| Gutenberg | Bài viết, tin tức và nội dung tự do phù hợp |
| ACF hoặc trường quản trị tương đương đã thống nhất | Nội dung có cấu trúc: hero, CTA, ảnh, thông tin theo section, quan hệ dữ liệu |
| WooCommerce, khi cần | Nền thương mại điện tử; mở rộng nghiệp vụ bằng plugin dự án |

Không sửa WordPress core, `wp-admin/`, `wp-includes/`, parent theme hoặc plugin bên thứ ba. Việc đưa các dependency này vào Git không cho phép chỉnh source của chúng.

`functions.php` chỉ nên chứa bootstrap, theme setup, enqueue, hook đơn giản và nạp module. Không đặt tính giá, truy vấn dữ liệu phức tạp, tích hợp hoặc workflow nghiệp vụ vào file này.

## 3. Chỉnh chữ và ảnh ngoài page builder

### 3.1. Chỉnh nội dung bằng trường quản trị

- Các chữ và ảnh thuộc phạm vi khách được sửa phải có trường quản lý thực sự: tiêu đề, mô tả, banner, ảnh section, nhãn nút, đường dẫn, chú thích và nội dung tương ứng.
- Không yêu cầu khách mở template, sửa chuỗi trong source hoặc chỉnh HTML để thay nội dung thông thường.
- Không xem việc “đã có field ACF” là hoàn thành: field phải xuất hiện trong đúng màn quản lý, lưu được và được frontend sử dụng đúng vị trí.
- Không giữ một trường cho khách sửa nhưng frontend lại đọc giá trị cố định hoặc một trường cũ khác.
- Nội dung thuộc trang nào phải dễ tìm từ màn quản lý trang đó; dữ liệu chung phải dẫn tới nơi quản lý chung.

### 3.2. Chỉnh từ bản xem trước khi có hỗ trợ

- Có thể dùng WordPress Customizer hoặc cơ chế xem trước phù hợp để sửa chữ/ảnh trong ngữ cảnh giao diện mà không cần page builder.
- Màn quản trị và công cụ chỉnh từ bản xem trước phải đọc/ghi cùng nguồn dữ liệu; không tạo hai bộ nội dung độc lập.
- Nếu một ảnh hoặc thông tin đang lấy từ bản ghi khác, control phải dẫn tới bản ghi đó hoặc cập nhật đúng nguồn dữ liệu ấy.
- Ví dụ: hero dùng ảnh của combo nổi bật thì phải sửa ảnh combo đang chọn, không lưu một “ảnh hero” riêng mà frontend không dùng.
- Bản xem trước chưa được xuất bản không tự ghi thay đổi thành nội dung chính thức. Chỉ người có quyền mới được dùng công cụ sửa.

### 3.3. Giới hạn cấu trúc

- Developer quản lý bố cục, responsive, HTML, truy vấn, API và nghiệp vụ.
- Khách chỉ chỉnh các nội dung và tùy chọn được thiết kế sẵn.
- Chỉ mở màu sắc, bật/tắt section, đổi thứ tự hoặc thêm/bớt mục khi dự án đã có yêu cầu tương ứng; không mặc định biến mọi trang thành trình dựng bố cục tự do.
- Không đưa Elementor, WPBakery, Divi hoặc page builder tương đương vào base nếu chưa được yêu cầu rõ ràng; không chồng nhiều builder.
- Gutenberg vẫn dùng cho nội dung tự do; custom block/pattern hoặc ACF dùng cho section có cấu trúc phù hợp.

## 4. Màn quản lý theo từng trang và từng section

### 4.1. Có đầu mối “Nội dung website”

Admin phải có nơi tập trung giúp người dùng tìm đúng màn sửa nội dung. Có thể dẫn tới màn sửa native của WordPress/ACF; không cần tạo lại một editor riêng nếu công cụ hiện có đã đáp ứng.

Ví dụ tổ chức, điều chỉnh theo những trang thực sự có trong dự án:

```text
Nội dung website
├── Thông tin chung
│   ├── Nhận diện, logo, biểu tượng và ảnh chia sẻ mặc định
│   ├── Liên hệ và mạng xã hội
│   ├── Header, footer và các nhãn dùng chung
│   └── Biểu mẫu và thông báo dùng chung
├── Trang chủ
│   ├── 01. Hero
│   ├── 02. Giới thiệu
│   ├── 03. Sản phẩm hoặc dịch vụ nổi bật
│   ├── Các section khác theo thiết kế đã duyệt
│   └── SEO
├── Các trang nội dung khác
│   └── Nhóm/tab theo section và SEO của từng trang
└── Các loại dữ liệu riêng
    └── Sản phẩm, dịch vụ hoặc loại bản ghi mà dự án thực sự cần
```

### 4.2. Quy tắc cho màn sửa trang

- Chia tab/nhóm theo section, dùng tên tiếng Việt dễ hiểu và thứ tự tương ứng với giao diện.
- Cho biết trường đang tác động tới vị trí nào; có hướng dẫn và liên kết xem khu vực tương ứng khi cần.
- Với màn nhiều trường, hỗ trợ cách tìm nội dung phù hợp; tận dụng tab, nhóm và tìm kiếm để người dùng không phải dò một danh sách dài.
- Dùng đúng control: ảnh mở Media Library, URL dùng trường URL, lựa chọn dữ liệu dùng relationship/select, số dùng trường số.
- Giải thích ý nghĩa của để trống, bỏ chọn và xóa ảnh; không để người dùng phải đoán kết quả.
- Trường SEO của trang nằm trong nhóm dễ tìm. Nội dung của bản ghi sản phẩm/dịch vụ được sửa ở chính bản ghi đó.
- Không đưa hàng loạt tùy chọn kỹ thuật vào màn khách hàng nếu chúng không giúp họ quản lý nội dung.

### 4.3. Không nhầm sửa nội dung với dựng lại section

Một section có các field chỉnh chữ và ảnh không đồng nghĩa khách được tự thêm/xóa/đổi thứ tự section hoặc thay layout. Nếu cần collection có thể thêm/bớt/sắp xếp, phải triển khai control và quy tắc lưu tương ứng; không tuyên bố đã hỗ trợ chỉ vì các mục cố định có field.

## 5. Quản lý thông tin tập trung và một nguồn dữ liệu chính

### 5.1. Dữ liệu toàn website

Những thông tin có cùng ý nghĩa và được dùng nhiều nơi phải có một nguồn quản lý, ví dụ:

- Tên thương hiệu, logo, favicon và ảnh chia sẻ mặc định.
- Hotline, số điện thoại phụ, email, địa chỉ và liên kết mạng xã hội.
- Liên kết gọi điện, Zalo, Messenger và các kênh liên hệ.
- Nhãn, thông báo hoặc thông tin header/footer dùng chung trong phạm vi đã cho phép chỉnh.

Đổi hotline tại nguồn chung phải đổi đồng thời số hiển thị và liên kết `tel:` ở các vị trí sử dụng. Không để footer, CTA và form giữ những bản sao viết cố định.

### 5.2. Dữ liệu theo bản ghi

- Tên, giá, ảnh, thông tin và quan hệ của sản phẩm/dịch vụ phải lấy từ bản ghi chính.
- Một bản ghi xuất hiện trên trang chủ, danh mục, trang chi tiết hoặc CTA phải dùng dữ liệu thống nhất cho những thuộc tính có cùng ý nghĩa.
- Chọn dữ liệu đã có bằng ID/relationship hoặc cơ chế tham chiếu phù hợp; không nhập lại cả tên, ảnh và giá thành các bản sao riêng ở từng section.
- Lựa chọn đối tượng nổi bật được lưu tại nguồn rõ ràng; các vị trí phụ thuộc cùng đọc nguồn đó.
- Điểm/số đánh giá, nếu có nội dung được duyệt, phải gắn đúng đối tượng và không dùng số viết cố định thay cho dữ liệu của đối tượng.

### 5.3. Nội dung riêng theo ngữ cảnh

Không ép các nội dung khác ý nghĩa dùng chung một field. Tiêu đề riêng của trang, lời dẫn riêng của section hoặc nội dung SEO ghi đè có thể độc lập. Cần ghi rõ trường nào dùng chung, trường nào chỉ dùng ở một vị trí và thứ tự ưu tiên khi có giá trị ghi đè.

Ví dụ: chính sách đầy đủ và phần tóm tắt cạnh form có thể là hai nội dung khác nhau; nếu quản lý riêng, phải hướng dẫn cập nhật cả hai khi chính sách thay đổi. Không tự suy ra một nội dung kinh doanh mới để “đồng bộ”.

### 5.4. Trạng thái trống là dữ liệu có ý nghĩa

- Phân biệt chưa được khởi tạo với khách đã chủ động xóa hoặc bỏ chọn.
- Không tự phục hồi ảnh đã bỏ, nội dung đã xóa hoặc đối tượng nổi bật đã tắt khi chạy lại defaults/migration.
- Khi đối tượng chưa xuất bản hoặc đã bị xóa, không tiếp tục hiển thị CTA mua hoặc dữ liệu như thể đối tượng còn hợp lệ.
- Cách ẩn, hiển thị trống hoặc dùng fallback phải được xác định theo yêu cầu từng trường; fallback không được làm sống lại dữ liệu khách đã chủ động bỏ.

## 6. Quản lý ảnh và Media

- Ảnh khách cần thay phải chọn/tải từ WordPress Media, không yêu cầu ghi đè file trong thư mục theme.
- Quản lý rõ vai trò của ảnh: đại diện, gallery, ảnh hover, nền, ảnh section, avatar hoặc ảnh chia sẻ, tùy nhu cầu thực tế.
- Nếu một ảnh có cùng vai trò ở nhiều vị trí, dùng chung nguồn; nếu vai trò khác nhau, có control riêng và hướng dẫn rõ.
- Với gallery cần chỉnh sửa, có thao tác chọn/thêm/bỏ ảnh và đổi thứ tự theo yêu cầu; phân biệt ảnh đại diện với vị trí đầu trong gallery.
- Bỏ ảnh khỏi bản ghi không đồng nghĩa xóa file Media dùng chung.
- Dùng alt từ Media hoặc quy tắc phù hợp ngữ cảnh; ảnh trang trí có thể có alt rỗng. Không nhồi từ khóa.
- Tối ưu định dạng, kích thước, ảnh responsive và lazy loading phù hợp. Không làm sai tỷ lệ hoặc thay hình đã duyệt chỉ để tối ưu.
- Ảnh nội dung mặc định đã duyệt có thể đi kèm release theo cơ chế khởi tạo; không đưa toàn bộ uploads riêng của site vào ZIP.

## 7. Cấu trúc dữ liệu và ACF

- Ưu tiên posts/postmeta, users/usermeta và taxonomy của WordPress cho dữ liệu CMS.
- Chỉ thêm CPT, taxonomy hoặc bảng riêng khi có nhu cầu thật. Bảng riêng phải có lý do về truy vấn, quan hệ, khối lượng hoặc vòng đời dữ liệu.
- Field definitions được quản lý phiên bản bằng JSON hoặc PHP; giá trị nội dung lưu trong database.
- Field phục vụ trình bày thuộc theme; field mô hình nghiệp vụ thuộc plugin. Không khai báo cùng một field ở hai nơi.
- Không xem ACF JSON là bản sao dữ liệu nội dung: nó mô tả field, không chứa đầy đủ các giá trị đã nhập.
- Xác định dependency và giấy phép khi dùng tính năng ACF; không mặc định dự án nào cũng có ACF PRO.
- Kiểm tra kiểu trả về, giá trị trống và tình huống dependency thiếu; tránh lỗi fatal hoặc lỗi hiển thị không được xử lý.
- Dùng prefix/namespace riêng của dự án cho symbol, option, field và hook cần tránh xung đột.

## 8. Thiết kế giao diện và tái sử dụng component

- Giữ layout, màu, typography, khoảng cách, hình ảnh và tương tác đã duyệt. Thay đổi thiết kế phải nằm trong phạm vi được cho phép.
- Tách các phần dùng lại thành component/template part: hero, tiêu đề section, card, CTA, header/footer, biểu mẫu… theo nhu cầu thực tế.
- Tìm và mở rộng component/helper sẵn có trước khi tạo bản tương tự; không sao chép HTML/logic sang nhiều template.
- Ưu tiên CSS có tổ chức, responsive theo hướng mobile-first; tránh inline style tràn lan và `!important` không cần thiết.
- Dùng JavaScript thuần cho tương tác đơn giản; không thêm framework lớn khi chưa có lý do.
- Chỉ nạp CSS/JS ở những trang cần; tránh tải lại asset của parent hoặc tải cùng thư viện nhiều lần.
- Không đưa nội dung kinh doanh có thể chỉnh sửa vào chuỗi cố định trong template. Nhãn kỹ thuật/giao diện ngoài phạm vi khách sửa vẫn cần được quản lý nhất quán và dịch khi phù hợp.

## 9. Nghiệp vụ và thương mại điện tử

- CPT, taxonomy, REST/AJAX, cron, tích hợp, import/export, validation và workflow quản trị thuộc plugin core dự án.
- Nghiệp vụ không được phụ thuộc vào việc một template đang được render. Thay theme không được làm mất định nghĩa mô hình dữ liệu và nghiệp vụ do plugin quản lý.
- Khi có thương mại điện tử, dùng WooCommerce làm engine nếu phù hợp yêu cầu; không mặc định thêm WooCommerce vào mọi dự án.
- Mở rộng WooCommerce bằng hook/filter trước; template override thuộc child theme, nghiệp vụ giá/đơn hàng/checkout thuộc plugin.
- Không sửa source WooCommerce hoặc plugin thương mại khác. Theo dõi tương thích khi override template.
- Những module riêng của Fractional CC không tự trở thành chức năng bắt buộc cho mọi dự án mới.

## 10. Nội dung, SEO và khả năng truy cập

- Chỉ sử dụng dữ kiện, nội dung và hình ảnh được cung cấp hoặc phê duyệt cho đúng dự án.
- Không tự bịa giá, đánh giá, số khách hàng, thành tích, chứng nhận, chính sách, ưu đãi, thông tin liên hệ hoặc claims.
- Thiếu dữ kiện thì hỏi hoặc để trống. Placeholder chỉ dùng khi được cho phép, phải gắn nhãn và không xuất bản như thông tin thật.
- Nội dung được duyệt cho Fractional CC không mặc nhiên được duyệt cho thương hiệu khác.
- SEO phải phù hợp nội dung hiển thị: semantic HTML, heading có thứ bậc, title/description chỉnh được, internal link hợp lệ, alt và tài nguyên tối ưu.
- Xác định nơi đang quản lý SEO; không tạo trùng metadata, canonical, sitemap, social metadata hoặc structured data.
- Schema phải khớp dữ liệu có thật và nội dung hiển thị. Không bỏ noindex có chủ đích của local/staging.
- Đảm bảo điều hướng bàn phím, label, focus, ngữ nghĩa nút/link và tương phản phù hợp; chỉ dùng ARIA khi cần.
- Nếu việc sửa SEO/accessibility cần thay đổi giao diện ngoài phạm vi được duyệt, phải trình bày thay đổi để thống nhất trước.

## 11. Bảo mật và phân quyền quản trị

- Validate/sanitize input, escape output đúng ngữ cảnh; không tin dữ liệu request.
- Kiểm tra capability và quyền trên đối tượng; dùng nonce cho thao tác cần bảo vệ. Nonce không thay thế phân quyền.
- SQL động phải dùng API phù hợp hoặc prepared query; không nối input người dùng vào SQL.
- REST/AJAX phải có quy tắc quyền truy cập phù hợp; không để lộ dữ liệu riêng.
- Upload/import phải kiểm tra loại file, dung lượng, đường dẫn và dữ liệu theo phạm vi thao tác.
- Không hardcode hoặc xuất credentials ra source, log, frontend hay release.
- Có thể ẩn menu không phù hợp với vai trò khách hàng, nhưng ẩn menu không phải biện pháp kiểm soát quyền; giữ đường quản trị/khôi phục cho administrator.
- Không tắt bảo mật để chữa lỗi; xử lý lỗi có kiểm soát, không công khai stack trace, SQL hoặc secrets.

## 12. Chất lượng code, dependency và hiệu năng

- Đọc code và quy ước hiện có trước khi viết; theo WordPress Coding Standards và style nhất quán của dự án.
- Ưu tiên WordPress native, tính năng theme/plugin đang dùng, hook/filter và phần mở rộng đúng trách nhiệm trước khi thêm dependency.
- Chỉ thêm plugin/thư viện khi có nhu cầu rõ; xét bảo trì, bảo mật, asset, giấy phép và khả năng gỡ bỏ.
- Không tạo folder, service, repository, endpoint, helper hoặc abstraction chưa có chỗ sử dụng.
- Tránh lặp logic, markup, query và asset; giữ function tập trung vào nhiệm vụ rõ ràng.
- Tránh N+1 query, query thừa, ảnh quá lớn, script bên thứ ba không cần thiết và polling liên tục.
- Dùng phân trang, index, cache phù hợp bằng chứng thực tế; không tạo hệ thống cache phức tạp chỉ để dự phòng.

## 13. Môi trường và Git

### 13.1. Quy định môi trường hiện tại

Dự án nguồn dùng XAMPP tại `C:\xampp\htdocs\fractionalcc-jp.vn`. Root repository chính là thư mục cài WordPress, dùng đường dẫn `wp-content/...`, không thêm tiền tố `wordpress/`.

Docker đang tạm thời không dùng: không khởi động container, yêu cầu Docker cho build/test hoặc tự chuyển môi trường. Khi dùng tài liệu này cho dự án mới, xác định đường dẫn, URL, PHP và môi trường riêng; không sao chép cứng cấu hình của Fractional CC. Không nâng/migrate môi trường trong một tác vụ không liên quan.

Kiểm tra runtime khi cần xác nhận activation, phiên bản hoặc URL; file đang có trên đĩa và ghi chú lịch sử không chứng minh trạng thái đang chạy.

### 13.2. Git cho starter snapshot

- Theo lựa chọn của chủ dự án, starter theo dõi WordPress core, parent themes, plugin bên thứ ba và vendor được đóng gói cùng dependency, ngoài custom code, scripts và tài liệu.
- Không khôi phục kiểu ignore chỉ cho phép vài thư mục hoặc ignore `vendor/` toàn cục.
- Giữ secrets, `wp-config.php`, `.env`, uploads/runtime, cache, log, backup và database dump thật ngoài Git.
- Ghi phiên bản dependency và cách khôi phục, bao gồm điều kiện giấy phép nếu có. Kiểm tra luật ignore thực tế trước khi commit.
- Source code và dữ liệu vận hành là hai phần riêng; không chép database local đè production để triển khai thay đổi code.

## 14. Dữ liệu mặc định, cài mới và nâng cấp

- Bộ nền phải hỗ trợ dữ liệu mặc định đã được duyệt và cơ chế khởi tạo có version, chạy lại an toàn.
- Chỉ bổ sung dữ liệu còn thiếu theo quy tắc rõ ràng; không ghi đè nội dung khách đã sửa, ảnh đã bỏ hoặc lựa chọn đã xóa.
- Không tự thay trang chủ, menu, theme đang dùng hoặc nội dung hiện hữu khi kích hoạt/cập nhật; chỉ thiết lập giá trị ban đầu theo điều kiện cài mới đã xác định.
- Dùng source key/định danh ổn định để ánh xạ bản ghi, Media, URL nội bộ và relationship sang site đích; không coi ID database local là ID cố định.
- Chạy lại không tạo trùng bản ghi hoặc ảnh. Migration cần theo dõi version và bảo toàn lựa chọn người dùng.
- Plugin core đóng gói defaults và initializer; field definitions, template và asset trình bày đi theo theme sở hữu chúng.
- Không mang khách hàng, đơn hàng, tài khoản, secrets hoặc dữ liệu vận hành riêng sang dự án khác.
- Dữ liệu mẫu dùng cho dự án mới phải được chọn riêng; không mặc định nhập toàn bộ sản phẩm, liên hệ, claims hoặc nhận diện Fractional CC.

## 15. Đóng gói và bàn giao

Mỗi release đặt trong `release/vX.Y.Z/`, có **đúng hai ZIP cài được qua WordPress**:

1. Plugin core dự án: `<project>-core-vX.Y.Z.zip`.
2. Child theme cần kích hoạt: `<project>-child-vX.Y.Z.zip`.

Tên `<project>` là slug của dự án; source hiện tại dùng `project-core` và `project-child`. Mỗi ZIP có một thư mục gốc đúng slug. Có thể kèm `RELEASE.md` và hồ sơ kiểm thử bên ngoài ZIP trong thư mục release.

- Hai ZIP không chứa WordPress core, parent theme, plugin bên thứ ba, secrets, database dump, tài khoản, log, backup hoặc uploads riêng.
- Việc Git lưu dependency đầy đủ và việc release chỉ có hai ZIP là hai quy định khác nhau, cùng được áp dụng.
- Ghi rõ dependency phải cài riêng, version, thứ tự cài/kích hoạt, khởi tạo defaults, thay đổi dữ liệu, kiểm thử và cách rollback.
- Kiểm tra cài mới và nâng cấp trên site/database riêng; không reset/seed phá dữ liệu của site đang làm việc.
- Backup database, uploads và cấu hình khôi phục bằng nơi lưu riêng; xác minh có thể phục hồi.
- Không sửa PHP trực tiếp trên production qua trình sửa file WordPress; ưu tiên tắt editor file ở production.

## 16. Quy trình phát triển và tiêu chí nghiệm thu

Trình tự: hiểu yêu cầu → đọc quy định và code → phân loại theme/nội dung/plugin/nghiệp vụ → tái sử dụng thành phần hiện có → triển khai vừa đủ → kiểm tra → bàn giao kết quả thực tế.

Với mỗi chức năng quản lý nội dung, chỉ xem là hoàn thành khi:

- [ ] Người dùng tìm được đúng trang, section và trường cần sửa.
- [ ] Chữ/ảnh lưu được và frontend hiển thị đúng dữ liệu vừa sửa.
- [ ] Nếu có chỉnh qua bản xem trước, nó dùng chung nguồn dữ liệu với admin và tuân thủ trạng thái xuất bản.
- [ ] Dữ liệu chung thay đổi đồng bộ ở các vị trí sử dụng; giá trị hiển thị và liên kết không lệch nhau.
- [ ] Để trống, bỏ ảnh, bỏ chọn và đối tượng không còn hợp lệ có hành vi đúng.
- [ ] Khách không cần sửa source hoặc dựng lại bố cục để thay nội dung.
- [ ] Quyền truy cập, nonce khi cần, validation và escaping được kiểm tra theo thao tác bị ảnh hưởng.
- [ ] Desktop/mobile, keyboard/focus, PHP và console được kiểm tra khi thay đổi giao diện hoặc tương tác.
- [ ] SEO và giao diện/nội dung đã duyệt được bảo toàn trong phạm vi thay đổi.
- [ ] Không có logic, component, query hoặc asset trùng không cần thiết do thay đổi tạo ra.

Với release, kiểm tra thêm cài mới, dependency thiếu, ánh xạ dữ liệu/ảnh, chạy lại không trùng và nâng cấp giữ edits, kể cả giá trị đã xóa. Với thương mại điện tử bị ảnh hưởng, kiểm tra giỏ hàng, checkout, giá, tạo đơn và email liên quan.

Không tuyên bố activation, build hoặc kiểm thử thành công nếu chưa kiểm tra thực tế. Ghi rõ những bước chưa chạy và giới hạn còn lại.

## 17. Áp dụng cho dự án tiếp theo

Khi tạo dự án mới, dùng bộ quy tắc này làm mặc định; chỉ cần chốt phần khác biệt:

1. Tên dự án, slug/prefix/namespace, domain và môi trường.
2. Parent theme và dependency cần dùng, cùng phiên bản/giấy phép.
3. Thiết kế được duyệt, danh sách trang, section và phạm vi khách được chỉnh.
4. Nhóm thông tin chung và nguồn dữ liệu chính của từng đối tượng.
5. Nghiệp vụ, loại dữ liệu và tích hợp thực sự cần cho dự án đó.
6. Nội dung, media và defaults được duyệt riêng cho thương hiệu mới.
7. Cách cài mới, migration, build, kiểm thử và triển khai.

Không cần trình bày lại các nguyên tắc nền tảng trong mỗi yêu cầu nhỏ. Các yêu cầu mới bổ sung phần khác biệt; nếu có thay đổi trái quy tắc nền, phải nêu rõ phạm vi thay đổi. Không tự nhân rộng module riêng của một dự án thành framework lớn cho mọi trường hợp.

### Đoạn hướng dẫn có thể đặt trong AGENTS.md của dự án mới

```text
Trước khi phát triển, đọc docs/QUY-TAC-NEN-TANG-WORDPRESS.md và các quyết định riêng của dự án.
Áp dụng bộ quy tắc đó làm mặc định cho kiến trúc, quản trị nội dung và bàn giao.
Giao diện trong child theme; nghiệp vụ trong plugin core; nội dung trong WordPress/Gutenberg/ACF.
Cho phép sửa chữ/ảnh không cần page builder; quản lý theo trang và section.
Thông tin chung và dữ liệu dùng nhiều nơi phải có một nguồn chính, không nhập lại thành bản sao.
Giữ UI và nội dung đã duyệt; không bịa dữ kiện; không sửa core/parent/dependency.
Đóng gói hai ZIP plugin core và child theme, có defaults đã duyệt và khởi tạo bảo toàn edits.
Đọc code hiện có trước khi làm, tái sử dụng, kiểm tra đúng phạm vi và báo rõ giới hạn.
```

## 18. Nguồn quy định trong dự án gốc

Tài liệu được tổng hợp từ các nguồn dưới đây và yêu cầu xuất bộ quy tắc dùng lại của chủ dự án. Các liên kết phục vụ đối chiếu tại repository gốc; nội dung quy tắc ở trên có thể đọc độc lập khi sao chép file sang dự án khác.

- [AGENTS.md](../AGENTS.md).
- [Skill WordPress của dự án](../.agents/skills/wordpress-ai-development/SKILL.md) và [43 nhóm chính sách kiến trúc](../.agents/skills/wordpress-ai-development/references/architecture-policy.md).
- [Phân chia kiến trúc](ARCHITECTURE.md) và [workflow XAMPP](SETUP.md).
- [Quản lý ảnh và nội dung](CONTENT-MANAGEMENT.md).
- [Quản lý ảnh đại diện và gallery](THUMBNAIL-MEDIA.md).
- [Lựa chọn sản phẩm/combo và nguồn dữ liệu chung](HOMEPAGE-SELECTIONS.md).
- [Quy định Git cho base](GIT-TEMPLATE.md) và [quy định release](../release/README.md).

Các mô tả runtime, số lượng field, ID trang, version và nội dung trong tài liệu lịch sử không phải giá trị cố định cho base mới. Mọi tuyên bố hiện trạng phải dựa trên kiểm tra của tác vụ tương ứng.
