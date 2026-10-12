<?php
/**
 * Presentation-owned sample page fields and content access.
 *
 * @package Project_Child
 */

defined( 'ABSPATH' ) || exit;

function project_child_register_sample_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_project_sample_page',
			'title'    => 'Nội dung trang giới thiệu',
			'fields'   => array(
				array( 'key' => 'field_project_hero_tab', 'label' => 'Mở đầu', 'type' => 'tab' ),
				array( 'key' => 'field_project_hero_eyebrow', 'name' => 'project_hero_eyebrow', 'label' => 'Dòng chữ nhỏ phía trên', 'type' => 'text', 'maxlength' => 100 ),
				array( 'key' => 'field_project_hero_title', 'name' => 'project_hero_title', 'label' => 'Tiêu đề chính', 'type' => 'text', 'required' => 1, 'maxlength' => 200 ),
				array( 'key' => 'field_project_hero_description', 'name' => 'project_hero_description', 'label' => 'Mô tả ngắn', 'type' => 'textarea', 'rows' => 3, 'new_lines' => '', 'maxlength' => 1000 ),
				array( 'key' => 'field_project_hero_image', 'name' => 'project_hero_image', 'label' => 'Ảnh mở đầu', 'type' => 'image', 'return_format' => 'id', 'preview_size' => 'medium', 'instructions' => 'Không bắt buộc. Chọn ảnh trong Thư viện và điền văn bản thay thế (alt) nếu ảnh có ý nghĩa.', 'mime_types' => 'jpg,jpeg,png,webp' ),
				array( 'key' => 'field_project_hero_button_text', 'name' => 'project_hero_button_text', 'label' => 'Chữ trên nút', 'type' => 'text', 'maxlength' => 80 ),
				array( 'key' => 'field_project_hero_button_url', 'name' => 'project_hero_button_url', 'label' => 'Đường dẫn nút', 'type' => 'url', 'instructions' => 'Nhập URL đầy đủ. Bỏ trống để ẩn nút.' ),
				array( 'key' => 'field_project_about_tab', 'label' => 'Giới thiệu', 'type' => 'tab' ),
				array( 'key' => 'field_project_about_visible', 'name' => 'project_about_visible', 'label' => 'Hiện phần giới thiệu', 'type' => 'true_false', 'ui' => 1, 'default_value' => 1 ),
				array( 'key' => 'field_project_about_title', 'name' => 'project_about_title', 'label' => 'Tiêu đề giới thiệu', 'type' => 'text', 'maxlength' => 200 ),
				array( 'key' => 'field_project_about_description', 'name' => 'project_about_description', 'label' => 'Nội dung giới thiệu', 'type' => 'textarea', 'rows' => 5, 'new_lines' => '', 'maxlength' => 3000 ),
				array( 'key' => 'field_project_contact_tab', 'label' => 'Liên hệ', 'type' => 'tab' ),
				array( 'key' => 'field_project_contact_visible', 'name' => 'project_contact_visible', 'label' => 'Hiện phần liên hệ', 'type' => 'true_false', 'ui' => 1, 'default_value' => 1 ),
				array( 'key' => 'field_project_contact_title', 'name' => 'project_contact_title', 'label' => 'Tiêu đề liên hệ', 'type' => 'text', 'maxlength' => 200 ),
				array( 'key' => 'field_project_contact_description', 'name' => 'project_contact_description', 'label' => 'Lời mời liên hệ', 'type' => 'textarea', 'rows' => 3, 'new_lines' => '', 'maxlength' => 1000 ),
				array( 'key' => 'field_project_contact_button_text', 'name' => 'project_contact_button_text', 'label' => 'Chữ trên nút email', 'type' => 'text', 'maxlength' => 80 ),
				array( 'key' => 'field_project_contact_email', 'name' => 'project_contact_email', 'label' => 'Email nhận liên hệ', 'type' => 'email', 'instructions' => 'Thay email mẫu bằng địa chỉ thật của bạn. Nút sẽ mở ứng dụng email của khách, không phải form gửi email trên website.' ),
			),
			'location' => array(
				array(
					array( 'param' => 'post_type', 'operator' => '==', 'value' => 'page' ),
					array( 'param' => 'page_template', 'operator' => '==', 'value' => 'templates/sample-page.php' ),
				),
			),
			'position' => 'normal',
			'active'   => true,
			'show_in_rest' => 0,
		)
	);
}
add_action( 'acf/init', 'project_child_register_sample_fields' );

/**
 * Read native post meta so saved content still renders if ACF is disabled.
 * Field definitions and editing remain ACF's responsibility.
 *
 * @param int $post_id Page ID.
 * @return array
 */
function project_child_get_sample_content( $post_id ) {
	$names = array(
		'hero_eyebrow', 'hero_title', 'hero_description', 'hero_image',
		'hero_button_text', 'hero_button_url', 'about_visible', 'about_title',
		'about_description', 'contact_visible', 'contact_title',
		'contact_description', 'contact_button_text', 'contact_email',
	);
	$content = array();
	foreach ( $names as $name ) {
		$value = get_post_meta( $post_id, 'project_' . $name, true );
		$content[ $name ] = is_scalar( $value ) ? (string) $value : '';
	}
	return $content;
}

function project_child_enqueue_sample_styles() {
	if ( ! is_page_template( 'templates/sample-page.php' ) ) {
		return;
	}
	$path = '/assets/css/sample-page.css';
	wp_enqueue_style( 'project-child-sample', get_stylesheet_directory_uri() . $path, array( 'project-child-main' ), (string) filemtime( get_stylesheet_directory() . $path ) );
}
add_action( 'wp_enqueue_scripts', 'project_child_enqueue_sample_styles', 30 );
