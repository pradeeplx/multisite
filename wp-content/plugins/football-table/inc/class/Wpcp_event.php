<?php

/**
 * Holds all functions of football receive data into wordpress .
 *
 * @author Dheeraj Tiwari <dheeraj@dheeraj@blsoftware.net>
 * @since 1.0.0
 * @package football data receive
 */

class Wpcp_event {



    // var $plugin_path = ABOI_BASE_DIR;

    /**

	 * The public constructor.

	 */

    public function __construct() {

        global $wpdb;

        add_action( 'rest_api_init', array( $this,'football_get_event_route' ) );

    }



    

    function football_get_event_route() {

        register_rest_route( 'league-api', 'import-event', array(

                        'methods' => 'GET',

                        'callback' => array( $this, 'receive_custom_football_event'),

            )

        );

    }

    

	

	 /**

     * Import data from football api to wordpress 

     * @version 1.0.0

     * @return string

     *

     */

    function receive_custom_football_event() {

		 if($_GET['league_id']){

		 $APIkey=get_option( 'football_api_key' );
		 $from_date = date('Y-m-d', strtotime(' -2 day'));
		 $to_date = date('Y-m-d', strtotime(' +1 year'));
		 $league_id=$_GET['league_id'];

		 $curl_options = array(

			 CURLOPT_URL => "https://apiv2.apifootball.com/?action=get_events&from=$from_date&to=$to_date&league_id=$league_id&APIkey=$APIkey",

			 CURLOPT_RETURNTRANSFER => true,

			 CURLOPT_HEADER => false,

			 CURLOPT_TIMEOUT => 30,

			 CURLOPT_CONNECTTIMEOUT => 5

			);

			$curl = curl_init();

			curl_setopt_array( $curl, $curl_options );

			$events = curl_exec( $curl );

            $events = (array) json_decode($events);

			$i=1;

			foreach($events as $event){
				$this->addUpdateEvent($event ,$league_id);

			}

			if($_GET['return_url']){

				$return_url=$_GET['return_url'].'&success=true';

			    header("location:$return_url");

			}

			

		 }else{

			

			  echo "Please add league id in url.";

			  if($_GET['return_url']){

				 $return_url=$_GET['return_url'];

			     header("location:$return_url");

			 }

		 }

	}



	

	

	public function addUpdateEvent($data,$league_id){

		global $wpdb;

		$match_id = trim( $data->match_id );
		$country_id = trim( $data->country_id );
		$country_name = trim( $data->country_name );
		$league_id = trim( $data->league_id );
		$league_name = trim( $data->league_name );
		$match_date = trim( $data->match_date );
		$match_time = trim( $data->match_time );
		$match_hometeam_id = trim( $data->match_hometeam_id );
		$match_hometeam_name = trim( $data->match_hometeam_name );
		$match_awayteam_id = trim( $data->match_awayteam_id );
		$match_awayteam_name = trim( $data->match_awayteam_name );

		$table_name = $wpdb->prefix ."events"; 	

		$checkData = $wpdb->get_results( "SELECT * FROM $table_name WHERE `match_id` = '".$match_id."' ");

		if ( $checkData ){

			//update query

			$query="UPDATE $table_name SET `country_id`=='".$country_id."',`country_name`=='".$country_name."',`league_id`=='".$league_id."',`league_name`=='".$league_name."',`match_date`=='".$match_date."',`match_time`=='".$match_time."',`match_hometeam_id`=='".$match_hometeam_id."',`match_hometeam_name`=='".$match_hometeam_name."',`match_awayteam_id`=='".$match_awayteam_id."',`match_awayteam_name`=='".$match_awayteam_name."' WHERE `match_id` = '".$match_id."' " ;

		}else{

			//insert query

			$query="INSERT INTO $table_name (`match_id`, `country_id`, `country_name`,  `league_id`, `league_name`, `match_date`, `match_time`, `match_hometeam_id`, `match_hometeam_name`, `match_awayteam_id`, `match_awayteam_name`) VALUES ('".$match_id."', '".$country_id."', '".$country_name."', '".$league_id."' , '".$league_name."', '".$match_date."', '".$match_time."', '".$match_hometeam_id."', '".$match_hometeam_name."' , '".$match_awayteam_id."', '".$match_awayteam_name."')";

		}

		$wpdb->query($query);
   
	}

}

$Wpcp_event = new Wpcp_event();