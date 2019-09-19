<?php 

function general_admin_notice(){
        if($_GET['success']){
			 echo '<div class="notice notice-success is-dismissible">
                <p>Import data Successfully.</p>
             </div>';
		}
 }
add_action('admin_notices', 'general_admin_notice');

add_filter( 'manage_football_league_posts_columns', 'set_custom_edit_football_league_columns' );
function set_custom_edit_football_league_columns($columns) {
	$columns['country'] = __( 'Country', 'Country' );
    $columns['Import_Team'] = __( 'Import Team', 'Import Team' );
	$columns['Import_Event'] = __( 'Import Event', 'Import Event' );
    return $columns;
}

// Add the data to the custom columns for the book post type:
add_action( 'manage_football_league_posts_custom_column' , 'custom_football_league_column', 10, 2 );
function custom_football_league_column( $column, $post_id ) {
	 switch ( $column ) {
		 case 'country' :
		      $country_name=get_post_meta( $post_id, 'country_name', true );
			   echo $country_name;
            break;
        case 'Import_Team' :
		      $id=get_post_meta( $post_id, 'league_id', true );
			  
			  if(get_option( 'football_api_key' )){
					$url=site_url().'/wp-json/team-api/import-team?league_id='.$id.'&return_url='.admin_url().'edit.php?post_type=football_league';
				}else{
		           $url=admin_url().'admin.php?page=football-api-setting&info=true';
	           }
              echo '<a href="'.$url.'" class="button">Import Team</a>';
            break;
		case 'Import_Event' :
		      $id=get_post_meta( $post_id, 'league_id', true );
			 
			  if(get_option( 'football_api_key' )){
					$url=site_url().'/wp-json/league-api/import-event?league_id='.$id.'&return_url='.admin_url().'edit.php?post_type=football_league';
				}else{
		            $url=admin_url().'admin.php?page=football-api-setting&info=true';
	           }
              echo '<a href="'.$url.'" class="button">Import Event</a>';
            break;
     }
}


add_filter( 'manage_football_team_posts_columns', 'set_custom_edit_football_team_columns' );
function set_custom_edit_football_team_columns($columns) {
    $columns['teamLogo'] = __( 'Logo', 'Logo' );
    return $columns;
}

// Add the data to the custom columns for the book post type:
add_action( 'manage_football_team_posts_custom_column' , 'custom_football_team_column', 10, 2 );
function custom_football_team_column( $column, $post_id ) {
	 switch ( $column ) {
        case 'teamLogo' :
		      $url=get_post_meta( $post_id, 'team_badge', true );
			   echo '<img src="'.$url.'" style="height: 50px;" />';
            break;
     }
}


// player

add_filter( 'manage_football_player_posts_columns', 'set_custom_edit_football_player_columns' );
function set_custom_edit_football_player_columns($columns) {
    $columns['player_number'] = __( 'Number', 'Number' );
	$columns['player_country'] = __( 'Country', 'Country' );
	$columns['player_type'] = __( 'Type', 'Type' );
	 return $columns;
}

// Add the data to the custom columns for the book post type:
add_action( 'manage_football_player_posts_custom_column' , 'custom_football_player_column', 10, 2 );
function custom_football_player_column( $column, $post_id ) {
	$player_number=get_post_meta( $post_id, 'player_number', true );
	$player_country=get_post_meta( $post_id, 'player_country', true );
	$player_type=get_post_meta( $post_id, 'player_type', true );
	 switch ( $column ) {
       case 'player_number' :
		      echo $player_number;
            break;
		case 'player_country' :
		      echo $player_country;
            break;
		case 'player_type' :
		      echo $player_type;
            break;
     }
}


add_filter('views_edit-football_league','my_filter');


function my_filter($views){
	if(get_option( 'football_api_key' )){
		$ur=site_url().'/wp-json/league-api/import-order?&return_url='.admin_url().'edit.php?post_type=football_league&success=true';
        $views['import'] = '<a href="'.$ur.'" class="primary">Import Leagues</a>';
	}else{
		$ur=admin_url().'admin.php?page=football-api-setting&info=true';
		$views['import'] = '<a href="'.$ur.'" class="primary">Import Leagues</a>';
	}
	
    return $views;
}

function global_notice_meta_box() {

    add_meta_box(
        'team-list',
        __( 'Team List', 'sitepoint' ),
        'global_notice_meta_box_callback',
        'football_league'
    );
}

