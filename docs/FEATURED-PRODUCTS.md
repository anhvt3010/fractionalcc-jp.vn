# Sản phẩm chủ lực trên trang chủ

Trong **Nội dung website → Sản phẩm**, mở sản phẩm, bật **Sản phẩm chủ lực**, nhập **Thứ tự hiển thị**, rồi cập nhật. Combo được chọn tương tự tại **Nội dung website → Combo**. Trang chủ hiển thị tối đa hai mục đã xuất bản, số thứ tự nhỏ đứng trước. Bỏ chọn một mục hiện tại trước khi chọn mục thay thế. Khi bỏ chọn tất cả, khối sản phẩm chủ lực được ẩn.

Mặc định là Mandelic Shot Peel Essence và combo Needle Serum C–CX4 như ảnh mẫu được cung cấp. Ảnh, giá, đường dẫn và lựa chọn trong biểu mẫu đặt mua lấy từ hồ sơ đang được chọn. Các trường trong **Thông tin thẻ sản phẩm chủ lực** quản lý tiêu đề, mô tả, dòng đánh giá, thông số, điểm nổi bật, quà tặng và chữ nút. Tiêu đề/mô tả riêng có thể để trống để dùng nội dung hồ sơ. Điểm nổi bật nhập mỗi dòng theo dạng `Nhãn | Mô tả`.

Tiêu đề và mô tả chung của khối vẫn sửa tại trang chủ. Nội dung thẻ từng sản phẩm đã chuyển sang hồ sơ sản phẩm/combo; không sửa các trường cũ của thẻ trên trang chủ. Nội dung cũ vẫn được giữ trong database để có thể quay lại phiên bản trước.

Migration `project_core_fcc_featured_version = 1` chỉ bổ sung metadata còn thiếu, giữ nội dung trang chủ đã chỉnh sửa và giữ lựa chọn đã bỏ đánh dấu. Nó chạy một lần khi quản trị viên mở trang quản trị sau khi nâng cấp; initializer đầy đủ cũng gọi cùng cơ chế cho site mới. Các field được đăng ký bằng PHP, không cần ACF Sync. Không tự kích hoạt theme/plugin hoặc đổi homepage.

Trạng thái nội dung mẫu, các dữ kiện còn thiếu và khác biệt nguồn vẫn theo [WEBSITE-BRIEF.md](WEBSITE-BRIEF.md). Các dòng đánh giá và quà tặng được giữ từ mẫu đã cung cấp, không có bước xác minh nguồn ngoài trong tính năng này.
