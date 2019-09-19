<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Site Logo
 * 
 * Access original fields: $args['mod_settings']
 * @author Themify
 */
if (TFCache::start_cache($args['mod_name'], self::$post_id, array('ID' => $args['module_ID']))):
	$fields_default = array(
		'display'          	=> 'text',
		'url_image' 		=> '',
		'width_image' 		=> 100,
		'height_image' 		=> 100,
		'link'             	=> 'siteurl',
		'custom_url'       	=> '',
		'html_tag'         	=> '',
		'css'      => '',
		'animation_effect'  => '',
);
$fields_args = wp_parse_args($args['mod_settings'], $fields_default);
unset($args['mod_settings']);
$container_class = apply_filters('themify_builder_module_classes', array(
    'module',
    'module-' . $args['mod_name'],
    'module-' . $args['module_ID'],
    $fields_args['css'],
    self::parse_animation_effect($fields_args['animation_effect'], $fields_args)
    ), $args['mod_name'], $args['module_ID'], $fields_args );
    if(!empty($args['element_id'])){
	$container_class[] = 'tb_'.$args['element_id'];
    }
$container_props = apply_filters('themify_builder_module_container_props', array(
    'class' =>  implode(' ', $container_class),
        ), $fields_args, $args['mod_name'], $args['module_ID']);
$args=null;
if ( 'image' === $fields_args['display'] && '' !== $fields_args['url_image'] ) {
    if (Themify_Builder_Model::is_img_php_disabled()) {
	// get image preset
	global $_wp_additional_image_sizes;
	$width_image = $fields_args['width_image'] !== '' ? $fields_args['width_image'] : get_option($preset . '_size_w');
	$height_image = $fields_args['height_image'] !== '' ? $fields_args['height_image'] : get_option($preset . '_size_h');
	$upload_dir = wp_upload_dir();
	$base_url = $upload_dir['baseurl'];
	$attachment_id = themify_get_attachment_id_from_url($fields_args['url_image'], $base_url);
	$class = $attachment_id ? 'wp-image-' . $attachment_id : '';
	$image = '<img src="' . esc_url($fields_args['url_image']) . '" alt="" width="' . $fields_args['width_image'] . '" height="' . $fields_args['height_image'] . '" class="' . $class . '">';
	if (!empty($attachment_id)) {
	    $image = wp_get_attachment_image($attachment_id, $preset);
	}
    } else {
	$image = themify_get_image('src=' . esc_url($fields_args['url_image']) . '&w=' . $fields_args['width_image'] . '&h=' . $fields_args['height_image'] . '&alt=&ignore=true');
    }
    $image = apply_filters('themify_image_make_responsive_image', $image);
} else {
    $image = get_bloginfo( 'name' );
}

$url = 'siteurl' === $fields_args['link']?site_url():( 'custom' === $fields_args['link'] && '' !== $fields_args['custom_url']?esc_url( $fields_args['custom_url'] ):false);
?>
<!-- Site Logo module -->
<div <?php echo self::get_element_attributes(self::sticky_element_props($container_props,$fields_args)); ?>>
    <?php $container_props=$container_class=null; ?>
    <?php if(!empty($fields_args['html_tag'])): ?>
    <<?php echo $fields_args['html_tag']?>>
    <?php endif; ?>
    
	<?php if ($url!==false):?>
	    <a href="<?php echo $url?>">
	<?php endif;?>

	    <?php echo $image;?>
	<?php if ($url!==false):?>
	    </a>
	<?php endif;?>
	<?php if(!empty($fields_args['html_tag'])): ?>
    </<?php echo $fields_args['html_tag']?>>
    <?php endif; ?>
</div>
<!-- /Site Logo module -->
<?php endif; ?>
<?php TFCache::end_cache(); ?>
