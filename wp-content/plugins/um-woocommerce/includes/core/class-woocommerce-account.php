<?php
namespace um_ext\um_woocommerce\core;

if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class WooCommerce_Account
 * @package um_ext\um_woocommerce\core
 */
class WooCommerce_Account {


	/**
	 * WooCommerce_Account constructor.
	 */
	function __construct() {

		add_filter( 'um_account_page_default_tabs_hook', array( &$this, 'account_tabs' ), 100 );

		add_filter( 'um_account_content_hook_billing', array( &$this, 'account_billing_tab_content' ), 10, 2 );
		add_filter( 'um_account_content_hook_shipping', array( &$this, 'account_shipping_tab_content' ), 10, 2 );
		add_filter( 'um_account_content_hook_orders', array( &$this, 'account_orders_tab_content' ), 10, 2 );
		add_filter( 'um_account_content_hook_downloads', array( &$this, 'account_downloads_tab_content' ), 10, 2 );
		add_filter( 'um_account_content_hook_payment-methods', array( &$this, 'account_payment_methods_tab_content' ), 10, 2 );

		/**
		 * Integration for plugin WooCommerce Memberships
		 * @link https://docs.woocommerce.com/document/woocommerce-memberships/ WooCommerce Memberships
		 * @since 2019-05-02
		 */
		if ( class_exists( 'WC_Memberships' ) ) {
			add_filter( 'um_account_content_hook_memberships', array( &$this, 'account_memberships_tab_content' ), 10, 1 );
		}

		if ( class_exists( 'WC_Subscriptions' ) ) {
			add_filter( 'um_account_content_hook_subscription', array( &$this, 'account_subscription_tab_content' ), 10, 1 );
		}

		add_action( 'um_submit_account_billing_tab_errors_hook', array( &$this, 'account_errors_hook' ), 10 );
		add_action( 'um_submit_account_shipping_tab_errors_hook', array( &$this, 'account_errors_hook' ), 10 );

		add_action( 'template_redirect', array( &$this, 'um_woocommerce_pre_update' ), 1 );
		
		/**
		 * Account address submit handler (for tabs "Billing Address" and "Shipping Address")
		 * @since 2019-05-23
		 */
		add_action( 'template_redirect', array( &$this, 'um_woocommerce_account_submit' ), 5 );

		add_action( 'um_update_profile_full_name', array( &$this, 'um_sync_update_user_wc_email' ), 10, 2 );
		add_action( 'woocommerce_checkout_update_user_meta', array( &$this, 'um_update_um_profile_from_wc_billing' ), 10, 2 );
		add_action( 'woocommerce_customer_save_address', array( &$this, 'um_update_um_profile_from_wc_billing' ), 10, 2 );
		add_action( 'um_after_user_account_updated', array( &$this, 'um_call_wc_user_account_update' ), 99, 2 );

		add_filter( 'um_custom_success_message_handler', array( &$this, 'um_woocommerce_custom_notice' ), 10, 2 );
	}


