<?php 
//user_left_sidebar();
require_once("ajax/custom-ajax.php");
function user_left_sidebar($args){
	 require_once("component/user-sidebar.php");
}
require_once("classes/havefun_rest_api.php");
//if($_GET['um_action']!='edit'){
	add_action( 'um_profile_header_cover_area', 'user_left_sidebar', 10, 1 );
//}


require_once("component/list-view.php");
//require_once("component/become_a_host.php");
require_once("classes/havefun_extra_services.php");
require_once("classes/havefun_extra_option_in_services.php");
require_once("classes/havefun_role_parmission.php");
require_once("classes/role_parmission_submit.php");


 function sendMail($POST="NOT FOUND"){
 //	echo "Please add code for create product";
 /// print_r($_POST);
  ///echo  get_post_meta( $POST, '_sale_price', true);
 /// die();
 }
 //add_action('acf/pre_save_post', 'sendMail', 20); 


// code for option page in admin section

 if( function_exists('acf_add_options_page') ) {
	
	acf_add_options_page(array(
		'page_title' 	=> 'Theme General Settings',
		'menu_title'	=> 'Theme Settings',
		'menu_slug' 	=> 'theme-general-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));
	
	acf_add_options_sub_page(array(
		'page_title' 	=> 'Theme Header Settings',
		'menu_title'	=> 'Header',
		'parent_slug'	=> 'theme-general-settings',
	));
	
	acf_add_options_sub_page(array(
		'page_title' 	=> 'Theme Footer Settings',
		'menu_title'	=> 'Footer',
		'parent_slug'	=> 'theme-general-settings',
	));
	
}

?>