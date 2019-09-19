<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Plugin Name: FooEvents for WooCommerce
 * Description: Adds event and ticketing features to WooCommerce
 * Version: 1.9.7
 * Author: FooEvents
 * Plugin URI: https://www.fooevents.com/
 * Author URI: https://www.fooevents.com/
 * Developer: FooEvents
 * Developer URI: https://www.fooevents.com/
 * Text Domain: woocommerce-events
 *
 * Copyright: © 2009-2017 FooEvents.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

//include config
require(WP_PLUGIN_DIR.'/fooevents/config.php');

class FooEvents {

    private $WooHelper;
    private $ICSHelper;
    private $Config;
    private $XMLRPCHelper;
    private $CommsHelper;
    private $CheckoutHelper;
    private $TicketHelper;
    private $UpdateHelper;
    private $Salt;
    private $ThemeHelper;
    private $APIKey;
    private $pluginFile;
    private $slug;
    private $pluginData;
    
    public function __construct() {
        
        $plugin = plugin_basename(__FILE__); 
        
        $this->APIKey = get_option('globalWooCommerceEventsAPIKey', true);
        $this->pluginFile = __FILE__;

        add_action('init', array($this, 'plugin_init'));
        add_action('admin_init', array($this, 'register_scripts'));
        add_action('admin_notices', array($this, 'check_woocommerce_events'));
        add_action('admin_notices', array($this, 'check_fooevents_errors'));
        add_action('wp_enqueue_scripts', array($this, 'register_scripts_frontend'));
        add_action('admin_init', array($this, 'register_styles'));
        add_action('admin_menu', array($this, 'add_woocommerce_submenu'));
        add_action('woocommerce_settings_tabs_settings_woocommerce_events', array($this, 'add_settings_tab_settings'));
        add_action('woocommerce_update_options_settings_woocommerce_events', array($this, 'update_settings_tab_settings'));
        add_action('wp_ajax_fooevents_ics', array($this, 'fooevents_ics'));
        add_action('wp_ajax_nopriv_fooevents_ics', array($this, 'fooevents_ics'));
        add_action('plugins_loaded', array($this, 'load_text_domain'));
        
        add_action('activated_plugin', array($this, 'activate_plugin'));
        add_action('wpml_loaded', array($this, 'fooevents_wpml_loaded'));
        
        add_action('wp_ajax_woocommerce_events_cancel', array($this, 'woocommerce_events_cancel'));
        add_action('wp_ajax_nopriv_woocommerce_events_cancel', array($this, 'woocommerce_events_cancel'));
        
        add_filter('woocommerce_settings_tabs_array', array($this, 'add_settings_tab'));
        add_filter('plugin_action_links_'.$plugin, array($this, 'add_plugin_links'));
        add_filter('add_to_cart_text', array($this, 'woo_custom_cart_button_text'));
        add_filter('woocommerce_product_single_add_to_cart_text', array($this, 'woo_custom_cart_button_text'));
        add_filter('woocommerce_product_add_to_cart_text', array($this, 'woo_custom_cart_button_text'));

        add_action('admin_init', array(&$this, 'assign_admin_caps'));
        register_deactivation_hook( __FILE__, array(&$this, 'remove_event_user_caps'));
        
    }
    
    /**
     * Basic checks to see if FooEvents will run correctly. 
     * 
     */
    public function check_woocommerce_events() {

        if ( $this->is_plugin_active( 'woocommerce_events/woocommerce-events.php' ) ) {

                $this->output_notices(array(__( 'WooCommerce Events has re-branded to FooEvents. Please disable and remove the older WooCommerce Events plugin.', 'woocommerce-events' )));

        } 

        if(!is_writable($this->Config->uploadsPath)) {

            $this->output_notices(array(sprintf(__( 'Directory %s is not writeable', 'woocommerce-events' ), $this->Config->uploadsPath)));
            
            if(!is_writable($this->Config->barcodePath)) {
                
                $this->output_notices(array(sprintf(__( 'Directory %s is not writeable', 'woocommerce-events' ), $this->Config->barcodePath)));

            }
            
            if(!is_writable($this->Config->themePacksPath)) {
                
                $this->output_notices(array(sprintf(__( 'Directory %s is not writeable', 'woocommerce-events' ), $this->Config->themePacksPath)));

            }
            
            if(!is_writable($this->Config->themePacksPath.'default')) {
                
                $this->output_notices(array(sprintf(__( 'Directory %s is not writeable', 'woocommerce-events' ), $this->Config->themePacksPath.'default')));

            }
            
        }
        
        if(file_exists($this->Config->emailTemplatePathTheme.'email/header.php') || file_exists($this->Config->emailTemplatePathTheme.'email/footer.php') || file_exists($this->Config->emailTemplatePathTheme.'email/ticket.php') || file_exists($this->Config->emailTemplatePathTheme.'email/tickets.php')) {

            $this->output_notices(array(sprintf(__( 'We have detected that you have overridden FooEvents ticket template files in your Wordpress theme. Please move these to an overridden ticket theme directory. Please consult the FooEvents documentation on how to do this.', 'woocommerce-events' ), $this->Config->themePacksPath.'default')));

        } 

    }
    
    
    /**
     * Checks for and displays FooEvents errors. 
     * 
     */
    public function check_fooevents_errors() {
        
        $errorCodes = array(
            '1' => __('Purchaser username already used. Ticket was not created', 'woocommerce-events'),
            '2' => __('An error occured. Ticket was not created', 'woocommerce-events'),
            '3' => __('Purchaser email address already used. Ticket was not created', 'woocommerce-events'),
        );
        
        if(!empty($_GET['fooevents_error'])) {
            
            $this->output_notices(array($errorCodes[$_GET['fooevents_error']]));
            
        }
        
    }

