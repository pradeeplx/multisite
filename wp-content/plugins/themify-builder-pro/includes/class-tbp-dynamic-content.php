<?php

class Tbp_Dynamic_Content {

	private static $items = array();

	/**
	 * Name of the option that stores Dynamic Content settings
	 *
	 * @type string
	 */
	private static $field_name = '__dc__';

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
		add_action( 'init', array( $this, 'register_items' ) );
		add_action( 'themify_builder_module_render_vars', array( $this, 'do_replace' ) );
		if ( Themify_Builder_Model::is_frontend_editor_page() === true ) {
		    add_action( 'themify_builder_frontend_enqueue', array( $this, 'admin_enqueue' ) );
		    add_action( 'themify_builder_admin_enqueue', array( $this, 'admin_enqueue' ), 15 );
		    add_action( 'wp_ajax_tpb_get_dynamic_content_fields', array( $this, 'options' ) );
		    add_action( 'wp_ajax_tpb_get_dynamic_content_preview', array( $this, 'preview' ) );
		}
		add_action( 'themify_builder_background_styling', array( $this, 'background_styling' ), 10, 4 );
	}

	public function register_items() {
		$items = array();
		$base_path = TBP_DIR . 'includes/dynamic-content/';
		$d = dir( $base_path );
		while ( ( false !== ( $entry = $d->read() ) ) ) {
			if ( $entry !== '.' && $entry !== '..' && $entry !== '.svn' ) {
			    include_once $base_path . $entry;
			    $name = pathinfo( $entry, PATHINFO_FILENAME );
			    $items[ $name ] = "Tbp_Dynamic_Item_{$name}";
			}
		}
		$d = $entry = null;
		$items = apply_filters( 'tbp_dynamic_items', $items );
		foreach ( $items as $id => $class ) {
		    $instance = new $class();
		    /* add this item only if is_available() */
		    if ( $instance->is_available() === true ) {
			    self::$items[ $id ] = $instance;
		    } else {
				$instance = null;
		    }
		}
		$items = null;
	}

	public function get( $id = null ) {
		return $id === null ? self::$items : ( isset( self::$items[ $id ] ) ? self::$items[ $id ] : false );
	}

	/**
	 * Returns an assoc array
	 *
	 * @return array
	 */
	public function get_list() {
		$list = array();
		foreach ( self::$items as $id => $instance ) {
			$list[ $id ] = array(
				'type' => $instance->get_type(),
			);
		}

		return $list;
	}

	/**
	 * Adds inline styles for styling the background image of Builder components
	 *
	 * hooked to "themify_builder_background_styling"
	 */
	function background_styling( $builder_id, $settings, $order_id, $type ) {
		$setting_name = in_array( $type, array( 'row', 'subrow', 'column' ), true ) ? 'styling' : 'mod_settings';

		if ( ! isset( $settings[ $setting_name ][ self::$field_name ] ) ) {
			return;
		}
		$dc = json_decode( $settings[ $setting_name ][ self::$field_name ], true );
		if ( ! is_array( $dc ) ) {
			return;
		}
		if ( $setting_name === 'styling' ) {
			$mod_name = $type;
			$types = Themify_Builder_Components_Manager::get_component_types();
			$module = $types[ $mod_name ];
		} else {
			$mod_name = $settings['mod_name'];
			$module = Themify_Builder_Model::$modules[ $mod_name ];
		}
		$styling = $module->get_form_settings( true );

		$bg_fields = $this->get_background_image_fields( $styling );
		if ( empty( $bg_fields ) || empty($settings['element_id']) ) {
			return;
		}

		$type_selector = in_array( $type, array( 'row', 'subrow', 'column' ), true ) ? '.module_' . $mod_name : '.module-' . $mod_name;
		$element_id = $settings['element_id'];
		$styles = '';
		foreach ( $dc as $key => $options ) {
			if ( isset( $bg_fields[ $key ] ) && ( $value = $this->get_value( $options ) ) ) {
			    $hover = isset( $bg_fields[ $key ]['ishover'] ) && $bg_fields[ $key ]['ishover'] === true ? ':hover' : '';
			    $selector = ".themify_builder.themify_builder_content-{$builder_id} {$type_selector}.tb_{$element_id}{$bg_fields[ $key ]['selector']}{$hover}";
			    $styles .= $selector . '{ background-image: url("' . $value . '") }';
			
			}
		}
		if ( ! empty( $styles ) ) {
			echo '<style type="text/css">' . $styles . '</style>';
		}
	}

	/**
	 * Loops through a component styling definition to find all background-image fields
	 *
	 * @return array
	 */
	public function get_background_image_fields( array $array ) {
		$iterator  = new RecursiveArrayIterator( $array );
		$recursive = new RecursiveIteratorIterator( $iterator, RecursiveIteratorIterator::SELF_FIRST );
		$list = array();
		foreach ( $recursive as  $value ) {
			if ( isset( $value['prop'], $value['id'] ) && $value['prop'] === 'background-image' ) {
				$list[ $value['id'] ] = $value;
			}
		}
		return $list;
	}

	public function do_replace( $vars ) {
		if ( ! isset( $vars['mod_settings'][ self::$field_name ] ) || $vars['mod_settings'][ self::$field_name ]==='{}' )
			return $vars;
		$fields = json_decode( $vars['mod_settings'][ self::$field_name ], true );

		if ( empty( $fields ) || ! is_array( $fields ) ) {
			return $vars;
		}
		foreach ( $fields as $key => $options ) {
			if ( ! isset( $options['item'] ) || isset( $options['repeatable'] ) ) {
			    if ( isset( $vars['mod_settings'][ $key ] ) && is_array( $vars['mod_settings'][ $key ] ) ) {
					unset( $options['repeatable'], $options['o'] );
					// loop through repeatable items
					foreach ( $options as $i => $items ) {  
						foreach ( $items as $field_name => $field_options ) {
							if ( isset( $field_options['item'] ) ) {
								$value = $this->get_value( $field_options );
								$vars['mod_settings'][ $key ][ $i ][ $field_name ] = $value;
							}
						}
					}
			    }
			} else {
				$value = $this->get_value( $options );
				$vars['mod_settings'][ $key ] = $value;
			}
		}

		return $vars;
	}

	/**
	 * Get value from saved DC settings
	 *
	 * Calls Tbp_Dynamic_Content::get_value for $options['item']
	 */
	private function get_value( $options ) {
		if ( isset( $options['item'] ) && ( $item = $this->get( $options['item'] ) ) ) {
			unset( $options['item'] );
			$value = $item->get_value( $options );
			if ( isset( $options['text_before'] ) ) {
				$value = $options['text_before'] . $value;
			}
			if ( isset( $options['text_after'] ) ) {
				$value .= $options['text_after'];
			}
			return $value;
		}
		return null;
	}

	function admin_enqueue() {
		$v=Tbp::get_instance()->get_version();
		wp_enqueue_script( 'tbp-dynamic-content', themify_enque(TBP_URL . 'admin/js/tbp-dynamic-content.js') , array( 'themify-builder-app-js' ), $v, true );
		wp_localize_script( 'tbp-dynamic-content', 'tbpDynamic',
			array(
				'items' => $this->get_list(),
				'field_name' => self::$field_name,
				'v'=>$v,
				'd_label'=>__('Dynamic','themify'),
				'emptyVal'=>__('Empty Value','themify'),
				'placeholder_image' => TBP_URL . 'admin/img/template-placeholder.png',
				'excludes' => $this->get_option_excludes()
			)
		);
	}

	/**
	 * list of option IDs that will not have DC enabled on them
	 *
	 * @return array
	 */
	private function get_option_excludes() {
		return array(
			'item_title_field',
			'placeholder',
			'button_t',
			'custom_url',
			'fallback_i',
			'prev_label',
			'next_label',
			'custom_link',
			'cat',
			'tag',
			'sku',
			'sep',
			'heading'
		);
	}

	/**
	 * Generate preview value
	 *
	 * Hooked to "wp_ajax_tpb_get_dynamic_content_preview"
	 */
	public function preview() {
		check_ajax_referer( 'tb_load_nonce', 'tb_load_nonce' );
		Themify_Builder::$frontedit_active = true;
		// before rendering the dynamic value, first set up the WP Loop
		if ( isset( $_POST['pid'] )  && Tbp_Utils::$isLoop !== true ) {
		    $post_id = (int) $_POST['pid'];
		    if ( $post_object = get_post( $post_id ) ) {
			    setup_postdata( $GLOBALS['post'] =& $post_object );
		    }
		}
		$options = ! empty( $_POST['values'] )? json_decode( stripslashes_deep( $_POST['values'] ), true ) : array();
		if ( isset( $options['item'] ) ) {
		    $value = array( 'value' => $this->get_value( $options ) );
		} else {
			$value = array( 'error' => __( 'Invalid value.', 'themify' ) );
		}
		die( json_encode( $value ) );
	}
	
	public function options() {
		check_ajax_referer('tb_load_nonce', 'tb_load_nonce');
		$items_list = $items_settings = array();
		$categories = array(
			'disabled' => '',
			'general' => __( 'General', 'themify' ),
			'post' => __( 'Post', 'themify' ),
			'wc' => __( 'WooCommerce', 'themify' ),
			'ptb' => __( 'Themify Post Type Builder', 'themify' ),
			'advanced' => __( 'Advanced', 'themify' ),
		);
		$items = $this->get();
		$items_list['empty'] = array( 'options' => array( '' => '' ) );
		foreach ( $items as $id => $class ) {
			$cat_id = $class->get_category();
			if ( ! isset( $items_list[ $cat_id ] ) ) {
			    $items_list[ $cat_id ] = array(
					'label' => $categories[ $cat_id ],
					'options' => array()
			    );
			}
			$items_list[ $cat_id ]['options'][ $id ] = $class->get_label();

			if ( $options = $class->get_options() ) {
				$items_settings[ $id ] = array(
					'type' => 'group',
					'options' =>  $options,
					'wrap_class' => 'field_' . $id,
				);
			}
		}
		$items=$categories=null;
		$items_settings['general_text'] = array(
			'type' => 'group',
			'options' => array(
				array(
					'label' => __( 'Text Before', 'themify' ),
					'id' => 'text_before',
					'type' => 'text'
				),
				array(
					'label' => __( 'Text After', 'themify' ),
					'id' => 'text_after',
					'type' => 'text'
				),
			),
			'wrap_class' => 'field_general_text field_general_textarea field_general_wp_editor'
		);
		$options = array(
			array(
				'id' => 'item',
				'type' => 'select',
				'options' => $items_list,
				'control' => false,
				'optgroup' => true
			),
			array(
				'type' => 'group',
				'options' => $items_settings,
				'wrap_class' => 'field_settings'
			),
		);
		die( json_encode( $options ) );
	}
}
Tbp_Dynamic_Content::get_instance();

class Tbp_Dynamic_Item {

	/**
	 * Returns true if this item is available.
	 *
	 * @return bool
	 */
	public function is_available() {
		return true;
	}
	/**
	 * Returns an array of Builder field types this item applies to.
	 *
	 * @return array
	 */
	public function get_type() {
		return array();
	}

	/**
	 * Returns the category this item belongs to
	 *
	 * @return string
	 */
	public function get_category() {
		return '';
	}

	public function get_label() {
		return '';
	}

	public function get_value( $args = array() ) {
		return null;
	}

	public function get_options() {
		return array();
	}
}