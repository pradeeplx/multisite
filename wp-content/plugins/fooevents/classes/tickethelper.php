<?php if ( ! defined( 'ABSPATH' ) ) exit;
class FooEvents_Ticket_Helper {
    
    public $Config;
    private $BarcodeHelper;
    public $MailHelper;
    public $Validation;
    public $CheckoutHelper;
    
    public function __construct($config) {
        
        $this->Config = $config;
        $this->register_ticket_post_type();
        
        //BarcodeHelper
        require_once($this->Config->classPath.'barcodehelper.php');
        $this->BarcodeHelper = new FooEvents_Barcode_Helper($this->Config);
        
        //MailHelper
        require_once($this->Config->classPath.'mailhelper.php');
        $this->MailHelper = new FooEvents_Mail_Helper($this->Config);

        add_action('admin_menu', array(&$this, 'hide_ticket_add_new'), 1, 2);
        add_action('manage_edit-event_magic_tickets_columns', array(&$this, 'add_admin_columns'), 10, 1);
        add_action('manage_event_magic_tickets_posts_custom_column', array(&$this, 'add_admin_column_content'), 10, 1);
        add_action('pre_get_posts', array($this, 'status_orderby'));
        add_action('add_meta_boxes', array(&$this, 'add_tickets_meta_boxes'), 1, 2);
        add_action('save_post', array(&$this, 'save_ticket_meta_boxes'), 1, 2);
        add_action('save_post', array(&$this, 'save_add_ticket_meta_boxes'), 1, 2);
        add_action('template_redirect', array( $this, 'redirect_ticket' ) );
        add_action('post_row_actions', array( $this, 'remove_ticket_view' ), 10, 2 );
        add_action('parse_query', array( $this, 'filter_unpaid_tickets' ) );
        add_action('admin_footer-edit.php', array( $this, 'display_bulk_resend' ));
        add_action('admin_action_resend_tickets',  array( $this, 'bulk_resend' ));
        add_action('wp_ajax_fetch_woocommerce_variations', array($this, 'fetch_woocommerce_variations'));
        add_action('wp_ajax_fetch_wordpress_user', array($this, 'fetch_wordpress_user'));
        add_action('wp_ajax_fetch_capture_attendee_details', array($this, 'fetch_capture_attendee_details'));
        add_action('admin_enqueue_scripts', array($this, 'disable_auto_save'), 1 );
        
        add_action('wp_ajax_resend_ticket', array($this, 'resend_ticket'));
        
        add_filter('pre_get_posts', array(&$this, 'tickets_where'), 10, 1);
        add_filter('manage_edit-event_magic_tickets_sortable_columns', array($this, 'sortable_admin_columns'));

    }
    
    /**
     * Registers the ticket post type.
     * 
     */
    private function register_ticket_post_type() {

        $labels = array(
		'name'               => __( 'Ticket', 'woocommerce-events' ),
		'singular_name'      => __( 'Ticket', 'woocommerce-events' ),
		'add_new'            => __( 'Add New', 'woocommerce-events' ),
		'add_new_item'       => __( 'Add New Ticket', 'woocommerce-events' ),
		'edit_item'          => __( 'Edit Ticket', 'woocommerce-events' ),
		'new_item'           => __( 'New Ticket', 'woocommerce-events' ),
		'all_items'          => __( 'All Tickets', 'woocommerce-events' ),
		'view_item'          => __( 'View Ticket', 'woocommerce-events' ),
		'search_items'       => __( 'Search Tickets', 'woocommerce-events' ),
		'not_found'          => __( 'No tickets found', 'woocommerce-events' ),
		'not_found_in_trash' => __( 'No tickets found in the Trash', 'woocommerce-events' ), 
		'parent_item_colon'  => '',
		'menu_name'          => __( 'Tickets', 'woocommerce-events' ));
        
        $args = array(
		'labels'        => $labels,
		'description'   => __( 'Event Tickets', 'woocommerce-events' ),
		'public'        => true,
		'exclude_from_search' => true,
		'menu_position' => 5,
		'supports'      => array('custom-fields'),
		'has_archive'   => true,
                //'capabilities'  => array( 'create_posts' => true ),
                'capabilities'  => array(
                    'publish_posts' => 'publish_event_magic_tickets',
                    'edit_posts' => 'edit_event_magic_tickets',
                    'edit_published_posts' => 'edit_published_event_magic_tickets',
                    'edit_others_posts' => 'edit_others_event_magic_tickets',
                    'delete_posts' => 'delete_event_magic_tickets',
                    'delete_others_posts' => 'delete_others_event_magic_tickets',
                    'read_private_posts' => 'read_private_event_magic_tickets',
                    'edit_post' => 'edit_event_magic_ticket',
                    'delete_post' => 'delete_event_magic_ticket',
                    'read_post' => 'read_event_magic_ticket',
                    'edit_published_post' => 'edit_published_event_magic_ticket',
                    'publish_post' => 'publish_event_magic_ticket',
                ),
                'capability_type' => array('event_magic_ticket','event_magic_tickets'),
                'map_meta_cap'  => true,
                'menu_icon'     => 'dashicons-tickets-alt',
                'has_archive'   => false,
                'publicly_queryable'    => false
	);
        
        register_post_type( 'event_magic_tickets', $args );	
        
    }

    /**
     * Adds admin columns to the event ticket custom post type.
     * 
     * @param array $columns
     * @return array $columns
     */
    public function add_admin_columns($columns) {
        
        $columns = array(
            'cb'                => __('Select', 'woocommerce-events'),
            'title'             => __('Title', 'woocommerce-events'),
            'Event'             => __('Event', 'woocommerce-events'),
            'Purchaser'         => __('Purchaser', 'woocommerce-events'),
            'Attendee'          => __('Attendee', 'woocommerce-events'),
            'PurchaseDate'      => __('Purchase Date', 'woocommerce-events'),
            'Status'            => __('Status', 'woocommerce-events')
        );
        
        return $columns;
    }
    
