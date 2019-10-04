<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Fetch all upcoming event a host.
 * @version 1.0.0
 * @return json
 *
 */
function get_upcoming_event(){
	global $wpdb;
	if(isset($_POST['paged'], $_POST['host_status'], $_POST['wp_nonce']) && wp_verify_nonce( $_POST['wp_nonce'], 'hfwc-fro-form-ajax' )) {
		$table_name = $wpdb->prefix ."events";
		$profile_id = trim( $_POST['host_status'] );
		$profile_info = get_user_by('id', $profile_id);
		$paged = 1;
		if(isset($_POST['paged'])){
			$paged = trim($_POST['paged']);
			$paged = ( $paged > 0) ? $paged : 1; 
		}
		$paged = $paged;
		$user_country = trim(get_user_meta( $profile_id, 'country', true ));
		$event_city = trim(get_user_meta( $profile_id, 'user-citys', true ));
	    $event_address_array =  array();
	    if( $event_city != '' ){
	        $event_address_array[] = $event_city;
	    }
	    if( $user_country != ''  ){
	        $event_address_array[] = $user_country;
	    }
	     $event_address = implode(' - ', $event_address_array);
		$user_team = trim(get_user_meta( $profile_id, 'team-names', true ));
		$editable = trim( $_POST['editable'] == 'true' ) ? 'yes' : 'no';
		
		$per_page = 5;
		$curret_date = date('Y-m-d');
		$rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE date(match_date) >= '".$curret_date."' AND `country_name` ='".$user_country."' AND `match_hometeam_name` = '".$user_team."' ");

	 
		$offset = ($paged-1) * $per_page;  	 
		$events = $wpdb->get_results("SELECT * FROM $table_name WHERE date(match_date) >= '".$curret_date."' AND `country_name` ='".$user_country."' AND `match_hometeam_name` = '".$user_team."' limit $offset,  $per_page ");
		$host_avatar = get_avatar_url( $profile_id );
		$max_people = get_user_meta( $profile_id , "max_people" ,true);
		$minimum_age = get_user_meta( $profile_id , "minimum_age" ,true);
		$output_string = '';
		if( $events ){
			foreach ($events as $event) {
				$tbl_post = $wpdb->prefix.'posts';
  				$tbl_postemta = $wpdb->prefix.'postmeta';
  				$product_id = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS $tbl_post.ID  FROM $tbl_post INNER JOIN hf_postmeta ON ( $tbl_post.ID = $tbl_postemta.post_id ) WHERE 1=1 AND $tbl_post.post_author IN (".$profile_id.") AND ( ( $tbl_postemta.meta_key = 'match_id' AND $tbl_postemta.meta_value = '".$event->match_id."' ) ) AND $tbl_post.post_type = 'product' AND (($tbl_post.post_status = 'publish' OR $tbl_post.post_status = 'draft')) GROUP BY $tbl_post.ID ORDER BY $tbl_post.post_date DESC LIMIT 0, 1");
  				global $woocommerce;
           
		        if( ! empty($product_id) ){
		            $product_data = new WC_Product( $product_id[0]->ID );
		            $product_price = $product_data->get_price_html();
		        }else{
		            $base_price  = get_user_meta( $profile_id, 'include_services_base_price', true);
		            $product_price = wc_price($base_price);
		         }
  				$event_title = $user_team . ' VS '.$event->match_awayteam_name;
				
				$output_string .="<div class='upcoming-event-data upcoming-host-event event-list-".$event->match_id."'>";
				$output_string .="<div class='evnt-action-wrap'>";
				if($profile_id==get_current_user_id() && $editable == 'yes') {
	  				

		  			if( empty($product_id) ){
		  			$output_string .="<span id='event_action_btn_".$event->match_id."' data-val='".$event->match_id."' class='fa fa-plus event_action_btn' title='Create Experience'>
						</span>";
		  			}else{
		  				$pro_stock = (int) get_post_meta( $product_id[0]->ID, '_stock', true );
            			if( $pro_stock  == $max_people ){
            				$pro_url = site_url('user/'.$profile_info->user_login.'/?profiletab=experience&subtab=update-event&um_info_action=edit&edit_experince_id='.$product_id[0]->ID);

            				$output_string .="<span id='event_action_btn_".$event->match_id."' data-id='".$profile_id."' data-val='".$product_id[0]->ID."' class='fa fa-edit edit_event_action_btn'  title='Edit Experience'>
                      </span>";
            			
            			 
            			}
		  			}
		  		}
				$output_string .="</div>";
	            $output_string .="<div class='event-title-wrap'>
	            	<div class='event-title-left'>
                       <h5 class='event-title' data-title='".$event_title."' data-city='".$event_city."''>".$event_title."</h5>
                    </div>
                    <div class='event-title-right'><span class='price-heading'>Price</span>".$product_price."</div>
              </div>
			  		<div class='event-list'>";
			  		
			  		$output_string .="<p><span><strong class='fa fa-marker'>Location : </strong>" . $event_address." </span></p>
			  		<div class='event-footer-wrap'>";
			   	$output_string .="<p><span><strong class='fa fa-date'>Date &amp; Time : </strong>".date('d F Y', strtotime($event->match_date)). " " . $event->match_time."</span></p>
					</div>
			  	</div>
			  </div>";
			}
		}
		echo json_encode(array('status' => 'success', 'message' => 'successfully', 'response' => $output_string));
	exit();
	}else{
		echo json_encode(array('status' => 'error', 'message' => 'Invalid Request', 'response' => ''));
		exit();
	}

}
add_action( 'wp_ajax_get_upcoming_event', 'get_upcoming_event');
add_action( 'wp_ajax_nopriv_get_upcoming_event', 'get_upcoming_event');

/**
 * Fetch all upcoming event for a single host. For other user or guest
 * @version 1.0.0
 * @return json
 *
 */

