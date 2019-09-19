<?php
/**
 * UM Custom Tab Builder Settings.
 *
 * @since   1.0.0
 * @package UM_Custom_Tab_Builder
 */
 if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
 	require_once( 'EDD_SL_Plugin_Updater.php' );
 }
/**
 * UM Custom Tab Builder Settings.
 *
 * @since 1.0.0
 */
class UMCTB_Settings {
	/**
	 * Parent plugin class.
	 *
	 * @since 1.0.0
	 *
	 * @var   UM_Custom_Tab_Builder
	 */
	protected $plugin = null;

	/**
	 * Option key, and option page slug.
	 *
	 * @var    string
	 * @since  0.0.1
	 */
	public $key = 'um_ctb_settings';

	/**
	 * Options page metabox ID.
	 *
	 * @var    string
	 * @since  0.0.1
	 */
	protected $metabox_id = 'um_ctb_settings_metabox';

	/**
	 * Options Page title.
	 *
	 * @var    string
	 * @since  0.0.1
	 */
	protected $title = '';

	/**
	 * Options Page hook.
	 *
	 * @var string
	 */
	protected $options_page = '';

    /**
	 * License key
	 * @var string
	 */
	public $um_license_key = 'um_ctb_license_key';

	/**
	 * License Status
	 * @var string
	 */
	public $um_license_status = 'um_ctb_license_status';

	/**
	 * Constructor.
	 *
	 * @since  1.0.0
	 *
	 * @param  UM_Custom_Tab_Builder $plugin Main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();

		// Set our title.
		$this->title = esc_attr__( 'Settings', 'um-custom-tab-builder' );
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since  1.0.0
	 */
	public function hooks() {
		// Hook in our actions to the admin.
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );

