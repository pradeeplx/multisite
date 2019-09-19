<?php

/**

 * Holds all functions of football receive data into wordpress .

 *

 * @author Dheeraj Tiwari <dheeraj@dheeraj@blsoftware.net>

 * @since 1.0.0

 * @package football data receive

 */

class Wpcp_team {



    // var $plugin_path = ABOI_BASE_DIR;

    /**

	 * The public constructor.

	 */

    public function __construct() {

        global $wpdb;

        add_action( 'rest_api_init', array( $this,'football_get_team_route' ) );

    }



    

    function football_get_team_route() {

        register_rest_route( 'league-api', 'import-team', array(

                        'methods' => 'GET',

                        'callback' => array( $this, 'receive_custom_football_team'),

            )

        );

    }

    

	

	 /**

     * Import data from football api to wordpress 

     * @version 1.0.0

     * @return string

     *

     */

    function receive_custom_football_team() {

		 if($_GET['league_id']){

		 $APIkey=get_option( 'football_api_key' );

		 $league_id=$_GET['league_id'];

		 $curl_options = array(

			 CURLOPT_URL => "https://apiv2.apifootball.com/?action=get_teams&league_id=$league_id&APIkey=$APIkey",

			 CURLOPT_RETURNTRANSFER => true,

			 CURLOPT_HEADER => false,

			 CURLOPT_TIMEOUT => 30,

			 CURLOPT_CONNECTTIMEOUT => 5

			);

			$curl = curl_init();

			curl_setopt_array( $curl, $curl_options );

			$teams = curl_exec( $curl );

            $teams = (array) json_decode($teams);

			$i=1;

			foreach($teams as $team){

				$this->addUpdateTeam($team ,$league_id);

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



	

	

	public function addUpdateTeam($data,$league_id){

		global $wpdb;

		$team_name=$data->team_name;

		$team_key= $data->team_key ;

		$team_badge=$data->team_badge;

		$players=$data->players;

		$date=date("Y-m-d");

		$table_name = $wpdb->prefix ."teams"; 	

		$checkData = $wpdb->get_results( "SELECT * FROM $table_name WHERE team_key = '".$team_key."' ");

		if ( $checkData ){

			//update query

			$query="UPDATE `wp_teams` SET `teamtitle`=='".$team_name."',`team_badge`=='".$team_badge."' WHERE `team_key` = '".$team_key."' " ;

		}else{

			//insert query

			$query="INSERT INTO $table_name (`teamtitle`, `team_key`, `team_badge`,  `league_id`, `date`) VALUES ('".$team_name."', '".$team_key."', '".$team_badge."', '".$league_id."' , '".$date."')";

		}

		$wpdb->query($query);

		foreach($players as $player){

		  $this->addUpdatePlayer($player,$team_key);

		}

		

	}

	

	// code for add upate players

	public function addUpdatePlayer($data,$team_key){

		$player_name=$data->player_name;

		$player_key= $data->player_key ;

		$player_number=$data->player_number;

		$player_country=$data->player_country ;

		$player_type=$data->player_type;

		$player_age=$data->player_age;

		$player_match_played=$data->player_match_played;

		$player_goals=$data->player_goals;

		$player_yellow_cards=$data->player_yellow_cards;

		$player_red_cards=$data->player_red_cards;

		global $wpdb;

		$table_name = $wpdb->prefix ."player"; 

		$date=date("Y-m-d");

		$checkData = $wpdb->get_results( "SELECT * FROM $table_name WHERE  player_key = '".$player_key."'");

		if ( $checkData ){

			$query="UPDATE $table_name SET `playertitle`= '".$player_name."',`player_number`='".$player_number."' ,`player_country`= '".$player_country."' ,`player_type`= '".$player_type."' ,`match_played`= '".$player_match_played."' ,`player_goals`= '".$player_goals."',`player_yellow_cards`='".$player_yellow_cards."' ,`player_red_cards`= '".$player_red_cards."'  , `date`= '".$date."' WHERE `player_key`='".$player_key."' ";

		}else{

			$query="INSERT INTO $table_name (`playertitle`, `player_key`, `player_number`, `player_country`, `player_type`, `match_played`, `player_goals`, `player_yellow_cards`, `player_red_cards`, `team_id`, `date`) VALUES ('".$player_name."','".$player_key."','".$player_number."','".$player_country."','".$player_type."','".$player_match_played."','".$player_goals."','".$player_yellow_cards."','".$player_red_cards."','".$team_key."','".$date."')";

		}

		$wpdb->query($query);

	}

}



$Wpcp_team = new Wpcp_team();