function get_guest_upcoming_matches(){
	global $wpdb;
	if(isset($_POST['paged'], $_POST['host_status'], $_POST['wp_nonce']) && wp_verify_nonce( $_POST['wp_nonce'], 'hfwc-fro-form-ajax' )) {
		$table_name = $wpdb->prefix ."events";
		$profile_id = trim( $_POST['host_status'] );
		$paged = 1;
		if(isset($_POST['paged'])){
			$paged = trim($_POST['paged']);
			$paged = ( $paged > 0) ? $paged : 1; 
		}
		$paged = $paged;
		 
		$curret_date = date('Y-m-d');
		 
		$host_avatar = get_avatar_url( $profile_id );
		
		$guest_arg = array(
        'post_type' => 'product',
        'status' => array('publish'),
        'author' => $profile_id,
        'posts_per_page' => 5,
        'paged' => $paged,
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
		$output_string = '';
		if ( $the_query->have_posts() ) {
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
	            $table_name = $wpdb->prefix.'events';
	            $match_id_detail = $wpdb->get_row("SELECT * FROM $table_name WHERE `match_id` = '".$match_id."' ");
				$default_trophy = '';
				$product_data = new WC_Product( $prod_id );
				if(isset( $match_id_detail->league_name ) ){
				    $default_trophy = $match_id_detail->league_name;
				}
				$output_string .="<div class='upcoming-event-data upcoming-guest-event event-list-".$prod_id."'>
	                <div class='event-title-wrap'>
                    <div class='event-title-left'>
                       <h5 class='event-title'>".get_the_title()."</h5>
                    </div>
                    <div class='event-title-right'>
                      <span class='price-heading'>Price</span>".$product_data->get_price_html()."
                    </div>
              		</div>
			  		<div class='event-list'>
			  		<div class='event-list-mid'>
			  		 	<p><span><strong class='fa fa-users'>Max People : </strong>" . $product_data->get_stock_quantity()." </span></p>
			  		 	<p><span><strong class='fa fa-date'>Date &amp; Time : </strong>".date('d F Y', strtotime($MatchDate)). " " . $MatchTime."</span></p>
			  		</div>
			  		<div class='event-footer-wrap'>
			  		<a  href='".get_permalink($pro_id)."' class='um-readmore-btn'  title='Message'>
              <span>Book Now</span>
              </a>
			  		</div>";
			   	$output_string .="
					</div>
			  	</div>
			  </div>";
			}
		}
		wp_reset_postdata();
		echo json_encode(array('status' => 'success', 'message' => 'successfully', 'response' => $output_string));
	exit();
	}else{
		echo json_encode(array('status' => 'error', 'message' => 'Invalid Request', 'response' => ''));
		exit();
	}

}
add_action( 'wp_ajax_get_guest_upcoming_matches', 'get_guest_upcoming_matches');
add_action( 'wp_ajax_nopriv_get_guest_upcoming_matches', 'get_guest_upcoming_matches');

/**
 * Create a product for a host.
 * @version 1.0.0
 * @return json
 *
 */
