<h1><?php echo $post->post_title; ?></h1>
<?php echo $message; ?>
<table class="form-table">
	<tbody>     
            <tr valign="top">  
                <td style="width: 200px;" valign="top">
                    <label><?php _e('Status:', 'woocommerce-events'); ?></label><Br />
                </td>
                <td>
                    <?php if(empty($WooCommerceEventsMultidayStatus)) :?>
                        <?php if(!empty($WooCommerceEventsStatus)) :?>
                             <?php echo esc_attr($WooCommerceEventsStatus).' '; ?>
                        <?php endif; ?> 
                    <?php else :?>
                        <?php echo $WooCommerceEventsMultidayStatus; ?>
                    <?php endif; ?> 
                </td>
            </tr>
            <?php if(!empty($WooCommerceEventsTicketType)) :?>
            <tr valign="top">  
                <td style="width: 200px;">
                    <label><?php _e('Ticket Type:', 'woocommerce-events'); ?></label><Br />
                </td>
                <td>
                   <?php if(!empty($WooCommerceEventsTicketType)) :?>
                    <?php echo esc_attr($WooCommerceEventsTicketType).' '; ?>
                   <?php endif; ?> 
                </td>
            </tr>
            <?php endif; ?>
            <?php if(!empty($WooCommerceEventsVariations)) :?>
                <?php foreach($WooCommerceEventsVariations as $variationName => $variationValue) :?>
                    <?php 
                        $variationNameOutput = str_replace('attribute_', '', $variationName);
                        $variationNameOutput = str_replace('pa_', '', $variationNameOutput);
                        $variationNameOutput = str_replace('_', ' ', $variationNameOutput);
                        $variationNameOutput = str_replace('-', ' ', $variationNameOutput);
                        $variationNameOutput = str_replace('Pa_', '', $variationNameOutput);
                        $variationNameOutput = ucwords($variationNameOutput);

                        $variationValueOutput = str_replace('_', ' ', $variationValue);
                        $variationValueOutput = str_replace('-', ' ', $variationValueOutput);
                        $variationValueOutput = ucwords($variationValueOutput);

                    ?>
                    <?php if($variationNameOutput != 'Ticket Type') :?>
                    <tr valign="top"> 
                        <td style="width: 200px;">
                            <label><?php echo urldecode($variationNameOutput); ?>:</label><Br />
                        </td>
                        <td>
                            <?php echo urldecode($variationValueOutput); ?>
                        </td>
                    </tr>
                    <?php endif;  ?>
                <?php endforeach; ?>
            <?php endif; ?>
            <tr valign="top">  
                <td style="width: 200px;">
                    <label><?php _e('Barcode:', 'woocommerce-events'); ?></label><Br />
                </td>
                <td>
                    <img src="<?php echo esc_url($barcodeURL); ?><?php echo esc_attr($barcodeFileName); ?>.png" />
                </td>
            </tr>
	</tbody>
	</tbody>
</table>
<h2>Purchaser</h2>
<table class="form-table">
	<tbody>     
            <tr valign="top">  
                <td style="width: 200px;">
                    <label><?php _e('Name:', 'woocommerce-events'); ?></label><Br />
                </td>
                <td>
                   <?php if(!empty($purchaser['customerFirstName'])) :?>
                    <?php echo esc_attr($purchaser['customerFirstName']).' '; ?>
                   <?php endif; ?> 
                    <?php if(!empty($purchaser['customerLastName'])) :?>
                    <?php echo esc_attr($purchaser['customerLastName']).' '; ?>
                   <?php endif; ?>
                   <?php if(!empty($purchaser['customerID'])) :?> 
                   <?php echo ' - <a href="'.get_site_url().'/wp-admin/user-edit.php?user_id='.$post->post_author.'">Profile</a>';  ?> 
                   <?php endif; ?> 
                </td>
            </tr>
            <tr valign="top">  
                <td style="width: 200px;"> 
                    <label><?php _e('Email:', 'woocommerce-events'); ?></label><Br />
                </td>
                <td>
                   <?php if(!empty($purchaser['customerEmail'])) :?>
                    <?php echo '<a href="mailto:'.esc_attr($purchaser['customerEmail']).'">'.esc_attr($purchaser['customerEmail']).'</a>'; ?>
                   <?php endif; ?> 
                </td>
            </tr>
            <tr valign="top">  
                <td style="width: 200px;">
                    <label><?php _e('Phone:', 'woocommerce-events'); ?></label><Br />
                </td>
                <td>
                   <?php if(!empty($purchaser['customerPhone'])) :?>
                    <?php echo esc_attr($purchaser['customerPhone']);  ?>
                   <?php endif; ?> 
                </td>
            </tr>
	</tbody>
