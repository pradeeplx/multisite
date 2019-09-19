<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Module Name: Product Image
 * Description: 
 */

class TB_Product_Image_Module extends Themify_Builder_Component_Module {

    function __construct() {
		parent::__construct(array(
		    'name' => __('Product Image', 'themify'),
		    'slug' => 'product-image',
			'category' => array('product_single')
		));
    }

    public function get_options() {
	    return array(
		    array(
			    'id' => 'image_w',
			    'type' => 'number',
			    'control'=>array(
				'event'=>'change'
			    ),
			    'label' => __('Image Width', 'themify')
		    ),
		    array(
			    'id' => 'auto_fullwidth',
			    'type' => 'checkbox',
			    'label' => '',
			    'options' => array(array('name' => '1', 'value' => __('Auto fullwidth image', 'themify'))),
			    'wrap_class' => 'auto_fullwidth'
		    ),
		    array(
			    'id' => 'image_h',
			    'type' => 'number',
			    'control'=>array(
				'event'=>'change'
			    ),
			    'label' => __('Image Height', 'themify')
		    ),
		    array(
			    'id' => 'appearance_image',
			    'type' => 'checkbox',
			    'label' => __('Appearance', 'themify'),
			    'img_appearance'=>true
		    ),
		    array(
			    'id'      => 'sale_b',
			    'type'    => 'toggle_switch',
			    'label' => __( 'Sale Badge', 'themify' ),
			    'options'   => array(
				    'on'  => array( 'name' => 'yes', 'value' => 's' ),
				    'off' => array( 'name' => 'no', 'value' => 'hi' ),
			    ),
			    'binding' => array(
				    'checked' => array(
					    'show' => array( 'badge_pos' )
				    ),
				    'not_checked' => array(
					    'hide' => array( 'badge_pos' )
				    )
			    )
		    ),
		    array(
			    'label' => '',
			    'after' => __( 'Badge Position', 'themify' ),
			    'id' => 'badge_pos',
			    'type' => 'select',
			    'options' => array(
				    'left' => __( 'Left', 'themify' ),
				    'right'  => __( 'Right', 'themify' )
			    )
		    ),
		    array(
			    'type'    => 'fallback'
		    ),
		    array('type' => 'tbp_custom_css')
	    );
	}

