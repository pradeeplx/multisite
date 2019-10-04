<?php
/**
 * After add to cart product redirect to checkout page.
 *
 */
function havefan_redirect_checkout_add_cart( $url ) {
   $url = get_permalink( get_option( 'woocommerce_checkout_page_id' ) ); 
   return $url;
}
 
add_filter( 'woocommerce_add_to_cart_redirect', 'havefan_redirect_checkout_add_cart' );

/**
 * Change 'Add to cart' text button
 *
 */
function woo_havefan_single_add_to_cart_text() {
    return __( 'Book Now', 'woocommerce' );
}
add_filter( 'add_to_cart_text', 'woo_havefan_single_add_to_cart_text' );               
add_filter( 'woocommerce_product_single_add_to_cart_text', 'woo_havefan_single_add_to_cart_text' );

/**
 * Redirect shop to event page
 *
 */
function havefan_shop_page_redirect() {
    if( is_shop() ){
        wp_redirect( home_url( 'event/?view=list' ) );
        exit();
    }
}
add_action( 'template_redirect', 'havefan_shop_page_redirect' );

/* add new tab called "mytab" */

add_filter('um_account_page_default_tabs_hook', 'my_custom_tab_in_um', 10 );
function my_custom_tab_in_um( $tabs ) {
	$tabs[800]['information']['icon'] = 'um-faicon-pencil';
	$tabs[800]['information']['title'] = 'My Information';
	$tabs[800]['information']['custom'] = true;
	return $tabs;
}
	
/* make our new tab hookable */

add_action('um_account_tab__information', 'um_account_tab__information');
function um_account_tab__information( $info ) {
	global $ultimatemember;
	extract( $info );

	$output = $ultimatemember->account->get_tab_output('information');
	if ( $output ) { echo $output; }
}

/* Finally we add some content in the tab */

add_filter('um_account_content_hook_information', 'um_account_content_hook_information');
function um_account_content_hook_information( $output ){
	ob_start();
	?>
		
	<div class="um-field ">
		
		<!-- Here goes your custom content  acfFormParsnal -->
		<div id="parsnalSection"></div>
		<?php   //echo  do_shortcode('[my_acf_user_form field_group="284" ]'); ?>
	</div>		
		
	<?php
		
	$output .= ob_get_contents();
	ob_end_clean();
	return $output;
}

function show_all_havafun_event(){
	ob_start();
	 
	global $member;
	if( um_profile_id() == get_current_user_id()){
		$profile_id = um_profile_id();
		require_once( get_stylesheet_directory().'/inc/component/experience-template/upcoming-event.php');
	}else if(um_profile_id() > 0){
		$profile_id = um_profile_id();
		require_once( get_stylesheet_directory().'/inc/component/experience-template/guest-upcoming-event.php');
	}else{
		echo "NA";
	}
	$output .= ob_get_contents();
	ob_end_clean();
	return $output;

}
add_shortcode('show-all-havafun-event', 'show_all_havafun_event');

