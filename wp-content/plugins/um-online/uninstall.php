<?php
/**
 * Uninstall Ultimate Member - Online
 *
 */

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if ( ! defined( 'um_online_path' ) ) {
	define( 'um_online_path', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'um_online_url' ) ) {
	define( 'um_online_url', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'um_online_plugin' ) ) {
	define( 'um_online_plugin', plugin_basename( __FILE__ ) );
}

$options = get_option( 'um_options', array() );
if ( ! empty( $options['uninstall_on_delete'] ) ) {
	delete_option( 'um_online_last_version_upgrade' );
	delete_option( 'um_online_version' );
	delete_option( 'um_online_users_last_updated' );
	delete_option( 'widget_um_online_users' );
	delete_option( 'um_online_users' );
}