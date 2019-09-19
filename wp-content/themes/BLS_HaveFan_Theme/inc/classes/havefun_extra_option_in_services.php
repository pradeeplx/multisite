<?php 
/* add new tab called "mytab" */

add_filter('um_account_page_default_tabs_hook', 'my_custom_tab_in_um', 10 );
function my_custom_tab_in_um( $tabs ) {
	$tabs[800]['information']['icon'] = 'um-faicon-pencil';
	$tabs[800]['information']['title'] = 'My Personal Information';
	$tabs[800]['information']['custom'] = true;
	return $tabs;
}
	
/* make our new tab hookable */

add_action('um_account_tab__information', 'um_account_tab__information');
function um_account_tab__information( $info ) {
	global $ultimatemember;
	extract( $info );

	$output = $ultimatemember->account->get_tab_output('information');
	if ( $output ) { echo $output; }
}

/* Finally we add some content in the tab */

add_filter('um_account_content_hook_information', 'um_account_content_hook_information');
function um_account_content_hook_information( $output ){
	ob_start();
	?>
		
	<div class="um-field ">
		
		<!-- Here goes your custom content  acfFormParsnal -->
		<div id="parsnalSection"></div>
		<?php   //echo  do_shortcode('[my_acf_user_form field_group="284" ]'); ?>
	</div>		
		
	<?php
		
	$output .= ob_get_contents();
	ob_end_clean();
	return $output;
}

function show_all_havafun_event(){
	ob_start();
	var_dump();
	global $member;
	if( um_profile_id() == get_current_user_id()){
		$profile_id = um_profile_id();
		require_once( get_stylesheet_directory().'/inc/component/experience-template/upcoming-event.php');
	}else if(um_profile_id() > 0){
		$profile_id = um_profile_id();
		require_once( get_stylesheet_directory().'/inc/component/experience-template/guest-upcoming-event.php');
	}else{
		echo "NA";
	}
	$output .= ob_get_contents();
	ob_end_clean();
	return $output;

}
add_shortcode('show-all-havafun-event', 'show_all_havafun_event');

?>