add_action( 'add_meta_boxes', 'global_notice_meta_box' );
function global_notice_meta_box_callback(){
	 $id=$_GET['post'];
	 $leagueKey=get_post_meta( $id, 'league_id', true );
	
	 if(get_option( 'football_api_key' )){
					 $url=site_url().'/wp-json/team-api/import-team?league_id='.$leagueKey.'&return_url='.admin_url().'post.php?post='.$id.'&action=edit';
				}else{
		            $url=admin_url().'admin.php?page=football-api-setting&info=true';
	           }
     echo '<a href="'.$url.'" class="button">Import Team</a>';
					
	?>
	<br/>
	
	<table class="wp-list-table widefat fixed striped pages">
	  <thead>
	    <tr>
		  <th>ID.</th>
		  <th>Team Name</th>
		  <th>Team Logo</th>
		  <th>Action</th>
		</tr>
	  </thead>
	  <tbody>
	 <?php 
	  
	  
	 
			$cc_args = array(
				'posts_per_page'   => -1,
				'post_type'        => 'football_team',
				'meta_key'         => 'teamConnectToLeague',
				'meta_value'       => $leagueKey
			);
			$cc_query = new WP_Query( $cc_args );
            $postList=$cc_query->posts;
             if ( $postList) {
              foreach($postList as $singlePost){
					?>
					 <tr>
					  <td><?php echo $singlePost->ID; ?> </td>
					  <td><?php echo $singlePost->post_title; ?> </td>
					  <td>
					    <?php $url=get_post_meta( $singlePost->ID, 'team_badge', true );
			             echo '<img src="'.$url.'" style="height: 50px;" />';
					    ?> 
					  </td>
					  <td><a href="<?php echo admin_url().'post.php?post='.$singlePost->ID.'&action=edit' ?>" class='primary'>View</a></td>
					</tr>
					<?php
				}
			 }
			?>
	   </tbody>
	</table>
	<?php
}

// code for show player in team

add_action( 'add_meta_boxes', 'player_meta_box' );
function player_meta_box() {

    add_meta_box(
        'player_meta_box',
        __( 'Player List', 'sitepoints' ),
        'global_player_meta_box_callback',
        'football_team'
    );
}

function global_player_meta_box_callback(){
	 $id=$_GET['post'];
	?>
	<br/>
	<table class="wp-list-table widefat fixed striped pages">
	  <thead>
	    <tr>
		  <th>Name</th>
		  <th>Country</th>
		  <th>No.</th>
		  <th>Type</th>
		  <th>Age</th>
		  <th>Total Match</th>
		  <th>Goals</th>
		  <th>Action</th>
		</tr>
	  </thead>
	  <tbody>
	  <?php 
	      $cc_args = array(
				'posts_per_page'   => -1,
				'post_type'        => 'football_player',
				'meta_key'         => 'player_in_team_id',
				'meta_value'       => $id
			);
			$cc_query = new WP_Query( $cc_args );
            $postList=$cc_query->posts;
             if ( $postList) {
              foreach($postList as $singlePost){
					?>
					 <tr>
					  <td><?php echo $singlePost->post_title; ?> </td>
					  <td><?php echo get_post_meta( $singlePost->ID, 'player_country', true );  ?> </td>
					  <td><?php echo get_post_meta( $singlePost->ID, 'player_number', true );  ?> </td>
					  
					  
					  <td><?php echo get_post_meta( $singlePost->ID, 'player_type', true );  ?> </td>
					  <td><?php echo get_post_meta( $singlePost->ID, 'player_age', true );  ?> </td>
					  <td><?php echo get_post_meta( $singlePost->ID, 'player_match_played', true );  ?> </td>
					  <td><?php echo get_post_meta( $singlePost->ID, 'player_goals', true );  ?> </td>
					  <td><a href="<?php echo admin_url().'post.php?post='.$singlePost->ID.'&action=edit' ?>" class='primary'>View</a></td>
					</tr>
					<?php
				}
			 }
			?>
	   </tbody>
	</table>
	
	<?php
}


// code for show single player in plyer profile

add_action( 'add_meta_boxes', 'singleplayer_meta_box' );
function singleplayer_meta_box() {

    add_meta_box(
        'singleplayer_meta_box',
        __( 'Player Profile', 'sitepoints' ),
        'global_player_Single_meta_box_callback',
        'football_player'
    );
}

function global_player_Single_meta_box_callback(){
	 $id=$_GET['post'];
	?>
	<br/>
	<table class="wp-list-table widefat fixed striped pages">
	   <tr>
	      <th>Name</th>
		  <td><?php the_title(); ?></td>
	   </tr>
	   <tr>
	      <th>Country</th>
		  <td><?php echo get_post_meta( $id, 'player_country', true );  ?></td>
	   </tr>
	   <tr>
	      <th>Player Number</th>
		  <td><?php echo get_post_meta( $id, 'player_number', true );  ?>
	   <tr>
	      <th>Player Type</th>
		  <td><?php echo get_post_meta( $id, 'player_type', true );  ?></td>
	   </tr>
	   <tr>
	      <th>Player Age</th>
		  <td><?php echo get_post_meta( $id, 'player_age', true );  ?></td>
	   </tr>
	   <tr>
	      <th>Player Total Match</th>
		  <td><?php echo get_post_meta( $id, 'player_match_played', true );  ?></td>
	   </tr>
	   <tr>
	      <th>Player Total Goal</th>
		  <td><?php echo get_post_meta( $id, 'player_goals', true );  ?></td>
	   </tr>
	   <tr>
	      <th>Player Total Yellow Cards </th>
		  <td><?php echo get_post_meta( $id, 'player_yellow_cards', true );  ?></td>
	   </tr>
	   <tr>
	      <th>Player Total Red Cards </th>
		  <td><?php echo get_post_meta( $id, 'player_red_cards', true );  ?></td>
	   </tr>
	</table>
	
	<?php
}


