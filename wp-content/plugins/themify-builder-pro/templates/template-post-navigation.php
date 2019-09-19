<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Post Navigation
 * 
 * Access original fields: $args['mod_settings']
 * @author Themify
 */
if (TFCache::start_cache($args['mod_name'], self::$post_id, array('ID' => $args['module_ID']))):
$fields_default = array(
    'labels' => 'yes',
    'prev_label' =>'',
    'next_label' =>'',
    'arrows' => 'yes',
    'prev_arrow' => '',
    'next_arrow' => '',
    'same_cat' => 'yes',
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
    ), $args['mod_name'], $args['module_ID'], $fields_args );
    if(!empty($args['element_id'])){
	$container_class[] = 'tb_'.$args['element_id'];
    }
$container_props = apply_filters('themify_builder_module_container_props', array(
    'class' =>  implode(' ', $container_class),
        ), $fields_args, $args['mod_name'], $args['module_ID']);
    $args=null;
?>
<!-- Post Navigation module -->
<div <?php echo self::get_element_attributes( self::sticky_element_props( $container_props, $fields_args ) ); ?>>
	<?php
	    $container_props=$container_class=null;
	    $the_query = Tbp_Utils::get_actual_query();
	    if ($the_query===null || $the_query->have_posts() ){
		if($the_query!==null){
		    $the_query->the_post();
		}
		$isPrev = get_previous_post_link()?true:false;
		if($isPrev===true || get_next_post_link()){
		    $same_cat = 'yes' === $fields_args['same_cat'];
		    $arrows = array('prev','next');
		    foreach($arrows as $ar){
			$arrow = 'yes' === $fields_args['arrows'] ? '' !== $fields_args[$ar.'_arrow'] ? '<span class="ti ' . $fields_args[$ar.'_arrow'] . '"></span>' : '&laquo;' : '' ;
			$text = '<span class="tbp_post_navigation_arrow">'.$arrow.'</span>';
			$label ='yes' === $fields_args['labels']  ? $fields_args[$ar.'_label']: '';
			$p='';
			if($ar==='prev'){
			    if($isPrev===true){
				$p = get_adjacent_post( $same_cat, '', true );
			    }
			}
			elseif(get_next_post_link()){
			    $p = get_adjacent_post( $same_cat, '', false );
			}
			if($p!==''){
			    if($isPrev===true){
				previous_post_link( '%link', $text . '<span class="tbp_post_navigation_label">' . $label . '</span>' . '<br/>' . $p->post_title, $same_cat );
			    }
			    else{
				next_post_link( '%link', '<span class="tbp_post_navigation_label">' . $label . '</span>' . $text . '<br/>' . $next_title->post_title, $same_cat );
			    }
			}
		    }
		}
		if($the_query!==null){
		    wp_reset_postdata();
		}
	} ?>
</div>
<!-- /Post Navigation module -->
<?php endif; ?>
<?php TFCache::end_cache(); ?>
