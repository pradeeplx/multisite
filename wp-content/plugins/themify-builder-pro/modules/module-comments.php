<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Module Name: Comments
 * Description: 
 */

class TB_Comments_Module extends Themify_Builder_Component_Module {

    function __construct() {
		parent::__construct(array(
		    'name' => __('Comments', 'themify'),
		    'slug' => 'comments',
			'category' => array('single')
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
						self::get_image('', 'b_i','bg_c','b_r','b_p')
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
						self::get_font_family(array('','.comment-title','.comment-reply-title'), 'f_f'),
						self::get_color(array('','.comment-title','.comment-reply-title'), 'f_c'),
						self::get_font_size(array('','.comment-title','.comment-reply-title'), 'f_s'),
						self::get_line_height(array('','.comment-title','.comment-reply-title'), 'l_h'),
						self::get_letter_spacing(array('','.comment-title','.comment-reply-title'), 'l_s'),
						self::get_text_align(array('','.comment-title','.comment-reply-title'), 't_a'),
						self::get_text_transform(array('','.comment-title','.comment-reply-title'), 't_t'),
						self::get_font_style(array('','.comment-title','.comment-reply-title'), 'f_st', 'f_w'),
						self::get_text_decoration(array('','.comment-title','.comment-reply-title'), 't_d_r'),
						self::get_text_shadow(array('','.comment-title','.comment-reply-title'),'t_sh'),
					)
					),
					'h' => array(
					'options' => array(
						self::get_font_family(array('','.comment-title','.comment-reply-title'), 'f_f_h'),
						self::get_color(array('','.comment-title','.comment-reply-title'), 'f_c', 'h'),
						self::get_font_size(array('','.comment-title','.comment-reply-title'), 'f_s', '', 'h'),
						self::get_line_height(array('','.comment-title','.comment-reply-title'), 'l_h', 'h'),
						self::get_letter_spacing(array('','.comment-title','.comment-reply-title'), 'l_s', 'h'),
						self::get_text_align(array('','.comment-title','.comment-reply-title'), 't_a', 'h'),
						self::get_text_transform(array('','.comment-title','.comment-reply-title'), 't_t', 'h'),
						self::get_font_style(array('','.comment-title','.comment-reply-title'), 'f_st', 'f_w', 'h'),
						self::get_text_decoration(array('','.comment-title','.comment-reply-title'), 't_d_r', 'h'),
						self::get_text_shadow(array('','.comment-title','.comment-reply-title'),'t_sh','h'),
					)
					)
				))
			)),
			// Paragraph
			self::get_expand(__('Paragraph', 'themify'), array(
				self::get_heading_margin_multi_field('', 'p', 'top'),
				self::get_heading_margin_multi_field('', 'p', 'bottom')
			)),
			// Link
			self::get_expand('l', array(
				self::get_tab(array(
					'n' => array(
					'options' => array(
						self::get_color(' a', 'l_c'),
						self::get_text_decoration(' a', 't_d')
					)
					),
					'h' => array(
					'options' => array(
						self::get_color(' a', 'l_c',null, null, 'hover'),
						self::get_text_decoration(' a', 't_d', 'h')
					)
					)
				))
			)),
			// Padding
			self::get_expand('p', array(
				self::get_tab(array(
					'n' => array(
					'options' => array(
						self::get_padding('', 'p')
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
						self::get_border('', 'b')
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
			// Height & Min Height
			self::get_expand('ht', array(
				self::get_height(),
				self::get_min_height(),
			)),
			// Rounded Corners
			self::get_expand('r_c', array(
				self::get_tab(array(
					'n' => array(
						'options' => array(
							self::get_border_radius('', 'r_c')
						)
					),
					'h' => array(
						'options' => array(
							self::get_border_radius('', 'r_c', 'h')
						)
					)
				))
			)),
			// Shadow
			self::get_expand('sh', array(
				self::get_tab(array(
					'n' => array(
						'options' => array(
							self::get_box_shadow('', 'sh')
						)
					),
					'h' => array(
						'options' => array(
							self::get_box_shadow('', 'sh', 'h')
						)
					)
				))
			)),
		);
		
		$labels = array(
			// Font
			self::get_seperator('f'),
			self::get_tab(array(
			    'n' => array(
				'options' => array(
				    self::get_font_family(' .comment-form label','f_f_l'),
				    self::get_color(' .comment-form label', 'f_c_l'),
				    self::get_font_size(' .comment-form label','f_s_l'),
					self::get_text_shadow(' .comment-form label','t_sh_l'),
				)
			    ),
			    'h' => array(
				'options' => array(
				    self::get_font_family(' .comment-form label','f_f_l','h'),
				    self::get_color(' .comment-form label', 'f_c_l',null,null,'h'),
				    self::get_font_size(' .comment-form label','f_s_l','','h'),
					self::get_text_shadow(' .comment-form label','t_sh_l','h'),
				)
			    )
			))
		);
		
		$inputs = array(
		    self::get_expand('bg', array(
			   self::get_tab(array(
			       'n' => array(
				   'options' => array(
				       self::get_color(array( ' input[type="text"]', ' input[type="email"]', ' input[type="url"]', ' textarea' ), 'b_c_i', 'bg_c', 'background-color'),
				   )
			       ),
			       'h' => array(
				   'options' => array(
				         self::get_color(array( ' input[type="text"]', ' input[type="email"]', ' input[type="url"]', ' textarea' ), 'b_c_i', 'bg_c', 'background-color','h'),
				   )
			       )
			   ))
		    )),
		    self::get_expand('f', array(
			self::get_tab(array(
			    'n' => array(
				'options' => array(
				    self::get_font_family(array( ' input[type="text"]', ' input[type="email"]', ' input[type="url"]', ' textarea' ),'f_f_i'),
				    self::get_color(array( ' input[type="text"]', ' input[type="email"]', ' input[type="url"]', ' textarea' ), 'f_c_i'),
				    self::get_font_size(array( ' input[type="text"]', ' input[type="email"]', ' input[type="url"]', ' textarea' ),'f_s_i'),
					self::get_text_shadow(array( ' input[type="text"]', ' input[type="email"]', ' input[type="url"]', ' textarea' ),'t_sh_i'),
				)
			    ),
			    'h' => array(
				'options' => array(
				    self::get_font_family(array( ' input[type="text"]', ' input[type="email"]', ' input[type="url"]', ' textarea' ),'f_f_i','h'),
				    self::get_color(array( ' input[type="text"]', ' input[type="email"]', ' input[type="url"]', ' textarea' ), 'f_c_i',null,null,'h'),
				    self::get_font_size(array( ' input[type="text"]', ' input[type="email"]', ' input[type="url"]', ' textarea' ),'f_s_i','','h'),
					self::get_text_shadow(array( ' input[type="text"]', ' input[type="email"]', ' input[type="url"]', ' textarea' ),'t_sh_i','h'),
				)
			    )
			))
		    )),
		    // Border
		    self::get_expand('b', array(
			self::get_tab(array(
			    'n' => array(
				'options' => array(
				    self::get_border(array( ' input[type="text"]', ' input[type="email"]', ' input[type="url"]', ' textarea' ),'in_b')
				)
			    ),
			    'h' => array(
				'options' => array(
				    self::get_border(array( ' input[type="text"]', ' input[type="email"]', ' input[type="url"]', ' textarea' ),'in_b','h')
				)
			    )
			))
		    )),
			// Padding
			self::get_expand('p', array(
			self::get_tab(array(
				'n' => array(
				'options' => array(
					self::get_padding(array( ' input[type="text"]', ' input[type="email"]', ' input[type="url"]', ' textarea' ), 'in_p')
				)
				),
				'h' => array(
				'options' => array(
					self::get_padding(array( ' input[type="text"]', ' input[type="email"]', ' input[type="url"]', ' textarea' ), 'in_p', 'h')
				)
				)
			))
			)),
			// Rounded Corners
			self::get_expand('r_c', array(
				self::get_tab(array(
					'n' => array(
						'options' => array(
							self::get_border_radius(array( ' input[type="text"]', ' input[type="email"]', ' input[type="url"]', ' textarea' ), 'in_r_c')
						)
					),
					'h' => array(
						'options' => array(
							self::get_border_radius(array( ' input[type="text"]', ' input[type="email"]', ' input[type="url"]', ' textarea' ), 'in_r_c', 'h')
						)
					)
				))
			)),
			// Shadow
			self::get_expand('sh', array(
				self::get_tab(array(
					'n' => array(
						'options' => array(
							self::get_box_shadow(array( ' input[type="text"]', ' input[type="email"]', ' input[type="url"]', ' textarea' ), 'in_b_sh')
						)
					),
					'h' => array(
						'options' => array(
							self::get_box_shadow(array( ' input[type="text"]', ' input[type="email"]', ' input[type="url"]', ' textarea' ), 'in_b_sh', 'h')
						)
					)
				))
			))
		);
		
		$send_button = array(
		    
		    self::get_expand('bg', array(
			   self::get_tab(array(
			       'n' => array(
				   'options' => array(
				       self::get_color(' input[type="submit"]', 'b_c_s', 'bg_c', 'background-color')
				   )
			       ),
			       'h' => array(
				   'options' => array(
				        self::get_color(' input[type="submit"]', 'b_c_s', 'bg_c', 'background-color','h')
				   )
			       )
			   ))
		    )),
		    self::get_expand('f', array(
			self::get_tab(array(
			    'n' => array(
				'options' => array(
				    self::get_font_family(' input[type="submit"]', 'f_f_s'),
				    self::get_color(' input[type="submit"]', 'f_c_s'),
				    self::get_font_size(' input[type="submit"]','f_s_s'),
					self::get_text_shadow(' input[type="submit"]' ,'t_sh_b'),
				)
			    ),
			    'h' => array(
				'options' => array(
				    self::get_font_family(' input[type="submit"]', 'f_f_s','h'),
				    self::get_color(' input[type="submit"]', 'f_c_s',null,null,'h'),
				    self::get_font_size(' input[type="submit"]', 'f_s_s','','h'),
					self::get_text_shadow(' input[type="submit"]', 't_sh_b','h'),
				)
			    )
			))
		    )),
		    // Border
		    self::get_expand('b', array(
			self::get_tab(array(
			    'n' => array(
				'options' => array(
				    self::get_border(' input[type="submit"]','b_s')
				)
			    ),
			    'h' => array(
				'options' => array(
				    self::get_border(' input[type="submit"]','b_s','h')
				)
			    )
			))
		    )),
			// Padding
			self::get_expand('p', array(
			self::get_tab(array(
				'n' => array(
				'options' => array(
					self::get_padding(' input[type="submit"]', 'p_sd')
				)
				),
				'h' => array(
				'options' => array(
					self::get_padding(' input[type="submit"]', 'p_sd', 'h')
				)
				)
			))
			)),
			// Rounded Corners
			self::get_expand('r_c', array(
				self::get_tab(array(
					'n' => array(
						'options' => array(
							self::get_border_radius(' input[type="submit"]', 'r_c_sd')
						)
					),
					'h' => array(
						'options' => array(
							self::get_border_radius(' input[type="submit"]', 'r_c_sd', 'h')
						)
					)
				))
			)),
			// Shadow
			self::get_expand('sh', array(
				self::get_tab(array(
					'n' => array(
						'options' => array(
							self::get_box_shadow(' input[type="submit"]', 's_sd')
						)
					),
					'h' => array(
						'options' => array(
							self::get_box_shadow(' input[type="submit"]', 's_sd', 'h')
						)
					)
				))
			))
		);

		$comment_title = array(
		    
		    self::get_expand('bg', array(
			   self::get_tab(array(
			       'n' => array(
				   'options' => array(
				       self::get_color(array(' .comment-title', ' .comment-reply-title'), 'b_c_ct', 'bg_c', 'background-color')
				   )
			       ),
			       'h' => array(
				   'options' => array(
				        self::get_color(array(' .comment-title', ' .comment-reply-title'), 'b_c_ct', 'bg_c', 'background-color','h')
				   )
			       )
			   ))
		    )),
		    self::get_expand('f', array(
				self::get_tab(array(
					'n' => array(
					'options' => array(
						self::get_font_family(array(' .comment-title', ' .comment-reply-title'), 'f_f_ct'),
						self::get_color_type(array(' .comment-title', ' .comment-reply-title'),'', 'f_c_t_ct', 'f_c_ct', 'f_g_c_ct'),
						self::get_font_size(array(' .comment-title', ' .comment-reply-title'), 'f_s_ct', ''),
						self::get_line_height(array(' .comment-title', ' .comment-reply-title'), 'l_h_ct'),
						self::get_letter_spacing(array(' .comment-title', ' .comment-reply-title'), 'l_s_ct'),
						self::get_text_align(array(' .comment-title', ' .comment-reply-title'), 't_a_ct'),
						self::get_text_transform(array(' .comment-title', ' .comment-reply-title'), 't_t_ct'),
						self::get_font_style(array(' .comment-title', ' .comment-reply-title'), 'f_st_ct', 'f_w_ct'),
						self::get_text_decoration(array(' .comment-title', ' .comment-reply-title'), 't_d_r_ct'),
						self::get_text_shadow(array(' .comment-title', ' .comment-reply-title'),'t_sh_ct'),
					)
					),
					'h' => array(
					'options' => array(
						self::get_font_family(array(' .comment-title', ' .comment-reply-title'), 'f_f_ct_h'),
						self::get_color_type(array(' .comment-title:hover', ' .comment-reply-title:hover'),'', 'f_c_t_ct_h', 'f_c_ct_h', 'f_g_c_ct_h', 'h'),
						self::get_font_size(array(' .comment-title', ' .comment-reply-title'), 'f_s_ct', '', 'h'),
						self::get_line_height(array(' .comment-title', ' .comment-reply-title'), 'l_h_ct', 'h'),
						self::get_letter_spacing(array(' .comment-title', ' .comment-reply-title'), 'l_s_ct', 'h'),
						self::get_text_align(array(' .comment-title', ' .comment-reply-title'), 't_a_ct', 'h'),
						self::get_text_transform(array(' .comment-title', ' .comment-reply-title'), 't_t_ct', 'h'),
						self::get_font_style(array(' .comment-title', ' .comment-reply-title'), 'f_st_ct', 'f_w_ct', 'h'),
						self::get_text_decoration(array(' .comment-title', ' .comment-reply-title'), 't_d_r_ct', 'h'),
						self::get_text_shadow(array(' .comment-title', ' .comment-reply-title'),'t_sh_ct', 'h'),
					)
					)
				))
		    )),
		    // Border
		    self::get_expand('b', array(
			self::get_tab(array(
			    'n' => array(
				'options' => array(
				    self::get_border(array(' .comment-title', ' .comment-reply-title'), 'b_ct')
				)
			    ),
			    'h' => array(
				'options' => array(
				    self::get_border(array(' .comment-title', ' .comment-reply-title'), 'b_ct', 'h')
				)
			    )
			))
		    )),
			// Padding
			self::get_expand('p', array(
			self::get_tab(array(
				'n' => array(
				'options' => array(
					self::get_padding(array(' .comment-title', ' .comment-reply-title'), 'p_ct')
				)
				),
				'h' => array(
				'options' => array(
					self::get_padding(array(' .comment-title', ' .comment-reply-title'), 'p_ct', 'h')
				)
				)
			))
			)),
			// Rounded Corners
			self::get_expand('r_c', array(
				self::get_tab(array(
					'n' => array(
						'options' => array(
							self::get_border_radius(array(' .comment-title', ' .comment-reply-title'), 'r_c_ct')
						)
					),
					'h' => array(
						'options' => array(
							self::get_border_radius(array(' .comment-title', ' .comment-reply-title'), 'r_c_ct', 'h')
						)
					)
				))
			)),
			// Shadow
			self::get_expand('sh', array(
				self::get_tab(array(
					'n' => array(
						'options' => array(
							self::get_box_shadow(array(' .comment-title', ' .comment-reply-title'), 's_ct')
						)
					),
					'h' => array(
						'options' => array(
							self::get_box_shadow(array(' .comment-title', ' .comment-reply-title'), 's_ct', 'h')
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
				'c_t' => array(
					'label' => __('Title', 'themify'),
					'options' => $comment_title
				),
				'l' => array(
					'label' => __('Labels', 'themify'),
					'options' => $labels
				),
				'i' => array(
					'label' => __('Inputs', 'themify'),
					'options' => $inputs
				),
				's_b' => array(
					'label' => __('Button', 'themify'),
					'options' => $send_button
				)
			)
		);
	}

	public function get_visual_type() {
		return 'ajax';
    }

    public function get_category() {
		return 'single';
	}

}

Themify_Builder_Model::register_module('TB_Comments_Module');
