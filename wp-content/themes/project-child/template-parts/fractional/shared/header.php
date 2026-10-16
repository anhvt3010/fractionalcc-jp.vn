<?php
/** One header and navigation for every Fractional CC page. @package Project_Child */
defined( 'ABSPATH' ) || exit;
$kind = project_child_fcc_kind();
$left = array(
	array( 'field' => 'fcc_nav_home', 'url' => project_core_fcc_url( 'home' ), 'current' => 'home' === $kind ? 'page' : '' ),
	array( 'field' => 'fcc_nav_products', 'url' => project_core_fcc_url( 'catalog' ), 'current' => 'catalog' === $kind ? 'page' : ( 'product' === $kind ? 'location' : '' ) ),
	array( 'field' => 'fcc_nav_brand', 'url' => project_core_fcc_url( 'home', '#gioi-thieu' ), 'current' => '' ),
);
$right = array(
	array( 'field' => 'fcc_nav_concerns', 'url' => project_core_fcc_url( 'concerns' ), 'current' => 'concerns' === $kind ? 'page' : '' ),
	array( 'field' => 'fcc_nav_legal', 'url' => project_core_fcc_url( 'home', '#cam-ket-phap-ly' ), 'current' => '' ),
);
$order_code = 'product' === $kind ? project_child_fcc_detail_order_code() : project_child_fcc_featured_combo_order_code();
get_template_part( 'template-parts/fractional/shared/part-8fe73eaef545' );
?>
<header class="site-header fcc-unified-header">
	<div class="container header-inner-split">
		<nav class="header-nav-side header-nav-left" aria-label="Menu chính bên trái">
			<ul class="nav-split-list"><?php get_template_part( 'template-parts/fractional/shared/header-links', null, array( 'items' => $left ) ); ?></ul>
		</nav>
		<a href="<?php echo esc_url( project_core_fcc_url( 'home' ) ); ?>" class="brand-logo-center" title="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_ab373a90954b36', 'shared' ) ); ?>"<?php echo project_child_fcc_image_edit_attributes( 'fcc_ui_1a0029c547f307' ); ?>>
			<?php
			echo project_child_fcc_picture_html(
				project_child_fcc_image( 'fcc_ui_1a0029c547f307' ),
				project_child_fcc_image_alt( 'fcc_ui_1a0029c547f307', 'fcc_ui_9426ed1ade78ab' ),
				'brand-logo-main-img',
				array( 'width' => 166, 'height' => 40 )
			);
			?>
			<span class="logo-divider"></span>
			<span class="distributor-badge"<?php echo project_child_fcc_image_edit_attributes( 'fcc_ui_be08f7c57f78ec' ); ?>>
				<?php
				echo project_child_fcc_picture_html(
					project_child_fcc_image( 'fcc_ui_be08f7c57f78ec' ),
					project_child_fcc_image_alt( 'fcc_ui_be08f7c57f78ec', 'fcc_ui_61ed77463dff18' ),
					'distributor-logo-img',
					array(
						'width'  => 40,
						'height' => 40,
						'title'  => project_child_fcc_raw_text( 'fcc_ui_a941b421c5e005', 'shared' ),
					)
				);
				?>
				<span class="distributor-text">
					<span class="distributor-title"><?php echo project_child_fcc_text( 'fcc_ui_f2b1ceab0294d6', 'shared', false ); ?></span>
					<span class="distributor-sub"><?php echo project_child_fcc_text( 'fcc_ui_bb054f8c102d4d', 'shared', false ); ?></span>
				</span>
			</span>
		</a>
		<nav class="header-nav-side header-nav-right" aria-label="Menu chính bên phải">
			<ul class="nav-split-list"><?php get_template_part( 'template-parts/fractional/shared/header-links', null, array( 'items' => $right ) ); ?></ul>
			<div class="header-actions"><?php get_template_part( 'template-parts/fractional/shared/header-order', null, array( 'order_code' => $order_code ) ); ?></div>
		</nav>
		<div class="header-mobile-bar">
			<?php get_template_part( 'template-parts/fractional/shared/header-order', null, array( 'order_code' => $order_code ) ); ?>
			<button type="button" class="mobile-nav-toggle" aria-label="Menu điều hướng" aria-controls="fcc-mobile-menu" aria-expanded="false"><span></span><span></span><span></span></button>
		</div>
	</div>
	<nav class="nav-menu" id="fcc-mobile-menu" aria-label="Menu chính trên điện thoại">
		<ul><?php get_template_part( 'template-parts/fractional/shared/header-links', null, array( 'items' => array_merge( $left, $right ) ) ); ?></ul>
	</nav>
</header>
