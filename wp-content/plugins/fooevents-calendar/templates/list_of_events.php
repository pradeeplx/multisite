<div class="fooevents-calendar-list">

    <?php if(!empty($events)) :?>    

    <?php

     global $wpdb;
      $user_array = array();
      foreach($events as $event) : ?>

        <?php if (is_array($event)) :?>

        <?php $thumbnail = get_the_post_thumbnail_url($event['post_id']); ?>
        
        <?php
        
         
            $postData = get_post($event['post_id']); 
             if(in_array($postData->post_author, $user_array)){
             	continue;
             }
            $user_array[]=$postData->post_author;
            $userInfo = get_user_by('ID', $postData->post_author);
            $host_product_url = get_permalink($event->ID);
            $host_full_name = '';
            $user_team = '';
            $event_city = '';
            $host_avatar = '';
            $product_img_url = $thumbnail;
            $default_cover = UM()->options()->get( 'default_cover' );
            $event_title = '';
            if( $userInfo ){
                $host_product_url = site_url('user/'.$userInfo->user_login.'/?profiletab=next-matches&experienceId='.$event['post_id']);
                $host_full_name = get_user_meta( $userInfo->ID, 'first_name', true). ' '.get_user_meta( $userInfo->ID, 'last_name', true);
                $user_team = trim(get_user_meta( $userInfo->ID, 'team-names', true ));
                $event_city = trim(get_user_meta( $userInfo->ID, 'user-citys', true ));
                $host_avatar = get_avatar_url( $userInfo->ID );
                $event_title = trim(get_user_meta( $userInfo->ID, 'title', true ));
                $product_img = get_field('banner_image', 'user_'.$userInfo->ID);
                
                if(isset($product_img['url'])){
                   $product_img_url = $product_img['url'];
                }else if( $default_cover && $default_cover['url'] ) {
                    $product_img_url = $default_cover['url'];
                }
            }
           
            global $woocommerce;
            $product_data = new WC_Product($event['post_id']);
            $thePrice = $product_data->get_price_html();
            $match_country = get_post_meta( $event['post_id'], 'match_country', true);
            $MaximumPeople = get_post_meta( $event['post_id'], 'MaximumPeople', true);
            $MinimumAge = get_post_meta( $event['post_id'], 'MinimumAge', true);
            $MatchDate = get_post_meta( $event['post_id'], 'MatchDate', true);
            $MatchTime = get_post_meta( $event['post_id'], 'MatchTime', true);
            $match_id = get_post_meta( $event['post_id'], 'match_id', true);
            $table_name = $wpdb->prefix.'events';
            $matchId= $event['post_id'];
            $match_id_detail = $wpdb->get_row("SELECT * FROM $table_name WHERE `match_id` = '".$match_id."' ");
            $default_team1 = '';
            $default_team2 = '';
            $default_trophy = '';
            //print_r($match_id_detail);
            if(isset( $match_id_detail->league_name ) ){
                $default_trophy = $match_id_detail->league_name;
            }
            if(isset( $match_id_detail->match_hometeam_name ) ){
                $default_team1 = $match_id_detail->match_hometeam_name;
            }
            if(isset( $match_id_detail->match_awayteam_name ) ){
                $default_team2 = $match_id_detail->match_awayteam_name;
            }

            
            ?>


        <div class="upcoming-event-data">
            <?php if(!empty($product_img_url)) :?>
                <div class="img-wrapper">
                    <img src="<?php echo $product_img_url; ?>">
                    <span class="event_host_name"><?php echo $host_full_name;?></span>
                    <?php if( $host_avatar != ''){
                       echo "<img src='".$host_avatar."' class='event-host-avatar'/>";
                    }
                    ?>
                </div>
             <?php endif; ?>

            <?php if(!empty($event['desc'])) : ?>

            <p><?php echo wp_kses_post($event['desc']); ?></p>

            <?php endif; ?>
          <div class="event-list event-list-229026">
             
              <div class="event-title-wrap">
                    <div class="event-title-left">
                       <h5 class="event-title" >
                         <?php //echo esc_html($event['title']);
                         
                         echo $event_title;
                         ?>
                       </h5>
                    </div>
                    <div class="event-title-right">
                      <span class="event-title" >
                          <?php //echo $user_team .' <b> VS </b> '. $default_team2;

                          echo $user_team ;
                           ?>
                      </span>
                    </div> 
              </div>
              <p><span><strong class="fa fa-users">Max People : </strong><?php echo $MaximumPeople;?></span></p>
          <p><span><strong class="fa fa-date">Minimum Age : </strong><?php echo $MinimumAge;?></span></p>
          <p><span><strong class="fa fa-marker">Location : </strong><?php echo $match_country;?></span></p>
          <p><span><strong class="fa fa-trophy">League : </strong><?php echo $default_trophy;?></span></p>
          <div class="event-footer-wrap">
            <a data-val="" href="<?php  echo $host_product_url; ?>" class="um-readmore-btn" data-message_to="5" title="Message">
          <span>Read more</span>
          </a>
          <p class="event-price">
            <span class="price-heading">Price</span>
            <?php echo $thePrice; ?>
          </p>
          </div>
          </div>
             
        
        </div>

           <!--  <h3 class="fooevents-shortcode-title"><a href="<?php  echo $host_product_url; ?>"><?php echo esc_html($event['title']); ?></a></h3>
            

            <p><?php echo  $event['post_id']; ?></p>
            <p class="fooevents-shortcode-date"><?php echo $event['unformated_date']; ?></p>

            <?php if(!empty($thumbnail)) :?>

            <img src="<?php echo $thumbnail; ?>" class="fooevents-calendar-list-thumb"/>

            <?php endif; ?>

            <?php if(!empty($event['desc'])) : ?>

            <p><?php echo wp_kses_post($event['desc']); ?></p>

            <?php endif; ?>

            <?php if($event['in_stock'] == 'yes'): ?>

            <p><a class="button" href="<?php  echo $host_product_url; ?>" rel="nofollow"><?php echo esc_html($event['ticketTerm']); ?></a></p>

            <?php endif; ?>

            <div class="foo-clear"></div>

        </div> -->

        <div class="fooevents-calendar-clearfix"></div>

        <?php endif; ?>

    <?php endforeach; ?>

<?php else : ?>

    <?php _e('No upcoming events.', 'fooevents-calendar'); ?>

<?php endif; ?>    

</div>