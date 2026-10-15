# Nhãn trên card sản phẩm

Vào **Nội dung website → Sản phẩm → Sửa sản phẩm → Thông tin & giá → Nhãn trên card sản phẩm**. Tích chọn **Sale**, **New**, **Best**, **Sold Out**, rồi nhấn **Cập nhật**. Có thể chọn nhiều nhãn; bỏ chọn hết để ẩn.

- Sale: khuyến mại, gradient đỏ cao cấp, chữ trắng có shadow và nếp gập 3D màu đỏ thẫm.
- New: sản phẩm mới, gradient xanh lá tươi mát tự nhiên, chữ trắng và nếp gập 3D màu xanh lá thẫm.
- Best: bán chạy, gradient vàng ánh kim hoàng gia (champagne gold) theo thương hiệu, chữ trắng và nếp gập 3D màu đồng đậm.
- Sold Out: hết hàng, gradient xám thanh lịch, chữ trắng và nếp gập 3D màu xám đậm.

Chỉ chọn nhãn phù hợp với thông tin đã xác nhận. Hệ thống không tự gán nhãn theo giá hoặc điểm đánh giá, và nhãn Sale không tự thay đổi giá bán.

Nhãn được thiết kế dạng giấy nhớ / ruy băng tab gập ôm ra ngoài viền bên trái ảnh sản phẩm (3D folded edge flap), xếp dọc tinh tế ở góc trên bên trái, dùng trên các card sản phẩm ở dạng danh sách (trang chủ mục danh mục, trang danh mục `/san-pham/`, trang nhu cầu da `/nhu-cau/`). Khu vực sản phẩm chủ lực trên trang chủ không hiển thị nhãn để giữ bố cục ảnh lớn thông thoáng. Giữ nguyên liên kết bấm ảnh mở trang chi tiết; nhãn không chặn thao tác bấm ảnh (pointer-events: none). Khi hover card sản phẩm, các nhãn có hiệu ứng trượt nhẹ phản hồi tương tác mượt mà.

Định nghĩa checkbox, danh sách giá trị hợp lệ và lưu trữ thuộc Project Core; component và CSS thuộc Project Child. Dữ liệu lưu ở `fcc_cardTags`, gồm các khóa `sale`, `new`, `best`, `sold_out`, theo thứ tự này. Field dùng quyền và nonce của editor WordPress/ACF. Giá trị ngoài danh sách bị từ chối khi validate và loại bỏ khi lưu/đọc.

Ở bản chốt **[2.0.0](../release/v2.0.0/RELEASE.md)**, defaults lấy đúng nhãn sản phẩm và combo hiện đang lưu trong CMS theo phê duyệt của người dùng, gồm các lựa chọn trống. Không cần ACF Sync; initializer giữ lựa chọn đã chỉnh hoặc đã bỏ chọn trên site có dữ liệu. Nhãn combo chỉnh trong phần thông tin combo và hiển thị trên card combo theo giao diện hiện tại.

Đã kiểm tra trên fixture riêng: 21 kiểm tra checkbox/validation/lưu/xóa/render/giữ edits; 281 kiểm tra browser ở 320, 375, 768, 1.440 px cho trang chủ và danh sách, gồm vị trí nhãn, màu, không đè CBMP và bấm xuyên nhãn vào chi tiết. Đã xem ảnh desktop/mobile. Màn hình 320 px có tràn ngang từ bố cục cũ ngay cả khi không có nhãn; thay đổi này không làm tăng độ tràn. Dữ liệu nhãn thử nghiệm chỉ bật trên fixture rồi khôi phục, không gắn nhãn cho sản phẩm trên site làm việc.
