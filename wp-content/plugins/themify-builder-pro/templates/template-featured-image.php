<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Featured Image
 * 
 * Access original fields: $args['mod_settings']
 * @author Themify
 */
if (TFCache::start_cache($args['mod_name'], self::$post_id, array('ID' => $args['module_ID']))):
$fields_default = array(
    'image_w' => '',
    'image_h' => '',
    'auto_fullwidth' => false,
    'appearance_image' => '',
    'link' => 'permalink',
    'custom_link' => '',
    'open_link' => 'regular',
    'lightbox' => '',
    'lightbox_w' => '',
    'lightbox_h' => '',
    'lightbox_w_unit' => '%',
    'lightbox_h_unit' => '%',
    'fallback_s' => 'no',
    'fallback_i' => '',
    'css' => '',
    'animation_effect' => ''
);
if (isset($args['mod_settings']['appearance_image'])) {
    $args['mod_settings']['appearance_image'] = self::get_checkbox_data($args['mod_settings']['appearance_image']);
}
$fields_args = wp_parse_args($args['mod_settings'], $fields_default);
unset($args['mod_settings']);
$container_class = apply_filters('themify_builder_module_classes', array(
    'module',
    'module-image',
    'module-' . $args['mod_name'],
    'module-' . $args['module_ID'],
    $fields_args['css'],
	$fields_args['appearance_image'],
    self::parse_animation_effect($fields_args['animation_effect'], $fields_args)
    ), $args['mod_name'], $args['module_ID'], $fields_args );

if ($fields_args['auto_fullwidth']=='1') {
    $container_class[]=' auto_fullwidth';
} 
if(!empty($args['element_id'])){
    $container_class[] = 'tb_'.$args['element_id'];
}
$container_props = apply_filters('themify_builder_module_container_props', array(
    'class' =>  implode(' ', $container_class),
        ), $fields_args, $args['mod_name'], $args['module_ID']);

	$output = '<div class="image-wrap" itemprop="image">';
	$the_query = Tbp_Utils::get_actual_query();
	if ( $the_query===null ||$the_query->have_posts() ) :
	    if($the_query!==null){
		$the_query->the_post();
	    }
			if ( 'none' !== $fields_args['link'] ) {
	$target = '';
				$link = '';
	$link_css_classes = array( 'tbp_featured_image_link' );
	$link_attr = array();
				if ( 'permalink' === $fields_args['link'] ) {
					$link = get_permalink();
		// Target
		if ( 'regular' === $fields_args['open_link'] ) {
			$target = ' target="_self"';
		} else if ( 'newtab' === $fields_args['open_link'] ) {
			$target = ' target="_blank"';
		}
		// Lightbox
		if ( 'lightbox' === $fields_args['open_link'] ) {
			$link_css_classes[] = 'themify_lightbox';

			if ( '' !== $fields_args['lightbox_w'] || '' !== $fields_args['lightbox_h'] ) {
				$lightbox_settings = array();
				if( '' !== $fields_args['lightbox_w'] ) {
                                $lightbox_settings[] = $fields_args['lightbox_w'] . $fields_args['lightbox_w_unit'];
				}
				if( '' !== $fields_args['lightbox_h'] ) {
                                $lightbox_settings[] = $fields_args['lightbox_h'] . $fields_args['lightbox_h_unit'];
				}
				$link_attr[] = sprintf( 'data-zoom-config="%s"', implode('|', $lightbox_settings ) );
			}
		}
				} else if ( 'custom' === $fields_args['link'] ) {
	    $link = esc_url( $fields_args['custom_link']);
				} else if ( 'media' === $fields_args['link'] ) {
					$link_attr = array();
					$link = get_the_post_thumbnail_url();
					$link_css_classes[] = 'themify_lightbox';
    }
}
$width = '' !== $fields_args['image_w'] ? 'w=' . esc_attr( $fields_args['image_w'] ) . '&' : '';
$height = '' !== $fields_args['image_h'] ? 'h=' . esc_attr( $fields_args['image_h'] ) . '&' : '';

if ( 'yes' === $fields_args['fallback_s'] && '' !== $fields_args['fallback_i'] ) {
	$image = themify_get_image($width . $height . 'src=' . esc_url($fields_args['fallback_i']) . '&alt=&ignore=true');
				if ( 'media' === $fields_args['link'] ) {
				    $link = esc_url( $fields_args['fallback_i'] );
				}
} else {
	if (Themify_Builder_Model::is_img_php_disabled()) {
		// get image preset
		global $_wp_additional_image_sizes;
		$upload_dir = wp_upload_dir();
		$base_url = $upload_dir['baseurl'];
		$attachment_id = themify_get_attachment_id_from_url($fields_args['url_image'], $base_url);
		$class = $attachment_id ? 'wp-image-' . $attachment_id : '';
		$image = '<img itemprop="imageObject" src="' . esc_url($fields_args['url_image']) . '" alt="" width="' . $fields_args['image_w'] . '" height="' . $fields_args['image_h'] . '" class="' . $class . '">';
		if (!empty($attachment_id)) {
			$image = wp_get_attachment_image($attachment_id);
		}
	} else {
		$image = themify_get_image($width . $height . 'alt=&ignore=true');
	}
                if (Themify_Builder::$frontedit_active && $image === '') {
                    $image = '<img itemprop="imageObject" src="' . THEMIFY_BUILDER_URI  . '/img/image-placeholder.jpg'. '">';
                }
}
$image = apply_filters('themify_image_make_responsive_image', $image);
			if ( 'none' !== $fields_args['link'] ) {
				$output .= '<a href="' . $link . '"' . $target . ' class="' . implode( " ", $link_css_classes ) . '" ' . implode( " ", $link_attr ) . '>';
			}
$output .= $image;
			if ( 'none' !== $fields_args['link'] ) {
    $output .= '</a>';
}
			$output .= '</div>';
	if($the_query!==null){
	    wp_reset_postdata();
	}
	endif; ?>
<!-- Featured Image module -->
<div <?php echo self::get_element_attributes(self::sticky_element_props($container_props,$fields_args)); ?>>
       <?php echo $output; ?>
    </div>
<!-- /Featured Image module -->
<?php endif; ?>
<?php TFCache::end_cache(); ?>