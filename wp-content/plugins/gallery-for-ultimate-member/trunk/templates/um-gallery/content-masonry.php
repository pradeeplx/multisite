<?php
global $photo;
$user_id = um_profile_id();
$data = array();
$users = array();
?>
<style type="text/css">
	.page-load-status {
	  display: none; /* hidden by default */
	  padding-top: 20px;
	  border-top: 1px solid #DDD;
	  text-align: center;
	  color: #777;
	}
</style>
<div class="um-gallery-item-wrapper um-gallery-masonry">
<?php
if ( ! empty( $images ) ) :
	foreach ( $images as $item ) {
			$photo                  = um_gallery_setup_photo( $item, true );
			$data[ $photo->id ]     = $photo;
			$users                  = um_gallery_setup_user( $users, $photo );
			$avatar                 = um_gallery_get_user_details('avatar', '', $users );
		?>
		<div class="um-gallery-item" id="um-photo-<?php echo esc_attr( um_gallery_get_id() ); ?>">
			<div class="um-gallery-inner">
				<a href="#" data-source-url="<?php echo esc_url( um_gallery_get_media_url() ); ?>" class="um-gallery-open-photo" id="um-gallery-item-<?php echo esc_attr( um_gallery_get_id() ); ?>" data-title=""  data-id="<?php echo esc_attr( um_gallery_get_id() ); ?>"><img src="<?php um_gallery_the_image_url( um_gallery_get_id(), 'full' ); ?>" />
				</a>
				<div class="um-gallery-overlay">
					<div class="um-gallery-img-actions">
						<a href="#" data-source-url="<?php echo esc_url( um_gallery_get_media_url() ); ?>" class="um-gallery-open-photo" id="um-gallery-item-<?php echo esc_attr( um_gallery_get_id() ); ?>" data-title=""  data-id="<?php echo esc_attr( um_gallery_get_id() ); ?>"><i class="um-faicon-expand" aria-hidden="true"></i></a>
					</div>
					<div class="um-gallery-img-info">
						<a target="_blank" href="<?php echo um_gallery_get_user_details('link', '', $users ); ?>"><img src="<?php  echo esc_url( $avatar['url'] )?>" alt="<?php  echo esc_attr( $avatar['alt'] )?>" class="<?php  echo esc_attr( $avatar['class'] )?>" /><?php echo um_gallery_get_user_details('name', '', $users ); ?></a>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
endif;
?>
</div>
<?php /*
<div class="page-load-status">
  <div class="loader-ellips infinite-scroll-request">
    <span class="loader-ellips__dot"></span>
    <span class="loader-ellips__dot"></span>
    <span class="loader-ellips__dot"></span>
    <span class="loader-ellips__dot"></span>
  </div>
  <p class="infinite-scroll-last">End of content</p>
  <p class="infinite-scroll-error">No more pages to load</p>
</div>



<p>
  <button class="view-more-button">View more</button>
</p>
*/ ?>
<script>
    /**
	 * Infinite Scroll + Masonry + ImagesLoaded
	 */
	(function() {

		// Main content container
		var $container = jQuery('.um-gallery-masonry');

		// Masonry + ImagesLoaded
		$container.imagesLoaded(function(){
			$container.masonry({
				// selector for entry content
				itemSelector: '.um-gallery-item'
				/*columnWidth: 200*/
			});
		});

		// Infinite Scroll
		/*$container.infiniteScroll({

			// selector for the paged navigation (it will be hidden)
			navSelector  : ".navigation",
			// selector for the NEXT link (to page 2)
			nextSelector : ".nav-previous a",
			// selector for all items you'll retrieve
			itemSelector : ".entry-content",
			path: '.pagination__next',
			// finished message
			loading: {
				finishedMsg: 'No more pages to load.'
				}
			},

			// Trigger Masonry as a callback
			function( newElements ) {
				// hide new items while they are loading
				var $newElems = jQuery( newElements ).css({ opacity: 0 });
				// ensure that images load before adding to masonry layout
				$newElems.imagesLoaded(function(){
					// show elems now they're ready
					$newElems.animate({ opacity: 1 });
					$container.masonry( 'appended', $newElems, true );
				});

		});*/
		var nextPenSlugs = [
		  '3d9a3b8092ebcf9bc4a72672b81df1ac',
		  '2cde50c59ea73c47aec5bd26343ce287',
		  'd83110c5f71ea23ba5800b6b1a4a95c4',
		];

		function getPenPath() {
		  var slug = nextPenSlugs[ this.loadCount ];
		  if ( slug ) {
		    return '<?php echo admin_url( 'admin-ajax.php?action=um_gallery_get_more_photos&slug=' ); ?>' + slug;
		  }
		}
		$container.infiniteScroll({
		  path: getPenPath,
		  append: '.post',
		  button: '.view-more-button',
		  // using button, disable loading on scroll 
		  scrollThreshold: false,
		  status: '.page-load-status',
		});
		/**
		 * OPTIONAL!
		 * Load new pages by clicking a link
		 */

		// Pause Infinite Scroll
		jQuery(window).unbind('.infscr');

		// Resume Infinite Scroll
		jQuery('.nav-previous a').click(function(){
			$container.infiniteScroll('retrieve');
			return false;
		});

	})();
</script>
<script type="text/javascript" id="um-gallery-data">
	var um_gallery_images = <?php echo json_encode( $data ); ?>;
	var um_gallery_users  = <?php echo json_encode( $users ); ?>;
</script>
