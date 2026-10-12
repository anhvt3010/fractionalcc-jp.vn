<?php
/**
 * Compile the supplied design into escaped PHP and versioned ACF/default data.
 * Development tool only; never executes through WordPress or a web request.
 * Run: C:\xampp\php\php.exe scripts/compile-fractional-template.php
 */
if ( 'cli' !== PHP_SAPI ) { http_response_code( 403 ); exit; }

$root = dirname( __DIR__ );
$source = $root . '/template/fractional-cc';
$theme = $root . '/wp-content/themes/project-child';
$plugin = $root . '/wp-content/plugins/project-core';
$existing_defaults = json_decode( file_get_contents( $plugin . '/defaults/fractional.json' ), true );
$pages = array(
	'home' => array( 'index.html', 'Trang chủ', 'trang-chu' ),
	'catalog' => array( 'san-pham.html', 'Sản phẩm', 'san-pham' ),
	'product' => array( 'chi-tiet-san-pham.html', 'Giao diện chi tiết sản phẩm', 'chi-tiet-san-pham' ),
	'policies' => array( 'chinh-sach.html', 'Chính sách', 'chinh-sach' ),
	'thankyou' => array( 'cam-on.html', 'Cảm ơn', 'cam-on' ),
);
$defaults = array( 'version' => '0.3.3', 'pages' => array( 'shared' => array( 'title' => 'Thông tin và giao diện chung Fractional CC', 'slug' => 'fractional-cc-settings', 'fields' => array() ) ) );
$schemas = array();
$tokens = array();
$bindings_path = $root . '/scripts/fractional-bindings.json';
$bindings = is_file( $bindings_path ) ? json_decode( file_get_contents( $bindings_path ), true ) : array();
if ( ! is_array( $bindings ) ) { throw new RuntimeException( 'Invalid content binding map.' ); }

function fcc_compile_write( $path, $content ) {
	if ( ! is_dir( dirname( $path ) ) ) { mkdir( dirname( $path ), 0775, true ); }
	if ( false === file_put_contents( $path, $content ) ) { throw new RuntimeException( 'Cannot write ' . $path ); }
}
function fcc_compile_php( $code ) {
	global $tokens;
	$key = 'FCCPHPTOKEN' . count( $tokens ) . 'END';
	$tokens[ $key ] = '<?php ' . $code . ' ?>';
	return $key;
}
function fcc_compile_html( $doc, $node ) {
	global $tokens;
	return strtr( $doc->saveHTML( $node ), $tokens );
}
function fcc_compile_context( $node, $kind ) {
	$parent = $node->parentNode;
	$section = 'Chữ và nội dung';
	$scope = 'product' === $kind ? 'shared' : $kind;
	while ( $parent instanceof DOMElement ) {
		$class = $parent->getAttribute( 'class' );
		if ( in_array( $parent->tagName, array( 'header', 'footer' ), true ) || preg_match( '/top-bar|modal-overlay|floating-actions|mobile-sticky/', $class ) ) { $scope = 'shared'; $section = 'Giao diện chung'; break; }
		if ( 'section' === $parent->tagName || $parent->hasAttribute( 'id' ) ) {
			$heading = $parent->getElementsByTagName( 'h2' )->item( 0 );
			$section = $heading ? trim( $heading->textContent ) : ( $parent->getAttribute( 'id' ) ?: $class );
		}
		$parent = $parent->parentNode;
	}
	return array( $scope, $section );
}
function fcc_compile_binding( $node, $kind, $attribute ) {
	$path = $node->getNodePath();
	$parent = $node instanceof DOMElement ? $node : $node->parentNode;
	while ( $parent instanceof DOMElement ) {
		if ( $parent->hasAttribute( 'id' ) ) { return $kind . '|#' . $parent->getAttribute( 'id' ) . substr( $path, strlen( $parent->getNodePath() ) ) . '|' . $attribute; }
		$parent = $parent->parentNode;
	}
	return $kind . '|' . $path . '|' . $attribute;
}
function fcc_compile_field( $scope, $section, $value, $type = 'text', $label = '', $binding = '' ) {
	global $defaults, $schemas, $bindings;
	$key = $binding && isset( $bindings[ $binding ] ) ? $bindings[ $binding ] : 'fcc_ui_' . substr( sha1( $scope . '|' . $section . '|' . $type . '|' . $value ), 0, 14 );
	if ( $binding ) { $bindings[ $binding ] = $key; }
	if ( ! isset( $defaults['pages'][ $scope ]['fields'][ $key ] ) ) {
		$defaults['pages'][ $scope ]['fields'][ $key ] = array( 'value' => $value, 'type' => $type );
		$schemas[ $scope ][ $section ][ $key ] = array( 'key' => 'field_' . $key, 'name' => $key, 'label' => $label ?: mb_substr( preg_replace( '/\s+/u', ' ', $value ), 0, 85 ), 'type' => $type, 'instructions' => 'Chỉ chỉnh nội dung; bố cục và kiểu trình bày được giữ theo thiết kế.' );
		if ( 'image' === $type ) { $schemas[ $scope ][ $section ][ $key ] += array( 'return_format' => 'id', 'preview_size' => 'medium', 'mime_types' => 'jpg,jpeg,png,webp' ); }
		if ( 'textarea' === $type ) { $schemas[ $scope ][ $section ][ $key ] += array( 'rows' => 3, 'new_lines' => '' ); }
	}
	return $key;
}
function fcc_compile_node_content( $doc, $node, $code ) {
	while ( $node->firstChild ) { $node->removeChild( $node->firstChild ); }
	$node->appendChild( $doc->createTextNode( fcc_compile_php( $code ) ) );
}