function create_host_product(){
	if(isset($_POST['hv_title'])){
		global $wpdb;
	 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
			$title = sanitize_text_field( trim( $_POST['hv_title'] ) );
			$hv_descr = sanitize_text_field(trim( $_POST['hv_descr']) );
			$hv_match_id = sanitize_text_field(trim( $_POST['hv_host_event_id']) );
			$host_id = trim($_POST['hv_host_id']);
			$product_img = get_field('banner_image', 'user_'.$host_id);
			$event_city = trim(get_user_meta( $host_id, 'user-citys', true ));
			$product_img_url = get_avatar_url( $host_id );
		 	$default_cover = UM()->options()->get( 'default_cover' );
			if(isset($product_img['url'])){
				$product_img = $product_img['url'];
			}else if( $default_cover && $default_cover['url'] ) {
				$product_img = $default_cover['url'];
			}else {
				$product_img = $product_img_url;
			}
			// $host_avatar = get_avatar_url( $host_id );
			$max_people = get_user_meta( $host_id , "max_people" ,true);
	 		$minimum_age = get_user_meta( $host_id , "minimum_age" ,true);
			$price = sanitize_text_field( trim( $_POST['hv_price'] ) );
			$basic_price = $price;
			// total Price
			$product_addon = array();
			$included_service = array();
			$k = 0;
			if(isset($_POST['include_food_services'])){
				$ex_temp = array();
				$ex_temp_title = array();
				foreach ($_POST['include_food_services'] as $value) {
					if( '' !=  trim($value) ){
						$ex_temp[] = $value;
						$ex_temp_title[] = get_the_title($value);
					}
					
				}
				if( !empty($ex_temp)){
					$temp = array();   
					$content = implode(', ', $ex_temp_title);
					$temp['name'] = 'Food';
					$temp['title_format'] = 'label';
					$temp['description_enable'] = 1;
					$temp['description'] = $content;
					$temp['type'] = 'custom_text';
					$temp['display'] = 'select';
					$temp['position'] = $k;
					$temp['required'] = 1;
					$temp['restrictions'] = 0;
					$temp['restrictions_type'] = 'any_text';
					$temp['adjust_price'] = 0;
					$temp['price_type'] = 'flat_fee';
					$temp['price'] = '';
					$temp['min'] = 0;
					$temp['max'] = 0;
					$temp['options'] = array( array(
						'label' => '',
						'price' => '',
						'image' => '',
						'price_type' => 'flat_fee')
						);
					$k++;
					$product_addon[] = $temp;
				}
				$included_service['food_services'] = $ex_temp;
			}
			if(isset($_POST['include_drink_services'])){
				$ex_temp = array();
				$ex_temp_title = array();
				foreach ($_POST['include_drink_services'] as $value) {
					if( '' !=  trim($value) ){
						$ex_temp[] = $value;
						$ex_temp_title[] = get_the_title($value);
					}
				}
				if( !empty($ex_temp)){
					$temp = array();   
					$content = implode(', ', $ex_temp_title);
					$temp['name'] = 'Drink';
					$temp['title_format'] = 'label';
					$temp['description_enable'] = 1;
					$temp['description'] = $content;
					$temp['type'] = 'custom_text';
					$temp['display'] = 'select';
					$temp['position'] = $k;
					$temp['required'] = 1;
					$temp['restrictions'] = 0;
					$temp['restrictions_type'] = 'any_text';
					$temp['adjust_price'] = 0;
					$temp['price_type'] = 'flat_fee';
					$temp['price'] = '';
					$temp['min'] = 0;
					$temp['max'] = 0;
					$temp['options'] = array( array(
						'label' => '',
						'price' => '',
						'image' => '',
						'price_type' => 'flat_fee')
						);
					$k++;
					$product_addon[] = $temp;
				}
				$included_service['drink_services'] = $ex_temp;
			}
			
			if(isset($_POST['include_ticket_services'])){
				$ex_temp = array();
				$ex_temp_title = array();
				foreach ($_POST['include_ticket_services'] as $value) {
					if( '' !=  trim($value) ){
						$ex_temp[] = $value;
						$ex_temp_title[] = get_the_title($value);
					}
				}
				if( !empty($ex_temp)){
					$temp = array();   
					$content = implode(', ', $ex_temp_title);
					$temp['name'] = 'Tickets';
					$temp['title_format'] = 'label';
					$temp['description_enable'] = 1;
					$temp['description'] = $content;
					$temp['type'] = 'custom_text';
					$temp['display'] = 'select';
					$temp['position'] = $k;
					$temp['required'] = 1;
					$temp['restrictions'] = 0;
					$temp['restrictions_type'] = 'any_text';
					$temp['adjust_price'] = 0;
					$temp['price_type'] = 'flat_fee';
					$temp['price'] = '';
					$temp['min'] = 0;
					$temp['max'] = 0;
					$temp['options'] = array( array(
						'label' => '',
						'price' => '',
						'image' => '',
						'price_type' => 'flat_fee')
						);
					$k++;
					$product_addon[] = $temp;
				}
				$included_service['ticket_services'] = $ex_temp;
			}	 
			if(isset($_POST['include_ticket_services'])){
				$ex_temp = array();
				$ex_temp_title = array();
				foreach ($_POST['include_ticket_services'] as $value) {
					if( '' !=  trim($value) ){
						$ex_temp[] = $value;
						$ex_temp_title[] = get_the_title($value);
					}
				}
				if( !empty($ex_temp)){
					$temp = array();   
					$content = implode(', ', $ex_temp_title);
					$temp['name'] = 'Tickets';
					$temp['title_format'] = 'label';
					$temp['description_enable'] = 1;
					$temp['description'] = $content;
					$temp['type'] = 'custom_text';
					$temp['display'] = 'select';
					$temp['position'] = $k;
					$temp['required'] = 1;
					$temp['restrictions'] = 0;
					$temp['restrictions_type'] = 'any_text';
					$temp['adjust_price'] = 0;
					$temp['price_type'] = 'flat_fee';
					$temp['price'] = '';
					$temp['min'] = 0;
					$temp['max'] = 0;
					$temp['options'] = array( array(
						'label' => '',
						'price' => '',
						'image' => '',
						'price_type' => 'flat_fee')
						);
					$k++;
					$product_addon[] = $temp;
				}
				$included_service['ticket_services'] = $ex_temp;
			} 
			if(isset($_POST['include_transport_services'])){
				$ex_temp = array();
				$ex_temp_title = array();
				foreach ($_POST['include_transport_services'] as $value) {
					if( '' !=  trim($value) ){
						$ex_temp[] = $value;
						$ex_temp_title[] = get_the_title($value);
					}
				}
				if( !empty($ex_temp)){
					$temp = array();   
					$content = implode(', ', $ex_temp_title);
					$temp['name'] = 'Transport';
					$temp['title_format'] = 'label';
					$temp['description_enable'] = 1;
					$temp['description'] = $content;
					$temp['type'] = 'custom_text';
					$temp['display'] = 'select';
					$temp['position'] = $k;
					$temp['required'] = 1;
					$temp['restrictions'] = 0;
					$temp['restrictions_type'] = 'any_text';
					$temp['adjust_price'] = 0;
					$temp['price_type'] = 'flat_fee';
					$temp['price'] = '';
					$temp['min'] = 0;
					$temp['max'] = 0;
					$temp['options'] = array( array(
						'label' => '',
						'price' => '',
						'image' => '',
						'price_type' => 'flat_fee')
						);
					$k++;
					$product_addon[] = $temp;
				}
				$included_service['transport_services'] = $ex_temp;
			} 
    		if(isset($_POST['include_tool_services'])){
				$ex_temp = array();
				$ex_temp_title = array();
				foreach ($_POST['include_tool_services'] as $value) {
					if( '' !=  trim($value) ){
						$ex_temp[] = $value;
						$ex_temp_title[] = get_the_title($value);
					}
				}
				if( !empty($ex_temp)){
					$temp = array();   
					$content = implode(', ', $ex_temp_title);
					$temp['name'] = 'Tools';
					$temp['title_format'] = 'label';
					$temp['description_enable'] = 1;
					$temp['description'] = $content;
					$temp['type'] = 'custom_text';
					$temp['display'] = 'select';
					$temp['position'] = $k;
					$temp['required'] = 1;
					$temp['restrictions'] = 0;
					$temp['restrictions_type'] = 'any_text';
					$temp['adjust_price'] = 0;
					$temp['price_type'] = 'flat_fee';
					$temp['price'] = '';
					$temp['min'] = 0;
					$temp['max'] = 0;
					$temp['options'] = array( array(
						'label' => '',
						'price' => '',
						'image' => '',
						'price_type' => 'flat_fee')
						);
					$k++;
					$product_addon[] = $temp;
				}
				$included_service['tool_services'] = $ex_temp;
			} 
			if(isset($_POST['include_other_services'])){
				$ex_temp = array();
				$ex_temp_title = array();
				foreach ($_POST['include_other_services'] as $value) {
					if( '' !=  trim($value) ){
						$ex_temp[] = $value;
						$ex_temp_title[] = get_the_title($value);
					}
				}
				if( !empty($ex_temp)){
					$temp = array();   
					$content = implode(', ', $ex_temp_title);
					$temp['name'] = 'Others';
					$temp['title_format'] = 'label';
					$temp['description_enable'] = 1;
					$temp['description'] = $content;
					$temp['type'] = 'custom_text';
					$temp['display'] = 'select';
					$temp['position'] = $k;
					$temp['required'] = 1;
					$temp['restrictions'] = 0;
					$temp['restrictions_type'] = 'any_text';
					$temp['adjust_price'] = 0;
					$temp['price_type'] = 'flat_fee';
					$temp['price'] = '';
					$temp['min'] = 0;
					$temp['max'] = 0;
					$temp['options'] = array( array(
						'label' => '',
						'price' => '',
						'image' => '',
						'price_type' => 'flat_fee')
						);
					 
					$k++;
					$product_addon[] = $temp;
				}
				$included_service['other_services'] = $ex_temp;
			} 
			 
			$data_array = array(
				'name' => $title,
				'type' => 'simple',
				'virtual' => true,
				'regular_price' => $price,
				 "images" => array(  array( 'src' => $product_img )),
				 "manage_stock" => true,
				 "stock_quantity" => trim(($max_people)),
				 "stock_status" => "instock",
				 "sold_individually" => false,
				'description' => $hv_descr,
				 
			);
			 
			$HaveAPI =  new HaveFun_RestAPI();
			// $output = $HaveAPI->create_product_wcfm_api( $data_array, $host_id );
			$output = $HaveAPI->create_product_wchf_api( $data_array, $host_id );
			 
			$output = json_decode($output,  true);
			 
			if(isset($output['id'])){
    			$new_post_id = $output['id'];
				$arg = array(
			    	'ID' => $new_post_id,
			    	'post_author' => $host_id,
					);
				wp_update_post( $arg );
    			$table_name = $wpdb->prefix.'events';
    			$match_details = $wpdb->get_results("SELECT * FROM $table_name WHERE `match_id` = '".$hv_match_id."' LIMIT 1");
    			update_post_meta( $new_post_id, 'MaximumPeople', $max_people);
    			update_post_meta( $new_post_id, 'MinimumAge', $minimum_age);
    			update_post_meta( $new_post_id, 'match_city', $event_city );
    			foreach ($match_details as $match ) {
    				update_post_meta( $new_post_id, 'MatchTime', $match->match_time);
    				update_post_meta( $new_post_id, 'MatchDate', $match->match_date);
    				update_post_meta( $new_post_id, 'Team1', $match->match_hometeam_name);
    				update_post_meta( $new_post_id, 'Team2', $match->match_awayteam_name);
    				update_post_meta( $new_post_id, 'match_country', $match->country_name);
    				
    				update_post_meta( $new_post_id, 'WooCommerceEventsDate', date('F d, Y', strtotime($match->match_date)));
					update_post_meta( $new_post_id, 'WooCommerceEventsLocation', $match->country_name);
					$dd_string = $match->match_date .' '.$match->match_time;
					$dd_h = date('h', strtotime($dd_string));
					$dd_m = date('i', strtotime($dd_string));
					$dd_a = date('a', strtotime($dd_string));
					$dd_dm = implode('.', str_split($dd_a)).'.';
					update_post_meta( $new_post_id, 'WooCommerceEventsHour', $dd_h);
					update_post_meta( $new_post_id, 'WooCommerceEventsMinutes', $dd_m);
					update_post_meta( $new_post_id, 'WooCommerceEventsPeriod', $dd_dm);
    			}
    			$userInfo = get_user_by( 'ID', $host_id );
				update_post_meta( $new_post_id, '_wcfm_new_product_notified', 'yes');
				update_post_meta( $new_post_id, 'wcfm_policy_product_options', array(''));
				update_post_meta( $new_post_id, '_backorders', 'no');
				// update_post_meta( $new_post_id, '_sold_individually', 'no');
				// update_post_meta( $new_post_id, '_stock_status', 'instock');
				// update_post_meta( $new_post_id, '_stock', trim(($max_people)));
				update_post_meta( $new_post_id, '_wcfm_product_author', $host_id);
				update_post_meta( $new_post_id, 'WooCommerceEventsEmail', $userInfo->user_email);
				update_post_meta( $new_post_id, 'WooCommerceEventsSupportContact', '');
				update_post_meta( $new_post_id, 'WooCommerceEventsEvent', 'Event');
				update_post_meta( $new_post_id, '_basic_price', $basic_price);
				update_post_meta( $new_post_id, 'match_id', $hv_match_id);
				update_post_meta( $new_post_id, '_included_service', $included_service);
    			update_post_meta( $new_post_id, '_product_addons', $product_addon);
    			update_post_meta( $new_post_id, '_product_addons_exclude_global', 0);
    			$redirect_url = site_url('user/'.$userInfo->user_login.'/?profiletab=next-matches&edit_um_event=edit');
    			echo json_encode(array('status' => 'success', 'message' => 'Ticket successfully created', 'response' => $new_post_id, 'redirect_url' => $redirect_url));
    			exit();
    		}else{
    			echo json_encode(array('status' => 'error', 'message' => 'Product is not created', 'response' => ''));
				exit();
    		}
	}
	echo json_encode(array('status' => 'error', 'message' => 'Invalid Request', 'response' => ''));
	exit();
	
}
add_action( 'wp_ajax_create_host_product', 'create_host_product');

