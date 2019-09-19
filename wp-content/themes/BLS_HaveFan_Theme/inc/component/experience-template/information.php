

<div class="main-container" id="information">
	 <div class="um-shadow">
	 	<div class="um-shadow-header">
			 <h6>Information</h6>
			 <?php 
	 		if ($_GET['um_info_action']=='edit') { ?>
			 <p class="sub-heading"><?php the_field('information_button_sub_heading_', 'option'); ?></p>
			<?php } ?>
	 		<?php 
				if($profile_id==get_current_user_id()){
				  ?>
				  <span title="<?php the_field('information_edit_tool_tip', 'option'); ?>"class="tooltip" >?</span>
	 		<a href="?profiletab=experience&subtab=information&um_info_action=edit" class="pull-right edit-profile-btn"><?php the_field('information_edit_button_text', 'option'); ?></a>
	 	<?php } ?>
	 	</div>

	 	<div class="um-shadow-body">
	 		<?php 
	 		if ($_GET['um_info_action']=='edit') {
	 		   echo do_shortcode('[my_acf_user_form field_group="89"]'); 
	 	}else{ 
	 			?>
	 		<div class="about-section">
	 			<div class="title">
	 				<h4><?php echo  get_user_meta( $profile_id , "title" ,true); ?></h4>
	 			</div>
	 			<div class="left-services">
	 				<ul>
	 					<li>
	 						<i class="fa fa-users"></i>
	 						<span>Max People : <b><?php echo  get_user_meta( $profile_id , "max_people" ,true); ?></b></span>
	 					</li>
	 					<li>
	 						<i class="fa fa-home"></i>
	 						<span>Minimum Age : <b><?php echo  get_user_meta( $profile_id , "minimum_age" ,true); ?></b></span>
	 					</li>
	 				</ul>
	 			</div>
	 			<div class="right-price">
	 				<p>Price</p>
	 				<h2>€ <?php echo  get_user_meta( $profile_id , "include_services_base_price" ,true); ?></h2>
	 			</div>
	 			
	 		</div>
	 		<div class="detail-section">
	 			<p>
	 				<?php echo  get_user_meta( $profile_id , "information_details" ,true); ?>
	 			</p>

	 		</div>
	 	<?php
	 	 } 
	 	 ?>
	 		
	 	</div>

	 </div>
</div>