$product_bindings = array(
	'pd-title' => 'name', 'pd-breadcrumb-name' => 'shortName', 'pd-category-badge' => 'category', 'pd-breadcrumb-category' => 'category',
	'pd-origin-badge' => 'origin', 'pd-spec-origin' => 'origin', 'pd-volume' => 'volume', 'pd-spec-cbmp' => 'cbmp', 'pd-tagline' => 'tagline',
	'pd-dossier-name' => 'name', 'pd-dossier-origin' => 'origin', 'pd-dossier-cbmp' => 'cbmp', 'pd-dossier-status' => 'legalStatus', 'pd-note-text' => 'notes',
	'pd-callout-title' => 'callout_title', 'pd-callout-sub' => 'callout_subtitle',
);
$fragments = array( 'pd-gallery-thumbs' => 'gallery', 'pd-usp-list' => 'usp', 'pd-actives-grid' => 'actives', 'pd-target-list' => 'targets', 'pd-steps-list' => 'steps', 'pd-related-combos' => 'combos', 'product-catalog-grid' => 'catalog' );

foreach ( $pages as $kind => $page ) {
	if ( 'product' !== $kind ) { $defaults['pages'][ $kind ] = array( 'title' => $page[1], 'slug' => $page[2], 'template' => 'templates/fractional-' . $kind . '.php', 'fields' => array() ); }
	$doc = new DOMDocument( '1.0', 'UTF-8' );
	libxml_use_internal_errors( true );
	$doc->loadHTML( '<?xml encoding="UTF-8">' . file_get_contents( $source . '/' . $page[0] ), LIBXML_NONET );
	libxml_clear_errors();
	$xpath = new DOMXPath( $doc );
	foreach ( iterator_to_array( $xpath->query( '//comment() | //script' ) ) as $node ) { $node->parentNode->removeChild( $node ); }
	$css = '';
	foreach ( $xpath->query( '//head/style' ) as $style ) { $css .= $style->textContent . "\n"; }
	if ( $css ) { fcc_compile_write( $theme . '/assets/fractional/' . $kind . '.css', $css ); }
	$title = trim( $xpath->query( '//title' )->item( 0 )->textContent );
	$description = $xpath->query( '//meta[@name="description"]' )->item( 0 );
	$scope = 'product' === $kind ? 'shared' : $kind;
	foreach ( array( 'seo_title' => $title, 'seo_description' => $description ? $description->getAttribute( 'content' ) : '' ) as $name => $value ) {
		$defaults['pages'][ $scope ]['fields']['fcc_' . $kind . '_' . $name] = array( 'value' => $value, 'type' => 'text' );
		$schemas[ $scope ]['SEO']['fcc_' . $kind . '_' . $name] = array( 'key' => 'field_fcc_' . $kind . '_' . $name, 'name' => 'fcc_' . $kind . '_' . $name, 'label' => 'seo_title' === $name ? 'Tiêu đề SEO – ' . $page[1] : 'Mô tả SEO – ' . $page[1], 'type' => 'seo_title' === $name ? 'text' : 'textarea' );
	}
	foreach ( $product_bindings as $id => $field ) {
		$node = $xpath->query( '//*[@id="' . $id . '"]' )->item( 0 );
		if ( $node && 'product' === $kind ) { fcc_compile_node_content( $doc, $node, 'echo esc_html( project_child_fcc_product_value( ' . var_export( $field, true ) . ' ) );' ); }
	}
	if ( 'product' === $kind ) {
		foreach ( array( 'pd-sale-price' => 'price', 'pd-original-price' => 'original', 'pd-save-amount' => 'saving', 'pd-buy-btn-label' => 'buy', 'pd-target-summary' => 'target_summary', 'pd-actives-summary' => 'active_summary' ) as $id => $field ) {
			$node = $xpath->query( '//*[@id="' . $id . '"]' )->item( 0 );
			if ( $node ) { fcc_compile_node_content( $doc, $node, 'echo esc_html( project_child_fcc_product_value( ' . var_export( $field, true ) . ' ) );' ); }
		}
		$main_image = $xpath->query( '//*[@id="pd-main-image"]' )->item( 0 );
		$main_image->setAttribute( 'src', fcc_compile_php( 'echo esc_url( project_child_fcc_product_value( \'image\' ) );' ) );
		$main_image->setAttribute( 'alt', fcc_compile_php( 'echo esc_attr( project_child_fcc_product_value( \'name\' ) );' ) );
	}
	foreach ( $fragments as $id => $fragment ) {
		$node = $xpath->query( '//*[@id="' . $id . '"]' )->item( 0 );
		if ( $node ) { fcc_compile_node_content( $doc, $node, 'project_child_fcc_fragment( ' . var_export( $fragment, true ) . ' );' ); }
	}
	$text_nodes = array();
	foreach ( iterator_to_array( $xpath->query( '//body//text()[normalize-space()]' ) ) as $node ) {
		$text_nodes[] = array( $node, fcc_compile_context( $node, $kind ) );
	}
	foreach ( $text_nodes as $text_node ) {
		$node = $text_node[0];
		$value = trim( $node->nodeValue );
		if ( false !== strpos( $value, 'FCCPHPTOKEN' ) || preg_match( '/^[\s|•\/★☆✓✔+\-–—0-9]+$/u', $value ) ) { continue; }
		$ancestors = $xpath->query( 'ancestor::svg | ancestor::style | ancestor::script', $node );
		if ( $ancestors->length ) { continue; }
		list( $scope, $section ) = $text_node[1];
		if ( preg_match( '/(?:1\.237\.500|1\.275\.600|593\.100|682\.500|506\.800|379\.776)/', $value ) ) {
			$reference = '';
			$parent = $node->parentNode;
			while ( $parent instanceof DOMElement && ! $reference && 'main' !== $parent->tagName ) {
				foreach ( $parent->getElementsByTagName( 'button' ) as $button ) {
					if ( $button->hasAttribute( 'data-product-select' ) ) { $reference = $button->getAttribute( 'data-product-select' ); break; }
				}
				$parent = $parent->parentNode;
			}
			$node->nodeValue = fcc_compile_php( 'echo esc_html( project_child_fcc_reference_text( ' . var_export( $value, true ) . ', ' . var_export( $reference, true ) . ' ) );' );
			continue;
		}
		$key = fcc_compile_field( $scope, $section, $value, mb_strlen( $value ) > 110 ? 'textarea' : 'text', '', fcc_compile_binding( $node, $kind, 'text' ) );
		// Native edit shortcuts contain buttons; keep them out of interactive/form elements.
		$inline = ! $xpath->query( 'ancestor::a | ancestor::button | ancestor::label | ancestor::option | ancestor::select | ancestor::textarea', $node )->length;
		$schemas[ $scope ][ $section ][ $key ]['fcc_inline'] = ! empty( $schemas[ $scope ][ $section ][ $key ]['fcc_inline'] ) || $inline;
		$schemas[ $scope ][ $section ][ $key ]['fcc_refresh'] = ! empty( $schemas[ $scope ][ $section ][ $key ]['fcc_refresh'] ) || ! $inline;
		$node->nodeValue = preg_replace( '/\S.*\S|\S/su', fcc_compile_php( 'echo project_child_fcc_text( ' . var_export( $key, true ) . ', ' . var_export( $scope, true ) . ( $inline ? '' : ', false' ) . ' );' ), $node->nodeValue, 1 );
	}
	foreach ( $xpath->query( '//body//*[@src or @href or @data-product-select or @style or @alt or @title or @placeholder]' ) as $node ) {
		$image_key = '';
		if ( 0 === strpos( $node->getAttribute( 'src' ), 'assets/images/' ) ) { $image_path = $node->getAttribute( 'src' ); $image_key = fcc_compile_field( 'shared', 'Thư viện ảnh giao diện', $image_path, 'image', basename( $image_path ), fcc_compile_binding( $node, $kind, 'src' ) ); }
		foreach ( array( 'alt', 'title', 'placeholder' ) as $attribute ) {
			$value = $node->getAttribute( $attribute );
			if ( ! $value || false !== strpos( $value, 'FCCPHPTOKEN' ) || 'svg' === $node->tagName ) { continue; }
			$key = fcc_compile_field( 'shared', 'Văn bản thay thế và nhãn', $value, 'text', $attribute . ' – ' . mb_substr( $value, 0, 65 ), fcc_compile_binding( $node, $kind, $attribute ) );
			$code = 'alt' === $attribute && $image_key ? 'echo esc_attr( project_child_fcc_image_alt( ' . var_export( $image_key, true ) . ', ' . var_export( $key, true ) . ' ) );' : 'echo esc_attr( project_child_fcc_raw_text( ' . var_export( $key, true ) . ', \'shared\' ) );';
			$node->setAttribute( $attribute, fcc_compile_php( $code ) );
		}
		if ( $node->hasAttribute( 'src' ) && 0 === strpos( $node->getAttribute( 'src' ), 'assets/images/' ) ) {
			$path = $node->getAttribute( 'src' );
			$key = $image_key;
			$node->setAttribute( 'src', fcc_compile_php( 'echo esc_url( project_child_fcc_image( ' . var_export( $key, true ) . ' ) );' ) );
		}
		if ( $node->hasAttribute( 'href' ) ) {
			$url = $node->getAttribute( 'href' );
			if ( preg_match( '#^(index|san-pham|chi-tiet-san-pham|chinh-sach|cam-on)\.html|^(tel:|mailto:|https://zalo\.me/)#', $url ) ) {
				$node->setAttribute( 'href', fcc_compile_php( 'echo esc_url( project_child_fcc_link( ' . var_export( $url, true ) . ' ) );' ) );
			}
		}
		if ( $node->hasAttribute( 'data-product-select' ) ) { $code = $node->getAttribute( 'data-product-select' ); $node->setAttribute( 'data-product-select', fcc_compile_php( 'echo esc_attr( project_child_fcc_order_code( ' . var_export( $code, true ) . ' ) );' ) ); }
		if ( $node->hasAttribute( 'style' ) ) {
			$style = $node->getAttribute( 'style' );
			$style_index = 0;
			$style = preg_replace_callback( '#assets/images/[a-zA-Z0-9/_-]+\.(?:png|jpe?g|webp)#', function ( $match ) use ( $node, $kind, &$style_index ) { $key = fcc_compile_field( 'shared', 'Thư viện ảnh giao diện', $match[0], 'image', basename( $match[0] ), fcc_compile_binding( $node, $kind, 'style-' . $style_index++ ) ); return fcc_compile_php( 'echo esc_url( project_child_fcc_image( ' . var_export( $key, true ) . ' ) );' ); }, $style );
			$node->setAttribute( 'style', $style );
		}
	}
	foreach ( $xpath->query( '//form' ) as $form ) {
		$form->setAttribute( 'method', 'post' );
		$form->setAttribute( 'action', fcc_compile_php( 'echo esc_url( admin_url( \'admin-post.php\' ) );' ) );
		$form->appendChild( $doc->createTextNode( fcc_compile_php( 'project_child_fcc_form_fields();' ) ) );
		foreach ( $form->getElementsByTagName( 'select' ) as $select ) { if ( 'selected_item' === $select->getAttribute( 'name' ) ) { fcc_compile_node_content( $doc, $select, 'project_child_fcc_order_options();' ); } }
	}
	if ( 'home' === $kind ) {
		$hero = $xpath->query( '//section[contains(concat(" ", normalize-space(@class), " "), " hero-section ")]' )->item( 0 );
		if ( $hero ) {
			$hero->removeAttribute( 'class' );
			$hero->setAttribute( 'data-fcc-hero', fcc_compile_php( 'project_child_fcc_hero_background_attributes();' ) );
		}
		$hero_img = $xpath->query( '//div[contains(concat(" ", normalize-space(@class), " "), " hero-visual ")]//img' )->item( 0 );
		if ( $hero_img ) {
			$hero_img->setAttribute( 'src', fcc_compile_php( 'echo esc_url( project_child_fcc_hero_product_image_url() );' ) );
			$hero_img->setAttribute( 'alt', fcc_compile_php( 'echo esc_attr( project_child_fcc_hero_product_image_alt() );' ) );
		}
		$hero_grid = $xpath->query( '//div[contains(concat(" ", normalize-space(@class), " "), " hero-grid ")]' )->item( 0 );
		$cta = $xpath->query( '//div[contains(concat(" ", normalize-space(@class), " "), " hero-cta-group ")]' )->item( 0 );
		$trust = $xpath->query( '//div[contains(concat(" ", normalize-space(@class), " "), " hero-trust-micro ")]' )->item( 0 );
		if ( $hero_grid && $cta && $trust ) {
			$hero_grid->setAttribute( 'class', $hero_grid->getAttribute( 'class' ) . ' fcc-hero-product-first' );
			$conversion = $doc->createElement( 'div' );
			$conversion->setAttribute( 'class', 'hero-conversion' );
			$conversion->appendChild( $cta );
			$conversion->appendChild( $trust );
			$hero_grid->appendChild( $conversion );
		}
		foreach ( iterator_to_array( $xpath->query( '//div[contains(concat(" ", normalize-space(@class), " "), " brand-stats-row ")]' ) ) as $stats ) {
			$stats->parentNode->removeChild( $stats );
		}
	}
	foreach ( $xpath->query( '//div[contains(concat(" ", normalize-space(@class), " "), " header-mobile-bar ")]' ) as $mobile_bar ) {
		$toggle = $xpath->query( './/button[contains(concat(" ", normalize-space(@class), " "), " mobile-nav-toggle ")]', $mobile_bar )->item( 0 );
		if ( $toggle ) { $mobile_bar->appendChild( $toggle ); }
	}
	$body = $doc->getElementsByTagName( 'body' )->item( 0 );
	$output = "<?php\ndefined( 'ABSPATH' ) || exit;\n?>\n";
	foreach ( iterator_to_array( $body->childNodes ) as $node ) {
		if ( ! ( $node instanceof DOMElement ) ) { continue; }
		if ( 'main' === $node->tagName ) {
			$output .= '<main' . ( $node->hasAttribute( 'class' ) ? ' class="' . htmlspecialchars( $node->getAttribute( 'class' ), ENT_QUOTES, 'UTF-8' ) . '"' : '' ) . ( $node->hasAttribute( 'id' ) ? ' id="' . htmlspecialchars( $node->getAttribute( 'id' ), ENT_QUOTES, 'UTF-8' ) . '"' : '' ) . ">\n";
			$index = 0;
			foreach ( iterator_to_array( $node->childNodes ) as $section ) {
				if ( ! ( $section instanceof DOMElement ) ) { continue; }
				$name = $kind . '/section-' . str_pad( (string) ++$index, 2, '0', STR_PAD_LEFT );
				$markup = fcc_compile_html( $doc, $section );
				$markup = str_replace( 'data-fcc-hero="<?php project_child_fcc_hero_background_attributes(); ?>"', '<?php project_child_fcc_hero_background_attributes(); ?>', $markup );
				// This section is maintained as a CMS-driven component, not compiled static cards.
				if ( 'home' !== $kind || 'san-pham-chu-luc' !== $section->getAttribute( 'id' ) ) {
					fcc_compile_write( $theme . '/template-parts/fractional/' . $name . '.php', "<?php defined( 'ABSPATH' ) || exit; ?>\n" . $markup . "\n" );
				}
				$output .= "<?php get_template_part( 'template-parts/fractional/" . $name . "' ); ?>\n";
			}
			$output .= "</main>\n";
		} else {
			$html = fcc_compile_html( $doc, $node );
			// Identical chrome/forms share a file; genuine design variants stay distinct.
			$name = 'shared/part-' . substr( sha1( $html ), 0, 12 );
			fcc_compile_write( $theme . '/template-parts/fractional/' . $name . '.php', "<?php defined( 'ABSPATH' ) || exit; ?>\n" . $html . "\n" );
			$output .= "<?php get_template_part( 'template-parts/fractional/" . $name . "' ); ?>\n";
		}
	}
	fcc_compile_write( $theme . '/template-parts/fractional/' . $kind . '.php', $output );
	if ( 'product' !== $kind ) {
		$template = "<?php\n/** Template Name: Fractional CC – " . $page[1] . " */\ndefined( 'ABSPATH' ) || exit;\nproject_child_fcc_render( '" . $kind . "' );\n";
		fcc_compile_write( $theme . '/templates/fractional-' . $kind . '.php', $template );
	}
}

