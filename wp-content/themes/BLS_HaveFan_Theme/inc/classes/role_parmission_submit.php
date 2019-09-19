
<?php
add_filter('um_account_page_default_tabs_hook', 'my_custom_tab_in_um_for_request_parmission', 10 );
function my_custom_tab_in_um_for_request_parmission( $tabs ) {
	$tabs[200]['request_host']['icon'] = 'um-faicon-user';
	$tabs[200]['request_host']['title'] = 'Want to Host';
	$tabs[200]['request_host']['custom'] = true;
	return $tabs;
}
	
/* make our new tab hookable */

add_action('um_account_tab__request_host', 'um_account_tab__information_host');
function um_account_tab__information_host( $info ) {
	global $ultimatemember;
	extract( $info );
    $output = $ultimatemember->account->get_tab_output('request_host');
	if ( $output ) { echo $output; }
}

/* Finally we add some content in the tab */

add_filter('um_account_content_hook_request_host', 'um_account_content_hook_information_host');
function um_account_content_hook_information_host( $output ){
	ob_start();
	?>
		<style>
			#um_account_submit_request_host{
				display: none;
			}
		</style>
	<div class="um-field ">
	<?php 
	 $user_meta=get_userdata(get_current_user_id());

     $user_roles=$user_meta->roles;
	if (in_array("wcfm_vendor", $user_roles))
		  {
		    ?>
		    <p>You are already HOST</p>
		    <?php
		  }else{
           ?>
           <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,</p>
		   <br/>
		   <a class="um-button" href="<?php echo get_site_url(); ?>/account/?parmission_send=true">Send Request for Host </a>
           <?php
		  } ?>
		
		
		
		
	</div>		
		
	<?php
		
	$output .= ob_get_contents();
	ob_end_clean();
	return $output;
}
?>