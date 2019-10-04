<div class="main-container" id="upcoming-event">
   <div class="um-shadow1">
    <div class="um-shadow-header">
      <?php
      global $wpdb;
    $table_name = $wpdb->prefix ."events";
    $user_country = trim(get_user_meta( $profile_id, 'country', true ));
    $event_city = trim(get_user_meta( $profile_id, 'user-citys', true ));
    $event_address_array =  array();
    if( $event_city != '' ){
        $event_address_array[] = $event_city;
    }
     if( $user_country != ''  ){
        $event_address_array[] = $user_country;
    }
    $max_people = get_user_meta( $profile_id , "max_people" ,true);

    $user_team = trim(get_user_meta( $profile_id, 'team-names', true ));
    $per_page = 5;
    $curret_date = date('Y-m-d');
    $rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE date(match_date) >= '".$curret_date."' AND `country_name` ='".$user_country."' AND `match_hometeam_name` = '".$user_team."' ");
    $pages = ceil($rowcount/$per_page);
     
    $events = $wpdb->get_results("SELECT * FROM $table_name WHERE date(match_date) >= '".$curret_date."' AND `country_name` ='".$user_country."' AND `match_hometeam_name` = '".$user_team."' limit 0,  $per_page ");
    
    $host_avatar = get_avatar_url( $profile_id );
     $event_address = implode(' - ', $event_address_array);
     ?>
      <h6>Next Matches </h6>
      <?php 
        if ( $_GET['edit_um_event']=='edit' && $profile_id==get_current_user_id() ) {
        ?>
      <p class="sub-heading"><?php the_field('next_matches_sub_heading','options'); ?> </p>
      <?php }?>
      <?php 
        if($profile_id==get_current_user_id() && !empty($events)){
           if ( $_GET['edit_um_event']=='edit') {
          ?>
          <span title="<?php the_field('next_matches_toll_tip_text','options'); ?>" class="tooltip">?</span>
        <?php } ?>
      <a href="?profiletab=next-matches&edit_um_event=edit" class="pull-right edit-profile-btn"><?php the_field('next_matches_edit_button_text','options'); ?></a>
      <?php } ?>
    </div>
    <div class="um-shadow-body">
    <div class="upcoming-event-section">
    <?php
    
    /** get all product **/
    // $get_products = $wpdb->get_
      if( $events ){
         
        echo '<div id="upcoming-event-section" class="exp_host">';
        foreach ($events as $event) {
          $default_editable = 'false';
          $tbl_post = $wpdb->prefix.'posts';
          $tbl_postemta = $wpdb->prefix.'postmeta';
          $product_id = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS $tbl_post.ID  FROM $tbl_post INNER JOIN hf_postmeta ON ( $tbl_post.ID = $tbl_postemta.post_id ) WHERE 1=1 AND $tbl_post.post_author IN (".$profile_id.") AND ( ( $tbl_postemta.meta_key = 'match_id' AND $tbl_postemta.meta_value = '".$event->match_id."' ) ) AND $tbl_post.post_type = 'product' AND (($tbl_post.post_status = 'publish' OR $tbl_post.post_status = 'draft')) GROUP BY $tbl_post.ID ORDER BY $tbl_post.post_date DESC LIMIT 0, 1");

          if( ! empty($product_id) ){
                $product_data = new WC_Product( $product_id[0]->ID );
                $product_price = $product_data->get_price_html();
            }else{
                $base_price  = get_user_meta( $profile_id, 'include_services_base_price', true);
                $product_price = wc_price($base_price);
             }
          $event_title = $user_team . ' VS '.$event->match_awayteam_name;


        ?>
        <div class="upcoming-event-data upcoming-host-event event-list-<?php echo $event->match_id;?>" >
              <div class="evnt-action-wrap">
              <?php
              if ( $_GET['edit_um_event']=='edit' && $profile_id==get_current_user_id() ) {
                $default_editable = 'true';
                  if( empty($product_id) ){
                   // no product created
                  ?>
                    <span id="event_action_btn_<?php echo $event->match_id;?>" data-val="<?php echo $event->match_id;?>" class="fa fa-plus event_action_btn " data-message_to="5" title="Create Experience">
                  </span>
                  <?php
                }else{
                   
                    $pro_stock = (int) get_post_meta( $product_id[0]->ID, '_stock', true );
                    if( $pro_stock  == $max_people  ){
                      ?>
                      <span id="event_action_btn_<?php echo $event->match_id;?>" data-id="<?php echo $profile_id;?>" data-val="<?php echo $product_id[0]->ID;?>" class="fa fa-edit edit_event_action_btn " data-message_to="5" title="Edit Experience">
                      </span>
                       <!--  <a  href="?profiletab=experience&subtab=update-event&um_info_action=edit&edit_experince_id=<?php echo $product_id[0]->ID;?>" data-val="<?php echo $event->match_id;?>" class="fa fa-edit edit_event_action_btn " data-message_to="5" title="Edit Product">
                      </a> -->
                      <?php
                    }
                }
                 
              }
              ?>
              </div>
              
              <div class="event-title-wrap">
                    <div class="event-title-left">
                       <h5 class="event-title" data-title="<?php echo $event_title;?>" data-city="<?php echo $event_city?>"><?php echo $event_title;?></h5>
                    </div>
                    <div class="event-title-right">
                      <span class="price-heading">Price</span>
                       
                      <?php echo  $product_price;?>                  
                    </div>
              </div>
          <div class="event-list">
          
          
          <p><span><strong class="fa fa-marker">Location : </strong><?php echo $event_address;?></span></p>
        
          <div class='event-footer-wrap'>
          <p><span><strong class="fa fa-date">Date &amp; Time : </strong><?php echo date('d F Y', strtotime($event->match_date)). ' '. $event->match_time;?></span></p>
          </div>
          </div>
             
        
        </div>
        <?php
        }
        echo "</div>";
      }else{
         echo "<p class='no-product-found'>No Match Found.</p>";
      }
    ?>
     
     
     
        <?php

        if( $pages > 1){
          ?>
          <ul class="pagination event-pagination" id="host-event-pagination">
          <input type='hidden' id='event_host_status' data-id="<?php echo $default_editable;?>" value='<?php echo $profile_id;?>' /> 
          <?php
          for( $i = 1; $i <= $pages; $i++ ){
            $active_class = ($i == 1) ? 'active' : '';
            ?>
            <li class="page-item <?php echo $active_class;?>" data-id="<?php echo $i;?>">
                <span class="page-link" href="#"><?php echo $i;?></span>
            </li>
          <?php
             
          }
          ?>
         </ul>  
          <?php
        }
         ?> 
         
     
     
        
      </div>
    </div>

   </div>
