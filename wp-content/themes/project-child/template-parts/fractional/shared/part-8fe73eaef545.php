<?php
defined( 'ABSPATH' ) || exit;

$topbar_text = '<span class="top-bar-item">'
	. '<svg width="18" height="12" viewBox="0 0 24 16" fill="none" xmlns="http://www.w3.org/2000/svg" style="vertical-align: middle; border-radius: 2px; margin-right: 6px; box-shadow: 0 0 2px rgba(0,0,0,0.4); flex-shrink: 0;">'
	. '<rect width="24" height="16" fill="#FFFFFF"></rect>'
	. '<circle cx="12" cy="8" r="4.8" fill="#BC002D"></circle>'
	. '</svg>'
	. '<strong>' . project_child_fcc_text( 'fcc_ui_bbd01ed1192ea8', 'shared' ) . '</strong> '
	. project_child_fcc_text( 'fcc_ui_e122cc77b5bbe5', 'shared' ) . ' '
	. '<strong>' . project_child_fcc_text( 'fcc_ui_e28520e695bc79', 'shared' ) . '</strong> '
	. ( project_core_fcc_contact( 'hotline' ) ? project_child_fcc_text( 'fcc_ui_af94720c731fdb', 'shared' ) : '' )
	. '</span>';
?>
<div class="top-bar">
    <div class="container top-bar-inner">
        <div class="top-bar-marquee">
            <div class="top-bar-track">
                <?php echo $topbar_text; ?>
                <?php echo str_replace( 'class="top-bar-item"', 'class="top-bar-item" aria-hidden="true"', $topbar_text ); ?>
                <?php echo str_replace( 'class="top-bar-item"', 'class="top-bar-item" aria-hidden="true"', $topbar_text ); ?>
                <?php echo str_replace( 'class="top-bar-item"', 'class="top-bar-item" aria-hidden="true"', $topbar_text ); ?>
            </div>
        </div>
    </div>
</div>
