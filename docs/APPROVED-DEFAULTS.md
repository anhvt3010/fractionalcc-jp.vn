# Dữ liệu đã chốt – release đầu tiên 1.0.0

> Bản chốt hiện hành **[3.0.0](../release/v3.0.0/RELEASE.md)**, 16/10/2026: người dùng duyệt toàn bộ nội dung hiện tại. Đã xuất lại CMS gồm 6 bản ghi nội dung/1.104 field, 10 sản phẩm, 1 combo, 35 hoạt chất và 75 ảnh, cùng lựa chọn/quan hệ/settings công khai. Kèm 4 trường quản lý hai popup video bằng file Media hoặc URL; hiện dùng hai URL Drive cũ, chưa chọn file upload. Core/Child/defaults đồng bộ 3.0.0. Đối chiếu snapshot với CMS, cài mới từ ZIP, nâng từ 2.2.1 giữ edits và giao diện desktop/mobile đã kiểm tra. Nội dung/seed site làm việc giữ nguyên; các mốc dưới đây là lịch sử.

> Đóng gói lại **2.2.1**, 16/10/2026: sửa đúng trường quà tặng combo thành `Serum dưỡng môi PDRN Shot theo ảnh combo; xác nhận khi đặt hàng.` theo yêu cầu người dùng. Các defaults khác và version giữ nguyên. CMS làm việc đã sửa trường tương ứng; không chạy initializer. Hosting có dữ liệu cũ cần sửa trường Quà tặng đã xác nhận trong Combo vì initializer bảo toàn nội dung đã lưu.

> Build **[2.2.1](../release/v2.2.1/RELEASE.md)**, 16/10/2026: kế thừa nguyên vẹn defaults 2.2.0, chỉ tăng version; không xuất lại CMS hoặc đổi dữ liệu đã duyệt. Gồm 74 Media, 10 sản phẩm và combo C–CX4 với 4 ảnh. Đã kiểm tra cài mới và nâng cấp giữ edits/xóa từ ZIP trên database riêng. Site làm việc giữ seed 2.1.0.

> Cập nhật **[2.2.0](../release/v2.2.0/RELEASE.md)**, 16/10/2026: chỉ bổ sung gallery combo C–CX4 và 3 ảnh do người dùng yêu cầu, tổng 74 Media trong defaults. Nội dung, giá, ảnh đại diện, lựa chọn và các defaults khác kế thừa 2.1.0; không xuất lại toàn bộ CMS. Ảnh mới đóng gói WebP, nội dung gallery dùng đường dẫn/ID portable. Cài mới và nâng cấp gallery rỗng đã kiểm tra trên site/database riêng; khởi tạo lại giữ ảnh/thứ tự đã sửa hoặc đã xóa. Site làm việc đã thêm ảnh, seed vẫn 2.1.0, không chạy initializer.

> Release hiện hành **[2.1.0](../release/v2.1.0/RELEASE.md)**, 15/10/2026: người dùng duyệt hiện trạng làm mặc định. Snapshot xuất từ CMS đang dùng có 6 bản ghi nội dung/1.096 field, 10 sản phẩm, 1 combo, 35 hoạt chất và 71 Media. Danh mục trang chủ chọn 6 sản phẩm riêng, combo nổi bật `combo_C-CX4` quản lý độc lập. Gồm sửa câu chữ, ảnh và thứ tự section hiện tại. Cài mới khôi phục cả ảnh thư viện chưa được sử dụng; chạy lại/nâng cấp giữ edits, giá trị trống và lựa chọn rỗng. Core, Child và defaults cùng 2.1.0, `contentManagement.version=2`. Không chạy initializer trên site làm việc; seed nguồn giữ 2.0.0. Xem release notes cho kết quả kiểm tra và hạn chế popup ở màn hình nhỏ.

> Release hiện hành **[2.0.0](../release/v2.0.0/RELEASE.md)**, 15/10/2026: người dùng chốt toàn bộ code và data hiện tại. Đã xuất lại CMS XAMPP: 6 bản ghi nội dung/1.090 field, 10 sản phẩm, 1 combo, 35 hoạt chất, 71 Media và 8 settings công khai. Giữ giá, SEO, liên hệ, nhãn, ảnh đại diện/hover, gallery/thứ tự và các lựa chọn hiện tại, gồm giá trị trống. Exporter lấy ảnh hover của editor native bằng đường dẫn portable; cài mới và nâng cấp giữ edits đã được kiểm tra từ hai ZIP. Không chạy initializer trên site làm việc. Các mốc bên dưới là lịch sử.

> Mốc **[1.0.11](../release/v1.0.11/RELEASE.md)**, 15/10/2026: toàn bộ defaults đã duyệt 1.0.10 được giữ nguyên, chỉ tăng version snapshot. Không xuất lại dữ liệu CMS hoặc thay nội dung/giá/ảnh/SEO/quan hệ/settings. Cài mới và nâng cấp giữ edits đã được kiểm tra từ hai ZIP.

> Hiện hành **[1.0.10](../release/v1.0.10/RELEASE.md)**, ngày 15/10/2026: theo yêu cầu tích hợp SEO, kế thừa snapshot 1.0.9 và chỉ cập nhật 26 title/description cho trang chủ, danh mục, vấn đề làn da và 10 sản phẩm. Mọi nội dung/ảnh/giá/quan hệ/thiết lập khác giữ nguyên; không xuất lại CMS local. Bản đồ 7 nhóm/62 truy vấn là kế hoạch biên tập trong quản trị, không phải bài đã xuất bản hoặc số liệu lượng tìm kiếm. Xem [SEO](SEO.md). Các mốc bên dưới là lịch sử.

