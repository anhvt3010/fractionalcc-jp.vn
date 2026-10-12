<?php
/**
 * A native Customizer setting backed by an existing ACF field.
 * Loaded only after WordPress loads its Customizer classes.
 *
 * @package Project_Child
 */

defined( 'ABSPATH' ) || exit;

class Project_Child_FCC_Customize_Setting extends WP_Customize_Setting {
	public $type = 'fcc_acf';
	public $post_id;
	public $meta_key;
	public $acf_key;
	public $field_type;
	public $choices;

	/** Bind only the allowlisted field supplied by the theme registration. */
	public function __construct( $manager, $id, $args = array() ) {
		$this->post_id  = absint( $args['post_id'] );
		$this->meta_key = $args['meta_key'];
		$this->acf_key  = $args['acf_key'];
		$this->field_type = isset( $args['field_type'] ) ? $args['field_type'] : 'text';
		$this->choices    = isset( $args['choices'] ) ? $args['choices'] : array();
		unset( $args['post_id'], $args['meta_key'], $args['acf_key'], $args['field_type'], $args['choices'] );
		parent::__construct( $manager, $id, $args );
	}

	/** Read the same value the ACF editor and public renderer use. */
	public function value() {
		$value = get_post_meta( $this->post_id, $this->meta_key, true );
		if ( 'image' === $this->field_type ) {
			return absint( $value );
		}
		return is_scalar( $value ) ? (string) $value : '';
	}

	/** Native Customizer handles changesets, capabilities and save nonces. */
	protected function update( $value ) {
		if ( ! current_user_can( 'edit_theme_options' ) || ! current_user_can( 'edit_post', $this->post_id ) || ! function_exists( 'update_field' ) ) {
			return false;
		}
		return update_field( $this->acf_key, $value, $this->post_id );
	}
}

/** Track changed settings with a single metadata filter instead of one per field. */
function project_child_fcc_preview_acf_setting( $setting ) {
	global $project_child_fcc_preview_settings;
	$project_child_fcc_preview_settings[ get_current_blog_id() ][ $setting->post_id ][ $setting->meta_key ] = $setting;
	add_filter( 'get_post_metadata', 'project_child_fcc_preview_metadata', 10, 4 );
}
add_action( 'customize_preview_fcc_acf', 'project_child_fcc_preview_acf_setting' );

/** Preview changes without touching saved metadata or ACF caches. */
function project_child_fcc_preview_metadata( $value, $post_id, $key, $single ) {
	global $project_child_fcc_preview_settings;
	$setting = isset( $project_child_fcc_preview_settings[ get_current_blog_id() ][ $post_id ][ $key ] ) ? $project_child_fcc_preview_settings[ get_current_blog_id() ][ $post_id ][ $key ] : null;
	if ( null !== $value || ! $setting ) {
		return $value;
	}
	$preview = $setting->post_value();
	return null === $preview ? $value : ( $single ? $preview : array( $preview ) );
}