    /**
     *  Initialize events plugin and helpers.
     * 
     */
    public function plugin_init() {

        //Main config
        $this->Config = new FooEvents_Config();

        //WooHelper
        require_once($this->Config->classPath.'woohelper.php');
        $this->WooHelper = new FooEvents_Woo_Helper($this->Config);
        
        //ICSHelper
        require_once($this->Config->classPath.'icshelper.php');
        $this->ICSHelper = new FooEvents_ICS_helper($this->Config);
        
        //CommsHelper
        require_once($this->Config->classPath.'commshelper.php');
        $this->CommsHelper = new FooEvents_Comms_Helper($this->Config);
        
        //XMLRPCHelper
        require_once($this->Config->classPath.'xmlrpchelper.php');
        $this->XMLRPCHelper = new FooEvents_XMLRPC_Helper($this->Config);
        
        //CheckoutHelper
        require_once($this->Config->classPath.'checkouthelper.php');
        $this->CheckoutHelper = new FooEvents_Checkout_Helper($this->Config);

        //ThemeHelper
        require_once($this->Config->classPath.'themehelper.php');
        $this->ThemeHelper = new FooEvents_Theme_Helper($this->Config);
        
        //BarcodeHelper
        require_once($this->Config->classPath.'barcodehelper.php');
        $this->BarcodeHelper = new FooEvents_Barcode_Helper($this->Config);

        //UpdateHelper
        require_once($this->Config->classPath.'updatehelper.php');
        $this->UpdateHelper = new FooEvents_Update_Helper($this->Config);

        $this->Salt = $this->Config->salt;
        
        if(empty($this->Salt)) {
            
            $salt = rand(111111,999999); 
            update_option('woocommerce_events_do_salt', $salt);
            $this->Salt = $salt;
            $this->Config->salt = $salt;
        }

        if (!file_exists($this->Config->uploadsPath) && is_writable($this->Config->uploadsDirPath)) {

            if (!mkdir($this->Config->uploadsPath, 0755, true)) {

                $this->output_notices(array(sprintf(__( 'FooEvents failed to create the directory %s please manually create this directory on your server.', 'woocommerce-events' ), $this->Config->uploadsPath)));

            }
            
            if (!file_exists($this->Config->barcodePath)) {

                if(!mkdir($this->Config->barcodePath, 0755, true)) {
                    
                    $this->output_notices(array(sprintf(__( 'FooEvents failed to create the directory %s please manually create this directory on your server.', 'woocommerce-events' ), $this->Config->barcodePath)));
                    
                }

            }

            if (!file_exists($this->Config->themePacksPath)) {

                if(!mkdir($this->Config->themePacksPath, 0755, true)) {
                    
                    $this->output_notices(array(sprintf(__( 'FooEvents failed to create the directory %s please manually create this directory on your server.', 'woocommerce-events' ), $this->Config->themePacksPath)));
                    
                }

            }
            
            if (!file_exists($this->Config->pdfTicketPath)) {

                if(!mkdir($this->Config->pdfTicketPath, 0755, true)) {
                    
                    $this->output_notices(array(sprintf(__( 'FooEvents failed to create the directory %s please manually create this directory on your server.', 'woocommerce-events' ), $this->Config->pdfTicketPath)));

                }

            }
            
            
            if(!file_exists($this->Config->uploadsPath.'themes/default') && is_writable($this->Config->uploadsDirPath)) {

               $this->xcopy($this->Config->emailTemplatePath, $this->Config->themePacksPath.'default');

            }

            //generate barcode
            if (!file_exists($this->Config->barcodePath.'/111111111.png')) {

                $this->BarcodeHelper->generate_barcode('111111111');

            }
 
        }

    }
    
    /**
     * When WPML is loaded
     * 
     */
    public function fooevents_wpml_loaded() {
        
        add_action('pre_get_posts', array($this, 'fooevents_wpml_compatibility'));
        
    }
    
    /**
     * WPML compatibility for events within app
     * 
     */
    public function fooevents_wpml_compatibility($wp_query) {
        
        $q = $wp_query->query_vars;
        
        if(!empty($_GET['action']) && $_GET['action'] == "woocommerce_events_csv") {
            
            return;
            
        }
        
        if (isset($q['meta_query']) && isset($q['post_type']) && in_array('event_magic_tickets', (array) $q['post_type'])) {

            foreach ( (array) $q['meta_query'] as $i => $meta_query ) {

                if ( $meta_query['key'] === 'WooCommerceEventsProductID' && is_numeric( $meta_query['value'] ) ) {

                        $trid = apply_filters( 'wpml_element_trid', null, $meta_query['value'], 'post_event_magic_tickets' );
                        $values = apply_filters( 'wpml_get_element_translations', null, $trid, 'post_event_magic_tickets' );
                        $q['meta_query'][ $i ]['value'] = wp_list_pluck( $values, 'element_id' );

                        $wp_query->query_vars = $q;

                }

            }

        }

    }
    
    /**
     * Register plugin scripts.
     * 
     */
    public function register_scripts() {
        
        global $wp_locale;
        
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_script('woocommerce-events-admin-script', $this->Config->scriptsPath . 'events-admin.js', array('jquery', 'jquery-ui-datepicker', 'wp-color-picker'), '1.1.0', true );

        $localArgs = array(
            'closeText'         => __( 'Done', 'woocommerce-events' ),
            'currentText'       => __( 'Today', 'woocommerce-events' ),
            'monthNames'        => $this->_strip_array_indices( $wp_locale->month ),
            'monthNamesShort'   => $this->_strip_array_indices( $wp_locale->month_abbrev ),
            'monthStatus'       => __( 'Show a different month', 'woocommerce-events' ),
            'dayNames'          => $this->_strip_array_indices( $wp_locale->weekday ),
            'dayNamesShort'     => $this->_strip_array_indices( $wp_locale->weekday_abbrev ),
            'dayNamesMin'       => $this->_strip_array_indices( $wp_locale->weekday_initial ),
            // set the date format to match the WP general date settings
            'dateFormat'        => $this->_date_format_php_to_js( get_option( 'date_format' ) ),
            // get the start of week from WP general setting
            'firstDay'          => get_option( 'start_of_week' ),
            // is Right to left language? default is false
            'isRTL'             => $wp_locale->is_rtl(),
        );
        
        wp_localize_script( 'woocommerce-events-admin-script', 'localObj', $localArgs );
        
    }
    
