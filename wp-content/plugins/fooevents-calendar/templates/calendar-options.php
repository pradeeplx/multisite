<div class="wrap" id="fooevents-calendar-options-page">
    <h1>FooEvents Calendar Settings</h1>
    <form method="post" action="options.php">
        <?php settings_fields('fooevents-calendar-settings-group'); ?>
        <?php do_settings_sections('fooevents-calendar-settings-group'); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('Eventbrite private token', 'fooevents-calendar'); ?></th>
                <td><input type="text" name="globalFooEventsEventbriteToken" id="globalFooEventsEventbriteToken" value="<?php echo esc_html($globalFooEventsEventbriteToken); ?>"></td>
                <td><?php _e('Optional API key used to add events to Eventbrite.', 'fooevents-calendar'); ?></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Enable 24 hour time format', 'fooevents-calendar'); ?></th>
                <td><input type="checkbox" name="globalFooEventsTwentyFourHour" id="globalFooEventsTwentyFourHour" value="yes" <?php echo $globalFooEventsTwentyFourHourChecked; ?>></td>
                <td><?php _e('Uses 24 hour time format on the calendar.', 'fooevents-calendar'); ?></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Only display start day', 'fooevents-calendar'); ?></th>
                <td><input type="checkbox" name="globalFooEventsStartDay" id="globalFooEventsStartDay" value="yes" <?php echo $globalFooEventsStartDayChecked; ?>></td>
                <td><?php _e('When multi-day plugin is active only display the event start day.', 'fooevents-calendar'); ?></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Enable full day events', 'fooevents-calendar'); ?></th>
                <td><input type="checkbox" name="globalFooEventsAllDayEvent" id="globalFooEventsAllDayEvent" value="yes" <?php echo $globalFooEventsAllDayEventChecked; ?>></td>
                <td><?php _e('Removes event time from calendar entry titles.', 'fooevents-calendar'); ?></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Calendar theme', 'fooevents-calendar'); ?></th>
                <td>
                    <select name="globalFooEventsCalendarTheme" id="globalFooEventsCalendarTheme">
                        <option value="default" <?php echo $globalFooEventsCalendarTheme == 'default' ? "Selected" : "" ?>>Default</option>
                        <option value="light" <?php echo $globalFooEventsCalendarTheme == 'light' ? "Selected" : "" ?>>Light</option>
                        <option value="dark" <?php echo $globalFooEventsCalendarTheme == 'dark' ? "Selected" : "" ?>>Dark</option>
                        <option value="flat" <?php echo $globalFooEventsCalendarTheme == 'flat' ? "Selected" : "" ?>>Flat</option>
                        <option value="minimalist" <?php echo $globalFooEventsCalendarTheme == 'minimalist' ? "Selected" : "" ?>>Minimalist</option>
                    </select>
                </td>
                <td><?php _e('Selects calendar theme to be used on Wordpress frontend.', 'fooevents-calendar'); ?></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Events list theme', 'fooevents-calendar'); ?></th>
                <td>
                    <select name="globalFooEventsCalendarListTheme" id="globalFooEventsCalendarListTheme">
                        <option value="default" <?php echo $globalFooEventsCalendarListTheme == 'default' ? "Selected" : "" ?>>Default</option>
                        <option value="light-card" <?php echo $globalFooEventsCalendarListTheme == 'light-card' ? "Selected" : "" ?>>Light Card</option>
                        <option value="dark-card" <?php echo $globalFooEventsCalendarListTheme == 'dark-card' ? "Selected" : "" ?>>Dark Card</option>
                    </select>
                </td>
                <td><?php _e('Selects events list theme to be used on Wordpress frontend.', 'fooevents-calendar'); ?></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Associate with post types', 'fooevents-calendar'); ?></th>
                <td>
                    <select multiple name="globalFooEventsCalendarPostTypes[]" id="globalFooEventsCalendarPostTypes">
                        <?php foreach($post_types as $post_type) :?>
                        <option value="<?php echo $post_type; ?>" <?php echo in_array($post_type, $globalFooEventsCalendarPostTypes) || empty($globalFooEventsCalendarPostTypes) ? "Selected" : "" ?>><?php echo $post_type; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td><?php _e('Selects which custom post types can be events.', 'fooevents-calendar'); ?></td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
    <?php if(!empty($globalFooEventsEventbriteToken)) :?>
    <h1>Eventbrite Import</h1>
    <div id="fooevents-eventbrite-import-output"></div>
    <table class="form-table">
        <tr>
            <td><a class="button" id="fooevents-eventbrite-import" href="#">Import</a></td>
        </tr>
    </table>
    <?php endif; ?>
</div>
