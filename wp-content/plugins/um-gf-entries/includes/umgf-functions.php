<?php
/**
 * General functions for UMGF_Entries
 *
 * @package  UMGF_Entries
 */

/**
 * Get Gravity form fields
 */
function umgf_get_fields() {
	$form_id = ( isset( $_GET['form_id'] ) ? absint( $_GET['form_id'] ) : 0 );
	$form = RGFormsModel::get_form_meta( $form_id );
	$fields = array();

	if ( is_array( $form['fields'] ) ) {
		foreach ( $form['fields'] as $field ) {
			if ( isset( $field['inputs'] ) && is_array( $field['inputs'] ) ) {

				foreach ( $field['inputs'] as $input ) {
					$fields[] = array( 'ID' => $input['id'], 'name' => GFCommon::get_label( $field, $input['id'] ) );
				}
			} elseif ( ! rgar( $field, 'displayOnly' ) ) {
				$fields[] = array( 'ID' => $field['id'], 'name' => GFCommon::get_label( $field ) );
			}
		}
	}
	wp_send_json( $fields );
}
add_action( 'wp_ajax_umgf_get_fields', 'umgf_get_fields' );

/**
 * Setup Tab for UMGF
 *
 * @return array
 */
function umgf_get_tabs() {
	global $wpdb;
	if ( false === ( $tabs_setup = get_option( 'umgf_tabs_cache' ) ) ) {
		$posts = $wpdb->get_results( "SELECT * FROM {$wpdb->posts} WHERE post_type='umgf_entries' AND post_status ='publish' ");
		if ( ! empty( $posts ) ) {
			foreach( $posts as $post ) {
				$post_id     = $post->ID;
				$section =  get_post_meta($post_id, '__umgf_tab_section', true);
				$tab_name = get_post_meta($post_id, '__umgf_tab_name', true);
				$tab_slug = get_post_meta($post_id, '__umgf_tab_slug', true);
				$tab_icon = get_post_meta($post_id, '__umgf_tab_icons', true);
				$tab_icon = ($tab_icon ? $tab_icon : 'um-faicon-user');
				$private_tab = get_post_meta($post_id, '__umgf_tab_privacy', true );
				$allowed_roles = get_post_meta($post_id, '__umgf_allowed_roles', true);

				$tabs_setup[] = array(
					'name'          => $tab_name,
					'slug'          => $tab_slug,
					'icon'          => $tab_icon,
					'private'       => $private_tab,
					'section'       => $section,
					'allowed_roles' => $allowed_roles,
					'post_id'       => $post_id,
				);

			}
		}
		update_option( 'umgf_tabs_cache', $tabs_setup );
	}
	return $tabs_setup;
}

/**
 * Get Post Tab
 *
 * @return string
 */
function umgf_get_tab_post_ID() {
	$slug = ( ! empty( $_GET['profiletab'] ) ? sanitize_text_field( $_GET['profiletab'] ) : '' );
	if ( ! $slug ) {
		return;
	}
	global $wpdb;
	$query = "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key ='__umgf_tab_slug' AND meta_value='%s'";
	return $wpdb->get_var( $wpdb->prepare( $query, $slug ) );
}