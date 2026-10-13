# Rà soát code và tài nguyên chưa dùng – 13/10/2026

> **Cập nhật: đã dọn theo yêu cầu.** Bản 0.3.26 đã bỏ hai hàm JS/handler cũ, nhánh PHP/helper không dùng, adapter lỗi thời, 162 rule CSS (135 nhóm selector), 18 ảnh trong theme và 5 attachment cũ. Đối chiếu trước/sau trên 15 trang giữ nguyên nội dung/link/ảnh và dữ liệu CMS; 24 form và các kiểm tra cài mới đạt. Xem [hồ sơ dọn và phát hành](../release/v0.3.26/RELEASE.md). Các mục bên dưới là báo cáo khảo sát trước khi dọn, không phải danh sách còn nguyên trong source.

Đã kiểm tra source của `project-core`, `project-child`, công cụ build và dữ liệu WordPress hiện tại trên XAMPP. Rà soát này chỉ đọc runtime/source và tạo báo cáo; không xóa ảnh, đổi nội dung, sửa CSS/JS hoặc chạy initializer. Workspace có cập nhật khác trong lúc kiểm tra, nên danh sách là ảnh chụp tại thời điểm rà soát, cần đối chiếu lại trước khi dọn.

## Kết quả chính

| Hạng mục | Kết quả | Mức xác nhận |
| --- | --- | --- |
| JavaScript | `renderOrderHandoff()` không có nơi gọi trong runtime | Đã đối chiếu tên hàm và luồng gửi đơn hiện tại |
| PHP | Nhánh fragment `usp`/`targets` và helper vẽ icon chỉ phục vụ nhánh này không còn được gọi từ template hiện tại | Đã đối chiếu các lời gọi và nguồn compiler; giữ các fragment khác |
| CSS chung | 154 rule, tương ứng 127 nhóm selector, không thấy được dùng trong HTML 15 trang hoặc PHP/JS hiện tại | Ứng viên dọn; chưa đo browser coverage/viewport/trạng thái tương tác |
| CSS đi kèm JavaScript cũ | Ba rule `.order-handoff`, `.order-handoff textarea`, `.order-handoff-actions` | Phụ thuộc hàm handoff không còn được gọi |
| Ảnh trong theme | 18 ảnh không có tham chiếu trực tiếp trong runtime/defaults/build script hoặc mapping media CMS | Ứng viên loại khỏi bản đóng gói, khoảng **3,96 MiB** (4.151.349 byte) |
| Ảnh trùng trong theme | 10 cặp có SHA-256 giống nhau | Trùng byte chính xác; các bản `image1.png`…`image10.png` chiếm **2,54 MiB**, đã nằm trong 18 ảnh trên, không cộng hai lần |
| Uploads | 5 attachment ảnh cũ chưa thấy tham chiếu hiện tại | Cần kiểm tra lần cuối trước khi xóa bằng WordPress; riêng file gốc khoảng **1,44 MiB** |
| PHP/template files | Chưa thấy template part dùng chung bị mồ côi; các hàm PHP có tham chiếu hoặc callback | Không đồng nghĩa mọi nhánh bên trong hàm đều đang chạy |

## JavaScript và PHP có thể dọn theo luồng hiện tại

1. `wp-content/themes/project-child/assets/fractional/main.js:420`: `renderOrderHandoff()` dựng khối sao chép nội dung rồi gửi bằng Zalo. Form hiện tại gọi AJAX để lưu đơn/gửi mail, không gọi hàm này. Dòng xử lý `input` tại khoảng dòng 222 chỉ xóa `.order-handoff` cũng là phần còn lại của luồng cũ. Khi bỏ hàm, bỏ đồng thời handler xóa khối và ba rule CSS tại `assets/fractional/assets/css/style.css:5519`.
2. `template-parts/fractional/components/product-detail.php:11`: nhánh `usp`/`targets` không có lời gọi `project_child_fcc_fragment('usp'|'targets')` trong template hiện tại. Template mới dựng các danh sách ở tab sản phẩm trực tiếp. Mapping `pd-usp-list`/`pd-target-list` trong compiler cũng không khớp ID của nguồn HTML hiện tại (`pd-target-list-bullets` là ID khác). Có thể dọn nhánh và mapping lỗi thời cùng nhau.
3. `inc/fractional-components.php:33`: `project_child_fcc_check_icon()` chỉ được gọi trong nhánh `usp`/`targets` trên. Nếu dọn nhánh thì helper này cũng không cần nữa. **Giữ** `project_child_fcc_clean_bullet()`: đang được tab sản phẩm gọi tại nhiều vị trí. **Giữ** fragment `gallery`, `actives`, `steps`, `combos`, `catalog`, `home_catalog`: tất cả có lời gọi thực tế.
4. `captureMarketingTracking()` vẫn được gọi và lưu UTM vào sessionStorage/`window.FCC_TRACKING`, nhưng chưa thấy consumer trong runtime hoặc trường tracking được đưa vào đơn hàng. Đây là code có đầu ra chưa được sử dụng, cần quyết định nối vào luồng nghiệp vụ hay bỏ; không coi tương đương hàm chưa được gọi.

