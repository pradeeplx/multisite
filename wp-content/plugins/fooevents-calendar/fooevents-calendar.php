<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**

 * Plugin Name: FooEvents Calendar

 * Description: Add event information to any post, page or custom post type and display it in a stylish calendar.

 * Version: 1.4.16

 * Author: FooEvents

 * Plugin URI: https://www.fooevents.com/fooevents-calendar/

 * Author URI: https://www.fooevents.com/

 * Developer: FooEvents

 * Developer URI: https://www.fooevents.com/

 * Text Domain: fooevents-calendar

 *

 * Copyright: © 2009-2017 FooEvents.

 * License: GNU General Public License v3.0

 * License URI: http://www.gnu.org/licenses/gpl-3.0.html

 */



require(WP_PLUGIN_DIR.'/fooevents-calendar/config.php');

require('vendors/eventbrite/HttpClient.php');



class FooEvents_Calendar {

    

    private $Config;



    public function __construct() {

        

        $plugin = plugin_basename(__FILE__); 



        add_shortcode('fooevents_calendar', array( $this, 'display_calendar'));

        add_shortcode('fooevents_events_list', array( $this, 'events_list' ));

        add_shortcode('fooevents_event', array( $this, 'event'));

        add_action('widgets_init', array($this, 'include_widgets'));

        add_action('wp_enqueue_scripts', array($this, 'include_scripts'));

        add_action('wp_enqueue_scripts', array($this, 'include_styles'));

        add_action('plugins_loaded', array( $this, 'load_text_domain'));

        

        add_action('admin_init', array($this, 'register_scripts'));

        add_action('admin_init', array($this, 'assign_admin_caps'));

        add_action('admin_init', array($this, 'register_calendar_options'));

        add_action('init', array($this, 'register_eventbrite_post_type'));

        

        add_action('add_meta_boxes', array($this, 'add_posts_meta_box'));

        add_action('save_post', array($this, 'save_posts_meta_box'));

        

        add_action('admin_menu', array($this, 'calendar_options_menu_item'));

        

        add_action('admin_notices', array($this, 'display_meta_errors'));

        

        add_action('activated_plugin', array($this, 'activate_plugin'));

        add_action('admin_menu', array( $this, 'add_intro_menu_item'));

        

        add_action('wp_ajax_fooevents-eventbrite-import', array($this, 'import_events_from_eventbrite'));

        

        add_filter('plugin_action_links_'.$plugin, array( $this, 'add_plugin_links'));

        

        register_deactivation_hook( __FILE__, array( &$this, 'remove_event_user_caps'));

        

        $this->plugin_init();

        

    }

    

    /**

     * Redirects plugin to welcome page on activation

     * 

     * @param string $plugin

     */

    public function activate_plugin($plugin) {

        

        if( $plugin == plugin_basename( __FILE__ ) ) {



            wp_redirect('admin.php?page=fooevents-calendar-help'); exit;

            

        }

        

    }

    

    /**

     * Adds and hides introduction page from menu

     * 

     */

    public function add_intro_menu_item() {

        

        add_submenu_page( 'null',__('FooEvents Calendar Introduction', 'fooevents-calendar'), __('FooEvents Calendar Introduction', 'fooevents-calendar'), 'manage_options', 'fooevents-calendar-help', array($this, 'add_intro_page')); 

        

    }

    

    /**

     * Display introduction page

     * 

     */

    public function add_intro_page() {

        

        require($this->Config->templatePath.'pluginintroduction.php');

        

    }

    

    /**

     * Adds plugin links to the plugins page

     * 

     * @param array $links

     * @return array $links

     */

    public function add_plugin_links($links) {

        

        $linkSettings = '<a href="options-general.php?page=fooevents_calendar">'.__('Settings', 'fooevents-calendar').'</a>'; 

        array_unshift($links, $linkSettings); 

        

        $linkIntroduction = '<a href="admin.php?page=fooevents-calendar-help">'.__('Introduction', 'fooevents-calendar').'</a>'; 

        array_unshift($links, $linkIntroduction); 

        

        return $links;

        

    }

    

    /**

     * Include front-end styles

     * 

     */

    public function include_styles() {

        

        wp_enqueue_style('fooevents-calendar-full-callendar-style', $this->Config->stylesPath.'fullcalendar.css', array(), '1.0.0');

        wp_enqueue_style('fooevents-calendar-full-callendar-print-style', $this->Config->stylesPath.'fullcalendar.print.css', array(), '1.0.0', 'print');

        wp_enqueue_style('fooevents-calendar-full-callendar-styles', $this->Config->stylesPath.'style.css', array(), '1.0.0');

        

        $calendar_theme = get_option('globalFooEventsCalendarTheme', true);



        if($calendar_theme === 'light') {

            

            wp_enqueue_style('fooevents-calendar-full-callendar-light', $this->Config->stylesPath.'fooevents-fullcalendar-light.css', array(), '1.0.0');

            

        } elseif($calendar_theme === 'dark') {

            

            wp_enqueue_style('fooevents-calendar-full-callendar-dark', $this->Config->stylesPath.'fooevents-fullcalendar-dark.css', array(), '1.0.0');

            

        } elseif($calendar_theme === 'flat') {

            

            wp_enqueue_style('fooevents-calendar-full-callendar-flat', $this->Config->stylesPath.'fooevents-fullcalendar-flat.css', array(), '1.0.0');

            

        } elseif($calendar_theme === 'minimalist') {

            

            wp_enqueue_style('fooevents-calendar-full-callendar-minimalist', $this->Config->stylesPath.'fooevents-fullcalendar-minimalist.css', array(), '1.0.0');

            

        }

        

        $list_theme = get_option('globalFooEventsCalendarListTheme', true);

        

        if($list_theme === 'light-card') {



            wp_enqueue_style('fooevents-calendar-list-light-card', $this->Config->stylesPath.'fooevents-list-light-card.css', array(), '1.0.0');

            

        } elseif($list_theme === 'dark-card') {

            

            wp_enqueue_style('fooevents-calendar-list-dark-card', $this->Config->stylesPath.'fooevents-list-dark-card.css', array(), '1.0.0');

            

        }

        

    }

    

    /**

     * Include front-end scripts

     * 

     */

    public function include_scripts(){

        

        wp_enqueue_script('jquery');

        wp_enqueue_script('fooevents-calendar-moment',  $this->Config->scriptsPath . 'moment.min.js', array(), '1.0.0');

        wp_enqueue_script('fooevents-calendar-full-callendar',  $this->Config->scriptsPath . 'fullcalendar.js', array(), '1.0.0');

        wp_enqueue_script('fooevents-calendar-full-callendar-locale',  $this->Config->scriptsPath . 'locale-all.js', array(), '1.0.0');

        

    }

    

    /**

     * Register admin plugin scripts.

     * 

     */

    public function register_scripts() {

        

        global $wp_locale;



        wp_enqueue_script('jquery-ui-datepicker');

        

        wp_enqueue_script('fooevents-calendar-admin-script', $this->Config->scriptsPath . 'calendar-admin.js', array( 'jquery-ui-datepicker', 'wp-color-picker' ), '1.0.0', true );

        

        $localArgs = array(

            'closeText'         => __('Done', 'fooevents-calendar'),

            'currentText'       => __('Today', 'fooevents-calendar'),

            'monthNames'        => $this->_strip_array_indices( $wp_locale->month ),

            'monthNamesShort'   => $this->_strip_array_indices( $wp_locale->month_abbrev),

            'monthStatus'       => __('Show a different month', 'fooevents-calendar'),

            'dayNames'          => $this->_strip_array_indices( $wp_locale->weekday ),

            'dayNamesShort'     => $this->_strip_array_indices( $wp_locale->weekday_abbrev),

            'dayNamesMin'       => $this->_strip_array_indices( $wp_locale->weekday_initial),

            // set the date format to match the WP general date settings

            'dateFormat'        => $this->_date_format_php_to_js( get_option('date_format')),

            // get the start of week from WP general setting

            'firstDay'          => get_option('start_of_week'),

            // is Right to left language? default is false

            'isRTL'             => $wp_locale->is_rtl(),

        );

        

        wp_localize_script('fooevents-calendar-admin-script', 'localObj', $localArgs);

        

    }

    

    /**

     * Initializes plugin

     * 

     */

    public function plugin_init() {

        

        //Main config

        $this->Config = new FooEvents_Calendar_Config();

        

    }

    