	/**
	 * Add tab to account page
	 *
	 * @param $tabs
	 *
	 * @return mixed
	 */
	function account_tabs( $tabs ) {

		if ( um_user( 'woo_account_billing' ) && ! UM()->options()->get( 'woo_hide_billing_tab_from_account' ) ) {
			$tabs[210]['billing'] = array(
				'icon'          => 'um-faicon-credit-card',
				'title'         => __( 'Billing Address', 'um-woocommerce' ),
				'submit_title'  => __( 'Save Address', 'um-woocommerce' ),
				'custom'        => true,
			);
		}

		if ( um_user( 'woo_account_shipping' ) && ! UM()->options()->get('woo_hide_shipping_tab_from_account') ) {
			$tabs[220]['shipping'] = array(
				'icon'          => 'um-faicon-truck',
				'title'         => __( 'Shipping Address', 'um-woocommerce' ),
				'submit_title'  => __( 'Save Address', 'um-woocommerce' ),
				'custom'        => true,
			);
		}

		if ( um_user( 'woo_account_orders' ) ) {
			$tabs[230]['orders'] = array(
				'icon'          => 'um-faicon-shopping-cart',
				'title'         => __( 'My Orders', 'um-woocommerce' ),
				'custom'        => true,
				'show_button'   => false,
			);
		}

		/**
		 * Integration for plugin WooCommerce Memberships
		 * @link https://docs.woocommerce.com/document/woocommerce-memberships/ WooCommerce Memberships
		 * @since 2019-05-02
		 */
		if ( class_exists( 'WC_Memberships' ) ) {
			$tabs[ 235 ][ 'memberships' ] = array(
					'icon'          => 'um-faicon-users',
					'title'         => __( 'Memberships', 'um-woocommerce' ),
					'custom'        => true,
					'show_button'   => false,
			);
		}

		if ( class_exists( 'WC_Subscriptions' ) ) {
			$tabs[240]['subscription'] = array(
				'icon'          => 'um-faicon-book',
				'title'         => __( 'Subscriptions', 'um-woocommerce' ),
				'custom'        => true,
				'show_button'   => false,
			);
		}

		if ( um_user( 'woo_account_downloads' ) ) {
			$tabs[250]['downloads'] = array(
				'icon'          => 'um-faicon-download',
				'title'         => __( 'Downloads', 'um-woocommerce' ),
				'custom'        => true,
				'show_button'   => false,
			);
		}

		if ( um_user( 'woo_account_payment_methods' ) ) {
			$tabs[260]['payment-methods'] = array(
				'icon'          => 'um-faicon-credit-card',
				'title'         => __( 'Payment methods', 'um-woocommerce' ),
				'custom'        => true,
				'show_button'   => false,
			);
		}

		return $tabs;
	}


	/**
	 * Edit Address - "Billing Address" or "Shipping Address" tab content
	 *
	 * @global	WP_User		$current_user
	 * @param		string		$address_type
	 */
	function edit_address( $address_type ) {
		// Current user
		global $current_user;

		$load_address = sanitize_key( $address_type );

		$address = WC()->countries->get_address_fields( get_user_meta( get_current_user_id(), $load_address . '_country', true ), $load_address . '_' );

		$arr_fields = array();
		// Prepare values
		foreach( $address as $key => $field ) {

			$value = get_user_meta( get_current_user_id(), $key, true );

			if( !$value ) {
				switch( $key ) {
					case 'billing_email' :
					case 'shipping_email' :
						$value = $current_user->user_email;
						break;
					case 'billing_country' :
					case 'shipping_country' :
						$value = WC()->countries->get_base_country();
						break;
					case 'billing_state' :
					case 'shipping_state' :
						$value = WC()->countries->get_base_state();
						break;
				}
			}

			if( !empty( $_POST[ $key ] ) ) {
				$value = wc_clean( $_POST[ $key ] );
			}

			$address[ $key ][ 'value' ] = apply_filters( 'woocommerce_my_account_edit_address_field_value', $value, $key, $load_address );
		}

		// Print fields
		do_action( "woocommerce_before_edit_address_form_{$load_address}" );

		foreach( $address as $key => $field ) {
			$field[ 'custom_attributes' ][ 'data-key' ] = $key;
			$field[ 'input_class' ][] = 'um-form-field';
			$field[ 'return' ] = true;
			$field[ 'type' ] = !empty( $field[ 'type' ] ) ? $field[ 'type' ] : 'text';
			
			// Get form field
			$html = woocommerce_form_field( $key, $field, $field[ 'value' ] );

			// Wrapp field in UM style
			$html = str_replace( '<label', '<div class="um-field-label"><label', $html );
			$html = str_replace( '</label>', '</label></div><div class="um-clear"></div>', $html );
			$html = preg_replace( '/\<span class\=\"woocommerce-input-wrapper\"\>(.*?)\<\/span\>/im', "<div class=\"um-field-area\">$1</div>", $html );
			$html = preg_replace( '/\<p([^\>]*?)\>(.*?)\<\/p\>/im', "$2", $html );

			if( in_array( $key, array( 'billing_email' ) ) ) {
				$html = str_replace( '<input', '<input disabled', $html );
			}
			$arr_fields[ $key ] = array( 'metakey' => $key );
			?>

			<div class="um-field um-field-<?php echo esc_attr( $key ); ?> um-field-<?php echo esc_attr( $field[ 'type' ] ); ?> um-field-type_<?php echo esc_attr( $field[ 'type' ] ); ?>" data-key="<?php echo esc_attr( $key ); ?>">

				<?php
				echo apply_filters( 'um_account_woocommerce_field', $html, $key, $value, $field, $load_address );

				if( UM()->fields()->is_error( $key ) ) {
					echo UM()->fields()->field_error( UM()->fields()->show_error( $key ) );
				}
				?>

			</div>

			<?php
		}

		$arr_fields = apply_filters('um_account_secure_fields', $arr_fields, $load_address );
		do_action( "woocommerce_after_edit_address_form_{$load_address}" );

		// Enqueue scripts
		wp_enqueue_script( 'wc-country-select' );
		wp_enqueue_script( 'wc-address-i18n' );
	}


