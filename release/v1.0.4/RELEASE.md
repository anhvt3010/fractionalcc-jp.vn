# Fractional CC 1.0.4 – PageSpeed và phân phối ảnh

Bản sửa dựa trên báo cáo PageSpeed `hwzz9kig85` cho `https://fractionalcc-jp.vn/`. Đã sửa source và tạo bộ cài; chưa cập nhật hosting hoặc xác nhận điểm PageSpeed production sau triển khai.

## Bộ cài

| Package | Số file | SHA-256 |
| --- | ---: | --- |
| [Project Core 1.0.4](project-core-v1.0.4.zip) | 21 | `0dbcda58655c4d87d210483cb24b68306bc7067eac49d7d92ca1d73e0f5c938b` |
| [Project Child 1.0.4](project-child-v1.0.4.zip) | 246 | `9275ae58e0bce2db19a5ba14c7a95b1efe2b77ecc89e4c31cf763b07038a4994` |

Đúng hai ZIP với root `project-core/` và `project-child/`. WordPress, Kadence và ACF cài riêng; yêu cầu khai báo WordPress 6.3+, PHP 7.4+ và phiên bản PHP phù hợp dependencies thực tế. Core chỉ tăng version trong bản này, giữ các sửa email của 1.0.3. Defaults/initializer vẫn version 1.0.2; không cần migration hoặc chạy lại khởi tạo để áp dụng tối ưu.

## Chẩn đoán và thay đổi

- Hosting tải ảnh hero PNG khoảng 2,75 MB dù theme có WebP: Media được nhập từ bản gốc nhưng không có WebP cạnh file uploads. Helper trước đây chỉ dò WebP cùng thư mục nên bỏ lỡ bản tối ưu đã đóng gói. Bản mới đối chiếu SHA-256 của ảnh thực tế với manifest theme và chọn đúng derivative; không thay URL hoặc metadata ảnh đã lưu. Ảnh do khách thay mà khác bytes không bị đổi thành ảnh mặc định cũ. URL ngoài site giữ nguyên.
- Hero có WebP 480/768/1200px, `srcset`/`sizes`, kích thước và preload đồng bộ. Ví dụ hero: bản 480px khoảng 29 KB, 768px khoảng 58 KB, 1200px khoảng 102 KB. Thẻ spotlight/product/combo cũng dùng helper nên không tải lại PNG lớn ngay dưới banner. Nền hero và logo watermark/favicon dùng derivative phù hợp.
- Font Lora và Plus Jakarta Sans được đặt tại theme, giữ font-family/weight/style hiện có và `font-display: swap`; kèm hai giấy phép SIL OFL. Frontend không yêu cầu Google Fonts API/gstatic.
- Gộp font/design/responsive/integration CSS thành `site.min.css`; giữ CSS bổ sung của từng loại trang. Script tương tác dùng `defer`. Bỏ riêng phép đo scrollbar của theme mẹ trên các template tự dựng không dùng biến đó; không sửa source Kadence. Đọc kích thước slider trước khi ghi DOM.
- WebP có revision URL theo bytes derivative; CSS/JS có version theo file. `.htaccess` trong thư mục assets đặt cache dài cho tài nguyên công khai và bật nén CSS/JS nếu Apache có mod_deflate. Không áp dụng cache này cho HTML, admin, đơn hàng hay biên lai.
- Sửa heading không bỏ cấp; giữ kích thước chữ bằng CSS. Nút slider có vùng bấm thật 28px, chấm hiển thị giữ hình dạng cũ. Tăng độ đậm của một số nhãn vàng/xám nhỏ theo lỗi contrast được báo cáo. Bố cục, font, ảnh gốc và nội dung CMS được giữ.
- Build generator không còn ghi vào uploads hoặc hardcode tháng/máy local. Ảnh JPG/PNG trùng basename có derivative riêng, tránh dùng nhầm ảnh feedback.

## Cập nhật hosting

1. Sao lưu database, code và uploads riêng trên hosting.
2. Upload ZIP core qua **Plugin → Cài mới → Tải plugin lên**, chọn thay thế bản hiện tại. Upload ZIP child qua **Giao diện → Thêm mới → Tải theme lên**, chọn thay thế bản hiện tại. Giữ Project Child/Kadence và ACF đang hoạt động.
3. Không cần khởi tạo lại mặc định hoặc ACF Sync. Xóa cache hosting/CDN. Không sao chép database thử nghiệm lên hosting.
4. Kiểm tra trang chủ bằng cửa sổ ẩn danh trên điện thoại và desktop; kiểm tra ảnh hero, mở menu, slider, modal mua hàng, chọn sản phẩm và gửi một đơn thử theo quy trình của bạn.
5. Chạy lại PageSpeed mobile/desktop sau khi cache cập nhật. Báo cáo cũ không tự thay đổi khi source đã sửa.

