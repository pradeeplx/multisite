<?php
    global $woocommerce;
        
    $event = $_GET['event'];
    $sorted_rows = array();
    $events_query = new WP_Query( array('post_type' => array('event_magic_tickets'), 'posts_per_page' => -1, 'meta_query' => array( array( 'key' => 'WooCommerceEventsProductID', 'value' => $event ) )) );
    $events = $events_query->get_posts();
    if (empty($events)) {
        echo "There are no attendees/tickets for this event.";
    }
    $x = 0;
    $location_name = get_post_meta($event, 'WooCommerceEventsLocation', true);

    $current_logo_url = get_post_meta($event, 'WooCommerceEventsTicketLogo', true);
    $new_logo_url = get_post_meta($event, 'WooCommerceEventsPrintTicketLogo', true);
        
    foreach($events as $eventItem) {
        
        $id = $eventItem->ID;
        $ticket = get_post($id);
        $ticketID = $ticket->post_title;
        $WooCommerceEventsTicketHash = get_post_meta($id, 'WooCommerceEventsTicketHash', true);
        $order_id = get_post_meta($id, 'WooCommerceEventsOrderID', true);
        $product_id = get_post_meta($id, 'WooCommerceEventsProductID', true);
        $event_name = get_post_meta($id, 'WooCommerceEventsProductName', true);
        $customer_id = get_post_meta($id, 'WooCommerceEventsCustomerID', true);
       
        
        $WooCommerceEventsStatus = get_post_meta($id, 'WooCommerceEventsStatus', true);
        $ticketType = get_post_meta($ticket->ID, 'WooCommerceEventsTicketType', true);
        
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

            $WooCommerceEventsAttendeeName = $order->billing_first_name;

        } 
        
        $WooCommerceEventsAttendeeLastName = get_post_meta($ticket->ID, 'WooCommerceEventsAttendeeLastName', true);
        if(empty($WooCommerceEventsAttendeeLastName)) {

            $WooCommerceEventsAttendeeLastName = $order->billing_last_name;

        }
        
        $WooCommerceEventsAttendeeEmail = get_post_meta($ticket->ID, 'WooCommerceEventsAttendeeEmail', true);
        if(empty($WooCommerceEventsAttendeeEmail)) {

            $WooCommerceEventsAttendeeEmail = $order->billing_email;

        }
        
        $WooCommerceEventsCaptureAttendeeTelephone = get_post_meta($ticket->ID, 'WooCommerceEventsAttendeeTelephone', true);
        $WooCommerceEventsCaptureAttendeeCompany = get_post_meta($ticket->ID, 'WooCommerceEventsAttendeeCompany', true);
        $WooCommerceEventsCaptureAttendeeDesignation = get_post_meta($ticket->ID, 'WooCommerceEventsAttendeeDesignation', true);
        $WooCommerceEventsPurchaserFirstName = get_post_meta($ticket->ID, 'WooCommerceEventsPurchaserFirstName', true);
        $WooCommerceEventsPurchaserLastName = get_post_meta($ticket->ID, 'WooCommerceEventsPurchaserLastName', true);
        $WooCommerceEventsPurchaserEmail = get_post_meta($ticket->ID, 'WooCommerceEventsPurchaserEmail', true);
        $WooCommerceEventsPurchaserPhone = get_post_meta($ticket->ID, 'WooCommerceEventsPurchaserPhone', true);

        $sorted_rows[$x]["TicketHash"] = $WooCommerceEventsTicketHash;
        $sorted_rows[$x]["TicketID"] = $ticketID;
        $sorted_rows[$x]["OrderID"] = $order_id;
        $sorted_rows[$x]["Event Name"] = $event_name;
        $sorted_rows[$x]["Location"] = $location_name;
        $sorted_rows[$x]["Event Name Variations"] = $event_name . " (" . $variationOutput . ")";
        
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
        $sorted_rows[$x]["Purchaser Company"] = $order->billing_company;
        
        

       


        if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
            require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
        }
        
        if ($this->is_plugin_active( 'fooevents_custom_attendee_fields/fooevents-custom-attendee-fields.php') || is_plugin_active_for_network('fooevents_custom_attendee_fields/fooevents-custom-attendee-fields.php')) {
            $Fooevents_Custom_Attendee_Fields = new Fooevents_Custom_Attendee_Fields();
            $fooevents_custom_attendee_fields_options = $Fooevents_Custom_Attendee_Fields->display_tickets_meta_custom_options_array($id);
        
            foreach($fooevents_custom_attendee_fields_options as $key => $value) {

                $sorted_rows[$x][$key] = $value;
            }
        }

        if ($this->is_plugin_active( 'fooevents_seating/fooevents-seating.php') || is_plugin_active_for_network('fooevents_seating/fooevents-seating.php')) {
            $Fooevents_Seating = new Fooevents_Seating();
            $fooevents_seating_options = $Fooevents_Seating->display_tickets_meta_seat_options_output($id);
            $fooevents_seating_options = str_replace('Row Name: ', '', $fooevents_seating_options);
            $fooevents_seating_options = str_replace('Seat Number: ', '', $fooevents_seating_options);
            $fooevents_seating_options = preg_replace('/<br>/', ' - ', $fooevents_seating_options, 1);
            $sorted_rows[$x]["SeatInfo"] = $fooevents_seating_options;
            
        }


        $x++;


        
    }
   
    $output = array();

    $y = 0; ?>


