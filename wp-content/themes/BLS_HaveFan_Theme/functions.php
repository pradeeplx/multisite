<?php

// $to = "kartik3rde@gmail.com";
// $subject = "My subject";
// $txt = "Hello world!";
// $headers = "From: kartik.linuxbean@gmail.com" . "\r\n" .
// "CC: shubham3rdee@gmail.com";
// if(mail($to,$subject,$txt,$headers)){
// 	echo "hello";
// }else{
// 	echo "bye";
// }

// die();




add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
function my_theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}
require_once("inc/child-init.php");


add_action( 'wp_footer', 'my_footer_scripts' );
function my_footer_scripts(){
   wp_enqueue_script( 'custom-js',  get_theme_file_uri() . '/assets/js/custom.js' );
    wp_enqueue_script( 'um-jquery.sticky-kit',  get_theme_file_uri() . '/assets/js/um-jquery.sticky-kit.js' );
   wp_enqueue_script( 'um-custom-js',  get_theme_file_uri() . '/assets/js/um-custom.js' );
    wp_localize_script( 'um-custom-js', 'cstmf_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'cstmf_ajax_nonce' => wp_create_nonce('hfwc-fro-form-ajax') ) );
}

// function my_pre_save_post( $post_id){
// 	var_dump($post_id);
// 	echo "<pre>";
// 	var_dump($_POST);
// 	die("E");
// }
// add_filter('acf/pre_save_post' , 'my_pre_save_post', 10, 1 );

add_filter( 'ajax_query_attachments_args', 'wpb_show_current_user_attachments' );
 
function wpb_show_current_user_attachments( $query ) {
    $user_id = get_current_user_id();
    if ( $user_id && !current_user_can('activate_plugins') && !current_user_can('edit_others_posts
') ) {
        $query['author'] = $user_id;
    }
    return $query;
} 


?>