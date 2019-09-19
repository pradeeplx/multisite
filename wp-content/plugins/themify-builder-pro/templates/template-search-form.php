<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Search Form
 * 
 * Access original fields: $args['mod_settings']
 * @author Themify
 */
if (TFCache::start_cache($args['mod_name'], self::$post_id, array('ID' => $args['module_ID']))):
$fields_default = array(
    'placeholder' => '',
    'button' => 'yes',
    'icon' => 'icon',
    'button_t' => '',
    'css' => '',
    'animation_effect' => '',
    'post_type' => 'any'
);
$fields_args = wp_parse_args($args['mod_settings'], $fields_default);
unset($args['mod_settings']);
$container_class = apply_filters( 'themify_builder_module_classes', array(
    'module',
    'module-' . $args['mod_name'],
    'module-' . $args['module_ID'],
    $fields_args['css'],
    self::parse_animation_effect($fields_args['animation_effect'], $fields_args)
), $args['mod_name'], $args['module_ID'], $fields_args );
    if(!empty($args['element_id'])){
	$container_class[] = 'tb_'.$args['element_id'];
    }
$container_props = apply_filters( 'themify_builder_module_container_props', array(
    'class' => implode( ' ', $container_class ),
), $fields_args, $args['mod_name'], $args['module_ID'] );
$args=null;
?>
<!-- Search Form module -->
<div <?php echo self::get_element_attributes(self::sticky_element_props($container_props,$fields_args)); ?>>
    <?php $container_props=$container_class=null; 
	do_action( 'pre_get_search_form' );
    ?>
    <form role="search" method="get" class="searchform" action="<?php echo  esc_url( home_url( '/' ) ); ?>">
	<input type="text" name="s" title="<?php esc_attr_e( $fields_args['placeholder'] ); ?>" placeholder="<?php esc_attr_e( $fields_args['placeholder'] ); ?>" value="<?php echo get_search_query(); ?>" />
	<?php if(!empty($fields_args['post_type']) && 'any' !== $fields_args['post_type']): ?>
        <input type="hidden" name="post_type" value="<?php echo $fields_args['post_type']; ?>" />
    <?php endif; ?>
	    <?php if ( $fields_args['button'] === 'yes' ): ?>
		<button type="submit" class="module-buttons">
		<?php echo $fields_args['icon'] === 'text' && $fields_args['button_t'] !== '' ? esc_attr( $fields_args['button_t'] ) : '<span class="tbp_icon_search"></span>'; ?>
	    </button>
	<?php endif; ?>
    </form>
</div>
<!-- /Search Form module -->
<?php endif; ?>
<?php TFCache::end_cache(); ?>
