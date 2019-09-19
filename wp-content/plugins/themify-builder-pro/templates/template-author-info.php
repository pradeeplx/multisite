<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Author Info
 * 
 * Access original fields: $args['mod_settings']
 * @author Themify
 */
if (TFCache::start_cache($args['mod_name'], self::$post_id, array('ID' => $args['module_ID']))):
    $fields_default = array(
	'author_layout' => '',
	'profile_picture' => 'on',
	'profile_name' => 'on',
	'html_tag' => 'h2',
	'author_link' => 'website',
	'bio' => 'on',
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
	$fields_args['author_layout'],
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
    <!-- Author Info module -->
    <div <?php echo self::get_element_attributes(self::sticky_element_props($container_props, $fields_args)); ?>>
	<?php
	$container_props=$container_class=null;
	$the_query = Tbp_Utils::get_actual_query();
	if ($the_query===null || $the_query->have_posts()) :
	    if($the_query!==null){
		$the_query->the_post();
	    }
	    ?>
	    <?php if ('yes' === $fields_args['profile_picture']): ?>
	        <div class="tbp_author_info_img"><?php echo get_avatar(get_the_author_meta('ID')); ?></div>
	    <?php endif; ?>

	    <?php if ('yes' === $fields_args['profile_name']): ?>

	        <<?php echo $fields_args['html_tag']; ?> class="tbp_author_info_name">
		
		<?php if ($fields_args['author_link'] === 'website' || $fields_args['author_link'] === 'archive'): ?>
		    <?php $link = 'website' === $fields_args['author_link'] ? get_the_author_meta('user_url') : get_author_posts_url(get_the_author_meta('ID'), get_the_author_meta('user_nicename')); ?>
		    <a href="<?php echo $link; ?>" class="tbp_author_info_link">
		    <?php endif; ?>

		    <?php the_author_meta('display_name'); ?>

		    <?php if (isset($link)): ?>
		    </a>
		<?php endif; ?>

	        </<?php echo $fields_args['html_tag']; ?>>

	    <?php endif; ?>

	    <?php if ('yes' === $fields_args['bio']): ?>
	        <div class="tbp_author_info_bio"><?php the_author_meta('description'); ?></div>
	    <?php endif; ?>

	    <?php
	    if($the_query!==null){
		wp_reset_postdata();
	    }
	endif;
	?>
    </div>
    <!-- /Author Info module -->
<?php endif; ?>
<?php TFCache::end_cache(); ?>