	/**
	 * Trigger Shipping/Billing fields validation
	 */
	function account_errors_hook() {

		$load_address = $_POST['_um_account_tab'];
		$load_address = sanitize_key( $load_address );

		$address = WC()->countries->get_address_fields( get_user_meta( get_current_user_id(), $load_address . '_country', true ), $load_address . '_' );

		$error_trigger = false;
		foreach ( $address as $key => $field_data ) {
			if ( $key == 'billing_email' ) {
				continue;
			}
			if ( ! empty( $field_data['required'] ) && empty( $_POST[ $key ] ) ) {
				UM()->form()->add_error( $key, sprintf( __( '"%s" field is required', 'ultimate-member' ), $field_data['label'] ) );
				$error_trigger = true;
			}
		}

		if ( $error_trigger ) {
			return;
		}
	}


	/**
	 * Add content to account tab
	 *
	 * @param $output
	 * @param $shortcode_args
	 *
	 * @return string
	 */
	function account_billing_tab_content( $output, $shortcode_args ) {
		global $wp;

		wp_enqueue_script( 'um-woocommerce' );
		wp_enqueue_style( 'um-woocommerce' );

		$wp->query_vars['edit-address'] = 'billing';
		ob_start(); ?>

		<div class="um-woo-form um-woo-billing">
			<?php $this->edit_address( 'billing' ); ?>
		</div>

		<?php $output .= ob_get_clean();

		return do_shortcode( $output );
	}


	/**
	 * Add content to account tab
	 *
	 * @param $output
	 * @param $shortcode_args
	 *
	 * @return string
	 */
	function account_shipping_tab_content( $output, $shortcode_args ) {
		global $wp;

		wp_enqueue_script( 'um-woocommerce' );
		wp_enqueue_style( 'um-woocommerce' );

		$wp->query_vars['edit-address'] = 'shipping';
		ob_start(); ?>

		<div class="um-woo-form um-woo-shipping">
			<?php $this->edit_address( 'shipping' ); ?>
		</div>

		<?php $output .= ob_get_clean();

		return do_shortcode( $output );
	}


