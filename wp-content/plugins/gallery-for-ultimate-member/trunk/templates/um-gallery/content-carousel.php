<?php
global $photo;
$user_id = um_profile_id();
$data = array();
$users = array();
?>
<div id="um-gallery-carousel" class="owl-carousel um-gallery-carousel">
  <?php
	if ( ! empty( $images ) ) :
	foreach ( $images as $item ) {
		$photo                  = um_gallery_setup_photo( $item );
		$data[ $photo->id ]     = $photo;
		$users                  = um_gallery_setup_user( $users, $photo );
		?>
		<div class="um-gallery-inner item um-gallery-item" id="um-photo-<?php echo esc_attr( um_gallery_get_id() ); ?>">
			<a href="#" data-source-url="<?php echo esc_url( um_gallery_get_media_url() ); ?>"  data-lightbox="example-set" data-title="" id="um-gallery-item-<?php echo esc_attr( um_gallery_get_id() ); ?>" class="um-gallery-open-photo" data-id="<?php echo esc_attr( um_gallery_get_id() ); ?>"><img src="<?php um_gallery_the_image_url(); ?>" />
			</a>
			<?php if ( um_gallery()->is_owner() ) : ?>
			<div class="um-gallery-mask">
				<a href="#" class="um-gallery-delete-item" data-id="<?php echo esc_attr( um_gallery_get_id() ); ?>"><i class="um-faicon-trash"></i></a>
				<?php /*?><a href="#" class="um-manual-trigger"  data-parent=".um-edit-form" data-child=".um-btn-auto-width" data-id="<?php echo $image->id; ?>"><i class="um-faicon-pencil"></i></a><?php */?>
			</div>
			<?php endif; ?>
		</div>
<?php
	}
	endif;
	?>
</div>
<?php
	//Carousel Options from admin
	$autoplay = um_gallery_pro_get_option('um_gallery_seconds_count');
	if($autoplay) {
		$autoplay = $autoplay * 1000;
	} else {
		$autoplay = 'false';
	}
	if (um_gallery_pro_get_option('um_gallery_autoplay') == 'off'){
		$autoplay = 'false';
	}
	$carousel_item_count = um_gallery_pro_get_option('um_gallery_carousel_item_count');
	if(!$carousel_item_count) {
		$carousel_item_count = 10;
	}
	$seconds_count = um_gallery_pro_get_option('um_gallery_seconds_count');
	$pagination = um_gallery_pro_get_option('um_gallery_pagination');
	if ($pagination == 1) {
		$pagination = 'true';
	} else {
		$pagination = 'false';
	}
	$autoheight = um_gallery_pro_get_option('um_gallery_autoheight');
	if ($autoheight == 1) {
		$autoheight = 'true';
	} else {
		$autoheight = 'false';
	}
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#um-gallery-carousel').owlCarousel({
		items : <?php echo $carousel_item_count; ?>,
		autoPlay: <?php echo $autoplay;?>,
		pagination :  <?php echo $pagination; ?>,
		autoHeight : <?php echo $autoheight; ?>
	});
});
</script>
<script type="text/javascript" id="um-gallery-data">
	var um_gallery_images = <?php echo json_encode($data); ?>;
	var um_gallery_users = <?php echo json_encode($users); ?>;
</script>
