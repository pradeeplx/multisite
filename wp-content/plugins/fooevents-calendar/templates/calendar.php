<div id='<?php echo $calendar_id; ?>' class="fooevents_calendar" style="clear:both"></div>
<script>
(function($) {

    var localObj = '<?php echo $localArgs['json_events']; ?>';
    var settings = JSON.parse(localObj);    
    
    if( $('#'+settings.id).length ) {
        
        jQuery('#'+settings.id).fullCalendar(settings);
        
    } 

})(jQuery);
</script>