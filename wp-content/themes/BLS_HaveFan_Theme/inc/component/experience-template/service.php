 
<div class="main-container" id="services">
	 <div class="um-shadow">
	 	<div class="um-shadow-header">
			 <h6>Included Services</h6>
			  <?php 
	 		if ($_GET['um_info_action']=='edit') { ?>
			 <p class="sub-heading"><?php the_field('included_services_sub_heading', 'option'); ?></p>
			
			 <p class="sub-heading">Sub Heading</p>
			<?php } ?>
	 		<?php 
				if($profile_id==get_current_user_id()){
						if ($_GET['um_info_action']=='edit') { 
				  ?>
				  <span title="Included Services edit section." class="tooltip">?</span>
				<?php } ?>
	 		<a href="?profiletab=experience&subtab=services&um_info_action=edit" class="pull-right edit-profile-btn">Edit Included Services</a>
	 	<?php } ?>
	 	</div>
	 	<div class="um-shadow-body">
	 		<?php 
	 		if ($_GET['um_info_action']=='edit') {
	 		   echo do_shortcode('[my_acf_user_form field_group="338" ]'); 
       }else{ 
	 			?>
	 		<div class="included-services">
	 			
	 			 <?php
					// check if the repeater field has rows of data
					//print_r(get_field('select_services' , 'user_'.$profile_id ));
	 			 $fieldTicketServices=get_field('extra_ticket_services' , 'user_'.$profile_id );
				 	 
					$ticket_service = array();
					foreach ( $fieldTicketServices as $field ) {
						$ticket_service[] = $field->post_title;
					}

					if(! empty($ticket_service)):
					?>
					<div class="single-included-services ticket-included-services">
						<div class="single-icon">
							<i class="fa fa-ticket"></i>
							
						</div>

						<div class="single-details">
							<h3>Tickets</h3>
							<p><?php echo implode(', ', $ticket_service); ?></p>
							 
						</div>
					</div>
					 <!--- Transport -->
                     <?php
                 endif;
					$fieldExtraServices=get_field('extra_food_services' , 'user_'.$profile_id );
				 	 
					$food_service = array();
					foreach ( $fieldExtraServices as $field ) {
						$food_service[] = $field->post_title;
					}
					if(! empty($food_service)):
					?>
					<div class="single-included-services food-included-services">
						<div class="single-icon">
							<i class="fa fa-birthday-cake"></i>
							
						</div>

						<div class="single-details">
							<h3>Food</h3>
							<p><?php echo implode(', ', $food_service); ?></p>
							 
						</div>
					</div>
                     
                     <!--- Drink -->
                     <?php
                	endif;
                    $fieldDrinkServices=get_field('extra_drink_services' , 'user_'.$profile_id );
				 	 
					$drink_service = array();
					foreach ( $fieldDrinkServices as $field ) {
						$drink_service[] = $field->post_title;
					}
					if(! empty($drink_service)):
					?>
					<div class="single-included-services drink-included-services">
						<div class="single-icon">
							<i class="fa fa-beer "></i>
							
						</div>

						<div class="single-details">
							<h3>Drinks</h3>
							<p><?php echo implode(', ', $drink_service); ?></p>
							 
						</div>
					</div>

					  <!--- Tickets -->
                     <?php
                 	endif;
                     
                     $fieldTransportServices=get_field('extra_transport_services' , 'user_'.$profile_id );
				 	 
					$transport_service = array();
					foreach ( $fieldTransportServices as $field ) {
						$transport_service[] = $field->post_title;
					}
					if(! empty($transport_service)):
					         ?>
					<div class="single-included-services transport-included-services">
						<div class="single-icon">
							<i class="fa fa-bus "></i>
							
						</div>

						<div class="single-details">
							<h3>Transport</h3>
							<p><?php echo implode(', ', $transport_service); ?></p>
							 
						</div>
					</div>
					<?php
				endif;
                     $fieldToolServices=get_field('extra_tool_services' , 'user_'.$profile_id );
				 	 
					$tool_service = array();
					foreach ( $fieldToolServices as $field ) {
						$tool_service[] = $field->post_title;
					}
					if(! empty($tool_service)):
					         ?>
					<div class="single-included-services tool-included-services">
						<div class="single-icon">
							<i class="fa fa-birthday-cake "></i>
							
						</div>

						<div class="single-details">
							<h3>Tools</h3>
							<p><?php echo implode(', ', $tool_service); ?></p>
							 
						</div>
					</div>
					<?php
					endif;
                    $fieldOtherServices=get_field('extra_other_services' , 'user_'.$profile_id );
				 	 
					$other_service = array();
					foreach ( $fieldOtherServices as $field ) {
						$other_service[] = $field->post_title;
					}
					if(! empty($other_service)):
					         ?>
					<div class="single-included-services other-included-services">
						<div class="single-icon">
							<i class="fa fa-birthday-cake "></i>
							
						</div>

						<div class="single-details">
							<h3>Tools</h3>
							<p><?php echo implode(', ', $other_service); ?></p>
							 
						</div>
					</div>
				<?php endif;?>

	 		</div>
	 	<?php } ?>
	 		
	 	</div>
      </div>
     
      
</div>

<?php 

acf_form_head();



?>
<!-- <div id="content"> -->
	
	<?php
	
	// acf_form(array(
	// 	'post_id'		=> 'new_post',
	// 	'post_title'	=> true,
	// 	'post_content'	=> false,
	// 	'new_post'		=> array(
	// 		'post_type'		=> 'product',
	// 		'post_status'	=> 'publish'
	// 	)
	// ));
	
	?>
	
<!-- </div> -->

