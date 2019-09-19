<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Archive Title
 *
 * Access original fields: $args['mod_settings']
 * @author Themify
 */
if (TFCache::start_cache($args['mod_name'], self::$post_id, array('ID' => $args['module_ID']))):
    $fields_default = array(
	'html_tag' => 'h2',
	'css' => '',
	'animation_effect' => ''
    );
    $fields_args = wp_parse_args($args['mod_settings'], $fields_default);
    unset($args['mod_settings']);
    $mod_name = $args['mod_name'];
    $container_class = apply_filters('themify_builder_module_classes', array(
	'module',
	'module-' . $mod_name,
	'module-' . $args['module_ID'],
	$fields_args['css'],
	self::parse_animation_effect($fields_args['animation_effect'], $fields_args)
	    ), $mod_name, $args['module_ID'], $fields_args);
    if (!empty($args['element_id'])) {
	$container_class[] = 'tb_' . $args['element_id'];
    }
    $container_props = apply_filters('themify_builder_module_container_props', array(
	'class' => implode(' ', $container_class),
	    ), $fields_args, $mod_name, $args['module_ID']);
    
    $args=null;
    if (is_search()) {
	    $title = sprintf(__('Search Results for: %s', 'themify'), esc_html(get_search_query(false)));
    } elseif (is_date()) {
	    $title = get_the_archive_title();
    } elseif (is_home()) {
	    $title = __('Latest Posts', 'themify');
    } else {
	$the_query = Tbp_Utils::get_actual_query();
	$title = '';
	if ($the_query===null || $the_query->have_posts() ){
	    if($the_query!==null){
		$the_query->the_post();
	    }
	    if (is_author()) {
		$title = '<span class="vcard">' . get_the_author() . '</span>';
	    } elseif (themify_is_woocommerce_active() === true && is_shop()) {
		$title = woocommerce_page_title(false);
	    } elseif (is_post_type_archive()) {
		$title = post_type_archive_title('', false);
	    } else {
		$title = single_term_title('', false);
	    }
	    if($the_query!==null){
		wp_reset_postdata();
	    }
	}
    }
    $isEmpty=empty($title) && (Tbp_Utils::$isActive===true || Themify_Builder::$frontedit_active===true);
    ?>
    <!-- Archive Title module -->
    <div <?php echo self::get_element_attributes(self::sticky_element_props($container_props, $fields_args)); ?>>
	<?php $container_props=$container_class=null;?>
        <<?php echo $fields_args['html_tag'] ?><?php if($isEmpty===true):?> class="tbp_empty_module"<?php endif;?>><?php echo $isEmpty===true?Themify_Builder_Model::get_module_name($mod_name):$title ?></<?php echo $fields_args['html_tag'] ?>>
    </div>
    <!-- /Archive Title module -->
<?php endif; ?>
<?php TFCache::end_cache(); ?>