    /**

     * Register Eventbrite custom post type for imported events

     * 

     */

    public function register_eventbrite_post_type() {



        register_post_type( 'fe_eventbrite_event',

            array(

                'labels' => array(

                    'name' => __('Imported Events', 'fooevents-calendar'),

                    'singular_name' => __('Imported Event', 'fooevents-calendar')

                ),

            'public' => true,

            'has_archive' => true,

            )

        );

        

    }

    

    /**

     * Include widget class

     * 

     */

    public function include_widgets() {    

        

        require('classes/calendarwidget.php');

        

    }  

    

    /**

     * Adds meta-box to non-product events

     * 

     */

    public function add_posts_meta_box() {



        $globalFooEventsCalendarPostTypes = get_option('globalFooEventsCalendarPostTypes');

        

        if(empty($globalFooEventsCalendarPostTypes)) {

            

            $globalFooEventsCalendarPostTypes = array();

            

        }

        

        foreach($globalFooEventsCalendarPostTypes as $post_type) {

            add_meta_box(

                'fooevents-event-meta-box',__( 'FooEvents Calendar Settings', 'fooevents-calendar' ), array($this, 'display_metabox'), $post_type

            );

        }

        

    }

    

    /**

     * Displays calendar option metabox on post pages

     * 

     * @global object $post

     */

    public function display_metabox() {

        

        global $post;

        

        $WooCommerceEventsDate = get_post_meta($post->ID, 'WooCommerceEventsDate', true);

        $WooCommerceEventsEvent = get_post_meta($post->ID, 'WooCommerceEventsEvent', true);

        $WooCommerceEventsHour = get_post_meta($post->ID, 'WooCommerceEventsHour', true);

        $WooCommerceEventsPeriod = get_post_meta($post->ID, 'WooCommerceEventsPeriod', true);

        $WooCommerceEventsMinutes = get_post_meta($post->ID, 'WooCommerceEventsMinutes', true);

        $WooCommerceEventsHourEnd = get_post_meta($post->ID, 'WooCommerceEventsHourEnd', true);

        $WooCommerceEventsMinutesEnd = get_post_meta($post->ID, 'WooCommerceEventsMinutesEnd', true);

        $WooCommerceEventsEndPeriod = get_post_meta($post->ID, 'WooCommerceEventsEndPeriod', true);

        $WooCommerceEventsDate = get_post_meta($post->ID, 'WooCommerceEventsDate', true);



        $endDate = '';

        $numDays = '';

        $multiDayType = '';

        $multidayTerm = '';

        

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

        

        $globalFooEventsEventbriteToken = get_option('globalFooEventsEventbriteToken');

        $eventbrite_option = '';



        if(!empty($globalFooEventsEventbriteToken)) {

            

            $eventbrite_option = $this->generate_eventbrite_option($post);

            

        }

        

        require($this->Config->templatePath.'eventmetabox.php');

        

        wp_nonce_field('fooevents_metabox_nonce', 'fooevents_metabox_nonce');

        

    }

    

    /**

     * Generate eventbrite options to be displayed on FooEvents plugin

     * 

     * @param object $post

     * @return text

     */

    public function generate_eventbrite_option($post) {

        

        $WooCommerceEventsAddEventbrite = get_post_meta($post->ID, 'WooCommerceEventsAddEventbrite', true);

        $WooCommerceEventsAddEventbriteChecked = '';

        

        if($WooCommerceEventsAddEventbrite) {

            

            $WooCommerceEventsAddEventbriteChecked = 'checked="checked"';

            

        }

        

        ob_start();

        

        require($this->Config->templatePath.'eventbrite-options.php');

        

        $eventbrite_option = ob_get_clean();



        return $eventbrite_option;

        

    }

    

    /**

     * Processes and saves calendar options on pages

     * 

     * @param int $post_id

     */

