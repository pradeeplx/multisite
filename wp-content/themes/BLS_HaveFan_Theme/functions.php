<?php





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
    wp_localize_script( 'um-custom-js', 'cstmf_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'cstmf_ajax_nonce' => wp_create_nonce('hfwc-fro-form-ajax') ,'site_url' => get_stylesheet_directory_uri()) );

    // wp_enqueue_script( 'um-gallery-js',  get_theme_file_uri() . '/assets/js/um-gallery.js' );
    // wp_enqueue_script( 'um-gallery-min-js', get_theme_file_uri() . '/assets/js/um-gallery.min.js' );
    // wp_enqueue_script( 'um-gallery-map-js',  get_theme_file_uri() . '/assets/js/um-gallery.min.js.map' );
    
}
add_action( "admin_enqueue_scripts", "havefan_admin_enqueue_scripts" );

function havefan_admin_enqueue_scripts() {
    // enqueue JS
     wp_enqueue_style( "my-css-handle", get_theme_file_uri() . '/assets/css/admin-um-css.css' );
    wp_enqueue_script( "havefan-admin-custom-js", get_theme_file_uri() . '/assets/js/admin-um-custom.js' );
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





function add_course_section_filter() {
    echo '<select name="request_host" style="float:none; display:none">';
    echo '<option value="Yes" selected>Host Request</option>';
    
    echo '<input id="post-query-submit" type="submit" class="button" value="Host Request" name="">';
}
add_action( 'restrict_manage_users', 'add_course_section_filter' );


function filter_users_by_course_section( $query ) {
    global $pagenow;

    if ( is_admin() && 
         'users.php' == $pagenow && 
         isset( $_GET[ 'request_host' ] ) && 
         !empty( $_GET[ 'request_host' ] ) 
       ) {
        $section = $_GET[ 'request_host' ];
        $meta_query = array(
            array(
                'key'   => 'send-request',
                'value' => $section
            ),
            array (
              'key' => 'hf_capabilities',
              'value' => 'customer',
              'compare' => 'LIKE'
           )
        );
 


       // $query->set( 'meta_key', 'send-request' );
        $query->set( 'meta_query', $meta_query );
    }
}
add_filter( 'pre_get_users', 'filter_users_by_course_section' );


?>