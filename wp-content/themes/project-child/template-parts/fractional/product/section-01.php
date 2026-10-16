<?php defined( 'ABSPATH' ) || exit; ?>
<div class="container">
      <nav class="pd-breadcrumb-bar" aria-label="Breadcrumb">
        <a href="<?php echo esc_url( project_child_fcc_link( 'index.html' ) ); ?>">
          <svg width="14" height="14" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            <polyline points="9 22 9 12 15 12 15 22"></polyline>
          </svg>
          <span><?php echo project_child_fcc_text( 'fcc_ui_db7aae96337c15', 'shared', false ); ?></span>
        </a>
        <span class="pd-breadcrumb-sep">/</span>
        <a href="<?php echo esc_url( project_child_fcc_link( 'san-pham.html' ) ); ?>"><?php echo project_child_fcc_text( 'fcc_ui_e4a25aa0335570', 'shared', false ); ?></a>
        <span class="pd-breadcrumb-sep">/</span>
        <strong class="pd-breadcrumb-current" id="pd-breadcrumb-name"><?php echo esc_html( project_child_fcc_product_value( 'shortName' ) ?: project_child_fcc_product_value( 'name' ) ); ?></strong>
      </nav>
    </div>