    public function save_posts_meta_box($post_id) {



        if (!isset( $_POST['fooevents_metabox_nonce'])) {

            

            return;

            

        }

        

        if (!wp_verify_nonce( $_POST['fooevents_metabox_nonce'], 'fooevents_metabox_nonce')) {

            

            return;

            

        }

        

        if (defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) {

            

            return;

            

        }

        

        if (isset( $_POST['post_type'] ) && 'page' == $_POST['post_type']) {

            

            if (!current_user_can( 'edit_page', $post_id)) {

                

                return;

                

            }

            

             

        }  else {



            if (!current_user_can( 'edit_post', $post_id)) {

                

                return;

                

            }

            

        }



        if(isset($_POST['WooCommerceEventsEvent'])) {

            

            $WooCommerceEventsEvent = sanitize_text_field($_POST['WooCommerceEventsEvent']);

            update_post_meta($post_id, 'WooCommerceEventsEvent', $WooCommerceEventsEvent);



        }

        

        $format = get_option( 'date_format' );

        

        $min = 60 * get_option( 'gmt_offset' );

        $sign = $min < 0 ? "-" : "+";

        $absmin = abs($min);



        try {

            

            $tz = new DateTimeZone(sprintf("%s%02d%02d", $sign, $absmin/60, $absmin%60));



        } catch(Exception $e) {

            

            $serverTimezone = date_default_timezone_get();

            $tz = new DateTimeZone($serverTimezone);



        }

        

        $WooCommerceEventsDate = sanitize_text_field($_POST['WooCommerceEventsDate']);

        $event_date = $WooCommerceEventsDate;

        

        if(isset($event_date)) {

            

            if(isset($_POST['WooCommerceEventsSelectDate'][0]) && isset($_POST['WooCommerceEventsMultiDayType']) && $_POST['WooCommerceEventsMultiDayType'] == 'select') {

                

                $event_date = sanitize_text_field($_POST['WooCommerceEventsSelectDate'][0]);

                

            }

            

            $event_date = str_replace('/', '-', $event_date);

            $event_date = str_replace(',', '', $event_date);



            update_post_meta($post_id, 'WooCommerceEventsDate', $WooCommerceEventsDate);

            

            $dtime = DateTime::createFromFormat($format, $event_date, $tz);



            $timestamp = '';

            if ($dtime instanceof DateTime) {

                

                if(isset($_POST['WooCommerceEventsHour']) && isset($_POST['WooCommerceEventsMinutes'])) {

                    

                    $WooCommerceEventsHour = sanitize_text_field($_POST['WooCommerceEventsHour']);

                    $WooCommerceEventsMinutes = sanitize_text_field($_POST['WooCommerceEventsMinutes']);

                    $dtime->setTime((int)$WooCommerceEventsHour, (int)$WooCommerceEventsMinutes);



                }



                $timestamp = $dtime->getTimestamp();



            } else {



                $timestamp = 0;



            }

            

            update_post_meta($post_id, 'WooCommerceEventsDateTimestamp', $timestamp);



        }

        

        if(isset($_POST['WooCommerceEventsEndDate'])) {

            

            $WooCommerceEventsEndDate = sanitize_text_field($_POST['WooCommerceEventsEndDate']);

            update_post_meta($post_id, 'WooCommerceEventsEndDate', $WooCommerceEventsEndDate);

            

            $dtime = DateTime::createFromFormat($format, $WooCommerceEventsEndDate, $tz);



            $timestamp = '';

            if ($dtime instanceof DateTime) {

                

                if(isset($_POST['WooCommerceEventsHourEnd']) && isset($_POST['WooCommerceEventsMinutesEnd'])) {

                    

                    $WooCommerceEventsHourEnd = sanitize_text_field($_POST['WooCommerceEventsHourEnd']);

                    $WooCommerceEventsMinutesEnd = sanitize_text_field($_POST['WooCommerceEventsMinutesEnd']);

                    $dtime->setTime((int)$WooCommerceEventsHourEnd, (int)$WooCommerceEventsMinutesEnd);



                }



                $timestamp = $dtime->getTimestamp();



            } else {



                $timestamp = 0;



            }



            update_post_meta($post_id, 'WooCommerceEventsEndDateTimestamp', $timestamp);



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

        

        if(isset($_POST['WooCommerceEventsHourEnd'])) {

            

            $WooCommerceEventsHourEnd = sanitize_text_field($_POST['WooCommerceEventsHourEnd']);

            update_post_meta($post_id, 'WooCommerceEventsHourEnd', $_POST['WooCommerceEventsHourEnd']);

            

        }

        

        if(isset($_POST['WooCommerceEventsMinutesEnd'])) {

            

            $WooCommerceEventsMinutesEnd = sanitize_text_field($_POST['WooCommerceEventsMinutesEnd']);

            update_post_meta($post_id, 'WooCommerceEventsMinutesEnd', $WooCommerceEventsMinutesEnd);

            

        }

        

        if(isset($_POST['WooCommerceEventsEndPeriod'])) {

            

            $WooCommerceEventsEndPeriod = sanitize_text_field($_POST['WooCommerceEventsEndPeriod']);

            update_post_meta($post_id, 'WooCommerceEventsEndPeriod', $WooCommerceEventsEndPeriod);

            

        }

        

        if(isset($_POST['WooCommerceEventsSelectDate'])) {

            

            $WooCommerceEventsSelectDate = sanitize_text_field($_POST['WooCommerceEventsSelectDate']);

            update_post_meta($post_id, 'WooCommerceEventsSelectDate', $WooCommerceEventsSelectDate);

            

        }

        

        if(isset($_POST['WooCommerceEventsMultiDayType'])) {

            

            $WooCommerceEventsMultiDayType = sanitize_text_field($_POST['WooCommerceEventsMultiDayType']);

            update_post_meta($post_id, 'WooCommerceEventsMultiDayType', $WooCommerceEventsMultiDayType);

            

        }

        

        if(isset($_POST['WooCommerceEventsNumDays'])) {

            

            $WooCommerceEventsNumDays = sanitize_text_field($_POST['WooCommerceEventsNumDays']);

            update_post_meta($post_id, 'WooCommerceEventsNumDays', $WooCommerceEventsNumDays);

            

        }



        $WooCommerceEventsAddEventbrite = sanitize_text_field($_POST['WooCommerceEventsAddEventbrite']);

        update_post_meta($post_id, 'WooCommerceEventsAddEventbrite', $WooCommerceEventsAddEventbrite);

        

        if($_POST['WooCommerceEventsAddEventbrite']) {

            

            $this->process_eventbrite($post_id);

            

        }

        

    }

    

    /**

     * Submit event to Eventbrite

     * 

     * @param type $post_id

     */

    public function process_eventbrite($post_id) {

        

        $error = '';

        if ( !session_id() ) {

            session_start();

        }

        

        $WooCommerceEventsEventbriteID = get_post_meta($post_id, 'WooCommerceEventsEventbriteID', true);



        if(empty($_POST['WooCommerceEventsDate'])) {

            

           $errors[] = __('Event start date required for Eventbrite.', 'fooevents-calendar');

            

        }

        

        if(isset($_POST['WooCommerceEventsEndDate']) && empty($_POST['WooCommerceEventsEndDate'])) {

            

            $errors[] = __('Event end date required for Eventbrite.', 'fooevents-calendar');

            

        } 

        

        if(empty($_POST['post_title'])) {

            

            $errors[] = __('Event title required for Eventbrite.', 'fooevents-calendar');

            

        }

        

        if (isset($errors)) {

            

            $_SESSION['fooevents_calendar_errors'] = $errors;

            

            return;

        }

        

        $event_date = $_POST['WooCommerceEventsDate'].' '.$_POST['WooCommerceEventsHour'].':'.$_POST['WooCommerceEventsMinutes'].$_POST['WooCommerceEventsPeriod'];

        $event_date = str_replace('/', '-', $event_date);

        $event_date = str_replace(',', '', $event_date);

        $event_date = date('Y-m-d H:i:s', strtotime($event_date));

        $event_date = str_replace(' ', 'T', $event_date);

        $event_date = $event_date.'Z';

        

        if(!empty($_POST['WooCommerceEventsEndDate'])) {

            

            $WooCommerceEventsEndDate = $_POST['WooCommerceEventsEndDate'];

        

        } else {

            

            $WooCommerceEventsEndDate = $_POST['WooCommerceEventsDate'];

            

        }

        

        $event_end_date = $WooCommerceEventsEndDate.' '.$_POST['WooCommerceEventsHourEnd'].':'.$_POST['WooCommerceEventsMinutesEnd'].$_POST['WooCommerceEventsEndPeriod'];



        $event_end_date = str_replace('/', '-', $event_end_date);

        $event_end_date = str_replace(',', '', $event_end_date);

        $event_end_date = date('Y-m-d H:i:s', strtotime($event_end_date));

        $event_end_date = str_replace(' ', 'T', $event_end_date);

        $event_end_date = $event_end_date.'Z';

        

        $timezone = get_option('timezone_string');



        $globalFooEventsEventbriteToken = get_option('globalFooEventsEventbriteToken');

        

        $client = new HttpClient($globalFooEventsEventbriteToken);



        $description = '';

        if(isset($_POST['excerpt'])) {

            

            $description = sanitize_text_field($_POST['excerpt']);

            

        } elseif(isset($_POST['post_content'])) {

            

            $description = sanitize_text_field($_POST['post_content']);

            

        }

        

        $title = '';

        if(isset($_POST['post_title'])) {

           

            $title = sanitize_text_field($_POST['post_title']);

            

        }

        

        $event_params = array(

            'event.name.html'         => $title,

            'event.description.html'   => $description,

            'event.start.utc'    => $event_date,

            'event.end.utc'      => $event_end_date,

            'event.start.timezone' => $timezone,

            'event.end.timezone' => $timezone,

            'event.currency' => 'USD'



        );

        

        $resp = array();

        

        if(empty($WooCommerceEventsEventbriteID)) {



            $resp = $client->post_events($event_params);

            

        } else {



            $resp = $client->post_event($WooCommerceEventsEventbriteID, $event_params); 



        }



        if(isset($resp['id'])) {

            

            $id = sanitize_text_field($resp['id']);

            update_post_meta($post_id, 'WooCommerceEventsEventbriteID', $id);



        }



    }

    

    public function import_events_from_eventbrite() {

        

        /*error_reporting(E_ALL);

        ini_set('display_errors', 1);*/

        

        $globalFooEventsEventbriteToken = get_option('globalFooEventsEventbriteToken');

         

        $client = new HttpClient($globalFooEventsEventbriteToken);



        $user = $client->get('/users/me/');

        

        if(!empty($user['error'])) {

            

            echo $user['error_description'];

            exit();

            

        }

        

        $event_params = array(

            'user.id'         => $user['id'],



        );

        

        $events = $client->get_user_owned_events('me');

        $local_eventbrite_events = $this->get_local_eventbrite_events();

        

        $added_events = 0;

        $updated_events = 0;

        

        if(!empty($events['events'])) {



            foreach ($events['events'] as $event) {



                if(!in_array($event['id'], $local_eventbrite_events)) {

                    

                    $origin_start_date = $event['start']['local'];

                    $origin_end_date = $event['end']['local'];

                    

                    $postID = '';

                    

                    $WooCommerceEventsDate = date('Y-m-d', strtotime($origin_start_date));

                    $WooCommerceEventsHour = date('H', strtotime($origin_start_date));

                    $WooCommerceEventsMinutes = date('i', strtotime($origin_start_date));



                    $WooCommerceEventsEndDate = date('Y-m-d', strtotime($origin_end_date));

                    $WooCommerceEventsHourEnd = date('H', strtotime($origin_end_date));

                    $WooCommerceEventsMinutesEnd = date('i', strtotime($origin_end_date));

                    

                    $post = array();

                    $origin_query = new WP_Query(array('post_type' => 'fe_eventbrite_event', 'posts_per_page' => -1, 'meta_query' => array( array('key' => 'WooCommerceEventsEventbriteID', 'value' => $event['id']))));

                    $origin = $origin_query->get_posts();

                    

                    $content = '';

                    

                    if(!empty($event['description']['text'])) {

                        

                        $content = $event['description']['text'];

                        

                    } else {

                        

                        $content = $event['name']['text'];

                        

                    }

                    

                    if(empty($origin)) {



                        $post = array(

                            'post_content' => $content,

                            'post_status' => "publish",

                            'post_title' => $event['name']['text'],

                            'post_type' => "fe_eventbrite_event"

                        );



                        $postID = wp_insert_post($post);

                        update_post_meta($postID, 'WooCommerceEventsEventbriteID', $event['id']);



                        $added_events++;

                        

                    } else {

                        

                        $origin = $origin[0];

                        

                        $post = array(

                            'ID' => $origin->ID,

                            'post_content' => $content,

                            'post_status' => "publish",

                            'post_title' => $event['name']['text'],

                            'post_type' => "fe_eventbrite_event"

                        );

                        

                        $postID = wp_update_post($post);

                        

                        $updated_events++;

                        

                    }

                    

                    update_post_meta($postID, 'WooCommerceEventsDate', $WooCommerceEventsDate);

                    update_post_meta($postID, 'WooCommerceEventsHour', $WooCommerceEventsHour);

                    update_post_meta($postID, 'WooCommerceEventsMinutes', $WooCommerceEventsMinutes);

                    

                    update_post_meta($postID, 'WooCommerceEventsEndDate', $WooCommerceEventsEndDate);

                    update_post_meta($postID, 'WooCommerceEventsHourEnd', $WooCommerceEventsHourEnd);

                    update_post_meta($postID, 'WooCommerceEventsMinutesEnd', $WooCommerceEventsMinutesEnd);

                    

                    update_post_meta($postID, 'WooCommerceEventsEvent', 'Event');

                    

                }

                

            }

            

        }



        printf(__('%d events added. %d events updated.', 'fooevents-calendar'), $added_events, $updated_events);

        exit();

        

    }

    

    public function get_local_eventbrite_events() {

        

        $globalFooEventsCalendarPostTypes = get_option('globalFooEventsCalendarPostTypes');



        $events_query = new WP_Query(array('post_type' => $globalFooEventsCalendarPostTypes, 'posts_per_page' => -1, 'meta_query' => array( array('key' => 'WooCommerceEventsEventbriteID', 'compare' => 'EXISTS'))));

        $events = $events_query->get_posts();



        $return_ids = array();

        

        if(!empty($events)) {

            

            foreach($events as $event) {

                

                $WooCommerceEventsEventbriteID = get_post_meta($event->ID, 'WooCommerceEventsEventbriteID', true);

                $return_ids[] = $WooCommerceEventsEventbriteID;

                

            }

            

        }

        

        return $return_ids;

        

    }

   

    

    /**

     * Displays a shortcode event

     * 

     */

    public function event($attributes) {

        

        /*ini_set('display_errors', 1);

        ini_set('display_startup_errors', 1);

        error_reporting(E_ALL);*/

        

        $productID = '';



        if(!empty($attributes['product'])) {

        

            $productID = $attributes['product'];

            

        }

        

        ob_start();

        if(!empty($productID)) {

            

            $event = get_post($productID);

            

            $ticketTerm = get_post_meta($productID, 'WooCommerceEventsTicketOverride', true);



            if(empty($ticketTerm)) {



                $ticketTerm = get_option('globalWooCommerceEventsTicketOverride', true);



            }



            if(empty($ticketTerm) || $ticketTerm == 1) {



                $ticketTerm = __('Book ticket', 'woocommerce-events');



            }

            

            if(!empty($event)) {



                $thumbnail = get_the_post_thumbnail_url($event->ID);



                //Check theme directory for template first

                if(file_exists($this->Config->templatePathTheme.'event.php') ) {



                     include($this->Config->templatePathTheme.'event.php');



                }else {



                    require($this->Config->templatePath.'event.php');



                }



            }

        

        }

        

        $event_output = ob_get_clean();



        return $event_output;

        

    }

    

    /**

     * Displays a shortcode list of events

     * 

     * @param array $attributes

     */

    public function events_list($attributes) {



        $num_events = '';

        $sort = '';

        $cat = '';

        $include_cats = array();



        if(!empty($attributes['num'])) {

            

            $num_events = $attributes['num'];

            

        } else {

            

            $num_events = 10;

            

        }

        

        if(!empty($attributes['sort'])) {

            

            $sort = strtoupper($attributes['sort']);

            

        } else {

            

            $sort = 'asc';

            

        }

        

        if(!empty($attributes['include_cat'])) {



            $include_cats = explode(',', $attributes['include_cat']);

            

        }

        

        if(!empty($attributes['cat'])) {

            

            $cat = $attributes['cat'];

            

        } else {

            

             $cat = '';

            

        }

        

        if ( ! function_exists( 'is_plugin_active_for_network' ) ) {

            require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

        }

        

        $events = array();

        $non_product_events = array();

        

        if ($this->is_plugin_active('fooevents/fooevents.php') || is_plugin_active_for_network('fooevents/fooevents.php')) {

        



            $events = $this->get_events($include_cats);

            $events = $this->fetch_events($events, 'events_list', true);

              

        }    

        

        $non_product_events = $this->get_non_product_events();

        $non_product_events = $this->fetch_events($non_product_events, 'events_list', true);

        

        $events = array_merge($events, $non_product_events);

        

        $events = $this->sort_events_by_date($events, $sort);



        if ($sort == 'asc') {

            

            if(!empty($num_events) && is_numeric($num_events)) {



                $events = array_slice($events, -$num_events, $num_events, true);



            }

            

        } else {

            

            if(!empty($num_events) && is_numeric($num_events)) {



                $events = array_slice($events, 0, $num_events, true);



            }

            

        }



        if(empty($attributes['type'])) {

            

            ob_start();

            

        }

        



        foreach($events as $key => $event) {



            if(empty($event)) {



                unset($events[$key]);



            }



            $ticketTerm = get_post_meta($event['post_id'], 'WooCommerceEventsTicketOverride', true);



            if(empty($ticketTerm)) {



                $ticketTerm = get_option('globalWooCommerceEventsTicketOverride', true);



            }



            if(empty($ticketTerm) || $ticketTerm == 1) {



                $ticketTerm = __( 'Book ticket', 'woocommerce-events' );



            }



            $events[$key]['ticketTerm'] = $ticketTerm;



        }



        //Check theme directory for template first

        if(file_exists($this->Config->templatePathTheme.'list_of_events.php') ) {



             include($this->Config->templatePathTheme.'list_of_events.php');



        }else {



            require($this->Config->templatePath.'list_of_events.php');



        }

     

        if(empty($attributes['type'])) {

            

            $event_list = ob_get_clean();



            return $event_list;

            

        }

        

    }



    /**

     * Outputs calendar to screen

     * 

     * @param array $attributes

     */

    public function display_calendar($attributes) {

        

        $include_cats = array();

        

        if(empty($attributes)) {

            

            $attributes = array();

            

        }



        $calendar_id = 'fooevents_calendar';

        

        if(!empty($attributes['id'])) {

            

            $calendar_id = $attributes['id'].'_fooevents_calendar';

            $attributes['id'] = $attributes['id'].'_fooevents_calendar';

            

            

        } else {

            

            $attributes['id'] = $calendar_id;

            

        }

        

        if(!empty($attributes['include_cat'])) {



            $include_cats = explode(',', $attributes['include_cat']);

            

        }

        

        if(!empty($attributes['cat'])) {

            

            $cat = $attributes['cat'];

            

        } else {

            

             $cat = '';

            

        }

        

        $attributes = $this->process_shortcodes($attributes);

        

        $globalFooEventsTwentyFourHour = get_option('globalFooEventsTwentyFourHour');



        if($globalFooEventsTwentyFourHour == 'yes') {

            

            $attributes['timeFormat'] = 'H:mm';

            

        }

        

        $attributes['buttonText'] = array("today" => __('Today', 'fooevents-calendar'));

        

        if ( ! function_exists( 'is_plugin_active_for_network' ) ) {

            require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

        }

        

        $events = array();

        $non_product_events = array();

        

        if ($this->is_plugin_active('fooevents/fooevents.php') || is_plugin_active_for_network('fooevents/fooevents.php')) {

        

            $events = $this->get_events($include_cats);
           
            $events = $this->fetch_events($events, 'calendar', false);
           

        }

        

        $non_product_events = $this->get_non_product_events();

        $non_product_events = $this->fetch_events($non_product_events, 'calendar', false);



        $events = array_merge_recursive($events, $non_product_events);   



        $json_events = array_merge($attributes, $events);

        $json_events = addslashes(json_encode($json_events, JSON_HEX_QUOT | JSON_HEX_APOS));

        

       

        

        $localArgs = array("json_events" => $json_events);

        

        if(empty($attributes['type'])) {

            

            ob_start();

            

        }

        

        //Check theme directory for template first

        if(file_exists($this->Config->templatePathTheme.'calendar.php') ) {

            

            include($this->Config->templatePathTheme.'calendar.php');



        } else {



            include($this->Config->templatePath.'calendar.php');



        }



        if(empty($attributes['type'])) {

            

            $calendar = ob_get_clean();



            return $calendar;

            

        }

        

    }

    

    /**

     * Displays event background color event options

     * 

     * @param object $post

     * @return string

     */

    public function generate_event_background_color_option($post) {

        

        ob_start();

        

        $WooCommerceEventsBackgroundColor = get_post_meta($post->ID, 'WooCommerceEventsBackgroundColor', true);



        require($this->Config->templatePath.'background-color-option.php');



        $background_color_option = ob_get_clean();

        

        return $background_color_option;

        

    }

    

    /**

     * Displays event background text options

     * 

     * @param object $post

     * @return string

     */

    public function generate_event_background_text_option($post) {

        

        ob_start();

        

        $WooCommerceEventsTextColor = get_post_meta($post->ID, 'WooCommerceEventsTextColor', true);



        require($this->Config->templatePath.'text-color-option.php');



        $text_color_option = ob_get_clean();

        

        return $text_color_option;

        

    }

    

    

    /**

     * Sorts events either ascending or descending

     * 

     * @param array $events

     * @param string $sort

     * @return array

     */

    public function sort_events_by_date($events, $sort) {

        

        if(!empty($events)) {

            

            $events = $events['events'];



            if(strtolower($sort) == 'asc') {



                usort($events, array($this, 'event_date_compare_asc'));



            } else {



                usort($events, array($this, 'event_date_compare_desc'));



            }



            foreach($events as $key => $event) {



                if(empty($event['title'])) {



                    unset($events[$key]);



                }



            }

        

        }

        return $events;

        

    }

    

    /**

     * Compares two dates in ascending order

     * 

     * @param array $a

     * @param array $b

     * @return array

     */

    public function event_date_compare_asc($a, $b)

    {

        if(empty($a)) {

            

            

            $a = array('start' => '');

            

        }

        

        if(empty($a['start'])) {

            

            

            $a = array('start' => '');

            

        }

        

        if(empty($b)) {

            

           

            $b = array('start' => '');

            

        }

        

        if(empty($b['start'])) {

            

            

            $b = array('start' => '');

            

        }



        $t1 = strtotime($a['start']);

        $t2 = strtotime($b['start']);

        

        return $t1 - $t2;



    }   

    

    /**

     * Compares two dates in descending order

     * 

     * @param array $a

     * @param array $b

     * @return array

     */

    public function event_date_compare_desc($a, $b)

    {

        if(empty($a)) {

            

            

            $a = array('start' => '');

            

        }

        

        if(empty($a['start'])) {

            

            

            $a = array('start' => '');

            

        }

        

        if(empty($b)) {

            

           

            $b = array('start' => '');

            

        }

        

        if(empty($b['start'])) {

            

            

            $b = array('start' => '');

            

        }



        $t2 = strtotime($a['start']);

        $t1 = strtotime($b['start']);

        

        return $t1 - $t2;



    }

    

    /**

     * Get all events 

     *

     * @return array

     */

    public function get_events($include_cats = array()) {

        
        $searchArray=array();
        $by_destination = '';
        if(isset($_GET['by_country']) && strlen($_GET['by_country']) ){
            $by_destination = ( trim($_GET['by_country']) != '' ) ? trim($_GET['by_country']) : '';

            $searchArray[]=array ('key' => 'match_country',
                                 'value' => $by_destination,
                                 'compare' => '=');
        }
        $by_checkin = '';
      
        if(isset($_GET['by_date']) && strlen($_GET['by_date'])){
            $by_checkin = ( trim($_GET['by_date']) != '' ) ? trim($_GET['by_date']) : '';
            $searchArray[]=array ('key' => 'MatchDate',
                                 'value' => $by_checkin,
                                 'compare' => '=');
        }
        $by_city = '';
        if(isset($_GET['by_city']) && strlen($_GET['by_city']) && strtolower(trim($_GET['by_city'])) !='all'){
            $by_city = ( trim($_GET['by_city']) != '' ) ? trim($_GET['by_city']) : '';
         
            $searchArray[]=array ('key' => 'match_city',
                                 'value' => $by_city,
                                 'compare' => '=');
        }
        
        // $by_checkout = '';
        // if(isset($_GET['by_checkout'])){
        //     $by_checkout = ( trim($_GET['by_checkout']) != '' ) ? trim($_GET['by_checkout']) : '';
        // }
        $by_team = '';
        if(isset($_GET['by_team']) && strlen($_GET['by_team'])){
            $by_team = ( trim($_GET['by_team']) != '' ) ? trim($_GET['by_team']) : '';
           $searchArray[]=array(
                            'relation' => 'OR',
                            array(
                                'key' => 'Team1',
                                'value' => $by_team,
                                'compare' => '=',
                            ),
                            array(
                                'key' => 'Team2',
                                'value' => $by_team,
                                'compare' => '=',
                            )
                        );
        }

        $searchArray[]=array ('key' => 'WooCommerceEventsEvent',

                                'value' => 'Event',

                                'compare' => '=',

                                );
        // var_dump($by_destination);
        //  var_dump($by_checkin);
         //  var_dump($searchArray);
         //  echo '----------'.strlen($_GET['by_team']);
        $args = array (

        'post_type' => 'product',

        'posts_per_page' => -1,

        'meta_query' => array ($searchArray),

        );

        

        if(!empty($include_cats)) {



            $args['tax_query'] = array('relation' => 'OR');

            

            foreach($include_cats as $include_cat) {

                

                $args['tax_query'][] = array(

                    'taxonomy' => 'product_cat',

                    'field' => 'slug',

                    'terms' => $include_cat

                );

                

            }

            

        }



        $events = new WP_Query($args) ;

        

        return $events->get_posts();

        

    }

    

    /**

     * Get custom post type events that are not WooCommerce products

     * 

     * @return array

     */

    public function get_non_product_events() {

        

        $globalFooEventsCalendarPostTypes = get_option('globalFooEventsCalendarPostTypes');

        $globalFooEventsCalendarPostTypes[] = 'fe_eventbrite_event';

        



        if(empty($globalFooEventsCalendarPostTypes)) {

            

            $globalFooEventsCalendarPostTypes = array('post');

            

        }

        

        $args = array (

        'post_type' => $globalFooEventsCalendarPostTypes,    

        'posts_per_page' => -1,

        'meta_query' => array (

            array (

                'key' => 'WooCommerceEventsDate',

                'value' => '',

                'compare' => '!=',

                ),

            ),

        );

        

        $events = new WP_Query($args); 

        

        return $events->get_posts();

        

    }

    

    /**

     * Process fetched events

     * 

     * @param array $events

     * @return array

     */

    public function fetch_events($events, $display_type, $include_desc = true) {



        $json_events = array();



        $x = 0;

        foreach($events as $event) {

            

            $Fooevents_Multiday_Events = '';

            $multi_day_type = '';

            

            if ( ! function_exists( 'is_plugin_active_for_network' ) ) {

                require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

            }

            

            if ($this->is_plugin_active('fooevents_multi_day/fooevents-multi-day.php') || is_plugin_active_for_network('fooevents_multi_day/fooevents-multi-day.php')) {

                

                $Fooevents_Multiday_Events = new Fooevents_Multiday_Events();

                $multi_day_type = $Fooevents_Multiday_Events->get_multi_day_type($event->ID);

            }

            

            $event_date_unformated = get_post_meta($event->ID, 'WooCommerceEventsDate', true);

            $event_hour = get_post_meta($event->ID, 'WooCommerceEventsHour', true);

            $event_minutes = get_post_meta($event->ID, 'WooCommerceEventsMinutes', true);

            $event_period = get_post_meta($event->ID, 'WooCommerceEventsPeriod', true);

            $event_background_color = get_post_meta($event->ID, 'WooCommerceEventsBackgroundColor', true);

            $event_text_color = get_post_meta($event->ID, 'WooCommerceEventsTextColor', true);

            $stock = get_post_meta($event->ID, '_stock', true);



            if(empty($event_date_unformated)) {

                

                if($multi_day_type != 'select') {

                 

                    continue;

                

                }

                

            }

            



            $event_date = $event_date_unformated.' '.$event_hour.':'.$event_minutes.$event_period;

            $format = get_option( 'date_format' );

            

            if($format == 'd/m/Y') {

                

                $event_date = str_replace("/", "-", $event_date);



            }



            $event_date = date_i18n('Y-m-d H:i:s', strtotime($event_date));

            $event_date = str_replace(' ', 'T', $event_date);



            $all_day_event = false;

            $globalFooEventsAllDayEvent = get_option( 'globalFooEventsAllDayEvent' );

            

            if($globalFooEventsAllDayEvent == 'yes') {

                

                $all_day_event = true;

                

            }


            $userInfo = get_user_by('ID', $event->post_author);
            $host_product_url = get_permalink($event->ID);
            if( $userInfo ){
               $host_product_url = site_url('user/'.$userInfo->user_login.'/?profiletab=experience');
            } 

            $json_events['events'][$x]= array(

                'title' => $event->post_title,

                'allDay' => $all_day_event,

                'start' => $event_date,

                'unformated_date' => $event_date_unformated,

            
                'url' => $host_product_url,

                'post_id' => $event->ID

            );

            

            if(!empty($event_background_color)) {

                

                $json_events['events'][$x]['color'] = $event_background_color;

                

            }

            

            if(!empty($event_text_color)) {

                

                $json_events['events'][$x]['textColor'] = $event_text_color;

                

            }

            

            if($include_desc) {

                

                $json_events['events'][$x]['desc'] = $event->post_excerpt;

                

            }

            

            if($multi_day_type == 'select') {

                

                unset($json_events['events'][$x]);

                $x--;

                

            }

            

            if ( ! function_exists( 'is_plugin_active_for_network' ) ) {

                require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

            }

            

            if ($this->is_plugin_active('fooevents_multi_day/fooevents-multi-day.php') || is_plugin_active_for_network('fooevents_multi_day/fooevents-multi-day.php')) {

      

                $event_end_date = $Fooevents_Multiday_Events->get_end_date($event->ID);

                $globalFooEventsStartDay = get_option('globalFooEventsStartDay');

                

                $multi_day_dates = array();

                

                if($multi_day_type == 'select') {



                    $multi_day_dates= $Fooevents_Multiday_Events->get_multi_day_selected_dates($event->ID);

                    

                    if($display_type == 'events_list') {

                        

                        $multi_day_dates = array($multi_day_dates[0]);

                        

                    }

                    

                    $y = 0;

                    foreach($multi_day_dates as $date) {

                        

                        if($y > 0 && $globalFooEventsStartDay == 'yes') {

                            

                            continue;

                            

                        }

                        

                        $x++;



                        $event_date = $date.' '.$event_hour.':'.$event_minutes.$event_period;

                        $event_date = str_replace('/', '-', $event_date);

                        $event_date = str_replace(',', '', $event_date);

                        $event_date = date_i18n('Y-m-d H:i:s', strtotime($event_date));

                        $event_date = str_replace(' ', 'T', $event_date);

                        

                        $json_events['events'][$x]= array(

                            'title' => $event->post_title,

                            'allDay' => $all_day_event,

                            'start' => $event_date,

                            'unformated_date' => $date,

                            'url' => get_permalink($event->ID),

                            'post_id' => $event->ID

                        );

                        

                        if($include_desc) {



                            $json_events['events'][$x]['desc'] = $event->post_excerpt;



                        }

                        

                        if(!empty($event_background_color)) {



                            $json_events['events'][$x]['color'] = $event_background_color;



                        }



                        if(!empty($event_text_color)) {



                            $json_events['events'][$x]['textColor'] = $event_text_color;



                        }

                        

                        $y++;

                        

                    }

                

                } else {

                    

                    if(!empty($event_end_date)) {



                        $event_end_date = $Fooevents_Multiday_Events->format_end_date($event->ID);

                        

                        if($globalFooEventsStartDay != 'yes') {

                        

                            $json_events['events'][$x]['end'] = $event_end_date;

                        

                        }



                    }

                    

                }

                

            }



            $product = wc_get_product($event->ID);

  

            if(!empty($product)) {

                if($product->is_in_stock()) {



                    $json_events['events'][$x]['in_stock'] = 'yes';



                } else {



                    $json_events['events'][$x]['in_stock'] = 'no';



                }

            } else {

                

                //Not a product so make in stock

                $json_events['events'][$x]['in_stock'] = 'yes';

                

            }

            

            $x++;

        }

    

        return $json_events;

        

    }

    

    /**

     * Process shortcodes

     * 

     * @param array $attributes

     * @return array

     * 

     */

    public function process_shortcodes($attributes) {

        

        $processed_attributes = array();



        if(empty($attributes['locale'])) {

            

            $attributes['locale'] = get_locale();

            

        } 

        

        foreach($attributes as $key => $attribute) {

            

            if (strpos($attribute, ':') !== false) {

                

                $att_ret = array();

                $parts = explode(';', $attribute);

                

                foreach($parts as $part) {

                    

                    if (strpos($part, '{') !== false) {

                        

                        $att_ret_sub = array();

                        

                        $start  = strpos($part, '{');

                        $end    = strpos($part, '}', $start + 1);

                        $length = $end - $start;

                        $att_sub = substr($part, $start + 1, $length - 1);

                        

                        $atts = explode(':', $part);

                        $att_key = trim($atts[0]);

                        

                        $atts = explode(':', $att_sub);

                        

                        $att_sub_key = trim($atts[0]);

                        $atts[1] = str_replace("'", "", $atts[1]);

                        $att_att = trim($atts[1]);

                        

                        $att_ret_sub[$this->process_key($att_sub_key)] = $att_att;

                        

                        $att_ret[$this->process_key($att_key)] = $att_ret_sub;

                        

                    } else {

                    

                        $atts = explode(':', $part);



                        $att_key = trim($atts[0]);

                        $atts[1] = str_replace("'", "", $atts[1]);

                        $att_att = trim($atts[1]);



                        $att_ret[$this->process_key($att_key)] = $att_att;



                    }



                }

                

                $processed_attributes[$this->process_key($key)] = $att_ret;

                

            } else {

            

                $processed_attributes[$this->process_key($key)] = $attribute;

            

            }

            

        }



        return $processed_attributes;

        

    }



    /**

     * Adds global calendar options to the WooCommerce Event settings panel 

     * 

     * @return array

     */

    public function get_tab_settings() {

        

        $settings = array('section_title' => array(

                'name'      => __( 'Calendar Settings', 'fooevents-calendar' ),

                'type'      => 'title',

                'desc'      => '',

                'id'        => 'wc_settings_fooevents_pdf_tickets_settings_title'

            ),

            'globalFooEventsTwentyFourHour' => array(

                'name'  => __( 'Enable 24 hour time format', 'fooevents-calendar' ),

                'type'  => 'checkbox',

                'id'    => 'globalFooEventsTwentyFourHour',

                'value' => 'yes',

                'desc'  => __( 'Uses 24 hour time format on the calendar.', 'fooevents-calendar' ),

                'class' => 'text uploadfield'

            )

            ,

            'globalFooEventsStartDay' => array(

                'name'  => __( 'Only display start day', 'fooevents-calendar' ),

                'type'  => 'checkbox',

                'id'    => 'globalFooEventsStartDay',

                'value' => 'yes',

                'desc'  => __( 'When multi-day plugin is active only display the event start day', 'fooevents-calendar' ),

                'class' => 'text uploadfield'

            ),

            'globalFooEventsAllDayEvent' => array(

                'name'  => __( 'Enable full day events', 'fooevents-calendar' ),

                'type'  => 'checkbox',

                'id'    => 'globalFooEventsAllDayEvent',

                'value' => 'yes',

                'desc'  => __( 'Removes event time from calendar entry titles.', 'fooevents-calendar' ),

                'class' => 'text uploadfield'

            ),

            'globalFooEventsCalendarTheme' => array(

                'name'  => __( 'Calendar theme', 'fooevents-calendar' ),

                'type'  => 'select',

                'id'    => 'globalFooEventsCalendarTheme',

                'std'     => '',

                'default' => '',

                'options' => array(

                    'default'   => __('Default', 'fooevents-calendar'),

                    'light'     => __('Light', 'fooevents-calendar'),

                    'dark'      => __('Dark', 'fooevents-calendar'),

                    'flat'      => __('Flat', 'fooevents-calendar'),

                    'minimalist'      => __('Minimalist', 'fooevents-calendar')

                ),

                'desc'  => __( 'Selects calendar theme to be used on Wordpress frontend.', 'fooevents-calendar' ),

                'class' => 'text uploadfield'

            ),

            'globalFooEventsCalendarListTheme' => array(

                'name'  => __( 'Events list theme', 'fooevents-calendar' ),

                'type'  => 'select',

                'id'    => 'globalFooEventsCalendarListTheme',

                'std'     => '',

                'default' => '',

                'options' => array(

                    'default'   => __('Default', 'fooevents-calendar'),

                    'light-card'     => __('Light Card', 'fooevents-calendar'),

                    'dark-card'      => __('Dark Card', 'fooevents-calendar')

                ),

                'desc'  => __( 'Selects events list theme to be used on Wordpress frontend.', 'fooevents-calendar' ),

                'class' => 'text uploadfield'

            )

            );

        

        $settings['section_end'] = array(

            'type' => 'sectionend',

            'id' => 'wc_settings_fooevents_pdf_tickets_settings_end'

        );

        

        return $settings;

        

    }

    

    /**

     * Register calendar options

     * 

     */

    public function register_calendar_options() {

        

        register_setting('fooevents-calendar-settings-group', 'globalFooEventsTwentyFourHour');

        register_setting('fooevents-calendar-settings-group', 'globalFooEventsStartDay');

        register_setting('fooevents-calendar-settings-group', 'globalFooEventsAllDayEvent');

        register_setting('fooevents-calendar-settings-group', 'globalFooEventsCalendarTheme');

        register_setting('fooevents-calendar-settings-group', 'globalFooEventsCalendarListTheme');

        register_setting('fooevents-calendar-settings-group', 'globalFooEventsCalendarPostTypes');

        register_setting('fooevents-calendar-settings-group', 'globalFooEventsEventbriteToken');

        

    }

    

    /**

     * Display calendar settings menu link

     * 

     */

    public function calendar_options_menu_item() {

        

        add_options_page( 'FooEvents Calendar', 'FooEvents Calendar', 'publish_fooevents_calendar', 'fooevents_calendar', array($this, 'calendar_options'));

        

    }

    

    /**

     * Display calendar options page

     * 

     */

    public function calendar_options() {

        

        if (!current_user_can('publish_fooevents_calendar'))  {

		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );

	}

        

        $globalFooEventsTwentyFourHour = get_option('globalFooEventsTwentyFourHour');

        $globalFooEventsTwentyFourHourChecked = '';

        

        if($globalFooEventsTwentyFourHour == 'yes') {

            

            $globalFooEventsTwentyFourHourChecked = 'checked="checked"';

            

        }

        

        $globalFooEventsStartDay = get_option('globalFooEventsStartDay');

        $globalFooEventsStartDayChecked = '';

        if($globalFooEventsStartDay == 'yes') {

            

            $globalFooEventsStartDayChecked = 'checked="checked"';

            

        }

        

        $globalFooEventsAllDayEvent = get_option('globalFooEventsAllDayEvent');

        $globalFooEventsAllDayEventChecked = '';

        if($globalFooEventsAllDayEvent == 'yes') {

            

            $globalFooEventsAllDayEventChecked = 'checked="checked"';

            

        }

        

        $globalFooEventsCalendarTheme = get_option('globalFooEventsCalendarTheme');

        $globalFooEventsCalendarListTheme = get_option('globalFooEventsCalendarListTheme');

        $globalFooEventsCalendarPostTypes = get_option('globalFooEventsCalendarPostTypes');

        $globalFooEventsEventbriteToken = get_option('globalFooEventsEventbriteToken');



        if(empty($globalFooEventsCalendarPostTypes)) {

            

            $globalFooEventsCalendarPostTypes = array();

            

        }



        $post_types = $this->get_custom_post_types(); 



        require($this->Config->templatePath.'calendar-options.php');

        

    }

    

