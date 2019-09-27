<?php

/**
 * Holds all functions of football receive data into wordpress .
 *
 * @author Dheeraj Tiwari <dheeraj@dheeraj@blsoftware.net>
 * @since 1.0.0
 * @package football data receive
 */

/*
* Creating a function to create our CPT
*/
 
function custom_post_type_parmission() {
 
    // Set UI labels for Custom Post Type
        $labels_food = array(
            'name'                => _x( 'Role Permission', 'Post Type General Name', 'twentythirteen' ),
            'singular_name'       => _x( 'Role Permission', 'Post Type Singular Name', 'twentythirteen' ),
            'menu_name'           => __( 'Permission Request', 'twentythirteen' ),
            'parent_item_colon'   => __( 'Parent Role Permission', 'twentythirteen' ),
            'all_items'           => __( 'All Role Permission', 'twentythirteen' ),
            'view_item'           => __( 'View Role Permission', 'twentythirteen' ),
            'add_new_item'        => __( 'Add New Role Permission', 'twentythirteen' ),
            'add_new'             => __( 'Add New', 'twentythirteen' ),
            'edit_item'           => __( 'Edit Role Permission', 'twentythirteen' ),
            'update_item'         => __( 'Update Role Permission', 'twentythirteen' ),
            'search_items'        => __( 'Search Role Permission', 'twentythirteen' ),
            'not_found'           => __( 'Not Found', 'twentythirteen' ),
            'not_found_in_trash'  => __( 'Not found in Trash', 'twentythirteen' ),
        );
         
    // Set other options for Custom Post Type
         
        $args_food = array(
            'label'               => __( 'Role Permission', 'twentythirteen' ),
            'description'         => __( 'Role Permission', 'twentythirteen' ),
            'labels'              => $labels_food,
            // Features this CPT supports in Post Editor
            'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
            // You can associate this CPT with a taxonomy or custom taxonomy. 
            'taxonomies'          => array( 'genres' ),
            /* A hierarchical CPT is like Pages and can have
            * Parent and child items. A non-hierarchical CPT
            * is like Posts.
            */ 
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 5,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'page',
        );
         
        // Registering your Custom Post Type
        register_post_type( 'role-parmission', $args_food );

        // Set UI labels for Custom Post Type
    }
     
    /* Hook into the 'init' action so that the function
    * Containing our post type registration is not 
    * unnecessarily executed. 
    */
     
   // add_action( 'init', 'custom_post_type_parmission', 0 );



    if($_GET['parmission_send']==true){
        $current_user = wp_get_current_user();
        $my_post = array(
          'post_title'    => $current_user->display_name,
          'post_content'  => $current_user->display_name." request for Host",
          'post_status'   => 'publish',
          'post_author'   => get_current_user_id(),
          'post_type'     => 'role-parmission'
        );
        //Insert the post into the database
        
        wp_insert_post( $my_post );
        $url=  get_site_url()."/account/";
        header("location:$url");
        die();
    }


    add_filter( 'manage_edit-role-parmission_columns', 'my_edit_role_parmission_columns' ) ;

		 function my_edit_role_parmission_columns( $columns ) {
             unset($columns['title']);
             unset($columns['comments']);
             unset($columns['author']);
             
             
            // $columns = array('heading' => 'Request by', 'request'=>'Request' ,'date'=>'Date' );
             $columns['heading'] = 'Request by';
             $columns['request'] = 'Request ';
			 
		 	 return $columns;
		 }

add_action( 'manage_role-parmission_posts_custom_column', 'my_manage_role_parmission_columns', 10, 2 );

function my_manage_role_parmission_columns( $column, $post_id ) {
	global $post;
    $postData= get_post($post_id);
	switch( $column ) {

		
		case 'heading' :
             
                 echo '<a href="'.admin_url( 'user-edit.php?user_id='.$postData->post_author, 'https' ).'">'.$postData->post_title.'</a>';

			break;
		case 'request' :
              echo $postData->post_content;
			break;


		default :
			break;
	}
	return $column;
}