/**
 * Update a single product for a host/product owner
 * @version 1.0.0
 * @return json
 *
 */

function update_host_product(){
	if(isset($_POST['u_hv_title'], $_POST['u_hv_host_id'], $_POST['u_hv_host_event_id'], $_POST['u_experience_id'])){
		global $wpdb;
	 
 
		$title = sanitize_text_field( trim( $_POST['u_hv_title'] ) );
		$post_content = sanitize_text_field(trim( $_POST['u_hv_descr']) );
		$product_id = (int) trim($_POST['u_experience_id']);
		$product_status = (trim( $_POST['u_availability']) == 'yes') ? 'publish' : 'draft';
		$my_post = array(

		   'ID' =>  $product_id,
		   'post_title'    => $title,
		   'post_content'  => $post_content,
		  	'post_status'   => $product_status
		);

		wp_update_post( $my_post );
		$hv_match_id = sanitize_text_field(trim( $_POST['u_hv_host_event_id']) );
		$host_id = trim($_POST['u_hv_host_id']);
		$event_city = trim(get_user_meta( $host_id, 'user-citys', true )); 
		// $host_avatar = get_avatar_url( $host_id );
		$max_people = get_user_meta( $host_id , "max_people" ,true);
 		$minimum_age = get_user_meta( $host_id , "minimum_age" ,true);
		$price = sanitize_text_field( trim( $_POST['u_hv_price'] ) );
		$basic_price = $price;
		// total Price
		$product_addon = array();
		$included_service = array();
		$k = 0;
			 
			if(isset($_POST['u_include_food_services'])){
				$ex_temp = array();
				$ex_temp_title = array();
				foreach ($_POST['u_include_food_services'] as $value) {
					if( '' !=  trim($value) ){
						$ex_temp[] = $value;
						$ex_temp_title[] = get_the_title($value);
					}
				}
				if( !empty($ex_temp)){
					 
					$temp = array();   
					$content = implode(', ', $ex_temp_title);
					 
					$temp['name'] = 'Food';
					$temp['title_format'] = 'label';
					$temp['description_enable'] = 1;
					$temp['description'] = $content;
					$temp['type'] = 'custom_text';
					$temp['display'] = 'select';
					$temp['position'] = $k;
					$temp['required'] = 1;
					$temp['restrictions'] = 0;
					$temp['restrictions_type'] = 'any_text';
					$temp['adjust_price'] = 0;
					$temp['price_type'] = 'flat_fee';
					$temp['price'] = '';
					$temp['min'] = 0;
					$temp['max'] = 0;
					$temp['options'] = array( array(
						'label' => '',
						'price' => '',
						'image' => '',
						'price_type' => 'flat_fee')
						);
					 
					$k++;
					$product_addon[] = $temp;
				}
				$included_service['food_services'] = $ex_temp;
				 

			}
					 
			if(isset($_POST['u_include_drink_services'])){
				$ex_temp = array();
				$ex_temp_title = array();
				foreach ($_POST['u_include_drink_services'] as $value) {
					if( '' !=  trim($value) ){
						$ex_temp[] = $value;
						$ex_temp_title[] = get_the_title($value);
					}
				}
				if( !empty($ex_temp)){
					 
					$temp = array();   
					$content = implode(', ', $ex_temp_title);
					 
					$temp['name'] = 'Drink';
					$temp['title_format'] = 'label';
					$temp['description_enable'] = 1;
					$temp['description'] = $content;
					$temp['type'] = 'custom_text';
					$temp['display'] = 'select';
					$temp['position'] = $k;
					$temp['required'] = 1;
					$temp['restrictions'] = 0;
					$temp['restrictions_type'] = 'any_text';
					$temp['adjust_price'] = 0;
					$temp['price_type'] = 'flat_fee';
					$temp['price'] = '';
					$temp['min'] = 0;
					$temp['max'] = 0;
					$temp['options'] = array( array(
						'label' => '',
						'price' => '',
						'image' => '',
						'price_type' => 'flat_fee')
						);
					 
					$k++;
					$product_addon[] = $temp;
				}
				$included_service['drink_services'] = $ex_temp;
				 

			}
			
			if(isset($_POST['u_include_ticket_services'])){
				$ex_temp = array();
				$ex_temp_title = array();
				foreach ($_POST['u_include_ticket_services'] as $value) {
					if( '' !=  trim($value) ){
						$ex_temp[] = $value;
						$ex_temp_title[] = get_the_title($value);
					}
				}
				if( !empty($ex_temp)){
					 
					$temp = array();   
					$content = implode(', ', $ex_temp_title);
					 
					$temp['name'] = 'Tickets';
					$temp['title_format'] = 'label';
					$temp['description_enable'] = 1;
					$temp['description'] = $content;
					$temp['type'] = 'custom_text';
					$temp['display'] = 'select';
					$temp['position'] = $k;
					$temp['required'] = 1;
					$temp['restrictions'] = 0;
					$temp['restrictions_type'] = 'any_text';
					$temp['adjust_price'] = 0;
					$temp['price_type'] = 'flat_fee';
					$temp['price'] = '';
					$temp['min'] = 0;
					$temp['max'] = 0;
					$temp['options'] = array( array(
						'label' => '',
						'price' => '',
						'image' => '',
						'price_type' => 'flat_fee')
						);
					 
					$k++;
					$product_addon[] = $temp;
				}
				$included_service['ticket_services'] = $ex_temp;
				 

			}	 

			if(isset($_POST['u_include_ticket_services'])){
				$ex_temp = array();
				$ex_temp_title = array();
				foreach ($_POST['u_include_ticket_services'] as $value) {
					if( '' !=  trim($value) ){
						$ex_temp[] = $value;
						$ex_temp_title[] = get_the_title($value);
					}
				}
				if( !empty($ex_temp)){
					 
					$temp = array();   
					$content = implode(', ', $ex_temp_title);
					 
					$temp['name'] = 'Tickets';
					$temp['title_format'] = 'label';
					$temp['description_enable'] = 1;
					$temp['description'] = $content;
					$temp['type'] = 'custom_text';
					$temp['display'] = 'select';
					$temp['position'] = $k;
					$temp['required'] = 1;
					$temp['restrictions'] = 0;
					$temp['restrictions_type'] = 'any_text';
					$temp['adjust_price'] = 0;
					$temp['price_type'] = 'flat_fee';
					$temp['price'] = '';
					$temp['min'] = 0;
					$temp['max'] = 0;
					$temp['options'] = array( array(
						'label' => '',
						'price' => '',
						'image' => '',
						'price_type' => 'flat_fee')
						);
					 
					$k++;
					$product_addon[] = $temp;
				}
				$included_service['ticket_services'] = $ex_temp;
				 

			} 

			if(isset($_POST['u_include_transport_services'])){
				$ex_temp = array();
				$ex_temp_title = array();
				foreach ($_POST['u_include_transport_services'] as $value) {
					if( '' !=  trim($value) ){
						$ex_temp[] = $value;
						$ex_temp_title[] = get_the_title($value);
					}
				}
				if( !empty($ex_temp)){
					 
					$temp = array();   
					$content = implode(', ', $ex_temp_title);
					 
					$temp['name'] = 'Transport';
					$temp['title_format'] = 'label';
					$temp['description_enable'] = 1;
					$temp['description'] = $content;
					$temp['type'] = 'custom_text';
					$temp['display'] = 'select';
					$temp['position'] = $k;
					$temp['required'] = 1;
					$temp['restrictions'] = 0;
					$temp['restrictions_type'] = 'any_text';
					$temp['adjust_price'] = 0;
					$temp['price_type'] = 'flat_fee';
					$temp['price'] = '';
					$temp['min'] = 0;
					$temp['max'] = 0;
					$temp['options'] = array( array(
						'label' => '',
						'price' => '',
						'image' => '',
						'price_type' => 'flat_fee')
						);
					 
					$k++;
					$product_addon[] = $temp;
				}
				$included_service['transport_services'] = $ex_temp;
				 

			} 
    		 
    		if(isset($_POST['u_include_tool_services'])){
				$ex_temp = array();
				$ex_temp_title = array();
				foreach ($_POST['u_include_tool_services'] as $value) {
					if( '' !=  trim($value) ){
						$ex_temp[] = $value;
						$ex_temp_title[] = get_the_title($value);
					}
				}
				if( !empty($ex_temp)){
					 
					$temp = array();   
					$content = implode(', ', $ex_temp_title);
					 
					$temp['name'] = 'Tools';
					$temp['title_format'] = 'label';
					$temp['description_enable'] = 1;
					$temp['description'] = $content;
					$temp['type'] = 'custom_text';
					$temp['display'] = 'select';
					$temp['position'] = $k;
					$temp['required'] = 1;
					$temp['restrictions'] = 0;
					$temp['restrictions_type'] = 'any_text';
					$temp['adjust_price'] = 0;
					$temp['price_type'] = 'flat_fee';
					$temp['price'] = '';
					$temp['min'] = 0;
					$temp['max'] = 0;
					$temp['options'] = array( array(
						'label' => '',
						'price' => '',
						'image' => '',
						'price_type' => 'flat_fee')
						);
					 
					$k++;
					$product_addon[] = $temp;
				}
				$included_service['tool_services'] = $ex_temp;

			} 
			if(isset($_POST['u_include_other_services'])){
				$ex_temp = array();
				$ex_temp_title = array();
				foreach ($_POST['u_include_other_services'] as $value) {
					if( '' !=  trim($value) ){
						$ex_temp[] = $value;
						$ex_temp_title[] = get_the_title($value);
					}
				}
				if( !empty($ex_temp)){
					 
					$temp = array();   
					$content = implode(', ', $ex_temp_title);
					 
					$temp['name'] = 'Others';
					$temp['title_format'] = 'label';
					$temp['description_enable'] = 1;
					$temp['description'] = $content;
					$temp['type'] = 'custom_text';
					$temp['display'] = 'select';
					$temp['position'] = $k;
					$temp['required'] = 1;
					$temp['restrictions'] = 0;
					$temp['restrictions_type'] = 'any_text';
					$temp['adjust_price'] = 0;
					$temp['price_type'] = 'flat_fee';
					$temp['price'] = '';
					$temp['min'] = 0;
					$temp['max'] = 0;
					$temp['options'] = array( array(
						'label' => '',
						'price' => '',
						'image' => '',
						'price_type' => 'flat_fee')
						);
					 
					$k++;
					$product_addon[] = $temp;
				}
				$included_service['other_services'] = $ex_temp;
				 

			} 
    		 
    		 
    		 
    			update_post_meta( $product_id, 'MaximumPeople', $max_people);
    			update_post_meta( $product_id, 'MinimumAge', $minimum_age);
    			
    			$userInfo = get_user_by( 'ID', $host_id );
    			update_post_meta( $product_id, '_price', $price );
    			update_post_meta( $product_id, '_regular_price', $price );
				update_post_meta( $product_id, 'virtual', 'yes');
				update_post_meta( $product_id, '_manage_stock', 'yes');
				update_post_meta( $product_id, '_backorders', 'no');
				update_post_meta( $product_id, '_sold_individually', 'no');
				update_post_meta( $product_id, '_stock_status', 'instock');
				update_post_meta( $product_id, '_stock', trim(($max_people)));
				update_post_meta( $product_id, '_wcfm_product_author', $host_id);
				update_post_meta( $product_id, 'WooCommerceEventsEmail', $userInfo->user_email);
				update_post_meta( $product_id, 'WooCommerceEventsSupportContact', '');
				update_post_meta( $product_id, 'WooCommerceEventsEvent', 'Event');
				update_post_meta( $product_id, '_basic_price', $basic_price);
				
				update_post_meta( $product_id, 'match_city', $event_city );
				

				update_post_meta( $product_id, 'match_id', $hv_match_id);
				update_post_meta( $product_id, '_included_service', $included_service);

    			update_post_meta( $product_id, '_product_addons', $product_addon);
    			update_post_meta( $product_id, '_product_addons_exclude_global', 0);
    			 
    			$redirect_url = site_url('user/'.$userInfo->user_login.'/?profiletab=next-matches&edit_um_event=edit');
    			echo json_encode(array('status' => 'success', 'message' => 'Ticket successfully Updateed', 'response' => $product_id, 'redirect_url' => $redirect_url));
    			exit();
		  
		 
	}
	echo json_encode(array('status' => 'error', 'message' => 'Invalid Request', 'response' => ''));
	exit();
	
}
add_action( 'wp_ajax_update_host_product', 'update_host_product');
 