    /**
     * Adds column content to the event ticket custom post type.
     * 
     * @param string $column
     * @param int $post_id
     * @global object $post
     * 
     */
    public function add_admin_column_content($column) {
        
        global $post;
        global $woocommerce;
        
        $order_id = get_post_meta($post->ID, 'WooCommerceEventsOrderID', true);
        $customer_id = get_post_meta($post->ID, 'WooCommerceEventsCustomerID', true);
        $order = array();
        try {
            $order = new WC_Order( $order_id );
        } catch (Exception $e) {
            
        }   
        //echo "-->".$order_id; exit();
        switch( $column ) {
            case 'Event' :
                
                $WooCommerceEventsProductID = get_post_meta($post->ID, 'WooCommerceEventsProductID', true);
                
                echo '<a href="'.get_site_url().'/wp-admin/post.php?post='.$WooCommerceEventsProductID.'&action=edit">'.get_the_title($WooCommerceEventsProductID).'</a>';
                
                break;
            case 'Purchaser' :
                
                if(empty($order)) {
                    
                   echo "<i>Warning: WooCommerce order has been deleted.</i><br /><br />"; 
                    
                }
                
                if(!empty($customer_id) && !($customer_id instanceof WP_Error)) {
    
                    $WooCommerceEventsPurchaserFirstName = get_post_meta($post->ID, 'WooCommerceEventsPurchaserFirstName', true);
                    $WooCommerceEventsPurchaserLastName = get_post_meta($post->ID, 'WooCommerceEventsPurchaserLastName', true);
                    $WooCommerceEventsPurchaserEmail = get_post_meta($post->ID, 'WooCommerceEventsPurchaserEmail', true);
                    echo '<a href="'.get_site_url().'/wp-admin/user-edit.php?user_id='.$customer_id.'">'.$WooCommerceEventsPurchaserFirstName.' '.$WooCommerceEventsPurchaserLastName.' - ( '.$WooCommerceEventsPurchaserEmail.' )</a>';
                    
                } else {
                    
                    //guest account
                    try {
                        
                        if(!empty($order)) {
                            
                            echo $order->get_billing_first_name().' '.$order->get_billing_last_name().' - ( '.$order->get_billing_email().' )';
                        
                            
                        }
                         
                    } catch (Exception $e) {
            
                    }   
                
                }
                
                break;
                
            case 'Attendee' : 
                
                $WooCommerceEventsAttendeeName = get_post_meta($post->ID, 'WooCommerceEventsAttendeeName', true);
                $WooCommerceEventsAttendeeLastName = get_post_meta($post->ID, 'WooCommerceEventsAttendeeLastName', true);
                $WooCommerceEventsAttendeeEmail = get_post_meta($post->ID, 'WooCommerceEventsAttendeeEmail', true);
                echo $WooCommerceEventsAttendeeName.' '.$WooCommerceEventsAttendeeLastName.'- '.$WooCommerceEventsAttendeeEmail;
                
                break;
            
            case 'PurchaseDate' :
                
                echo $post->post_date;
                
                break;
            case 'Status' :
                
                $WooCommerceEventsProductID = get_post_meta($post->ID, 'WooCommerceEventsProductID', true);
                $WooCommerceEventsNumDays = (int)get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsNumDays', true);
                $WooCommerceEventsMultidayStatus = '';
                $WooCommerceEventsStatus = get_post_meta($post->ID, 'WooCommerceEventsStatus', true);
                
                if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
                    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
                }

                if (($this->is_plugin_active('fooevents_multi_day/fooevents-multi-day.php') || is_plugin_active_for_network('fooevents_multi_day/fooevents-multi-day.php')) && $WooCommerceEventsNumDays > 1) {

                    $Fooevents_Multiday_Events = new Fooevents_Multiday_Events();
                    $WooCommerceEventsMultidayStatus = $Fooevents_Multiday_Events->display_multiday_status_ticket_meta_all($post->ID);

                }
                
                if(empty($WooCommerceEventsMultidayStatus) || $WooCommerceEventsStatus == 'Unpaid' || $WooCommerceEventsStatus == 'Canceled' || $WooCommerceEventsStatus == 'Cancelled') {

                    echo $WooCommerceEventsStatus;

                } else {
                    
                    echo $WooCommerceEventsMultidayStatus;
                    
                }
                
                break;
            case 'Options' :
                
                
                break;
        }
        
    }
    
    /**
     * Make columns sortable
     * 
     */
    public function sortable_admin_columns($columns) {
        
        $columns['Status']          = 'Status';
        $columns['Event']           = 'Event';
        $columns['Purchaser']       = 'Purchaser';
        $columns['Attendee']        = 'Attendee';
        $columns['PurchaseDate']    = 'PurchaseDate';
        
        return $columns;
        
    }
    
    /**
     * Make the status field sortable
     * 
     */
    public function status_orderby($query) {
        
        if( ! is_admin() ) {
            return;
        }
        
        if(isset($_GET['post_type']) && $_GET['post_type'] == 'event_magic_tickets') {
            
            $orderby = $query->get( 'orderby');

            if( 'Status' == $orderby ) {
                $query->set('meta_key','WooCommerceEventsStatus');
                $query->set('orderby','meta_value');
            }
            
            if( 'Attendee' == $orderby ) {
                $query->set('meta_key','WooCommerceEventsAttendeeName');
                $query->set('orderby','meta_value');
            }
            
            if( 'Purchaser' == $orderby ) {
                $query->set('meta_key','WooCommerceEventsPurchaserFirstName');
                $query->set('orderby','meta_value');
            }
            
            if( 'Event' == $orderby ) {
                
                $query->set('meta_key','WooCommerceEventsProductName');
                $query->set('orderby','meta_value');

            }
            
        }

        return $query;
        
    }

    /**
     * Adds meta boxes to the tickets custom post type page.
     * 
     */
    public function add_tickets_meta_boxes() {
        
        $screens = array('event_magic_tickets');
        
        foreach ( $screens as $screen ) {
            
            if(isset($_GET['post'])) {
            
                add_meta_box(
                            'woocommerce_events_ticket_details',
                            __( 'Ticket Details', 'woocommerce-events' ),
                             array(&$this, 'add_tickets_meta_ticket_details'),
                            $screen, 'normal', 'high'
                    );

                add_meta_box(
                            'woocommerce_events_ticket_status',
                            __( 'Ticket Status', 'woocommerce-events' ),
                             array(&$this, 'add_tickets_meta_ticket_status'),
                            $screen, 'side', 'default'
                    );

                add_meta_box(
                            'woocommerce_events_ticket_resend_ticket',
                            __( 'Resend Ticket', 'woocommerce-events' ),
                             array(&$this, 'add_tickets_meta_ticket_resend_tickets'),
                            $screen, 'side', 'low'
                    );
            
            }
            
            if(!isset($_GET['post'])) {
                
                add_meta_box(
                            'woocommerce_events_ticket_add_event',
                            __( 'Event', 'woocommerce-events' ),
                             array(&$this, 'woocommerce_events_ticket_add_event'),
                            $screen, 'normal', 'high'
                );
                
                
            }
            
        }
        
    }
    
    /**
     * Displays manual add ticket form.
     * 
     */
    public function woocommerce_events_ticket_add_event() {
        
        $events = new WP_Query( array('post_type' => array('product'), 'posts_per_page' => -1, 'meta_query' => array( array( 'key' => 'WooCommerceEventsEvent', 'value' => 'Event' ) )) );
        $events = $events->get_posts();
        
        /*echo "<pre>";
        print_r($events);
        echo "</pre>";*/
        
        $users = get_users();

        require($this->Config->templatePath.'addticketmeta.php');
    }
    
    
    /**
     * Add ticket details meta box
     * 
     * @global object $post
     */
    public function add_tickets_meta_ticket_details() {
        
        global $post;
        global $woocommerce;
        
        $order_id = get_post_meta($post->ID, 'WooCommerceEventsOrderID', true);
        $customer_id = get_post_meta($post->ID, 'WooCommerceEventsCustomerID', true);
        
        $order = array();
        try {
            $order = new WC_Order( $order_id );
        } catch (Exception $e) {
            
        }  
        
        $message = '';
        $purchaser = array();
        $purchaser['customerFirstName']     = get_post_meta($post->ID, 'WooCommerceEventsPurchaserFirstName', true);
        $purchaser['customerLastName']      = get_post_meta($post->ID, 'WooCommerceEventsPurchaserLastName', true);
        $purchaser['customerEmail']         = get_post_meta($post->ID, 'WooCommerceEventsPurchaserEmail', true);
        
        if (!empty($order)) {
            
            $purchaser['customerPhone'] = $order->get_billing_phone();
            
        } else {
            
            $message = '<i>Warning: WooCommerce order has been deleted.</i>';
            $purchaser['customerPhone'] =  '';
            
        }
        
        if(!empty($customer_id)) {

            $purchaser['customerID'] = $customer_id;
        
        } else {
            
            $purchaser['customerID']            = 0;
            
        }
        
        $WooCommerceEventsProductID = get_post_meta($post->ID, 'WooCommerceEventsProductID', true);
        $WooCommerceEventsNumDays = (int)get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsNumDays', true);
        $WooCommerceEventsMultidayStatus = '';
        
        if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
            require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
        }
        
        if (($this->is_plugin_active('fooevents_multi_day/fooevents-multi-day.php') || is_plugin_active_for_network('fooevents_multi_day/fooevents-multi-day.php')) && $WooCommerceEventsNumDays > 1) {
            
            $Fooevents_Multiday_Events = new Fooevents_Multiday_Events();
            $WooCommerceEventsMultidayStatus = $Fooevents_Multiday_Events->display_multiday_status_ticket_meta_all($post->ID);
            
        }
        
        $WooCommerceEventsTicketID                          = get_post_meta($post->ID, 'WooCommerceEventsTicketID', true);
        $WooCommerceEventsTicketHash                        = get_post_meta($post->ID, 'WooCommerceEventsTicketHash', true);
        $WooCommerceEventsTicketType                        = get_post_meta($post->ID, 'WooCommerceEventsTicketType', true);
        $WooCommerceEventsAttendeeName                      = get_post_meta($post->ID, 'WooCommerceEventsAttendeeName', true);
        $WooCommerceEventsAttendeeLastName                  = get_post_meta($post->ID, 'WooCommerceEventsAttendeeLastName', true);
        $WooCommerceEventsAttendeeEmail                     = get_post_meta($post->ID, 'WooCommerceEventsAttendeeEmail', true);
        $WooCommerceEventsCaptureAttendeeTelephone          = get_post_meta($post->ID, 'WooCommerceEventsAttendeeTelephone', true);
        $WooCommerceEventsCaptureAttendeeCompany            = get_post_meta($post->ID, 'WooCommerceEventsAttendeeCompany', true);
        $WooCommerceEventsCaptureAttendeeDesignation        = get_post_meta($post->ID, 'WooCommerceEventsAttendeeDesignation', true);
        $WooCommerceEventsVariations                        = get_post_meta($post->ID, 'WooCommerceEventsVariations', true);
        
        $barcodeFileName = '';
        
        if(!empty($WooCommerceEventsTicketHash)) {
            
            $barcodeFileName = $WooCommerceEventsTicketHash.'-'.$WooCommerceEventsTicketID;
            
        } else {
            
            $barcodeFileName = $WooCommerceEventsTicketID;
            
        }
        
        if(!empty($WooCommerceEventsVariations) && !is_array($WooCommerceEventsVariations)) {

            $WooCommerceEventsVariations = json_decode($WooCommerceEventsVariations);

        }
        
        $WooCommerceEventsEvent                 = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsEvent', true);
        $WooCommerceEventsDate                  = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsDate', true);
        $WooCommerceEventsEndDate                  = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsEndDate', true);
        $WooCommerceEventsHour                  = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsHour', true);
        $WooCommerceEventsMinutes               = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsMinutes', true);
        $WooCommerceEventsHourEnd               = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsHourEnd', true);
        $WooCommerceEventsMinutesEnd            = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsMinutesEnd', true);
        $WooCommerceEventsLocation              = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsLocation', true);
        $WooCommerceEventsTicketLogo            = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsTicketLogo', true);
        $WooCommerceEventsTicketHeaderImage     = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsTicketHeaderImage', true);
        $WooCommerceEventsSupportContact        = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsSupportContact', true);
        $WooCommerceEventsGPS                   = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsGPS', true);
        $WooCommerceEventsDirections            = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsDirections', true);
        $WooCommerceEventsEmail                 = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsEmail', true);
        $WooCommerceEventsTicketText            = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsTicketText', true);
        $WooCommerceEventsTicketDisplayPrice    = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsTicketDisplayPrice', true);

        $WooCommerceEventsTitle             = get_the_title($WooCommerceEventsProductID);
        
        $WooCommerceEventsStatus = get_post_meta($post->ID, 'WooCommerceEventsStatus', true);
        
        $attendeeTerm                           = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsAttendeeOverride', true);
        
        if(empty($attendeeTerm)) {

            $attendeeTerm = get_option('globalWooCommerceEventsAttendeeOverride', true);

        }
        
        if(empty($attendeeTerm) || $attendeeTerm == 1) {

            $attendeeTerm = __('Attendee', 'woocommerce-events');

        }
        
        $barcodeURL =  $this->Config->barcodeURL;
        
        if (!file_exists($this->Config->barcodePath.$WooCommerceEventsTicketID.'.png')) {
            
            $this->BarcodeHelper->generate_barcode($WooCommerceEventsTicketID, $WooCommerceEventsTicketHash);
            
        }
        
        $this->BarcodeHelper->generate_barcode($WooCommerceEventsTicketID, $WooCommerceEventsTicketHash);
        
        require($this->Config->templatePath.'ticketdetailmeta.php');
        
        if ( ! function_exists( 'is_plugin_active_for_network' ) ) {

            require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

        }
        
        if ( $this->is_plugin_active('fooevents_custom_attendee_fields/fooevents-custom-attendee-fields.php') || is_plugin_active_for_network('fooevents_custom_attendee_fields/fooevents-custom-attendee-fields.php') ) {

            $Fooevents_Custom_Attendee_Fields = new Fooevents_Custom_Attendee_Fields();
            $Fooevents_Custom_Attendee_Fields->display_tickets_meta_custom_options($post);

        }
        
        if ( $this->is_plugin_active('fooevents_pdf_tickets/fooevents-pdf-tickets.php') || is_plugin_active_for_network('fooevents_pdf_tickets/fooevents-pdf-tickets.php')) {
                                     
            $FooEvents_PDF_Tickets = new FooEvents_PDF_Tickets();
            $FooEvents_PDF_Tickets->display_ticket_download($post->ID, $WooCommerceEventsTicketID, $this->Config->barcodePath, $this->Config->eventPluginURL);

        }
        
        if ( $this->is_plugin_active('fooevents_seating/fooevents-seating.php') || is_plugin_active_for_network('fooevents_seating/fooevents-seating.php') ) {

            $Fooevents_Seating = new Fooevents_Seating();
            $Fooevents_Seating->display_tickets_meta_seat_options($post);

        }

    }
    
    /**
     * Add ticket status meta box.
     * 
     * @global object $post
     */
    public function add_tickets_meta_ticket_status() {
        
        global $post;

        $WooCommerceEventsStatus = get_post_meta($post->ID, 'WooCommerceEventsStatus', true);
        $WooCommerceEventsProductID = get_post_meta($post->ID, 'WooCommerceEventsProductID', true);
        $WooCommerceEventsNumDays = (int)get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsNumDays', true);
        $WooCommerceEventsMultidayStatus = '';
        
        if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
            require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
        }
        
        if (($this->is_plugin_active('fooevents_multi_day/fooevents-multi-day.php') || is_plugin_active_for_network('fooevents_multi_day/fooevents-multi-day.php')) && $WooCommerceEventsNumDays > 1) {
        
            $Fooevents_Multiday_Events = new Fooevents_Multiday_Events();
            $WooCommerceEventsMultidayStatus = $Fooevents_Multiday_Events->display_multiday_status_ticket_form_meta($post->ID);
            
        }
        
        require($this->Config->templatePath.'ticketstatusmeta.php');
        
    }
    
    /**
     * Add resend ticket box
     * 
     * @global object $post
     */
    public function add_tickets_meta_ticket_resend_tickets() {
        
        global $post;
        global $woocommerce;
        
        $order_id = get_post_meta($post->ID, 'WooCommerceEventsOrderID', true);
        $customer_id = get_post_meta($post->ID, 'WooCommerceEventsCustomerID', true);
        
        $order = array();
        try {
            $order = new WC_Order( $order_id );
        } catch (Exception $e) {
            
        }  
        
        $purchaser = array();
        
        if (!empty($order)) {
            
            $purchaser['customerEmail'] = $order->get_billing_email();
            
        } else {
            
            $purchaser['customerEmail'] =  '';
                    
        }
        
        require($this->Config->templatePath.'ticketresendticketmeta.php');
        
    }
    
    /**
     * Saves tickets meta box settings
     * 
     * @param int $post_ID
     * @global object $post
     */
    public function save_ticket_meta_boxes($post_ID) {
        
        global $post;
        global $woocommerce;
        
        if (is_object($post) && isset( $_POST )) {
       
            if( $post->post_type == "event_magic_tickets" ) {

                if (isset( $_POST ) && isset($_POST['ticket_status']) && $_POST['ticket_status'] == 'true' && isset($_POST['WooCommerceEventsStatus']) ) {

                    update_post_meta($post_ID, 'WooCommerceEventsStatus', sanitize_text_field($_POST['WooCommerceEventsStatus'])); 

                }
                
                $WooCommerceEventsProductID = get_post_meta($post->ID, 'WooCommerceEventsProductID', true);
                $WooCommerceEventsNumDays = (int)get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsNumDays', true);
                
                if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
                    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
                }

                if (($this->is_plugin_active('fooevents_multi_day/fooevents-multi-day.php') || is_plugin_active_for_network('fooevents_multi_day/fooevents-multi-day.php')) && $WooCommerceEventsNumDays > 1) {
                    
                    $Fooevents_Multiday_Events = new Fooevents_Multiday_Events();
                    $WooCommerceEventsMultidayStatus = $Fooevents_Multiday_Events->capture_multiday_status_ticket_meta($post_ID);

                }

                if (!empty($_POST['WooCommerceEventsResendTicket']) && !empty($_POST['WooCommerceEventsResendTicketEmail'])) {
                    
                    $this->resend_ticket($post->ID);
     
                }

            }
       
        }
        
    }
    
    /**
     * Hides the add new menu item.
     * 
     * @global $submenu
     */
    public function hide_ticket_add_new() {
        
        /*global $submenu;
        
        unset($submenu['edit.php?post_type=event_magic_tickets'][10]);*/
        
    }
    
    /**
     * Display bulk ticket resend option.
     * 
     */
    public function display_bulk_resend(){

        global $post_type;

        if($post_type == 'event_magic_tickets') {
          ?>
          <script type="text/javascript">
            jQuery(document).ready(function() {
              jQuery('<option>').val('resend_tickets').text('<?php _e('Resend Tickets')?>').appendTo("select[name='action']");
              jQuery('<option>').val('resend_tickets').text('<?php _e('Resend Tickets')?>').appendTo("select[name='action2']");
            });
          </script>
          <?php
        }

    }
    
    /**
     * Bulk resend tickets.
     * 
     */
    public function bulk_resend() {
        
        $tickets = $_REQUEST['post'];
        
        foreach($tickets as $ticket) {
            
            $this->resend_ticket($ticket);
            
        }

    }
    
    /**
     * Redirects tickets custom most type
     * 
     */
    public function redirect_ticket() {
        
        $queried_post_type = get_query_var('post_type');
        if ( is_single() && 'event_magic_tickets' ==  $queried_post_type ) {
          wp_redirect( home_url(), 301 );
          exit;
        }
        
    }
    
    /**
     * Removes view link
     * 
     */
    public function remove_ticket_view($action, $post) {

        if ( $post->post_type == "event_magic_tickets" ) {
            
            unset ($action['view']);
            
        }
        return $action;
        
    }
    
    /**
     * Removes unpaid tickets from the ticket list
     * 
     */
    public function filter_unpaid_tickets($query) {
        
        //if( is_admin() AND $query->query['post_type'] == 'event_magic_tickets' ) {    

            /*$query->query_vars['meta_key']      = 'WooCommerceEventsStatus';
            $query->query_vars['meta_value']    = 'Unpaid'; 
            $query->query_vars['meta_compare']  = '!=';*/

        //}

        return $query;
        
    }
    
    /**
     * Searches for post meta
     * 
     * @param object $query
     */
    public function tickets_where($query) {

        if(isset($_GET['post_type']) && $_GET['post_type'] == 'event_magic_tickets' && $this->is_edit_page('edit')) {

            $custom_fields = array(
                "WooCommerceEventsAttendeeName",
                "WooCommerceEventsAttendeeEmail",
                "WooCommerceEventsCustomerID",
                "WooCommerceEventsVariations",
                "WooCommerceEventsPurchaserFirstName",
                "WooCommerceEventsPurchaserLastName",
                "WooCommerceEventsPurchaserEmail",
                "WooCommerceEventsStatus",
                "WooCommerceEventsTicketID",
                "WooCommerceEventsOrderID",
                "WooCommerceEventsProductName"
            );
            
            $globalWooCommerceEventsHideUnpaidTickets = get_option('globalWooCommerceEventsHideUnpaidTickets', true);
            
            $meta_query = array('relation' => 'AND');
            array_push($meta_query, array(
                'key' => "WooCommerceEventsStatus",
                'value' => '',
                'compare' => '!='
            ));
            
            if($globalWooCommerceEventsHideUnpaidTickets == 'yes') {
                
                array_push($meta_query, array(
                    'key' => "WooCommerceEventsStatus",
                    'value' => 'Unpaid',
                    'compare' => '!='
                ));
                
            }
            
            if(empty($query->query_vars['s']) && isset($_GET['s'])) {
                
                $query->query_vars['s'] = sanitize_text_field($_GET['s']);
                
            }
            
            $query->set("meta_query", $meta_query);
            
            $searchterm = $query->query_vars['s'];

            $query->query_vars['s'] = "";

            if ($searchterm != "") {
                $meta_query = array('relation' => 'OR');
                foreach($custom_fields as $cf) {
                    array_push($meta_query, array(
                        'key' => $cf,
                        'value' => $searchterm,
                        'compare' => 'LIKE'
                    ));
                }
  
                $query->set("meta_query", $meta_query);
            };
        
        }
        
    }
    
    /**
     * Fetch WooCommerce variations for manual add ticket 
     * 
     */
    public function fetch_woocommerce_variations() {
        
        global $woocommerce;
        
        if(!empty($_POST['productID'])) {
            
            $productID = sanitize_text_field($_POST['productID']);
            $product = wc_get_product($productID);

            $variations = '';
            if( $product->is_type( 'variable' ) ){

                $variations = $product->get_available_variations();

            }

            if(!empty($variations)) {
                
                echo '<h2>Variations</h2>';
                echo '<p class="form-field">';
                echo '<label>Variation: </label>';    
                echo '<select id="WooCommerceEventsSelectedVariation" name="WooCommerceEventsSelectedVariation">';
                
                foreach($variations as $variation) {
                    
                    echo '<option value="'.$variation['variation_id'].'">';
                    
                    foreach($variation['attributes'] as $attributeType => $attribute) {
                        
                        $variationNameOutput = str_replace('attribute_', '', $attributeType);
                        $variationNameOutput = str_replace('pa_', '', $variationNameOutput);
                        $variationNameOutput = str_replace('_', ' ', $variationNameOutput);
                        $variationNameOutput = str_replace('-', ' ', $variationNameOutput);
                        $variationNameOutput = str_replace('Pa_', '', $variationNameOutput);
                        $variationNameOutput = ucwords($variationNameOutput); 
                        echo $variationNameOutput.": ".$attribute." ";
                        
                    }
                    
                    echo "</option>";

                }
                
                echo '</select>';
                echo '</p>';
                
            }

        }
        
        exit();
    }
    
    /**
     * Fetch WooCommerce user for manual add ticket 
     * 
     */
    public function fetch_wordpress_user() {
        
        global $woocommerce;
        
        if(!empty($_POST['userID'])) {
            
            $user = get_user_by('id', sanitize_text_field($_POST['userID']));
            
            echo json_encode($user);
            
        }
        
        exit();
        
    }
    
    /**
     * Fetch WooCommerce attendee for manual add ticket 
     * 
     */
    public function fetch_capture_attendee_details() {
        
        $WooCommerceEventsCaptureAttendeeDetails  = get_post_meta(sanitize_text_field($_POST['productID']), 'WooCommerceEventsCaptureAttendeeDetails', true);
        
        echo json_encode(array("capture" => $WooCommerceEventsCaptureAttendeeDetails));
        
        exit();
    }
    
    /**
     * Save manual add ticket
     * 
     * @param int $post_ID
     */
    public function save_add_ticket_meta_boxes($post_ID) {
        
        global $post;
        
        //Check it's not an auto save routine
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
             return;

        if (is_object($post) && isset( $_POST )) {
            
            if( $post->post_type == "event_magic_tickets" && !empty($_POST['add_ticket']) ) {
                
                if (!session_id()) {
                    session_start();
                }

                
                global $woocommerce;

                wp_dequeue_script( 'autosave' );
                
                $error = false; 
                
                if(empty($_POST['WooCommerceEventsEvent'])) {

                    $error = true;

                }
                
                if(empty($_POST['WooCommerceEventsPurchaserFirstName'])) {

                    $error = true;

                }
                
                if(empty($_POST['WooCommerceEventsPurchaserUserName'])) {

                    $error = true;

                }

                if(empty($_POST['WooCommerceEventsPurchaserEmail'])) {

                    $error = true;

                }
                
                if(empty($_POST['add_ticket'])) {

                    $error = true;

                }
                
                if($error) {
                    
                    wp_redirect('edit.php?post_type=event_magic_tickets&fooevents_error=2');
                    exit();
                    
                }
                
                $userID = '';
                
                if(empty($_POST['WooCommerceEventsClientID'])) {
                    
                    $usernames = $this->get_usernames();
                    
                    if(in_array($_POST['WooCommerceEventsPurchaserUserName'], $usernames)) {
  
                        wp_redirect('edit.php?post_type=event_magic_tickets&fooevents_error=1');
                        exit();
                        
                    }
                    
                    $random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
                    $userID = wp_create_user(sanitize_text_field($_POST['WooCommerceEventsPurchaserUserName']), $random_password, sanitize_text_field($_POST['WooCommerceEventsPurchaserEmail']));
                    
                    if ($userID instanceof WP_Error) {
                        
                        if(array_key_exists("existing_user_email",$userID->errors)) {
                            
                            wp_redirect('edit.php?post_type=event_magic_tickets&fooevents_error=3');
                            
                        } else {
                            
                            wp_redirect('edit.php?post_type=event_magic_tickets&fooevents_error=2');
                            
                        }
                        
                        exit();
                        
                    }

                    if(!empty($userID)) {
                        
                        wp_update_user( array ('ID' => $userID, 'display_name' => $_POST['WooCommerceEventsPurchaserFirstName']));    
                        wp_update_user( array ('ID' => $userID, 'role' => 'Customer'));    
                    
                    }

                   
                } else {
                    
                    $userID = $_POST['WooCommerceEventsClientID'];
                    
                }
                
                if(!empty($userID) && !empty($_POST['add_ticket'])) {

                    $order_data = array(
                        'status'        => 'completed',
                        'customer_id'   => $userID,
                        'customer_note' => '',
                        'total'         => '',
                    );
                    
                    $address = array();
                    if(!empty($_POST['WooCommerceEventsPurchaserFirstName']) || !empty($_POST['WooCommerceEventsPurchaserEmail'])) {

                        $address = array(
                            'first_name' => $_POST['WooCommerceEventsPurchaserFirstName'],
                            'email'      => $_POST['WooCommerceEventsPurchaserEmail'],
                        );

                    }
                    
                    $productVariation = '';
                    $price = '';
                    
                    if(!empty($_POST['WooCommerceEventsSelectedVariation'])) {
                        
                        $productVariation = new WC_Product_Variation(sanitize_text_field($_POST['WooCommerceEventsSelectedVariation']));
                        
                        /*echo "<pre>";
                        print_r($productVariation);
                        echo "</pre>";
                        exit();*/
                        
                        $price = wc_price($productVariation->get_price());
                        
                    }
                    $productDetails=array();
                    $variations = array();
                    $x = 0;
                    
                    if(!empty($productVariation)) {
                        
                        foreach($productVariation->get_variation_attributes() as $attribute=>$attribute_value){

                            $productDetails['variation'][$attribute] = $attribute_value;
                            $variations[$attribute] = (string)$attribute_value;
                            $x++;

                        }
                        
                    }
                    
                    $id = ($_POST['WooCommerceEventsSelectedVariation']) ? $_POST['WooCommerceEventsSelectedVariation'] : $_POST['WooCommerceEventsEvent'];
                    remove_action('save_post', array(&$this, 'save_add_ticket_meta_boxes'), 1, 2);
                    $order = wc_create_order($order_data);
                    $order->add_product( get_product($id), 1, $productDetails );
                    $order->set_address( $address, 'billing' );
                    $order->set_address( $address, 'shipping' );
                    $order->calculate_totals();
                    $order->payment_complete();
                    
                    $post = array(
                            
                        'ID' => $post_ID,
                        'post_author' => $userID,
                        'post_content' => "Ticket",
                        'post_status' => "publish",
                        'post_title' => 'Assigned Ticket',
                        'post_type' => "event_magic_tickets"

                    );
                    
                    $user = get_user_by('id', $userID);
                    $rand = rand(111111,999999);
                    $ticketID = $post_ID.$rand;
                    $post['post_title'] = '#'.$ticketID;
                    $postID = wp_update_post( $post );

                    if(empty($price)) {
                        
                        $product = wc_get_product(sanitize_text_field($_POST['WooCommerceEventsEvent']));
                        $price = $product->get_price();
                        $price = wc_price($price);
                        
                    }
                    
                    //ticket fields
                    update_post_meta($post_ID, 'WooCommerceEventsCustomerID', $userID );   
                    update_post_meta($post_ID, 'WooCommerceEventsProductID', sanitize_text_field($_POST['WooCommerceEventsEvent']));   
                    update_post_meta($post_ID, 'WooCommerceEventsOrderID', $order->id );   
                    update_post_meta($post_ID, 'WooCommerceEventsTicketID', $ticketID);
                    update_post_meta($post_ID, 'WooCommerceEventsStatus', 'Not Checked In');
                    update_post_meta($post_ID, 'WooCommerceEventsAttendeeName', sanitize_text_field($_POST['WooCommerceEventsAttendeeName']));
                    update_post_meta($post_ID, 'WooCommerceEventsAttendeeLastName', sanitize_text_field($_POST['WooCommerceEventsAttendeeLastName']));
                    update_post_meta($post_ID, 'WooCommerceEventsAttendeeEmail',  sanitize_text_field($_POST['WooCommerceEventsAttendeeEmail']));
                    update_post_meta($post_ID, 'WooCommerceEventsAttendeeTelephone', '');
                    update_post_meta($post_ID, 'WooCommerceEventsAttendeeCompany', '');
                    update_post_meta($post_ID, 'WooCommerceEventsAttendeeDesignation', '');
                    update_post_meta($post_ID, 'WooCommerceEventsPurchaserFirstName', $user->data->display_name);
                    update_post_meta($post_ID, 'WooCommerceEventsPurchaserLastName', '');
                    update_post_meta($post_ID, 'WooCommerceEventsPurchaserEmail', $user->data->user_email);
                    update_post_meta($post_ID, 'WooCommerceEventsVariationID',sanitize_text_field($_POST['WooCommerceEventsSelectedVariation']));
                    update_post_meta($post_ID, 'WooCommerceEventsVariations', $variations);
                    update_post_meta($post_ID, 'WooCommerceEventsPrice', $price);
                    
                    $product = get_post(sanitize_text_field($_POST['WooCommerceEventsEvent']));
                    update_post_meta($post_ID, 'WooCommerceEventsProductName', $product->post_title);
                    
                }
                
            } else {
                
                return;
                exit();
                
            }
            
            remove_action('save_post', array(&$this, 'save_add_ticket_meta_boxes'), 1, 2);

            //exit();
            
        }    
        
    }
    
    /**
     * Disable ticket post type auto save.
     * 
     */
    public function disable_auto_save() {

        if ( 'event_magic_tickets' == get_post_type() ) {

            wp_dequeue_script( 'autosave' );
        
        }
        
    }
    
    
    /**
     * Check if is edit page
     * 
     * @return boolean
     */
    private function is_edit_page($new_edit = null) {
        
        global $pagenow;
        
        if (!is_admin()) { 
            
            return false;
        }    

        if($new_edit == "edit") {
            
            return in_array( $pagenow, array( 'edit.php',  ) );
            
        } elseif($new_edit == "new") {
            
            return in_array( $pagenow, array( 'post-new.php' ) );
            
        } else {
            
            return in_array( $pagenow, array( 'post.php', 'post-new.php' ) );
            
        }    
        
    }
    
    /**
     * Get usernames
     * 
     * @return array
     */
    private function get_usernames() {
        
        $users = get_users();
        $usernames = array();
        
        foreach($users as $user) {
            
            $usernames[] = $user->user_login;
            
        }
        
        return $usernames;
        
    }
    
    /**
     * Processes resend ticket
     * 
     * @param int $postID
     */
    public function resend_ticket($postID) {
        
        if(isset($_POST['postID'])) {
            
            $postID = $_POST['postID'];
            
        }

        /*error_reporting(E_ALL);
        ini_set('display_errors', '1');*/
        
        $ticket = $this->get_ticket_data($postID);
        
        $productID = get_post_meta($postID, 'WooCommerceEventsProductID', true);
        $WooCommerceEventsEvent = get_post_meta($productID, 'WooCommerceEventsEvent', true);
        
        $customerDetails = array();
        
        $order_id = get_post_meta($postID, 'WooCommerceEventsOrderID', true);
        $customer_id = get_post_meta($postID, 'WooCommerceEventsCustomerID', true);
        
        $order = array();
        try {
            $order = new WC_Order( $order_id );
        } catch (Exception $e) {
            
        }  

        $WooCommerceEventsEmailSubjectSingle = get_post_meta($productID, 'WooCommerceEventsEmailSubjectSingle', true);
        if(empty($WooCommerceEventsEmailSubjectSingle)) {

            $WooCommerceEventsEmailSubjectSingle  = __('{OrderNumber} Ticket', 'woocommerce-events');

        }
        $subject = str_replace('{OrderNumber}', '[#'.$order_id.']', $WooCommerceEventsEmailSubjectSingle);
        
        if (!empty($order)) {
            
            $customerDetails['customerFirstName']   = $order->get_billing_first_name();
            $customerDetails['customerLastName']    = $order->get_billing_last_name();
            $customerDetails['customerEmail']       = $order->get_billing_email();

        } else {
            
            $customerDetails['customerFirstName']   = '';
            $customerDetails['customerLastName']    = '';
            $customerDetails['customerEmail']       = '';
            
        }
        
        $WooCommerceEventsTicketTheme = get_post_meta($productID, 'WooCommerceEventsTicketTheme', true);
        if(empty($WooCommerceEventsTicketTheme)) {

            $WooCommerceEventsTicketTheme = $this->Config->emailTemplatePath;

        }
        
        $header = $this->MailHelper->parse_email_template($WooCommerceEventsTicketTheme.'/header.php', array(), $ticket); 
        $footer = $this->MailHelper->parse_email_template($WooCommerceEventsTicketTheme.'/footer.php', array(), $ticket);
        $ticket['ticketNumber'] = 1;
        $body = $this->MailHelper->parse_ticket_template($WooCommerceEventsTicketTheme.'/ticket.php', $ticket);
		$ticketBody = $header.$body.$footer;
        
        $to = '';
        if (isset($_POST['WooCommerceEventsResendTicketEmail'])) {
            
            $to = sanitize_text_field($_POST['WooCommerceEventsResendTicketEmail']);
            
        } elseif(!empty($WooCommerceEventsAttendeeEmail) && $WooCommerceEventsAttendeeEmail != 1) {
            
            $to = $WooCommerceEventsAttendeeEmail;
            
        } else {
            
            $to = $customerDetails['customerEmail'];
            
        }
        
        $attachment = '';
        if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
            require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
        }
        if ( $this->is_plugin_active('fooevents_pdf_tickets/fooevents-pdf-tickets.php') || is_plugin_active_for_network('fooevents_pdf_tickets/fooevents-pdf-tickets.php')) {

            $globalFooEventsPDFTicketsEnable = get_option( 'globalFooEventsPDFTicketsEnable' );
            $globalFooEventsPDFTicketsAttachHTMLTicket = get_option( 'globalFooEventsPDFTicketsAttachHTMLTicket' );

            if($globalFooEventsPDFTicketsEnable == 'yes') {

                $FooEvents_PDF_Tickets = new FooEvents_PDF_Tickets();
                $attachment = $FooEvents_PDF_Tickets->generate_ticket(array($ticket), $this->Config->barcodePath, $this->Config->path);
                $FooEventsPDFTicketsEmailText = get_post_meta($productID, 'FooEventsPDFTicketsEmailText', true);

                /*$header = $FooEvents_PDF_Tickets->parse_email_template('email-header.php');
                $footer = $FooEvents_PDF_Tickets->parse_email_template('email-footer.php');*/

              
                
                if($globalFooEventsPDFTicketsAttachHTMLTicket === 'yes') {
					$ticketBody = $FooEventsPDFTicketsEmailText.$ticketBody;
				} else {
                    
                    $header = $FooEvents_PDF_Tickets->parse_email_template('email-header.php');
                    $footer = $FooEvents_PDF_Tickets->parse_email_template('email-footer.php');

                    $ticketBody = $header.$FooEventsPDFTicketsEmailText.$footer;

                }
                
                if(empty($body)) {

                    $body = __('Your tickets are attached. Please print them and bring them to the event.', 'fooevents-pdf-tickets');

                }

            }

        }
        
        $mailStatus = $this->MailHelper->send_ticket($to, $subject, $ticketBody, $attachment);
        
        if(isset($_POST['postID'])) {
            
            echo json_encode(array('message' => "Mail has been sent."));
            exit();
            
        }
        
    }
    
    /**
     * Retrieves ticket data from database.
     * 
     * @param int $ticketID
     * @return type
     */
    public function get_ticket_data($ticketID) {

        $ticket = array();
        $WooCommerceEventsProductID                 = get_post_meta($ticketID, 'WooCommerceEventsProductID', true);
        $WooCommerceEventsOrderID                   = get_post_meta($ticketID, 'WooCommerceEventsOrderID', true);
        $WooCommerceEventsTicketType                = get_post_meta($ticketID, 'WooCommerceEventsTicketType', true);
        $WooCommerceEventsTicketID                  = get_post_meta($ticketID, 'WooCommerceEventsTicketID', true);
        $WooCommerceEventsTicketHash                = get_post_meta($ticketID, 'WooCommerceEventsTicketHash', true);
        $WooCommerceEventsStatus                    = get_post_meta($ticketID, 'WooCommerceEventsStatus', true);
        $ticket['WooCommerceEventsVariations']      = get_post_meta($ticketID, 'WooCommerceEventsVariations', true);
        
        $ticket['WooCommerceEventsPrice']           = get_post_meta($ticketID, 'WooCommerceEventsPrice', true);
        $ticket['WooCommerceEventsPriceSymbol']     = get_post_meta($ticketID, 'WooCommerceEventsPriceSymbol', true);

        $ticket['type'] = "HTML";

        
        if(!empty($ticket['WooCommerceEventsVariations'] ) && !is_array($ticket['WooCommerceEventsVariations'] )) {

            $ticket['WooCommerceEventsVariations']  = json_decode($ticket['WooCommerceEventsVariations'] );

        }

        $ticket['WooCommerceEventsVariationID']     = get_post_meta($ticketID, 'WooCommerceEventsVariationID', true);

        $customer = get_post_meta($WooCommerceEventsOrderID, '_customer_user', true);
        
        $order = array();
        try {
            $order = new WC_Order( $WooCommerceEventsOrderID );
        } catch (Exception $e) {
            
        }  
        
        $customerDetails = array(
                        'customerID'        => $customer
        );

        
        if (!empty($order)) {

            $customerDetails['customerFirstName']   = $order->get_billing_first_name();
            $customerDetails['customerLastName']    = $order->get_billing_last_name();
            $customerDetails['customerEmail']       = $order->get_billing_email();
            

        } else {
            
            $customerDetails['customerFirstName']   = '';
            $customerDetails['customerLastName']    = '';
            $customerDetails['customerEmail']       = '';
            
        }
        
        $ticket['fooevents_custom_attendee_fields_options'] = '';
        $ticket['fooevents_seating_options'] = '';
        
        $customer = get_post_meta($WooCommerceEventsOrderID, '_customer_user', true);
        
        if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
            require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
        }

        if ($this->is_plugin_active( 'fooevents_custom_attendee_fields/fooevents-custom-attendee-fields.php') || is_plugin_active_for_network('fooevents_custom_attendee_fields/fooevents-custom-attendee-fields.php')) {

            $Fooevents_Custom_Attendee_Fields = new Fooevents_Custom_Attendee_Fields();
            $ticket['fooevents_custom_attendee_fields_options'] = $Fooevents_Custom_Attendee_Fields->display_tickets_meta_custom_options_output($ticketID);

        }
        
         if ($this->is_plugin_active( 'fooevents_seating/fooevents-seating.php') || is_plugin_active_for_network('fooevents_seating/fooevents-seating.php')) {

            $Fooevents_Seating = new Fooevents_Seating();
            $ticket['fooevents_seating_options'] = $Fooevents_Seating->display_tickets_meta_seat_options_output($ticketID);

        }
        
        $WooCommerceEventsEvent                     = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsEvent', true);
        $WooCommerceEventsCaptureAttendeeDetails    = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsCaptureAttendeeDetails', true);
        $WooCommerceEventsSendEmailTickets          = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsSendEmailTickets', true);
        $WooCommerceEventsEmailSubjectSingle        = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsEmailSubjectSingle', true);
        
        //update ticket as paid
        if($WooCommerceEventsStatus == 'Unpaid') {

            update_post_meta($ticketID, 'WooCommerceEventsStatus', 'Not Checked In');

        }
        
        $ticket['WooCommerceEventsEvent']                       = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsEvent', true);
        $ticket['WooCommerceEventsDate']                        = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsDate', true);
        $ticket['WooCommerceEventsEndDate']                        = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsEndDate', true);
        $ticket['WooCommerceEventsSelectDate']                  = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsSelectDate', true);
        $ticket['WooCommerceEventsMultiDayType']                = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsMultiDayType', true);
        $ticket['WooCommerceEventsHour']                        = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsHour', true);
        $ticket['WooCommerceEventsMinutes']                     = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsMinutes', true);
        $ticket['WooCommerceEventsPeriod']                      = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsPeriod', true);
        $ticket['WooCommerceEventsHourEnd']                     = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsHourEnd', true);
        $ticket['WooCommerceEventsMinutesEnd']                  = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsMinutesEnd', true);
        $ticket['WooCommerceEventsEndPeriod']                   = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsEndPeriod', true);
        $ticket['WooCommerceEventsLocation']                    = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsLocation', true);
        $ticket['WooCommerceEventsTicketLogo']                  = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsTicketLogo', true);
        $ticket['WooCommerceEventsTicketHeaderImage']           = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsTicketHeaderImage', true);
        $ticket['WooCommerceEventsSupportContact']              = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsSupportContact', true);
        $ticket['WooCommerceEventsTicketBackgroundColor']       = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsTicketBackgroundColor', true);
        $ticket['WooCommerceEventsTicketButtonColor']           = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsTicketButtonColor', true);
        $ticket['WooCommerceEventsTicketTextColor']             = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsTicketTextColor', true);
        $ticket['WooCommerceEventsTicketPurchaserDetails']      = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsTicketPurchaserDetails', true);
        $ticket['WooCommerceEventsTicketAddCalendar']           = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsTicketAddCalendar', true);
        $ticket['WooCommerceEventsTicketDisplayDateTime']       = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsTicketDisplayDateTime', true);
        $ticket['WooCommerceEventsTicketDisplayBarcode']        = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsTicketDisplayBarcode', true);
        $ticket['WooCommerceEventsTicketText']                  = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsTicketText', true);
        $ticket['WooCommerceEventsDirections']                  = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsDirections', true);
        $ticket['WooCommerceEventsTicketDisplayPrice']          = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsTicketDisplayPrice', true);
        $ticket['WooCommerceEventsIncludeCustomAttendeeDetails'] = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsIncludeCustomAttendeeDetails', true);
        
        $ticket['WooCommerceEventsTicketType']                  = $WooCommerceEventsTicketType;
        $ticket['WooCommerceEventsProductID']                   = $WooCommerceEventsProductID;
        $ticket['WooCommerceEventsTicketID']                    = $WooCommerceEventsTicketID;
        $ticket['WooCommerceEventsTicketHash']                  = $WooCommerceEventsTicketHash;
        $ticket['WooCommerceEventsOrderID']                     = $WooCommerceEventsOrderID;
        $ticket['WooCommerceEventsSendEmailTickets']            = $WooCommerceEventsSendEmailTickets;
        
        if ($this->is_plugin_active('fooevents_multi_day/fooevents-multi-day.php') || is_plugin_active_for_network('fooevents_multi_day/fooevents-multi-day.php')) { 

            if($ticket['WooCommerceEventsMultiDayType'] == 'select') {

                $ticket['WooCommerceEventsDate'] = $ticket['WooCommerceEventsSelectDate'][0];
                $ticket['WooCommerceEventsEndDate'] = $ticket['WooCommerceEventsSelectDate'][1];

            }
        
        }
        
        $barcodeFileName = '';
        
        if(!empty($WooCommerceEventsTicketHash)) {
            
            $barcodeFileName = $WooCommerceEventsTicketHash.'-'.$WooCommerceEventsTicketID;
            
        } else {
            
            $barcodeFileName = $WooCommerceEventsTicketID;
            
        }
        
        $ticket['barcodeFileName'] = $barcodeFileName;
        
        $ticketDetails = get_post($WooCommerceEventsProductID);
        
        $ticket['WooCommerceEventsTicketText'] = apply_filters('meta_content', $ticket['WooCommerceEventsTicketText']);

        if(!empty($ticket['WooCommerceEventsTicketLogo'])) {
                
                $logo_id = $this->get_logo_id($ticket['WooCommerceEventsTicketLogo']);
                
                if($logo_id) {
                    
                    $ticket['WooCommerceEventsTicketLogoID'] = $this->get_logo_id($ticket['WooCommerceEventsTicketLogo']);
                    $ticket['WooCommerceEventsTicketLogoPath'] = get_attached_file($ticket['WooCommerceEventsTicketLogoID']);
                    
                } else {
                    
                    $ticket['WooCommerceEventsTicketLogoPath'] = $ticket['WooCommerceEventsTicketLogo'];
                    
                }
                
                
            }
	    
	if(!empty($ticket['WooCommerceEventsTicketHeaderImage'])) {
                
                $header_image_id = $this->get_logo_id($ticket['WooCommerceEventsTicketHeaderImage']);
                
                if($header_image_id) {
                    
                    $ticket['WooCommerceEventsTicketHeaderImageID'] = $this->get_logo_id($ticket['WooCommerceEventsTicketHeaderImage']);
                    $ticket['WooCommerceEventsTicketHeaderImagePath'] = get_attached_file($ticket['WooCommerceEventsTicketHeaderImageID']);
                    
                } else {
                    
                    $ticket['WooCommerceEventsTicketHeaderImagePath'] = $ticket['WooCommerceEventsTicketHeaderImage'];
                    
                }
                
                
            }
        
        $globalWooCommerceEventsTicketBackgroundColor   = get_option('globalWooCommerceEventsTicketBackgroundColor', true);
        $globalWooCommerceEventsTicketButtonColor       = get_option('globalWooCommerceEventsTicketButtonColor', true);
        $globalWooCommerceEventsTicketTextColor         = get_option('globalWooCommerceEventsTicketTextColor', true);
        $globalWooCommerceEventsTicketLogo              = get_option('globalWooCommerceEventsTicketLogo', true);

        if(empty($ticket['WooCommerceEventsTicketBackgroundColor'])) {

            $ticket['WooCommerceEventsTicketBackgroundColor'] = $globalWooCommerceEventsTicketBackgroundColor;

        }

        if(empty($ticket['WooCommerceEventsTicketButtonColor'])) {

            $ticket['WooCommerceEventsTicketButtonColor'] = $globalWooCommerceEventsTicketButtonColor;

        }

        if(empty($ticket['WooCommerceEventsTicketTextColor'])) {

            $ticket['WooCommerceEventsTicketTextColor'] = $globalWooCommerceEventsTicketTextColor;

        }

        if(empty($ticket['name'])) {

             $ticket['name'] = $ticketDetails->post_title;

        } 
        
        $timestamp                                              = time();
        $key                                                    = md5($WooCommerceEventsTicketID + $timestamp + $this->Config->salt);                              
        $ticket['cancelLink']                                   = get_site_url().'/wp-admin/admin-ajax.php?action=woocommerce_events_cancel&id='.$WooCommerceEventsTicketID.'&t='.$timestamp.'&k='.$key;

        if($WooCommerceEventsCaptureAttendeeDetails === 'on') {

            
            $ticket['WooCommerceEventsAttendeeTelephone']   = get_post_meta($ticketID, 'WooCommerceEventsAttendeeTelephone', true);
            $ticket['WooCommerceEventsAttendeeCompany']     = get_post_meta($ticketID, 'WooCommerceEventsAttendeeCompany', true);
            $ticket['WooCommerceEventsAttendeeDesignation'] = get_post_meta($ticketID, 'WooCommerceEventsAttendeeDesignation', true);
            $ticket['WooCommerceEventsAttendeeEmail']       = get_post_meta($ticketID, 'WooCommerceEventsAttendeeEmail', true);
            $ticket['customerFirstName']                    = get_post_meta($ticketID, 'WooCommerceEventsAttendeeName', true);
            $ticket['customerLastName']                     = get_post_meta($ticketID, 'WooCommerceEventsAttendeeLastName', true);
            $ticket['customerEmail']                        = $ticket['WooCommerceEventsAttendeeEmail'];

        } else {

            $ticket['customerFirstName']                    = $customerDetails['customerFirstName']; 
            $ticket['customerLastName']                     = $customerDetails['customerLastName'];
            $ticket['customerEmail']                        = $customerDetails['customerEmail'];

            if(!empty($customerDetails['billing_phone'])) {

                $ticket['WooCommerceEventsAttendeeTelephone']   = $customerDetails['billing_phone'];

            } else {

                $ticket['WooCommerceEventsAttendeeTelephone']   = '';

            }

            $ticket['WooCommerceEventsAttendeeCompany']     = '';
            $ticket['WooCommerceEventsAttendeeDesignation'] = '';

        }
        
        //generate barcode
        if (!file_exists($this->Config->barcodePath.$ticket['WooCommerceEventsTicketID'].'.png')) {

            $this->BarcodeHelper->generate_barcode($ticket['WooCommerceEventsTicketID'], $WooCommerceEventsTicketHash);

        }

        $ticket['FooEventsTicketFooterText'] = get_post_meta($WooCommerceEventsProductID, 'FooEventsTicketFooterText', true);
        
        if(empty($ticket['WooCommerceEventsTicketBackgroundColor'])) {

            $ticket['WooCommerceEventsTicketBackgroundColor'] = '#55AF71';

        }

        if(empty($ticket['WooCommerceEventsTicketButtonColor'])) {

            $ticket['WooCommerceEventsTicketButtonColor'] = '#55AF71';

        }

        if(empty($ticket['WooCommerceEventsTicketTextColor'])) {

            $ticket['WooCommerceEventsTicketTextColor'] = '#FFFFFF';

        }

        return $ticket;
        
    }
    
    /**
     * Returns image url attachment
     * 
     * @global object $wpdb
     * @param string $image_url
     * @return boolean
     */
    public function get_logo_id($image_url) {
        global $wpdb;
        $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url )); 

        if (!empty($attachment[0])) {

            return $attachment[0]; 

        } else {

            return false;

        }    
    }
    
    /**
     * Outputs notices to screen.
     * 
     * @param array $notices
     */
    private function output_notices($notices) {

        foreach ($notices as $notice) {

                echo "<div class='updated'><p>$notice</p></div>";

        }

    }
    
    /**
     * Checks if a plugin is active.
     * 
     * @param string $plugin
     * @return boolean
     */
    private function is_plugin_active( $plugin ) {

        return in_array( $plugin, (array) get_option( 'active_plugins', array() ) );

    }
    
}