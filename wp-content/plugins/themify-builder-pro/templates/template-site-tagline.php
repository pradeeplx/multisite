<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Site Tagline
 * 
 * Access original fields: $args['mod_settings']
 * @author Themify
 */
if (TFCache::start_cache($args['mod_name'], self::$post_id, array('ID' => $args['module_ID']))):
    $fields_default = array(
	'link' => '',
	'html_tag' => 'h2',
	'css' => '',
	'animation_effect' => ''
    );
    $fields_args = wp_parse_args($args['mod_settings'], $fields_default);
    unset($args['mod_settings']);
    $container_class = apply_filters('themify_builder_module_classes', array(
	'module',
	'module-' . $args['mod_name'],
	'module-' . $args['module_ID'],
	$fields_args['css'],
	self::parse_animation_effect($fields_args['animation_effect'], $fields_args)
	    ), $args['mod_name'], $args['module_ID'], $fields_args);
    if(!empty($args['element_id'])){
	$container_class[] = 'tb_'.$args['element_id'];
    }
    $container_props = apply_filters('themify_builder_module_container_props', array(
	'class' => implode(' ', $container_class),
	    ), $fields_args, $args['mod_name'], $args['module_ID']);
    $args=null;
    ?>
    <!-- Site Tagline module -->
    <div <?php echo self::get_element_attributes(self::sticky_element_props($container_props, $fields_args)); ?>>
	<?php $container_props=$container_class=null; ?>
	<<?php echo $fields_args['html_tag'] ?> class="tbp_site_tagline_heading">
	    <?php if ('' !== $fields_args['link']): ?>
	        <a href="<?php echo esc_url($fields_args['link']) ?>">
	    <?php endif; ?>
	    <?php echo get_bloginfo('description'); ?>
	    <?php if ('' !== $fields_args['link']): ?>
	        </a>
	    <?php endif; ?>
	</<?php echo $fields_args['html_tag'] ?>>
    </div>
    <!-- /Site Tagline module -->
<?php endif; ?>
<?php TFCache::end_cache(); ?>