    /**

     * Assign admin permissions

     * 

     */

    public function assign_admin_caps() {

        

        $role = get_role('administrator');

        $role->add_cap('publish_fooevents_calendar');



    }

    

    /**

     * Removes user permissions

     * 

     * @global array $wp_roles

     */

    public function remove_event_user_caps() {

        

        $delete_caps = array(

            'publish_fooevents_calendar', 

        );

        

        global $wp_roles;

	foreach ($delete_caps as $cap) {

                

            foreach (array_keys($wp_roles->roles) as $role) {



                echo $role.' - '.$cap.'<br />';



                $wp_roles->remove_cap($role, $cap);



            }

                

	}

        

    }

    

    /**

     * Process keys and bride FullCalendar js

     * 

     * @param array $key

     * @return array

     */

    public function process_key($key) {



        $check_key = $this->check_general($key);

        if($check_key !== $key) {



            return $check_key;

            

        }

        

        $check_key = $this->check_views($key);

        if($check_key !== $key) {



            return $check_key;

            

        }

        

        $check_key = $this->check_agenda($key);

        if($check_key !== $key) {



            return $check_key;

            

        }

        

        $check_key = $this->check_listview($key);

        if($check_key !== $key) {



            return $check_key;

            

        }

        

        $check_key = $this->check_currentdate($key);

        if($check_key !== $key) {



            return $check_key;

            

        }

        

        $check_key = $this->check_texttimecust($key);

        if($check_key !== $key) {



            return $check_key;

            

        }

        

        $check_key = $this->check_clickinghovering($key);

        if($check_key !== $key) {



            return $check_key;

            

        }

        

        $check_key = $this->check_selection($key);

        if($check_key !== $key) {



            return $check_key;

            

        }

        

        $check_key = $this->check_eventdata($key);

        if($check_key !== $key) {



            return $check_key;

            

        }

        

        $check_key = $this->check_eventrendering($key);

        if($check_key !== $key) {



            return $check_key;

            

        }

        

        $check_key = $this->check_timelineview($key);

        if($check_key !== $key) {



            return $check_key;

            

        }

        

        return $key;

        

    }

    