## Công cụ build cần đồng bộ trước khi dọn

`scripts/adapt-fractional-interactions.mjs:78` tạo lại `handleOrderSubmit()` từ logic cũ; đoạn catch vẫn gọi `form.submit()` và không có phản hồi tại form/khóa gửi mới. Script ghi đè `assets/fractional/main.js` tại dòng 225 và vẫn được hướng dẫn chạy trong `docs/FRACTIONAL-WORDPRESS.md`.

Do đó, chỉ sửa hoặc xóa phần thừa trong file runtime là chưa đủ: chạy adapter có thể đưa code cũ trở lại và làm mất bản sửa form. Đây là công cụ có sử dụng trong workflow được ghi lại, không phải file mồ côi. Cần cập nhật nó cùng runtime nếu thực hiện dọn.

Compiler cũng sao chép toàn bộ ảnh từ `template/fractional-cc/assets/images` sang theme. Trước khi loại 18 ảnh khỏi package, cần điều chỉnh bước copy/manifest build hoặc tách nguồn lưu trữ, để build không chép lại ảnh đã bỏ. Không chạy các công cụ này trong lần rà soát.

## CSS cần review theo nhóm

154 rule/127 nhóm selector thuộc `wp-content/themes/project-child/assets/fractional/assets/css/style.css`. Ví dụ các nhóm bố cục cũ:

- `.trust-grid`, `.trust-item`, `.trust-icon-box`, `.trust-logo-img`, `.trust-text`.
- `.problem-grid`, `.problem-card`, `.problem-icon`, `.problem-insight`.
- `.mechanism-grid`, `.mechanism-media`, `.mechanism-steps`, `.step-card`.
- `.routine-grid`, `.routine-box`, `.routine-step-item`, `.routine-step-visual`.
- `.active-card`, `.actives-grid`, `.commit-grid`, `.commit-card`.
- `.pd-usp-section`, `.pd-usp-wrap`, `.pd-usp-list`, `.pd-target-list` và một số lớp heading/cert cũ.

Phép kiểm tra tĩnh xét selector nhóm, class/id trong HTML, class/id xuất hiện trong PHP/JS, và loại trường hợp chỉ thiếu `.admin-bar`. Không dùng việc “không thấy trên một trang” để kết luận selector thừa.

Các trường hợp **cần giữ**:

- `.admin-bar` trong `wordpress.css`/`catalog.css`: chỉ hiện khi người dùng đăng nhập, không có trong HTTP khách vãng lai.
- `fractional-content-editor.css`, `fractional-customizer.css`: dùng trong editor/Customizer, không tải trên frontend là đúng.
- CSS của trang vấn đề làn da: các class `concern-pane-card`, `concern-overview-grid`, `concern-regimen-section` có trong PHP hiện tại; đã loại khỏi danh sách dọn sau khi đối chiếu source.
- `sample-page.css` và template mẫu: hiện có 0 trang gán template này, nhưng đây là chức năng starter được giữ trong dự án. Không tự xóa như tài nguyên mồ côi.
- `assets/css/main.css` hiện chỉ có comment, 198 byte. Có thể tối giản enqueue theo ngữ cảnh nếu tối ưu tiếp; không đem lại tiết kiệm đáng kể và không phải lỗi render.

Chưa có browser coverage nên không khẳng định có thể xóa cả 154 rule ngay lập tức. Cần nghiệm thu desktop/mobile, modal, menu, tab, slider, Customizer và trạng thái đơn hàng khi thực hiện dọn.

## Ảnh: phạm vi và dữ liệu cần giữ

Theme có 61 ảnh. Cả 61 bản trong thư mục nguồn template có hash giống bản runtime tương ứng; đây là quan hệ nguồn/build có chủ đích, không cộng chúng vào 10 cặp trùng tên trong cùng theme.

