# Fractional CC 1.0.1 – khôi phục đồng ý trong popup

Popup ở trang chủ, danh mục, vấn đề làn da và sản phẩm thiếu checkbox, trong khi server yêu cầu đồng ý nên đơn hợp lệ bị từ chối. Bản cập nhật khôi phục checkbox bằng nội dung đã có trong CMS; nút Mua ngay chỉ bật khi tích chọn và chưa gửi yêu cầu.

## Bộ cài

| Package | Số file | SHA-256 |
| --- | --- | --- |
| [Project Core 1.0.1](project-core-v1.0.1.zip) | 21 | `54c04a5016f0ed6dbf94d1700fb70b43bb972f014b3f313d819096d25276cb1c` |
| [Project Child 1.0.1](project-child-v1.0.1.zip) | 130 | `b29cbf4e012740e078403016867011431c14c36fe5fa4c9f131ab9235c760a95` |

Đúng hai ZIP với root project-core/project-child, CRC và byte khớp source. Không có config, secrets, database, uploads hoặc browser profiles. [Báo cáo package](qa/package-report.json).

## Hành vi

- Ba mẫu popup dùng chung component checkbox, mặc định chưa chọn và required. Trang sản phẩm dùng câu đồng ý riêng đã lưu trong CMS; các popup còn lại dùng câu đồng ý tư vấn qua điện thoại hiện có.
- Nút popup disabled ngay trong HTML. JavaScript đồng bộ trạng thái checkbox và gửi yêu cầu cho cả form inline/popup; bỏ tích hoặc reset khóa lại nút.
- Trong khi đang gửi, nút luôn bị khóa. Response lỗi không bật nút nếu người dùng đã bỏ tích; thiếu control đồng ý cũng không gửi request.
- Giữ kiểm tra consent ở server, nonce, validation và throttle của 1.0.0. Không thêm trường dữ liệu mới hoặc đổi thiết kế ngoài checkbox/trạng thái nút được yêu cầu.

Defaults approved 1.0.1 giữ 6 bản ghi/1.085 trường, 10 sản phẩm, 1 combo, 35 hoạt chất, 30 Media và các liên hệ đã duyệt. Chạy initializer trên site active giữ nguyên 54 bản ghi nội dung và 30 Media. Không cần ACF Sync hoặc reset nội dung.

## Kiểm tra

- PHP lint, JavaScript syntax, client test: trạng thái chưa tích/tích/bỏ tích/reset, control thiếu, đang gửi, payload consent, lỗi server/network và chống gửi trùng.
- Cài/kích hoạt hai ZIP trên database riêng: 2.644 defaults, 291 contacts, 44 order AJAX và 44 native POST checks; mail được capture/chặn, không gửi thật.
- 1.081 kiểm tra trình duyệt: 10 sản phẩm/13 widths, cả ba mẫu popup ở mobile/desktop, checkbox bắt buộc, FormData có agree_terms=on, thao tác Space bằng bàn phím, reset/reopen và nút disabled. Không có lỗi console. [Báo cáo](qa/report.json), [mobile](qa/consent-390.png), [desktop](qa/consent-1440.png).

Đã thử trên XAMPP PHP 8.0.30, WordPress 7.1.3, Kadence 1.5.2, ACF 6.8.10. Chưa kiểm tra hosting/Safari/email delivery thật. Dependency cài riêng; lưu ý PHP production theo [báo cáo bảo mật](../../docs/SECURITY-AUDIT-2026-10-14.md).

## Cập nhật hosting

1. Sao lưu database/uploads/config và code trên hosting.
2. WordPress → Plugin → Cài mới → Tải plugin: chọn Project Core ZIP và **Thay thế bản hiện tại**.
3. Giao diện → Theme → Thêm mới → Tải theme: chọn Project Child ZIP và **Thay thế bản hiện tại**; giữ Project Child active và Kadence được cài.
4. Mở admin bằng administrator có upload_files để initializer cập nhật version, giữ nội dung hiện có. Không reset defaults hoặc import database local.
5. Xóa cache WordPress/hosting/CDN và tải lại trình duyệt. Kiểm tra cả home/catalog/concerns/product: chưa tích nút bị khóa, tích nút bật, bỏ tích khóa lại.

HTML cũ từ cache có thể vẫn thiếu checkbox nên cần purge cache sau thay ZIP. Bộ cài sẵn sàng cập nhật; tác vụ này chưa thao tác trực tiếp trên hosting. Giữ [1.0.0](../v1.0.0/RELEASE.md) để đối chiếu; rollback cả bản sẽ đưa lại lỗi checkbox, nên ưu tiên sửa tiếp hoặc dùng backup môi trường đích có kế hoạch.
