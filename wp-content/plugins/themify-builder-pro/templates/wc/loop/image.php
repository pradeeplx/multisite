<?php
themify_product_image_start(); // Hook 
$product_image='';
if (!Themify_Builder_Model::is_img_php_disabled()) {
		$param_image = 'w=' . $args['image_w'] . '&h=' . $args['image_h'] . '&ignore=true';
    if ($args['fallback_s'] === 'yes' && $args['fallback_i'] !== '' && !has_post_thumbnail()) {
		$param_image.='&src=' . esc_url($args['fallback_i']) . '&alt=';
    }
    $product_image = themify_get_image($param_image);
}
 if(!empty($product_image)){
    if($args['link']!=='none'){
		$hasLink=true;
		$link = $args['link']==='permalink'?themify_get_featured_image_link():($args['link']==='media'?wp_get_attachment_url(get_post_thumbnail_id()):'');
		$link_attr=Tbp_Utils::getLinkParams($args,$link);
		if(!isset($link_attr['href'])){
			$hasLink=false;
		}
    }
    else{
	$hasLink=false;
    }
    global $product;
?>
<figure class="product-image post-image<?php echo isset($args['auto_fullwidth'] ) && $args['auto_fullwidth'] == '1' ? ' auto_fullwidth' : ''; ?><?php echo isset($args['appearance_image'])? ' image-wrap' : ''; ?><?php echo $args['sale_b'] === 'yes' ? ' sale-badge-' . $args['badge_pos'] : ''; ?>">
    <?php if ($args['sale_b'] === 'yes'):?>
	<?php woocommerce_show_product_loop_sale_flash();?>
    <?php endif; ?>
    <?php if($hasLink===true):?>
	<a <?php echo self::get_element_attributes($link_attr); ?>>
    <?php endif;?>
	    
    <?php echo $product_image;?>  
    <?php if($hasLink===true):?>
	</a>
    <?php endif;?>
</figure>
<?php  } 
themify_product_image_end(); // Hook
$args=null;