<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Module Name: Product Rating
 * Description: 
 */

class TB_Product_Rating_Module extends Themify_Builder_Component_Module {

    function __construct() {
		parent::__construct(array(
		    'name' => __('Product Rating', 'themify'),
		    'slug' => 'product-rating',
		    'category' => array('product_single')
		));
    }

    public function get_options() {
		return array(
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
							self::get_image()
						)
					),
					'h' => array(
						'options' => array(
							self::get_image('', 'b_i','bg_c','b_r','b_p', 'h')
						)
					)
				))
			)),
			// Font
			self::get_expand('f', array(
				self::get_tab(array(
					'n' => array(
					'options' => array(
						self::get_font_family(array(' .woocommerce-product-rating', ' .woocommerce-product-rating a'), 'f_f_g'),
						self::get_color_type(array(' .woocommerce-product-rating', ' .woocommerce-product-rating a'),'', 'f_c_t_g',  'f_c_g', 'f_g_c_g'),
						self::get_font_size(array(' .woocommerce-product-rating', ' .woocommerce-product-rating a'), 'f_s_g', ''),
						self::get_line_height(array(' .woocommerce-product-rating', ' .woocommerce-product-rating a'), 'l_h_g'),
						self::get_letter_spacing(array(' .woocommerce-product-rating', ' .woocommerce-product-rating a'), 'l_s_g'),
						self::get_text_align(array(' .woocommerce-product-rating', ' .woocommerce-product-rating a'), 't_a_g'),
						self::get_text_transform(array(' .woocommerce-product-rating', ' .woocommerce-product-rating a'), 't_t_g'),
						self::get_font_style(array(' .woocommerce-product-rating', ' .woocommerce-product-rating a'), 'f_st_g', 'f_w_g'),
						self::get_text_decoration(array(' .woocommerce-product-rating', ' .woocommerce-product-rating a'), 't_d_r_g'),
						self::get_text_shadow(array(' .woocommerce-product-rating', ' .woocommerce-product-rating a'),'t_sh_g','h'),
					)
					),
					'h' => array(
					'options' => array(
						self::get_font_family(array(' .woocommerce-product-rating', ' .woocommerce-product-rating a'), 'f_f_g_h'),
						self::get_color_type(array(' .woocommerce-product-rating', ' .woocommerce-product-rating a'),'', 'f_c_t_g_h',  'f_c_g_h', 'f_g_c_g_h'),
						self::get_font_size(array(' .woocommerce-product-rating', ' .woocommerce-product-rating a'), 'f_s_g', '', 'h'),
						self::get_line_height(array(' .woocommerce-product-rating', ' .woocommerce-product-rating a'), 'l_h_g', 'h'),
						self::get_letter_spacing(array(' .woocommerce-product-rating', ' .woocommerce-product-rating a'), 'l_s_g', 'h'),
						self::get_text_align(array(' .woocommerce-product-rating', ' .woocommerce-product-rating a'), 't_a_g', 'h'),
						self::get_text_transform(array(' .woocommerce-product-rating', ' .woocommerce-product-rating a'), 't_t_g', 'h'),
						self::get_font_style(array(' .woocommerce-product-rating', ' .woocommerce-product-rating a'), 'f_st_g', 'f_w_g', 'h'),
						self::get_text_decoration(array(' .woocommerce-product-rating', ' .woocommerce-product-rating a'), 't_d_r_g', 'h'),
						self::get_text_shadow(array(' .woocommerce-product-rating', ' .woocommerce-product-rating a'),'t_sh_g','h'),
					)
					)
				))
			)),
			// Padding
			self::get_expand('p', array(
				self::get_tab(array(
					'n' => array(
						'options' => array(
							self::get_padding()
						)
					),
					'h' => array(
						'options' => array(
							self::get_padding('', 'p', 'h')
						)
					)
				))
			)),
			// Margin
			self::get_expand('m', array(
				self::get_tab(array(
					'n' => array(
						'options' => array(
							self::get_margin()
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
							self::get_border()
						)
					),
					'h' => array(
						'options' => array(
							self::get_border('', 'b', 'h')
						)
					)
				))
			)),
			// Filter
			self::get_expand('f_l',
				array(
					self::get_tab(array(
						'n' => array(
							'options' => count($a = self::get_blend())>2 ? array($a) : $a
						),
						'h' => array(
							'options' => count($a = self::get_blend('','bl_m_h','h'))>2 ? array($a + array('ishover'=>true)) : $a
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
								self::get_border_radius()
							)
						),
						'h' => array(
							'options' => array(
								self::get_border_radius('', 'r_c', 'h')
							)
						)
					))
				)
			),
			// Shadow
			self::get_expand('sh', array(
					self::get_tab(array(
						'n' => array(
							'options' => array(
								self::get_box_shadow()
							)
						),
						'h' => array(
							'options' => array(
								self::get_box_shadow('', 'sh', 'h')
							)
						)
					))
				)
			),
			// Position
			self::get_expand('po', array( self::get_css_position()))
		);

		return array(
			'type' => 'tabs',
			'options' => array(
				'g' => array(
					'options' => $general
				)
			)
		);
	}

	public function get_visual_type() {
		return 'ajax';
    }

    public function get_category() {
		return array( 'product' );
	}

}

if ( themify_is_woocommerce_active() ) {
	Themify_Builder_Model::register_module('TB_Product_Rating_Module');
}
