<?php defined( 'ABSPATH' ) || exit; ?>
<header class="site-header">
    <div class="container header-inner-split">
      
      <nav class="header-nav-side header-nav-left" aria-label="Menu chính bên trái">
        <ul class="nav-split-list">
          <li><a href="<?php echo esc_url( project_child_fcc_link( 'index.html#san-pham-chu-luc' ) ); ?>" class="nav-link"><?php echo project_child_fcc_text( 'fcc_ui_030d29589ecca2', 'shared', false ); ?></a></li>
          <li><a href="<?php echo esc_url( project_child_fcc_link( 'index.html#diem-noi-bat' ) ); ?>" class="nav-link"><?php echo project_child_fcc_text( 'fcc_ui_b4c0ae913bf5a6', 'shared', false ); ?></a></li>
          <li><a href="<?php echo esc_url( project_child_fcc_link( 'index.html#gioi-thieu' ) ); ?>" class="nav-link"><?php echo project_child_fcc_text( 'fcc_ui_ffa1098f587730', 'shared', false ); ?></a></li>
        </ul>
      </nav>

      
      <a href="<?php echo esc_url( project_child_fcc_link( 'index.html' ) ); ?>" class="brand-logo-center" title="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_ab373a90954b36', 'shared' ) ); ?>">
        <img src="<?php echo esc_url( project_child_fcc_image( 'fcc_ui_1a0029c547f307' ) ); ?>" alt="<?php echo esc_attr( project_child_fcc_image_alt( 'fcc_ui_1a0029c547f307', 'fcc_ui_9426ed1ade78ab' ) ); ?>" class="brand-logo-main-img">
        <span class="logo-divider"></span>
        <div class="distributor-badge">
          <img src="<?php echo esc_url( project_child_fcc_image( 'fcc_ui_be08f7c57f78ec' ) ); ?>" alt="<?php echo esc_attr( project_child_fcc_image_alt( 'fcc_ui_be08f7c57f78ec', 'fcc_ui_61ed77463dff18' ) ); ?>" class="distributor-logo-img" title="<?php echo esc_attr( project_child_fcc_raw_text( 'fcc_ui_a941b421c5e005', 'shared' ) ); ?>">
          <div class="distributor-text">
            <span class="distributor-title"><?php echo project_child_fcc_text( 'fcc_ui_f2b1ceab0294d6', 'shared', false ); ?></span>
            <span class="distributor-sub"><?php echo project_child_fcc_text( 'fcc_ui_bb054f8c102d4d', 'shared', false ); ?></span>
          </div>
        </div>
      </a>

      
      <nav class="header-nav-side header-nav-right" aria-label="Menu chính bên phải">
        <ul class="nav-split-list">
          <li><a href="<?php echo esc_url( project_child_fcc_link( 'index.html#routine' ) ); ?>" class="nav-link"><?php echo project_child_fcc_text( 'fcc_ui_3dbd96608220ae', 'shared', false ); ?></a></li>
          <li><a href="<?php echo esc_url( project_child_fcc_link( 'index.html#cam-ket-phap-ly' ) ); ?>" class="nav-link"><?php echo project_child_fcc_text( 'fcc_ui_23be8881d2ac63', 'shared', false ); ?></a></li>
        </ul>
        <div class="header-actions">
          <button class="btn-header-order" data-open-order id="pd-header-order-btn">
            <?php echo project_child_fcc_text( 'fcc_ui_9681bed187e219', 'shared', false ); ?>
          </button>
        </div>
      </nav>

      
      <div class="header-mobile-bar">
        <button class="mobile-nav-toggle" aria-label="Menu điều hướng">
          <span></span><span></span><span></span>
        </button>
        <button class="btn-header-order" data-open-order id="pd-header-order-btn-mob">
          <?php echo project_child_fcc_text( 'fcc_ui_9681bed187e219', 'shared', false ); ?>
        </button>
      </div>
    </div>

    
    <div class="nav-menu">
      <ul>
        <li><a href="<?php echo esc_url( project_child_fcc_link( 'index.html#san-pham-chu-luc' ) ); ?>" class="nav-link"><?php echo project_child_fcc_text( 'fcc_ui_f5248afaa5707e', 'shared', false ); ?></a></li>
        <li><a href="<?php echo esc_url( project_child_fcc_link( 'index.html#diem-noi-bat' ) ); ?>" class="nav-link"><?php echo project_child_fcc_text( 'fcc_ui_75c0b9744b7141', 'shared', false ); ?></a></li>
        <li><a href="<?php echo esc_url( project_child_fcc_link( 'index.html#gioi-thieu' ) ); ?>" class="nav-link"><?php echo project_child_fcc_text( 'fcc_ui_7765c53f3b0cc4', 'shared', false ); ?></a></li>
        <li><a href="<?php echo esc_url( project_child_fcc_link( 'index.html#danh-muc' ) ); ?>" class="nav-link"><?php echo project_child_fcc_text( 'fcc_ui_3768eee65faaa8', 'shared', false ); ?></a></li>
        <li><a href="<?php echo esc_url( project_child_fcc_link( 'index.html#routine' ) ); ?>" class="nav-link"><?php echo project_child_fcc_text( 'fcc_ui_1b9b25c3ff94b1', 'shared', false ); ?></a></li>
        <li><a href="<?php echo esc_url( project_child_fcc_link( 'index.html#danh-gia' ) ); ?>" class="nav-link"><?php echo project_child_fcc_text( 'fcc_ui_8e50f2be218967', 'shared', false ); ?></a></li>
        <li><a href="<?php echo esc_url( project_child_fcc_link( 'index.html#cam-ket-phap-ly' ) ); ?>" class="nav-link"><?php echo project_child_fcc_text( 'fcc_ui_04f8a3a25234b4', 'shared', false ); ?></a></li>
        <li><a href="<?php echo esc_url( project_child_fcc_link( 'index.html#faq' ) ); ?>" class="nav-link"><?php echo project_child_fcc_text( 'fcc_ui_52b1954ad7b65f', 'shared', false ); ?></a></li>
        <li><a href="<?php echo esc_url( project_child_fcc_link( 'san-pham.html' ) ); ?>" class="nav-link"><?php echo project_child_fcc_text( 'fcc_ui_8dd9f85de733a5', 'shared', false ); ?></a></li>
        <li><a href="<?php echo esc_url( project_child_fcc_link( 'chinh-sach.html' ) ); ?>" class="nav-link"><?php echo project_child_fcc_text( 'fcc_ui_125a2c81361b62', 'shared', false ); ?></a></li>
        <li style="padding-top: 10px; border-bottom: none;">
          <a href="<?php echo esc_url( project_child_fcc_link( 'tel:0985561862' ) ); ?>" class="hotline-pill" style="display: inline-flex;">
            <span><?php echo project_child_fcc_text( 'fcc_ui_ff8b62e42bfee6', 'shared', false ); ?></span>
          </a>
        </li>
      </ul>
    </div>
  </header>