    /**

     * Check generals options

     * 

     * @param string $key

     * @return string

     */

    public function check_general($key) {

        

        switch ($key) {

            case "defaultview":

                return "defaultView";

                break;

            case "defaultdate":

                return "defaultDate";

                break;

            case "custombuttons":

                return "customButtons";

                break;

            case "buttonicons":

                return "buttonIcons";

                break;

            case "themebuttonicons":

                return "themeButtonIcons";

                break;

            case "firstday":

                return "firstDay";

                break;

            case "isrtl":

                return "isRTL";

                break;

            case "hiddendays":

                return "hiddenDays";

                break;

            case "fixedweekcount":

                return "fixedWeekCount";

                break;

            case "weeknumbers":

                return "weekNumbers";

                break;

            case "weeknumberswithindays":

                return "weekNumbersWithinDays";

                break;

            case "weeknumbercalculation":

                return "weekNumberCalculation";

                break;

            case "businesshours":

                return "businessHours";

                break;

            case "contentheight":

                return "contentHeight";

                break;

            case "aspectratio":

                return "aspectRatio";

                break;

            case "handlewindowresize":

                return "handleWindowResize";

                break;

            case "windowresizedelay":

                return "windowResizeDelay";

                break;

            case "eventlimit":

                return "eventLimit";

                break;

            case "eventlimitclick":

                return "eventLimitClick";

                break;

            case "viewrender":

                return "viewRender";

                break;

            case "viewdestroy":

                return "viewDestroy";

                break;

            case "dayrender":

                return "dayRender";

                break;

            case "windowresize":

                return "windowResize";

                break;

        }

        

        return $key;

        

    }

    

