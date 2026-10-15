<?php
/** Editorial keyword map; public metadata stays in the child theme.
 * @package Project_Core
 */
defined( 'ABSPATH' ) || exit;

function project_core_fcc_seo_menu() {
	add_submenu_page( 'project-website-content', 'SEO & từ khóa', 'SEO & từ khóa', 'edit_pages', 'fcc-seo', 'project_core_fcc_seo_guide' );
}
add_action( 'admin_menu', 'project_core_fcc_seo_menu' );

function project_core_fcc_seo_guide() {
	if ( ! current_user_can( 'edit_pages' ) ) { wp_die( esc_html__( 'Bạn không có quyền truy cập trang này.', 'project-core' ), '', array( 'response' => 403 ) ); }
	$plan = json_decode( file_get_contents( dirname( __DIR__, 2 ) . '/defaults/seo-keywords.json' ), true );
	if ( ! is_array( $plan ) || empty( $plan['groups'] ) ) { wp_die( esc_html__( 'Không đọc được bản đồ từ khóa.', 'project-core' ) ); }
	?>
	<div class="wrap">
		<h1>SEO & từ khóa Fractional CC</h1>
		<p><?php echo esc_html( $plan['method'] ); ?></p>
		<p>Sửa tiêu đề, mô tả và ảnh chia sẻ tại tab <strong>SEO</strong> của từng trang hoặc sản phẩm. Mỗi trang tập trung một nhu cầu chính và một mô tả riêng, đúng nội dung đang hiển thị. Các từ khóa bên dưới là hướng biên tập, không phải danh sách để dán vào mọi trang.</p>
		<p>Ngày rà soát: <?php echo esc_html( $plan['reviewed'] ); ?>. Bài kiến thức mới nên dùng Gutenberg, có nguồn, ngày cập nhật và người duyệt chuyên môn khi đề cập điều trị. Các bài đề xuất chưa được tạo hoặc xuất bản tự động.</p>
		<?php foreach ( $plan['groups'] as $group ) : ?>
			<h2><?php echo esc_html( $group['name'] ); ?></h2>
			<p><strong><?php echo esc_html( $group['priority'] . ' · ' . $group['intent'] ); ?></strong> — <?php echo esc_html( $group['status'] ); ?></p>
			<ul><?php foreach ( $group['keywords'] as $keyword ) : ?><li><?php echo esc_html( $keyword ); ?></li><?php endforeach; ?></ul>
			<p><?php echo esc_html( $group['note'] ); ?></p>
			<?php $id = $group['scope'] ? project_core_fcc_page_id( $group['scope'] ) : 0; ?>
			<?php if ( $id && current_user_can( 'edit_post', $id ) ) : ?>
				<p><a class="button" href="<?php echo esc_url( get_edit_post_link( $id, 'raw' ) ); ?>">Sửa SEO trang liên quan hiện có</a> <a class="button" href="<?php echo esc_url( get_permalink( $id ) ); ?>">Xem nội dung hiện có</a></p>
			<?php endif; ?>
		<?php endforeach; ?>
		<h2>Nguồn tham khảo & đo lường</h2>
		<ul><?php foreach ( $plan['sources'] as $source ) : ?><li><a href="<?php echo esc_url( $source['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $source['title'] ); ?></a>: <?php echo esc_html( $source['note'] ); ?></li><?php endforeach; ?></ul>
		<p>Trên domain public, dùng Search Console để kiểm tra lập chỉ mục và theo dõi truy vấn, impression, click, CTR theo từng URL. Sitemap dùng sẵn của WordPress: <a href="<?php echo esc_url( home_url( '/wp-sitemap.xml' ) ); ?>">wp-sitemap.xml</a>. Local/staging và trang Cảm ơn tiếp tục noindex. Không có cam kết thứ hạng hoặc thời gian lên top.</p>
	</div>
	<?php
}