</div>

<!-- Model PopUp Start-->
<div id="HaveFunModal" class="HaveFunModal">

  <!-- Modal content -->
  <div class="havefun-content">
    <span class="close">&times;</span>
    <div class="havefun-content-wrap">
	    <div id="errorMsg"></div>
        <form id="assing_product" class="assing_product" method="post" action="#">
          <table>
            <tr>
              <td>
                Title *
              </td>
              <td>
                <input type="text" name="hv_title" value="" id="hv_title">
                <input type="hidden" name="hv_host_id" value="<?php echo $profile_id;?>" id="hv_host_id">
                <input type="hidden" name="hv_host_event_id" value="" id="hv_host_event_id">
              </td>
            </tr>
            <tr>
              <td>
                Price *
              </td>
              <td>
                <input type="number" min="0" name="hv_price" data-price="<?php echo  get_user_meta( $profile_id, 'include_services_base_price', true); ?>" value="<?php echo  get_user_meta( $profile_id, 'include_services_base_price', true); ?>" id="hv_price">
              </td>
            </tr>
            <tr>
              <td>
                Description
              </td>
              <td>
                 <textarea id="hv_descr" name="hv_descr"></textarea>
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
              
                <select name="include_food_services[]" multiple="">
                  <option value="">None</option>
              <?php  foreach ( $extra_food_services as $field ) {
                echo '<option value="'.$field->ID.'">'.$field->post_title.'</option>';
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
              
                <select name="include_drink_services[]" multiple="">
                  <option value="">None</option>
              <?php  foreach ( $extra_drink_services as $field ) {
                echo '<option value="'.$field->ID.'">'.$field->post_title.'</option>';
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
              
                <select name="include_ticket_services[]" multiple="">
                  <option value="">None</option>
              <?php  foreach ( $extra_ticket_services as $field ) {
                echo '<option value="'.$field->ID.'">'.$field->post_title.'</option>';
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
              
                <select name="include_transport_services[]" multiple="">
                  <option value="">None</option>
              <?php  foreach ( $extra_transport_services as $field ) {
                echo '<option value="'.$field->ID.'">'.$field->post_title.'</option>';
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
              
                <select name="include_tool_services[]" multiple="">
                  <option value="">None</option>
              <?php  foreach ( $extra_tool_services as $field ) {
                echo '<option value="'.$field->ID.'">'.$field->post_title.'</option>';
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
              
                <select name="include_other_services[]" multiple="">
                  <option value="">None</option>
              <?php  foreach ( $extra_other_services as $field ) {
                echo '<option value="'.$field->ID.'">'.$field->post_title.'</option>';
              }
              ?>
                </select>
                
                 
              </td>
            </tr>
           
            <?php endif;?>


            <tr>

              <td colspan="2">
                <input type="hidden" name="action" value="create_host_product">
                <input type="submit" id="hv_create_product_btn" name="hv_create_product_btn" >
              </td>
            </tr>
          </table>  
        </form>
    </div>
  </div>

</div>
<!-- Model PopUp End -->
<!-- Model PopUp Start-->
<div id="EditHaveFunModal" class="HaveFunModal">

  <!-- Edit Event Modal content -->
  <div class="havefun-content">
    <span class="close">&times;</span>
    <div class="havefun-content-wrap">
         
    </div>
  </div>

</div>
<!-- Edit Event Model PopUp End -->
 