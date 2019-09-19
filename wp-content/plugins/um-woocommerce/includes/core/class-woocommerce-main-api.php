<?php
namespace um_ext\um_woocommerce\core;


if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class WooCommerce_Main_API
 * @package um_ext\um_woocommerce\core
 */
class WooCommerce_Main_API {


	/**
	 * WooCommerce_Main_API constructor.
	 */
	function __construct() {

	}


	/**
	 * Check if Woo Subscriptions plugin is active
	 *
	 * @return bool
	 */
	function is_wc_subscription_plugin_active() {
		return function_exists( 'wcs_get_subscription' );
	}


	/**
	 * Check single product order need or not need to change user role
	 *
	 * @param int $order_id
	 *
	 * @return array|bool
	 */
	function change_role_data_single( $order_id ) {
		$order = new \WC_Order( $order_id );
		$user_id = $order->get_user_id();
		um_fetch_user( $user_id );

		// fetch role and excluded roles
		$user_role = UM()->user()->get_role();
		$excludes = UM()->options()->get( 'woo_oncomplete_except_roles' );
		$excludes = empty( $excludes ) ? array() : $excludes;

		$data = array();

		//items have more priority
		$items = $order->get_items();
		foreach ( $items as $item ) {
			$id = $item['product_id'];
			if ( get_post_meta( $id, '_um_woo_product_role', true ) != '' && ( empty( $excludes ) || ! in_array( $user_role, $excludes ) ) ) {
				$role = esc_attr( get_post_meta( $id, '_um_woo_product_role', true ) );
				$data = array( 'user_id' => $user_id, 'role' => $role );
			}
		}

		if ( empty( $data ) ) {
			$role = UM()->options()->get( 'woo_oncomplete_role' );
			if ( $role && ! user_can( $user_id, $role ) && ( empty( $excludes ) || ! in_array( $user_role, $excludes ) ) ) {
				return array( 'user_id' => $user_id, 'role' => $role );
			}
		} else {
			return $data;
		}

		return false;
	}


	/**
	 * Check single product order need or not need to change user role
	 *
	 * @param int $order_id
	 *
	 * @return array|bool
	 */
	function change_role_data_single_refund( $order_id ) {
		$order = new \WC_Order( $order_id );
		$user_id = $order->get_user_id();

		$role = UM()->options()->get( 'woo_onrefund_role' );
		if ( $role && ! user_can( $user_id, $role ) ) {
			return array( 'user_id' => $user_id, 'role' => $role );
		}

		return false;
	}


	/**
	 * Get Order Data via AJAX
	 */
	function ajax_get_order() {
		UM()->check_ajax_nonce();

		if ( ! isset( $_POST['order_id'] ) || ! is_user_logged_in() ) {
			wp_send_json_error();
		}

		$is_customer = get_post_meta( $_POST['order_id'], '_customer_user', true );

		if ( $is_customer != get_current_user_id() ) {
			wp_send_json_error();
		}
		um_fetch_user( get_current_user_id() );

		$order_id = $_POST['order_id'];
		$order = wc_get_order( $order_id );
		$notes = $order->get_customer_order_notes();

		$t_args = compact( 'order', 'order_id', 'notes' );
		$output = UM()->get_template( 'order-popup.php', um_woocommerce_plugin, $t_args );

		wp_send_json_success( $output );
	}


	/**
	 * Get Subscription Data via AJAX
	 */
	function ajax_get_subscription() {
		UM()->check_ajax_nonce();

		$subscription = wcs_get_subscription( $_POST['subscription_id'] );
		$actions = wcs_get_all_user_actions_for_subscription( $subscription, get_current_user_id() );
		$notes = $subscription->get_customer_order_notes();

		$columns = array(
			'last_order_date_created' => _x( 'Last Order Date', 'admin subscription table header', 'ultimate-member' ),
			'next_payment'            => _x( 'Next Payment Date', 'admin subscription table header', 'ultimate-member' ),
			'end'                     => _x( 'End Date', 'table heading', 'ultimate-member' ),
			'trial_end'               => _x( 'Trial End Date', 'admin subscription table header', 'ultimate-member' ),
		);

		$t_args = compact( 'actions', 'columns', 'notes', 'subscription' );
		$output = UM()->get_template( 'subscription.php', um_woocommerce_plugin, $t_args );

		wp_send_json_success( $output );
	}


	/**
	 * Check if current user has subscriptions and return subscription IDs
	 * @param  integer			$user_id
	 * @param  string				$product_id
	 * @param  string				$status
	 * @param  array|int		$except_subscriptions
	 * @return array|bool		subscription products ids
	 */
	function user_has_subscription( $user_id = 0, $product_id = '', $status = 'any', $except_subscriptions = array() ) {

		if ( ! function_exists('wcs_get_users_subscriptions') ) {
			return '';
		}

		$subscriptions = wcs_get_users_subscriptions( $user_id );
		$has_subscription = false;
		$arr_product_ids = array();
		if ( empty( $product_id ) ) { // Any subscription
			if ( ! empty( $status ) && 'any' != $status ) { // We need to check for a specific status
				foreach ( $subscriptions as $subscription ) {
					if( in_array( $subscription->get_id(), (array) $except_subscriptions ) ){
						continue;
					}
					if ( $subscription->has_status( $status ) ) {
						$order_items  = $subscription->get_items();
						foreach ( $order_items as $order ) {
							$arr_product_ids[ ] = wcs_get_canonical_product_id( $order );
						}
					}
				}

				return $arr_product_ids;

			} elseif ( ! empty( $subscriptions ) ) {
				$has_subscription = true;
			}
		} else {
			foreach ( $subscriptions as $subscription ) {
				if( in_array( $subscription->get_id(), (array) $except_subscriptions ) ){
					continue;
				}
				if ( $subscription->has_product( $product_id ) && ( empty( $status ) || 'any' == $status || $subscription->has_status( $status ) ) ) {
					$has_subscription = true;
					break;
				}
			}
		}
		return $has_subscription;
	}

}