/**
 * If host want to product a any product so geneate update form.
 * @version 1.0.0
 * @return HTML
 *
 */ 
function get_event_form(){
	global $wpdb;
	if(isset($_POST['event_id'], $_POST['host_id'], $_POST['wp_nonce']) && wp_verify_nonce( $_POST['wp_nonce'], 'hfwc-fro-form-ajax' )) {
		?>
 
 	<?php 
	 
		$edit_product_id = trim( $_POST['event_id']);
		$profile_id = (int) trim( $_POST['host_id'] );
		$product_details = get_post( $edit_product_id );
	    if( $product_details != null ){
	     
	        $product_title = $product_details->post_title;
	        $post_content = $product_details->post_content;
	        $included_service = get_post_meta( $edit_product_id, '_included_service', true);
	        $regular_price = get_post_meta( $edit_product_id, '_regular_price', true);
	        $basic_price = get_post_meta( $edit_product_id, '_basic_price', true);
	        $post_status = $product_details->post_status;
	    }else{
	    	echo "<p class='no-product-found'>No Product Found.</p>";
	    	exit();
	    }
	?>
	    <div id="errorMsgs"></div>
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
                                  	<option value="">None</option>
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
                              	<option value="">None</option>
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
                                    <option value="">None</option>
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
                            	<option value="">None</option>
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
                            	<option value="">None</option>
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
                           	<?php
                           	$selected_yes = ( $post_status == 'publish' ) ? 'selected' : '';
                           	$selected_no = ( $post_status != 'publish' ) ? 'selected' : '';
                           	?>
                                <select name="u_availability" id="u_availability">
                                    <option value="yes" <?php echo $selected_yes;?>>Yes</option>
                                     <option value="no" <?php echo $selected_no;?>>No</option>
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
	 	
<?php
	}
 
	exit();
}
add_action( 'wp_ajax_get_event_form', 'get_event_form');

