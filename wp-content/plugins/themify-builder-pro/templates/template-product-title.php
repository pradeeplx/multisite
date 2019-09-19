<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Product Title
 * 
 * Access original fields: $args['mod_settings']
 * @author Themify
 */
if (themify_is_woocommerce_active() && TFCache::start_cache($args['mod_name'], self::$post_id, array('ID' => $args['module_ID']))):
    $fields_default = array(
	'link' => 'permalink',
	'open_link' => 'regular',
	'lightbox_w_unit' => '%',
	'lightbox_h_unit' => '%',
	'html_tag' => 'h2',
	'no_follow' => 'no',
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
    <!-- Product Title module -->
    <div <?php echo self::get_element_attributes(self::sticky_element_props($container_props, $fields_args)); ?>>
	<?php 
	$container_props=$container_class=null; 
	$the_query = Tbp_Utils::get_wc_actual_query();
	if ($the_query===null || $the_query->have_posts()) {
	    if($the_query!==null){
		$the_query->the_post();
	    }
	    themify_product_title_start();
	    self::retrieve_template('partials/title.php', $fields_args);
	    themify_product_title_end();
	    if($the_query!==null){
		wp_reset_postdata();
	    }
	}
	?>
    </div>
    <!-- /Product Title module -->
<?php endif; ?>
<?php TFCache::end_cache(); ?>