18 ảnh dưới đây không được thấy trong runtime/defaults hoặc mapping `_fcc_asset_source`. Tuy nhiên, `banner/artboard-7.jpg`, `banner/hero-main.jpg`, `banner/feature-routine.jpg` vẫn được nhắc trong `template/.../assets/js/data.js` cũ; favicon/ảnh cơ chế còn được ghi trong brief; tên ảnh `imageN.png` còn là thông tin xuất xứ Excel trong `products_parsed.json`. Vì vậy nên loại khỏi runtime/package bằng workflow có kiểm soát, giữ nguồn tham chiếu nếu còn cần. Không xóa toàn bộ nguồn template hoặc ảnh mặc định chỉ vì website đang dùng bản copy ở uploads.

Uploads được đối chiếu với trường ảnh trong schema ACF, ảnh product/combo, thumbnail, ảnh trong block Gutenberg, URL trong nội dung, favicon/theme mods/widgets và HTML thực tế. File kích thước nhỏ/srcset là biến thể WordPress tạo ra, không tự coi là ảnh thừa. 5 attachment ở danh sách dưới là ứng viên; nếu dọn, dùng WordPress để xử lý cả metadata và biến thể, không chỉ xóa file bằng filesystem.

Không xóa hàng loạt field/schema cũ: phép tìm key literal tìm được 160 key không thấy trực tiếp trong render, nhưng gồm cả SEO được đọc bằng key động và dữ liệu lưu trữ. Kết quả này chỉ để định hướng kiểm tra, không phải 160 field có thể xóa.

## Phương pháp và giới hạn

- HTTP GET trên 15 trang: trang chủ, danh mục, vấn đề làn da, chính sách, cảm ơn và 10 trang sản phẩm, đều trả 200.
- Đọc source PHP/CSS/JS/JSON của plugin/theme và scripts; xét callback WordPress khi tìm hàm có nơi sử dụng.
- Đối chiếu ảnh với defaults, metadata media/CMS, block/nội dung và runtime; kiểm tra SHA-256 để chứng minh trùng byte.
- Runtime: child theme `project-child`, parent Kadence; plugin hoạt động ACF và `project-core`.
- Không can thiệp WordPress core, parent theme, plugin bên thứ ba, credentials/SMTP, lịch sử release hoặc dữ liệu khách hàng.
- Chưa kiểm tra browser coverage, dữ liệu ở hệ thống bên ngoài, trạng thái UI chưa render hoặc mọi metadata tùy biến của plugin bên thứ ba. Không suy ra rằng các file không thấy trong HTTP đều có thể xóa.

## Thứ tự dọn đề xuất

1. Đồng bộ adapter với runtime; bỏ handoff JS/CSS và nhánh PHP/mapping compiler lỗi thời.
2. Loại bản ảnh trùng tên và ảnh không cần khỏi package qua manifest/copy build; giữ nguồn được ghi trong brief/Excel.
3. Dọn CSS theo nhóm đã xác minh, nghiệm thu tương tác/viewport rồi mới mở rộng.
4. Đối chiếu 5 attachment lần cuối và dọn qua WordPress nếu không cần lưu trữ.

Các danh sách chi tiết dưới đây được lấy từ kết quả rà soát, không chứa nội dung khách hàng hay credentials.

## Danh sách 18 ảnh trong theme

Đường dẫn gốc: `wp-content/themes/project-child/assets/fractional/assets/images/`.

| File | KiB |
| --- | ---: |
| `banner/artboard-7.jpg` | 624.3 |
| `banner/fcc-hero-banner.jpg` | 196.3 |
| `banner/feature-routine.jpg` | 171.1 |
| `banner/hero-main.jpg` | 51.7 |
| `brand/image1.png` | 74.3 |
| `brand/image2.png` | 9.6 |
| `brand/logo-favicon.png` | 0.3 |
| `mechanism/mechanism-infographic.jpg` | 324.2 |
| `products/image1.png` | 373.6 |
| `products/image10.png` | 177.4 |
| `products/image2.png` | 260.1 |
| `products/image3.png` | 168.1 |
| `products/image4.png` | 230 |
| `products/image5.png` | 150.1 |
| `products/image6.png` | 173.3 |
| `products/image7.png` | 209.4 |
| `products/image8.png` | 670.1 |
| `products/image9.png` | 190 |

## Các cặp trùng byte trong theme

