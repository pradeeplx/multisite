(function($) {
    
    if ( $( "#WooCommerceEventsMetaEvent" ).length ) {

        checkEventForm();
		
        $('#WooCommerceEventsMetaEvent').change(function() {

                checkEventForm();

        })
    
        if( (typeof localObj === "object") && (localObj !== null) )
        {

            jQuery('.WooCommerceEventsMetaBoxDate').datepicker({

                showButtonPanel: true,
                closeText: localObj.closeText,
                currentText: localObj.currentText,
                monthNames: localObj.monthNames,
                monthNamesShort: localObj.monthNamesShort,
                dayNames: localObj.dayNames,
                dayNamesShort: localObj.dayNamesShort,
                dayNamesMin: localObj.dayNamesMin,
                dateFormat: localObj.dateFormat,
                firstDay: localObj.firstDay,
                isRTL: localObj.isRTL,

            });

        } else {

            jQuery('#WooCommerceEventsMetaBoxDate').datepicker();

        }
    
    }
    
    if ( $( "#fooevents-calendar-options-page" ).length ) {
        
        jQuery('.wrap').on('click', '#fooevents-eventbrite-import', function(e) {
            
            jQuery('#fooevents-eventbrite-import-output').html('Fetching...');
            
            var data = {
                'action': 'fooevents-eventbrite-import',
                'import': true
            };

            // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            jQuery.post(ajaxurl, data, function(response) {
                
                jQuery('#fooevents-eventbrite-import-output').html(response);
                
            });
            
            return false;
        });
        
    }
    
    function checkEventForm() {

        var WooCommerceEventsEvent = $('#WooCommerceEventsMetaEvent').val();

        if(WooCommerceEventsEvent == 'Event') {

                jQuery('#WooCommerceEventsMetaForm').show();

        } else {

                jQuery('#WooCommerceEventsMetaForm').hide();

        }

    } 
    
})(jQuery);

