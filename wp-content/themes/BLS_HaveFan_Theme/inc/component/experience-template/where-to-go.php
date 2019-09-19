<div class="main-container" id="where-to-go">
	 <div class="um-shadow">
	 	<div class="um-shadow-header">
			 <h6>Where to go</h6>
			  <?php 
	 		if ($_GET['um_info_action']=='edit') { ?>
			 <p class="sub-heading"><?php the_field('where_to_go_sub_heading', 'option'); ?></p>
			<?php } ?>
	 		<?php 
				if($profile_id==get_current_user_id()){
				  ?>
				   <span title="<?php the_field('where_to_go_toll_tip_text', 'option'); ?>" class="tooltip">?</span>
	 		<a href="?profiletab=experience&subtab=where-to-go&um_info_action=edit" class="pull-right edit-profile-btn"><?php the_field('where_to_go_edit_button_text', 'option'); ?></a>
	 	<?php } ?>
	 	</div>
	 	<div class="um-shadow-body">
	 		<?php 
	 		if ($_GET['um_info_action']=='edit') {
	 		   echo do_shortcode('[my_acf_user_form field_group="133" ]'); 
	 	}else{ 
	 			?>
	 		<div class="where-to-section">
		       	<div class="wapper-section" >
			 		<p><?php  echo  get_user_meta( $profile_id, 'where_to_go_list', true);  ?></p>
			 	</div>
	 		</div>
	 		<?php
	 	 } 
	 	 ?>
	 	</div>

	 </div>
</div>