function default_hf_search_form( $atts ){
	ob_start();
	 $atts = shortcode_atts( array(
			'layout' => 'vertical'
		   ), $atts, 'default_hf_search_form' );
 	
	global $wpdb;
	$event_Table = $wpdb->prefix.'events';
	$postmeta_table = $wpdb->prefix.'postmeta';
	$form_class = 'form_'.$atts['layout'];
	$selected_country = '';
	if(isset($_GET['by_country'])){
		$selected_country = ( trim( $_GET['by_country'] ) != '' ) ? trim( $_GET['by_country'] ) : '';
	}
	$selected_city = '';
	if(isset($_GET['by_city']) && strlen($_GET['by_city']) && strtolower(trim($_GET['by_city'])) !='all'){
		$selected_city = ( trim( $_GET['by_city'] ) != '' ) ? trim( $_GET['by_city'] ) : '';
	}
	 
	if(isset($_GET['by_date'])){
		$selected_date = ( trim( $_GET['by_date'] ) != '' ) ? trim( $_GET['by_date'] ) : '';
	}
	$selected_team = '';
	if(isset($_GET['by_team'])){
		$selected_team = ( trim( $_GET['by_team'] ) != '' ) ? trim( $_GET['by_team'] ) : '';
	}
	// $default_city_list = array();
	// $default_team_list = array();
	$default_date_array = array();

	// $available_city_list = array();
	$available_team_list = array();
	// $availble_date_list = array();

	$haveFan_all_country = array();
	$haveFan_all_city = array();
	$haveFan_all_team = array();
	$haveFan_all_dates = array();
	$MatchDate_list= $wpdb->get_results( "SELECT `meta_value` FROM $postmeta_table where  `meta_key`='MatchDate'  group by `meta_value` " );
	foreach ($MatchDate_list as $key) {
		$default_date_array[]= date('j-n-Y',strtotime(trim($key->meta_value)) );
	}
	$curret_date = date('Y-m-d');
	$meta_query_array = array('relation' => 'AND',array(
            'key' => 'MatchDate',
            'value' => $curret_date,
            'compare' => '>=',
            'type' => 'DATE',
            )
		);
	if( '' != $selected_country ){
		$meta_query_array[] = array(
            'key' => 'match_country',
            'value' => $selected_country,
            'compare' => '=',
            );
	}
	if('' != $selected_city ){
		$meta_query_array[] = array(
            'key' => 'match_city',
            'value' => $selected_city,
            'compare' => '=',
        );
	}
	if('' != $selected_team ){
		$meta_query_array[] = array('relation' => 'OR', array(
            'key' => 'Team1',
            'value' => $selected_team,
            'compare' => '=',
            ), array(
            'key' => 'Team2',
            'value' => $selected_team,
            'compare' => '=',
            )
        );
	}
	 
    $guest_arg = array(
	        'post_type' => 'product',
	        'status' => array('publish'),
	        'posts_per_page' => -1,
	        'meta_key' => 'MatchDate',
	        'orderby'   => 'meta_value',
	        'order'     => 'ASC',
	        'meta_query' => $meta_query_array
	    );
	    
	    
		$the_query = new WP_Query( $guest_arg );
		
		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
		 	$the_query->the_post();
            $prod_id = get_the_ID();


            	$country = trim(get_post_meta( $prod_id, 'match_country', true));
				if( '' != $country ){
					$haveFan_all_country[] = $country;
				}
				$city = trim(get_post_meta( $prod_id, 'match_city', true));
				if( '' != $city ){
					$haveFan_all_city[] = $city;
				}
				$date = trim(get_post_meta( $prod_id, 'MatchDate', true));
				if( '' != $date ){
					$haveFan_all_dates[] = date('j-n-Y',strtotime(trim($date))) ;
				}
				$Team1 = trim(get_post_meta( $prod_id, 'Team1', true));
				$Team2 = trim(get_post_meta( $prod_id, 'Team2', true));
				if( '' != $Team1 ){
					$haveFan_all_team[] = $Team1;
				}
				if( '' != $Team2 ){
					$haveFan_all_team[] = $Team2;
				}
				
			}
		}
		wp_reset_postdata();
		 


		// $all_city_list = array_unique($default_city_list);
		$all_date_array = array_unique($default_date_array);
		$available_country_array = array_unique($haveFan_all_country);
		$available_city_array = array_unique($haveFan_all_city);
		$available_date_array = array_unique($haveFan_all_dates);
		$available_team_list = array_unique($haveFan_all_team);

		sort($available_country_array);
		sort($available_city_array);
		sort($available_date_array);
		sort($available_team_list);

		//** get all list from the current date
		$curret_date = date('Y-m-d');
		$meta_query_array1 = array('relation' => 'AND',array(
            'key' => 'MatchDate',
            'value' => $curret_date,
            'compare' => '>=',
            'type' => 'DATE',
            )
		);
		$guest_arg1 = array(
	        'post_type' => 'product',
	        'status' => array('publish'),
	        'posts_per_page' => -1,
	        'meta_key' => 'MatchDate',
	        'orderby'   => 'meta_value',
	        'order'     => 'ASC',
	        'meta_query' => $meta_query_array1
	    );
	    
	    
		$the_query1 = new WP_Query( $guest_arg1 );
		$full_country_array = array();
		$full_city_array = array();
		$full_teams_array = array();
		$full_dates_array = array();
		if ( $the_query1->have_posts() ) {
			while ( $the_query1->have_posts() ) {
		 	$the_query1->the_post();
            $prod_id1 = get_the_ID();


            	$country = trim(get_post_meta( $prod_id1, 'match_country', true));
				if( '' != $country ){
					$full_country_array[] = $country;
				}
				$city = trim(get_post_meta( $prod_id1, 'match_city', true));
				if( '' != $city ){
					$full_city_array[] = $city;
				}
				$date = trim(get_post_meta( $prod_id1, 'MatchDate', true));
				if( '' != $date ){
					$full_dates_array[] = date('j-n-Y',strtotime(trim($date))) ;
				}
				$Team1 = trim(get_post_meta( $prod_id1, 'Team1', true));
				$Team2 = trim(get_post_meta( $prod_id1, 'Team2', true));
				if( '' != $Team1 ){
					$full_teams_array[] = $Team1;
				}
				if( '' != $Team2 ){
					$full_teams_array[] = $Team2;
				}
				
			}
		}
		wp_reset_postdata();
		$full_country_list = array_unique($full_country_array);
		$full_city_list = array_unique($full_city_array);
		$full_teams_list = array_unique($full_teams_array);
		$full_dates_list = array_unique($full_dates_array);

		sort($full_country_list);
		sort($full_city_list);
		sort($full_teams_list);
		sort($full_dates_list);
				 
	?>
	 <div class="<?php if($atts['layout']=='horizontal'){ echo 'default-search-box'; } ?>  um-shadow <?php echo $form_class;?>">
	 	 
	 	<?php 
  		$all_teams = $wpdb->get_results( "SELECT match_hometeam_name FROM $event_Table WHERE match_hometeam_name IS NOT NULL UNION SELECT match_awayteam_name FROM $event_Table WHERE match_awayteam_name IS NOT NULL ORDER BY match_hometeam_name  " );
  		$default_all_teams_array = array();
  		foreach ($all_teams as $team_val) {
  			$default_all_teams_array[] = $team_val->match_hometeam_name;
  		}	



	 	?>
	 	<input type="hidden" name="" id="dateJson" value='<?php echo json_encode($all_date_array); ?>'>
	 	<input type="hidden" name="" id="default_all_teams_data" class="default_all_teams_data" value='<?php echo json_encode($default_all_teams_array); ?>'>
	 	<input type="hidden" name="" id="available_date_array" class="available_date_array" value='<?php echo json_encode($available_date_array); ?>'>
	 	<input type="hidden" name="" id="available_city_array" class="available_city_array" value='<?php echo json_encode($available_city_array); ?>'>
	 	<input type="hidden" name="" id="available_team_list" class="available_team_list" value='<?php echo json_encode($available_team_list); ?>'>

	 	<?php if($atts['layout']=='horizontal'){

           ?>
           <form class="default-search-form" method="GET" action="<?php echo site_url('event?view=list');?>">
           	<input type="hidden" name="view" value="list"/>
			
			<div class="horizontal-search-form">
				<label><i class="fa fa-globe"></i></label>
	           
				<select name="by_country"  id="by_country" class="um-form-field valid not-required um-s1">

					<option value="" selected="" disable="" >All</option>
					<?php
					foreach ($available_country_array as $value) {
	            		echo '<option value="'.$value.'">'.$value.'</option>';
	            	}
	            	?>
				</select>
			</div>
			<div class="horizontal-search-form">
				<label><i class="fa fa-building"></i></label>
				<?php //all_city_list ?>
				<p>
				
				<select name="by_city" id="by_city" class="um-form-field valid not-required um-s1">
					<option value="">All</option>
					<?php
						 foreach ($available_city_array as $ev_city) {
							$seleced = ( $selected_city == trim($ev_city)) ? 'selected' : '';
							 echo '<option '. $seleced.' value="'.trim($ev_city).'">'.$ev_city.'</option>';
						}
						?>
				</select> 
				</p>
				
			</div>
			<div class="horizontal-search-form">
				<label><i class="fa fa-futbol"></i></label>
				<p>
					<select name="by_team" id="by_team" class="um-form-field valid not-required um-s1">
					<option value="">All</option>
						<?php
						 foreach ($available_team_list as $ev_team) {

							$seleced = ( $selected_team == trim($ev_team)) ? 'selected' : '';
							 echo '<option '. $seleced.' value="'.trim($ev_team).'">'.$ev_team.'</option>';
						}
						
						?>
				</select>
				</p>
			</div>
			<div class="horizontal-search-form">
				<label><i class="fa fa-clock "></i></label>
				<p><input type="text" placeholder="Date" autocomplete="off" name="by_date" id="by_date" value="<?php echo $selected_date;?>"></p>
				<?php
				      $view='calendar';
	                  if($_GET['view']=='list'){
	                   $view='list';
	                  }
				 ?>
			</div>
			
			<input type="submit" value="Search">
		</form>
           <?php
	 	}else{
	 		?>
	 		<div class="toggle-header">
	 			<p class="toggle-sidebar-event">Search Experiance <span class="down-sidebar"><i class="fa fa-angle-down"></i></span> <span class="up-sidebar" style="display: none"><i class="fa fa-angle-up"></i></span></p>
	 		</div>
            <div class="sidebar-toggle-wraper" >
	 		<form class="default-search-form" method="GET" action="<?php echo site_url('event');?>">
			
			<div class="sidebar-search-form">
				<label>Country</label>
				
				<?php $all_country = $wpdb->get_results( "SELECT * FROM $postmeta_table where `meta_key`='match_country'  group by `meta_value`" );
			    ?>
				<select name="by_country" id="by_country" class="um-form-field um-s1">

					<option value="" >All</option>
				 <?php foreach ($all_country as $ev_country) {

						$seleced = ( $selected_country == trim($ev_country->meta_value)) ? 'selected' : '';
						
					 echo '<option '.$seleced.' value="'.trim($ev_country->meta_value).'">'.trim($ev_country->meta_value).'</option>';
				 }
				?>
				 </select> 
			</div>
			<div class="sidebar-search-form">
				<label>City</label>
				 
		 		<select name="by_city" id="by_city" class="um-form-field valid not-required um-s1">
					<option value="">All</option>
					<?php 
					if(isset($_GET['by_country']) && '' == trim($_GET['by_country'])){
		 				foreach ($full_city_list as $ev_city) {
							$seleced = ( $selected_city == trim($ev_city)) ? 'selected' : '';
						 	echo '<option '. $seleced.' value="'.trim($ev_city).'">'.$ev_city.'</option>';
						}
					}else{
						foreach ($available_city_array as $ev_city) {
							$seleced = ( $selected_city == trim($ev_city)) ? 'selected' : '';
						 	echo '<option '. $seleced.' value="'.trim($ev_city).'">'.$ev_city.'</option>';
						}
					}
					 
				?>
				</select> 
			</div>
			<div class="sidebar-search-form">
				<label>Team</label>
				<select name="by_team" id="by_team" class="um-form-field um-s1">
				   <option value="">All</option>
				<?php 
				if(isset($_GET['by_city'], $_GET['by_country']) && '' == trim($_GET['by_city']) && '' == trim($_GET['by_country'])){
		 				foreach ($full_teams_list as $ev_team) {
						$seleced = ( $selected_team == trim($ev_team)) ? 'selected' : '';
						echo '<option '. $seleced.' value="'.trim($ev_team).'" class="12">'.trim($ev_team).'</option>';
					}
				}else{
					foreach ($available_team_list as $ev_team) {
						$seleced = ( $selected_team == trim($ev_team)) ? 'selected' : '';
						echo '<option '. $seleced.' value="'.trim($ev_team).'" class="13">'.trim($ev_team).'</option>';
					}
				}
				
				?>
			   </select>
			</div>

			<div class="sidebar-search-form">
				<label>Date</label>
				<input type="text" placeholder="Date" autocomplete="off" name="by_date" id="by_date" value="<?php echo $selected_date;?>">
			</div>
			
			<?php
			      $view='calendar';
                  if($_GET['view']=='list'){
                   $view='list';
                  }
			 ?>
			<input type="hidden" name="view" id="list_view_selector" value="<?php echo $view; ?>">
			<div class="sidebar-search-form um-submit-btn">
				<input type="submit" value="Search">
			</div>
		</form>
	</div>
		<?php 
	 	} ?>
	 	
	 </div>
	<?php
	$output .= ob_get_contents();
	ob_end_clean();
	return $output;

}
add_shortcode('default_hf_search_form', 'default_hf_search_form');
function um_become_host_form( ){
	ob_start();
	global $wpdb;
	$event_Table = $wpdb->prefix.'events';
	$user_id = get_current_user_id();
	?>
	<!-- Model PopUp Start-->
<div id="BecomeHostModal" class="HaveFunModal">

  <!-- Edit Event Modal content -->
  <div class="havefun-content">
    <span class="close">&times;</span>
    <div class="havefun-content-wrap">
       <div class="um-shadow-header">
			 <h6>Registration for host form</h6>
	   </div>

        <form id="become-host-form" method="post" action="#">		
			<div class="acf-fields acf-form-fields -top">
				<div class="acf-field acf-field-text">
					<div class="acf-label">
						<label>Full Name</label>
					</div>
					<div class="acf-input">
						<div class="acf-input-wrap">
							<?php 
							$f_name = get_user_meta( $user_id, 'first_name', true);
							$l_name = get_user_meta( $user_id, 'last_name', true);
							$full_name = $f_name.' '.$l_name;
							?> 
							<input style="width: 100%;" type="text" name="become_fullname" id="become_fullname" value="<?php echo $full_name;?>" readonly="">						
							<input type="hidden" name="become_fname"  value="<?php echo $f_name;?>" id="become_fname">
							<input type="hidden" name="become_lname" value="<?php echo $l_name;?>" id="become_lname">	
							<input type="hidden" name="become_user_id" value="<?php echo $user_id;?>" id="become_user_id">	
						</div>
					</div>
				</div>
				<div class="acf-field acf-field-text">
					<div class="acf-label">
						<label>Contact No.</label>
					</div>
					<div class="acf-input">
						<div class="acf-input-wrap">
							<input style="width: 100%;" type="number" min="0"  id="become_contact" name="become_contact" value="" />							
						</div>
					</div>
				</div>
				<div class="acf-field acf-field-text">
					<div class="acf-label">
						<label>Biography</label>
					</div>
					<div class="acf-input">
						<div class="acf-input-wrap">
							<textarea style="width: 100%;" name="become-biography"></textarea>						
						</div>
					</div>
				</div>
				<div class="acf-field acf-field-text">
					<div class="acf-label">
						<label>Message</label>
					</div>
					<div class="acf-input">
						<div class="acf-input-wrap">
							<textarea style="width: 100%;" id="become-message" name="become-message"></textarea>						
						</div>
					</div>
				</div>
				<div class="acf-field acf-field-text">
					<div class="acf-label">
						<label>Stadium</label>
					</div>
					<div class="acf-input">
						<div class="acf-input-wrap">
							<select style="width: 100%;" required="" id="become_stadium" name="become_stadium" multiple="" class="um-form-field um-s1">
								<option value="" disable="" ></option>
								<option value="Tribuna">Tribuna</option>
								<option value="Centrale">Centrale</option>
								<option value="Non So">Non So</option>
							</select>
							
						</div>
					</div>
				</div>

				<div class="acf-field acf-field-text">
					<div class="acf-label">
						<label>Team</label>
						
					</div>
					<div class="acf-input">
						<div class="acf-input-wrap">
							<select style="width: 100%;"  id="become_team" name="become_team"class="um-form-field um-s1" required="">
								 
								<?php 
								$all_teams = $wpdb->get_results( "SELECT match_hometeam_name FROM $event_Table WHERE match_hometeam_name IS NOT NULL UNION SELECT match_awayteam_name FROM $event_Table WHERE match_awayteam_name IS NOT NULL ORDER BY match_hometeam_name  " );
								foreach ($all_teams as $ev_team) {
										echo '<option  value="'.trim($ev_team->match_hometeam_name).'">'.$ev_team->match_hometeam_name.'</option>';
									}
								?>
								 
							</select>
						</div>
					</div>
				</div>

				<div class="acf-field acf-field-text">
					<div class="acf-label">
						<label>Country</label>
						
					</div>
					<div class="acf-input">
						<div  class="acf-input-wrap">
						<?php require_once('country-city.php');
		            		$country_city_list = (array) json_decode($country_city);
		            	?>
						<select style="width: 100%;"  name="become_country" required="" id="become_country" class="um-form-field um-s1">
							<option disabled="" selected="">Select Country</option>
							 
							<?php
							foreach ($country_city_list as $key => $value) {
								$seleced = ( $selected_country == trim($key)) ? 'selected' : '';
			            		echo '<option '.$seleced.' value="'.$key.'">'.$key.'</option>';
			            	}
			            	?>
						</select>
						</div>
					</div>
				</div>
				<div class="acf-field acf-field-text">
					<div class="acf-label">
						<label>City</label>
					</div>
					<div class="acf-input">
						<div class="acf-input-wrap">
							<select style="width: 100%;"  id="become_city" name="become_city"class="um-form-field um-s1" required="">
								 
							</select>
						</div>
					</div>
				</div>
				<input type="hidden" name="send-request" id="send-request" value="Yes" />
				<br/>
				 <div class="acf-field acf-field-text">
				 	<input type="submit" name="submit_become_host" value="Register Now"/>
				 </div>
			</div>		
		</form> 

    </div>
  </div>

</div>
<!-- Edit Event Model PopUp End -->
	
	<?php
		
	$output .= ob_get_contents();
	ob_end_clean();
	return $output;
}
add_shortcode('um_become_host_form', 'um_become_host_form');
add_filter( 'woocommerce_get_availability', 'havefan_wcs_custom_get_availability', 1, 2);
function havefan_wcs_custom_get_availability( $availability, $_product ) {
    
    // Change In Stock Text
    
    if ( $_product->is_in_stock() ) {
    	$stock_message = 'Max People: ' .$_product->get_stock_quantity();
        $availability['availability'] = __($stock_message, 'woocommerce');
    }
    // Change Out of Stock Text
    if ( ! $_product->is_in_stock() ) {
        $availability['availability'] = __('Sold Out', 'woocommerce');
    }
    return $availability;
}
?>