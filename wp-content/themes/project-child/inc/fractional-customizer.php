<?php
/**
 * Native Customizer controls and shortcuts for the existing ACF presentation fields.
 *
 * @package Project_Child
 */

defined( 'ABSPATH' ) || exit;

/** Return a stable control/setting identifier without creating another content store. */
function project_child_fcc_customizer_id( $scope, $key ) {
	return 'fcc_text_' . $scope . '_' . $key;
}

/** Register text controls against the same post metadata used by the CMS. */
function project_child_fcc_customize_register( $manager ) {
	if ( ! function_exists( 'acf_get_field' ) || ! function_exists( 'project_core_fcc_page_id' ) || ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	require_once __DIR__ . '/class-fcc-customize-setting.php';
	$manager->add_panel(
		'fcc_content',
		array(
			'title'       => 'Nội dung Fractional CC',
			'description' => 'Sửa chữ, chọn ảnh và xem trước theo từng khu vực. Nhấn Xuất bản để lưu vào ACF/CMS. Thông tin sản phẩm/giá được quản lý trong CMS.',
			'priority'    => 25,
		)
	);

	$labels  = array( 'home' => 'Trang chủ', 'shared' => 'Nội dung chung', 'catalog' => 'Danh mục', 'policies' => 'Chính sách', 'thankyou' => 'Cảm ơn' );
	$schema  = project_child_fcc_schema();
	$counter = 0;
	foreach ( $labels as $scope => $label ) {
		$post_id = project_core_fcc_page_id( $scope );
		if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) || empty( $schema[ $scope ] ) ) {
			continue;
		}
		foreach ( $schema[ $scope ] as $section => $fields ) {
			$section_id = 'fcc_section_' . $scope . '_' . substr( md5( $section ), 0, 12 );
			$heading    = array( 'hero-section' => 'Khối đầu trang (Hero)', 'trust-bar' => 'Thanh cam kết' );
			$manager->add_section( $section_id, array( 'title' => $label . ' · ' . ( isset( $heading[ $section ] ) ? $heading[ $section ] : $section ), 'panel' => 'fcc_content', 'priority' => ++$counter ) );
			foreach ( $fields as $field ) {
				if ( ! in_array( $field['type'], array( 'text', 'textarea', 'image', 'select' ), true ) || ! acf_get_field( $field['key'] ) || ( 'image' === $field['type'] && ! current_user_can( 'upload_files' ) ) ) {
					continue;
				}
				$key     = $field['name'];
				$id      = project_child_fcc_customizer_id( $scope, $key );
				$inline  = ! empty( $field['fcc_inline'] ) && isset( $manager->selective_refresh );
				$choices = isset( $field['choices'] ) ? $field['choices'] : array();
				$setting = new Project_Child_FCC_Customize_Setting(
					$manager,
					$id,
					array(
						'post_id'           => $post_id,
						'meta_key'          => $key,
						'acf_key'           => $field['key'],
						'field_type'        => $field['type'],
						'choices'           => $choices,
						'transport'         => $inline && empty( $field['fcc_refresh'] ) ? 'postMessage' : 'refresh',
						'sanitize_callback' => 'image' === $field['type'] ? 'absint' : ( 'textarea' === $field['type'] ? 'sanitize_textarea_field' : 'sanitize_text_field' ),
						'validate_callback' => 'project_child_fcc_customizer_validate',
					)
				);
				$manager->add_setting( $setting );
				$args = array( 'label' => $field['label'], 'description' => isset( $field['instructions'] ) ? $field['instructions'] : '', 'section' => $section_id, 'type' => $field['type'], 'choices' => $choices );
				if ( 'hero-section' === $section ) {
					if ( 'fcc_home_hero_background' === $key ) {
						$args['priority'] = 1;
					} elseif ( 'fcc_home_hero_background_position' === $key ) {
						$args['priority'] = 2;
					} elseif ( 'fcc_home_hero_product_image' === $key ) {
						$args['priority'] = 3;
					} elseif ( 'fcc_home_hero_product_alt' === $key ) {
						$args['priority'] = 4;
					}
				}
				if ( 'image' === $field['type'] ) {
					unset( $args['type'] );
					$args['mime_type'] = 'image';
					$manager->add_control( new WP_Customize_Media_Control( $manager, $id, $args ) );
				} else {
					$manager->add_control( $id, $args );
				}
				if ( $inline ) {
					$manager->selective_refresh->add_partial(
						$id,
						array(
							'settings'            => array( $id ),
							'selector'            => '[data-fcc-setting="' . $id . '"]',
							'container_inclusive' => true,
							'fallback_refresh'    => true,
							'render_callback'     => static function () use ( $key, $scope ) {
								return project_child_fcc_text( $key, $scope );
							},
						)
					);
				}
			}
		}
	}
}
add_action( 'customize_register', 'project_child_fcc_customize_register' );

