<?php
themify_product_image_start(); // Hook 
$product = wc_get_product( get_the_ID() );
$attachment_id = $product->get_image_id();

//Width/Height
$style='';
if($args['image_w'] !== '' || $args['image_h'] !== '' ){
	
	if ($args['image_w'] !== '') {
		$width = $args['image_w'].'&';
		$style .= 'width:' . $args['image_w'] . 'px;';
	}
	if ($args['image_h'] !== '') {
		$height = $args['image_h'].'&';
		$style.= 'height:' . $args['image_h'] . 'px;';
	}
	$style=' style="'.$style.'"';
}
//slider/image
$image='';
if(!empty($attachment_id)){
	if ( $args['image_w'] !== '' || $args['image_h'] !== '' ) {
		if ( $args['image_w'] !== '' ) {
			$thumbnail_size[0] = $args['image_w'];
		} else {
			$thumbnail_size[0] = $args['image_h'];
		}
		if ( $args['image_h'] !== '' ) {
			$thumbnail_size[1] = $args['image_h'];
		} else {
			$thumbnail_size[1] = $args['image_w'];
		}
	}
	else{
		$thumbnail_size = 'thumb';
	}

	$image_size        = apply_filters( 'woocommerce_gallery_image_size', $args['image_w'] === '' && $args['image_h'] === '' ? 'woocommerce_single' : $thumbnail_size );
	$full_size         = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
	$thumbnail_src     = wp_get_attachment_image_src( $attachment_id, $thumbnail_size );
	$full_src          = wp_get_attachment_image_src( $attachment_id, $full_size );
	$alt_text          = trim( wp_strip_all_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) );

	$image =  wp_get_attachment_image(
				$attachment_id,
				$image_size,
				false,
				apply_filters(
					'woocommerce_gallery_image_html_attachment_image_params',
					array(
						'title'                   => _wp_specialchars( get_post_field( 'post_title', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
						'data-caption'            => _wp_specialchars( get_post_field( 'post_excerpt', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
						'data-src'                => esc_url( $full_src[0] ),
						'data-large_image'        => esc_url( $full_src[0] ),
						'data-large_image_width'  => esc_attr( $full_src[1] ),
						'data-large_image_height' => esc_attr( $full_src[2] ),
						'class'                   => 'wp-post-image',
					),
					$attachment_id,
					$image_size,
					true
				)
			);;
	
	$attachment_ids = $product->get_gallery_image_ids();
	$full_src= $full_src[0];
}
else{
	
	if(Themify_Builder_Model::is_img_php_disabled()){
		global $_wp_additional_image_sizes;
		$upload_dir = wp_upload_dir();
		$base_url = $upload_dir['baseurl'];
		$attachment_id = themify_get_attachment_id_from_url($args['url_image'], $upload_dir['baseurl']);
		$class = $attachment_id ? 'wp-image-' . $attachment_id : '';
		$full_src = esc_url($args['url_image']);
		$image = '<img src="' . $full_src . '" alt="" width="' . $args['image_w'] . '" height="' . $args['image_h'] . '" class="' . $class . '">';
		if (!empty($attachment_id)) {
			$image = wp_get_attachment_image($attachment_id);
		}
		$image = apply_filters('themify_image_make_responsive_image', $image);
	}
	else{
		//fallback
		$param_image = 'w=' . $args['image_w'] . '&h=' . $args['image_h'] . '&ignore=true';
		$full_src = $args['fallback_s'] === 'yes' && $args['fallback_i'] !== '' && !has_post_thumbnail()?esc_url($args['fallback_i']):get_the_post_thumbnail_url();
		$param_image.='&src=' . $full_src;
		$image = themify_get_image($param_image);
		
	}
}
?>
<div class="product <?php echo $args['sale_b'] === 'yes' ? ' sale-badge-' . $args['badge_pos'] : ''; ?>">
		<?php if ($args['sale_b'] === 'yes'):?>
			<?php woocommerce_show_product_sale_flash();?>
		<?php endif; ?>
	<div class="woocommerce-product-gallery woocommerce-product-gallery--with-images woocommerce-product-gallery--columns-4 images"<?php echo $style?>>
		<figure class="woocommerce-product-gallery__wrapper">
			<div <?php if(isset($thumbnail_src)):?>data-thumb="<?php echo esc_url( $thumbnail_src[0] )?>" data-thumb-alt="<?php echo esc_attr_e( $alt_text )?>" <?php endif;?>class="woocommerce-product-gallery__image image-wrap">
				<a href="<?php echo $full_src?>"><?php echo $image?></a>
				<?php if(!empty($attachment_ids)):?>
					<?php foreach ( $attachment_ids as $id ):?>
						<?php echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', wc_get_gallery_image_html( $id ), $id );?>
					<?php endforeach;?>
				<?php endif;?>
			</div>
		</figure>
	</div>
</div>
<?php
themify_product_image_end(); // Hook