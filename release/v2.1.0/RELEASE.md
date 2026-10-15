# Fractional CC 2.1.0 – Chốt dữ liệu và cập nhật trang chủ

Ngày 15/10/2026. Người dùng duyệt hiện trạng làm dữ liệu mặc định. Đã xuất lại CMS đang hoạt động, build và kiểm tra hai ZIP cuối cùng. Plugin, child theme và snapshot cùng version **2.1.0**, trạng thái `approved`. Chưa triển khai hosting.

## Hai gói cài đặt

| Package | Số file | SHA-256 |
| --- | ---: | --- |
| [Project Core 2.1.0](project-core-v2.1.0.zip) | 28 | `6cbbd2deab7bd1108e4ea5fc5a355f764378303822de88f0519f4071f6b4248c` |
| [Project Child 2.1.0](project-child-v2.1.0.zip) | 295 | `daa177f86cf2982a449b3e4474a87c978a83f2af9d17f84da865e461adf35954` |

Đúng hai ZIP cài qua WordPress, root lần lượt `project-core/` và `project-child/`. Plugin chứa nghiệp vụ, snapshot và initializer; child theme chứa giao diện, ACF schema và ảnh đã duyệt. Không chứa WordPress core, Kadence, ACF, config, credentials, database, tài khoản, đơn hàng, logs hoặc uploads riêng.

Dependencies cài riêng: WordPress, Kadence và ACF. Header yêu cầu WordPress 6.3+, PHP 7.4+; môi trường đích còn phải đáp ứng yêu cầu của dependencies. Đã kiểm tra với WordPress **7.1.3**, Kadence **1.5.2**, ACF Free **6.8.10**, PHP CLI XAMPP **8.2.12** và Chrome headless. Không coi đây là chứng nhận đã thử mọi phiên bản PHP/WordPress trong khoảng hỗ trợ. [Runtime nguồn](qa/runtime.json).

## Dữ liệu và chức năng được chốt

- **6 bản ghi nội dung, 1.096 field, 10 sản phẩm, 1 combo, 35 hoạt chất, 71 Media**, cùng gallery, ảnh đại diện/hover, alt/caption, giá, SEO, liên hệ, các quan hệ và 8 thiết lập công khai.
- Danh mục trang chủ chọn 6 sản phẩm: Mandelic, Needle Serum C, Needle Serum CX4, PDRN Shot 1000+, Needle Serum AX4 và PDRN Shot Lip Essence, đúng thứ tự CMS. Combo không chiếm suất sản phẩm.
- Combo nổi bật độc lập, chỉ chọn một; snapshot chọn `combo_C-CX4`. Bật combo khác thay thế combo cũ; bỏ chọn giữ trạng thái không có combo. Ảnh, tên, giá, sản phẩm đi kèm và nút mua dùng combo đang chọn.
- Phần vấn đề làn da nằm ngay trước điểm nổi bật; giữ nhãn “VẤN ĐỀ LÀN DA”, các sửa đổi câu chữ, tiêu đề viết hoa, nền và các phần đã được yêu cầu bỏ. Giữ nguyên UI hiện tại.
- Customizer cho sửa/thay ảnh và xem trước. Ảnh Hero dùng ảnh của combo; màn sửa Trang chủ trong CMS dẫn tới đúng màn sửa ảnh combo. Bộ lọc và tìm kiếm combo dùng sản phẩm liên kết; bỏ chọn combo không còn nút “Chọn combo này” trong phần vấn đề da.
- `contentManagement.version=2` bổ sung các field mới còn thiếu trên site đã cài, giữ giá trị đã sửa hoặc để trống.
- Cài mới khôi phục toàn bộ 71 ảnh được duyệt, kể cả ảnh thư viện chưa được section nào sử dụng. Chạy lại trên site đã có seed chỉ bổ sung ảnh cần cho nội dung thiếu; không khôi phục ảnh đã chủ động bỏ chọn.

Snapshot SHA-256: `dc4b170603d37d0cebd314a10842385749f336cf23bb3daecd88091d717ef784`.

Không chạy initializer/reset catalog trên site làm việc. Đối chiếu trước/sau xác nhận nội dung, metadata, Reading, lựa chọn sản phẩm/combo và seed của site nguồn giữ nguyên; seed nguồn vẫn **2.0.0**. Chỉ source/header và bộ mặc định trên đĩa chuyển sang 2.1.0.

## Cài mới và cập nhật

1. Sao lưu riêng database, uploads và code của môi trường đích; kiểm tra có thể phục hồi.
2. Cài/kích hoạt Kadence và ACF. Upload ZIP Core trong **Plugin**, ZIP Child trong **Giao diện**; chọn thay thế khi cập nhật. Dùng đúng cặp 2.1.0 và kích hoạt Project Core/Project Child.
3. Mở admin bằng administrator có quyền upload. Cài mới sẽ khởi tạo khi đủ dependencies. Trên site đã dùng, vào **Nội dung website → Bổ sung dữ liệu còn thiếu** để ghi nhận seed 2.1.0. Không cần ACF Sync.
4. Xóa cache và kiểm tra trang chủ, danh mục, combo nổi bật, thay ảnh, chi tiết sản phẩm và biểu mẫu theo quy trình của site.