/** Validate before native Customizer sanitation and persistence. */
function project_child_fcc_customizer_validate( $validity, $value, $setting ) {
	if ( ! current_user_can( 'edit_post', $setting->post_id ) ) {
		$validity->add( 'fcc_permissions', 'Bạn không có quyền sửa nội dung này.' );
	}
	if ( 'image' === $setting->field_type ) {
		if ( ! current_user_can( 'upload_files' ) ) {
			$validity->add( 'fcc_image_permissions', 'Bạn không có quyền chọn ảnh.' );
		}
		$is_id = '' === $value || ( is_int( $value ) && $value >= 0 ) || ( is_string( $value ) && ctype_digit( $value ) );
		if ( ! $is_id || ( absint( $value ) && ( ! wp_attachment_is_image( absint( $value ) ) || ! in_array( get_post_mime_type( absint( $value ) ), array( 'image/jpeg', 'image/png', 'image/webp' ), true ) ) ) ) {
			$validity->add( 'fcc_image_invalid', 'Chọn ảnh JPG, PNG hoặc WebP trong Thư viện; xóa ảnh để dùng lại nền mặc định.' );
		}
	} elseif ( 'select' === $setting->field_type ) {
		if ( ! is_string( $value ) || ! array_key_exists( $value, $setting->choices ) ) {
			$validity->add( 'fcc_choice_invalid', 'Chọn một giá trị có trong danh sách.' );
		}
	} elseif ( ! is_string( $value ) || strlen( $value ) > 65535 ) {
		$validity->add( 'fcc_text_invalid', 'Nội dung phải là văn bản, tối đa 65.535 byte.' );
	}
	return $validity;
}

/** Add wrappers only to authorized native previews, never to public HTML. */
function project_child_fcc_customizer_markup( $text, $key, $scope, $editable ) {
	global $wp_customize;
	if ( ! $editable || ! is_customize_preview() || ! current_user_can( 'edit_post', project_core_fcc_page_id( $scope ) ) ) {
		return $text;
	}
	$id = project_child_fcc_customizer_id( $scope, $key );
	if ( ! $wp_customize || ! isset( $wp_customize->selective_refresh ) || ! $wp_customize->get_setting( $id ) || ! $wp_customize->selective_refresh->get_partial( $id ) ) {
		return $text;
	}
	return '<span class="fcc-editable-text" data-fcc-setting="' . esc_attr( $id ) . '">' . $text . '</span>';
}

/** Keep preview pencil positioning local to each text fragment. */
function project_child_fcc_customizer_preview_assets() {
	$path = '/assets/css/fractional-customizer.css';
	wp_enqueue_style( 'fcc-customizer-preview', get_stylesheet_directory_uri() . $path, array( 'customize-preview' ), (string) filemtime( get_stylesheet_directory() . $path ) );
}
add_action( 'customize_preview_init', 'project_child_fcc_customizer_preview_assets' );