<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>FooEvents Attendee Badges</title>
    <style>
      
        html, body, div, h1, h2 {
            vertical-align: baseline;
            margin: 0;
            padding: 0;
            border: 0;
        }

        html {
            line-height: 1;
        }
        
        h1 {
            font-size: 22px;
        }

        h2 {
            font-size: 20px;
        }

        h3 {
            font-weight: normal;
            line-height: 14px;
        }

        h3 .seat {
            font-weight: bold;
        }


        /* START Badge styles */
        .letter_10, .letter_30 {
            width: 8.5in;
            padding: .50in 0 0 .30in;
        }

        .a4_12 {
            width: 190.5mm;
            padding: 4.5mm 9.75mm;
        }

        .a4_16 {
            width: 198mm;
            padding: 12.9mm 6mm;
        }

        .a4_24 {
            width: 210mm;
            padding: 9mm 0 8mm 0;
        }
        
        .a4_39 {
            width: 198mm;
            padding: 14.015mm 6mm;
        }

        .a4_45 {
            width: 192.5mm;
            padding: 13.95mm 8.75mm;
        }

        .a4_12 .badge_12 {
            width: 63.5mm;
            height: 72mm;
        }

        .a4_16 .badge_16 {
            width: 99mm;
            height: 33.9mm;
        }

        .a4_24 .badge_24 {
            width: 70mm;
            height: 35mm;
        }
        
        .a4_39 .badge_39 {
            width: 66mm;
            height: 20.69mm;
        }

        .a4_45 .badge_45 {
            width: 38.5mm;
            height: 29.9mm;
        }

        .badge_page, .ticket_page {
            margin: 0;
            font-family: Arial, Sans-serif;
            text-align: center;
            page-break-after: always;
        }

        .letter_10 .badge, .letter_30 .badge {
            padding: .025in .3in 0;
            margin-right: .155in;
            margin-top: .025in;
        }

        .badge {
            float: left;
            text-align: center;
            overflow: hidden;
            -moz-box-sizing: border-box;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
            position: relative;
        }

        .badge_line_on {
            outline: 1px dotted #ccc;
        }

        .letter_10 .badge_10 {
            width: 4.025in;
            height: 2in;
        }


        .letter_30 .badge_30 {
            width: 2.625in;
            height: 1in;
        }

        .badge_10 .badge_inner, .badge_12 .badge_inner {
            padding-bottom: 20px;
        }


        .badge_30 h1, .badge_24 h1 {
            font-size: 16px;
            margin: 4px auto;
        }

        .badge_16 h1 {
            font-size: 16px;
            margin: 7px auto;
        }

        .badge_39 h1 {
            font-size: 15px;
            margin: 3px auto;
        }

        .badge_45 h1 {
            font-size: 12px;
            margin: 3px auto;
        }

        .badge_30 h2, .badge_24 h2 {
            font-size: 14px;
            margin: 4px auto;
        }

        .badge_16 h2 {
            font-size: 14px;
            margin: 7px auto;
        }

        .badge_39 h2 {
            font-size: 13px;
            margin: 3px auto;
        }

        .badge_45 h2 {
            font-size: 11px;
            margin: 3px auto;
        }
        
        .badge_30 h3, .badge_24 h3 {
            font-size: 12px;
            margin: 4px auto;
        }

        .badge_16 h3 {
            font-size: 12px;
            margin: 7px auto;
        }

        .badge_39 h3 {
            font-size: 11px;
            margin: 3px auto;
        }

        .badge_45 h3 {
            font-size: 11px;
            margin: 3px auto;
        }

        .badge_16 .event_car {
            margin: 7px auto;
            display: block;
        }

        .badge_39 .event_car, .badge_45 .event_car {
            margin: 3px auto;
            display: block;
        }

        .badge_30 .event_car, .badge_24 .event_car {
            font-size: 10px;
        }

        .badge_10 h1, .badge_12 h1 {
            font-size: 22px;
            margin-top: 20px;
        }

        .badge_10 h2, .badge_12 h2 {
            font-size: 20px;
            margin-top: 20px;
        }
        
        .badge_10 .event_car, .badge_12 .event_car {
            font-size: 16px;
            line-height: 20px;
        }

        .badge_10 h3, .badge_12 h3 {
            font-size: 18px;
            margin-top: 20px;
        }

        .badge_30 img, .badge_16 img, .badge_24 img {
            width: 130px;
        }

        .badge_39 img, .badge_45 img {
            width: 100px;
        }
        
        .badge_inner {
            margin: 0;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 90%;
            overflow-wrap: break-word;
            word-wrap: break-word;
        }
        /* END Badge styles */




        /* START Print Ticket styles */

        .ticket {
            float: left;
            text-align: center;
            overflow: hidden;
            -moz-box-sizing: border-box;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
            position: relative;
            word-break: break-all;
        }

        .ticket h1, .ticket h3 {
            margin: 0px;
        }        
       
        .ticket_small, .ticket_small_uppercase {
            font-size: 12px;
            line-height: 15px;
            opacity: 0.7
        }

        .ticket_medium, .ticket_medium_uppercase {
            font-size: 17px;
            line-height: 19px;
            margin: 0px;
            line-height: 20px;
        }

        .ticket_large, .ticket_large_uppercase {
            font-size: 26px;
            line-height: 27px;
            font-weight: normal;
        }

        .ticket_small b {
            font-size: 10px;
            opacity: 0.9
        }

        .ticket_small_uppercase, .ticket_medium_uppercase, .ticket_large_uppercase {
            text-transform: uppercase;
        }        

        .ticket_hr {
            width: 30%;
            opacity: 0.3;
            margin-bottom: 0;
            margin-top: 10px;
        }

        .ticket_spacer {
            width: 100%;
            height: 10px;
        }

        .tickets_letter_10, .tickets_avery_letter_10 {
            width: 11in;
            padding: 0;            
        }

        .tickets_letter_10 .ticket_10,
        .tickets_avery_letter_10 .ticket_10 {
            width: 5.5in;
            height: 1.7in;
            position: relative;
        }

        .tickets_a4_10 {
            width: 297mm;
            padding: 0;            
        }

        .tickets_a4_10 .ticket_10 {
            width: 148.2mm;
            height: 42mm;
            position: relative;
        }

        .ticket_page.ticket_line_on .ticket_10 {
            border-bottom: 1px solid #aaaaaa;
            border-right: 1px solid #aaaaaa;
        }

        .tickets_letter_10 .ticket_10_stub,
        .tickets_avery_letter_10 .ticket_10_stub {
            width: 1.5in;
            height: 1.7in;
            float: right;
            position: relative;
        }

        .tickets_a4_10 .ticket_10_stub {
            width: 1.5in;
            height: 1.7in;
            float: right;
            position: relative;
        }

        .tickets_letter_10 .ticket_10_stub img,
        .tickets_avery_letter_10 .ticket_10_stub img,
        .tickets_a4_10 .ticket_10_stub img {
            width: 100%;
        }

        .tickets_letter_10 .ticket_10_stub .ticket_inner,
        .tickets_avery_letter_10 .ticket_10_stub .ticket_inner,
        .tickets_a4_10 .ticket_10_stub .ticket_inner {
            width: 85%;
            margin: 0;
            position: absolute;
            top: 50%;
            transform: translate(-50%, -50%);
            left: 50%;
        }

        .ticket_page.ticket_line_on .ticket_10_stub {
            border-left: 1px solid #aaaaaa;
        }

        .tickets_letter_10 img.ticket_logo,
        .tickets_avery_letter_10 img.ticket_logo,
        .tickets_a4_10 img.ticket_logo {
            width: 22%;
            margin: 0;
            position: absolute;
            top: 50%;
            left: 4%;
            transform: translate(0, -50%);
        }

        /* default: no logo, no stub */
        .ticket_info {
            width: 94%;
            right: 0;
            padding: 3%;
            margin: 0;
            position: absolute;
            top: 50%;
            transform: translate(0, -50%);
        }

        /* logo, no stub */
       .ticket_logo ~ .ticket_info {
            width: 68%;
       }

       /* stub, no logo */     
       .ticket_10_stub ~ .ticket_inner .ticket_info {
            width: 68%;
            right: 1.5in;
       }
       
       /* stub and logo */     
        .ticket_10_stub ~ .ticket_inner .ticket_logo ~ .ticket_info {
            width: 41%;    
       }

        .ticket_info img {
            width: 70%;
            text-align: center;
            max-width: 165px;
        }

        /* END Print Ticket styles */
       

    </style>
    <script type="text/javascript">
        function printFunction() { 
            window.print();
        }
    </script>