</table>
<h2><?php echo $attendeeTerm; ?></h2>
<table class="form-table">
	<tbody>     
            <tr valign="top">  
                <td style="width: 200px;">
                    <label><?php _e('First Name:', 'woocommerce-events'); ?></label><Br />
                </td>
                <td>
                   <?php if(!empty($WooCommerceEventsAttendeeName)) :?>
                       <?php echo esc_attr($WooCommerceEventsAttendeeName); ?>
                   <?php endif; ?> 
                </td>
            </tr>
            <tr valign="top">  
                <td style="width: 200px;">
                    <label><?php _e('Last Name:', 'woocommerce-events'); ?></label><Br />
                </td>
                <td>
                   <?php if(!empty($WooCommerceEventsAttendeeLastName)) :?>
                       <?php echo esc_attr($WooCommerceEventsAttendeeLastName); ?>
                   <?php endif; ?> 
                </td>
            </tr>
            <tr valign="top">  
                <td style="width: 200px;"> 
                    <label><?php _e('Email:', 'woocommerce-events'); ?></label><Br />
                </td>
                <td>
                    <?php if(!empty($WooCommerceEventsAttendeeEmail)) :?>
                        <?php echo esc_attr($WooCommerceEventsAttendeeEmail); ?>
                    <?php endif; ?> 
                </td>
            </tr>
            <tr valign="top">  
                <td style="width: 200px;"> 
                    <label><?php _e('Telephone:', 'woocommerce-events'); ?></label><Br />
                </td>
                <td>
                    <?php if(!empty($WooCommerceEventsCaptureAttendeeTelephone)) :?>
                        <?php echo esc_attr($WooCommerceEventsCaptureAttendeeTelephone); ?>
                    <?php endif; ?> 
                </td>
            </tr>
            <tr valign="top">  
                <td style="width: 200px;"> 
                    <label><?php _e('Company:', 'woocommerce-events'); ?></label><Br />
                </td>
                <td>
                    <?php if(!empty($WooCommerceEventsCaptureAttendeeCompany)) :?>
                        <?php echo esc_attr($WooCommerceEventsCaptureAttendeeCompany); ?>
                    <?php endif; ?> 
                </td>
            </tr>
            <tr valign="top">  
                <td style="width: 200px;"> 
                    <label><?php _e('Designation:', 'woocommerce-events'); ?></label><Br />
                </td>
                <td>
                    <?php if(!empty($WooCommerceEventsCaptureAttendeeDesignation)) :?>
                        <?php echo esc_attr($WooCommerceEventsCaptureAttendeeDesignation); ?>
                    <?php endif; ?> 
                </td>
            </tr>
	</tbody>
</table>
<h2>Event</h2>
<table class="form-table">
	<tbody>     
            <tr valign="top">  
                <td style="width: 200px;">
                    <label><?php _e('Event:', 'woocommerce-events'); ?></label><Br />
                </td>
                <td>
                   <?php if(!empty($WooCommerceEventsTitle)) :?>
                    <?php echo esc_attr($WooCommerceEventsTitle).' '; ?>
                   <?php endif; ?> 
                </td>
            </tr>
            <tr valign="top">  
                <td style="width: 200px;">
                    <label><?php _e('Date:', 'woocommerce-events'); ?></label><Br />
                </td>
                <td>
                   <?php if(!empty($WooCommerceEventsDate)) :?>
                    <?php echo esc_attr($WooCommerceEventsDate).' '; ?>
                   <?php endif; ?> 
                </td>
            </tr>
            <tr valign="top">  
                <td style="width: 200px;">
                    <label><?php _e('Start time:', 'woocommerce-events'); ?></label><Br />
                </td>
                <td>
                   <?php if(!empty($WooCommerceEventsHour) && !empty($WooCommerceEventsMinutes)) :?>
                    <?php echo esc_attr($WooCommerceEventsHour).':'.esc_attr($WooCommerceEventsMinutes); ?>
                   <?php endif; ?> 
                </td>
            </tr>
            <tr valign="top">  
                <td style="width: 200px;">
                    <label><?php _e('End time:', 'woocommerce-events'); ?></label><Br />
                </td>
                <td>
                   <?php if(!empty($WooCommerceEventsHourEnd) && !empty($WooCommerceEventsMinutesEnd)) :?>
                    <?php echo esc_attr($WooCommerceEventsHourEnd).':'.esc_attr($WooCommerceEventsMinutesEnd); ?>
                   <?php endif; ?> 
                </td>
            </tr>
            <tr valign="top">  
                <td style="width: 200px;">
                    <label><?php _e('Venue:', 'woocommerce-events'); ?></label><Br />
                </td>
                <td>
                   <?php if(!empty($WooCommerceEventsLocation)) :?>
                    <?php echo esc_attr($WooCommerceEventsLocation).' '; ?>
                   <?php endif; ?> 
                </td>
            </tr>
            <tr valign="top">  
                <td style="width: 200px;">
                    <label><?php _e('GPS Coordinates:', 'woocommerce-events'); ?></label><Br />
                </td>
                <td>
                   <?php if(!empty($WooCommerceEventsGPS)) :?>
                    <?php echo esc_attr($WooCommerceEventsGPS).' '; ?>
                   <?php endif; ?> 
                </td>
            </tr>
            <tr valign="top">  
                <td style="width: 200px;">
                    <label><?php _e('Phone:', 'woocommerce-events'); ?></label><Br />
                </td>
                <td>
                   <?php if(!empty($WooCommerceEventsSupportContact)) :?>
                    <?php echo esc_attr($WooCommerceEventsSupportContact).' '; ?>
                   <?php endif; ?> 
                </td>
            </tr>
            <tr valign="top">  
                <td style="width: 200px;">
                    <label><?php _e('Email:', 'woocommerce-events'); ?></label><Br />
                </td>
                <td>
                   <?php if(!empty($WooCommerceEventsEmail)) :?>
                    <?php echo esc_attr($WooCommerceEventsEmail).' '; ?>
                   <?php endif; ?> 
                </td>
            </tr>
	</tbody>
</table>