        //License
		add_action( 'admin_init',  array( $this, 'plugin_updater' ), 0 );
		add_action( 'admin_init',  array( $this, 'register_license_option' ) );
		add_action( 'admin_init',  array( $this, 'activate_license' ) );
		add_action( 'admin_init',  array( $this, 'deactivate_license' ) );
	}

	/**
	 * Add menu options page.
	 *
	 * @since  0.0.1
	 */
	public function add_options_page() {
		$this->options_page = add_submenu_page(
			'edit.php?post_type=um_ctb',
			$this->title,
			$this->title,
			'manage_options',
			$this->key,
			array( $this, 'admin_page_display' )
		);

		// Include CMB CSS in the head to avoid FOUC.
		add_action( "admin_print_styles-{$this->options_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
	}

	/**
	 * Admin page markup. Mostly handled by CMB2.
	 *
	 * @since  0.0.1
	 */
	public function admin_page_display() {
        $active_tab = 'license';
		if ( isset( $_GET['tab'] ) ) {
			$active_tab = $_GET['tab'];
		}
		?>
		<div class="wrap cmb2-options-page <?php echo esc_attr( $this->key ); ?>">
            <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<h2 class="nav-tab-wrapper">
				<a href="<?php echo admin_url( 'admin.php?page=' . $this->key . '&tab=license' ); ?>" class="nav-tab <?php echo 'license' == $active_tab ? 'nav-tab-active' : ''; ?>"><?php _e( 'License', 'um-custom-tab-builder' ); ?></a>
			</h2>
            <?php
			if ( 'license' == $active_tab ) {
				echo '<form method="post" action="options.php">';
				$this->license_fields();
				submit_button( __( 'Update License', 'um-custom-tab-builder' ), 'primary','submit', true );
				echo '</form>';
			} else {
				cmb2_metabox_form( $this->metabox_id, $this->key );
			} // end if/else
			?>
		</div>
		<?php
	}
	/**
	 * License Fields setup.
	 *
	 * @since 1.0.0
	 */
	public function license_fields() {
		$license 	= get_option( $this->um_license_key );
		$status 	= get_option( $this->um_license_status );
		settings_fields( $this->um_license_key . '_field' );
		?>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row" valign="top">
						<?php _e( 'License Key', 'um-custom-tab-builder' ); ?>
					</th>
					<td>
						<input id="um_license_key" name="<?php echo esc_attr( $this->um_license_key ); ?>"  type="text"  class="regular-text" value="<?php echo esc_attr( $license ); ?>" />
						<label class="description" for="um_license_key"><?php _e( 'Enter your license key', 'um-custom-tab-builder'  ); ?></label>
					</td>
				</tr>
				<?php if ( false !== $license ) { ?>
					<tr valign="top">
						<th scope="row" valign="top">
							<?php _e( 'Activate License', 'um-custom-tab-builder' ); ?>
						</th>
						<td>
							<?php if ( false !== $status   && 'valid' == $status ) { ?>
								<span style="color:green;line-height: 25px;"><?php _e( 'active', 'um-custom-tab-builder'  ); ?></span>
								<?php wp_nonce_field( 'um_ctb_license_nonce', 'um_ctb_license_nonce' ); ?>
								<input type="submit" class="button-secondary" name="um_ctb_license_deactivate" value="<?php esc_attr_e( 'Deactivate License', 'um-custom-tab-builder'  ); ?>"/>
							<?php } else {
								wp_nonce_field( 'um_ctb_license_nonce', 'um_ctb_license_nonce' ); ?>
								<input type="submit" class="button-secondary" name="um_ctb_license_activate" value="<?php esc_attr_e( 'Activate License', 'um-custom-tab-builder'  ); ?>"/>
							<?php } ?>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<?php
	}

	public function plugin_updater() {
		// retrieve our license key from the DB
		$license_key = trim( get_option( $this->um_license_key ) );

		// setup the updater
		$edd_updater = new EDD_SL_Plugin_Updater( UM_CTB_STORE_URL, UM_CTB_PATH, array(
				'version' 	=> UM_CTB_VERSION, // current version number
				'license' 	=> $license_key, 		// license key (used get_option above to retrieve from DB)
				'item_id'   => UM_CTB_ITEM_ID,
				'author' 	=> 'SuitePlugins', // author of this plugin
			)
		);
	}

	public function register_license_option() {
		register_setting( $this->um_license_key . '_field', $this->um_license_key, array( $this, 'sanitize_license' ) );
	}
	public function sanitize_license( $new ) {
		$old = get_option( $this->um_license_key );
		if ( $old && $old != $new ) {
			delete_option( $this->um_license_status ); // new license has been entered, so must reactivate
		}
		return $new;
	}

	public function activate_license() {
		// listen for our activate button to be clicked
		if ( isset( $_POST['um_ctb_license_activate'] ) ) {
			// run a quick security check
			if ( ! check_admin_referer( 'um_ctb_license_nonce', 'um_ctb_license_nonce' ) ) {
				return; // get out if we didn't click the Activate button
			}

			// retrieve the license from the database
			$license = trim( get_option( $this->um_license_key ) );

			// data to send in our API request
			$api_params = array(
				'edd_action'	=> 'activate_license',
				'license' 		=> $license,
				'item_name' 	=> urlencode( UM_CTB_ITEM_NAME ), // the name of our product in EDD
				'item_id'       => UM_CTB_ITEM_ID,
				'url'	   	    => home_url(),
			);

			// Call the custom API.
			$response = wp_remote_post( UM_CTB_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

			// make sure the response came back okay
			if ( is_wp_error( $response ) ) {
				return false;
			}

			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			// $license_data->license will be either "valid" or "invalid"
			update_option( $this->um_license_status, $license_data->license );

		}
	}


	public function deactivate_license() {

		// listen for our activate button to be clicked
		if ( isset( $_POST['um_ctb_license_deactivate'] ) ) {

			// run a quick security check
			if ( ! check_admin_referer( 'um_ctb_license_nonce', 'um_ctb_license_nonce' ) ) {
				return; // get out if we didn't click the Activate button
			}

			// retrieve the license from the database
			$license = trim( get_option( $this->um_license_key ) );

			// data to send in our API request
			$api_params = array(
				'edd_action'	=> 'deactivate_license',
				'license' 		=> $license,
				'item_name' 	=> urlencode( UM_CTB_ITEM_NAME ), // the name of our product in EDD
				'item_id'       => UM_CTB_ITEM_ID,
				'url'           => home_url(),
			);

			// Call the custom API.
			$response = wp_remote_post( UM_CTB_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

			// make sure the response came back okay
			if ( is_wp_error( $response ) ) {
				return false;
			}

			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			// $license_data->license will be either "deactivated" or "failed"
			if ( 'deactivated' == $license_data->license ) {
				delete_option( $this->um_license_status );
			}
		}
	}
}
