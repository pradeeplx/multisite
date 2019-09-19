<div class="main-container" id="update-event">
	 <div class="um-shadow">
	 	<?php 
		if($_GET['um_info_action']=='edit' && $profile_id==get_current_user_id()){
			$edit_product_id = trim( $_GET['edit_experince_id']);
			// WP_Query arguments
		 
			 
			$product_details = get_post( $edit_product_id );
                  if( $product_details != null ){
                        $product_title = $product_details->post_title;
                        $post_content = $product_details->post_content;
                        $included_service = get_post_meta( $edit_product_id, '_included_service', true);
                        $regular_price = get_post_meta( $edit_product_id, '_regular_price', true);
                        $basic_price = get_post_meta( $edit_product_id, '_basic_price', true);
                        // echo "<pre>";
                        // print_r($included_service);
                  }
			 
		 
		?>
	 	<div class="um-shadow-header">
	 		<h6>Update Match</h6>
	 		<p class="sub-heading">Sub Heading</p>
      <span title="Go Back" class="tooltip">?</span>
	 		<a href="?profiletab=experience&subtab=upcoming-event&um_info_action=edit" class="pull-right edit-profile-btn">Go Back</a>
	 	
	 	</div>
	 	<div class="um-shadow-body">
	 		<div class="update-event-section">
	 			<form id="update_product" class="update_product" method="post" action="#">
      		<table>
      			<tr>
      				<td>
      					Title
      				</td>
      				<td>
      					<input type="text" name="u_hv_title" value="<?php echo $product_title;?>" id="u_hv_title" >
      					<input type="hidden" name="u_hv_host_id" value="<?php echo $profile_id;?>" id="u_hv_host_id">
      					<input type="hidden" name="u_hv_host_event_id" value="<?php echo get_post_meta( $edit_product_id, 'match_id', true);?>" id="hv_host_event_id">
                                    <input type="hidden" name="u_experience_id" value="<?php echo $edit_product_id;?>" id="u_experience_id">
      				</td>
      			</tr>
      			<tr>
      				<td>
      					Price
      				</td>
      				<td>
      					<input type="number" min="0" name="u_hv_price" value="<?php echo $basic_price; ?>" id="u_hv_price">
      				</td>
      			</tr>
      			<tr>
      				<td>
      					Description
      				</td>
      				<td>
      					<textarea id="u_hv_descr" name="u_hv_descr"><?php echo trim($post_content);?></textarea>
      				</td>
      			</tr>
                        <?php  
                        $extra_food_services = get_field('extra_food_services' , 'user_'.$profile_id );
                        if( !empty( $extra_food_services )):
                        ?>
                        <tr>
                              <td>
                                  Food
                              </td>
                              <td>
                                
                                  <select name="u_include_food_services[]" multiple="">
                                <?php  
                                foreach ( $extra_food_services as $field ) {
                                    $selected = '';
                                    if(isset( $included_service['food_services'])):
                                         
                                          if (in_array($field->ID, $included_service['food_services'])):
                                              $selected = 'selected';   
                                          endif;        
                                    endif;
                                  echo '<option value="'.$field->ID.'" '.$selected.'>'.$field->post_title.'</option>';
                                }
                                ?>
                                  </select>
                                  
                                   
                              </td>
                        </tr>
                         
                        <?php endif;?>
      			<?php  
                        $extra_drink_services = get_field('extra_drink_services' , 'user_'.$profile_id );
                        if( !empty( $extra_drink_services )):
                        ?>
                        <tr>
                              <td>
                            Drinks
                              </td>
                              <td>
                          
                              <select name="u_include_drink_services[]" multiple="">
                                <?php  
                                foreach ( $extra_drink_services as $field ) {
                                    $selected = '';
                                    if(isset( $included_service['drink_services'])):
                                         
                                          if (in_array($field->ID, $included_service['drink_services'])):
                                              $selected = 'selected';   
                                          endif;        
                                    endif;
                                  echo '<option value="'.$field->ID.'"  '.$selected.'>'.$field->post_title.'</option>';
                                }
                                ?>
                              </select>
                            
                             
                              </td>
                        </tr>
                         
                        <?php endif;?>
                        <?php  
                        $extra_ticket_services = get_field('extra_ticket_services' , 'user_'.$profile_id );
                        if( !empty( $extra_ticket_services )):
                        ?>
                        <tr>
                            <td>
                            Tickets
                            </td>
                            <td>
                          
                                  <select name="u_include_ticket_services[]" multiple="">
                                    <?php  
                                    foreach ( $extra_ticket_services as $field ) {
                                          $selected = '';
                                          if(isset( $included_service['ticket_services'])):
                                               
                                                if (in_array($field->ID, $included_service['ticket_services'])):
                                                    $selected = 'selected';   
                                                endif;        
                                          endif;
                                        echo '<option value="'.$field->ID.'"  '.$selected.'>'.$field->post_title.'</option>';
                                      }
                                    ?>
                                    </select>
                            
                             
                           </td>
                        </tr>
                        
                        <?php endif;?>
                        <?php  
                        $extra_transport_services = get_field('extra_transport_services' , 'user_'.$profile_id );
                        if( !empty( $extra_transport_services )):
                        ?>
                        <tr>
                             <td>
                            Transport
                             </td>
                             <td>
                          
                            <select name="u_include_transport_services[]" multiple="">
                          <?php
                          foreach ( $extra_transport_services as $field ) {
                              $selected = '';
                              if(isset( $included_service['transport_services'])):
                                   
                                    if (in_array($field->ID, $included_service['transport_services'])):
                                        $selected = 'selected';   
                                    endif;        
                              endif;
                            echo '<option value="'.$field->ID.'"  '.$selected.'>'.$field->post_title.'</option>';
                          }
                          ?>
                            </select>
                             
                             </td>
                        </tr>
                         
                        <?php endif;?>
                        <?php  
                        $extra_tool_services = get_field('extra_tool_services' , 'user_'.$profile_id );
                        if( !empty( $extra_tool_services )):
                        ?>
                        <tr>
                          <td>
                            Tools
                          </td>
                          <td>
                          
                            <select name="u_include_tool_services[]" multiple="">
                          <?php  
                          foreach ( $extra_tool_services as $field ) {
                              $selected = '';
                              if(isset( $included_service['tool_services'])):
                                   
                                    if (in_array($field->ID, $included_service['tool_services'])):
                                        $selected = 'selected';   
                                    endif;        
                              endif;
                            echo '<option value="'.$field->ID.'" '.$selected.'>'.$field->post_title.'</option>';
                          }
                          ?>
                            </select>
                            
                             
                          </td>
                        </tr>
                         
                        <?php endif;?>
                        <?php  
                        $extra_other_services = get_field('extra_other_services' , 'user_'.$profile_id );
                        if( !empty( $extra_other_services )):
                        ?>
                        <tr>
                          <td>
                            Others
                          </td>
                          <td>
                          
                            <select name="u_include_other_services[]" multiple="">
                          <?php  
                          foreach ( $extra_other_services as $field ) {
                              if(isset( $included_service['other_services'])):
                                   
                                    if (in_array($field->ID, $included_service['other_services'])):
                                        $selected = 'selected';   
                                    endif;        
                              endif;
                            echo '<option value="'.$field->ID.'" '.$selected.'>'.$field->post_title.'</option>';
                          }
                          ?>
                            </select>
                            
                             
                          </td>
                        </tr>
                        
                        <?php endif;?>
                        <tr>
                           <td>Availability</td>
                           <td>
                                <select name="u_availability" id="u_availability">
                                    <option value="yes">Yes</option>
                                     <option value="no">No</option>
                                </select>
                           </td>
                         </tr>
      			<tr>
      				<td colspan="2">
      					<input type="hidden" name="action" value="update_host_product">
      					<input type="submit" id="hv_update_product_btn" name="hv_update_product_btn" value="Update Now" >
      				</td>
      			</tr>
      		</table>	
      	</form>
	 		</div> 
	 	</div>
	 	<?php } ?>
	 </div>
</div>