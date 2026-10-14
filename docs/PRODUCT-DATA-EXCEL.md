# Dữ liệu sản phẩm mặc định từ Excel – 13/10/2026

> Rà soát `0.3.5` đã sửa tham chiếu ảnh trong defaults đang còn dùng đường dẫn legacy. Cài mới từ hai ZIP hiện đạt đầy đủ 305 kiểm tra Excel/catalog; khởi tạo thường trên site đã dùng giữ ảnh người dùng đã lưu. Các trạng thái phiên bản cũ bên dưới là lịch sử.

Nguồn được người dùng chỉ định và đồng ý cập nhật: [List sản phẩm fractional CC.xlsx](<../template/fractional-cc/List sản phẩm fractional CC.xlsx>), sheet `Trang tính1`, vùng `A3:O13`. SHA-256: `0dd50638d92c260f391261bd38437fcb528439620c785e86964ad9acdf732170`. File có 10 sản phẩm, 10 ảnh nhúng tại cột B và giá combo tại `O9`. Không có công thức.

## Ánh xạ từng trường

| Cột | Nguồn | WordPress/defaults |
| --- | --- | --- |
| A | STT | `_fcc_source_id`, khóa `product_1` đến `product_10`; giữ ID/permalink hiện có |
| B | Ảnh nhúng | `fcc_image`, ảnh đầu gallery; ảnh được trích đúng hàng và kiểm tra SHA-256 |
| C | Thương hiệu | `fcc_brand` |
| D | Tên sản phẩm | `post_title`, tiêu đề thẻ chủ lực |
| E | Phân loại | `fcc_category`; giá trị `Mỹ Phẩm`, giữ bộ lọc `fcc_categoryType` riêng |
| F | Xuất xứ | `fcc_origin` |
| G | Công dụng | `fcc_tagline`, mô tả thẻ chủ lực |
| H | Thành phần chính | `fcc_ingredientsText` lưu toàn bộ; `fcc_actives` liên kết thẻ từng ý; cập nhật điểm nổi bật thẻ chủ lực |
| I | Đối tượng phù hợp | `fcc_targetUsers`, giữ từng dòng |
| J | USP / Điểm nổi bật | `fcc_uspBullets`, giữ từng dòng |
| K | Tình trạng pháp lý | `fcc_legalStatus`, giữ nguyên nội dung nguồn |
| L | Số công bố mỹ phẩm | `fcc_cbmp`, bỏ khoảng trắng đầu/cuối |
| M | Ghi chú | `fcc_notes`; trống khi nguồn trống, hai sản phẩm môi có ghi chú đầy đủ |
| N | Giá | `fcc_salePrice`, `fcc_originalPrice`; đọc giá gạch ngang trong ngoặc, không đổi giá trống thành 0 |
| O | Giá combo | Cập nhật combo C–CX4 hiện có: giá bán 1.237.500đ, giá gốc 1.700.000đ, tiết kiệm 462.500đ |

Giá bán theo thứ tự STT 1–10: 628.000; 386.000; 810.000; 910.000; 386.000; 790.000; 910.000; 630.000; 445.000; 445.000 VNĐ. Mandelic có giá gốc 850.000đ; các sản phẩm khác không có giá gốc trong Excel nên để trống.

File không cung cấp hồ sơ combo riêng. Giữ tên, thành phần C + CX4, ảnh, quà tặng và mô tả combo đã có; chỉ thay các trường giá có nguồn. Giữ các trường ngoài Excel như dung tích, hướng dẫn dùng, đánh giá, nhãn, bộ lọc và thiết lập sản phẩm chủ lực. Không bổ sung claims mới hoặc tự sửa nội dung hóa chất khác nhau giữa các cột: ví dụ H6 nhắc Vitamin C trong hồ sơ Serum A, H7 dùng mô tả chung dòng A/C. Nội dung nguồn được lưu đầy đủ để chủ nội dung đối chiếu nhãn nếu cần; thông tin CBMP là nội dung file cung cấp, không phải kết quả tra cứu cơ quan cấp.

## Defaults, cập nhật và khởi tạo

- `wp-content/plugins/project-core/defaults/fractional.json` là bộ mặc định runtime hiện tại, version `1.0.1`; `defaults/catalog-excel.json` ghi dữ liệu nguồn từng ô/hàng và provenance/hash ảnh.
- Ngày 14/10/2026 người dùng yêu cầu bỏ ảnh trích từ Excel. Thư mục cũ đã gỡ; ảnh active/defaults dùng các file có sẵn trực tiếp trong `assets/fractional/assets/images/products/`. Không dùng attachment ID của site local làm giá trị mặc định. [Hồ sơ thay ảnh](../release/v1.0.0/RELEASE.md).
- Site mới: kích hoạt ACF, Project Core và Project Child. Activation theme/plugin xếp lịch khởi tạo; lần vào quản trị khi ACF/theme sẵn sàng sẽ tạo đủ dữ liệu. Không tự đổi homepage, URL, menu hoặc theme của site.
- Site đã có dữ liệu: reactivation và khởi tạo thường chỉ bổ sung phần thiếu, bảo toàn tên, giá, ảnh bị bỏ chọn, nội dung Gutenberg, hoạt chất, nội dung trang và sản phẩm chủ lực đã chỉnh.
- Cập nhật có ghi đè từ Excel là thao tác riêng đã được người dùng cho phép cho site hiện tại: `php scripts/setup-fractional-site.php --update-catalog`. Chỉ CLI, yêu cầu quyền quản trị/upload; thay các trường catalog có nguồn, không đổi URL/permalink/homepage hoặc lựa chọn sản phẩm chủ lực. Gallery được khởi tạo trước đây được cập nhật ảnh; gallery/nội dung tự soạn được giữ.
- Chạy lại compiler giao diện giữ bộ catalog đã duyệt; không trả về dữ liệu cũ trong `data.js`.

## Công cụ và kiểm tra

Compiler legacy `scripts/import-fractional-excel.ps1` xác nhận header, STT/tên, CBMP và định dạng giá; dùng ảnh có sẵn trong theme, không trích ảnh nhúng hoặc sửa workbook. Snapshot đã approved được bảo vệ khỏi ghi đè. Các lần chốt tiếp theo dùng CMS và `scripts/export-fractional-defaults.php` theo release workflow.

`php scripts/verify-fractional-catalog.php` đối chiếu 305 điểm giữa nguồn Excel, defaults và database: từng trường, phân biệt trống/0, thành phần, giá, mapping/hash ảnh và combo. Site local và site mới từ hai ZIP đã đạt. Trên database kiểm thử riêng, chạy lại sau khi chỉnh nội dung không tạo trùng hoặc ghi đè edits; activation không đổi homepage; khởi tạo ẩn danh bị từ chối. HTTP của 10 trang sản phẩm, trang chủ và danh mục đạt; dữ liệu biểu mẫu đặt mua có giá mới. Không gửi email/đơn thử và không thay dữ liệu site đang làm việc để thử reset.

Backup catalog trước cập nhật đã được đọc lại và kiểm tra hash, nằm riêng tại `C:/Users/anhvt/.codex/backups/fractionalcc-jp.vn/`, không nằm trong repo/ZIP. Chưa thực hiện phục hồi backup vào site thật. Chưa kiểm tra trực quan trình duyệt desktop/mobile trong bản cập nhật nội dung này.