	/**
	 * Add content to account tab
	 *
	 * @param $output
	 * @param $shortcode_args
	 *
	 * @return string
	 */
	function account_orders_tab_content( $output, $shortcode_args ) {

		$orders_per_page = 10;
		$orders_page = isset( $_REQUEST['orders_page'] ) ? $_REQUEST['orders_page'] : 1;

		$args = apply_filters( "um_woocommerce_account_orders_args", array(
			'posts_per_page'	=> $orders_per_page,
			'paged'						=> $orders_page,
			'meta_key'    		=> '_customer_user',
			'meta_value'  		=> get_current_user_id(),
			'post_type'				=> wc_get_order_types( 'view-orders' ),
			'post_status' 		=> array_keys( wc_get_order_statuses() ),
			'order'						=> 'ASC'
		) );

		$loop = new \WP_Query( $args );

		$total_pages =  ceil( $loop->found_posts / $orders_per_page );
		$pages_to_show = $total_pages ;

		$url = UM()->account()->tab_link( 'orders' );

		$date_format = get_option( 'date_format' );
		$time_format = get_option( 'time_format' );
		$date_time_format = $date_format . ' ' . $time_format;

		$customer_orders = $loop->posts;

		$t_args = compact( 'args', 'customer_orders', 'date_time_format', 'orders_page', 'orders_per_page', 'pages_to_show', 'total_pages', 'url' );
		$output .= UM()->get_template( 'orders.php', um_woocommerce_plugin, $t_args );

		wp_enqueue_script( 'um-woocommerce' );
		wp_enqueue_style( 'um-woocommerce' );

		return do_shortcode( $output );
	}
	
	
	/**
	 * Add content to account tab 'Downloads'
	 * @param string $output
	 * @return string
	 */
	function account_downloads_tab_content( $output = '' ) {
		wp_enqueue_script( 'um-woocommerce' );
		wp_enqueue_style( 'um-woocommerce' );

		ob_start();
		echo '<div class="um-woo-form um-woo-downloads">';
		do_action( 'woocommerce_account_downloads_endpoint' );
		echo '</div>';
		$output .= ob_get_clean();
		
		return do_shortcode( $output );
	}
	
	
	/**
	 * Add content to account tab 'Payment methods'
	 * @param string $output
	 * @return string
	 */
	function account_payment_methods_tab_content( $output = '' ) {
		wp_enqueue_script( 'um-woocommerce' );
		wp_enqueue_style( 'um-woocommerce' );
		
		// fake data for function is_add_payment_method_page()
		add_filter( 'woocommerce_get_myaccount_page_id', function( $page_id ){
			global $post, $wp;
			$wp->query_vars['payment-methods'] = 1;
			return $post->ID;
		}, 20 );

		ob_start();
		echo '<div class="um-woo-form um-woo-payment-methods">';
		do_action( 'woocommerce_account_payment-methods_endpoint' );
		echo '</div>';
		$output .= ob_get_clean();
		
		return do_shortcode( $output );
	}


	/**
	 * Add content to account tab 'Memberships'
	 * 
	 * @link	https://docs.woocommerce.com/document/woocommerce-memberships/ WooCommerce Memberships
	 * @since	2019-05-02
	 * 
	 * @param		string	$output
	 * @return	string
	 */
	function account_memberships_tab_content( $output = '' ) {

		if ( is_callable( 'wc_memberships_get_user_memberships' ) ) {

			$user_memberships = wc_memberships_get_user_memberships();

			if ( $user_memberships ) {
				ob_start();

				/** Fires before the Memberships table in My Account page. */
				do_action( 'wc_memberships_before_my_memberships' );

				wc_get_template( 'myaccount/my-memberships.php', array(
					'customer_memberships'  => $user_memberships,
					'user_id'               => get_current_user_id(),
				) );

				/** Fires after the Memberships table in My Account page. */
				do_action( 'wc_memberships_after_my_memberships' );

				$output = ob_get_clean();
			}

			if ( empty( $output ) ) {
				$output = '<p>' . __( 'No User Memberships found', 'um-woocommerce' ) . '</p>';
			}
		}

		$output = '<div class="um-woo-form um-woo-memberships">' . $output . '</div>';

		return do_shortcode( $output );
	}
	

	/**
	 * @param $output
	 *
	 * @return string
	 */
	function account_subscription_tab_content( $output ) {
		wp_enqueue_script( 'um-woocommerce' );
		wp_enqueue_style( 'um-woocommerce' );

		ob_start();
		echo '<div class="um-woo-form um-woo-subscriptions">';
		do_action( 'woocommerce_add_subscriptions_to_my_account' );
		echo '</div>';
		$output .= ob_get_clean();
		
		return do_shortcode( $output );
	}


