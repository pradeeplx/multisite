<?php
/*
 * Plugin Name: GravityForm Entries for Ultimate Member
 * Plugin URI: https://suiteplugins.com/
 * Description: Display Gravity Form entries on Ultimate Member profile tab
 * Version: 1.0.3
 * Author: SuitePlugins
 * Author URI: https://suiteplugins.com/
 * License:     GPLv2 or later.
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: um-gf-entriess
 * Domain Path: /languages
 */

define( 'UMGF_URL', plugin_dir_url( __FILE__ ) );
define( 'UMGF_PATH', plugin_dir_path( __FILE__ ) );
define( 'UMGF_PLUGIN', plugin_basename( __FILE__ ) );

define( 'UM_GF_PLUGIN_PATH', __FILE__ );
define( 'UM_GF_STORE_URL', 'http://suiteplugins.com' ); 
define( 'UM_GF_ITEM_ID', 1029 );
define( 'UM_GF_PLUGIN_LICENSE_PAGE', 'umgf-license' );
define( 'UM_GF_PLUGIN_VERSION', '1.0.3' );
define( 'UM_GF_ITEM_NAME', 'Gravity Forms Entries for Ultimate Member' );

/**
 * Load Plugin Language
 */
function umgf_load_plugin_textdomain() {
	load_plugin_textdomain( 'um-gf-entries', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'umgf_load_plugin_textdomain' );

require_once( dirname( __FILE__ ) . '/umgf-license.php' );
if ( ! class_exists( 'UMGF_Entries' ) ) :

	/**
	 * Class UMGF_Entries
	 */
	class UMGF_Entries {
		/**
		 * The single instance of the class
		 *
		 * @var UMGF_Entries
		 */
		protected static $_instance = null;
		/**
		 * Main UMGF_Entries Instance
		 *
		 * Ensures only one instance of UltimateMember_Stories is loaded or can be loaded.
		 *
		 * @static
		 * @return UMGF_Entries - Main instance
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}
		/**
		* Initiate construct
		*/
		public function __construct() {

			// Setup some base path and URL information.
			$this->file       = __FILE__;
			$this->basename   = apply_filters( 'umgf_entries_plugin_basenname',plugin_basename( $this->file ) );
			$this->plugin_dir = apply_filters( 'umgf_entries_plugin_dir_path',plugin_dir_path( $this->file ) );
			$this->plugin_url = apply_filters( 'umgf_entries_plugin_dir_url',plugin_dir_url ( $this->file ) );

			$this->post_type = 'umgf_entries';
			$this->slug = 'umgf';
			$this->includes();
		}

		/**
		* Include additional files.
		*/
		public function includes() {
			require_once( $this->plugin_dir . 'includes/umgf-functions.php' );
			require_once( $this->plugin_dir . 'includes/umgf-setup.php' );
		}
	}
	if ( ! function_exists( 'umgf_entries' ) ) {
		/**
		 * Run the plugin
		 */
		function umgf_entries() {
			return UMGF_Entries::instance();
		}
}
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if ( class_exists( 'GFForms' ) || is_plugin_active( 'ultimate-member/index.php' ) ) {
		umgf_entries();
	}
endif;

add_action( 'admin_notices', 'umgf_admin_notice' );
/**
 * Add admin notice
 */
function umgf_admin_notice() {

	if ( get_option( 'umgf_dismiss_notice' ) ) {
		return;
	}
	$plugin_messages = array();

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	// Download the GravityForms plugin.
	if ( ! class_exists( 'GFForms' ) ) {
		$plugin_messages[] = '<a href="http://www.gravityforms.com/">GravityForms</a>';
	}

	// Download the Ultimate Member system.
	if ( ! class_exists( 'UM' ) ) {
		$plugin_messages[] = '<a href="https://wordpress.org/plugins/ultimate-member/">Ultimate Member</a>';
	}

	if ( count( $plugin_messages ) > 0 ) {
		echo '<div id="message" class="umgf-notice notice error  is-dismissable" style="position: relative">';

			echo '<p><strong>' . __( 'UM GravityForm Entries requires you to install ','um-gf-entries' ) . implode( ', ', $plugin_messages ) . '</strong></p>';
			echo '<button type="button" class="notice-dismiss">
		<span class="screen-reader-text">Dismiss this notice.</span>
	</button>';
		echo '</div>';
	}
}

add_action( 'admin_footer', 'umgf_admin_js_section' );
/**
 * Add Notice JS script.
 */
function umgf_admin_js_section() {
	?>
	<script type="text/javascript">
	jQuery(document).ready(function($) {
			jQuery(document).on( 'click', '.umgf-notice .notice-dismiss', function() {
			jQuery.ajax({
				url: ajaxurl,
				data: {
					action: 'umgf_dismiss_notice'
				},
				success: function(response){
					$('.umgf-notice').slideUp();
				}
			})
		});
	});
	</script>
	<?php
}

/**
 * Remove Admin Notice
 */
function umgf_dismiss_notice() {
	add_option( 'umgf_dismiss_notice', 1 );
	echo 1;
	exit;
}
add_action( 'wp_ajax_umgf_dismiss_notice', 'umgf_dismiss_notice' );
