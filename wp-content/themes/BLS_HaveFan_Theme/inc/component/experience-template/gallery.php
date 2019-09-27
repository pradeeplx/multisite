
<div class="main-container" id="photo-gallery">
	 <div class="um-shadow">
	 	<div class="um-shadow-header">
			 <h6>Photo gallery</h6>
			 <?php 
	 		if ($_GET['um_info_action']=='edit') { ?>
			 <p class="sub-heading"><?php the_field('photo_gallery_sub_heading', 'option'); ?></p>
			<?php } ?>
	 		<?php 
				if($profile_id==get_current_user_id()){
				  ?>
				  <span title="<?php the_field('photo_gallery_tool_tip_text', 'option'); ?>" class="tooltip">?</span>
	 		<a href="?profiletab=experience&subtab=photo-gallery&um_info_action=edit" class="pull-right edit-profile-btn"><?php the_field('photo_gallery_edit_button_text', 'option'); ?></a>
	 	<?php } ?>
	 		
	 	</div>
	 	<?php 
	 		if ($_GET['um_info_action']=='edit') {
	 		   echo do_shortcode('[my_acf_user_form field_group="911"]'); 
	 	}else{ 
	 			?>
	 	<div class="um-shadow-body">
	 		<div class="gallery-section">
	 			<?php 

					$images = get_field('experience_banner','user_'.$profile_id);
					$size = 'full'; // (thumbnail, medium, large, full or custom size)
                     
					if( $images ): ?>
						<div class="um-gallery--recent-photos-wrapper ">
						    <ul class="um-gallery--recent-photos">
						        <?php foreach( $images as $image ): ?>
						        	<?php $img= wp_get_attachment_image_src( $image['ID'], $size ); ?>
						            <li class="um--gallery-col-3 open-gallery-modal"  image-url="<?php echo $img[0]; ?>">
						            	
						            	<img src="<?php echo $img[0]; ?>" class="gallery-image">
						            </li>
						        <?php endforeach; ?>
						    </ul>
						</div>
					<?php endif; ?>
					<?php //echo do_shortcode('[um_gallery_recent_photos_grid ]'); ?>
	 		</div>
	 		
	 	</div>
	 	<?php
	 	 } 
	 	 ?>

	 </div>
</div>