$hero_fields = array(
	'fcc_home_hero_background' => array( 'value' => 'assets/images/banner/hero-background.jpg', 'type' => 'image', 'label' => 'Ảnh nền toàn bộ Hero', 'return_format' => 'id', 'preview_size' => 'medium', 'mime_types' => 'jpg,jpeg,png,webp', 'instructions' => 'Chọn ảnh làm nền phủ toàn bộ Hero. Xóa ảnh để dùng lại nền mặc định.' ),
	'fcc_home_hero_background_position' => array( 'value' => 'center center', 'type' => 'select', 'label' => 'Vị trí ảnh nền Hero', 'choices' => array( 'center center' => 'Giữa', 'center top' => 'Trên', 'center bottom' => 'Dưới', 'left center' => 'Trái', 'right center' => 'Phải' ), 'instructions' => 'Ảnh phủ kín Hero (cover), có thể cắt bớt mép tùy kích thước màn hình. Chọn vị trí để ưu tiên vùng ảnh.' ),
	'fcc_home_hero_product_image' => array( 'value' => 'assets/images/banner/combo-c-cx4.png', 'type' => 'image', 'label' => 'Ảnh sản phẩm nổi bật Hero', 'return_format' => 'id', 'preview_size' => 'medium', 'mime_types' => 'jpg,jpeg,png,webp', 'instructions' => 'Chọn ảnh sản phẩm nổi bật hiển thị ở khung banner Hero bên phải. Xóa ảnh để dùng lại ảnh mặc định.' ),
	'fcc_home_hero_product_alt' => array( 'value' => 'Combo Siêu Tinh Chất Vi Kim Fractional CC Serum C và CX4 Nhật Bản', 'type' => 'text', 'label' => 'Văn bản thay thế (Alt) ảnh sản phẩm Hero', 'instructions' => 'Mô tả ngắn gọn cho ảnh sản phẩm nổi bật (tối ưu SEO & trợ năng).' ),
);
$hero_schema_fields = array();
foreach ( $hero_fields as $key => $field ) {
	$defaults['pages']['home']['fields'][ $key ] = array( 'value' => $field['value'], 'type' => $field['type'] );
	unset( $field['value'] );
	$hero_schema_fields[ $key ] = array( 'key' => 'field_' . $key, 'name' => $key ) + $field;
}
$existing_hero_section = isset( $schemas['home']['hero-section'] ) ? $schemas['home']['hero-section'] : array();
$schemas['home']['hero-section'] = array_merge( $hero_schema_fields, $existing_hero_section );

