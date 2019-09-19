<?php
$this->gallery_admin_head();
if ( 'license' == $this->active_tab ) {
	echo '<form method="post" action="options.php">';
	$this->license_fields();
	submit_button( __( 'Update License', 'um-gallery-pro' ), 'primary','submit', true );
	echo '</form>';
} elseif ( 'addons' == $this->active_tab ) {
	$this->addons_tab();
} elseif ( 'advanced' == $this->active_tab ) {
	$this->tools_tab();
} elseif ( 'labels' == $this->active_tab ) {
	cmb2_metabox_form( $this->metabox_id . '-labels', $this->key );
} elseif ( 'layout' == $this->active_tab ) {
	cmb2_metabox_form( $this->metabox_id . '-layout', $this->key );
} else {
	cmb2_metabox_form( $this->metabox_id, $this->key );
}
?>
</div>