Cùng thư mục `assets/fractional/assets/images/products/`.

| Bản tên chung | Bản tên sản phẩm |
| --- | --- |
| `image1.png` | `needle-mask-c.png` |
| `image10.png` | `needle-serum-a.png` |
| `image2.png` | `needle-serum-c.png` |
| `image3.png` | `needle-mask-a.png` |
| `image4.png` | `pdrn-lip-essence.png` |
| `image5.png` | `mandelic-peel-essence.png` |
| `image6.png` | `pdrn-toning-essence.png` |
| `image7.png` | `needle-serum-ax4.png` |
| `image8.png` | `pdrn-lip-plumper.png` |
| `image9.png` | `needle-serum-cx4.png` |

## Attachment cũ cần đối chiếu trước khi dọn

| ID | File gốc |
| ---: | --- |
| 9 | `wp-content/uploads/2026/10/combo-c-cx4-2.png` |
| 16 | `wp-content/uploads/2026/10/feedback-1-2.jpg` |
| 17 | `wp-content/uploads/2026/10/feedback-2-2.jpg` |
| 18 | `wp-content/uploads/2026/10/feedback-3-2.jpg` |
| 19 | `wp-content/uploads/2026/10/feedback-4-2.jpg` |

## Đầy đủ 127 nhóm selector CSS ứng viên

Các selector xuất hiện nhiều lần ở media query được gộp trong danh sách này; tổng số rule là 154. Ba rule handoff được nêu riêng phía trên.

```css
.active-badge
.active-card
.active-card h3
.active-card p
.active-card:hover
.actives-grid
.actives-grid .active-card p
.badge-doctor
.badge-red
.brand-auth-seal
.brand-auth-seal svg
.brand-intro-box
.brand-origin-badge
.brand-philosophy-quote
.brand-stat-card
.brand-stat-lbl
.brand-stats-row
.brand-stat-val
.combo-card:hover .combo-image-wrap img
.combo-image-wrap
.combo-image-wrap img
.combo-items-list
.combo-items-list li
.combo-items-list li::before
.combo-save-pill
.combos-footer-link
.commit-card
.commit-card h3
.commit-card p
.commit-card:hover
.commit-grid
.commit-icon
.container-narrow
.corporate-details-list
.corporate-details-list li strong
.corporate-header
.corporate-logo
.duo-badge
.duo-badge-dark
.duo-img-box
.duo-img-box img
.duo-ingredients-grid
.duo-ingredients-title
.duo-spotlight-card:hover .duo-img-box img
.final-cta-badge
.final-cta-badge-dot
.final-cta-contacts-row
.final-cta-contacts-row .contact-pill-btn
.final-cta-direct-info
.final-cta-direct-info a
.final-cta-direct-info a:hover
.final-cta-info-sep
.form-secure-note
.hero-badge-top-spotlight
.hero-floating-pill
.hotline-pill
.hotline-pill:hover
.luxury-divider-emblem
.luxury-divider-line
.mechanism-grid
.mechanism-media
.mechanism-steps
.partnership-badge
.pd-actives-section
.pd-cert-badge
.pd-cert-badge svg
.pd-cocoon-cert-row
.pd-cocoon-heading
.pd-target-list
.pd-usp-list
.pd-usp-section
.pd-usp-wrap
.problem-card
.problem-card h3
.problem-card p
.problem-card:hover
.problem-grid
.problem-icon
.problem-insight
.problem-insight p
.product-badge-float
.product-spicule-pill
.quick-contacts-row
.review-photo-tag
.reviews-counter
.reviews-counter-current
.reviews-header-flex
.reviews-header-flex .section-header
.reviews-mobile-hint
.reviews-nav-controls
.routine-box
.routine-box-header
.routine-box-header h3
.routine-grid
.routine-icon-circle
.routine-icon-day
.routine-icon-night
.routine-product-mini
.routine-step-card-item:hover .routine-step-visual
.routine-step-item
.routine-step-role-gold
.routine-step-role-pill
.routine-step-text h4
.routine-step-text p
.routine-step-visual
.routine-step-visual img
.routine-step-visual-icon
.section-divider-luxury
.section-subtle
.section-tag-gold
.step-badge
.step-card
.step-card:hover
.step-content h3
.step-content p
.step-number
.text-muted
.text-red
.tingling-note
.tingling-note p
.tingling-note-icon
.trust-grid
.trust-icon-box
.trust-item
.trust-logo-img
.trust-text h4
.trust-text p
```
