<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Cart Icon
 *
 * Access original fields: $args['mod_settings']
 * @author Themify
 */
if (themify_is_woocommerce_active() && TFCache::start_cache($args['mod_name'], self::$post_id, array('ID' => $args['module_ID'])) && !is_null(WC()->cart)):

    $fields_default = array(
	'icon' => 'ti-shopping-cart',
	'style' => 'slide',
	'bubble' => 'off',
	'sub_total' => 'off',
	'alignment' => '',
	'animation_effect' => ''
    );
    $fields_args = wp_parse_args($args['mod_settings'], $fields_default);
    unset($args['mod_settings']);
    $container_class = apply_filters('themify_builder_module_classes', array(
	'module',
	'module-' . $args['mod_name'],
	'module-' . $args['module_ID'],
	'tbp_cart_icon_style_' . $fields_args['style'],
	self::parse_animation_effect($fields_args['animation_effect'], $fields_args)
	    ), $args['mod_name'], $args['module_ID'], $fields_args);
    if(!empty($fields_args['alignment'])){
		$container_class[] = $fields_args['alignment'] . '-align';
    } 
    if(!empty($args['element_id'])){
	$container_class[] = 'tb_'.$args['element_id'];
    }
    $container_props = apply_filters('themify_builder_module_container_props', array(
	'class' => implode(' ', $container_class),
	    ), $fields_args, $args['mod_name'], $args['module_ID']);
    
    $args=null;
    ?>
    <!-- Cart Icon module -->
    <div <?php echo self::get_element_attributes(self::sticky_element_props($container_props, $fields_args)); ?>>
	<?php
	$container_props=$container_class=null;
	global $woocommerce;
	$total = $woocommerce->cart->get_cart_contents_count();
	$cart_is_dropdown = 'dropdown' === $fields_args['style'];
	?>
        <div class="tbp_cart_icon_container">
	    <?php if ('on' === $fields_args['sub_total']): ?>
		<?php echo $woocommerce->cart->get_cart_subtotal(); ?>
	    <?php endif; ?>
	    <a href="<?php echo $cart_is_dropdown === true ? wc_get_cart_url() : '#tbp_slide_cart'; ?>">
		<i class="<?php echo esc_attr($fields_args['icon']); ?> tbp_shop_cart_icon"></i>
		    <?php if ('on' === $fields_args['bubble']): ?>
			<span class="tbp_cart_count<?php echo $total <= 0 ? ' tbp_cart_empty' : ''; ?>"><?php echo $total; ?></span>
		    <?php endif; ?>
	    </a>
	    <?php if ($cart_is_dropdown === false): ?>
		<div id="tbp_slide_cart" class="tbp_sidemenu sidemenu-off">
		    <a id="tbp_cart_icon_close" class="ti-close"></a>
		<?php endif; ?>

		<?php self::retrieve_template('wc/shopdock.php'); ?>

		<?php if ($cart_is_dropdown === false): ?>
		</div>
		<!-- /#slide-cart -->
	    <?php endif; ?>
        </div>
    </div>
    <!-- /Cart Icon module -->
<?php endif;
TFCache::end_cache();

