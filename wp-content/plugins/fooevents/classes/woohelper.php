<?php if ( ! defined( 'ABSPATH' ) ) exit;
class FooEvents_Woo_Helper {
	
    public  $Config;
    public  $TicketHelper;
    private $BarcodeHelper;
    public  $MailHelper;

    public function __construct($config) {

        $this->check_woocommerce_exists();
        $this->Config = $config;

        //TicketHelper
        require_once($this->Config->classPath.'tickethelper.php');
        $this->TicketHelper = new FooEvents_Ticket_Helper($this->Config);

        //BarcodeHelper
        require_once($this->Config->classPath.'barcodehelper.php');
        $this->BarcodeHelper = new FooEvents_Barcode_Helper($this->Config);
        
        //MailHelper
        require_once($this->Config->classPath.'mailhelper.php');
        $this->MailHelper = new FooEvents_Mail_Helper($this->Config);
        
        add_action('woocommerce_product_tabs', array(&$this, 'add_front_end_tab'), 10, 2);
        add_action('woocommerce_order_status_completed', array(&$this, 'process_order_tickets'), 10, 1);
        add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'add_product_options_tab' ) );
        add_action( 'woocommerce_product_data_panels', array( $this, 'add_product_options_tab_options' ) );
        add_action( 'woocommerce_process_product_meta', array( $this, 'process_meta_box' ) );
        add_action( 'wp_ajax_woocommerce_events_csv', array( $this, 'woocommerce_events_csv' ) );
        add_action( 'wp_ajax_nopriv_woocommerce_events_csv', array( $this, 'woocommerce_events_csv' ) );
        add_action( 'wp_ajax_woocommerce_events_attendee_badges', array( $this, 'woocommerce_events_attendee_badges' ) );
        add_action( 'wp_ajax_nopriv_woocommerce_events_attendee_badges', array( $this, 'woocommerce_events_attendee_badges' ) );
       // add_action( 'wp_ajax_nopriv_woocommerce_events_csv', array( $this, 'woocommerce_events_csv' ) );
        add_action('woocommerce_thankyou_order_received_text', array( $this, 'display_thank_you_text' ));
        add_action('woocommerce_order_status_cancelled', array($this, 'order_status_cancelled'));
        add_action('woocommerce_order_status_completed', array(&$this, 'order_status_completed_cancelled'), 10, 1);
        
        add_filter( 'woocommerce_events_meta_format', 'wptexturize');
        add_filter( 'woocommerce_events_meta_format', 'convert_smilies');
        add_filter( 'woocommerce_events_meta_format', 'convert_chars');
        add_filter( 'woocommerce_events_meta_format', 'wpautop');
        add_filter( 'woocommerce_events_meta_format', 'shortcode_unautop');
        add_filter( 'woocommerce_events_meta_format', 'prepend_attachment');


    }

    /**
     * Checks if the WooCommerce plugin exists
     * 
     */
    public function check_woocommerce_exists() {

        if ( !class_exists( 'WooCommerce' ) ) {

                $this->output_notices(array(__( 'WooCommerce is required for FooEvents. Please install and activate the latest version of WooCommerce.', 'woocommerce-events' )));

        } 

    }

    /**
     * Initializes the WooCommerce meta box
     * 
     */
    public function add_product_options_tab() {

        echo '<li class="custom_tab_fooevents"><a href="#woocommerce_events_data">'.__( ' Event', 'woocommerce-events' ).'</a></li>';

    }


 /**
     * Displays the event form 
     * 
     * @param object $post
     */
    public function add_product_options_tab_options() {

        global $post;

        $WooCommerceEventsEvent                     = get_post_meta($post->ID, 'WooCommerceEventsEvent', true);
        $WooCommerceEventsDate                      = get_post_meta($post->ID, 'WooCommerceEventsDate', true);
        $WooCommerceEventsHour                      = get_post_meta($post->ID, 'WooCommerceEventsHour', true);
        $WooCommerceEventsPeriod                    = get_post_meta($post->ID, 'WooCommerceEventsPeriod', true);
        $WooCommerceEventsMinutes                   = get_post_meta($post->ID, 'WooCommerceEventsMinutes', true);
        $WooCommerceEventsHourEnd                   = get_post_meta($post->ID, 'WooCommerceEventsHourEnd', true);
        $WooCommerceEventsMinutesEnd                = get_post_meta($post->ID, 'WooCommerceEventsMinutesEnd', true);
        $WooCommerceEventsEndPeriod                 = get_post_meta($post->ID, 'WooCommerceEventsEndPeriod', true);
        $WooCommerceEventsLocation                  = get_post_meta($post->ID, 'WooCommerceEventsLocation', true);
        $WooCommerceEventsTicketLogo                = get_post_meta($post->ID, 'WooCommerceEventsTicketLogo', true);
        $WooCommerceEventsPrintTicketLogo           = get_post_meta($post->ID, 'WooCommerceEventsPrintTicketLogo', true);
        $WooCommerceEventsTicketHeaderImage         = get_post_meta($post->ID, 'WooCommerceEventsTicketHeaderImage', true);
        $WooCommerceEventsSupportContact            = get_post_meta($post->ID, 'WooCommerceEventsSupportContact', true);
        $WooCommerceEventsGPS                       = get_post_meta($post->ID, 'WooCommerceEventsGPS', true);
        $WooCommerceEventsGoogleMaps                = get_post_meta($post->ID, 'WooCommerceEventsGoogleMaps', true);
        $WooCommerceEventsDirections                = get_post_meta($post->ID, 'WooCommerceEventsDirections', true);
        $WooCommerceEventsEmail                     = get_post_meta($post->ID, 'WooCommerceEventsEmail', true);
        $WooCommerceEventsTicketBackgroundColor     = get_post_meta($post->ID, 'WooCommerceEventsTicketBackgroundColor', true);
        $WooCommerceEventsTicketButtonColor         = get_post_meta($post->ID, 'WooCommerceEventsTicketButtonColor', true);
        $WooCommerceEventsTicketTextColor           = get_post_meta($post->ID, 'WooCommerceEventsTicketTextColor', true);
        $WooCommerceEventsTicketPurchaserDetails    = get_post_meta($post->ID, 'WooCommerceEventsTicketPurchaserDetails', true);
        $WooCommerceEventsTicketAddCalendar         = get_post_meta($post->ID, 'WooCommerceEventsTicketAddCalendar', true);
        $WooCommerceEventsTicketDisplayDateTime     = get_post_meta($post->ID, 'WooCommerceEventsTicketDisplayDateTime', true);
        $WooCommerceEventsTicketDisplayBarcode      = get_post_meta($post->ID, 'WooCommerceEventsTicketDisplayBarcode', true);
        $WooCommerceEventsTicketDisplayPrice            = get_post_meta($post->ID, 'WooCommerceEventsTicketDisplayPrice', true);
        $WooCommerceEventsTicketText                    = get_post_meta($post->ID, 'WooCommerceEventsTicketText', true);
        $WooCommerceEventsThankYouText                  = get_post_meta($post->ID, 'WooCommerceEventsThankYouText', true);
        $WooCommerceEventsEventDetailsText              = get_post_meta($post->ID, 'WooCommerceEventsEventDetailsText', true);
        $WooCommerceEventsCaptureAttendeeDetails        = get_post_meta($post->ID, 'WooCommerceEventsCaptureAttendeeDetails', true);
        $WooCommerceEventsEmailAttendee                 = get_post_meta($post->ID, 'WooCommerceEventsEmailAttendee', true);
        $WooCommerceEventsSendEmailTickets              = get_post_meta($post->ID, 'WooCommerceEventsSendEmailTickets', true);
        $WooCommerceEventsCaptureAttendeeTelephone      = get_post_meta($post->ID, 'WooCommerceEventsCaptureAttendeeTelephone', true);
        $WooCommerceEventsCaptureAttendeeCompany        = get_post_meta($post->ID, 'WooCommerceEventsCaptureAttendeeCompany', true);
        $WooCommerceEventsCaptureAttendeeDesignation    = get_post_meta($post->ID, 'WooCommerceEventsCaptureAttendeeDesignation', true);

        $WooCommerceEventsViewSeatingChart         = get_post_meta($post->ID, 'WooCommerceEventsViewSeatingChart', true);

        $WooCommerceEventsExportUnpaidTickets           = get_post_meta($post->ID, 'WooCommerceEventsExportUnpaidTickets', true);
        $WooCommerceEventsExportBillingDetails          = get_post_meta($post->ID, 'WooCommerceEventsExportBillingDetails', true);
        
        $WooCommerceBadgeSize                           = get_post_meta($post->ID, 'WooCommerceBadgeSize', true);
        $WooCommerceBadgeField1                         = get_post_meta($post->ID, 'WooCommerceBadgeField1', true);
        $WooCommerceBadgeField2                         = get_post_meta($post->ID, 'WooCommerceBadgeField2', true);
        $WooCommerceBadgeField3                         = get_post_meta($post->ID, 'WooCommerceBadgeField3', true);

        $WooCommercePrintTicketSize                     = get_post_meta($post->ID, 'WooCommercePrintTicketSize', true);
        $WooCommercePrintTicketField1                   = get_post_meta($post->ID, 'WooCommercePrintTicketField1', true);
        $WooCommercePrintTicketField1_font              = get_post_meta($post->ID, 'WooCommercePrintTicketField1_font', true);
        $WooCommercePrintTicketField2                   = get_post_meta($post->ID, 'WooCommercePrintTicketField2', true);
        $WooCommercePrintTicketField2_font              = get_post_meta($post->ID, 'WooCommercePrintTicketField2_font', true);
        $WooCommercePrintTicketField3                   = get_post_meta($post->ID, 'WooCommercePrintTicketField3', true);
        $WooCommercePrintTicketField3_font              = get_post_meta($post->ID, 'WooCommercePrintTicketField3_font', true);
        $WooCommercePrintTicketField4                   = get_post_meta($post->ID, 'WooCommercePrintTicketField4', true);
        $WooCommercePrintTicketField4_font              = get_post_meta($post->ID, 'WooCommercePrintTicketField4_font', true);
        $WooCommercePrintTicketField5                   = get_post_meta($post->ID, 'WooCommercePrintTicketField5', true);
        $WooCommercePrintTicketField5_font              = get_post_meta($post->ID, 'WooCommercePrintTicketField5_font', true);
        $WooCommercePrintTicketField6                   = get_post_meta($post->ID, 'WooCommercePrintTicketField6', true);
        $WooCommercePrintTicketField6_font              = get_post_meta($post->ID, 'WooCommercePrintTicketField6_font', true);
        
        $WooCommerceEventsPrintTicketLogoOption         = get_post_meta($post->ID, 'WooCommerceEventsPrintTicketLogoOption', true);
        $WooCommerceEventsCutLinesPrintTicket           = get_post_meta($post->ID, 'WooCommerceEventsCutLinesPrintTicket', true);

        
        $WooCommerceEventsCutLines                      = get_post_meta($post->ID, 'WooCommerceEventsCutLines', true);

        $WooCommerceEventsEmailSubjectSingle            = get_post_meta($post->ID, 'WooCommerceEventsEmailSubjectSingle', true);
        $WooCommerceEventsTicketTheme                   = get_post_meta($post->ID, 'WooCommerceEventsTicketTheme', true);
        
        $WooCommerceEventsAttendeeOverride              = get_post_meta($post->ID, 'WooCommerceEventsAttendeeOverride', true);
        $WooCommerceEventsTicketOverride                = get_post_meta($post->ID, 'WooCommerceEventsTicketOverride', true);

        $WooCommerceEventsViewSeatingChart         = get_post_meta($post->ID, 'WooCommerceEventsViewSeatingChart', true);

        $globalWooCommerceEventsGoogleMapsAPIKey = get_option('globalWooCommerceEventsGoogleMapsAPIKey', true);
    
        if($globalWooCommerceEventsGoogleMapsAPIKey == 1) {

            $globalWooCommerceEventsGoogleMapsAPIKey = '';

        }

        if(empty($WooCommerceEventsEmailSubjectSingle)) {

            $WooCommerceEventsEmailSubjectSingle = __('{OrderNumber} Ticket', 'woocommerce-events');

        }

        $globalWooCommerceEventsTicketBackgroundColor   = get_option('globalWooCommerceEventsTicketBackgroundColor', true);
        $globalWooCommerceEventsTicketButtonColor       = get_option('globalWooCommerceEventsTicketButtonColor', true);
        $globalWooCommerceEventsTicketTextColor         = get_option('globalWooCommerceEventsTicketTextColor', true);
        $globalWooCommerceEventsTicketLogo              = get_option('globalWooCommerceEventsTicketLogo', true);
        $globalWooCommerceEventsTicketHeaderImage       = get_option('globalWooCommerceEventsTicketHeaderImage', true);

        $endDate = '';
        $numDays = '';
        $multiDayType = '';
        $multidayTerm = '';
        $eventBackgroundColour = '';
        $eventTextColour = '';
        if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
            require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
        }
        
        if ($this->is_plugin_active('fooevents_multi_day/fooevents-multi-day.php') || is_plugin_active_for_network('fooevents_multi_day/fooevents-multi-day.php')) {

            $Fooevents_Multiday_Events = new Fooevents_Multiday_Events();
            $endDate = $Fooevents_Multiday_Events->generate_end_date_option($post);
            $numDays = $Fooevents_Multiday_Events->generate_num_days_option($post);
            $multiDayType = $Fooevents_Multiday_Events->generate_multiday_type_option($post);
            $multidayTerm = $Fooevents_Multiday_Events->generate_multiday_term_option($post);
            
        }
        
        if ($this->is_plugin_active('fooevents_calendar/fooevents-calendar.php') || is_plugin_active_for_network('fooevents_calendar/fooevents-calendar.php')) {
            
            $FooEvents_Calendar = new FooEvents_Calendar();
            $eventBackgroundColour = $FooEvents_Calendar->generate_event_background_color_option($post);
            $eventTextColour = $FooEvents_Calendar->generate_event_background_text_option($post);
            
        }

        $eventbrite_option = '';
        if ($this->is_plugin_active('fooevents-calendar/fooevents-calendar.php') || is_plugin_active_for_network('fooevents-calendar/fooevents-calendar.php')) {
            
            $FooEvents_Calendar = new FooEvents_Calendar();
            $eventBackgroundColour = $FooEvents_Calendar->generate_event_background_color_option($post);
            $eventTextColour = $FooEvents_Calendar->generate_event_background_text_option($post);
            
            $globalFooEventsEventbriteToken = get_option('globalFooEventsEventbriteToken');
            
            if(!empty($globalFooEventsEventbriteToken)) {
                
                $eventbrite_option = $FooEvents_Calendar->generate_eventbrite_option($post);
            
            }
            
        }
        
        $eventsIncludeCustomAttendeeFields = '';
        
        if ($this->is_plugin_active('fooevents_custom_attendee_fields/fooevents-custom-attendee-fields.php') || is_plugin_active_for_network('fooevents_custom_attendee_fields/fooevents-custom-attendee-fields.php')) {
            
            $Fooevents_Custom_Attendee_Fields = new Fooevents_Custom_Attendee_Fields($post);
            $eventsIncludeCustomAttendeeFields = $Fooevents_Custom_Attendee_Fields->generate_include_custom_attendee_options($post);
            
            $fooevents_custom_attendee_fields_options = $Fooevents_Custom_Attendee_Fields->display_tickets_meta_custom_options_array($post->ID); 
            if (!empty($fooevents_custom_attendee_fields_options["fooevents_custom_attendee_fields_options_serialized"])) {
                $custom_fields = json_decode($fooevents_custom_attendee_fields_options["fooevents_custom_attendee_fields_options_serialized"], true);
                $cf_array = [];
               
                foreach( $custom_fields as $key => $value) {
                    foreach( $value as $key_cf => $value_cf) {
                        if (strpos($key_cf, '_label') !== false)
                        {
                            
                            $cf_array["fooevents_custom_" . str_replace(" ", "_", strtolower($value_cf))] = $value_cf;
                        }   
                    }
                }
            }
        }

        $themes = $this->get_ticket_themes();
        
        require($this->Config->templatePath.'eventmetaoptions.php');

    }
    
    /**
     * Gets a list of FooEvents Ticket themes
     * 
     */
    public function get_ticket_themes()  {
        
        $valid_themes = array();
        
        foreach (new DirectoryIterator($this->Config->themePacksPath) as $file) {
            
            if ($file->isDir() && !$file->isDot()) {
                
                $theme_name = $file->getFilename();
                
                $theme_path = $file->getPath();
                $theme_path = $theme_path.'/'.$theme_name;
                
                $theme_name = str_replace('_', " ", $theme_name);
                $theme_name = ucwords($theme_name);

                if(file_exists($theme_path.'/header.php') && file_exists($theme_path.'/footer.php') && file_exists($theme_path.'/ticket.php')) {
                    
                    $valid_themes[$theme_name] = $theme_path;
                    
                }

            }
            
        }

        return $valid_themes;
        
    }
    
    /**
     * Processes the meta box form once the plubish / update button is clicked.
     * 
     * @global object $woocommerce_errors
     * @param int $post_id
     * @param object $post
     */
    public function process_meta_box($post_id) {

        global $woocommerce_errors;
        global $wp_locale;
        
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        if(isset($_POST['WooCommerceEventsEvent'])) {
            
            $WooCommerceEventsEvent = sanitize_text_field($_POST['WooCommerceEventsEvent']);
            update_post_meta($post_id, 'WooCommerceEventsEvent', $WooCommerceEventsEvent);

        }

        $format = get_option('date_format');
        
        $min = 60 * get_option('gmt_offset');
        $sign = $min < 0 ? "-" : "+";
        $absmin = abs($min);

        try {
            
            $tz = new DateTimeZone(sprintf("%s%02d%02d", $sign, $absmin/60, $absmin%60));

        } catch(Exception $e) {
            
            $serverTimezone = date_default_timezone_get();
            $tz = new DateTimeZone($serverTimezone);

        }
        
        $event_date = sanitize_text_field($_POST['WooCommerceEventsDate']);
        
        if(isset($event_date)) {
            
            if(isset($_POST['WooCommerceEventsSelectDate'][0]) && isset($_POST['WooCommerceEventsMultiDayType']) && $_POST['WooCommerceEventsMultiDayType'] == 'select') {
                
                $event_date = sanitize_text_field($_POST['WooCommerceEventsSelectDate'][0]);
                
            }

            $event_date = str_replace('/', '-', $event_date);
            $event_date = str_replace(',', '', $event_date);
            
            $WooCommerceEventsDate = sanitize_text_field($_POST['WooCommerceEventsDate']);
            
            update_post_meta($post_id, 'WooCommerceEventsDate', $WooCommerceEventsDate);
            
            $dtime = DateTime::createFromFormat($format, $event_date, $tz);

            $timestamp = strtotime($event_date);
            
            if(empty($timestamp)) {
                
                $timestamp = 0;
                
            }
            
            update_post_meta($post_id, 'WooCommerceEventsDate', $_POST['WooCommerceEventsDate']);

            update_post_meta($post_id, 'WooCommerceEventsDateTimestamp', $timestamp);

        }
        
        if(isset($_POST['WooCommerceEventsEndDate'])) {
            
            $WooCommerceEventsEndDate = sanitize_text_field($_POST['WooCommerceEventsEndDate']);
            update_post_meta($post_id, 'WooCommerceEventsEndDate', $WooCommerceEventsEndDate);
            
            $dtime = DateTime::createFromFormat($format, $_POST['WooCommerceEventsEndDate'], $tz);

            $timestamp = '';
            if ($dtime instanceof DateTime) {
                
                if(isset($_POST['WooCommerceEventsHourEnd']) && isset($_POST['WooCommerceEventsMinutesEnd'])) {
                
                    $dtime->setTime((int)$_POST['WooCommerceEventsHourEnd'], (int)$_POST['WooCommerceEventsMinutesEnd']);

                }

                $timestamp = $dtime->getTimestamp();

            } else {

                $timestamp = 0;

            }

            update_post_meta($post_id, 'WooCommerceEventsEndDateTimestamp', $timestamp);

        }
        
        if(isset($_POST['WooCommerceEventsMultiDayType'])) {
            
            $WooCommerceEventsMultiDayType = sanitize_text_field($_POST['WooCommerceEventsMultiDayType']);
            update_post_meta($post_id, 'WooCommerceEventsMultiDayType', $WooCommerceEventsMultiDayType);
            
        }
        
        if(isset($_POST['WooCommerceEventsSelectDate'])) {
            
            $WooCommerceEventsSelectDate = $_POST['WooCommerceEventsSelectDate'];
            update_post_meta($post_id, 'WooCommerceEventsSelectDate', $WooCommerceEventsSelectDate);
            
        }
        
        if(isset($_POST['WooCommerceEventsNumDays'])) {
            
            $WooCommerceEventsNumDays = sanitize_text_field($_POST['WooCommerceEventsNumDays']);
            update_post_meta($post_id, 'WooCommerceEventsNumDays', $WooCommerceEventsNumDays);
            
        }
        
        if(isset($_POST['WooCommerceEventsHour'])) {
            
            $WooCommerceEventsHour = sanitize_text_field($_POST['WooCommerceEventsHour']);
            update_post_meta($post_id, 'WooCommerceEventsHour', $WooCommerceEventsHour);

        }

        if(isset($_POST['WooCommerceEventsMinutes'])) {
            
            $WooCommerceEventsMinutes = sanitize_text_field($_POST['WooCommerceEventsMinutes']);
            update_post_meta($post_id, 'WooCommerceEventsMinutes', $WooCommerceEventsMinutes);

        }

        if(isset($_POST['WooCommerceEventsPeriod'])) {
            
            $WooCommerceEventsPeriod = sanitize_text_field($_POST['WooCommerceEventsPeriod']);
            update_post_meta($post_id, 'WooCommerceEventsPeriod', $WooCommerceEventsPeriod);

        }

        if(isset($_POST['WooCommerceEventsLocation'])) {

            $WooCommerceEventsLocation = htmlentities(stripslashes($_POST['WooCommerceEventsLocation']));
            update_post_meta($post_id, 'WooCommerceEventsLocation', $WooCommerceEventsLocation);

        }

        if(isset($_POST['WooCommerceEventsTicketLogo'])) {
            
            $WooCommerceEventsTicketLogo = sanitize_text_field($_POST['WooCommerceEventsTicketLogo']);
            update_post_meta($post_id, 'WooCommerceEventsTicketLogo', $WooCommerceEventsTicketLogo);

        }

        if(isset($_POST['WooCommerceEventsPrintTicketLogo'])) {
            
            $WooCommerceEventsPrintTicketLogo = sanitize_text_field($_POST['WooCommerceEventsPrintTicketLogo']);
            update_post_meta($post_id, 'WooCommerceEventsPrintTicketLogo', $WooCommerceEventsPrintTicketLogo);
 
        }
	
	if(isset($_POST['WooCommerceEventsTicketHeaderImage'])) {
            
            $WooCommerceEventsTicketHeaderImage = sanitize_text_field($_POST['WooCommerceEventsTicketHeaderImage']);
            update_post_meta($post_id, 'WooCommerceEventsTicketHeaderImage', $WooCommerceEventsTicketHeaderImage);

        }

        if(isset($_POST['WooCommerceEventsTicketText'])) {
            
            $WooCommerceEventsTicketText = wp_kses_post($_POST['WooCommerceEventsTicketText']);
            update_post_meta($post_id, 'WooCommerceEventsTicketText', $WooCommerceEventsTicketText);

        }

        if(isset($_POST['WooCommerceEventsThankYouText'])) {
            
            $WooCommerceEventsThankYouText = wp_kses_post($_POST['WooCommerceEventsThankYouText']);
            update_post_meta($post_id, 'WooCommerceEventsThankYouText', $WooCommerceEventsThankYouText);

        }
        
        if(isset($_POST['WooCommerceEventsEventDetailsText'])) {
            
            $WooCommerceEventsEventDetailsText = wp_kses_post($_POST['WooCommerceEventsEventDetailsText']);
            update_post_meta($post_id, 'WooCommerceEventsEventDetailsText', $WooCommerceEventsEventDetailsText);

        }
        
        if(isset($_POST['WooCommerceEventsSupportContact'])) {

            $WooCommerceEventsSupportContact = htmlentities(stripslashes($_POST['WooCommerceEventsSupportContact']));
            update_post_meta($post_id, 'WooCommerceEventsSupportContact', $WooCommerceEventsSupportContact);

        }

        if(isset($_POST['WooCommerceEventsHourEnd'])) {
            
            $WooCommerceEventsHourEnd = sanitize_text_field($_POST['WooCommerceEventsHourEnd']);
            update_post_meta($post_id, 'WooCommerceEventsHourEnd', $WooCommerceEventsHourEnd);

        }

        if(isset($_POST['WooCommerceEventsMinutesEnd'])) {
            
            $WooCommerceEventsMinutesEnd = sanitize_text_field($_POST['WooCommerceEventsMinutesEnd']);
            update_post_meta($post_id, 'WooCommerceEventsMinutesEnd', $WooCommerceEventsMinutesEnd);

        }

        if(isset($_POST['WooCommerceEventsEndPeriod'])) {
            
            $WooCommerceEventsEndPeriod = sanitize_text_field($_POST['WooCommerceEventsEndPeriod']);
            update_post_meta($post_id, 'WooCommerceEventsEndPeriod', $WooCommerceEventsEndPeriod);

        }

        if(isset($_POST['WooCommerceEventsAddEventbrite'])) {

            if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
                require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
            }

            if ($this->is_plugin_active('fooevents-calendar/fooevents-calendar.php') || is_plugin_active_for_network('fooevents-calendar/fooevents-calendar.php')) {
                
                $FooEvents_Calendar = new FooEvents_Calendar();
                $FooEvents_Calendar->process_eventbrite($post_id);

            }

            $WooCommerceEventsAddEventbrite= sanitize_text_field($_POST['WooCommerceEventsAddEventbrite']);
            update_post_meta($post_id, 'WooCommerceEventsAddEventbrite', $WooCommerceEventsAddEventbrite);
  
        } else {

            update_post_meta($post_id, 'WooCommerceEventsAddEventbrite', '');

        }
        
        if(isset($_POST['WooCommerceEventsGPS'])) {

            $WooCommerceEventsGPS = htmlentities(stripslashes($_POST['WooCommerceEventsGPS']));
            update_post_meta($post_id, 'WooCommerceEventsGPS', $WooCommerceEventsGPS);

        }

        if(isset($_POST['WooCommerceEventsDirections'])) {

            $WooCommerceEventsDirections = htmlentities(stripslashes($_POST['WooCommerceEventsDirections']));
            update_post_meta($post_id, 'WooCommerceEventsDirections', $WooCommerceEventsDirections);

        }

        if(isset($_POST['WooCommerceEventsEmail'])) {

            $WooCommerceEventsEmail = esc_textarea($_POST['WooCommerceEventsEmail']);
            update_post_meta($post_id, 'WooCommerceEventsEmail', $WooCommerceEventsEmail);

        }

        if(isset($_POST['WooCommerceEventsTicketBackgroundColor'])) {
            
            $WooCommerceEventsTicketBackgroundColor = sanitize_text_field($_POST['WooCommerceEventsTicketBackgroundColor']);
            update_post_meta($post_id, 'WooCommerceEventsTicketBackgroundColor', $WooCommerceEventsTicketBackgroundColor);

        }

        if(isset($_POST['WooCommerceEventsTicketButtonColor'])) {
            
            $WooCommerceEventsTicketButtonColor = sanitize_text_field($_POST['WooCommerceEventsTicketButtonColor']);
            update_post_meta($post_id, 'WooCommerceEventsTicketButtonColor', $WooCommerceEventsTicketButtonColor);

        }

        if(isset($_POST['WooCommerceEventsTicketTextColor'])) {
            
            $WooCommerceEventsTicketTextColor = sanitize_text_field($_POST['WooCommerceEventsTicketTextColor']);
            update_post_meta($post_id, 'WooCommerceEventsTicketTextColor', $WooCommerceEventsTicketTextColor);

        }
        
        if(isset($_POST['WooCommerceEventsBackgroundColor'])) {
            
            $WooCommerceEventsBackgroundColor = sanitize_text_field($_POST['WooCommerceEventsBackgroundColor']);
            update_post_meta($post_id, 'WooCommerceEventsBackgroundColor', $WooCommerceEventsBackgroundColor);

        }
        
        if(isset($_POST['WooCommerceEventsTextColor'])) {
            
            $WooCommerceEventsTextColor = sanitize_text_field($_POST['WooCommerceEventsTextColor']);
            update_post_meta($post_id, 'WooCommerceEventsTextColor', $WooCommerceEventsTextColor);

        }

        if(isset($_POST['WooCommerceEventsGoogleMaps'])) {
            
            $WooCommerceEventsGoogleMaps = sanitize_text_field($_POST['WooCommerceEventsGoogleMaps']);
            update_post_meta($post_id, 'WooCommerceEventsGoogleMaps', $WooCommerceEventsGoogleMaps);

        }

        if(isset($_POST['WooCommerceEventsTicketPurchaserDetails'])) {
            
            $WooCommerceEventsTicketPurchaserDetails = sanitize_text_field($_POST['WooCommerceEventsTicketPurchaserDetails']);
            update_post_meta($post_id, 'WooCommerceEventsTicketPurchaserDetails', $WooCommerceEventsTicketPurchaserDetails);

        } else {

            update_post_meta($post_id, 'WooCommerceEventsTicketPurchaserDetails', 'off');

        }

        if(isset($_POST['WooCommerceEventsTicketAddCalendar'])) {
            
            $WooCommerceEventsTicketAddCalendar = sanitize_text_field($_POST['WooCommerceEventsTicketAddCalendar']);
            update_post_meta($post_id, 'WooCommerceEventsTicketAddCalendar', $WooCommerceEventsTicketAddCalendar);

        } else {

            update_post_meta($post_id, 'WooCommerceEventsTicketAddCalendar', 'off');

        }

        if(isset($_POST['WooCommerceEventsTicketDisplayDateTime'])) {
            
            $WooCommerceEventsTicketDisplayDateTime = sanitize_text_field($_POST['WooCommerceEventsTicketDisplayDateTime']);
            update_post_meta($post_id, 'WooCommerceEventsTicketDisplayDateTime', $WooCommerceEventsTicketDisplayDateTime);

        } else {

            update_post_meta($post_id, 'WooCommerceEventsTicketDisplayDateTime', 'off');

        }

        if(isset($_POST['WooCommerceEventsTicketDisplayBarcode'])) {
            
            $WooCommerceEventsTicketDisplayBarcode = sanitize_text_field($_POST['WooCommerceEventsTicketDisplayBarcode']);
            update_post_meta($post_id, 'WooCommerceEventsTicketDisplayBarcode', $WooCommerceEventsTicketDisplayBarcode);

        } else {

            update_post_meta($post_id, 'WooCommerceEventsTicketDisplayBarcode', 'off');

        }

        if(isset($_POST['WooCommerceEventsTicketDisplayPrice'])) {
            
            $WooCommerceEventsTicketDisplayPrice = sanitize_text_field($_POST['WooCommerceEventsTicketDisplayPrice']);
            update_post_meta($post_id, 'WooCommerceEventsTicketDisplayPrice', $WooCommerceEventsTicketDisplayPrice);

        } else {

            update_post_meta($post_id, 'WooCommerceEventsTicketDisplayPrice', 'off');

        }
        
        if(isset($_POST['WooCommerceEventsIncludeCustomAttendeeDetails'])) {
            
            $WooCommerceEventsIncludeCustomAttendeeDetails = sanitize_text_field($_POST['WooCommerceEventsIncludeCustomAttendeeDetails']);
            update_post_meta($post_id, 'WooCommerceEventsIncludeCustomAttendeeDetails', $WooCommerceEventsIncludeCustomAttendeeDetails);

        } else {

            update_post_meta($post_id, 'WooCommerceEventsIncludeCustomAttendeeDetails', 'off');

        }

        if(isset($_POST['WooCommerceEventsCaptureAttendeeDetails'])) {
            
            $WooCommerceEventsCaptureAttendeeDetails = sanitize_text_field($_POST['WooCommerceEventsCaptureAttendeeDetails']);
            update_post_meta($post_id, 'WooCommerceEventsCaptureAttendeeDetails', $WooCommerceEventsCaptureAttendeeDetails);

        } else {

            update_post_meta($post_id, 'WooCommerceEventsCaptureAttendeeDetails', 'off');

        }
        
        if(isset($_POST['WooCommerceEventsEmailAttendee'])) {
            
            $WooCommerceEventsEmailAttendee = sanitize_text_field($_POST['WooCommerceEventsEmailAttendee']);
            update_post_meta($post_id, 'WooCommerceEventsEmailAttendee', $WooCommerceEventsEmailAttendee);

        } else {

            update_post_meta($post_id, 'WooCommerceEventsEmailAttendee', 'off');

        }

        if(isset($_POST['WooCommerceEventsCaptureAttendeeTelephone'])) {
            
            $WooCommerceEventsCaptureAttendeeTelephone = sanitize_text_field($_POST['WooCommerceEventsCaptureAttendeeTelephone']);
            update_post_meta($post_id, 'WooCommerceEventsCaptureAttendeeTelephone', $WooCommerceEventsCaptureAttendeeTelephone);

        } else {

            update_post_meta($post_id, 'WooCommerceEventsCaptureAttendeeTelephone', 'off');

        }

        if(isset($_POST['WooCommerceEventsCaptureAttendeeCompany'])) {
            
            $WooCommerceEventsCaptureAttendeeCompany = sanitize_text_field($_POST['WooCommerceEventsCaptureAttendeeCompany']);
            update_post_meta($post_id, 'WooCommerceEventsCaptureAttendeeCompany', $WooCommerceEventsCaptureAttendeeCompany);

        } else {

            update_post_meta($post_id, 'WooCommerceEventsCaptureAttendeeCompany', 'off');

        }

        if(isset($_POST['WooCommerceEventsCaptureAttendeeDesignation'])) {
            
            $WooCommerceEventsCaptureAttendeeDesignation = sanitize_text_field($_POST['WooCommerceEventsCaptureAttendeeDesignation']);
            update_post_meta($post_id, 'WooCommerceEventsCaptureAttendeeDesignation', $WooCommerceEventsCaptureAttendeeDesignation);

        } else {

            update_post_meta($post_id, 'WooCommerceEventsCaptureAttendeeDesignation', 'off');

        }

        if(isset($_POST['WooCommerceEventsSendEmailTickets'])) {
            
            $WooCommerceEventsSendEmailTickets = sanitize_text_field($_POST['WooCommerceEventsSendEmailTickets']);
            update_post_meta($post_id, 'WooCommerceEventsSendEmailTickets', $WooCommerceEventsSendEmailTickets);

        } else {

            update_post_meta($post_id, 'WooCommerceEventsSendEmailTickets', 'off');

        }

        if(isset($_POST['WooCommerceEventsEmailSubjectSingle'])) {
            
            $WooCommerceEventsEmailSubjectSingle = htmlentities($_POST['WooCommerceEventsEmailSubjectSingle']);
            update_post_meta($post_id, 'WooCommerceEventsEmailSubjectSingle', $WooCommerceEventsEmailSubjectSingle);

        } else {

            update_post_meta($post_id, 'WooCommerceEventsEmailSubjectSingle', '{OrderNumber} Ticket');

        }

        if(isset($_POST['WooCommerceEventsExportUnpaidTickets'])) {
            
            $WooCommerceEventsExportUnpaidTickets = sanitize_text_field($_POST['WooCommerceEventsExportUnpaidTickets']);
            update_post_meta($post_id, 'WooCommerceEventsExportUnpaidTickets', $WooCommerceEventsExportUnpaidTickets);

        } else {

            update_post_meta($post_id, 'WooCommerceEventsExportUnpaidTickets', 'off');

        }

        if(isset($_POST['WooCommerceEventsExportBillingDetails'])) {
            
            $WooCommerceEventsExportBillingDetails = sanitize_text_field($_POST['WooCommerceEventsExportBillingDetails']);
            update_post_meta($post_id, 'WooCommerceEventsExportBillingDetails', $WooCommerceEventsExportBillingDetails);

        } else {

            update_post_meta($post_id, 'WooCommerceEventsExportBillingDetails', 'off');

        }

        if(isset($_POST['WooCommerceBadgeSize'])) {
            
            $WooCommerceBadgeSize = sanitize_text_field($_POST['WooCommerceBadgeSize']);
            update_post_meta($post_id, 'WooCommerceBadgeSize', $WooCommerceBadgeSize);

        } 

        if(isset($_POST['WooCommerceBadgeField1'])) {
            
            $WooCommerceBadgeField1 = sanitize_text_field($_POST['WooCommerceBadgeField1']);
            update_post_meta($post_id, 'WooCommerceBadgeField1', $WooCommerceBadgeField1);

        } 

        if(isset($_POST['WooCommerceBadgeField2'])) {
            
            $WooCommerceBadgeField2 = sanitize_text_field($_POST['WooCommerceBadgeField2']);
            update_post_meta($post_id, 'WooCommerceBadgeField2', $WooCommerceBadgeField2);

        } 

        if(isset($_POST['WooCommerceBadgeField3'])) {
            
            $WooCommerceBadgeField3 = sanitize_text_field($_POST['WooCommerceBadgeField3']);
            update_post_meta($post_id, 'WooCommerceBadgeField3', $WooCommerceBadgeField3);

        } 

        if(isset($_POST['WooCommerceEventsCutLines'])) {
            
            $WooCommerceEventsCutLines = sanitize_text_field($_POST['WooCommerceEventsCutLines']);
            update_post_meta($post_id, 'WooCommerceEventsCutLines', $WooCommerceEventsCutLines);

        } else {

            update_post_meta($post_id, 'WooCommerceEventsCutLines', 'off');

        }

        if(isset($_POST['WooCommercePrintTicketSize'])) {
            
            $WooCommercePrintTicketSize = sanitize_text_field($_POST['WooCommercePrintTicketSize']);
            update_post_meta($post_id, 'WooCommercePrintTicketSize', $WooCommercePrintTicketSize);
 
        } 
 
        if(isset($_POST['WooCommercePrintTicketField1'])) {
            
            $WooCommercePrintTicketField1 = sanitize_text_field($_POST['WooCommercePrintTicketField1']);
            update_post_meta($post_id, 'WooCommercePrintTicketField1', $WooCommercePrintTicketField1);
 
        }
        
        if(isset($_POST['WooCommercePrintTicketField1_font'])) {
            
            $WooCommercePrintTicketField1_font = sanitize_text_field($_POST['WooCommercePrintTicketField1_font']);
            update_post_meta($post_id, 'WooCommercePrintTicketField1_font', $WooCommercePrintTicketField1_font);
 
        }
 
        if(isset($_POST['WooCommercePrintTicketField2'])) {
            
            $WooCommercePrintTicketField2 = sanitize_text_field($_POST['WooCommercePrintTicketField2']);
            update_post_meta($post_id, 'WooCommercePrintTicketField2', $WooCommercePrintTicketField2);
 
        }
        
        if(isset($_POST['WooCommercePrintTicketField2_font'])) {
            
            $WooCommercePrintTicketField2_font = sanitize_text_field($_POST['WooCommercePrintTicketField2_font']);
            update_post_meta($post_id, 'WooCommercePrintTicketField2_font', $WooCommercePrintTicketField2_font);
 
        }
 
        if(isset($_POST['WooCommercePrintTicketField3'])) {
            
            $WooCommercePrintTicketField3 = sanitize_text_field($_POST['WooCommercePrintTicketField3']);
            update_post_meta($post_id, 'WooCommercePrintTicketField3', $WooCommercePrintTicketField3);
 
        }
 
        if(isset($_POST['WooCommercePrintTicketField3_font'])) {
            
            $WooCommercePrintTicketField3_font = sanitize_text_field($_POST['WooCommercePrintTicketField3_font']);
            update_post_meta($post_id, 'WooCommercePrintTicketField3_font', $WooCommercePrintTicketField3_font);
 
        }
 
        if(isset($_POST['WooCommercePrintTicketField4'])) {
            
            $WooCommercePrintTicketField4 = sanitize_text_field($_POST['WooCommercePrintTicketField4']);
            update_post_meta($post_id, 'WooCommercePrintTicketField4', $WooCommercePrintTicketField4);
 
        }
 
        if(isset($_POST['WooCommercePrintTicketField4_font'])) {
            
            $WooCommercePrintTicketField4_font = sanitize_text_field($_POST['WooCommercePrintTicketField4_font']);
            update_post_meta($post_id, 'WooCommercePrintTicketField4_font', $WooCommercePrintTicketField4_font);
 
        }
 
        if(isset($_POST['WooCommercePrintTicketField5'])) {
            
            $WooCommercePrintTicketField5 = sanitize_text_field($_POST['WooCommercePrintTicketField5']);
            update_post_meta($post_id, 'WooCommercePrintTicketField5', $WooCommercePrintTicketField5);
 
        }
 
        if(isset($_POST['WooCommercePrintTicketField5_font'])) {
            
            $WooCommercePrintTicketField5_font = sanitize_text_field($_POST['WooCommercePrintTicketField5_font']);
            update_post_meta($post_id, 'WooCommercePrintTicketField5_font', $WooCommercePrintTicketField5_font);
 
        }
 
        if(isset($_POST['WooCommercePrintTicketField6'])) {
            
            $WooCommercePrintTicketField6 = sanitize_text_field($_POST['WooCommercePrintTicketField6']);
            update_post_meta($post_id, 'WooCommercePrintTicketField6', $WooCommercePrintTicketField6);
 
        }
 
        if(isset($_POST['WooCommercePrintTicketField6_font'])) {
            
            $WooCommercePrintTicketField6_font = sanitize_text_field($_POST['WooCommercePrintTicketField6_font']);
            update_post_meta($post_id, 'WooCommercePrintTicketField6_font', $WooCommercePrintTicketField6_font);
 
        }
 
        if(isset($_POST['WooCommerceEventsPrintTicketLogoOption'])) {
            
            $WooCommerceEventsPrintTicketLogoOption = sanitize_text_field($_POST['WooCommerceEventsPrintTicketLogoOption']);
            update_post_meta($post_id, 'WooCommerceEventsPrintTicketLogoOption', $WooCommerceEventsPrintTicketLogoOption);
 
        }
 
        if(isset($_POST['WooCommerceEventsCutLinesPrintTicket'])) {
            
            $WooCommerceEventsCutLinesPrintTicket = sanitize_text_field($_POST['WooCommerceEventsCutLinesPrintTicket']);
            update_post_meta($post_id, 'WooCommerceEventsCutLinesPrintTicket', $WooCommerceEventsCutLinesPrintTicket);
 
        } else {
 
            update_post_meta($post_id, 'WooCommerceEventsCutLinesPrintTicket', 'off');
 
        }
        
        if(isset($_POST['WooCommerceEventsTicketTheme'])) {
            
            $WooCommerceEventsTicketTheme = sanitize_text_field($_POST['WooCommerceEventsTicketTheme']);
            update_post_meta($post_id, 'WooCommerceEventsTicketTheme', $WooCommerceEventsTicketTheme);

        } 
        
        if(isset($_POST['WooCommerceEventsAttendeeOverride'])) {
            
            $WooCommerceEventsAttendeeOverride = sanitize_text_field($_POST['WooCommerceEventsAttendeeOverride']);
            update_post_meta($post_id, 'WooCommerceEventsAttendeeOverride', $WooCommerceEventsAttendeeOverride);

        }
        
        if(isset($_POST['WooCommerceEventsTicketOverride'])) {
            
            $WooCommerceEventsTicketOverride = sanitize_text_field($_POST['WooCommerceEventsTicketOverride']);
            update_post_meta($post_id, 'WooCommerceEventsTicketOverride', $WooCommerceEventsTicketOverride);

        }
        
        if(isset($_POST['WooCommerceEventsDayOverride'])) {
            
            $WooCommerceEventsDayOverride = sanitize_text_field($_POST['WooCommerceEventsDayOverride']);
            update_post_meta($post_id, 'WooCommerceEventsDayOverride', $WooCommerceEventsDayOverride);

        }

        if(isset($_POST['WooCommerceEventsViewSeatingChart'])) {
            
            $WooCommerceEventsViewSeatingChart = sanitize_text_field($_POST['WooCommerceEventsViewSeatingChart']);
            update_post_meta($post_id, 'WooCommerceEventsViewSeatingChart', $WooCommerceEventsViewSeatingChart);

        } else {

            update_post_meta($post_id, 'WooCommerceEventsViewSeatingChart', 'off');

        }

    }

    /**
     * Displays the event details on the front end template. Before WooCommerce Displays content.
     * 
     * @param array $tabs
     * @global object $post
     * @return array $tabs
     */
    public function add_front_end_tab($tabs) {

        global $post;

        $WooCommerceEventsEvent = get_post_meta($post->ID, 'WooCommerceEventsEvent', true);

        $WooCommerceEventsGoogleMaps = get_post_meta($post->ID, 'WooCommerceEventsGoogleMaps', true);

        $globalWooCommerceHideEventDetailsTab = get_option('globalWooCommerceHideEventDetailsTab', true);

        if($WooCommerceEventsEvent == 'Event') {

            if($globalWooCommerceHideEventDetailsTab !== 'yes') {

                $tabs['woocommerce_events'] = array(
                    'title'     => __('Event Details', 'woocommerce-events'),
                    'priority'  => 30,
                    'callback'  => 'fooevents_displayEventTab'
                );

            }

            if(!empty($WooCommerceEventsGoogleMaps)) {

                $tabs['description'] = array(
                    'title'     => __('Description', 'woocommerce-events'),
                    'priority' => 1,
                    'callback'  => 'fooevents_displayEventTabMap'
                );

            }

        }
        return $tabs;

    }

    /**
     * Creates an orders tickets
     * 
     * @param int $order_id
     */
    public function create_tickets($order_id) {

        $WooCommerceEventsOrderTickets = get_post_meta($order_id, 'WooCommerceEventsOrderTickets', true);
        $WooCommerceEventsSentTicket =  get_post_meta($order_id, 'WooCommerceEventsTicketsGenerated', true);

        if($WooCommerceEventsSentTicket != 'yes' && !empty($WooCommerceEventsOrderTickets)) {

            $x = 1;
            foreach($WooCommerceEventsOrderTickets as $event => $tickets) {

                $y = 1;
                foreach($tickets as $ticket) {
                    
                    if(!empty($ticket['WooCommerceEventsOrderID'])) {

                        $rand = rand(111111,999999);

                        $post = array(

                                'post_author' => $ticket['WooCommerceEventsCustomerID'],
                                'post_content' => "Ticket",
                                'post_status' => "publish",
                                'post_title' => 'Assigned Ticket',
                                'post_type' => "event_magic_tickets"

                        );

                        $post['ID'] = wp_insert_post( $post );
                        $ticketID = $post['ID'].$rand;
                        $post['post_title'] = '#'.$ticketID;
                        $postID = wp_update_post( $post );

                        $ticketHash = $this->generate_random_string(8);

                        update_post_meta($postID, 'WooCommerceEventsTicketID', $ticketID);
                        update_post_meta($postID, 'WooCommerceEventsTicketHash', $ticketHash);
                        update_post_meta($postID, 'WooCommerceEventsProductID', $ticket['WooCommerceEventsProductID']);
                        update_post_meta($postID, 'WooCommerceEventsOrderID', $ticket['WooCommerceEventsOrderID']);
                        update_post_meta($postID, 'WooCommerceEventsTicketType', $ticket['WooCommerceEventsTicketType']);
                        update_post_meta($postID, 'WooCommerceEventsStatus', 'Unpaid');
                        update_post_meta($postID, 'WooCommerceEventsCustomerID', $ticket['WooCommerceEventsCustomerID']);
                        update_post_meta($postID, 'WooCommerceEventsAttendeeName', $ticket['WooCommerceEventsAttendeeName']);
                        update_post_meta($postID, 'WooCommerceEventsAttendeeLastName', $ticket['WooCommerceEventsAttendeeLastName']);
                        update_post_meta($postID, 'WooCommerceEventsAttendeeEmail', $ticket['WooCommerceEventsAttendeeEmail']);
                        update_post_meta($postID, 'WooCommerceEventsAttendeeTelephone', $ticket['WooCommerceEventsAttendeeTelephone']);
                        update_post_meta($postID, 'WooCommerceEventsAttendeeCompany', $ticket['WooCommerceEventsAttendeeCompany']);
                        update_post_meta($postID, 'WooCommerceEventsAttendeeDesignation', $ticket['WooCommerceEventsAttendeeDesignation']);
                        update_post_meta($postID, 'WooCommerceEventsVariations', $ticket['WooCommerceEventsVariations']);
                        update_post_meta($postID, 'WooCommerceEventsVariationID', $ticket['WooCommerceEventsVariationID']);

                        update_post_meta($postID, 'WooCommerceEventsPurchaserFirstName', $ticket['WooCommerceEventsPurchaserFirstName']);
                        update_post_meta($postID, 'WooCommerceEventsPurchaserLastName', $ticket['WooCommerceEventsPurchaserLastName']);
                        update_post_meta($postID, 'WooCommerceEventsPurchaserEmail', $ticket['WooCommerceEventsPurchaserEmail']);

                        update_post_meta($postID, 'WooCommerceEventsPrice', $ticket['WooCommerceEventsPrice']);
                        //update_post_meta($postID, 'WooCommerceEventsPriceSymbol', $ticket['WooCommerceEventsPriceSymbol']);

                        if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
                                require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
                        }

                        if ( $this->is_plugin_active( 'fooevents_custom_attendee_fields/fooevents-custom-attendee-fields.php' ) || is_plugin_active_for_network('fooevents_custom_attendee_fields/fooevents-custom-attendee-fields.php') ) {

                            $Fooevents_Custom_Attendee_Fields = new Fooevents_Custom_Attendee_Fields();
                            $WooCommerceEventsCustomAttendeeFields = $Fooevents_Custom_Attendee_Fields->process_capture_custom_attendee_options($postID, $ticket['WooCommerceEventsCustomAttendeeFields']);

                        }

                        if ( $this->is_plugin_active( 'fooevents_seating/fooevents-seating.php' ) || is_plugin_active_for_network('fooevents_seating/fooevents-seating.php') ) {

                            $Fooevents_Seating = new Fooevents_Seating();
                            $WooCommerceEventsSeatingFields = $Fooevents_Seating->process_capture_seating_options($postID, $ticket['WooCommerceEventsSeatingFields']);

                        }

                        $product = get_post($ticket['WooCommerceEventsProductID']);

                        update_post_meta($postID, 'WooCommerceEventsProductName', $product->post_title);

                        $y++;
                    
                    }
                        
                }

                $x++;

            }
            
            update_post_meta($order_id, 'WooCommerceEventsTicketsGenerated', 'yes');
            
        }

    }    
    
    /**
     * Sends a ticket email once an order is completed.
     * 
     * @param int $order_id
     * @global $woocommerce, $evotx;
     */
     public function send_ticket_email($order_id) {

        /*error_reporting(E_ALL);
        ini_set('display_errors', '1');*/
        
        $this->create_tickets($order_id);
     
        set_time_limit(0);

        global $woocommerce;

        $order = new WC_Order( $order_id );
        $tickets = $order->get_items();

        $WooCommerceEventsTicketsPurchased = get_post_meta($order_id, 'WooCommerceEventsTicketsPurchased', true);
        
        $customer = get_post_meta($order_id, '_customer_user', true);
        $usermeta = get_user_meta($customer);

        $WooCommerceEventsSentTicket =  get_post_meta($order_id, 'WooCommerceEventsSentTicket', true);

        if ( $this->is_plugin_active( 'fooevents_custom_attendee_fields/fooevents-custom-attendee-fields.php' ) || is_plugin_active_for_network('fooevents_custom_attendee_fields/fooevents-custom-attendee-fields.php') ) {

            $Fooevents_Custom_Attendee_Fields = new Fooevents_Custom_Attendee_Fields();
            $WooCommerceEventsCustomAttendeeFields = $Fooevents_Custom_Attendee_Fields->process_capture_custom_attendee_options($postID, $ticket['WooCommerceEventsCustomAttendeeFields']);

        }
        
        if ( $this->is_plugin_active( 'fooevents_seating/fooevents-seating.php' ) || is_plugin_active_for_network('fooevents_seating/fooevents-seating.php') ) {

            $Fooevents_Seating = new Fooevents_Seating();
            $WooCommerceEventsSeatingFields = $Fooevents_Seating->process_capture_seating_options($postID, $ticket['WooCommerceEventsSeatingFields']);

        }

        $product = get_post($ticket['WooCommerceEventsProductID']);

        update_post_meta($postID, 'WooCommerceEventsProductName', $product->post_title);

        $x++;

    }    

    /**
    * Sends a ticket email once an order is completed.
    * 
    * @param int $order_id
    * @global $woocommerce, $evotx;
    */
    public function process_order_tickets($order_id) {

        set_time_limit(0);
        
        $this->create_tickets($order_id);
        $this->build_send_tickets($order_id);
        


    }
    
    /**
     * Builds tickets to be emailed
     * 
     * @param int $order_id
     */
    public function build_send_tickets($order_id) {
        
        /*error_reporting(E_ALL);
        ini_set('display_errors', 1);*/
        
        $order = array();
        try {
            $order = new WC_Order($order_id);
        } catch (Exception $e) {
            
        }  
        
        $tickets_query = new WP_Query( array('post_type' => array('event_magic_tickets'), 'posts_per_page' => -1, 'meta_query' => array( array( 'key' => 'WooCommerceEventsOrderID', 'value' => $order_id ) )) );
        $orderTickets = $tickets_query->get_posts();
        
        $emailHTML = '';
        
        $sortedOrderTickets = array();
        
        //Sort tickets into events

        foreach($orderTickets as $orderTicket) {
            
            $ticket = $this->TicketHelper->get_ticket_data($orderTicket->ID);

            $sortedOrderTickets[$ticket['WooCommerceEventsProductID']][] = $ticket;
            
        }
        
        foreach($sortedOrderTickets as $productID => $tickets) {

            $WooCommerceEventsEmailAttendee = get_post_meta($productID, 'WooCommerceEventsEmailAttendee', true);
            
            $WooCommerceEventsEmailSubjectSingle = get_post_meta($productID, 'WooCommerceEventsEmailSubjectSingle', true);
            if(empty($WooCommerceEventsEmailSubjectSingle)) {

                $WooCommerceEventsEmailSubjectSingle  = __('{OrderNumber} Ticket', 'woocommerce-events');

            }
            $subject = str_replace('{OrderNumber}', '[#'.$order_id.']', $WooCommerceEventsEmailSubjectSingle);
            
            $WooCommerceEventsTicketTheme = get_post_meta($productID, 'WooCommerceEventsTicketTheme', true);
            if(empty($WooCommerceEventsTicketTheme)) {
                
                $WooCommerceEventsTicketTheme = $this->Config->emailTemplatePath;
                
            }
            
			$header = $this->MailHelper->parse_email_template($WooCommerceEventsTicketTheme.'/header.php', array(), $tickets[0]); 
			$footer = $this->MailHelper->parse_email_template($WooCommerceEventsTicketTheme.'/footer.php', array(), $tickets[0]);
            
            $ticketBody = '';
            
            $emailAttendee = false;
            
            foreach($tickets as $ticket) {
                
                $body = $this->MailHelper->parse_ticket_template($WooCommerceEventsTicketTheme.'/ticket.php', $ticket);
                $ticketBody .= $body;

                //Send to attendee
                if ($WooCommerceEventsEmailAttendee == 'on' && isset($ticket['WooCommerceEventsAttendeeEmail'])) {
                    
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
                            
                            if($globalFooEventsPDFTicketsAttachHTMLTicket !== 'yes') {
                                
                                $header = $FooEvents_PDF_Tickets->parse_email_template('email-header.php');
                                $footer = $FooEvents_PDF_Tickets->parse_email_template('email-footer.php');

                                $body = $header.$FooEventsPDFTicketsEmailText.$footer;
                            
                            }
                            
                            if(empty($body)) {

                                $body = __('Your tickets are attached. Please print them and bring them to the event.', 'fooevents-pdf-tickets');

                            }
                            
                        }
                        
                    }
                    
                    if($ticket['WooCommerceEventsSendEmailTickets'] === 'on') {
                    
                        $mailStatus = $this->MailHelper->send_ticket($ticket['WooCommerceEventsAttendeeEmail'], $subject, $header.$body.$footer, $attachment);
                    
                    }
                    
                    $emailAttendee = true;

                }
                
            }
            
            //Send to purchaser
            if ($WooCommerceEventsEmailAttendee != 'on' && $emailAttendee === false) {
                
                $attachment = '';
                
                if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
                    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
                }
                if ( $this->is_plugin_active('fooevents_pdf_tickets/fooevents-pdf-tickets.php') || is_plugin_active_for_network('fooevents_pdf_tickets/fooevents-pdf-tickets.php')) {
                    
                    $globalFooEventsPDFTicketsEnable = get_option('globalFooEventsPDFTicketsEnable');
                    $globalFooEventsPDFTicketsLayout = get_option('globalFooEventsPDFTicketsLayout');
                    $globalFooEventsPDFTicketsAttachHTMLTicket = get_option('globalFooEventsPDFTicketsAttachHTMLTicket');
                    
                    if(empty($globalFooEventsPDFTicketsLayout)) {

                        $globalFooEventsPDFTicketsLayout = 'single';

                    }
                    
                    if($globalFooEventsPDFTicketsEnable == 'yes') {

                        $FooEvents_PDF_Tickets = new FooEvents_PDF_Tickets();

                        if($globalFooEventsPDFTicketsLayout == 'single') {

                            $attachment = $FooEvents_PDF_Tickets->generate_ticket($tickets, $this->Config->barcodePath, $this->Config->path);

                        } else {

                            $attachment = $FooEvents_PDF_Tickets->generate_multiple_ticket($tickets, $this->Config->barcodePath, $this->Config->path);

                        }
                        
                        if($globalFooEventsPDFTicketsAttachHTMLTicket === 'yes') {

                                $attachedText = get_post_meta($productID, 'FooEventsPDFTicketsEmailText', true);

                                if (empty($attachedText))
                                        $attachedText = __('Your tickets are attached. Please print them and bring them to the event.', 'fooevents-pdf-tickets');

                                $header = $attachedText.$header;

                        } else {
      
                            $ticketBody = get_post_meta($productID, 'FooEventsPDFTicketsEmailText', true);
							
							if(empty($ticketBody)||$ticketBody == '')
								$ticketBody = __('Your tickets are attached. Please print them and bring them to the event.', 'fooevents-pdf-tickets');

                            $header = $FooEvents_PDF_Tickets->parse_email_template('email-header.php');
                            $footer = $FooEvents_PDF_Tickets->parse_email_template('email-footer.php');
                        } 

                    }
                    
                }
                
                $orderEmailAddress = $order->get_billing_email();
                //$WooCommerceEventsSendEmailTickets = get_post_meta($WooCommerceEventsProductID, 'WooCommerceEventsSendEmailTickets', true);
                
                if($ticket['WooCommerceEventsSendEmailTickets'] === 'on') {

                    $mailStatus = $this->MailHelper->send_ticket($orderEmailAddress, $subject, $header.$ticketBody.$footer, $attachment);
                    
                }
            }
            
        }

    }

    /**
     * Displays thank you text on order completion page.
     * 
     * @param type $thankYouText
     * @return type
     */
    public function display_thank_you_text($thankYouText) {

        /*error_reporting(0);
        ini_set('display_errors', 0);*/

        global $woocommerce;
        global $post;

        //$paged = get_query_var();

        $actualLink = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $segments = array_reverse(explode('/', $actualLink));

        $orderID = $segments[1];
        $order = new WC_Order($orderID);
        $items = $order->get_items();

        $products = array();

        foreach($items as $item) {

            $products[$item['product_id']] = $item['product_id'];

        }

        foreach($products as $key => $productID) {

            $WooCommerceEventsThankYouText = get_post_meta($productID, 'WooCommerceEventsThankYouText', true);

            if(!empty($WooCommerceEventsThankYouText)) {

                echo $WooCommerceEventsThankYouText."<br/><br/>";

            }

        }

        return $thankYouText;

    }

    /**
     * Cancels ticket when order is canceled.
     * 
     * @param int $order_id
     */
    public function order_status_cancelled($order_id) {

        $tickets = new WP_Query( array('post_type' => array('event_magic_tickets'), 'posts_per_page' => -1, 'meta_query' => array( array( 'key' => 'WooCommerceEventsOrderID', 'value' => $order_id ) )) );
        $tickets = $tickets->get_posts();

        foreach ($tickets as $ticket) {

            update_post_meta($ticket->ID, 'WooCommerceEventsStatus', 'Canceled');

        }

    }

    public function order_status_completed_cancelled($order_id) {

        $tickets = new WP_Query( array('post_type' => array('event_magic_tickets'), 'posts_per_page' => -1, 'meta_query' => array( array( 'key' => 'WooCommerceEventsOrderID', 'value' => $order_id ) )) );
        $tickets = $tickets->get_posts();

        foreach ($tickets as $ticket) {

            $ticketStatus = get_post_meta($ticket->ID, 'WooCommerceEventsStatus', true);

            if($ticketStatus == 'Canceled') {

                update_post_meta($ticket->ID, 'WooCommerceEventsStatus', 'Not Checked In');

            }

        }

    }

    /**
     * Generates attendee CSV export.
     * 
     */
    public function woocommerce_events_csv() {
        
        if(!current_user_can('publish_event_magic_tickets'))
        {
            echo "User role does not have permission to export attendee details. Please contact site admin.";
            exit();
        }
        
        error_reporting(1);
        ini_set('display_errors', '1');
        
        global $woocommerce;

        $event = sanitize_text_field($_GET['event']);
        $includeUnpaidTickets = sanitize_text_field($_GET['exportunpaidtickets']);
        $exportbillingdetails = sanitize_text_field($_GET['exportbillingdetails']);
        
        $csv_blueprint = array("TicketID", "OrderID", "Attendee First Name", "Attendee Last Name", "Attendee Email", "Ticket Status", "Ticket Type", "Variation", "Attendee Telephone", "Attendee Company", "Attendee Designation", "Purchaser First Name", "Purchaser Last Name", "Purchaser Email", "Purchaser Phone", "Purchaser Company");
        $sorted_rows = array();
        
        $events_query = new WP_Query( array('post_type' => array('event_magic_tickets'), 'posts_per_page' => -1, 'meta_query' => array( array( 'key' => 'WooCommerceEventsProductID', 'value' => $event ) )) );
        $events = $events_query->get_posts();
        
        $x = 0;
        foreach($events as $eventItem) {
            
            $id = $eventItem->ID;
            $ticket = get_post($id);
            $ticketID = $ticket->post_title;
            
            $order_id = get_post_meta($id, 'WooCommerceEventsOrderID', true);
            $product_id = get_post_meta($id, 'WooCommerceEventsProductID', true);
            $customer_id = get_post_meta($id, 'WooCommerceEventsCustomerID', true);
            $WooCommerceEventsStatus = get_post_meta($id, 'WooCommerceEventsStatus', true);
            $ticketType = get_post_meta($ticket->ID, 'WooCommerceEventsTicketType', true);
            
            if($includeUnpaidTickets != 'true' && $WooCommerceEventsStatus == 'Unpaid') {
                
                continue;
                
            }
            
            $WooCommerceEventsVariations = get_post_meta($id, 'WooCommerceEventsVariations', true);
            if(!empty($WooCommerceEventsVariations) && !is_array($WooCommerceEventsVariations)) {
                
                $WooCommerceEventsVariations = json_decode($WooCommerceEventsVariations);
                
            }
            $variationOutput = '';
            $i = 0;
            if(!empty($WooCommerceEventsVariations)) {
                foreach($WooCommerceEventsVariations as $variationName => $variationValue) {

                    if($i > 0) {

                        $variationOutput .= ' | ';

                    }

                    $variationNameOutput = str_replace('attribute_', '', $variationName);
                    $variationNameOutput = str_replace('pa_', '', $variationNameOutput);
                    $variationNameOutput = str_replace('_', ' ', $variationNameOutput);
                    $variationNameOutput = str_replace('-', ' ', $variationNameOutput);
                    $variationNameOutput = str_replace('Pa_', '', $variationNameOutput);
                    $variationNameOutput = ucwords($variationNameOutput);

                    $variationValueOutput = str_replace('_', ' ', $variationValue);
                    $variationValueOutput = str_replace('-', ' ', $variationValueOutput);
                    $variationValueOutput = ucwords($variationValueOutput);

                    $variationOutput .= $variationNameOutput.': '.$variationValueOutput;

                    $i++;
                }
            }
            
            $order = '';
            
            try {
                $order = new WC_Order( $order_id );
            } catch (Exception $e) {

            } 
            
            $WooCommerceEventsAttendeeName = get_post_meta($ticket->ID, 'WooCommerceEventsAttendeeName', true);
            if(empty($WooCommerceEventsAttendeeName)) {

                if(!empty($order)) {

                    $WooCommerceEventsAttendeeName = $order->get_billing_first_name();

                } else {

                    $WooCommerceEventsAttendeeName = '';

                }

            } 
            
            $WooCommerceEventsAttendeeLastName = get_post_meta($ticket->ID, 'WooCommerceEventsAttendeeLastName', true);
            if(empty($WooCommerceEventsAttendeeLastName)) {

                if(!empty($order)) {

                    $WooCommerceEventsAttendeeLastName = $order->get_billing_last_name();

                } else {

                    $WooCommerceEventsAttendeeLastName = '';

                }

            }
            
            $WooCommerceEventsAttendeeEmail = get_post_meta($ticket->ID, 'WooCommerceEventsAttendeeEmail', true);
            if(empty($WooCommerceEventsAttendeeEmail)) {

                if(!empty($order)) {

                    $WooCommerceEventsAttendeeEmail = $order->get_billing_email();

                } else {

                    $WooCommerceEventsAttendeeEmail = '';

                }

            }
            
            $WooCommerceEventsCaptureAttendeeTelephone = get_post_meta($ticket->ID, 'WooCommerceEventsAttendeeTelephone', true);
            $WooCommerceEventsCaptureAttendeeCompany = get_post_meta($ticket->ID, 'WooCommerceEventsAttendeeCompany', true);
            $WooCommerceEventsCaptureAttendeeDesignation = get_post_meta($ticket->ID, 'WooCommerceEventsAttendeeDesignation', true);
            $WooCommerceEventsPurchaserFirstName = get_post_meta($ticket->ID, 'WooCommerceEventsPurchaserFirstName', true);
            $WooCommerceEventsPurchaserLastName = get_post_meta($ticket->ID, 'WooCommerceEventsPurchaserLastName', true);
            $WooCommerceEventsPurchaserEmail = get_post_meta($ticket->ID, 'WooCommerceEventsPurchaserEmail', true);
            $WooCommerceEventsPurchaserPhone = get_post_meta($ticket->ID, 'WooCommerceEventsPurchaserPhone', true);

            $sorted_rows[$x]["TicketID"] = $ticketID;
            $sorted_rows[$x]["OrderID"] = $order_id;
            $sorted_rows[$x]["Attendee First Name"] = $WooCommerceEventsAttendeeName;
            $sorted_rows[$x]["Attendee Last Name"] = $WooCommerceEventsAttendeeLastName;
            $sorted_rows[$x]["Attendee Email"] = $WooCommerceEventsAttendeeEmail;
            $sorted_rows[$x]["Ticket Status"] = $WooCommerceEventsStatus;
            $sorted_rows[$x]["Ticket Type"] = $ticketType;
            $sorted_rows[$x]["Variation"] = $variationOutput;
            $sorted_rows[$x]["Attendee Telephone"] = $WooCommerceEventsCaptureAttendeeTelephone;
            $sorted_rows[$x]["Attendee Company"] = $WooCommerceEventsCaptureAttendeeCompany;
            $sorted_rows[$x]["Attendee Designation"] = $WooCommerceEventsCaptureAttendeeDesignation;
            $sorted_rows[$x]["Purchaser First Name"] = $WooCommerceEventsPurchaserFirstName;
            $sorted_rows[$x]["Purchaser Last Name"] = $WooCommerceEventsPurchaserLastName;
            $sorted_rows[$x]["Purchaser Email"] = $WooCommerceEventsPurchaserEmail;
            $sorted_rows[$x]["Purchaser Phone"] = $WooCommerceEventsPurchaserPhone;
            
            if(!empty($order)) {

                $sorted_rows[$x]["Purchaser Company"] = $order->get_billing_company();

            } else {

                $sorted_rows[$x]["Purchaser Company"] = '';

            }
            
            if(!empty($exportbillingdetails)) {
                
                if(!empty($order)) {
                
                    $billing_address_1 = $order->get_billing_address_1();

                    $billing_fields = array("Billing Address 1" => '', "Billing Address 2" => '', "Billing City" => '', "Billing Postal Code" => '', "Billing Country" => '', "Billing State" => '', "Billing Phone Number" => '');
                    $billing_headings = array_keys($billing_fields);

                    foreach ($billing_headings as $value) {

                        if(!in_array($value, $csv_blueprint)) {

                            $csv_blueprint[] = $value;

                        }

                    }

                    $sorted_rows[$x]["Billing Address 1"] = $order->get_billing_address_1();
                    $sorted_rows[$x]["Billing Address 2"] = $order->get_billing_address_2();
                    $sorted_rows[$x]["Billing City"] = $order->get_billing_city();
                    $sorted_rows[$x]["Billing Postal Code"] = $order->get_billing_postcode();
                    $sorted_rows[$x]["Billing Country"] = $order->get_billing_country();
                    $sorted_rows[$x]["Billing State"] = $order->get_billing_state();
                    $sorted_rows[$x]["Billing Phone Number"] = $order->get_billing_phone();
                    
                }
            }
            
            if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
                require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
            }
            
            if ($this->is_plugin_active( 'fooevents_custom_attendee_fields/fooevents-custom-attendee-fields.php') || is_plugin_active_for_network('fooevents_custom_attendee_fields/fooevents-custom-attendee-fields.php')) {

                $Fooevents_Custom_Attendee_Fields = new Fooevents_Custom_Attendee_Fields();
                $fooevents_custom_attendee_fields_options = $Fooevents_Custom_Attendee_Fields->display_tickets_meta_custom_options_array($id);
                
                $fooevents_custom_attendee_fields_headings = array_keys($fooevents_custom_attendee_fields_options);

                foreach ($fooevents_custom_attendee_fields_headings as $value) {
                    
                    if(!in_array($value, $csv_blueprint)) {
                        
                        $csv_blueprint[] = $value;
                        
                    }
                    
                }
                
                foreach($fooevents_custom_attendee_fields_options as $key => $value) {
                    
                    $sorted_rows[$x][$key] = $value;
                    
                }
                
            }
            
            if ($this->is_plugin_active( 'fooevents_seating/fooevents-seating.php') || is_plugin_active_for_network('fooevents_seating/fooevents-seating.php')) {

                $Fooevents_Seating = new Fooevents_Seating();
                $fooevents_seating_options = $Fooevents_Seating->display_tickets_meta_seat_options_array($id);
                
                $fooevents_seating_headings = array_keys($fooevents_seating_options);

                foreach ($fooevents_seating_headings as $value) {
                    
                    if(!in_array($value, $csv_blueprint)) {
                        
                        $csv_blueprint[] = $value;
                        
                    }
                    
                }
                
                foreach($fooevents_seating_options as $key => $value) {
                    
                    $sorted_rows[$x][$key] = $value;
                    
                }
                
            }
            
            if ($this->is_plugin_active( 'fooevents_multi_day/fooevents-multi-day.php') || is_plugin_active_for_network('fooevents_multi_day/fooevents-multi-day.php')) {
                
                $WooCommerceEventsNumDays = get_post_meta($product_id, 'WooCommerceEventsNumDays', true);
                
                $Fooevents_Multiday_Events = new Fooevents_Multiday_Events();
                $fooevents_multiday_statuses = $Fooevents_Multiday_Events->get_array_of_check_ins($id, $WooCommerceEventsNumDays);

                $fooevents_multiday_statuses_headings = array_keys($fooevents_multiday_statuses);

                foreach ($fooevents_multiday_statuses_headings as $value) {
                    
                    if(!in_array($value, $csv_blueprint)) {
                        
                        $csv_blueprint[] = $value;
                        
                    }
                    
                }
                
                foreach($fooevents_multiday_statuses as $key => $value) {
                    
                    $sorted_rows[$x][$key] = $value;
                    
                }
                
            }

            $x++;
            
        }
       
        //unpaid tickets 
        if($includeUnpaidTickets) {
            
            $statuses = array('wc-processing', 'wc-on-hold' );
            $order_ids = $this->get_orders_ids_by_product_id( $event, $statuses );
            $order_ids = array_unique($order_ids);

            $x = 0;
            $unpaidTickets = array();
            foreach($order_ids as $order_id) {
                
                $unpaid_order = '';
                try {
                    
                    $unpaid_order = new WC_Order($order_id);
                    
                } catch (Exception $e) {

                } 

                $WooCommerceEventsOrderTickets = get_post_meta($order_id, 'WooCommerceEventsOrderTickets', true);

                if(!empty($WooCommerceEventsOrderTickets)) {
                    foreach ($WooCommerceEventsOrderTickets as $order => $unpaidOrderTickets) {

                        foreach($unpaidOrderTickets as $unpaidOrderTicket) {
                            
                            if($unpaidOrderTicket['WooCommerceEventsProductID'] == $_GET['event']) {
                            
                                $UnpaidWooCommerceEventsAttendeeName = $unpaidOrderTicket['WooCommerceEventsAttendeeName'];
                                if(empty($UnpaidWooCommerceEventsAttendeeName)) {

                                    $UnpaidWooCommerceEventsAttendeeName = $unpaidOrderTicket['WooCommerceEventsPurchaserFirstName'];

                                } 

                                $UnpaidWooCommerceEventsAttendeeLastName = $unpaidOrderTicket['WooCommerceEventsAttendeeLastName'];
                                if(empty($UnpaidWooCommerceEventsAttendeeLastName)) {

                                    $UnpaidWooCommerceEventsAttendeeLastName = $unpaidOrderTicket['WooCommerceEventsPurchaserLastName'];

                                } 

                                $UnpaidWooCommerceEventsAttendeeEmail = $unpaidOrderTicket['WooCommerceEventsAttendeeEmail'];
                                if(empty($UnpaidWooCommerceEventsAttendeeEmail)) {

                                    $UnpaidWooCommerceEventsAttendeeEmail = $unpaidOrderTicket['WooCommerceEventsPurchaserEmail'];

                                }

                                $unpaidOrderWooCommerceEventsVariations = $unpaidOrderTicket['WooCommerceEventsVariations'];
                                if(!empty($unpaidOrderWooCommerceEventsVariations) && !is_array($unpaidOrderWooCommerceEventsVariations)) {

                                    $unpaidOrderWooCommerceEventsVariations = json_decode($unpaidOrderWooCommerceEventsVariations);

                                }

                                $unpaidVariationOutput = '';
                                $i = 0;
                                if(!empty($unpaidOrderWooCommerceEventsVariations)) {
                                    foreach($unpaidOrderWooCommerceEventsVariations as $variationName => $variationValue) {

                                        if($i > 0) {

                                            $variationOutput .= ' | ';

                                        }

                                        $variationNameOutput = str_replace('attribute_', '', $variationName);
                                        $variationNameOutput = str_replace('pa_', '', $variationNameOutput);
                                        $variationNameOutput = str_replace('_', ' ', $variationNameOutput);
                                        $variationNameOutput = str_replace('-', ' ', $variationNameOutput);
                                        $variationNameOutput = str_replace('Pa_', '', $variationNameOutput);
                                        $variationNameOutput = ucwords($variationNameOutput);

                                        $variationValueOutput = str_replace('_', ' ', $variationValue);
                                        $variationValueOutput = str_replace('-', ' ', $variationValueOutput);
                                        $variationValueOutput = ucwords($variationValueOutput);

                                        $unpaidVariationOutput .= $variationNameOutput.': '.$variationValueOutput;

                                        $i++;
                                    }
                                }

                                $unpaidTickets[$x]["TicketID"] = 'NA';
                                $unpaidTickets[$x]["OrderID"] = $unpaidOrderTicket['WooCommerceEventsOrderID'];
                                $unpaidTickets[$x]["Attendee First Name"] = $UnpaidWooCommerceEventsAttendeeName;
                                $unpaidTickets[$x]["Attendee Last Name"] = $UnpaidWooCommerceEventsAttendeeLastName;
                                $unpaidTickets[$x]["Attendee Email"] = $UnpaidWooCommerceEventsAttendeeEmail;
                                $unpaidTickets[$x]["Ticket Status"] = $unpaidOrderTicket['WooCommerceEventsStatus'];
                                $unpaidTickets[$x]["Ticket Type"] = $unpaidOrderTicket['WooCommerceEventsTicketType'];
                                $unpaidTickets[$x]["Variation"] = $unpaidVariationOutput;
                                $unpaidTickets[$x]["Attendee Telephone"] = $unpaidOrderTicket['WooCommerceEventsAttendeeTelephone'];
                                $unpaidTickets[$x]["Attendee Company"] = $unpaidOrderTicket['WooCommerceEventsAttendeeCompany'];
                                $unpaidTickets[$x]["Attendee Designation"] = $unpaidOrderTicket['WooCommerceEventsAttendeeDesignation'];
                                $unpaidTickets[$x]["Purchaser First Name"] = $unpaidOrderTicket['WooCommerceEventsPurchaserFirstName'];
                                $unpaidTickets[$x]["Purchaser Last Name"] = $unpaidOrderTicket['WooCommerceEventsPurchaserLastName'];
                                $unpaidTickets[$x]["Purchaser Email"] = $unpaidOrderTicket['WooCommerceEventsPurchaserEmail'];
                                $unpaidTickets[$x]["Purchaser Phone"] = $unpaid_order->billing_phone;
                                $unpaidTickets[$x]["Purchaser Company"] = $unpaid_order->get_billing_company();

                                if(!empty($exportbillingdetails)) {

                                    $unpaidTickets[$x]["Billing Address 1"] = $unpaid_order->get_billing_address_1();
                                    $unpaidTickets[$x]["Billing Address 2"] = $unpaid_order->get_billing_address_2();
                                    $unpaidTickets[$x]["Billing City"] = $unpaid_order->get_billing_city();
                                    $unpaidTickets[$x]["Billing Postal Code"] = $unpaid_order->get_billing_postcode();
                                    $unpaidTickets[$x]["Billing Country"] = $unpaid_order->get_billing_country();
                                    $unpaidTickets[$x]["Billing State"] = $unpaid_order->get_billing_state();
                                    $unpaidTickets[$x]["Billing Phone Number"] = $unpaid_order->get_billing_phone();

                                }

                                if ($this->is_plugin_active( 'fooevents_custom_attendee_fields/fooevents-custom-attendee-fields.php') || is_plugin_active_for_network('fooevents_custom_attendee_fields/fooevents-custom-attendee-fields.php')) {

                                    $y = 15;
                                    if(!empty($unpaidOrderTicket['WooCommerceEventsCustomAttendeeFields'])) {

                                        foreach($unpaidOrderTicket['WooCommerceEventsCustomAttendeeFields'] as $unpaidCustomField => $unpaidCustomValue) {

                                            $unpaidTickets[$x][$unpaidCustomField] = $unpaidCustomValue;

                                        }

                                    }

                                }

                                $x++;
                            
                            }

                        }

                    }
                }

            }
            
            $sorted_rows = array_merge($sorted_rows, $unpaidTickets);
            
        }

        $output = array();
        
        $y = 0;
        foreach($sorted_rows as $item) {
            
            foreach($item as $key => $valuetest) {
                
                foreach($csv_blueprint as $heading) {

                    if($key === $heading) {

                        $output[$y][$heading] = $valuetest;
  
                    } 

                }

                foreach($csv_blueprint as $heading) {
                    
                    if(empty($output[$y][$heading])) {
                        
                        $output[$y][$heading] = '';
                        
                    }
                    
                }
            }

            $y++;
            
        }
        
        
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="'.date("Ymdhis").'.csv"');
        
        $fp = fopen('php://output', 'w');
        
        if(empty($output)) {

            $output[] = array(__('No tickets found.', 'woocommerce-events'));

        } else {

            fputcsv($fp, $csv_blueprint);

        }
        
        foreach ($output as $fields) {

            fputcsv($fp, $fields);

        }
        
        exit();

    }





















    /**
     * Generates attendee badges.
     * 
     */
    public function woocommerce_events_attendee_badges() {
        
        if(!current_user_can('publish_event_magic_tickets'))
        {
            echo "User role does not have permission to export attendee details. Please contact site admin.";
            exit();
        }
        
        error_reporting(0);
        ini_set('display_errors', '0');
        
        require($this->Config->templatePath.'attendeebadges.php');
        exit();
    }

















    
    /**
     * Get's orders that contain a particular order
     * 
     * @global object $wpdb
     * @param int $product_id
     * @param string $order_status
     * @return object
     */
    private function get_orders_ids_by_product_id( $product_id, $order_status = array( 'wc-completed' ) ){
        global $wpdb;

        $results = $wpdb->get_col("
            SELECT order_items.order_id
            FROM {$wpdb->prefix}woocommerce_order_items as order_items
            LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
            LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID
            WHERE posts.post_type = 'shop_order'
            AND posts.post_status IN ( '" . implode( "','", $order_status ) . "' )
            AND order_items.order_item_type = 'line_item'
            AND order_item_meta.meta_key = '_product_id'
            AND order_item_meta.meta_value = '$product_id'
        ");

        return $results;
    }
    
    /**
     * Generates random string used for ticket hash
     * 
     * @param int $length
     * @return string
     */
    private function generate_random_string($length = 10) {
        
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);

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