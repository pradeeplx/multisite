<?php 
function custom_post_type() {
  $labels = array(
		'name'                  => _x( 'Football League', 'Football League ', 'Football League' ),
		'singular_name'         => _x( 'Football League ', 'Football League', 'football_league' ),
		'menu_name'             => __( 'Football League', 'football_league' ),
		'name_admin_bar'        => __( 'Football League', 'football_league' ),
		'archives'              => __( 'Football League', 'football_league' ),
		'attributes'            => __( 'Football League', 'football_league' ),
		'all_items'             => __( 'Football League', 'football_league' ),
		'add_new_item'          => __( 'Add New League', 'football_league' ),
		'add_new'               => __( 'Add League', 'football_league' ),
		'new_item'              => __( 'New League', 'football_league' ),
		'edit_item'             => __( 'Edit League', 'football_league' ),
		'update_item'           => __( 'Update League', 'football_league' ),
		'view_item'             => __( 'View League', 'football_league' ),
		'view_items'            => __( 'View League', 'football_league' ),
		'search_items'          => __( 'Search League', 'football_league' ),
		'not_found'             => __( 'Not League', 'football_league' ),
		'not_found_in_trash'    => __( 'Not League in Trash', 'football_league' ),
		'featured_image'        => __( 'Featured Image', 'football_league' ),
		'set_featured_image'    => __( 'Set featured image', 'football_league' ),
		'remove_featured_image' => __( 'Remove featured image', 'football_league' ),
		'use_featured_image'    => __( 'Use as featured image', 'football_league' ),
		'insert_into_item'      => __( 'Insert into League', 'football_league' ),
		'uploaded_to_this_item' => __( 'Uploaded to this League', 'football_league' ),
		'items_list'            => __( 'League list', 'football_league' ),
		'items_list_navigation' => __( 'League list navigation', 'football_league' ),
		'filter_items_list'     => __( 'Filter League list', 'football_league' ),
	);
	$args = array(
		'label'                 => __( 'football league', 'football league' ),
		'description'           => __( 'football league Description', 'football_league' ),
		'labels'                => $labels,
		'supports'              => false,
		//'taxonomies'            => array( 'category', 'post_tag' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( 'football_league', $args );

}
add_action( 'init', 'custom_post_type', 0 );
// Register Custom Taxonomy
function league_taxonomy() {

	$labels = array(
		'name'                       => _x( 'Session', 'Taxonomy General Name', 'Football' ),
		'singular_name'              => _x( 'Session', 'Taxonomy Singular Name', 'Football' ),
		'menu_name'                  => __( 'Session', 'Football' ),
		'all_items'                  => __( 'All Items', 'Football' ),
		'parent_item'                => __( 'Parent Item', 'Football' ),
		'parent_item_colon'          => __( 'Parent Item:', 'Football' ),
		'new_item_name'              => __( 'New Item Name', 'Football' ),
		'add_new_item'               => __( 'Add New Item', 'Football' ),
		'edit_item'                  => __( 'Edit Item', 'Football' ),
		'update_item'                => __( 'Update Item', 'Football' ),
		'view_item'                  => __( 'View Item', 'Football' ),
		'separate_items_with_commas' => __( 'Separate items with commas', 'Football' ),
		'add_or_remove_items'        => __( 'Add or remove items', 'Football' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'Football' ),
		'popular_items'              => __( 'Popular Items', 'Football' ),
		'search_items'               => __( 'Search Items', 'Football' ),
		'not_found'                  => __( 'Not Found', 'Football' ),
		'no_terms'                   => __( 'No items', 'Football' ),
		'items_list'                 => __( 'Items list', 'Football' ),
		'items_list_navigation'      => __( 'Items list navigation', 'Football' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'football_session', array( 'football_league' ), $args );

}
add_action( 'init', 'league_taxonomy', 0 );


// code for add team


function custom_post_type_team() {
  $labels = array(
		'name'                  => _x( 'Football Team ', 'Football Team ', 'Football Team ' ),
		'singular_name'         => _x( 'Football Team ', 'Football Team', 'football_team' ),
		'menu_name'             => __( 'Football Team', 'football_team' ),
		'name_admin_bar'        => __( 'Football Team', 'football_team' ),
		'archives'              => __( 'Football Team', 'football_team' ),
		'attributes'            => __( 'Football Team', 'football_team' ),
		'all_items'             => __( 'Football Team', 'football_team' ),
		'add_new_item'          => __( 'Add New Team', 'football_team' ),
		'add_new'               => __( 'Add Team', 'football_team' ),
		'new_item'              => __( 'New Team', 'football_team' ),
		'edit_item'             => __( 'Edit Team', 'football_team' ),
		'update_item'           => __( 'Update Team', 'football_team' ),
		'view_item'             => __( 'View Team', 'football_team' ),
		'view_items'            => __( 'View Team', 'football_team' ),
		'search_items'          => __( 'Search Team', 'football_team' ),
		'not_found'             => __( 'Not Team', 'football_team' ),
		'not_found_in_trash'    => __( 'Not Team in Trash', 'football_team' ),
		'featured_image'        => __( 'Featured Image', 'football_team' ),
		'set_featured_image'    => __( 'Set featured image', 'football_team' ),
		'remove_featured_image' => __( 'Remove featured image', 'football_team' ),
		'use_featured_image'    => __( 'Use as featured image', 'football_team' ),
		'insert_into_item'      => __( 'Insert into Team', 'football_team' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Team', 'football_team' ),
		'items_list'            => __( 'Team list', 'football_team' ),
		'items_list_navigation' => __( 'Team list navigation', 'football_team' ),
		'filter_items_list'     => __( 'Filter Team list', 'football_team' ),
	);
	$args = array(
		'label'                 => __( 'football_team', 'football_team' ),
		'description'           => __( 'football_team Description', 'football_team' ),
		'labels'                => $labels,
		'supports'              => false,
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( 'football_team', $args );

}
add_action( 'init', 'custom_post_type_team', 0 );


// code for add Player


function custom_post_type_Player() {
  $labels = array(
		'name'                  => _x( 'Football Player', 'Football Player ', 'Football Player' ),
		'singular_name'         => _x( 'Football Player ', 'Football Player', 'football_Player' ),
		'menu_name'             => __( 'Football Player', 'football_Player' ),
		'name_admin_bar'        => __( 'Football Player', 'football_Player' ),
		'archives'              => __( 'Football Player', 'football_Player' ),
		'attributes'            => __( 'Football Player', 'football_Player' ),
		'all_items'             => __( 'Football Player', 'football_Player' ),
		'add_new_item'          => __( 'Add New Player', 'football_Player' ),
		'add_new'               => __( 'Add Player', 'football_Player' ),
		'new_item'              => __( 'New Player', 'football_Player' ),
		'edit_item'             => __( 'Edit Player', 'football_Player' ),
		'update_item'           => __( 'Update Player', 'football_Player' ),
		'view_item'             => __( 'View Player', 'football_Player' ),
		'view_items'            => __( 'View Player', 'football_Player' ),
		'search_items'          => __( 'Search Player', 'football_Player' ),
		'not_found'             => __( 'Not Player', 'football_Player' ),
		'not_found_in_trash'    => __( 'Not Player in Trash', 'football_Player' ),
		'featured_image'        => __( 'Featured Image', 'football_Player' ),
		'set_featured_image'    => __( 'Set featured image', 'football_Player' ),
		'remove_featured_image' => __( 'Remove featured image', 'football_Player' ),
		'use_featured_image'    => __( 'Use as featured image', 'football_Player' ),
		'insert_into_item'      => __( 'Insert into Player', 'football_Player' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Player', 'football_Player' ),
		'items_list'            => __( 'Player list', 'football_Player' ),
		'items_list_navigation' => __( 'Player list navigation', 'football_Player' ),
		'filter_items_list'     => __( 'Filter Player list', 'football_Player' ),
	);
	$args = array(
		'label'                 => __( 'football_Player', 'football_Player' ),
		'description'           => __( 'football_Player Description', 'football_Player' ),
		'labels'                => $labels,
		'supports'              => false,
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( 'football_Player', $args );

}
add_action( 'init', 'custom_post_type_Player', 0 );