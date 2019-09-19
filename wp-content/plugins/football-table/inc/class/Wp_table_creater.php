<?php
/**
 * Holds all functions of create database table .
 *
 * @author Dheeraj Tiwari <dheeraj@dheeraj@blsoftware.net>
 * @since 1.0.0
 * @package football data database
 */
class Wp_table_creater {
    var $plugin_path = ABOI_BASE_DIR;
    /**
	 * The public constructor.
	 */
    public function __construct() {
        $this->football_allTable_creater();
    }
    public function football_allTable_creater(){
		if($this->checkTable_exit('leagues')){
			$data=array('title','league_id','football_session','country_id','country_name','date');
			$this->createTable('leagues',$data);
		}
		
		if($this->checkTable_exit('teams')){
			$data=array('teamtitle','team_key','team_badge','league_id','date');
			$this->createTable('teams',$data);
		}
		if($this->checkTable_exit('player')){
			$data=array('playertitle','player_key','player_number','player_country','player_type','match_played','player_goals','player_yellow_cards','player_red_cards','team_id','date');
			$this->createTable('player',$data);
		}
		
	}
	public function checkTable_exit($table){
		global $wpdb;
		$table_name = $wpdb->base_prefix.$table;
		$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );
        if ( ! $wpdb->get_var( $query ) == $table_name ) {
			return true;
		}else{
			return false;
		} 
	} 
	public function createTable($table_name,$columnArray){
		   $columnString='';
		   foreach($columnArray as $column){
			    $columnString=$columnString.", $column VARCHAR(250) ";
			}
			global $wpdb;
		   $table_name = $wpdb->base_prefix.$table_name;
		   $sql = "CREATE TABLE " . $table_name . " (
			 id INT NOT NULL AUTO_INCREMENT
			".$columnString." ,
			PRIMARY KEY ( id )
			)";
	        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
	}
    
    
}
$Wp_table_creater = new Wp_table_creater();