Cài mới nhận snapshot vừa chốt. Cập nhật site đã có dữ liệu **không ghi đè nội dung, giá, ảnh, lựa chọn và settings đã tồn tại**, kể cả giá trị trống. Vì vậy, upload ZIP không tự ép câu chữ mặc định mới lên nội dung đã lưu trên hosting. Không dùng reset hoặc nhập lại catalog để đồng bộ nội dung. Domain, secrets và cấu hình gửi mail quản lý riêng trên môi trường đích.

Rollback bằng backup môi trường đích và cặp package trước đã kiểm chứng, bảo toàn dữ liệu mới phát sinh. Không sao chép database QA lên hosting.

## Kiểm tra từ hai ZIP cuối cùng

| Kiểm tra | Kết quả |
| --- | --- |
| Root ZIP, file count, CRC, SHA-256, byte khớp source, không có file runtime riêng | Đạt cho cả hai ZIP |
| Đối chiếu CMS nguồn với snapshot | **1.672 đạt**, không ghi CMS |
| Cài mới: field, nội dung, Media, metadata ảnh, quan hệ, settings và chạy lại | **3.021 đạt**, đủ 71 ảnh, không trùng Media |
| Nâng từ ZIP 2.0.0 lên 2.1.0, admin migration và chạy lại hai lần | **7.906 đạt**, giữ 128 bản ghi hiện có, giá trị đã sửa/xóa, gallery, tags, SEO, lựa chọn rỗng và Reading |
| Thiếu ACF / thiếu child theme | Từ chối khởi tạo, không ghi nội dung |
| Giá, tên, ảnh, thành phần và dữ liệu danh mục | **305 đạt** |
| Giới hạn 6 sản phẩm, đổi/bỏ combo, migration và quyền | **36 đạt** |
| Editor Trang chủ/Combo, lưu native và nonce | **11 đạt** |
| Sản phẩm trong combo, giá, bộ lọc, tìm kiếm và escaping | **30 đạt** |
| Customizer ảnh, shortcut, preview, validation và quyền | **117 đạt** |
| SEO/HTTP trên 15 trang | **290 đạt** |
| Bảo mật và biểu mẫu | **82 + 44 đạt**, kiểm tra client đạt; email bị chặn gửi |
| Browser trang chủ/danh mục tại 375, 768, 1.440 px | **25 đạt**, ảnh nội bộ, thứ tự, 6 sản phẩm + combo, nút mua, bộ lọc, không tràn ngang |
| Browser đối chiếu source/ZIP: trang chủ, danh mục, vấn đề da ở 375/1.440 px | **7 đạt**, nội dung, heading, thẻ và console khớp |
| Responsive chi tiết 10 sản phẩm ở 13 độ rộng | **1.846 kiểm tra đạt trước khi dừng tại lỗi popup đã có**, chưa đạt hoàn toàn; xem bên dưới |
| PHP lint và JavaScript syntax | Đạt |

Fixture cài mới và nâng cấp dùng database/site riêng, package giải nén từ ZIP, không dùng junction tới source dự án. [Kiểm tra package](qa/package-report.json), [cài mới](qa/defaults.txt), [nguồn](qa/source-snapshot.txt), [nâng cấp](qa/upgrade.txt), [dependencies](qa/dependencies.txt), [danh mục](qa/catalog.txt), [lựa chọn/editor](qa/selection.txt), [ảnh/combo](qa/combo-images.txt), [SEO](qa/seo.txt), [bảo mật/form](qa/order-security.txt), [browser](qa/browser/review.json), [đối chiếu](qa/comparison/comparison.json), [responsive chi tiết](qa/product-browser/report.json).

Chạy lại kiểm tra archive từ root dự án: `D:/xampp/php/php.exe -d extension=zip release/v2.1.0/qa/verify-packages.php`. Cần ZIP PHP extension; không cần đổi cấu hình PHP chung.

## Hạn chế đã xác nhận

**Popup gallery Mandelic với 5 ảnh có phần ảnh/nút không vừa khung tại 320–375 px.** Đã tái hiện trên cả site làm việc và site cài từ ZIP; 1.440 px vừa khung. Đây là hạn chế đã ghi ở 2.0.0, được giữ nguyên theo yêu cầu đóng gói hiện trạng. Bộ responsive dừng tại Mandelic 320 px sau 1.846 kiểm tra; không coi kết quả này là responsive đạt hoàn toàn. [Số đo đối chiếu](qa/comparison/comparison.json).

Chưa triển khai hosting, chưa thử upload qua wp-admin trên hosting, chưa gửi mail thật hoặc xác minh domain/Search Console. Local/staging tiếp tục noindex; trang Cảm ơn luôn noindex.
