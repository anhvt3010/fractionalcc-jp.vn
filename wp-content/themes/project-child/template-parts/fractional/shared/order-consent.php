<?php
/** Required consent shared by the order popup variants. @package Project_Child */
defined( 'ABSPATH' ) || exit;
$fcc_consent_label = isset( $args['label'] ) ? $args['label'] : 'fcc_ui_2eb27fa50153ca';
?>
<div class="form-checkbox-row">
	<input type="checkbox" name="agree_terms" value="on" id="modal-agree" required>
	<label for="modal-agree"><?php echo project_child_fcc_text( $fcc_consent_label, 'shared', false ); ?></label>
</div>
