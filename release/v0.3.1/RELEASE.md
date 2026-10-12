# Fractional CC 0.3.1

Ngày 12/10/2026. Bản local đã áp dụng mã nguồn trên XAMPP; chưa nghiệm thu production.

## Thay đổi

Trước đây text của thiết kế chỉ sửa trong ACF/CMS. Bản này bổ sung panel **Nội dung Fractional CC** trong Customizer, với 799 trường text/textarea theo từng trang/khu vực. Bấm bút chì cạnh chữ trong preview để mở đúng ô sửa, xem trước rồi **Xuất bản** vào cùng dữ liệu ACF. Sửa trong CMS và Customizer không tạo hai bản nội dung.

Các text nằm trong nút/link/biểu mẫu, thuộc tính và SEO được sửa trong panel, với full preview refresh khi cần. Không chèn bút chì vào phần tử tương tác/biểu mẫu. Ảnh, thông tin sản phẩm/giá và combo vẫn quản lý trong CMS. Không đổi template nguồn, bố cục hay hai CSS thiết kế; công cụ sửa chỉ xuất hiện trong preview xác thực.

Customizer quản lý nonce, capability và changeset. Setting chỉ được đăng ký cho field có trong schema/ACF và bản ghi người dùng được sửa; validate input, sanitize text và escape output. Native Selective Refresh dùng cùng renderer PHP; preview không ghi metadata. Không thêm plugin/page builder/dependency.

## Hai gói ZIP

| Gói | Bytes | SHA-256 |
| --- | ---: | --- |
| `project-core-v0.3.1.zip` | 44.658 | `B450FA5D4719B039D7A961D5ED407B384DB27C0E4E4D10E259067712A2D83788` |
| `project-child-v0.3.1.zip` | 8.696.607 | `3FB47AA065AA0B3032E49ED1205249CD5E69966E4C75F9B80363844DB6FC7F96` |

Dependencies: Kadence parent, ACF miễn phí và WordPress tương thích. Kiểm thử với XAMPP PHP `8.0.30`, WordPress `7.1.3`, ACF `6.8.10`. Slug theme/plugin giữ nguyên, cả hai header version `0.3.1`. Defaults nội dung và seed version giữ `0.3.0`; không cần chạy initializer hoặc migration để bật công cụ mới. Update giữ nguyên nội dung đã sửa và trạng thái local `noindex`.

## Kiểm thử

- PHP lint tất cả file PHP trong hai gói build.
- Customizer xuất panel/setting; đọc đúng giá trị hiện tại của ACF.
- Preview không đổi website công khai; chỉ preview tải marker/CSS/JS công cụ sửa.
- Native Selective Refresh trả đoạn nội dung mới; native Xuất bản lưu đúng field ACF, CMS đọc lại cùng giá trị.
- Từ chối nonce sai, input không phải text, field không đăng ký và preview của khách chưa đăng nhập; script injection bị sanitize/escape.
- Kiểm thử CMS trước đây vẫn đạt: lưu ACF, nonce, escaping, quyền và 503 controls trong editor trang chủ.
- Site chính giữ homepage ID `30`, 10 sản phẩm, 1 combo, 39 hoạt chất; năm loại trang và 29 tài nguyên local trả HTTP 200, không có warning/fatal trong response.
- Binding Customizer trên site chính được kiểm tra bằng preview trong bộ nhớ không ghi database.
- Hai CSS thiết kế khớp checksum template nguồn.

Chưa kiểm tra thao tác bút chì/keyboard và desktop/mobile qua trình duyệt vì không có browser kết nối; chưa cài lại từ ZIP trên site sạch khác. Kiểm thử HTTP không thay thế nghiệm thu trực quan. Luồng tiếp nhận đơn phía server nằm ngoài bản cập nhật này.

## Dùng và rollback

Mở [Tùy biến nội dung](http://localhost/fractionalcc-jp.vn/wp-admin/customize.php?autofocus%5Bpanel%5D=fcc_content), chọn khu vực hoặc bấm bút chì trong preview, sửa text và Xuất bản. Với tab đang mở từ trước khi cập nhật, tải lại bằng Ctrl+F5.

Site hiện vẫn dùng database chung với thư mục `wordpress` cũ. Lưu text là sửa metadata của database này; không tách database hay đổi môi trường trong bản cập nhật. Đã kiểm thử các thao tác ghi trên database QA riêng và khôi phục nội dung QA sau kiểm thử; không ghi text kiểm thử vào site chính.

Rollback code về gói trước sẽ bỏ công cụ Customizer; nội dung đã lưu qua ACF được giữ để CMS/rendering trước đây tiếp tục dùng. Sao lưu database/config/uploads riêng trước rollout/rollback trên môi trường khác. Không khôi phục toàn bộ database dùng chung nếu chưa kiểm tra các thay đổi phát sinh cần bảo toàn.