</head>
<body onload="printFunction();">
    <?php
        $page_size = !empty($_GET["size"]) ? $_GET["size"] : "letter_10";
        
        $nr_per_page = substr($page_size, -2);

        $content_to_echo1 = "";
        $content_to_echo2 = "";
        $content_to_echo3 = "";
        $content_to_echo4 = "";
        $content_to_echo5 = "";
        $content_to_echo6 = "";
        $event = "";
        $event_var = "";
        $var_only = "";
        $barcode = "";
        $ticketnr = "";
        $name = "";
        $email = "";
        $telephone = "";
        $company = "";
        $designation = "";
        $seat = "";
        $location = "";        
        $current_url = "";

        foreach($sorted_rows as $item) :
        
            $ticketnr = "<p><b>Ticket:</b> " . $item["TicketID"] . "</p>";
            if (!empty($item["TicketHash"]))
                $item["TicketHash"] .= "-";
        
            $barcode = "<img src='" . esc_url($this->Config->barcodeURL) . $item["TicketHash"] . str_replace("#","",$item["TicketID"]) . ".png' />";
            $name = $item["Attendee First Name"] . " " . $item["Attendee Last Name"];
            $event = $item["Event Name"];
            $event_var = $item["Event Name Variations"];
            $var_only = $item["Variation"];
            $email = $item["Attendee Email"];
            $telephone = $item["Attendee Telephone"];
            $company = $item["Attendee Company"];
            $designation = $item["Attendee Designation"];
            $seat = $item["SeatInfo"];
            $location = $item["Location"];

            if ($_GET["attendee_show"] == "badges") :
                switch($_GET["badgefield1"]){
                    case "event":
                        $content_to_echo1 = $event;
                        break;
                    case "event_var":
                        $content_to_echo1 = "<span class='event_car'>" . $event_var . "</span>";
                        break;
                    case "var_only":
                        $content_to_echo1 = $var_only;
                        break;
                    case "ticketnr":
                        $content_to_echo1 = $ticketnr;
                        break;
                    case "barcode":
                        $content_to_echo1 = $barcode;
                        break;
                    case "name":
                        $content_to_echo1 = $name;
                        break;
                    case "email":
                        $content_to_echo1 = $email;
                        break;
                    case "telephone":
                        $content_to_echo1 = $telephone;
                        break;
                    case "company":
                        $content_to_echo1 = $company;
                        break;
                    case "designation":
                        $content_to_echo1 = $designation;
                        break;
                    case "seat":
                        $content_to_echo1 = $seat;
                        break;
                    case stristr($_GET["badgefield1"],'fooevents_custom'):
                        $cf_name = ucwords(str_replace('_', ' ', str_replace('fooevents_custom_', ' ', $_GET["badgefield1"])));
                        if (!empty($item[$_GET["badgefield1"]]))
                            $content_to_echo1 = $cf_name . ": " . $item[$_GET["badgefield1"]];
                        break;
                }

                switch($_GET["badgefield2"]){
                    case "event":
                        $content_to_echo2 = $event;
                        break;
                    case "event_var":
                        $content_to_echo2 = "<span class='event_car'>" . $event_var . "</span>";
                        break;
                    case "var_only":
                        $content_to_echo2 = $var_only;
                        break;
                    case "ticketnr":
                        $content_to_echo2 = $ticketnr;
                        break;
                    case "barcode":
                        $content_to_echo2 = $barcode;
                        break;
                    case "name":
                        $content_to_echo2 = $name;
                        break;
                    case "email":
                        $content_to_echo2 = $email;
                        break;
                    case "telephone":
                        $content_to_echo2 = $telephone;
                        break;
                    case "company":
                        $content_to_echo2 = $company;
                        break;
                    case "designation":
                        $content_to_echo2 = $designation;
                        break;
                    case "seat":
                        $content_to_echo2 = $seat;
                        break;
                    case stristr($_GET["badgefield2"],'fooevents_custom'):
                        $cf_name = ucwords(str_replace('_', ' ', str_replace('fooevents_custom_', ' ', $_GET["badgefield2"])));
                        if (!empty($item[$_GET["badgefield2"]]))
                            $content_to_echo2 = $cf_name . ": " . $item[$_GET["badgefield2"]];
                        break;
                }

                switch($_GET["badgefield3"]){
                    case "event":
                        $content_to_echo3 = $event;
                        break;
                    case "event_var":
                        $content_to_echo3 = "<span class='event_car'>" . $event_var . "</span>";
                        break;
                    case "var_only":
                        $content_to_echo3 = $var_only;
                        break;
                    case "ticketnr":
                        $content_to_echo3 = $ticketnr;
                        break;
                    case "barcode":
                        $content_to_echo3 = $barcode;
                        break;
                    case "name":
                        $content_to_echo3 = $name;
                        break;
                    case "email":
                        $content_to_echo3 = $email;
                        break;
                    case "telephone":
                        $content_to_echo3 = $telephone;
                        break;
                    case "company":
                        $content_to_echo3 = $company;
                        break;
                    case "designation":
                        $content_to_echo3 = $designation;
                        break;
                    case "seat":
                        $content_to_echo3 = $seat;
                        break;
                    case stristr($_GET["badgefield3"],'fooevents_custom'):
                        $cf_name = ucwords(str_replace('_', ' ', str_replace('fooevents_custom_', ' ', $_GET["badgefield3"])));
                        if (!empty($item[$_GET["badgefield3"]]))
                            $content_to_echo3 = $cf_name . ": " . $item[$_GET["badgefield3"]];
                        break;
                }
            
                if ($y == 0) {
                    echo '<div class="badge_page ' . $page_size . '">'; 
                } elseif ($y%$nr_per_page == 0) {
                    echo '</div><div class="badge_page ' . $page_size . '">'; 
                }
            ?>

    
            <div class="badge badge_<?php echo $nr_per_page . " badge_line_" . $_GET["cutlines"]; ?>">
                <div class="badge_inner">
                    <?php echo "<h1>" . $content_to_echo1 . "</h1>"; ?>
                    <?php echo "<h2>" . $content_to_echo2 . "</h2>"; ?>
                    <?php echo "<h3>" . $content_to_echo3 . "</h3>"; ?>
                </div>
            </div>
        <?php endif; ?>

    
        <?php if ($_GET["attendee_show"] == "tickets") :

            switch($_GET["ticketfield1"]){
                case "event":
                    $content_to_echo1 = "<div class='ticket_" . $_GET["font1"] . "'>" . $event . "</div>";
                    break;
                case "event_var":
                    $content_to_echo1 = "<div class='ticket_" . $_GET["font1"] . "'>" . $event_var . "</div>";
                    break;
                case "ticketnr":
                    $content_to_echo1 = $item["TicketID"] != "" ? "<div class='ticket_seat ticket_" . $_GET["font1"] . "'><b>Ticket</b><div>" . $item["TicketID"] . "</div></div>" : "";
                    break;
                case "barcode":
                    $content_to_echo1 = $barcode;
                    break;
                case "name":
                    $content_to_echo1 = "<div class='ticket_" . $_GET["font1"] . "'>" . $name . "</div>";
                    break;
                case "seat":
                    $content_to_echo1 = $seat != "" ? "<div class='ticket_seat ticket_" . $_GET["font1"] . "'><b>Seat</b><div>" . $seat . "</div></div>" : "";
                    break;
                case "location":
                    $content_to_echo1 = "<div class='ticket_" . $_GET["font1"] . "'>" . $location . "</div>";
                    break;
                case stristr($_GET["ticketfield1"],'fooevents_custom'):
                    $cf_name = ucwords(str_replace('_', ' ', str_replace('fooevents_custom_', ' ', $_GET["ticketfield1"])));
                    if (!empty($item[$_GET["ticketfield1"]]))
                        $content_to_echo1 = "<div class='ticket_" . $_GET["font1"] . "'><b>" . $cf_name . "</b><div>" . $item[$_GET["ticketfield1"]] . "</div></div>";
                    break;
            }

            switch($_GET["ticketfield2"]){
                case "event":
                    $content_to_echo2 = "<div class='ticket_" . $_GET["font2"] . "'>" . $event . "</div>";
                    break;
                case "event_var":
                    $content_to_echo2 = "<div class='ticket_" . $_GET["font2"] . "'>" . $event_var . "</div>";
                    break;
                case "ticketnr":
                    $content_to_echo2 = $item["TicketID"] != "" ? "<div class='ticket_seat ticket_" . $_GET["font2"] . "'><b>Ticket</b><div>" . $item["TicketID"] . "</div></div>" : "";
                    break;
                case "barcode":
                    $content_to_echo2 = $barcode;
                    break;
                case "name":
                    $content_to_echo2 = "<div class='ticket_" . $_GET["font2"] . "'>" . $name . "</div>";
                    break;
                case "seat":
                    $content_to_echo2 = $seat != "" ? "<div class='ticket_seat ticket_" . $_GET["font2"] . "'><b>Seat</b><div>" . $seat . "</div></div>" : "";
                    break;
                case "location":
                    $content_to_echo2 = "<div class='ticket_" . $_GET["font2"] . "'>" . $location . "</div>";
                    break;
                case stristr($_GET["ticketfield2"],'fooevents_custom'):
                    $cf_name = ucwords(str_replace('_', ' ', str_replace('fooevents_custom_', ' ', $_GET["ticketfield2"])));
                    if (!empty($item[$_GET["ticketfield2"]]))
                        $content_to_echo2 = "<div class='ticket_" . $_GET["font2"] . "'><b>" . $cf_name . "</b><div>" . $item[$_GET["ticketfield2"]] . "</div></div>";
                    break;
            }

            switch($_GET["ticketfield3"]){
                case "event":
                    $content_to_echo3 = "<div class='ticket_" . $_GET["font3"] . "'>" . $event . "</div>";
                    break;
                case "event_var":
                    $content_to_echo3 = "<div class='ticket_" . $_GET["font3"] . "'>" . $event_var . "</div>";
                    break;
                case "ticketnr":
                    $content_to_echo3 = $item["TicketID"] != "" ? "<div class='ticket_seat ticket_" . $_GET["font3"] . "'><b>Ticket</b><div>" . $item["TicketID"] . "</div></div>" : "";
                    break;
                case "barcode":
                    $content_to_echo3 = $barcode;
                    break;
                case "name":
                    $content_to_echo3 = "<div class='ticket_" . $_GET["font3"] . "'>" . $name . "</div>";
                    break;
                case "seat":
                    $content_to_echo3 = $seat != "" ? "<div class='ticket_seat ticket_" . $_GET["font3"] . "'><b>Seat</b><div>" . $seat . "</div></div>" : "";
                    break;
                case "location":
                    $content_to_echo3 = "<div class='ticket_" . $_GET["font3"] . "'>" . $location . "</div>";
                    break;
                case stristr($_GET["ticketfield3"],'fooevents_custom'):
                    $cf_name = ucwords(str_replace('_', ' ', str_replace('fooevents_custom_', ' ', $_GET["ticketfield3"])));
                    if (!empty($item[$_GET["ticketfield3"]]))
                        $content_to_echo3 = "<div class='ticket_" . $_GET["font3"] . "'><b>" . $cf_name . "</b><div>" . $item[$_GET["ticketfield3"]] . "</div></div>";
                    break;
            }


            switch($_GET["ticketfield4"]){
                case "event":
                    $content_to_echo4 = "<div class='ticket_" . $_GET["font4"] . "'>" . $event . "</div>";
                    break;
                case "event_var":
                    $content_to_echo4 = "<div class='ticket_" . $_GET["font4"] . "'>" . $event_var . "</div>";
                    break;
                case "ticketnr":
                    $content_to_echo4 = $item["TicketID"] != "" ? "<div class='ticket_seat ticket_" . $_GET["font4"] . "'><b>Ticket</b><div>" . $item["TicketID"] . "</div></div>" : "";
                    break;
                case "barcode":
                    $content_to_echo4 = $barcode;
                    break;
                case "name":
                    $content_to_echo4 = "<div class='ticket_" . $_GET["font4"] . "'>" . $name . "</div>";
                    break;
                case "seat":
                    $content_to_echo4 = $seat != "" ? "<div class='ticket_seat ticket_" . $_GET["font4"] . "'><b>Seat</b><div>" . $seat . "</div></div>" : "";
                    break;
                case "location":
                    $content_to_echo4 = "<div class='ticket_" . $_GET["font4"] . "'>" . $location . "</div>";
                    break;
                case stristr($_GET["ticketfield4"],'fooevents_custom'):
                    $cf_name = ucwords(str_replace('_', ' ', str_replace('fooevents_custom_', ' ', $_GET["ticketfield4"])));
                    if (!empty($item[$_GET["ticketfield4"]]))
                        $content_to_echo4 = "<div class='ticket_" . $_GET["font4"] . "'><b>" . $cf_name . "</b><div>" . $item[$_GET["ticketfield4"]] . "</div></div>";
                    break;
            }

            switch($_GET["ticketfield5"]){
                case "event":
                    $content_to_echo5 = "<div class='ticket_" . $_GET["font5"] . "'>" . $event . "</div>";
                    break;
                case "event_var":
                    $content_to_echo5 = "<div class='ticket_" . $_GET["font5"] . "'>" . $event_var . "</div>";
                    break;
                case "ticketnr":
                    $content_to_echo5 = $item["TicketID"] != "" ? "<div class='ticket_seat ticket_" . $_GET["font5"] . "'><b>Ticket</b><div>" . $item["TicketID"] . "</div></div>" : "";
                    break;
                case "barcode":
                    $content_to_echo5 = $barcode;
                    break;
                case "name":
                    $content_to_echo5 = "<div class='ticket_" . $_GET["font5"] . "'>" . $name . "</div>";
                    break;
                case "seat":
                    $content_to_echo5 = $seat != "" ? "<div class='ticket_seat ticket_" . $_GET["font5"] . "'><b>Seat</b><div>" . $seat . "</div></div>" : "";
                    break;
                case "location":
                    $content_to_echo5 = "<div class='ticket_" . $_GET["font5"] . "'>" . $location . "</div>";
                    break;
                case stristr($_GET["ticketfield5"],'fooevents_custom'):
                    $cf_name = ucwords(str_replace('_', ' ', str_replace('fooevents_custom_', ' ', $_GET["ticketfield5"])));
                    if (!empty($item[$_GET["ticketfield5"]]))
                    $content_to_echo5 = "<div class='ticket_" . $_GET["font5"] . "'><b>" . $cf_name . "</b><div>" . $item[$_GET["ticketfield5"]] . "</div></div>";
                    break;
            }


            switch($_GET["ticketfield6"]){
                case "event":
                    $content_to_echo6 = "<div class='ticket_" . $_GET["font6"] . "'>" . $event . "</div>";
                    break;
                case "event_var":
                    $content_to_echo6 = "<div class='ticket_" . $_GET["font6"] . "'>" . $event_var . "</div>";
                    break;
                case "ticketnr":
                    $content_to_echo6 = $item["TicketID"] != "" ? "<div class='ticket_seat ticket_" . $_GET["font6"] . "'><b>Ticket</b><div>" . $item["TicketID"] . "</div></div>" : "";
                    break;
                case "barcode":
                    $content_to_echo6 = $barcode;
                    break;
                case "name":
                    $content_to_echo6 = "<div class='ticket_" . $_GET["font6"] . "'>" . $name . "</div>";
                    break;
                case "seat":
                    $content_to_echo6 = $seat != "" ? "<div class='ticket_seat ticket_" . $_GET["font6"] . "'><b>Seat</b><div>" . $seat . "</div></div>" : "";
                    break;
                case "location":
                    $content_to_echo6 = "<div class='ticket_" . $_GET["font6"] . "'>" . $location . "</div>";
                    break;
                case stristr($_GET["ticketfield6"],'fooevents_custom'):
                    $cf_name = ucwords(str_replace('_', ' ', str_replace('fooevents_custom_', ' ', $_GET["ticketfield6"])));
                    if (!empty($item[$_GET["ticketfield6"]]))
                        $content_to_echo6 = "<div class='ticket_" . $_GET["font6"] . "'><b>" . $cf_name . "</b><div>" . $item[$_GET["ticketfield6"]] . "</div></div>";
                    break;
            }


        
            if ($y == 0) {
                echo '<div class="ticket_page ' . $page_size . '  ticket_line_' . $_GET["cutlines"] . '">'; 
            } elseif ($y%$nr_per_page == 0) {
                echo '</div><div class="ticket_page ' . $page_size . '  ticket_line_' . $_GET["cutlines"] . '">'; 
            }

        ?>


        <div class="ticket ticket_10">
            <?php if (($_GET["ticketfield4"] != "nothing") || ($_GET["ticketfield5"] != "nothing") || ($_GET["ticketfield6"]) != "nothing"): ?>
            <div class="ticket_10_stub">
                <div class="ticket_inner">
                 <?php 
                    echo $content_to_echo4;
                    echo "<div class='ticket_spacer'></div>";
                    echo $content_to_echo5;
                    echo "<div class='ticket_spacer'></div>";
                    echo $content_to_echo6;
                 ?> 
                 </div>
            </div>
            <?php endif; ?>
            <div class="ticket_inner">
            
                <?php if($_GET["showlogo"] == "current_logo") :
                        echo '<img class="ticket_logo" src=' .  $current_logo_url . ' />';
                    elseif($_GET["showlogo"] == "new_logo") :
                        echo '<img class="ticket_logo" src=' .  $new_logo_url . ' />';
                    endif; ?>
                
                <div class="ticket_info">
                    <?php        
                        echo $content_to_echo1;
                        echo "<hr class='ticket_hr' /><div class='ticket_spacer'></div>";
                        echo $content_to_echo2;
                        echo "<div class='ticket_spacer'></div>";
                        echo $content_to_echo3;
                     ?>

                </div>
            </div>
        </div>
        
        
        
        <?php endif; ?>



    <?php
        $y++;
        endforeach;
    ?>

</body>
</html>