/**
 * Submit acf user form  data  with ajax for 
 * @return json
 *
 */
 function havefan_save_acf_usermeta(){
 	if(isset($_POST['user_id'], $_POST['action'], $_POST['wp_nonce']) && wp_verify_nonce( $_POST['wp_nonce'], 'hfwc-fro-form-ajax' )) {
 		global $wpdb;
 		$current_user_id = get_current_user_id();
 		$user_id = str_replace('user_', '', trim($_POST['user_id']));
 		unset($_POST['user_id']);
 		unset($_POST['action']);
 		unset($_POST['wp_nonce']);
 		if( $current_user_id ==  $user_id ){
 			foreach ($_POST as $key => $value) {

 			 	if( '' !=  trim($key) ){
 			 		update_user_meta( $current_user_id, trim($key), $value );
 			 	}
 			}
      
 		echo json_encode(array('status' => 'success', 'message' => 'User data successfully updated'));
 		UM()->user()->remove_cache( $current_user_id );
 			exit();
 		}else{
 			echo json_encode(array('status' => 'error', 'message' => 'Invalid User Request.'));
 			exit();
 		}
 	}
 	echo json_encode(array('status' => 'error', 'message' => 'Invalid Request.'));		
 	exit();
 }
 add_action( 'wp_ajax_havefan_save_acf_usermeta', 'havefan_save_acf_usermeta');