    /**
     * Registers scripts on the Wordpress frontend.
     * 
     */
    public function register_scripts_frontend() {

        wp_enqueue_script('woocommerce-events-front-script',  $this->Config->scriptsPath . 'events-frontend.js', array('jquery'), '1.0.0', true);
        
    }

    /**
     * Register plugin styles.
     * 
     */
    public function register_styles() {

        wp_enqueue_style('woocommerce-events-admin-script',  $this->Config->stylesPath . 'events-admin.css', array(), '1.0.0');
        wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
        
        wp_enqueue_style('wp-color-picker');

    }

    /**
     * Assign FooEvents user permissions to the admin user
     * 
     */    
    public function assign_admin_caps() {
        
        $role = get_role( 'administrator' );
        
        $role->add_cap('publish_event_magic_tickets'); 
        $role->add_cap('edit_event_magic_tickets'); 
        $role->add_cap('edit_published_event_magic_tickets'); 
        $role->add_cap('edit_others_event_magic_tickets'); 
        $role->add_cap('delete_event_magic_tickets'); 
        $role->add_cap('delete_others_event_magic_tickets'); 
        $role->add_cap('read_private_event_magic_tickets'); 
        $role->add_cap('edit_event_magic_ticket'); 
        $role->add_cap('delete_event_magic_ticket'); 
        $role->add_cap('read_event_magic_ticket'); 
        $role->add_cap('edit_published_event_magic_ticket'); 
        $role->add_cap('publish_event_magic_ticket'); 
        $role->add_cap('delete_others_event_magic_ticket'); 
        $role->add_cap('delete_published_event_magic_ticket'); 
        $role->add_cap('delete_published_event_magic_tickets'); 
        
    }
    
