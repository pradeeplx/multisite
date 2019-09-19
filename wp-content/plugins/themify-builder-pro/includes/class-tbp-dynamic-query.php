<?php

class Tbp_Dynamic_Query {

	var $field_name = 'tbpdq';

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @return	A single instance of this class.
	 */
	public static function get_instance() {
		static $instance = null;
		if ( $instance === null ) {
			$instance = new self;
		}

		return $instance;
	}

	private function __construct() {
		add_filter( 'themify_builder_module_settings_fields', array( $this, 'themify_builder_module_settings_fields' ), 10, 2 );
		add_action( 'themify_builder_module_render_vars', array( $this, 'themify_builder_module_render_vars' ) );
	}

	/**
	 * Looks for fields of "query_posts" type and adds the dynamic query options to it
	 *
	 * @return null
	 */
	function add_fields( &$opt, $key ) {
		if ( isset( $opt['id'], $opt['type'] ) && $opt['type'] === 'query_posts' ) {
			$opt = array(
				'type' => 'group',
				'options' => array(
					array(
						'id' => $this->field_name,
						'type' => 'toggle_switch',
						'label' => __('Dynamic Query', 'tbp'),
						'options' => array(
							'off' => array( 'value' => __( 'Disabled', 'tbp' ), 'name' => 'off' ),
							'on' => array( 'value' => __( 'Enabled', 'tbp' ), 'name' => 'on' ),
						),
						'binding' => array(
							'off' => array( 'show' => array( $opt['id'] ) ),
							'on' => array( 'hide' => array( $opt['id'] ) ),
						),
						'help' => __( 'Use this on Builder Pro archive template only. The archive view (category or tag pages) will use this module to display the posts.', 'tbp' ),
					),
					$opt,
				),
			);
		} else {
			if ( is_array( $opt ) ) {
				array_walk( $opt, array( $this, 'add_fields' ) );
			}
		}
	}

	/**
	 * Filter Builder module settings array
	 *
	 * @return array
	 */
	function themify_builder_module_settings_fields( $options, $module ) {
		array_walk( $options, array( $this, 'add_fields' ) );

		return $options;
	}

	/**
	 * Runs just before a module is rendered, enable Dynamic Query if applicable
	 *
	 * @return array
	 */
	function themify_builder_module_render_vars( $vars ) {
		/**
		 * Reset the "pre_get_posts" filter
		 * This is to ensure that filter is applied only once and does not affect other modules.
		 */
		remove_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );

		if ( ! is_archive() )
			return $vars;

		if ( isset( $vars['mod_settings'][ $this->field_name ] ) && $vars['mod_settings'][ $this->field_name ] === 'on' ) {
			add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
		}

		return $vars;
	}

	/**
	 * Replace all the query vars of the current query with global $wp_query
	 *
	 */
	function pre_get_posts( $query ) {
		global $wp_query;

		/**
		 * In case this is the last module in the page and there are other queries running
		 * after this, reset "pre_get_posts" again to ensure this filter runs only once.
		 */
		remove_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );

		$query->query_vars = $wp_query->query_vars;
		if ( isset( $query->query['posts_per_page'] ) ) {
			$query->query_vars['posts_per_page'] = $query->query['posts_per_page'];
		}
		if ( isset( $query->query['offset'] ) ) {
			$query->query_vars['offset'] = $query->query['offset'];
		}
		if ( isset( $query->query['paged'] ) ) {
			$query->query_vars['paged'] = $query->query['paged'];
		}
	}
}
Tbp_Dynamic_Query::get_instance();