    /**

     * Check view options

     * 

     * @param string $key

     * @return string

     */

    public function check_views($key) {

        

        switch ($key) {

            case "defaultview":

                return "defaultView";

                break;

            case "getview":

                return "getView";

                break;

            case "changeview":

                return "changeView";

                break;

        }   

        

        return $key;

        

    }

    

    /**

     * Check agenda options

     * 

     * @param string $key

     * @return string

     */

    public function check_agenda($key) {

        

        switch ($key) {

            case "alldayslot":

                return "allDaySlot";

                break;

            case "alldaytext":

                return "allDayText";

                break;

            case "slotduration":

                return "slotDuration";

                break;

            case "slotlabelformat":

                return "slotLabelFormat";

                break;

            case "slotlabelinterval":

                return "slotLabelInterval";

                break;

            case "snapduration":

                return "snapDuration";

                break;

            case "scrolltime":

                return "scrollTime";

                break;

            case "mintime":

                return "minTime";

                break;

            case "maxtime":

                return "maxTime";

                break;

            case "sloteventoverlap":

                return "slotEventOverlap";

                break;

        }   

        

        return $key;

        

    }

    

    /**

     * Check listview options

     * 

     * @param string $key

     * @return string

     */

    public function check_listview($key) {

        

        switch ($key) {

            case "listdayformat":

                return "listDayFormat";

                break;

            case "listdayaltformat":

                return "listDayAltFormat";

                break;

            case "noeventsmessage":

                return "noEventsMessage";

                break;

        }

        

        return $key;

        

    }

    

