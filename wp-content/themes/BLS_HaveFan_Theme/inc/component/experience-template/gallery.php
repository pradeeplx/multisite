
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
	 		<a href="?profiletab=gallery" class="pull-right edit-profile-btn"><?php the_field('photo_gallery_edit_button_text', 'option'); ?></a>
	 	<?php } ?>
	 		
	 	</div>
	 	<div class="um-shadow-body">
	 		<div class="gallery-section">
	 			<?php echo do_shortcode('[um_gallery_recent_photos_grid ]'); ?>
	 		</div>
	 		
	 	</div>

	 </div>
</div>