$source_data = file_get_contents( $source . '/assets/js/data.js' );
preg_match( '/const FCC_DATA = (\{.*?\});/s', $source_data, $match );
$catalog = json_decode( $match[1], true, 512, JSON_THROW_ON_ERROR );
$defaults += array_intersect_key( $catalog, array_flip( array( 'brand', 'products', 'combos' ) ) );
if ( isset( $existing_defaults['catalogSource'] ) ) {
	foreach ( array( 'products', 'combos', 'catalogSource' ) as $key ) { $defaults[ $key ] = $existing_defaults[ $key ]; }
}
if ( isset( $existing_defaults['featured'] ) ) { $defaults['featured'] = $existing_defaults['featured']; }
fcc_compile_write( $plugin . '/defaults/fractional.json', json_encode( $defaults, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . "\n" );
fcc_compile_write( $theme . '/inc/fractional-fields.json', json_encode( $schemas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . "\n" );
ksort( $bindings );
fcc_compile_write( $bindings_path, json_encode( $bindings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . "\n" );
$assets = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $source . '/assets', FilesystemIterator::SKIP_DOTS ) );
foreach ( $assets as $file ) {
	if ( ! $file->isFile() || ! preg_match( '/\.(?:png|jpe?g|webp|css)$/', $file->getFilename() ) ) { continue; }
	$relative = str_replace( '\\', '/', substr( $file->getPathname(), strlen( $source ) + 1 ) );
	fcc_compile_write( $theme . '/assets/fractional/' . $relative, file_get_contents( $file->getPathname() ) );
}
// Remove only obsolete files emitted by this compiler, inside its shared folder.
$used = array();
foreach ( array_keys( $pages ) as $kind ) {
	$markup = file_get_contents( $theme . '/template-parts/fractional/' . $kind . '.php' );
	preg_match_all( '#template-parts/fractional/shared/(part-[a-f0-9]+)#', $markup, $matches );
	$used = array_merge( $used, $matches[1] );
}
$shared_root = realpath( $theme . '/template-parts/fractional/shared' );
foreach ( glob( $shared_root . '/part-*.php' ) as $path ) {
	$resolved = realpath( $path );
	if ( ! $resolved || dirname( $resolved ) !== $shared_root || ! preg_match( '/^part-[a-f0-9]{12}\.php$/', basename( $resolved ) ) ) { throw new RuntimeException( 'Unsafe generated-file path.' ); }
	if ( ! in_array( basename( $resolved, '.php' ), $used, true ) && 0 === strpos( file_get_contents( $resolved ), "<?php defined( 'ABSPATH' ) || exit; ?>" ) ) { unlink( $resolved ); }
}
echo 'Compiled 5 designs; ' . array_sum( array_map( function ( $page ) { return count( $page['fields'] ); }, $defaults['pages'] ) ) . " editable content fields, 10 products and default data.\n";