    /**

     * Check currentdate options

     * 

     * @param string $key

     * @return string

     */

    public function check_currentdate($key) {

        

        switch ($key) {

            case "defaultdate":

                return "defaultDate";

                break;

            case "nowindicator":

                return "nowIndicator";

                break;

        }

        

        return $key;

        

    }

    

    /**

     * Check text time custom options

     * 

     * @param string $key

     * @return string

     */

    public function check_texttimecust($key) {

        

        switch ($key) {

            case "timeformat":

                return "timeFormat";

                break;

            case "columnformat":

                return "columnFormat";

                break;

            case "titleformat":

                return "titleFormat";

                break;

            case "columnformat":

                return "columnFormat";

                break;

            case "titleformat":

                return "titleFormat";

                break;

            case "buttontext":

                return "buttonText";

                break;

            case "monthnames":

                return "monthNames";

                break;

            case "monthnamesshort":

                return "monthNamesShort";

                break;

            case "daynames":

                return "dayNames";

                break;

            case "daynamesshort":

                return "dayNamesShort";

                break;

            case "weeknumbertitle":

                return "weekNumberTitle";

                break;

            case "displayeventtime":

                return "displayEventTime";

                break;

            case "displayeventend":

                return "displayEventEnd";

                break;

            case "eventlimittext":

                return "eventLimitText";

                break;

            case "daypopoverformat":

                return "dayPopoverFormat";

                break;

        }

        

        return $key;

        

    }

    

