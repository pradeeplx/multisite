<div class="main-container" id="guest-requests">
	 <div class="um-shadow">
	 	<div class="um-shadow-header">
			 <h6>Guest Requests </h6>
			 <?php 
	 		if ($_GET['um_info_action']=='edit') { ?>
			 <p class="sub-heading"><?php the_field('guest_requests_sub_heading', 'option'); ?></p>
			<?php } ?>
	 		<?php 
				if($profile_id==get_current_user_id()){
				  ?>
				  <span title="<?php the_field('guest_requests_edit_button_text', 'option'); ?>" class="tooltip">?</span>
				  <a href="?profiletab=experience&subtab=guest-requests&um_info_action=edit" class="pull-right edit-profile-btn"><?php the_field('guest_requests_toll_tip_text', 'option'); ?></a>
				  <?php
				}
	 		?>
	 		
	 	</div>
	 	<div class="um-shadow-body">
	 		<div class="where-to-section">
	 			<?php 
	 		if ($_GET['um_info_action']=='edit') {
	 		   echo do_shortcode('[my_acf_user_form field_group="141"]'); 
	 	}else{ 
	 	?>
		    <div class="wapper-section">
		   	<p><?php echo get_user_meta( $profile_id, 'guest_requests', true);?></p>
		   </div>
	<?php
	} 
	 	 ?> 
	 			
	 		</div>
	 	</div>

	 </div>
</div>