Nếu hosting dùng Nginx hoặc CDN ghi đè header, `.htaccess` trong ZIP không điều khiển được lớp đó. Cấu hình cache dài cho **tài nguyên theme công khai đã có version** (`css`, `js`, `webp`, `woff2`) và bật gzip/Brotli cho HTML/CSS/JS ở máy chủ/CDN. Không bật cache public cho admin/AJAX/POST hoặc trang cảm ơn có dữ liệu đơn hàng. Kiểm tra header trả về thực tế; không chỉ dựa vào màn hình cài đặt plugin cache. Trang chủ hosting đã bật compression cho một số tài nguyên trong báo cáo cũ; XAMPP kiểm tra hiện chưa có mod_deflate.

Đối chiếu `currentSrc` của ảnh hero trong DevTools: phải là WebP có revision ở theme khi ảnh Media vẫn là bản đã duyệt. URL preload phải chọn cùng ứng viên với ảnh để không tải đồng thời cả bản lớn và nhỏ. Ảnh mới do khách tự upload vẫn hiển thị đúng; nếu chưa có derivative phù hợp, nó giữ ảnh thật và cần được tối ưu riêng.

## Số đo và kiểm tra

So sánh cùng fixture XAMPP ổ D:, Lighthouse CLI 13.5.0 mobile mặc định, không gửi mail ra ngoài. Đây là số đo lab local, không phải cam kết điểm production:

| Chỉ số mobile | Trước sửa | Sau sửa |
| --- | ---: | ---: |
| Dung lượng tải | 3.633.705 bytes | 633.068 bytes |
| FCP | 4,07 giây | 3,46 giây |
| LCP | 19,22 giây | 4,74 giây |
| Performance | 64 | 67 |
| Accessibility | 92 | 100 |
| Best Practices | 100 | 100 |

Desktop sau sửa: Performance 98, Accessibility 100, Best Practices 100, LCP 0,97 giây. Mobile vẫn cần đo lại trên hosting có nén HTTP; LCP local chưa đạt ngưỡng tốt. Điểm Performance dao động theo CPU/mạng: không dùng số desktop để thay cho mobile. SEO fixture là 66 do noindex/robots local có chủ đích, giữ nguyên trước/sau; không bỏ bảo vệ local để tăng điểm. Báo cáo production được cung cấp có SEO 100.

LCP audit và điểm Performance đều có giá trị hợp lệ ở các báo cáo local cuối. Lighthouse 13.5.0 vẫn ghi cảnh báo `LanternError: NO_LCP` ở phần insight nội bộ; chưa xác định được nguyên nhân thiếu LCP trong lần chạy PageSpeed production ban đầu chỉ từ snapshot. Không tuyên bố đã loại bỏ mọi khả năng `NO_LCP` trên dịch vụ PageSpeed; cần lần đo production mới sau cài đặt.

Kiểm tra đã chạy:

- PHP lint toàn bộ PHP trong hai ZIP; CRC/root/file count và bytes khớp source.
- 149 kiểm tra image/HTML/cache/security: preload khớp `picture`, một CSS bundle, script defer, URL ảnh/headers, dimensions, ảnh ngoài site và path traversal, ảnh khách khác bytes không nhận default cũ.
- 181 kiểm tra đơn hàng/biên lai/quyền riêng tư, 219 kiểm tra MIME email: đạt, không có mail gửi ra ngoài.
- 1.081 kiểm tra browser responsive của 10 trang sản phẩm trên 13 chiều rộng, tabs/gallery/modal mua hàng: đạt. Kiểm tra trực quan ảnh đầu trang desktop/mobile, giữ bố cục.
- Cài mới từ đúng hai ZIP trên WordPress/database riêng; kiểm tra approved defaults và chạy lại bảo toàn nội dung đã sửa/xóa. Chi tiết trong [qa/verification.txt](qa/verification.txt) và [qa/package-report.json](qa/package-report.json).

Chưa truy cập quản trị hosting, sửa cấu hình Nginx/CDN/DNS hoặc đo lại production sau deploy. Workspace hiện thiếu `wp-config.php`; không tuyên bố site làm việc đã kích hoạt bản mới. Nếu cần rollback, dùng hai ZIP 1.0.3 cùng backup phù hợp, bảo toàn đơn hàng/dữ liệu phát sinh.
