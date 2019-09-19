
<?php if($profile_id==get_current_user_id()){ ?>
<div class="main-container" id="banner-image">
	 <div class="um-shadow">
	 	<div class="um-shadow-header">
	 		<h6>Banner Image</h6>
			 <?php 
	 		if ($_GET['um_info_action']=='edit') { ?>
			 <p class="sub-heading"><?php the_field('banner_image_sub_heading', 'option'); ?></p>
			<?php } ?>
	 		
	 		
	 		
	 	</div>
	 	<div class="um-shadow-body">
	 		<div class="gallery-section">
	 			<?php 
				
				  
	 			echo do_shortcode('[my_acf_user_form field_group="428" ]'); 
	 			  ?>
	 		</div>
	 		
	 	</div>

	 </div>
</div>
<?php } ?>