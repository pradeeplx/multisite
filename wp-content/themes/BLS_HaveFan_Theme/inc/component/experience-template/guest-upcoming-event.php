<div class="main-container" id="upcoming-event">
   <div class="um-shadow1">
    <div class="um-shadow-header">
      <h6>Next Matches</h6>
     </div>
    <div class="um-shadow-body">
    <div class="upcoming-event-section">
    <?php
    global $wpdb;
    
    $curret_date = date('Y-m-d');
    $host_avatar = get_avatar_url( $profile_id );
    // $user_country = trim(get_user_meta( $profile_id, 'country', true ));
    // $event_city = trim(get_user_meta( $profile_id, 'user-citys', true ));
  $host_full_name = get_user_meta( $profile_id, 'first_name', true). ' '.get_user_meta( $profile_id, 'last_name', true);
   
    // The Query
    $guest_arg = array(
        'post_type' => 'product',
        'status' => array('publish'),
        'author' => $profile_id,
        'posts_per_page' => -1,
        'paged' => 1,
        'meta_key' => 'MatchDate',
        'orderby'   => 'meta_value',
        'order'     => 'ASC',
        'meta_query' => array(
            array(
                'key' => 'MatchDate',
                'value' => $curret_date,
                'compare' => '>=',
                'type' => 'DATE',
                )
            )
        );
    $the_query = new WP_Query( $guest_arg );

    $total = $the_query->found_posts;
   
    $total_pages = ceil($total / 5);
    /** get all product **/
    // $get_products = $wpdb->get_
    if ( $the_query->have_posts() ) {
        $table_name = $wpdb->prefix.'events';
        echo '<div id="upcoming-event-section">';
        while ( $the_query->have_posts() ) {
            $the_query->the_post();
            $prod_id = get_the_ID();
            $user_country = trim(get_post_meta( $prod_id, 'match_country', true ));
            $event_city = trim(get_post_meta( $prod_id, 'match_city', true ));
            
          
            $event_address_array =  array();
            if( $event_city != '' ){
                $event_address_array[] = $event_city;
            }
            if( $user_country != ''  ){
                $event_address_array[] = $user_country;
            }
            $event_address = implode(' - ', $event_address_array);
            $team1 = get_post_meta( $prod_id, 'Team1', true);
            $team2 = get_post_meta( $prod_id, 'Team2', true);
            $match_country = get_post_meta( $prod_id, 'match_country', true);
            $MaximumPeople = get_post_meta( $prod_id, 'MaximumPeople', true);
            $MinimumAge = get_post_meta( $prod_id, 'MinimumAge', true);
            $MatchDate = get_post_meta( $prod_id, 'MatchDate', true);
            $MatchTime = get_post_meta( $prod_id, 'MatchTime', true);
            $match_id = get_post_meta( $prod_id, 'match_id', true);
            $match_id_detail = $wpdb->get_row("SELECT * FROM $table_name WHERE `match_id` = '".$match_id."' ");
            $default_trophy = '';
             $product_data = new WC_Product( $prod_id );
               
            if(isset( $match_id_detail->league_name ) ){
                $default_trophy = $match_id_detail->league_name;
            }

            
        ?>
        <div class="upcoming-event-data upcoming-guest-event event-list-<?php echo $event->prod_id;?>">
              <div class="event-title-wrap">
                    <div class="event-title-left">
                       <h5 class="event-title"><?php the_title();?></h5>
                    </div>
                    <div class="event-title-right">
                      <span class="price-heading">Price</span>
                       
                      <?php echo $product_data->get_price_html();?>                  
                    </div>
              </div>    
          <div class="event-list">
            <div class="event-list-mid">
            <p><span><strong class="fa fa-marker">Location : </strong><?php echo $event_address;?></span></p>
            <p><span><strong class="fa fa-date">Date &amp; Time : </strong><?php echo date('d F Y', strtotime($MatchDate)). ' '. $MatchTime;?></span></p>
            </div>
            <div class='event-footer-wrap'>
          
            <a  href="<?php echo get_permalink($prod_id);?>" class="um-readmore-btn" data-message_to="5" title="Book Now">
                <span>Book Now</span>
            </a>
            </div>
          </div>
             
        
        </div>
        <?php
        }
        echo "</div>";
      }else{
        echo "<p class='no-product-found'>No Match Found.</p>";
      }

      /* Restore original Post Data */
      wp_reset_postdata();
    

      if( $total_pages > 1)
      {
          ?>
          <ul class="pagination event-pagination" id="guest-event-pagination">
          <input type='hidden' id='event_host_status' data-id="<?php echo $default_editable;?>" value='<?php echo $profile_id;?>' /> 
          <?php
          for( $i = 1; $i <= $total_pages; $i++ ){
            $active_class = ($i == 1) ? 'active' : '';
            ?>
            <li class="page-item <?php echo $active_class;?>" data-id="<?php echo $i;?>">
                <span class="page-link" href="#"><?php echo $i;?></span>
            </li>
          <?php
          }
          ?>
           </ul>
         </div>
      <?php 
      }
      ?> 
     
     
        
      </div>
    </div>

   </div>
</div>