	/**
	 * Before woocommerce update address
	 */
	function um_woocommerce_pre_update() {
		/*global $wp;

		if ( isset( $_POST['um_account_submit'] ) && get_query_var('um_tab') == 'shipping' ) {
			$wp->query_vars['edit-address'] = 'shipping';
		}

		if ( isset( $_POST['um_account_submit'] ) && get_query_var('um_tab') == 'billing' ) {
			$wp->query_vars['edit-address'] = 'billing';
		}*/

		if ( wc_has_notice( __( 'Address changed successfully.', 'woocommerce' ) ) ) {
			wc_clear_notices();
			$url = UM()->account()->tab_link( 'billing' );
			exit( wp_redirect( add_query_arg( 'updated', 'edit-billing', $url ) ) );
		}

	}


	/**
	 * Account address submit handler
	 * @since 2019-05-23
	 */
	function um_woocommerce_account_submit() {
		
		// run only on "Account" page submit
		if ( um_submitting_account_page() && um_is_core_page( 'account' ) ) {

			$um_tab = get_query_var( 'um_tab' );
			
			// run only on tabs "Billing Address" or "Shipping Address"
			if ( in_array( $um_tab, array( 'billing', 'shipping' ) ) ) {
				UM()->account()->account_submit();
			}
		}
	}


	/**
	 * Update billing email when the user's email address is changed
	 *
	 * @param $user_id
	 * @param $changes
	 */
	function um_sync_update_user_wc_email( $user_id, $changes ) {
		if(isset($changes['user_email'])) {
			update_user_meta( UM()->user()->id, 'billing_email', $changes['user_email']);
		}

		if(isset($changes['first_name'])) {
			update_user_meta( UM()->user()->id, 'billing_first_name', $changes['first_name']);
		}

		if(isset($changes['last_name'])) {
			update_user_meta( UM()->user()->id, 'billing_last_name', $changes['last_name']);
		}
	}


	/**
	 * Update um profile when wc billing is updated
	 *
	 * @param $user_id
	 * @param null $data
	 */
	function um_update_um_profile_from_wc_billing($user_id, $data = null) {

		if ( isset( $_POST['um_account_submit'] ) && isset( $_POST[ 'billing_first_name'] ) && isset( $_POST['billing_last_name'] ) && isset( $_POST[ 'billing_email' ] ) ) {
			$changes = array();
			foreach($_POST as $key => $value) {
				if(preg_match('/^billing_/', $key)) {
					$key           = str_replace('billing_', '', $key);

					if (in_array($key, array('first_name', 'last_name', 'user_email'))) {
						$changes[$key] = $value;

						update_user_meta( $user_id, $key, $value );
					}
				}
			}

			wp_update_user( array(
				'ID'            => $user_id,
				'user_email'    => $_POST['billing_email']
			) );

			// hook for name changes
			do_action( 'um_update_profile_full_name', $user_id, $changes );

			UM()->user()->remove_cache( $user_id );
		}
	}


	/**
	 * @param $user_id
	 * @param $changes
	 */
	function um_call_wc_user_account_update( $user_id, $changes ) {
		global $wp;

		if( $wp->query_vars['edit-address'] == 'billing' || $wp->query_vars['edit-address'] == 'shipping' ) {
			do_action( 'woocommerce_customer_save_address', $user_id, $wp->query_vars['edit-address'] );
		}

		if ( isset( $_POST['um_account_submit'] ) && get_query_var('um_tab') == 'shipping' ) {
			exit( wp_redirect( add_query_arg('updated','edit-shipping') ) );
		}

		if ( isset( $_POST['um_account_submit'] ) && get_query_var('um_tab') == 'billing' ) {
			exit( wp_redirect( add_query_arg('updated','edit-billing') ) );
		}
	}






	/**
	 * Custom notice
	 *
	 * @param $msg
	 * @param $err_t
	 *
	 * @return string
	 */
	function um_woocommerce_custom_notice( $msg, $err_t ) {

		if ( $err_t == 'edit-billing' ) {
			$msg = __( 'Your billing address is updated.', 'um-woocommerce' );
		}

		if ( $err_t == 'edit-shipping' ) {
			$msg = __( 'Your shipping address is updated.', 'um-woocommerce' );
		}

		return $msg;
	}




}