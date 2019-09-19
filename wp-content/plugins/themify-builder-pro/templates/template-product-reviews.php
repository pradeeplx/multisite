<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Product Reviews
 * 
 * Access original fields: $args['mod_settings']
 * @author Themify
 */
if (themify_is_woocommerce_active() && TFCache::start_cache($args['mod_name'], self::$post_id, array('ID' => $args['module_ID']))):
    $fields_default = array(
	'description' => 'yes',
	'additionaly'=>'yes',
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
	$elementId=$args['element_id'];
	$container_class[] = 'tb_'.$elementId;
    }
    else{
	$elementId=$args['module_ID'];
    }
    $container_props = apply_filters('themify_builder_module_container_props', array(
	'class' => implode(' ', $container_class),
	    ), $fields_args, $args['mod_name'], $args['module_ID']);
    $args=null;
    ?>
    <!-- Product Reviews module -->
    <div <?php echo self::get_element_attributes(self::sticky_element_props($container_props, $fields_args)); ?>>
	<?php
	$container_props=$container_class=null; 
	global $woocommerce;
	$the_query = Tbp_Utils::get_wc_actual_query();
	if ($the_query===null ||$the_query->have_posts()) {
	    if($the_query!==null){
		$the_query->the_post();
	    }
	    TB_Product_Reviews_Module::$elId=$elementId;
	    TB_Product_Reviews_Module::$hasDescription=$fields_args['description']==='yes';
	    TB_Product_Reviews_Module::$hasAdditionaly=$fields_args['additionaly']==='yes';
	    if(Themify_Builder::$frontedit_active===true){
		global $withcomments;
		$withcomments = true; 
	    }
	    add_filter( 'woocommerce_product_tabs', array('TB_Product_Reviews_Module','getTabs') );
	    ?>
	    <div class="product">
		<?php woocommerce_output_product_data_tabs();?>
	    </div>
	    <?php
	    remove_filter( 'woocommerce_product_tabs', array('TB_Product_Reviews_Module','getTabs') );
	    if($the_query!==null){
			wp_reset_postdata();
	    }
	}
	?>
    </div>
    <!-- /Product Reviews module -->
<?php endif; ?>
<?php TFCache::end_cache(); ?>