add_action( 'tribe_events_single_event_before_the_content', 'tribe_events_single_event_after_the_contents' );
function tribe_events_single_event_after_the_contents(){
	
	$id=get_the_ID();
	 $to= get_post_meta( $id, 'Event_match_hometeam_id', true );
	 $vs=get_post_meta( $id, 'Event_match_awayteam_id', true );
        global $wpdb;
		$team_key= $data->team_key ;
		$table_name = $wpdb->prefix ."postmeta"; 	
		$checkData = $wpdb->get_results( "SELECT * FROM $table_name WHERE meta_key = 'team_key' AND meta_value = '".$to."'");
		$imgTo='';
		$imgVs= '';
		$post_id_vs='';
		$post_id_to='';
		if ( $checkData ){
			$post_id_to=$checkData[0]->post_id;
			$imgTo= get_post_meta( $post_id_to, 'team_badge', true );
		}	
		$table_name = $wpdb->prefix ."postmeta"; 	
		$checkDatavs = $wpdb->get_results( "SELECT * FROM $table_name WHERE meta_key = 'team_key' AND meta_value = '".$vs."'");
		if ( $checkDatavs ){
			$post_id_vs=$checkDatavs[0]->post_id;
			$imgVs= get_post_meta( $post_id_vs, 'team_badge', true );
		}
		?>
		
	    <table>
			    <tr>
				   
					  <td colspan="7"><center><img src="<?php echo $imgTo; ?>" /></center></td>
				   </tr>
			    <tr>
			       <th colspan="3">Team Name</th>
				   <td colspan="3"><?php echo get_post_meta( $id , 'Event_match_hometeam_name', true ); ?></td>
			    </tr>
				<tr>
				   <th>Player Name</th>
				   <th>Country</th>
				   <th>Player Number</th>
				   <th>Type</th>
				   <th>Total Match</th>
				   <th>Age</th>
				   <th>Goals</th>
				</tr>
			    <?php
				  $cc_args = array(
						'posts_per_page'   => -1,
						'post_type'        => 'football_player',
						'meta_key'         => 'player_in_team_id',
						'meta_value'       => $post_id_to
					);
					$cc_query = new WP_Query( $cc_args );
					$postList=$cc_query->posts;
					 if ( $postList) {
					  foreach($postList as $singlePost){
						 ?>
						  <tr>
							 <td ><?php echo $singlePost->post_title; ?></td>
							  <td><?php echo get_post_meta( $singlePost->ID, 'player_country', true );  ?> </td>
							  <td><?php echo get_post_meta( $singlePost->ID, 'player_number', true );  ?> </td>
							  
							  
							  <td><?php echo get_post_meta( $singlePost->ID, 'player_type', true );  ?> </td>
							  <td><?php echo get_post_meta( $singlePost->ID, 'player_age', true );  ?> </td>
							  <td><?php echo get_post_meta( $singlePost->ID, 'player_match_played', true );  ?> </td>
							  <td><?php echo get_post_meta( $singlePost->ID, 'player_goals', true );  ?> </td>
						  </tr>
						  <?php
					  }
					 }
				  ?>
			</table>
		<table>
			    <tr>
				   
					  <td colspan="7"><center><img src="<?php echo $imgVs; ?>" /></center></td>
				   </tr>
			    <tr>
			       <th colspan="3">Team Name</th>
				   <td colspan="3"><?php echo get_post_meta( $id , 'Event_match_awayteam_name', true ); ?></td>
			    </tr>
				<tr>
				   <th>Player Name</th>
				   <th>Country</th>
				   <th>Player Number</th>
				   <th>Type</th>
				   <th>Total Match</th>
				   <th>Age</th>
				   <th>Goals</th>
				</tr>
			    <?php
				  $cc_args = array(
						'posts_per_page'   => -1,
						'post_type'        => 'football_player',
						'meta_key'         => 'player_in_team_id',
						'meta_value'       => $post_id_vs
					);
					$cc_query = new WP_Query( $cc_args );
					$postList=$cc_query->posts;
					 if ( $postList) {
					  foreach($postList as $singlePost){
						 ?>
						  <tr>
							 <td ><?php echo $singlePost->post_title; ?></td>
							  <td><?php echo get_post_meta( $singlePost->ID, 'player_country', true );  ?> </td>
							  <td><?php echo get_post_meta( $singlePost->ID, 'player_number', true );  ?> </td>
							  
							  
							  <td><?php echo get_post_meta( $singlePost->ID, 'player_type', true );  ?> </td>
							  <td><?php echo get_post_meta( $singlePost->ID, 'player_age', true );  ?> </td>
							  <td><?php echo get_post_meta( $singlePost->ID, 'player_match_played', true );  ?> </td>
							  <td><?php echo get_post_meta( $singlePost->ID, 'player_goals', true );  ?> </td>
						  </tr>
						  <?php
					  }
					 }
				  ?>
			</table>
		  
		<?php
	   
}


