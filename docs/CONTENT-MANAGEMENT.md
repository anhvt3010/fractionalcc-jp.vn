# Quản lý ảnh và nội dung Fractional CC

> Ch?t 14/10/2026: to?n b? n?i dung/s?n ph?m/media/ph?c ?? hi?n t?i ?? ???c duy?t l?m [defaults 1.0.0](APPROVED-DEFAULTS.md). C?c release c? ?? x?a; [release ??u ti?n](../release/v1.0.0/RELEASE.md) l? h? s? hi?n h?nh. C?c m? t? ch? duy?t/local_sample c?a b?n tr??c d??i ??y l? l?ch s?.

Cập nhật 14/10/2026, bản 0.3.29. Các nội dung trước đây viết trực tiếp trong template đã có trường chỉnh sửa WordPress/ACF, giữ bố cục hiện tại. Dữ liệu cũ được chuyển sang các trường mới; cập nhật hoặc khởi tạo lại chỉ bổ sung giá trị còn thiếu, không ghi đè nội dung đã sửa hoặc ảnh đã xóa.

## Chỉnh sửa trên site local

Đăng nhập WordPress, mở **Nội dung website** để tìm trang hoặc dùng các liên kết dưới đây. Trong màn chỉnh sửa, chọn nhóm/tab tương ứng, sửa nội dung và bấm **Cập nhật**. Field ảnh mở thư viện Media; không cần chỉnh PHP hay đồng bộ ACF.

| Nội dung | Nơi chỉnh |
| --- | --- |
| Trang Vấn đề làn da: hero, 4 tình trạng, 4 ảnh, dấu hiệu, nguyên nhân, giải pháp, tiêu đề gợi ý, nguyên tắc, tư vấn và SEO | [Vấn đề làn da](http://localhost/fractionalcc-jp.vn/wp-admin/post.php?post=30&action=edit) → các tab 01–06 và SEO |
| Sản phẩm/combo theo tình trạng da | Cùng trang → **Gợi ý phác đồ – sản phẩm theo tình trạng da**. Chọn bản ghi hiện có, sắp xếp và lưu; bỏ hết lựa chọn sẽ ẩn phần danh sách |
| Nội dung công nghệ, giải thích chi tiết, 2 ảnh công nghệ/chu trình | [Trang chủ](http://localhost/fractionalcc-jp.vn/wp-admin/post.php?post=24&action=edit) → **Công nghệ – nội dung chi tiết** |
| Link video chu trình | Trang chủ → **Chu trình – video**. Để trống giữ đường dẫn Zalo hiện có |
| Tóm tắt giao hàng/thanh toán cạnh form và thông tin liên hệ | [Thông tin chung](http://localhost/fractionalcc-jp.vn/wp-admin/post.php?post=6&action=edit) → **Form đặt hàng – liên hệ & chính sách**; số hotline và số hiển thị ở nhóm thông tin thương hiệu |
| Chữ nút, nhãn thẻ, thông báo biểu mẫu | Thông tin chung → các nhóm **Nhãn…**, **Thông báo biểu mẫu & tương tác** |
| Biểu tượng website và ảnh chia sẻ mặc định | Thông tin chung → **Ảnh chia sẻ & biểu tượng** |
| Ảnh chia sẻ riêng cho từng trang | Trang chủ/Danh mục/Vấn đề làn da/Chính sách/Cảm ơn → **SEO** |
| Bộ lọc và khối tư vấn danh mục | Trang Danh mục → **Danh mục – bộ lọc**, **Danh mục – tư vấn** |
| Điểm/số lượt đánh giá sản phẩm và combo | Bản ghi Sản phẩm/Combo tương ứng. Trang chi tiết sản phẩm dùng số liệu của chính sản phẩm |
| SEO riêng và chữ callout sản phẩm | Bản ghi Sản phẩm → nhóm trình bày/SEO. Để trống dùng tên, tagline, ảnh chính và nhãn chung |
| Văn bản thay thế ảnh sản phẩm/gallery | **Media → Thư viện → ảnh → Văn bản thay thế**. Gallery vẫn quản lý bằng block Gallery trong editor sản phẩm |

ID trang trong liên kết là của site local hiện tại; site cài mới hãy mở qua menu WordPress, không sao chép các ID này.

## Lưu ý

- Các lựa chọn [sản phẩm theo phác đồ](REGIMEN-REVIEW.md) trên local vẫn **chờ người dùng duyệt**, chưa được đưa vào defaults hoặc migration.
- Các thông tin/đánh giá/claims có sẵn được giữ để chỉnh sửa, không được coi là đã kiểm chứng chỉ vì đã có ô quản lý. Trạng thái nội dung mẫu và chặn index local được giữ nguyên.
- Tóm tắt chính sách cạnh form và trang Chính sách có các trường riêng theo vị trí; khi thay chính sách cần cập nhật cả hai. Hotline hiển thị và link gọi đã dùng chung cấu hình.
- Số lượng/thứ tự FAQ, khối feedback, chu trình và cấu trúc menu vẫn theo template. Bản này không thêm trình dựng bố cục hoặc collection tùy ý; paragraph Gutenberg tự do chưa được render trong trang sản phẩm.
- [Báo cáo rà soát ban đầu](CONTENT-MANAGEMENT-AUDIT-2026-10-13.md) ghi hiện trạng trước cập nhật. [Hồ sơ release](../release/v1.0.0/RELEASE.md) ghi migration, kiểm tra và khôi phục.