    /**

     * Check clicking hovering options

     * 

     * @param string $key

     * @return string

     */

    public function check_clickinghovering($key) {

        

        switch ($key) {

            case "navlinks":

                return "navLinks";

                break;

        }

        

        return $key;

        

    }

    

    /**

     * Check selection options

     * 

     * @param string $key

     * @return string

     */

    public function check_selection($key) {

        

        switch ($key) {

            case "selecthelper":

                return "selectHelper";

                break;

            case "unselectauto":

                return "unselectAuto";

                break;

            case "unselectcancel":

                return "unselectCancel";

                break;

            case "selectoverlap":

                return "selectOverlap";

                break;

            case "selectconstraint":

                return "selectConstraint";

                break;

            case "selectallow":

                return "selectAllow";

                break;

        }

        

        return $key;

        

    }

    

    /**

     * Check event data options

     * 

     * @param string $key

     * @return string

     */

    public function check_eventdata($key) {

        

        switch ($key) {

            case "eventsources":

                return "eventSources";

                break;

            case "alldaydefault":

                return "allDayDefault";

                break;

            case "unselectcancel":

                return "unselectCancel";

                break;

            case "startparam":

                return "startParam";

                break;

            case "endparam":

                return "endParam";

                break;

            case "timezoneparam":

                return "timezoneParam";

                break;

            case "lazyfetching":

                return "lazyFetching";

                break;

            case "defaulttimedeventduration":

                return "defaultTimedEventDuration";

                break;

            case "defaultalldayeventduration":

                return "defaultAllDayEventDuration";

                break;

            case "forceeventduration":

                return "forceEventDuration";

                break;

        }

        

        return $key;

        

    }

    

    /**

     * Check event rendering options

     * 

     * @param string $key

     * @return string

     */

    public function check_eventrendering($key) {

        

        switch ($key) {

            case "eventcolor":

                return "eventColor";

                break;

            case "eventbackgroundcolor":

                return "eventBackgroundColor";

                break;

            case "eventbordercolor":

                return "eventBorderColor";

                break;

            case "eventtextcolor":

                return "eventTextColor";

                break;

            case "nextdaythreshold":

                return "nextDayThreshold";

                break;

            case "eventorder":

                return "eventOrder";

                break;

        }

        

        return $key;

        

    }

    

    /**

     * Check timeline view options

     * 

     * @param string $key

     * @return string

     */

    public function check_timelineview($key) {

        

        switch ($key) {

            case "resourceareawidth":

                return "resourceAreaWidth";

                break;

            case "resourcelabeltext":

                return "resourceLabelText";

                break;

            case "resourcecolumns":

                return "resourceColumns";

                break;

            case "slotwidth":

                return "slotWidth";

                break;

            case "slotduration":

                return "slotDuration";

                break;

            case "slotlabelformat":

                return "slotLabelFormat";

                break;

            case "slotlabelinterval":

                return "slotLabelInterval";

                break;

            case "slotlabelinterval":

                return "slotLabelInterval";

                break;

            case "snapduration":

                return "snapDuration";

                break;

            case "snapduration":

                return "snapDuration";

                break;

            case "scrolltime":

                return "scrollTime";

                break;

        }

        

        return $key;

        

    }

    

    /**

     * Loads text-domain for localization

     * 

     */

    public function load_text_domain() {



        $path = dirname( plugin_basename( __FILE__ ) ) . '/languages/';

        $loaded = load_plugin_textdomain( 'fooevents-calendar', false, $path);

        

        /*if ( ! $loaded )

        {

            print "File not found: $path"; 

            exit;

        }*/

        

    }

    

    /**

    * Format array for the datepicker

    *

    * WordPress stores the locale information in an array with a alphanumeric index, and

    * the datepicker wants a numerical index. This function replaces the index with a number

    */

    private function _strip_array_indices($ArrayToStrip) {

        

        foreach( $ArrayToStrip as $objArrayItem) {

            $NewArray[] =  $objArrayItem;

        }



        return( $NewArray );

        

    }

    

    private function get_custom_post_types() {

        

        $post_types = get_post_types(); 

        

        

        

        unset($post_types['attachment']);

        unset($post_types['revision']);

        unset($post_types['nav_menu_item']);

        unset($post_types['custom_css']);

        unset($post_types['customize_changeset']);

        unset($post_types['oembed_cache']);

        unset($post_types['product']);

        unset($post_types['product_variation']);

        unset($post_types['shop_order']);

        unset($post_types['shop_order_refund']);

        unset($post_types['shop_coupon']);

        unset($post_types['event_magic_tickets']);

        unset($post_types['user_request']);

        unset($post_types['wp_block']);

        unset($post_types['scheduled-action']);

        unset($post_types['fe_eventbrite_event']);

        

        return $post_types;

        

    }

    

    /**

    * Convert the php date format string to a js date format

    */

   private function _date_format_php_to_js( $sFormat ) {

       

        switch( $sFormat ) {

            //Predefined WP date formats

            case 'jS F Y':

            return( 'd MM, yy' );

            break;

            case 'F j, Y':

            return( 'MM dd, yy' );

            break;

            case 'j F Y':

            return( 'd MM yy' );

            break;

            case 'Y/m/d':

            return( 'yy/mm/dd' );

            break;

            case 'm/d/Y':

            return( 'mm/dd/yy' );

            break;

            case 'd/m/Y':

            return( 'dd/mm/yy' );

            break;

            case 'Y-m-d':

            return( 'yy-mm-dd' );

            break;

            case 'm-d-Y':

            return( 'mm-dd-yy' );

            break;

            case 'd-m-Y':

            return( 'dd-mm-yy' );

            break;

            case 'j. FY':

            return( 'd. MMyy' );

            break;

        

            default:

            return( 'yy-mm-dd' );

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

    

    public function display_meta_errors() {

        

        if ( !session_id() ) {

            session_start();

        }

        

        if(!empty($_SESSION)) {

        

            if (array_key_exists( 'fooevents_calendar_errors', $_SESSION )) {



                echo '<div class="error">';

                foreach($_SESSION['fooevents_calendar_errors'] as $error) {

                    echo "<p>".$error."</p>";

                }

                echo '</div>';



            }

        

        }

        

        unset($_SESSION['fooevents_calendar_errors']);

        

    }

    

}



$FooEvents_Calendar = new FooEvents_Calendar();



function uninstall_fooeventscalendar() {

    

    delete_option('globalFooEventsAllDayEvent');

    delete_option('globalFooEventsTwentyFourHour');

    

}



register_uninstall_hook(__FILE__, 'uninstall_fooeventscalendar');