    /**
     * Removes FooEvents user permissions when plugin is disabled 
     * 
     */
    public function remove_event_user_caps() {
            
        $delete_caps = array(
            'publish_event_magic_tickets', 
            'edit_event_magic_tickets', 
            'edit_published_event_magic_tickets', 
            'edit_others_event_magic_tickets', 
            'delete_event_magic_tickets', 
            'delete_others_event_magic_tickets', 
            'read_private_event_magic_tickets', 
            'edit_event_magic_ticket', 
            'delete_event_magic_ticket', 
            'read_event_magic_ticket', 
            'edit_published_event_magic_ticket', 
            'publish_event_magic_ticket', 
            'delete_others_event_magic_ticket', 
            'delete_published_event_magic_ticket', 
            'delete_published_event_magic_tickets', 
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
     * xcopy function to move templates to new location in uploads directory
     * 
     */
    private function xcopy($source, $dest, $permissions = 0755)
    {
  
        if (is_link($source)) {
            
            return symlink(readlink($source), $dest);
            
        }

        if (is_file($source)) {
            
            return copy($source, $dest);
            
        }

        if (!is_dir($dest)) {
            
            mkdir($dest, $permissions);
            
        }

        $dir = dir($source);
        while (false !== $entry = $dir->read()) {

            if ($entry == '.' || $entry == '..') {
                continue;
            }

            $this->xcopy("$source/$entry", "$dest/$entry", $permissions);
        }

        $dir->close();
        return true;
    }
    
    /**
     * Adds option to for redirect.
     * 
     */
    static function activate_plugin($plugin) {

        $salt = rand(111111,999999); 
        update_option('woocommerce_events_do_salt', $salt);
 
        if( $plugin == plugin_basename( __FILE__ ) ) {

            wp_redirect('admin.php?page=woocommerce-events-help'); exit;
            
        }    

    }

    /**
     * Adds a settings tab to WooCommerce
     * 
     * @param array $settings_tabs
     */
    public function add_settings_tab($settings_tabs) {

        $settings_tabs['settings_woocommerce_events'] = __( 'Events', 'woocommerce-settings-woocommerce-events' );
        return $settings_tabs;


    }

    /**
     * Adds the WooCommerce tab settings
     * 
     */
    public function add_settings_tab_settings() {

        woocommerce_admin_fields( $this->get_tab_settings() );

    }

    /**
     * Gets the WooCommerce tab settings
     * 
     * @return array $settings
     */
    public function get_tab_settings() {
        
        $settings = array(
            'section_start' => array(
                'type' => 'sectionstart',
                'id' => 'wc_settings_tab_demo_section_start'
            ),
            'section_title' => array(
                'name'      => __( 'Event Settings', 'woocommerce-events' ),
                'type'      => 'title',
                'desc'      => '',
                'id'        => 'wc_settings_fooevents_settings_title'
            ),
            'globalWooCommerceEventsAPIKey' => array(
                'name'  => __( 'FooEvents API key', 'woocommerce-events' ),
                'type'  => 'text',
                'id'    => 'globalWooCommerceEventsAPIKey',
                'desc'  => __( 'Required for auto plugin updates (leave empty if plugin purchased on CodeCanyon.net)', 'woocommerce-events' )
            ),
            'globalWooCommerceEnvatoAPIKey' => array(
                'name'  => __( 'Envato purchase code', 'woocommerce-events' ),
                'type'  => 'text',
                'id'    => 'globalWooCommerceEnvatoAPIKey',
                'desc'  => __( 'Required for auto plugin updates (leave empty if plugin purchased on FooEvents.com)', 'woocommerce-events' )
            ),
            'globalWooCommerceEventsGoogleMapsAPIKey' => array(
                'name'  => __( 'Google Maps API key', 'woocommerce-events' ),
                'type'  => 'text',
                'id'    => 'globalWooCommerceEventsGoogleMapsAPIKey',
                'desc'  => __( 'Enable Google Maps on product page.', 'woocommerce-events' )
            ),
            'globalWooCommerceEventsTicketBackgroundColor' => array(
                'name'      => __( 'Global ticket border', 'woocommerce-events' ),
                'type'      => 'text',
                'id'        => 'globalWooCommerceEventsTicketBackgroundColor',
                'class'     => 'color-field'
            ),
            'globalWooCommerceEventsTicketButtonColor' => array(
                'name'      => __( 'Global ticket button', 'woocommerce-events' ),
                'type'      => 'text',
                'id'        => 'globalWooCommerceEventsTicketButtonColor',
                'class'     => 'color-field'
            ),
            'globalWooCommerceEventsTicketTextColor' => array(
                'name'      => __( 'Global ticket button text', 'woocommerce-events' ),
                'type'      => 'text',
                'id'        => 'globalWooCommerceEventsTicketTextColor',
                'class'     => 'color-field'
            ),
            'globalWooCommerceEventsTicketLogo' => array(
                'name'      => __( 'Global ticket logo', 'woocommerce-events' ),
                'type'      => 'text',
                'id'        => 'globalWooCommerceEventsTicketLogo',
                'desc'      => __( 'URL to ticket logo file', 'woocommerce-events' ),
                'class' => 'text uploadfield'
            ),
	    'globalWooCommerceEventsTicketHeaderImage' => array(
                'name'      => __( 'Global ticket header image', 'woocommerce-events' ),
                'type'      => 'text',
                'id'        => 'globalWooCommerceEventsTicketHeaderImage',
                'desc'      => __( 'URL to ticket main header file', 'woocommerce-events' ),
                'class' => 'text uploadfield'
            ),
            'globalWooCommerceEventsChangeAddToCart' => array(
                'name'  => __( 'Change add to cart text', 'woocommerce-events' ),
                'type'  => 'checkbox',
                'id'    => 'globalWooCommerceEventsChangeAddToCart',
                'desc'  => __( 'Changes "Add to cart" to "Book ticket" for event products', 'woocommerce-events' ),
                'class' => 'text uploadfield'
            ),
            'globalWooCommerceHideEventDetailsTab' => array(
                'name'      => __( 'Hide event details tab', 'woocommerce-events' ),
                'type'      => 'checkbox',
                'id'        => 'globalWooCommerceHideEventDetailsTab',
                'desc'      => __( 'Hides the event details tab on the product page', 'woocommerce-events' ),
                'class'     => 'text uploadfield' 
            ),
            'globalWooCommerceUsePlaceHolders' => array(
                'name'      => __( 'Use placeholders on checkout form', 'woocommerce-events' ),
                'type'      => 'checkbox',
                'id'        => 'globalWooCommerceUsePlaceHolders',
                'desc'      => __( "Displays the form place holders. Useful for themes that don't support form labels.", 'woocommerce-events' ),
                'class'     => 'text uploadfield' 
            ),
           'globalWooCommerceHideUnpaidTicketsApp' => array(
                'name'      => __( 'Hide unpaid tickets in app', 'woocommerce-events' ),
                'type'      => 'checkbox',
                'id'        => 'globalWooCommerceHideUnpaidTicketsApp',
                'desc'      => __( 'Hides the unpaid tickets in the iOS and Android apps', 'woocommerce-events' ),
                'class'     => 'text uploadfield' 
            ), 
            'globalWooCommerceEventsHideUnpaidTickets' => array(
                'name'  => __( 'Hide unpaid tickets', 'woocommerce-events' ),
                'type'  => 'checkbox',
                'id'    => 'globalWooCommerceEventsHideUnpaidTickets',
                'desc'  => __( 'Hides unpaid tickets in ticket admin', 'woocommerce-events' )
            ), 
            'globalWooCommerceEventsEmailTicketAdmin' => array(
                'name'  => __( 'Email copy of ticket to admin', 'woocommerce-events' ),
                'type'  => 'checkbox',
                'id'    => 'globalWooCommerceEventsEmailTicketAdmin',
                'desc'  => __( 'Sends admin a ticket copy', 'woocommerce-events' )
            ),  
            'section_end' => array(
                'type' => 'sectionend',
                'id' => 'wc_settings_tab_demo_section_end'
            ),
            'app_settings_section_start' => array(
                'type' => 'sectionstart',
                'id' => 'wc_settings_tab_app_settings_section_start'
            ),
            'app_settings_section_title' => array(
                'name' => __( 'Pro App Settings', 'woocommerce-events' ),
                'type' => 'title',
                'desc' => 'The following settings can be used to customize the appearance of the FooEvents Check-ins Pro app once you have logged in. You will find more information on the pro app and a download link here: <a href="http://www.fooevents.com/apps/pro" target="_blank">http://www.fooevents.com/apps/pro</a>',
                'id' => 'wc_settings_woocommerce_events_app_settings_section_title'
            ),
            'globalWooCommerceEventsAppLogo' => array(
                'name' => __( 'App logo', 'woocommerce-events' ),
                'type' => 'text',
                'id' => 'globalWooCommerceEventsAppLogo',
                'desc' => __( 'URL to the image that will display on the app\'s sign-in screen. A PNG image with transparency and a width of around 940px is recommended', 'woocommerce-events' ),
                'class' => 'text uploadfield'
            ),
            'globalWooCommerceEventsAppColor' => array(
                'name' => __( 'Color', 'woocommerce-events' ),
                'type' => 'text',
                'id' => 'globalWooCommerceEventsAppColor',
                'desc' => __( 'Used for the app\'s top navigation bar and sign-in button', 'woocommerce-events' ),
                'class' => 'color-field'
            ),
            'globalWooCommerceEventsAppTextColor' => array(
                'name' => __( 'Text color', 'woocommerce-events' ),
                'type' => 'text',
                'id' => 'globalWooCommerceEventsAppTextColor',
                'desc' => __( 'Used for the text in the app\'s top navigation bar and sign-in button', 'woocommerce-events' ),
                'class' => 'color-field'
            ),
            'globalWooCommerceEventsAppBackgroundColor' => array(
                'name' => __( 'Background color', 'woocommerce-events' ),
                'type' => 'text',
                'id' => 'globalWooCommerceEventsAppBackgroundColor',
                'desc' => __( 'Used for the sign-in screen\'s background', 'woocommerce-events' ),
                'class' => 'color-field'
            ),
            'globalWooCommerceEventsAppSignInTextColor' => array(
                'name' => __( 'Sign-in screen text color', 'woocommerce-events' ),
                'type' => 'text',
                'id' => 'globalWooCommerceEventsAppSignInTextColor',
                'desc' => __( 'Used for the text beneath the logo on the app\'s sign-in screen', 'woocommerce-events' ),
                'class' => 'color-field'
            ),
            'app_settings_section_end' => array(
                'type' => 'sectionend',
                'id' => 'wc_settings_tab_app_settings_section_end'
            ),
            'term_settings_section_start' => array(
                'type' => 'sectionstart',
                'id' => 'wc_settings_tab_app_settings_section_start'
            ),
            'term_settings_section_title' => array(
                'name' => __( 'Override Terminology', 'woocommerce-events' ),
                'type' => 'title',
                'desc' => 'Terminology settings can be used to override FooEvents wording',
                'id' => 'wc_settings_woocommerce_events_term_settings_section_title'
            ),
            'globalWooCommerceEventsAttendeeOverride' => array(
                'name' => __( 'Attendee', 'woocommerce-events' ),
                'type' => 'text',
                'id' => 'globalWooCommerceEventsAttendeeOverride',
            ),   
            'globalWooCommerceEventsTicketOverride' => array(
                'name' => __( 'Book ticket', 'woocommerce-events' ),
                'type' => 'text',
                'id' => 'globalWooCommerceEventsTicketOverride',
            ),
            'WooCommerceEventsDayOverride' => array(
                'name' => __( 'Day', 'woocommerce-events' ),
                'type' => 'text',
                'id' => 'WooCommerceEventsDayOverride',
            ),
            'term_settings_section_end' => array(
                'type' => 'sectionend',
                'id' => 'wc_settings_tab_app_settings_section_end'
            )
        );
        
        if($this->Config->clientMode === true) {
            $settings['globalWooCommerceEventsChangeCanceledMessage'] = array(
                'name'  => __( 'Canceled ticket message', 'woocommerce-events' ),
                'type'  => 'textarea',
                'id'    => 'globalWooCommerceEventsCanceledTicketMessage',
                'default' => __( 'Your ticket has been canceled.', 'woocommerce-events' ),
                'class' => 'text uploadfield'
            );
        }

        $settings['section_end'] = array(
            'type' => 'sectionend',
            'id' => 'wc_settings_fooevents_settings_end'
        );

        return $settings;
    }

    /**
     * Saves the WooCommerce tab settings
     * 
     */
    public function update_settings_tab_settings() {

        woocommerce_update_options( $this->get_tab_settings() );

    }
    
    /**
     * Adds the WooCommerce sub menu
     * 
     */
    public function add_woocommerce_submenu() {

        add_submenu_page( 'null',__( 'FooEvents Introduction', 'woocommerce-events' ), __( 'FooEvents Introduction', 'woocommerce-events' ), 'manage_options', 'woocommerce-events-help', array($this, 'add_woocommerce_submenu_page') ); 

    }
    
    /**
     * Adds the WooCommerce sub menu page
     * 
     */
    public function add_woocommerce_submenu_page() {
        
        require($this->Config->templatePath.'pluginintroduction.php');

    }
    
    /**
     * Adds plugin links to the plugins page
     * 
     * @param array $links
     * @return array $links
     */
    public function add_plugin_links($links) {
        
        $linkSettings = '<a href="admin.php?page=wc-settings&tab=settings_woocommerce_events">'.__( 'Settings', 'woocommerce-events' ).'</a>'; 
        array_unshift($links, $linkSettings); 
        
        $linkIntroduction = '<a href="admin.php?page=woocommerce-events-help">'.__( 'Introduction', 'woocommerce-events' ).'</a>'; 
        array_unshift($links, $linkIntroduction); 
        
        return $links;
        
    }
    
    /**
     * Builds the calendar ICS file
     * 
     */
    public function fooevents_ics() {
        
        error_reporting(E_ALL);
        ini_set('display_errors', '1');

        $event = sanitize_text_field($_GET['event']);
                
        $post = get_post($event);
        
        $WooCommerceEventsEvent         = get_post_meta($event, 'WooCommerceEventsEvent', true);
        $WooCommerceEventsDate          = get_post_meta($event, 'WooCommerceEventsDate', true);
        $WooCommerceEventsHour          = get_post_meta($event, 'WooCommerceEventsHour', true);
        $WooCommerceEventsMinutes       = get_post_meta($event, 'WooCommerceEventsMinutes', true);
        $WooCommerceEventsPeriod        = get_post_meta($event, 'WooCommerceEventsPeriod', true);
        $WooCommerceEventsHourEnd       = get_post_meta($event, 'WooCommerceEventsHourEnd', true);
        $WooCommerceEventsMinutesEnd    = get_post_meta($event, 'WooCommerceEventsMinutesEnd', true);
        $WooCommerceEventsLocation      = get_post_meta($event, 'WooCommerceEventsLocation', true);
        $WooCommerceEventsEndPeriod     = get_post_meta($event, 'WooCommerceEventsEndPeriod', true);

        $WooCommerceEventsDate = str_replace('/', '-', $WooCommerceEventsDate);
        $WooCommerceEventsDate = str_replace(',', '', $WooCommerceEventsDate);
        
        $WooCommerceEventsPeriod = strtoupper(str_replace('.', '', $WooCommerceEventsPeriod));
        $WooCommerceEventsEndPeriod = strtoupper(str_replace('.', '', $WooCommerceEventsEndPeriod));
        
        if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
            require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
        }
        
        $multiDayType = '';
        
        if ($this->is_plugin_active('fooevents_multi_day/fooevents-multi-day.php') || is_plugin_active_for_network('fooevents_multi_day/fooevents-multi-day.php')) {
            
            $Fooevents_Multiday_Events = new Fooevents_Multiday_Events();
            $multiDayType = $Fooevents_Multiday_Events->get_multi_day_type($event);

        }
        
        if($multiDayType == 'select') {
            
            $multiDayDates = $Fooevents_Multiday_Events->get_multi_day_selected_dates($event);
            
            foreach($multiDayDates as $dayDate) {
                
                $startDate = date("Y-m-d H:i:s", strtotime($dayDate." ".$WooCommerceEventsHour.':'.$WooCommerceEventsMinutes.' '.$WooCommerceEventsPeriod));
                $endDate = date("Y-m-d H:i:s", strtotime($dayDate." ".$WooCommerceEventsHourEnd.':'.$WooCommerceEventsMinutesEnd.' '.$WooCommerceEventsEndPeriod));
                
                $this->ICSHelper->build_ICS($startDate, $endDate,$post->post_title, get_bloginfo('name'), $WooCommerceEventsLocation); 
                
            }
            
        } else {
            
            $startDate = date("Y-m-d H:i:s", strtotime($WooCommerceEventsDate." ".$WooCommerceEventsHour.':'.$WooCommerceEventsMinutes.' '.$WooCommerceEventsPeriod));
            $endDate = date("Y-m-d H:i:s", strtotime($WooCommerceEventsDate." ".$WooCommerceEventsHourEnd.':'.$WooCommerceEventsMinutesEnd.' '.$WooCommerceEventsEndPeriod));

            $this->ICSHelper->build_ICS($startDate, $endDate,$post->post_title, get_bloginfo('name'), $WooCommerceEventsLocation); 
            
        }

        
        $this->ICSHelper->show();
        
        exit();
    }

    /**
     * Changes the WooCommerce 'Add to cart' text
     * 
     */
    public function woo_custom_cart_button_text($text) {
        
        global $post;
        global $product;

        $WooCommerceEventsEvent                         = get_post_meta($post->ID, 'WooCommerceEventsEvent', true);
        $globalWooCommerceEventsChangeAddToCart         = get_option('globalWooCommerceEventsChangeAddToCart', true);
        $ticketTerm                                     = get_post_meta($post->ID, 'WooCommerceEventsTicketOverride', true);
        
        if(empty($ticketTerm)) {

            $ticketTerm = get_option('globalWooCommerceEventsTicketOverride', true);

        }
        
        if(empty($ticketTerm) || $ticketTerm == 1) {

            $ticketTerm = __( 'Book ticket', 'woocommerce-events' );

        }

        if($WooCommerceEventsEvent == 'Event' && $globalWooCommerceEventsChangeAddToCart === 'yes') {
        
            return $ticketTerm;
        
        } else {
            
            return $text;
            
        }
        
    }
    
    /**
     * External access to ticket data
     * 
     * @param int $ticketID
     * @return array
     */
    public function get_ticket_data($ticketID) {
        
        //Main config
        $this->Config = new FooEvents_Config();
        
        //TicketHelper
        require_once($this->Config->classPath.'tickethelper.php');
        $this->TicketHelper = new FooEvents_Ticket_Helper($this->Config);
        
        $ticket_data = $this->TicketHelper->get_ticket_data($ticketID);
        
        return $ticket_data;
        
    }
    
    /**
     * Returns the plugin path
     * 
     * @return string
     */
    public function get_plugin_path() {
        
        return $this->Config->path;
        
    }
    
    /**
     * Returns the plugin URL
     * 
     * @return string
     */
    public function get_plugin_url() {
        
        return $this->Config->eventPluginURL;
        
    }
    
    /**
     * Returns the barcode path
     * 
     * @return string
     */
    public function get_barcode_path() {
        
        return $this->Config->barcodePath;
        
    }
    
    /**
     * Loads text-domain for localization
     * 
     */
    public function load_text_domain() {
        
        $path = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
        $loaded = load_plugin_textdomain( 'woocommerce-events', false, $path);
        
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
    private function _strip_array_indices( $ArrayToStrip ) {
        
        foreach( $ArrayToStrip as $objArrayItem) {
            $NewArray[] =  $objArrayItem;
        }

        return( $NewArray );
        
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
     * Returns if a plugin is active or not
     * 
     * @param string $plugin
     * @return bool
     */
    private function is_plugin_active( $plugin ) {

        return in_array( $plugin, (array) get_option( 'active_plugins', array() ) );

    }

}

$FooEvents = new FooEvents();

//TODO: move this function into WooHelper
function fooevents_displayEventTab() {
    
    global $post;
    $Config = new FooEvents_Config();
    
    $WooCommerceEventsEventDetailsText  = get_post_meta($post->ID, 'WooCommerceEventsEventDetailsText', true);
    $WooCommerceEventsEvent             = get_post_meta($post->ID, 'WooCommerceEventsEvent', true);
    $WooCommerceEventsDate              = get_post_meta($post->ID, 'WooCommerceEventsDate', true);
    $WooCommerceEventsEndDate           = get_post_meta($post->ID, 'WooCommerceEventsEndDate', true);
    $WooCommerceEventsHour              = get_post_meta($post->ID, 'WooCommerceEventsHour', true);
    $WooCommerceEventsMinutes           = get_post_meta($post->ID, 'WooCommerceEventsMinutes', true);
    $WooCommerceEventsPeriod            = get_post_meta($post->ID, 'WooCommerceEventsPeriod', true);
    $WooCommerceEventsHourEnd           = get_post_meta($post->ID, 'WooCommerceEventsHourEnd', true);
    $WooCommerceEventsMinutesEnd        = get_post_meta($post->ID, 'WooCommerceEventsMinutesEnd', true);
    $WooCommerceEventsEndPeriod         = get_post_meta($post->ID, 'WooCommerceEventsEndPeriod', true);
    $WooCommerceEventsLocation          = get_post_meta($post->ID, 'WooCommerceEventsLocation', true);
    $WooCommerceEventsTicketLogo        = get_post_meta($post->ID, 'WooCommerceEventsTicketLogo', true);
    $WooCommerceEventsSupportContact    = get_post_meta($post->ID, 'WooCommerceEventsSupportContact', true);
    $WooCommerceEventsGPS               = get_post_meta($post->ID, 'WooCommerceEventsGPS', true);
    $WooCommerceEventsDirections        = get_post_meta($post->ID, 'WooCommerceEventsDirections', true);
    $WooCommerceEventsEmail             = get_post_meta($post->ID, 'WooCommerceEventsEmail', true);
    $WooCommerceEventsMultiDayType      = get_post_meta($post->ID, 'WooCommerceEventsMultiDayType', true);
    $WooCommerceEventsSelectDate        = get_post_meta($post->ID, 'WooCommerceEventsSelectDate', true);
    
    $dayTerm = get_post_meta($post->ID, 'WooCommerceEventsDayOverride', true);

    if(empty($dayTerm)) {

        $dayTerm = get_option('WooCommerceEventsDayOverride', true);

    }

    if(empty($dayTerm) || $dayTerm == 1) {

        $dayTerm = __('Day', 'woocommerce-events');

    }
    
    $multiDayEvent = false;
    
    if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
        require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
    }

    if (fooevents_check_plugin_active('fooevents_multi_day/fooevents-multi-day.php') || is_plugin_active_for_network('fooevents_multi_day/fooevents-multi-day.php')) {
        
        $multiDayEvent = true;
        
    }
    
    if(file_exists($Config->emailTemplatePathTheme.'eventtab.php') ) {
        
        require($Config->emailTemplatePathTheme.'eventtab.php');
    
    } else {
        
        require($Config->templatePath.'eventtab.php');
        
    }
    
}

function fooevents_displayEventTabMap() {
    
    global $post;
    $Config = new FooEvents_Config();
    
    $WooCommerceEventsGoogleMaps = get_post_meta($post->ID, 'WooCommerceEventsGoogleMaps', true);
    $globalWooCommerceEventsGoogleMapsAPIKey = get_option('globalWooCommerceEventsGoogleMapsAPIKey', true);
    
    if($globalWooCommerceEventsGoogleMapsAPIKey == 1) {
        
        $globalWooCommerceEventsGoogleMapsAPIKey = '';
        
    }

    $eventContent = $post->post_content;
    
    $eventContent = apply_filters( 'the_content', $eventContent );
    
    if(!empty($WooCommerceEventsGoogleMaps) && !empty($globalWooCommerceEventsGoogleMapsAPIKey)) {
        
        if(file_exists($Config->emailTemplatePathTheme.'eventtabmap.php') ) {

            require($Config->emailTemplatePathTheme.'eventtabmap.php');

        } else {

            require($Config->templatePath.'eventtabmap.php');

        }
        
    }
    
}

function fooevents_ics() {
    
    $Config = new FooEvents_Config();
    
    
}

add_action( 'wp_dashboard_setup', 'fooevents_dashboard_widget' );

function fooevents_dashboard_widget() {
    
    wp_add_dashboard_widget(
        'fooevents_widget',
        'FooEvents',
        'fooevents_widget_display'
    ); 
    
    
}

function fooevents_widget_display() {
    
    $events_query = new WP_Query( array('post_type' => array('event_magic_tickets'), 'posts_per_page' => -1, 'meta_query' => array( array( 'key' => 'WooCommerceEventsStatus', 'value' => 'Unpaid', 'compare' => '!=' ) )) );
    $events = $events_query->get_posts();
    $ticket_count = $events_query->found_posts;
    
    $events_query = new WP_Query( array('post_type' => array('event_magic_tickets'), 'posts_per_page' => -1, 'meta_query' => array( array( 'key' => 'WooCommerceEventsStatus', 'value' => 'Not Checked In', 'compare' => '=' ) )) );
    $events = $events_query->get_posts();
    $not_checked_in_count = $events_query->found_posts;
    
    $events_query = new WP_Query( array('post_type' => array('event_magic_tickets'), 'posts_per_page' => -1, 'meta_query' => array( array( 'key' => 'WooCommerceEventsStatus', 'value' => 'Checked In', 'compare' => '=' ) )) );
    $events = $events_query->get_posts();
    $checked_in_count = $events_query->found_posts;
    
    $events_query = new WP_Query( array('post_type' => array('event_magic_tickets'), 'posts_per_page' => -1, 'meta_query' => array( array( 'key' => 'WooCommerceEventsStatus', 'value' => 'Checked In', 'compare' => '=' ) )) );
    $events = $events_query->get_posts();
    $checked_in_count = $events_query->found_posts;
    
    $events_query = new WP_Query( array('post_type' => array('product'), 'posts_per_page' => -1, 'meta_query' => array( array( 'key' => 'WooCommerceEventsEvent', 'value' => 'Event', 'compare' => '=' ) )) );
    $events = $events_query->get_posts();
    $event_count = $events_query->found_posts;
    
    $fooevents = get_plugin_data(WP_PLUGIN_DIR.'/fooevents/fooevents.php');
    $woocommerce = get_plugin_data(WP_PLUGIN_DIR.'/woocommerce/woocommerce.php');

    echo "<div class='fooevents_widget_item'>"."Total tickets: ".$ticket_count."</div>";
    
    echo "<div class='fooevents_widget_item'>"."Total events: ".$event_count."</div>";

    echo "<div class='fooevents_widget_item'>"."Tickets 'Not Checked In': ".$not_checked_in_count."</div>";
    
    echo "<div class='fooevents_widget_item'>"."Tickets 'Checked In': ".$checked_in_count."</div>";

    echo "<p><a href='".$fooevents['PluginURI']."' target='_BLANK'>FooEvents</a> ".$fooevents['Version']. " running on <a href='".$woocommerce['PluginURI']."' target='_BLANK'>WooCommerce</a> ".$woocommerce['Version']."</p>";

    if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
        require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
    }
    
    $fooevents_pdf_tickets_active = 'No';
    $fooevents_pdf_tickets = array('Version' => '');
    if ( fooevents_check_plugin_active('fooevents_pdf_tickets/fooevents-pdf-tickets.php') || is_plugin_active_for_network('fooevents_pdf_tickets/fooevents-pdf-tickets.php')) {
        
        $fooevents_pdf_tickets_active = 'Yes';
        $fooevents_pdf_tickets = get_plugin_data(WP_PLUGIN_DIR.'/fooevents_pdf_tickets/fooevents-pdf-tickets.php');
        
    }
    
    echo "<div class='fooevents_widget_item'><a href='https://www.fooevents.com/fooevents-pdf-tickets/' target='_BLANK'>FooEvents PDF tickets</a>: "."<b>".$fooevents_pdf_tickets_active."</b> ".$fooevents_pdf_tickets['Version']."</div>";
    
    $fooevents_express_check_in_active = 'No';
    $fooevents_express_check_in = array('Version' => '');
    if ( fooevents_check_plugin_active('fooevents_express_check_in/fooevents-express-check_in.php') || is_plugin_active_for_network('fooevents_express_check_in/fooevents-express-check_in.php')) {
        
        $fooevents_express_check_in_active = 'Yes';
        $fooevents_express_check_in = get_plugin_data(WP_PLUGIN_DIR.'/fooevents_express_check_in/fooevents-express-check_in.php');
        
    }
    
    echo "<div class='fooevents_widget_item'><a href='https://www.fooevents.com/fooevents-express-check-in/' target='_BLANK'>FooEvents Express Check-in</a>: "."<b>".$fooevents_express_check_in_active."</b> ".$fooevents_express_check_in['Version']."</div>";
   
    $fooevents_calendar_active = 'No';
    $fooevents_calendar = array('Version' => '');
    if ( fooevents_check_plugin_active('fooevents-calendar/fooevents-calendar.php') || is_plugin_active_for_network('fooevents-calendar/fooevents-calendar.php')) {
        
        $fooevents_calendar_active = 'Yes';
        $fooevents_calendar = get_plugin_data(WP_PLUGIN_DIR.'/fooevents-calendar/fooevents-calendar.php');
        
    }
    
    echo "<div class='fooevents_widget_item'><a href='https://www.fooevents.com/fooevents-calendar/' target='_BLANK'>FooEvents Calendar</a>: "."<b>".$fooevents_calendar_active."</b> ".$fooevents_calendar['Version']."</div>";
    
    $fooevents_custom_attendee_fields_active = 'No';
    $fooevents_custom_attendee_fields = array('Version' => '');
    if ( fooevents_check_plugin_active('fooevents_custom_attendee_fields/fooevents-custom-attendee-fields.php') || is_plugin_active_for_network('fooevents_custom_attendee_fields/fooevents-custom-attendee-fields.php')) {
        
        $fooevents_custom_attendee_fields_active = 'Yes';
        $fooevents_custom_attendee_fields = get_plugin_data(WP_PLUGIN_DIR.'/fooevents_custom_attendee_fields/fooevents-custom-attendee-fields.php');
        
    }
    
    echo "<div class='fooevents_widget_item'><a href='https://www.fooevents.com/fooevents-custom-attendee-fields/' target='_BLANK'>FooEvents Custom Attendee Fields</a>: "."<b>".$fooevents_custom_attendee_fields_active."</b> ".$fooevents_custom_attendee_fields['Version']."</div>";
    
    $fooevents_multi_day_active = 'No';
    $fooevents_multi_day = array('Version' => '');
    if ( fooevents_check_plugin_active('fooevents_multi_day/fooevents-multi-day.php') || is_plugin_active_for_network('fooevents_multi_day/fooevents-multi-day.php')) {
        
        $fooevents_multi_day_active = 'Yes';
        $fooevents_multi_day = get_plugin_data(WP_PLUGIN_DIR.'/fooevents_multi_day/fooevents-multi-day.php');
        
    }
    
    echo "<div class='fooevents_widget_item'><a href='https://www.fooevents.com/fooevents-multi-day/' target='_BLANK'>FooEvents Multi-Day</a>: "."<b>".$fooevents_multi_day_active."</b> ".$fooevents_multi_day['Version']."</div>";
    
    $fooevents_seating_active = 'No';
    $fooevents_seating = array('Version' => '');
    if ( fooevents_check_plugin_active('fooevents_seating/fooevents-seating.php') || is_plugin_active_for_network('fooevents_seating/fooevents-seating.php')) {
        
        $fooevents_seating_active = 'Yes';
        $fooevents_seating = get_plugin_data(WP_PLUGIN_DIR.'/fooevents_seating/fooevents-seating.php');
        
    }
    
    echo "<div class='fooevents_widget_item'><a href='https://www.fooevents.com/fooevents-seating/' target='_BLANK'>FooEvents Seating</a>: "."<b>".$fooevents_seating_active."</b> ".$fooevents_seating['Version']."</div>";
    
}

function fooevents_check_plugin_active( $plugin ) {

    return in_array( $plugin, (array) get_option( 'active_plugins', array() ) );

}


function uninstallFooEvents() {
    
    delete_option('globalWooCommerceEventsAPIKey');
    delete_option('globalWooCommerceEnvatoAPIKey');
    delete_option('globalWooCommerceEventsGoogleMapsAPIKey');
    delete_option('globalWooCommerceEventsTicketBackgroundColor');
    delete_option('globalWooCommerceEventsTicketButtonColor');
    delete_option('globalWooCommerceEventsTicketTextColor');
    delete_option('globalWooCommerceEventsTicketLogo');
    delete_option('globalWooCommerceEventsTicketHeaderImage');
    delete_option('globalWooCommerceEventsChangeAddToCart');
    delete_option('globalWooCommerceHideEventDetailsTab');
    delete_option('globalWooCommerceUsePlaceHolders');
    delete_option('globalWooCommerceHideUnpaidTicketsApp');
    delete_option('globalWooCommerceEventsHideUnpaidTickets');
    delete_option('globalWooCommerceEventsEmailTicketAdmin');
    delete_option('globalWooCommerceEventsAppLogo');
    delete_option('globalWooCommerceEventsAppColor');
    delete_option('globalWooCommerceEventsAppTextColor');
    delete_option('globalWooCommerceEventsAppBackgroundColor');
    delete_option('globalWooCommerceEventsAppSignInTextColor');
    delete_option('globalWooCommerceEventsAttendeeOverride');
    delete_option('globalWooCommerceEventsTicketOverride');
    delete_option('WooCommerceEventsDayOverride');
    
}
register_uninstall_hook(__FILE__, 'uninstallFooEvents');