/**
 * Submit review form  data  with ajax for 
 * @return json
 *
 */
 function havefan_save_userReview(){
 	if(isset($_POST['user_id'], $_POST['action'], $_POST['wp_nonce']) && wp_verify_nonce( $_POST['wp_nonce'], 'hfwc-fro-form-ajax' )) {
 		global $wpdb;
 		$table = $wpdb->prefix.'postmeta';
 		$current_user_id = get_current_user_id();
 		$user_id = $_POST['user_id'];
 		$order_id = $_POST['order_id'];
 		// check exists review or not
 		$check_review = $wpdb->get_row( "SELECT * FROM $table WHERE `meta_key` = '_review_order_id' AND `meta_value` = '".$order_id."' ");
 		 
 		//$check_review = get_post_meta( $order_id, '_review_order_id', true );
 		if( isset($check_review->post_id) ){
 			echo json_encode(array('status' => 'success', 'message' => 'Review already given.'));
 			exit();
 		}
 	 
 		unset($_POST['user_id']);
 		unset($_POST['action']);
 		unset($_POST['wp_nonce']);
 		if( $current_user_id ==  $user_id ){
           // Create post object
 			$post_author=1;
 			if($_POST['order_id']){
               $post   = get_post( $_POST['order_id'] );
               $post_author=$post->post_author;
              
	       }
		   $my_post = array(
			  'post_title'    => $_POST['review_title'],
			  'post_content'  => $_POST['review_details'],
			  'post_status'   => 'publish',
			  'post_type' => 'um_review',
			  'post_author'   => $post_author,
			);
	 
	      // Insert the post into the database
	       $post_id = wp_insert_post( $my_post );
	       update_post_meta( $post_id, '_reviewer_id', $user_id );
	       update_post_meta( $post_id, '_status', 1 );
	       update_post_meta( $post_id, '_rating', $_POST['review'] );
	       update_post_meta( $post_id, '_flagged', 0 );
	       update_post_meta( $post_id, '_user_id', $post_author );
	       update_post_meta( $post_id, '_review_order_id', $order_id );
	       // update total review
	       $_reviews = get_user_meta( $post_author, '_reviews', true);

	       $_reviews_total = get_user_meta( $post_author, '_reviews_total', true);
	       $_reviews_compound = get_user_meta( $post_author, '_reviews_compound', true);

	       $_reviews_total = $_reviews_total +  1;
	       $_reviews_compound = $_reviews_compound + (int) $_POST['review'];
	       if(is_array( $_reviews ) ){
	       		$_reviews[$post_id] = (int) $_POST['review'];
	       		update_user_meta( $post_author, '_reviews', $_reviews );
	       }else{

	       		$temp = array();
	       		$temp[$post_id] = (int) $_POST['review'];
	       		update_user_meta( $post_author, '_reviews', $temp );

	       }
	        $average = ceil( $_reviews_compound / $_reviews_total );
	       update_user_meta( $post_author, '_reviews_total', $_reviews_total);
	       update_user_meta( $post_author, '_reviews_compound', $_reviews_compound);
	       update_user_meta( $post_author, '_reviews_avg', $average);

 			echo json_encode(array('status' => 'success', 'message' => 'Review Add successfully.'));
 			exit();
 		}else{
 			echo json_encode(array('status' => 'error', 'message' => 'Invalid User Request.'));
 			exit();
 		}
 	}
 	echo json_encode(array('status' => 'error', 'message' => 'Invalid Request.'));		
 	exit();
 }
 add_action( 'wp_ajax_havefan_save_userReview', 'havefan_save_userReview');








 /**
 * Submit acf user form  data  with ajax for 
 * @return json
 *
 */
 function havefan_event_list(){
 	if(isset($_GET['type'], $_GET['wp_nonce']) && wp_verify_nonce( $_GET['wp_nonce'], 'hfwc-fro-form-ajax' )) {
 		$type=$_GET['type'];
 		if($type=='calendar'){
 			if(isset($_GET['by_date']) && strlen(trim($_GET['by_date'])) == 10 ){
 				$default_dd = trim($_GET['by_date']);
 				echo do_shortcode('[fooevents_calendar  defaultDate= "'.$default_dd.'"]'); 
 			}else{
 				echo do_shortcode('[fooevents_calendar]'); 
 			}
           
 		}else{
           echo do_shortcode('[fooevents_events_list]'); 
 		} 
 		
 	}
 	exit();
 }
 add_action( 'wp_ajax_havefan_event_list', 'havefan_event_list');
 add_action( 'wp_ajax_nopriv_havefan_event_list', 'havefan_event_list');

