# SEO Fractional CC – 1.0.10

Ngày 15/10/2026, tích hợp SEO vào site XAMPP hiện tại theo nội dung đã duyệt và các nhóm từ khóa người dùng cung cấp. Giữ nguyên giao diện, URL và nội dung hiển thị. Không thêm plugin SEO bên thứ ba.

## Vị trí chỉnh sửa

- **Nội dung website → SEO & từ khóa**: 7 nhóm, 62 truy vấn, ý định tìm kiếm, mức ưu tiên, trang liên quan, điều kiện triển khai và nguồn tham khảo. Mục này yêu cầu quyền `edit_pages`, chỉ đọc dữ liệu kế hoạch.
- Trang chủ, Danh mục, Vấn đề làn da: chỉnh tiêu đề, mô tả, ảnh chia sẻ ở tab **SEO** của trang.
- Sản phẩm: chỉnh **Tiêu đề SEO riêng**, **Mô tả SEO riêng**, **Ảnh chia sẻ riêng** trong khung **SEO & nhãn ảnh trang sản phẩm**. Giữ các field ACF có sẵn.
- Danh mục nghiên cứu nằm trong `wp-content/plugins/project-core/defaults/seo-keywords.json`; không tự xuất danh sách này vào HTML hoặc tạo trang công khai.

## Nội dung đã áp dụng

Cập nhật 26 giá trị title/description: trang chủ, danh mục, vấn đề làn da và 10 sản phẩm. Ưu tiên thương hiệu, serum vi kim, dưỡng ẩm, dưỡng sáng, retinol, vitamin C, AHA/BHA, niacinamide, PDRN và dưỡng môi theo hồ sơ từng sản phẩm. Trang vấn đề làn da tập trung đốm nâu/không đều màu, da sần/lỗ chân lông, nếp nhăn và thâm sau mụn.

Không suy thành phần của AX4 từ mô tả chung dòng A/C. Không biến từ khóa “trị mụn”, “trị nám” thành công dụng điều trị của mỹ phẩm. Không gắn nhãn thuần chay/organic, tạo showroom, đánh giá hoặc xếp hạng “tốt nhất”. Các truy vấn về kem chống nắng, sữa rửa mặt, tẩy trang, serum B5 riêng và so sánh đối thủ vẫn là kế hoạch biên tập khi có dữ liệu phù hợp.

Đây là ưu tiên theo mức phù hợp với danh mục và ý định tìm kiếm, chưa phải bảng xếp hạng lượng tìm kiếm. Chưa có dữ liệu Search Console/Keyword Planner hoặc bảng Google Trends hiện tại để chứng minh từ khóa “hot nhất”.

## Kỹ thuật

Child theme `inc/seo.php` thay phần SEO cũ trong `inc/fractional.php`:

- Mỗi trang Fractional CC có một title, description và canonical, dùng URL WordPress hiện tại; không hardcode domain và không giữ UTM trong canonical.
- Dùng metadata của bản ghi đang truy cập; hỗ trợ canonical phân trang của WordPress. Preview và nội dung có mật khẩu không xuất metadata tùy chỉnh.
- Open Graph/Twitter dùng cùng title/description; ảnh chia sẻ có kích thước và alt, sản phẩm dự phòng bằng ảnh đại diện.
- JSON-LD `WebSite`, `WebPage`, `Product` và breadcrumb sản phẩm lấy từ nội dung thật trong CMS. Giá `Offer` dùng VNĐ, chỉ xuất khi có giá dương. Không suy tồn kho, sao đánh giá, review, chứng nhận hoặc dịch vụ y tế.
- Dùng sitemap WordPress sẵn có, loại trang Cảm ơn. Local/staging, nội dung chưa duyệt và trang Cảm ơn giữ `noindex`; không thay tùy chọn Reading của site.
- Khi phát hiện Yoast, Rank Math, AIOSEO, SEOPress hoặc The SEO Framework, module nhường title/metadata/schema/canonical cho plugin. Có filter `project_child_fcc_external_seo` cho tích hợp khác. Chưa thử cài các plugin này; khi chuyển chủ sở hữu SEO cần cấu hình robots và sitemap tương ứng bên plugin mới.

Core plugin `src/Fractional/seo.php` quản lý trang hướng dẫn từ khóa, không xuất public metadata. Không thêm CSS/JS frontend.

## Defaults và nâng cấp

Defaults 1.0.10 kế thừa toàn bộ snapshot 1.0.9, chỉ thay 26 trường SEO và version. Đã đối chiếu dữ liệu có cấu trúc: mọi giá, ảnh, nội dung, liên hệ, quan hệ và thiết lập khác giữ nguyên. Không xuất lại toàn bộ CMS local để thay snapshot 1.0.9.

Trên site làm việc, cập nhật SEO có đối chiếu với baseline cũ và sao lưu metadata riêng trước khi ghi. Không thay field khác hoặc chạy lại initializer trên site làm việc.

Site mới nhận bộ SEO mặc định. Trên site đã dùng, initializer chỉ bổ sung field thiếu và bảo toàn metadata đã có, kể cả giá trị rỗng đã lưu. Các title/description trang cũ vì vậy không bị ép thay khi upload ZIP; chỉnh chúng trong ACF nếu muốn dùng nội dung mới. Không cần ACF Sync.

## Kiểm tra và đo lường

Chạy `C:/xampp/php/php.exe scripts/verify-fractional-seo.php` trên site local để kiểm tra HTTP. Dùng `PROJECT_WORDPRESS_ROOT` trỏ site riêng có marker `project_core_fcc_test_install` và thêm `--fixture` để kiểm tra sửa/xóa metadata, quyền quản trị, giá thiếu, escaping, nâng version seed và chủ sở hữu SEO bên ngoài. Không chạy các kiểm tra có sửa dữ liệu trên site làm việc.

Kết quả thực tế và hai ZIP: [release 1.0.10](../release/v1.0.10/RELEASE.md). Chưa triển khai hosting, kết nối Search Console hoặc gửi sitemap lên Google. Chưa xác nhận rich results bằng dịch vụ Google trên domain public; kiểm tra local xác nhận cấu trúc JSON và tính nhất quán với CMS.

Sau khi triển khai domain chính thức và cho phép index, theo dõi impression/click/CTR/truy vấn theo từng URL trong Search Console. Bài mới dùng Gutenberg, có nguồn và ngày cập nhật; nội dung điều trị cần người có chuyên môn duyệt.

## Nguồn tham khảo

- [Google SEO Starter Guide](https://developers.google.com/search/docs/fundamentals/seo-starter-guide): title/description riêng, nội dung hữu ích; Google không dùng meta keywords.
- [Google Product snippets](https://developers.google.com/search/docs/appearance/structured-data/product-snippet): yêu cầu mô tả sản phẩm và giá theo nội dung thực tế.
- [Think with Google – Vietnam Search for Tomorrow](https://www.thinkwithgoogle.com/_qs/documents/10586/Vietnam_-_Search_for_tomorrow_EN_version.pdf): tham khảo lịch sử về nhóm nhu cầu skincare; dữ liệu 2017–2020, không phải số liệu năm 2026.
- [AAD – Retinoid or retinol?](https://www.aad.org/public/everyday-care/skin-care-secrets/anti-aging/retinoid-retinol): nguồn cho kế hoạch biên tập kiến thức, không thay hướng dẫn sản phẩm.
- [Google Trends Việt Nam](https://trends.google.com/trends/explore?geo=VN): công cụ để kiểm tra mức quan tâm tương đối khi lập lịch bài tiếp theo.
