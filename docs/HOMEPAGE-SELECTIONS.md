# Chọn sản phẩm và combo trên trang chủ

Danh mục trang chủ có hai lựa chọn độc lập:

- **Sản phẩm:** tối đa 6 sản phẩm đã xuất bản. Trong từng sản phẩm, bật **Hiển thị trong danh mục trang chủ** và nhập thứ tự. Combo không chiếm suất sản phẩm. Khi đã đủ 6, bỏ chọn một sản phẩm trước khi thêm sản phẩm mới.
- **Combo:** mở combo đã xuất bản, bật **Hiển thị combo nổi bật** tại nhóm **Combo nổi bật trên website**. Bật combo mới tự thay thế combo cũ. Bỏ chọn combo đang dùng sẽ tắt các vị trí nổi bật của combo đó.

Combo nổi bật cung cấp ảnh Hero, thẻ combo trong mục chủ lực, khung combo ở danh mục trang chủ, phần giới thiệu combo nổi bật trên trang danh mục và các nút mua combo trong header, thanh điện thoại, CTA và các mục giới thiệu liên quan. Tên, ảnh, giá và quà tặng lấy từ CMS của combo đã chọn. Các combo khác vẫn có trong danh sách đầy đủ và trong các quan hệ sản phẩm/phác đồ đã cấu hình riêng.

Mục chủ lực giữ bố cục hai thẻ: khi có combo nổi bật, dùng sản phẩm chủ lực đứng đầu và combo đó; khi không chọn combo, dùng tối đa hai sản phẩm chủ lực. Lựa chọn này độc lập với 6 sản phẩm trong danh mục trang chủ.

Ảnh Hero trong Customizer dùng ảnh của combo nổi bật. Thay ảnh tại đây cũng cập nhật ảnh combo trong CMS; bản xem trước chưa ghi dữ liệu cho đến khi xuất bản. Văn bản thay thế lấy từ Media của ảnh hoặc tên combo.

Trong màn sửa Trang chủ của CMS, ô ảnh Hero dẫn tới màn sửa combo đang chọn; không còn ô ảnh riêng lưu vào metadata cũ rồi không hiển thị. Khi không chọn combo, phần vấn đề làn da không hiện nút “Chọn combo này”.

Bộ lọc nhu cầu của thẻ combo lấy từ các sản phẩm đã xuất bản trong quan hệ **Sản phẩm trong combo**. Tìm kiếm bao gồm tên của các sản phẩm đó; nội dung quà tặng không tự gán thêm nhu cầu cho combo.

## Chuyển dữ liệu và khởi tạo

Lựa chọn combo được lưu bằng một ID duy nhất trong option `project_core_fcc_featured_combo_id`. Toggle trong ACF đọc/ghi option này qua cơ chế lưu native, giữ kiểm tra quyền và nonce của WordPress/ACF. Giá trị `0` là chủ động không chọn combo, không tự quay về C–CX4.

Nâng cấp chuyển combo đã được chọn trong danh mục/chủ lực cũ sang lựa chọn mới một lần. Giữ nguyên các sản phẩm đã chọn, thứ tự và nội dung; không tự chọn thêm sản phẩm thứ sáu. Combo chưa xuất bản hoặc đã xóa không được đưa vào các vị trí nổi bật/nút mua mặc định.

Defaults dùng `homeCatalog` chỉ chứa source key sản phẩm và `featuredCombo` chứa một source key combo hoặc chuỗi rỗng. Export giữ lựa chọn này, không đưa ID local vào defaults. Chạy lại initializer bảo toàn lựa chọn đã sửa, kể cả lựa chọn rỗng.

## Kiểm tra

Hai script sau chỉ chạy khi `PROJECT_WORDPRESS_ROOT` trỏ đến site riêng có option `project_core_fcc_test_install`:

- `scripts/verify-fractional-home-catalog.php`: giới hạn sản phẩm, đổi/bỏ combo, migration và HTML trang chủ/danh mục.
- `scripts/verify-fractional-featured-combo-http.php`: control trong editor Combo, lưu native, quyền truy cập và nonce.

`scripts/verify-fractional-customizer-images.php` kiểm tra chỉ đọc đối với control ảnh và bản xem trước của Customizer.