/** Set search form data **/
 
function havefan_set_search_from_field_data(){
	if(isset($_POST['searchtype'], $_POST['search_country'], $_POST['search_city'], $_POST['search_team']) ) {
		global $wpdb;
		$postMeta_table = $wpdb->prefix.'postmeta';
		$searchtype = trim( $_POST['searchtype']);
		$search_country = trim( $_POST['search_country'] );
		$search_city = trim( $_POST['search_city'] );
		$search_team = trim( $_POST['search_team'] );
		$city_array = array();
		$date_array = array();
		$team_array = array();
		 
		  
			$curret_date = date('Y-m-d');
		    $meta_query_array = array('relation' => 'AND',array(
		                'key' => 'MatchDate',
		                'value' => $curret_date,
		                'compare' => '>=',
		                'type' => 'DATE',
		                ));
			if( $search_country != ''){
				$meta_query_array[] = array(
		                'key' => 'match_country',
		                'value' => $search_country,
		                'compare' => '=',
		                );
			}
			if( $search_city != ''){
				$meta_query_array[] = array(
		                'key' => 'match_city',
		                'value' => $search_city,
		                'compare' => '=',
		                );
			}if( $search_team != ''){
				$meta_query_array[] = array('relation' => 'OR', array(
		                'key' => 'Team1',
		                'value' => $search_team,
		                'compare' => '=',
		                ), array(
		                'key' => 'Team2',
		                'value' => $search_team,
		                'compare' => '=',
		                ));
		 
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
					  
					$city = trim(get_post_meta( $prod_id, 'match_city', true));
					if( '' != $city ){
						$city_array[] = $city;
					}  
					$date = trim(get_post_meta( $prod_id, 'MatchDate', true));
					if( '' != $date ){
						$date_array[] = date('j-n-Y',strtotime(trim($date))) ;
					}
					$Team1 = trim(get_post_meta( $prod_id, 'Team1', true));
					$Team2 = trim(get_post_meta( $prod_id, 'Team2', true));
					if( '' != $Team1 ){
						$team_array[] = $Team1;
					}
					if( '' != $Team2 ){
						$team_array[] = $Team2;
					}

				}
				wp_reset_postdata();
				echo json_encode(array('status'=> 'success', 'message' => 'data successfully','cities'=> array_unique($city_array), 'teams'=> array_unique($team_array), 'all_dates' => array_unique($date_array) ));
				exit();
			}
			wp_reset_postdata();
			echo json_encode(array('status'=> 'success', 'message' => 'data successfully', 'cities' =>$city_array, 'teams'=> $team_array, 'all_dates' => $date_array));
				exit();
		 
		  
	}
	echo json_encode(array( 'status' => 'error', 'message' => 'Invalid Request.' ));
	exit();
}

 add_action( 'wp_ajax_havefan_set_search_from_field_data', 'havefan_set_search_from_field_data');
 add_action( 'wp_ajax_nopriv_havefan_set_search_from_field_data', 'havefan_set_search_from_field_data');

 function get_host_country_city(){
 	// echo "<pre>";
 	// print_r($_POST);
  
 	$user_id = $_POST['user_id'];
 	$country = $_POST['country'];
 	$select_city = get_user_meta( $user_id, 'user-citys', true);
 	$select_country = get_user_meta( $user_id, 'country', true);
 	global $wpdb;
 	$table = $wpdb->prefix.'country_city_list';
 	$select_list = $wpdb->get_row("SELECT * FROM $table WHERE `country` = '".$country."' ");
 	// echo "<pre>";
 	// print_r($select_list);
 	if(isset($select_list->id)){
 		echo json_encode(array('status' => 'success', 'select_city'=> $select_city, 'select_country'=> $select_country, 'city_list' => json_encode($select_list->city)));
 		exit();
 	}
  
 	$all_city = json_encode(array());
 	echo json_encode(array('status' => 'success', 'select_city'=> '', 'select_country'=> '', 'city_list' => $all_city));
 	exit();
 	 
}
add_action('wp_ajax_get_host_country_city', 'get_host_country_city');

?>