	public function get_styling() {
		$general = array(
			// Background
			self::get_expand('bg', array(
				self::get_tab(array(
					'n' => array(
					'options' => array(
						self::get_image('.module img', 'b_i','bg_c','b_r','b_p')
					)
					),
					'h' => array(
					'options' => array(
						self::get_image('.module img', 'b_i','bg_c','b_r','b_p', 'h')
					)
					)
				))
			)),
			// Padding
			self::get_expand('p', array(
				self::get_tab(array(
					'n' => array(
					'options' => array(
						self::get_padding('.module img', 'p')
					)
					),
					'h' => array(
					'options' => array(
						self::get_padding('.module img', 'p', 'h')
					)
					)
				))
			)),
			// Margin
			self::get_expand('m', array(
				self::get_tab(array(
					'n' => array(
					'options' => array(
						self::get_margin('', 'm')
					)
					),
					'h' => array(
					'options' => array(
						self::get_margin('', 'm', 'h')
					)
					)
				))
			)),
			// Border
			self::get_expand('b', array(
				self::get_tab(array(
					'n' => array(
					'options' => array(
						self::get_border(' img', 'b')
					)
					),
					'h' => array(
					'options' => array(
						self::get_border(' img', 'b', 'h')
					)
					)
				))
			)),
			// Filter
			self::get_expand('f_l',
				array(
					self::get_tab(array(
						'n' => array(
							'options' => count($a = self::get_blend(' img','fl'))>2 ? array($a) : $a
						),
						'h' => array(
							'options' => count($a = self::get_blend(' img','fl_h','h'))>2 ? array($a + array('ishover'=>true)) : $a
						)
					))
				)
			),
			// Width
			self::get_expand('w', array(
				self::get_tab(array(
					'n' => array(
						'options' => array(
							self::get_width('', 'w')
						)
					),
					'h' => array(
						'options' => array(
							self::get_width('', 'w', 'h')
						)
					)
				))
			)),
			// Rounded Corners
			self::get_expand('r_c', array(
				self::get_tab(array(
					'n' => array(
						'options' => array(
							self::get_border_radius(' img', 'r_c')
						)
					),
					'h' => array(
						'options' => array(
							self::get_border_radius(' img', 'r_c', 'h')
						)
					)
				))
			)),
			// Shadow
			self::get_expand('sh', array(
				self::get_tab(array(
					'n' => array(
						'options' => array(
							self::get_box_shadow('.module .woocommerce-product-gallery__wrapper', 'sh')
						)
					),
					'h' => array(
						'options' => array(
							self::get_box_shadow('.module .woocommerce-product-gallery__wrapper', 'sh', 'h')
						)
					)
				))
			)),
			// Position
			self::get_expand('po', array( self::get_css_position()))
		);

		$sale_badge = array(
			// Background
			self::get_expand('bg', array(
			self::get_tab(array(
				'n' => array(
				'options' => array(
					self::get_color('.module .onsale', 'b_c_s_b', 'bg_c', 'background-color')
				)
				),
				'h' => array(
				'options' => array(
					self::get_color('.module .onsale', 'b_c_s_b', 'bg_c', 'background-color', 'h')
				)
				)
			))
			)),
			// Font
			self::get_expand('f', array(
				self::get_tab(array(
					'n' => array(
					'options' => array(
						self::get_color('.module .onsale', 'f_c_s_b'),
						self::get_font_size('.module .onsale', 'f_s_s_b', ''),
					)
					),
					'h' => array(
					'options' => array(
						self::get_color('.module .onsale', 'f_c_s_b', 'h'),
						self::get_font_size('.module .onsale', 'f_s_s_b', '', 'h'),
					)
					)
				))
			)),
			// Padding
			self::get_expand('p', array(
			self::get_tab(array(
				'n' => array(
				'options' => array(
					self::get_padding('.module .onsale', 'p_s_b')
				)
				),
				'h' => array(
				'options' => array(
					self::get_padding('.module .onsale', 'p_s_b', 'h')
				)
				)
			))
			)),
			// Margin
			self::get_expand('m', array(
			self::get_tab(array(
				'n' => array(
				'options' => array(
					self::get_margin('.module .onsale', 'm_s_b')
				)
				),
				'h' => array(
				'options' => array(
					self::get_margin('.module .onsale', 'm_s_b', 'h')
				)
				)
			))
			)),
			// Border
			self::get_expand('b', array(
			self::get_tab(array(
				'n' => array(
				'options' => array(
					self::get_border('.module .onsale', 'b_s_b')
				)
				),
				'h' => array(
				'options' => array(
					self::get_border('.module .onsale', 'b_s_b', 'h')
				)
				)
			))
			)),
			// Rounded Corners
			self::get_expand('r_c', array(
				self::get_tab(array(
					'n' => array(
						'options' => array(
							self::get_border_radius('.module .onsale', 'r_c_s_b')
						)
					),
					'h' => array(
						'options' => array(
							self::get_border_radius('.module .onsale', 'r_c_s_b', 'h')
						)
					)
				))
			)),
			// Shadow
			self::get_expand('sh', array(
				self::get_tab(array(
					'n' => array(
						'options' => array(
							self::get_box_shadow('.module .onsale', 'sh_s_b')
						)
					),
					'h' => array(
						'options' => array(
							self::get_box_shadow('.module .onsale', 'sh_s_b', 'h')
						)
					)
				))
			))

		);
		
		return array(
			'type' => 'tabs',
			'options' => array(
				'g' => array(
					'options' => $general
				),
				's' => array(
					'label' => __('Sale Badge', 'themify'),
					'options' => $sale_badge
				),
			)
		);
	}

	public function get_default_settings() {
		return array(
			'lightbox_w_unit' => '%',
			'lightbox_h_unit' => '%',
			'sale_b' => 'yes'
		);
	}

	public function get_visual_type() {
		return 'ajax';
    }

    public function get_category() {
		return array( 'product' );
	}

}

if ( themify_is_woocommerce_active()) {
	Themify_Builder_Model::register_module('TB_Product_Image_Module');
}
