<?php
/*
 * Plugin Name: Football Table
 * Description: We can create Football all type data table .
 * Version: 1.0.0
 * Author: Dheeraj Tiwari
 */

// Do not access file directly!
if ( ! defined( 'ABSPATH' ) ) {
	die;
}


 
// function to create the DB / 					
function your_plugin_options_install() {
  require_once(plugin_dir_path( __FILE__ ).'inc/class/Wp_table_creater.php');
}
// run the install scripts upon plugin activation
register_activation_hook(__FILE__,'your_plugin_options_install');


// include all import json curl classes.
require_once(plugin_dir_path( __FILE__ ).'inc/class/Wpcp_league.php');
require_once(plugin_dir_path( __FILE__ ).'inc/class/Wpcp_team.php');


/**
 * When activate plugin so setup initial data
 * @version 1.0.0
 * @return void
 */



/**
 * Create admin Page to Footaball API Setting.
 */
 // Hook for adding admin menus
 add_action('admin_menu', 'aboi_football_table_setting');
 
 
/**
 * Adds a new top-level page to the administration menu.
 */
function aboi_football_table_setting() {
     add_menu_page( 'Football Table', 'Football Table',
        'manage_options',
        'football-table-setting',
        'aboi_football_table_setting_callback',
        ''
    );
}
 
/**
 * Disply callback for the API Setting page.
 */
function aboi_football_table_setting_callback() {
	echo "welcome in my new plugin .";
	require_once(plugin_dir_path( __FILE__ ).'inc/action/setting_page.php');
}











