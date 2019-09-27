<?php
$page = ( ! empty( $_REQUEST['page'] )  ? esc_attr( $_REQUEST['page'] ) : '' );
$wp_list_table = new UM_Gallery_Lite_List_Table();
?>
<div class="wrap">
	<form method="get">
		<input type="hidden" name="page" value="<?php echo $page; ?>" />
		<?php
		$wp_list_table->search_box( 'Search', 'um-gallery-pro' );
		$wp_list_table->prepare_items();
		$wp_list_table->display(); ?>
	</form>
</div>