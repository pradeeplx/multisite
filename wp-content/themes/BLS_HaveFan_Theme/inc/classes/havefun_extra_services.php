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
 
function custom_post_type() {
 
    // Set UI labels for Custom Post Type
        $labels_food = array(
            'name'                => _x( 'Food Services', 'Post Type General Name', 'twentythirteen' ),
            'singular_name'       => _x( 'Food Service', 'Post Type Singular Name', 'twentythirteen' ),
            'menu_name'           => __( 'Food Services', 'twentythirteen' ),
            'parent_item_colon'   => __( 'Parent Food Service', 'twentythirteen' ),
            'all_items'           => __( 'All Food Services', 'twentythirteen' ),
            'view_item'           => __( 'View Food Service', 'twentythirteen' ),
            'add_new_item'        => __( 'Add New Food Service', 'twentythirteen' ),
            'add_new'             => __( 'Add New', 'twentythirteen' ),
            'edit_item'           => __( 'Edit Food Service', 'twentythirteen' ),
            'update_item'         => __( 'Update Food Service', 'twentythirteen' ),
            'search_items'        => __( 'Search Food Service', 'twentythirteen' ),
            'not_found'           => __( 'Not Found', 'twentythirteen' ),
            'not_found_in_trash'  => __( 'Not found in Trash', 'twentythirteen' ),
        );
         
    // Set other options for Custom Post Type
         
        $args_food = array(
            'label'               => __( 'food service', 'twentythirteen' ),
            'description'         => __( 'Food Service ', 'twentythirteen' ),
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
        register_post_type( 'food-service', $args_food );

        // Set UI labels for Custom Post Type
        $labels_drink = array(
            'name'                => _x( 'Drink Services', 'Post Type General Name', 'twentythirteen' ),
            'singular_name'       => _x( 'Drink Service', 'Post Type Singular Name', 'twentythirteen' ),
            'menu_name'           => __( 'Drinks Services', 'twentythirteen' ),
            'parent_item_colon'   => __( 'Parent Drinks', 'twentythirteen' ),
            'all_items'           => __( 'All Drinks', 'twentythirteen' ),
            'view_item'           => __( 'View Drink', 'twentythirteen' ),
            'add_new_item'        => __( 'Add New Drink', 'twentythirteen' ),
            'add_new'             => __( 'Add New', 'twentythirteen' ),
            'edit_item'           => __( 'Edit Drink', 'twentythirteen' ),
            'update_item'         => __( 'Update Drink', 'twentythirteen' ),
            'search_items'        => __( 'Search Drink', 'twentythirteen' ),
            'not_found'           => __( 'Not Found', 'twentythirteen' ),
            'not_found_in_trash'  => __( 'Not found in Trash', 'twentythirteen' ),
        );
         
    // Set other options for Custom Post Type
         
        $args_drink = array(
            'label'               => __( 'drink service', 'twentythirteen' ),
            'description'         => __( 'Drink Service', 'twentythirteen' ),
            'labels'              => $labels_drink,
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
        register_post_type( 'drink-service', $args_drink );

        // Set UI labels for Custom Post Type
        $labels_ticket = array(
            'name'                => _x( 'Ticket Services', 'Post Type General Name', 'twentythirteen' ),
            'singular_name'       => _x( 'Ticket Service', 'Post Type Singular Name', 'twentythirteen' ),
            'menu_name'           => __( 'Tickets Service ', 'twentythirteen' ),
            'parent_item_colon'   => __( 'Parent Tickets', 'twentythirteen' ),
            'all_items'           => __( 'All Tickets', 'twentythirteen' ),
            'view_item'           => __( 'View Ticket', 'twentythirteen' ),
            'add_new_item'        => __( 'Add New Ticket', 'twentythirteen' ),
            'add_new'             => __( 'Add New', 'twentythirteen' ),
            'edit_item'           => __( 'Edit Ticket', 'twentythirteen' ),
            'update_item'         => __( 'Update Ticket', 'twentythirteen' ),
            'search_items'        => __( 'Search Ticket', 'twentythirteen' ),
            'not_found'           => __( 'Not Found', 'twentythirteen' ),
            'not_found_in_trash'  => __( 'Not found in Trash', 'twentythirteen' ),
        );
         
    // Set other options for Custom Post Type
         
        $args_ticket = array(
            'label'               => __( 'ticket service', 'twentythirteen' ),
            'description'         => __( 'Ticket service ', 'twentythirteen' ),
            'labels'              => $labels_ticket,
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
        register_post_type( 'ticket-service', $args_ticket );

        // Set UI labels for Custom Post Type
        $labels_transport = array(
            'name'                => _x( 'Transport Services', 'Post Type General Name', 'twentythirteen' ),
            'singular_name'       => _x( 'Transport Service', 'Post Type Singular Name', 'twentythirteen' ),
            'menu_name'           => __( 'Transports Services', 'twentythirteen' ),
            'parent_item_colon'   => __( 'Parent Transports', 'twentythirteen' ),
            'all_items'           => __( 'All Transports', 'twentythirteen' ),
            'view_item'           => __( 'View Transport', 'twentythirteen' ),
            'add_new_item'        => __( 'Add New Transport', 'twentythirteen' ),
            'add_new'             => __( 'Add New', 'twentythirteen' ),
            'edit_item'           => __( 'Edit Transport', 'twentythirteen' ),
            'update_item'         => __( 'Update Transport', 'twentythirteen' ),
            'search_items'        => __( 'Search Transport', 'twentythirteen' ),
            'not_found'           => __( 'Not Found', 'twentythirteen' ),
            'not_found_in_trash'  => __( 'Not found in Trash', 'twentythirteen' ),
        );
         
    // Set other options for Custom Post Type
         
        $args_transport = array(
            'label'               => __( 'transport service', 'twentythirteen' ),
            'description'         => __( 'Transport Service', 'twentythirteen' ),
            'labels'              => $labels_transport,
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
        register_post_type( 'transport-service', $args_transport );

        // Set UI labels for Custom Post Type
        $labels_tools = array(
            'name'                => _x( 'Tool Serives', 'Post Type General Name', 'twentythirteen' ),
            'singular_name'       => _x( 'Tool Serive', 'Post Type Singular Name', 'twentythirteen' ),
            'menu_name'           => __( 'Tool Serives', 'twentythirteen' ),
            'parent_item_colon'   => __( 'Parent Tool Serives', 'twentythirteen' ),
            'all_items'           => __( 'All Tool Serives', 'twentythirteen' ),
            'view_item'           => __( 'View Tool Serives', 'twentythirteen' ),
            'add_new_item'        => __( 'Add New Tool Serives', 'twentythirteen' ),
            'add_new'             => __( 'Add New', 'twentythirteen' ),
            'edit_item'           => __( 'Edit Tool Serives', 'twentythirteen' ),
            'update_item'         => __( 'Update Tool Serives', 'twentythirteen' ),
            'search_items'        => __( 'Search Tool Serives', 'twentythirteen' ),
            'not_found'           => __( 'Not Found', 'twentythirteen' ),
            'not_found_in_trash'  => __( 'Not found in Trash', 'twentythirteen' ),
        );
         
    // Set other options for Custom Post Type
         
        $args_tools = array(
            'label'               => __( 'tool', 'twentythirteen' ),
            'description'         => __( 'Tool', 'twentythirteen' ),
            'labels'              => $labels_tools,
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
        register_post_type( 'tool-service', $args_tools );

        // Set UI labels for Custom Post Type
        $labels_others = array(
            'name'                => _x( 'Other Serives', 'Post Type General Name', 'twentythirteen' ),
            'singular_name'       => _x( 'Other Serive', 'Post Type Singular Name', 'twentythirteen' ),
            'menu_name'           => __( 'Other Serives', 'twentythirteen' ),
            'parent_item_colon'   => __( 'Parent Other Serives', 'twentythirteen' ),
            'all_items'           => __( 'All Other Serives', 'twentythirteen' ),
            'view_item'           => __( 'View Other Serives', 'twentythirteen' ),
            'add_new_item'        => __( 'Add New Other Serives', 'twentythirteen' ),
            'add_new'             => __( 'Add New', 'twentythirteen' ),
            'edit_item'           => __( 'Edit Other Serives', 'twentythirteen' ),
            'update_item'         => __( 'Update Other Serives', 'twentythirteen' ),
            'search_items'        => __( 'Search Other Serives', 'twentythirteen' ),
            'not_found'           => __( 'Not Found', 'twentythirteen' ),
            'not_found_in_trash'  => __( 'Not found in Trash', 'twentythirteen' ),
        );
         
    // Set other options for Custom Post Type
         
        $args_others = array(
            'label'               => __( 'other serice', 'twentythirteen' ),
            'description'         => __( 'Other serice', 'twentythirteen' ),
            'labels'              => $labels_others,
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
        register_post_type( 'other-services', $args_others );
     
    }
     
    /* Hook into the 'init' action so that the function
    * Containing our post type registration is not 
    * unnecessarily executed. 
    */
     
    add_action( 'init', 'custom_post_type', 0 );


 // $haveFun_Extra_Services = new HaveFun_Extra_Services();
 //$dd = $HaveFun_RestAPI->update_wcfm_paid_services( array('regular_price' => '25', 'name' => 'demo'), 2, 240);
// var_dump($dd);


function havefuneventcl_widgets_init() {
    register_sidebar( array(
        'name' => __( 'Event Calendar Sidebar', 'havefuneventcl' ),
        'id' => 'event-page-sidebar',
        'before_widget' => '<div>',
        'after_widget' => '</div>',
        'before_title' => '<h1>',
        'after_title' => '</h1>',
    ) );
}
add_action( 'widgets_init', 'havefuneventcl_widgets_init' );