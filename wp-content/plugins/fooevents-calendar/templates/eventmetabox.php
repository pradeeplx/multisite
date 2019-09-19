<div class="options_group">
        <p class="form-field">
               <label><?php _e('Is this post an event?:', 'fooevents-calendar'); ?></label>
               <select name="WooCommerceEventsEvent" id="WooCommerceEventsMetaEvent">
                    <option value="NotEvent" <?php echo ($WooCommerceEventsEvent == 'NotEvent')? 'SELECTED' : '' ?>><?php _e('No', 'fooevents-calendar'); ?></option>
                    <option value="Event" <?php echo ($WooCommerceEventsEvent == 'Event')? 'SELECTED' : '' ?>><?php _e('Yes', 'fooevents-calendar'); ?></option>
               </select>
               <img class="help_tip" data-tip="<?php _e('Enable this option to add event and ticketing features.', 'woocommerce-events'); ?>" src="<?php echo plugins_url(); ?>/woocommerce/assets/images/help.png" height="16" width="16" />
        </p>
</div>
<div id="WooCommerceEventsMetaForm" style="display:none;">
    <?php echo $numDays; ?>
    <?php echo $multiDayType; ?>
    <div class="options_group" id="WooCommerceEventsDateContainer">
        <p class="form-field">
            <label><?php _e('Start Date:', 'fooevents-calendar'); ?></label>
            <input type="text" id="WooCommerceEventsMetaBoxDate" class="WooCommerceEventsMetaBoxDate" name="WooCommerceEventsDate" value="<?php echo esc_html($WooCommerceEventsDate); ?>"/>
            <img class="help_tip" data-tip="<?php _e('The date that the event is scheduled to take place. This is used as a label on the frontend of the website. FooEvents Calendar uses this to display the event.', 'woocommerce-events'); ?>" src="<?php echo plugins_url(); ?>/woocommerce/assets/images/help.png" height="16" width="16" />
        </p>
    </div>
    <?php echo $endDate; ?>
    <div class="options_group">
        <p class="form-field">
            <label><?php _e('Start time:', 'fooevents-calendar'); ?></label><br />
            <select name="WooCommerceEventsHour" id="WooCommerceEventsHour">
                <?php for($x=0; $x<=23; $x++) :?>
                <?php $x = sprintf("%02d", $x); ?>
                <option value="<?php echo $x; ?>" <?php echo ($WooCommerceEventsHour == $x) ? 'SELECTED' : ''; ?>><?php echo $x; ?></option>
                <?php endfor; ?>
            </select>
            <select name="WooCommerceEventsMinutes" id="WooCommerceEventsMinutes">
                <?php for($x=0; $x<=59; $x++) :?>
                <?php $x = sprintf("%02d", $x); ?>
                <option value="<?php echo $x; ?>" <?php echo ($WooCommerceEventsMinutes == $x) ? 'SELECTED' : ''; ?>><?php echo $x; ?></option>
                <?php endfor; ?>
            </select>
            <select name="WooCommerceEventsPeriod" id="WooCommerceEventsPeriod">
                <option value="">-</option>
                <option value="a.m." <?php echo ($WooCommerceEventsPeriod == 'a.m.') ? 'SELECTED' : ''; ?>>a.m.</option>
                <option value="p.m." <?php echo ($WooCommerceEventsPeriod == 'p.m.') ? 'SELECTED' : ''; ?>>p.m.</option>
            </select>
            <img class="help_tip" data-tip="<?php _e('The time that the event is scheduled to start', 'woocommerce-events'); ?>" src="<?php echo plugins_url(); ?>/woocommerce/assets/images/help.png" height="16" width="16" />
        </p>
    </div>
    <div class="options_group">
        <p class="form-field">
            <label><?php _e('End time:', 'fooevents-calendar'); ?></label><br />
            <select name="WooCommerceEventsHourEnd" id="WooCommerceEventsHourEnd">
                <?php for($x=0; $x<=23; $x++) :?>
                <?php $x = sprintf("%02d", $x); ?>
                <option value="<?php echo $x; ?>" <?php echo ($WooCommerceEventsHourEnd == $x) ? 'SELECTED' : ''; ?>><?php echo $x; ?></option>
                <?php endfor; ?>
            </select>
            <select name="WooCommerceEventsMinutesEnd" id="WooCommerceEventsMinutesEnd">
                <?php for($x=0; $x<=59; $x++) :?>
                <?php $x = sprintf("%02d", $x); ?>
                <option value="<?php echo $x; ?>" <?php echo ($WooCommerceEventsMinutesEnd == $x) ? 'SELECTED' : ''; ?>><?php echo $x; ?></option>
                <?php endfor; ?>
            </select>
            <select name="WooCommerceEventsEndPeriod" id="WooCommerceEventsEndPeriod">
                <option value="">-</option>
                <option value="a.m." <?php echo ($WooCommerceEventsEndPeriod == 'a.m.') ? 'SELECTED' : ''; ?>>a.m.</option>
                <option value="p.m." <?php echo ($WooCommerceEventsEndPeriod == 'p.m.') ? 'SELECTED' : ''; ?>>p.m.</option>
            </select>
            <img class="help_tip" data-tip="<?php _e('The time that the event is scheduled to end', 'woocommerce-events'); ?>" src="<?php echo plugins_url(); ?>/woocommerce/assets/images/help.png" height="16" width="16" />
        </p>
    </div>
    <?php echo $eventbrite_option; ?>
</div>    
<div style="height:100px;"></div>