<?php
/*
 * Plugin Name: BLS Admin Theme
 * Description: Change admin theme layout.
 * Version: 1.0.0
 * Author: Dheeraj Tiwari
 */

// Do not access file directly!
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

define( 'BLSAT_VERSION', '1.0.0' );
define( 'BLSAT_BASE_DIR', plugin_dir_path( __FILE__ ) );
define( 'BLSAT_BASE_URL', plugin_dir_url( __FILE__ ) );


/**
 * Remove update and maintenance notice message admin panel
 * @version 1.0.0
 * @return void
 *
 */
function hide_update_nag() {
    remove_action( 'admin_notices', 'update_nag', 3 );
    remove_action( 'admin_notices', 'maintenance_nag', 10 );
}
add_action( 'admin_head', 'hide_update_nag', 1 );

/**
 * Adding custom css and js files in admin panel
 * @version 1.0.0
 * @return void
 *
 */
function blsat_enqueue_admin_script( $hook ) {
    
    wp_enqueue_script( 'blsat_custom_admin_js', plugin_dir_url( __FILE__ ) . 'assets/js/blsat_custom_admin.js', false );
    wp_enqueue_script( 'jquery_mousewheel_JS', plugin_dir_url( __FILE__ ) . 'assets/js/jquery.mousewheel.min.js', false );
     wp_enqueue_script( 'jquery_sticky_kit_JS', plugin_dir_url( __FILE__ ) . 'assets/js/jquery.sticky-kit.js', false );
    wp_localize_script( 'blsat_custom_admin_js', 'cstm_ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'cstm_site_url' => site_url() ) );
   	wp_register_style( 'blsat_custom_admin_css',  plugin_dir_url( __FILE__ ) . 'assets/css/blsat_custom_admin.css', false );
    wp_enqueue_style( 'blsat_custom_admin_css' );
}
add_action( 'admin_enqueue_scripts', 'blsat_enqueue_admin_script' );

/**
 * Ajax return admin profiel data in admin panel
 * @version 1.0.0
 * @return void
 *
 */
function blsat_wp_stats_ajax_online_total(){
    $current_user = wp_get_current_user();
    $args = array();
    $display_name = $current_user->data->display_name;
    $user_id = $current_user->data->ID;
    $avatar_url = get_avatar_url ($user_id,$args);
    $roles = "";

    if ( !empty( $current_user->roles ) && is_array( $current_user->roles ) ) {
        foreach ( $current_user->roles as $role )
            $roles .= $role.", ";
    }
    $roles = substr($roles,0,-2);
    $str = "<div class='menu-userinfo'>";
    $str .= "<div class='dispavatar'><a href='".get_edit_user_link($user_id)."'><img src='".$avatar_url."'></a></div>";
    $str .= "<div class='dispname'><a href='javascript:;'>Hi ".$display_name."</a><a href='javascript:;' class='mtrl-menu-profile-links'><i class='open-links'></i><ul class='all-links'></ul></a></span></div>";
    $str .= "<div class='disproles'>".$role."</div>";
    $str .= "</div>";
    echo $str;
    die();
}
add_action('wp_ajax_blsat_wp_stats_ajax_online_total', 'blsat_wp_stats_ajax_online_total');
add_action('wp_ajax_nopriv_blsat_wp_stats_ajax_online_total', 'blsat_wp_stats_ajax_online_total');
?>