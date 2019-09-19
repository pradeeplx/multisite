<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Template Archive Posts
 *
 * Access original fields: $args['mod_settings']
 * @author Themify
 */
if ( TFCache::start_cache( $args['mod_name'], self::$post_id, array( 'ID' => $args['module_ID'] ) ) ):
    $fields_default = array(
	'layout_post' => 'grid3',
	'masonry' => 'off',
	'no_found'=>'',
	'per_page' => get_option( 'posts_per_page' ),
	'pagination' => 'yes',
	'pagination_option' => 'numbers',
	'next_link' => '',
	'prev_link' => '',
	'tab_content_archive_posts' => array(),
	'css' => '',
	'animation_effect' => '',
	'offset'=>'',
	'order' => 'desc',
	'orderby' => 'id'
    );
    
    $fields_args = wp_parse_args($args['mod_settings'], $fields_default);
    unset($args['mod_settings']);
    $mod_name=$args['mod_name'];
    $container_class = apply_filters('themify_builder_module_classes', array(
	'module',
	'module-' . $mod_name,
	'module-' . $args['module_ID'],
	$fields_args['css'],
	self::parse_animation_effect($fields_args['animation_effect'], $fields_args)
	), $mod_name, $args['module_ID'], $fields_args );

    if ( !empty( $args['element_id'] ) ) {
	$container_class[] = 'tb_' . $args['element_id'];
    }
    $masonry_class = $fields_args['masonry'] === 'yes' && in_array($fields_args['layout_post'], array('grid2', 'grid3', 'grid4', 'grid2_thumb'), true) ? 'masonry' : '';
    $paged = $fields_args['pagination'] === 'yes' || $fields_args['pagination'] === 'on' ? self::get_paged_query() : 1;

    $per_page = (int)$fields_args['per_page'];
    $post_type=get_query_var('post_type');
    if(Tbp_Public::$isTemplatePage===true || empty($post_type)){
	    $post_type='post';
    }
    $query_args = array(
	    'post_type' => $post_type,
	    'post_status' => 'publish',
	    'ptb_disable'=>true,
	    'order' => $fields_args['order'],
	    'orderby' => $fields_args['orderby'],
	    'posts_per_page' => $per_page,
	    'paged' => $paged,
	    'offset' => ( ( $paged - 1 ) * $per_page )
    );
    if($fields_args['offset']!==''){
	$query_args['offset']+=(int)$fields_args['offset'];
    }
    if( ! empty( $fields_args['meta_key'] ) && in_array( $fields_args['orderby'], array( 'meta_value', 'meta_value_num' ),true ) ) {
	    $query_args[ 'meta_key' ] = $fields_args['meta_key'];
    }
    if ( is_category() || is_tag() || is_tax() ) {
	    $obj = get_queried_object(); 
	    if ( !empty( $obj ) ) {
		    if(is_category()){
			$query_args['cat'] = $obj->term_id;
		    }
		    elseif (is_tag() ) {
			$query_args['tag_id'] = $obj->term_id;
		    } 
		    elseif(is_tax()){
			    $tax = get_taxonomy($obj->taxonomy);
			    if(!empty($tax)){
				    $query_args['tax_query']=array(
					    array(
						'taxonomy' => $obj->taxonomy,
						'field'    => 'id',
						'terms'    => $obj->term_id
					    )
				    );
				    $query_args['post_type']=$tax->object_type;
			    }
		    }
	    }
    }
    elseif(is_search()){
	$query_args['s'] =get_search_query();
    }
    elseif(is_author()){
	$query_args['author'] =get_the_author_meta( 'ID' );
    }
    elseif(is_date()){
	    $datArray = array('year','monthnum','w','day','hour','minute','second','m');
	    foreach($datArray as $v){
		    $q = get_query_var($v);
		    if($q!=='' && $q!==null && $q!==false){
			$query_args[$v]=$q;
		    }
	    }
	    $datArray=null;
    }
    if(isset($fields_args['builder_content']) && Tbp_Utils::$isLoop===true){
	$fields_args['builder_id']=$args['builder_id'];
	unset($fields_args['tab_content_archive_posts']);
	$isAPP=true;
	$fields_args['builder_content']= json_decode($fields_args['builder_content'],true);
	if (!empty($args['element_id'])) {
	    $container_class[] = 'themify_builder_content-' . $args['element_id'];
	}
    }
    else{
	$isAPP=null;
    }
    $container_props = apply_filters('themify_builder_module_container_props', array(
	'class' => implode(' ', $container_class),
	    ), $fields_args, $mod_name, $args['module_ID']);
  
    $the_query = new WP_Query( $query_args );
    $query_args=$args=null;
    ?>
    <!-- <?php echo $mod_name?> module -->
    <div <?php echo self::get_element_attributes( self::sticky_element_props( $container_props, $fields_args ) ); ?>>
	<?php
	$container_props=$container_class=null;
	if ( $the_query->have_posts() ) :
		
		Tbp_Utils::disable_ptb_loop();
	    $isLoop = $ThemifyBuilder->in_the_loop === true;
	    $ThemifyBuilder->in_the_loop = true;
	    ?>
	    <div class="builder-posts-wrap clearfix loops-wrapper <?php echo $fields_args['layout_post'] . ' ' . $masonry_class; ?>">
		<?php
		while ($the_query->have_posts()) :
		    $the_query->the_post();
		    themify_post_before(); // hook
		    ?>
			<article itemscope itemtype="http://schema.org/BlogPosting" id="post-<?php echo the_ID(); ?>" <?php post_class('post clearfix'); ?>>
				<?php
				themify_post_start(); // hook
				if($isAPP===true){
				    self::retrieve_template('partials/advanched-archive.php', $fields_args);
				}
				else{
				    self::retrieve_template('partials/simple-archive.php', $fields_args);
				}
				themify_post_end(); // hook
				?>
			</article>
		    <?php
		    themify_post_after(); // hook
		endwhile;
		wp_reset_postdata();
		?>
	    </div>
	    <?php
	    $ThemifyBuilder->in_the_loop = $isLoop;
	    if ($fields_args['pagination'] === 'yes') {
		self::retrieve_template('partials/pagination.php', array(
		    'pagination_option' => $fields_args['pagination_option'],
		    'next_link' => $fields_args['next_link'],
		    'prev_link' => $fields_args['prev_link'],
		    'query' => $the_query
		));
	    }
	    ?>
	<?php else:?>
	    <?php echo $fields_args['no_found'];?>
	<?php endif; ?>
    </div>
    <!-- /<?php echo $mod_name?> module -->
<?php endif; ?>
<?php TFCache::end_cache(); ?>
