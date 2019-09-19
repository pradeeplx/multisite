<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Product Description
 * 
 * Access original fields: $args['mod_settings']
 * @author Themify
 */
if (themify_is_woocommerce_active() && TFCache::start_cache($args['mod_name'], self::$post_id, array('ID' => $args['module_ID']))):
$fields_default = array(
    'description' => 'short',
    'css' => '',
    'animation_effect' => ''
);
$fields_args = wp_parse_args($args['mod_settings'], $fields_default);
unset($args['mod_settings']);
$animation_effect = self::parse_animation_effect($fields_args['animation_effect'], $fields_args);
$container_class = apply_filters( 'themify_builder_module_classes', array(
    'module',
    'module-' . $args['mod_name'],
    'module-' . $args['module_ID'],
    $fields_args['css'],
    $animation_effect
), $args['mod_name'], $args['module_ID'], $fields_args );

if ( ! empty( $args['element_id'] ) ) {
    $container_class[] = 'tb_' . $args['element_id'];
}
$container_props = apply_filters( 'themify_builder_module_container_props', array(
    'class' => implode( ' ', $container_class ),
), $fields_args, $args['mod_name'], $args['module_ID'] );
$mod_name=$args['mod_name'];
$args=null;
?>
<!-- Product Description module -->
<div <?php echo self::get_element_attributes(self::sticky_element_props($container_props,$fields_args)); ?>>
    <?php
    $container_props=$container_class=null;
    $the_query = Tbp_Utils::get_wc_actual_query();
    if ($the_query===null || $the_query->have_posts() ){
	if($the_query!==null){
	    $the_query->the_post();
	}
        self::retrieve_template('wc/description.php', array('description'=>$fields_args['description'],'mod_name'=>$mod_name));
	if($the_query!==null){
	    wp_reset_postdata();
	}
    } ?>
</div>
<!-- /Product Description module -->
<?php endif; ?>
<?php TFCache::end_cache(); ?>