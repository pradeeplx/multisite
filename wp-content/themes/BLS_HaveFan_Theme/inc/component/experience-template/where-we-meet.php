
<div class="main-container" id="where-we-meet">
	 <div class="um-shadow">
	 	<div class="um-shadow-header">
			 <h6>Where we meet</h6>
			 <?php 
	 		if ($_GET['um_info_action']=='edit') { ?>
			 <p class="sub-heading"><?php the_field('where_we_meet_sub_heading', 'option'); ?></p>
			<?php } ?>
	 		<?php 
				if($profile_id==get_current_user_id()){
				  ?>
			 <span title="<?php the_field('where_we_meet_toll_tip_text', 'option'); ?>" class="tooltip">?</span>
	 		<a href="?profiletab=experience&subtab=where-we-meet&um_info_action=edit" class="pull-right edit-profile-btn"><?php the_field('where_we_meet_edit_button_text', 'option'); ?></a>
	 	<?php } ?>
	 	</div>
	 	<div class="um-shadow-body">
	 		<?php 
	 		if ($_GET['um_info_action']=='edit') {
	 		   echo do_shortcode('[my_acf_user_form field_group="230" ]'); 
	 	}else{ 
	 			?>
	 		<div class="included-services">
	 			<div class="single-included-services">
	 				 <div class="single-icon">
	 				 	<i class="fa fa-city"></i>
	 				 </div>
	 				 <div class="single-details">
	 				 	<h6>City</h6>
	 				 	<p class="where-we-meet-details">
	 				 		<?php echo  get_user_meta( $profile_id , "city-where-to-meet" ,true); ?>
	 				 	</p>
	 				 	<p class="where-we-meet-details">
	 				 		<?php echo  get_user_meta( $profile_id , "country-where-we-meet" ,true); ?>
	 				 	</p>
	 				 </div>
	 			</div>
	 			<div class="single-included-services">
	 				 <div class="single-icon">
	 				 	<i class="fa fa-address-card "></i>
	 				 </div>
	 				 <div class="single-details">
	 				 	<h6 class="where-we-meet-details">Address</h6>
	 				 	<p class="where-we-meet-details">
	 				 		<?php echo  get_user_meta( $profile_id , "address-where-to-meet" ,true); ?>
	 				 	</p>
	 				 </div>
	 			</div>
	 			<!-- <div class="single-included-services">
	 				 <div class="single-icon">
	 				 	<i class="fa fa-calendar"></i>
	 				 </div>
	 				 <div class="single-details">
	 				 	<h6>Time & Date</h6>
	 				 	<p class="where-we-meet-details">
	 				 		<?php echo  get_field(  "date-where-to-meet" , 'user_'.$profile_id ); ?>
	 				 	</p>
	 				 	<?php echo  get_user_meta( $profile_id , "date-where-we-meet" ,true); ?>
	 				 	<p class="where-we-meet-details">
	 				 		<?php echo  get_field(  "time-where-we-meet" , 'user_'.$profile_id ); ?>
	 				 	</p>

	 				 </div>
	 			</div> -->
	 			<p class="where-we-meet-detail"><?php echo  get_user_meta( $profile_id , "details-whare-we-meet" ,true); ?></p>
	 			
	 		</div>
	 	<?php } ?>
	 		
	 	</div>
      </div>
      
</div>