> Bản chốt hiện hành **[1.0.9](../release/v1.0.9/RELEASE.md)** theo yêu cầu ngày 15/10/2026: xuất lại từ CMS XAMPP đang dùng, giữ nguyên nội dung và bốn nhãn thông tin hiện tại. Snapshot có **6 bản ghi nội dung với 1.090 giá trị field, 10 sản phẩm, 1 combo, 35 hoạt chất và 70 Media**, kèm gallery/thứ tự, avatar, alt/caption, lựa chọn danh mục/phác đồ, liên hệ, SEO và 8 thiết lập website công khai. Exporter loại các field không còn đăng ký để không đưa phần hướng dẫn đã bỏ trở lại; không xóa dữ liệu lịch sử trong database. Core, Child và defaults cùng version 1.0.9; trạng thái `approved`.

> Cập nhật theo yêu cầu ngày 15/10/2026: defaults **[1.0.6](../release/v1.0.6/RELEASE.md)** bỏ hai nhãn của khối cách dùng cũ và nội dung `howToUse` không còn hiển thị. Tiêu đề phần thành phần dùng lại nhãn đã duyệt “Bảng Thành Phần & Cơ Chế Hoạt Động”. Giữ 70 Media, gallery và lưu ý sản phẩm; không xóa metadata lịch sử trên site đã cài.

> Bổ sung được người dùng duyệt ngày 15/10/2026: defaults **1.0.5** dùng bộ ảnh từ `template/Thumbnail`, gồm 40 ảnh sản phẩm và 7 avatar (45 WebP duy nhất do có ảnh trùng). Cài mới có 70 ảnh Media đang được tham chiếu; cập nhật site cũ giữ thêm ảnh lịch sử đang tồn tại. Xem [quản lý ảnh và cơ chế cập nhật giữ edits](THUMBNAIL-MEDIA.md). Các số liệu 30 Media bên dưới là hồ sơ của snapshot ban đầu.

> Hiện hành: **[1.0.1](../release/v1.0.1/RELEASE.md)**. Giữ nội dung đã duyệt, liên hệ và 30 Media; cập nhật bảo mật/hiệu năng và version defaults. Chuyển seed active sang 1.0.1 giữ nguyên 54 bản ghi nội dung và 30 Media.

Ngày 14/10/2026, người dùng yêu cầu dọn release cũ và đóng gói site hiện tại thành bản đầu tiên **1.0.0**. Defaults và hai package cùng version, trạng thái `approved`. [Hồ sơ release](../release/v1.0.0/RELEASE.md) ghi kết quả kiểm tra thực tế.

Snapshot chứa **6 bản ghi nội dung với 1.085 giá trị trường**, **10 sản phẩm, 1 combo, 35 hoạt chất và 30 ảnh Media**. Giữ nội dung, giá, SEO, chính sách, gallery, alt/caption, giá trị trống, thứ tự 2 mục chủ lực, 6 mục danh mục trang chủ và 4 danh sách phác đồ đã chọn.

Thông tin liên hệ dùng nguồn chung ở **Nội dung website → Quản lý liên hệ**. Hai URL do người dùng cung cấp đã lưu active/defaults:

- Zalo: `https://zalo.me/84985561862`
- Facebook: `https://web.facebook.com/hangnhatchomoinha.vn`

10 ảnh sản phẩm dùng file có sẵn trực tiếp trong `wp-content/themes/project-child/assets/fractional/assets/images/products/`; thư mục `excel-20261013` đã gỡ. [Dọn uploads](UPLOADS-CLEANUP-2026-10-14.md) giữ 30 ảnh đang dùng và loại 30 file thừa. Bộ cài không chứa các ảnh uploads dư.

Defaults thuộc `wp-content/plugins/project-core/defaults/fractional.json`; schema và ảnh nguồn thuộc child theme. Exporter đối chiếu SHA-256, dùng lại asset khớp byte hoặc đóng gói bản đã duyệt theo hash. Source key ánh xạ quan hệ, gallery và URL sang ID/domain site đích.

Initializer chọn homepage khi cài mới chưa có lựa chọn. Chạy lại giữ nội dung, Media metadata, ảnh đã xóa, phác đồ đã bỏ chọn, settings và Reading đã chỉnh; không tạo bản ghi hoặc ảnh trùng. Trên site đang dùng, chuyển seed version sang 1.0.0 giữ nguyên 54 bản ghi nội dung và 30 Media.

Không export tài khoản, credentials, SMTP secrets, đơn hàng, khách hàng, transient, revision, URL môi trường hoặc demo WordPress không thuộc website. Local/development/staging noindex độc lập với trạng thái approved; trang Cảm ơn luôn noindex. Domain public và indexing production cấu hình riêng.

Lần chốt tiếp theo dùng `scripts/export-fractional-defaults.php --user=<administrator-id> --version=<version> --approve`, tăng version hai package rồi build và thử cài mới. Exporter yêu cầu quyền quản trị/upload. Compiler HTML cũ bị chặn khi defaults đã approved để bảo vệ dữ liệu đã chốt.
