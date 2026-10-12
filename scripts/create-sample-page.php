<?php
/**
 * Create the local sample page once, without overwriting an existing page.
 * Run: C:\xampp\php\php.exe scripts/create-sample-page.php
 */

if ( 'cli' !== PHP_SAPI ) {
	http_response_code( 403 );
	exit( 'This script is available from the command line only.' );
}

$wordpress_root = getenv( 'PROJECT_WORDPRESS_ROOT' ) ?: dirname( __DIR__ );
require rtrim( $wordpress_root, '/\\' ) . '/wp-load.php';

if ( 'project-child' !== get_stylesheet() || ! function_exists( 'update_field' ) ) {
	fwrite( STDERR, "Activate Project Child and ACF before creating the sample page.\n" );
	exit( 1 );
}

$existing = get_posts( array( 'post_type' => 'page', 'post_status' => 'any', 'posts_per_page' => 1, 'meta_key' => '_project_core_sample_page', 'meta_value' => '1' ) );
if ( $existing ) {
	echo 'Sample page already exists: ' . get_permalink( $existing[0]->ID ) . PHP_EOL;
	exit( 0 );
}
if ( get_page_by_path( 'gioi-thieu-mau', OBJECT, 'page' ) ) {
	fwrite( STDERR, "The sample slug is already used by another page; no changes made.\n" );
	exit( 1 );
}

$administrators = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
if ( ! $administrators ) {
	fwrite( STDERR, "An administrator is required to create the sample page.\n" );
	exit( 1 );
}
wp_set_current_user( (int) $administrators[0] );
if ( ! current_user_can( 'publish_pages' ) ) {
	fwrite( STDERR, "The selected account cannot publish pages.\n" );
	exit( 1 );
}

$fields = array(
	'hero_eyebrow'        => 'Chào mừng đến với website của tôi',
	'hero_title'          => 'Cùng tạo nên những điều tốt đẹp.',
	'hero_description'    => 'Một nơi để chia sẻ câu chuyện, giới thiệu công việc và kết nối với những người quan tâm đến bạn.',
	'hero_button_text'    => 'Tìm hiểu thêm',
	'about_visible'       => 1,
	'about_title'         => 'Mỗi hành trình đều bắt đầu bằng một câu chuyện.',
	'about_description'   => "Chúng tôi tin rằng những ý tưởng có ý nghĩa bắt đầu từ sự lắng nghe và thấu hiểu.\n\nHãy dùng phần này để giới thiệu bạn là ai, điều bạn đang làm và giá trị bạn muốn mang đến cho mọi người.",
	'contact_visible'     => 1,
	'contact_title'       => 'Hãy bắt đầu một cuộc trò chuyện.',
	'contact_description' => 'Bạn có câu hỏi hoặc muốn cùng chúng tôi thực hiện một ý tưởng? Hãy liên hệ để chia sẻ thêm.',
	'contact_button_text' => 'Gửi email',
	'contact_email'       => 'hello@example.com',
);
foreach ( array_keys( $fields ) as $name ) {
	if ( ! acf_get_field( 'field_project_' . $name ) ) {
		fwrite( STDERR, 'Missing field definition: ' . $name . PHP_EOL );
		exit( 1 );
	}
}

$page_id = wp_insert_post(
	array(
		'post_type'    => 'page',
		'post_title'   => 'Trang giới thiệu mẫu',
		'post_name'    => 'gioi-thieu-mau',
		'post_status'  => 'publish',
		'post_author'  => get_current_user_id(),
		'post_content' => '<!-- wp:heading --><h2 class="wp-block-heading">Điều chúng tôi quan tâm</h2><!-- /wp:heading -->' . "\n" . '<!-- wp:paragraph --><p>Chất lượng, sự rõ ràng và những kết nối lâu dài. Chúng tôi luôn dành thời gian để hiểu nhu cầu của bạn và tìm ra cách làm phù hợp.</p><!-- /wp:paragraph -->',
		'meta_input'   => array( '_wp_page_template' => 'templates/sample-page.php', '_project_core_sample_page' => '1' ),
	),
	true
);
if ( is_wp_error( $page_id ) ) {
	fwrite( STDERR, $page_id->get_error_message() . PHP_EOL );
	exit( 1 );
}
$fields['hero_button_url'] = get_permalink( $page_id ) . '#gioi-thieu';
foreach ( $fields as $name => $value ) {
	update_field( 'field_project_' . $name, $value, $page_id );
	if ( (string) get_post_meta( $page_id, 'project_' . $name, true ) !== (string) $value ) {
		fwrite( STDERR, 'Could not save sample field: ' . $name . '. Page ID: ' . $page_id . PHP_EOL );
		exit( 1 );
	}
}
echo 'Created sample page: ' . get_permalink( $page_id ) . PHP_EOL;
echo 'Edit sample page: ' . admin_url( 'post.php?post=' . $page_id . '&action=edit' ) . PHP_EOL;
