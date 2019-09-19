<?php
/**
 * Holds all functions of football league insert update.
 *
 * @author Dheeraj Tiwari <dheeraj@dheeraj@blsoftware.net>
 * @since 1.0.0
 * @package football data receive
 */
class Wpcp_league {

    var $plugin_path = ABOI_BASE_DIR;
    /**
	 * The public constructor.
	 */
    public function __construct() {
        global $wpdb;
        add_action( 'rest_api_init', array( $this,'my_get_league_route' ) );
    }

    
    function my_get_league_route() {
        register_rest_route( 'league-api', 'import-league', array(
                        'methods' => 'GET',
                        'callback' => array( $this, 'receive_custom_phrase'),
                    )
        );
    }
	
	// function for add and update league data
    public function addUpdateLeague($data){
		global $wpdb;
		$league_name=$data->league_name;
		$league_id= $data->league_id ;
		$league_season=$data->league_season;
		$country_id=$data->country_id;
		$country_name=$data->country_name;
		$date=date("Y-m-d");
		$table_name = $wpdb->prefix ."leagues"; 	
		$checkData = $wpdb->get_results( "SELECT * FROM $table_name WHERE league_id = '".$league_id."' ");
		if ( $checkData ){
			//update query
			$query="UPDATE $table_name SET `title`='".$league_name."',`football_session`='".$league_season."',`country_id`='".$country_id."',`country_name`= '".$country_name."' WHERE `wp_leagues`.`league_id` = '".$league_id."'" ;
		}else{
			//insert query
			$query="INSERT INTO $table_name (`title`, `league_id`, `football_session`, `country_id`, `country_name`, `date`) VALUES ('".$league_name."', '".$league_id."', '".$league_season."', '".$country_id."', '".$country_name."', '".$date."')";
		}
		$wpdb->query($query);
	}
	
    /**
     * Import data from football api to wordpress 
     * @version 1.0.0
     * @return string
     *
     */
    function receive_custom_phrase() {
		 $APIkey=get_option( 'football_api_key' );
		 $curl_options = array(
			  CURLOPT_URL => "https://apiv2.apifootball.com/?action=get_leagues&APIkey=$APIkey",
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_HEADER => false,
			  CURLOPT_TIMEOUT => 30,
			  CURLOPT_CONNECTTIMEOUT => 5
			);
			$curl = curl_init();
			curl_setopt_array( $curl, $curl_options );
			$leagues = curl_exec( $curl );
            $leagues = (array) json_decode($leagues);
			foreach($leagues as $league){
				$league_id= $this->addUpdateLeague($league);
			}
			if($_GET['return_url']){
				$return_url=$_GET['return_url'].'&success=true';
			    header("location:$return_url");
			}
    }
}

$Wpcp_league = new Wpcp_league();