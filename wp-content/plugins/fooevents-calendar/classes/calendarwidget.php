<?php
class fooevents_calendar_widget extends WP_Widget {

     /**
      * Initialize widget
      * 
      */
    function __construct() {
        parent::__construct(
        false,
        __( 'FooEvents Calendar', 'fooevents-calendar' ),
        array( 'description' => __( "A calendar or list view of your site's Events.", 'fooevents-calendar' ) ) );
    }
    
    /**
     * Output widget to screen
     * 
     * @param array $args
     * @param array $instance
     */
    function widget($args, $instance) 
    { 
        extract( $args ); 
        
        $type = '';
        if(!empty($instance['type'])) {
            
            $type      = esc_attr($instance['type']);     
            
        }
        
        $title = '';
        if(!empty($instance['title'])) {
            
            $title      = esc_attr($instance['title']);  
            
        }
        
        $number_of_events = 5;
        if(!empty($instance['number_of_events'])) {
            
            $number_of_events      = esc_attr($instance['number_of_events']);  
            
        }
        
        $sort = 'asc';
        if(!empty($instance['sort'])) {
            
            $sort      = esc_attr($instance['sort']);  
            
        }
        
        $start_date = '';
        if (!empty($instance['start_date'])) {
             
            $start_date  = esc_attr($instance['start_date']);  
            
        } 
        
        echo $before_widget;
        
        if(!empty($title)) {
            
            echo '<h2 class="widget-title">'.$title.'</h2>';
            
        }  
        
        $defaultDate_output = '';
        if(!empty($start_date)) {
            
            
            $defaultDate_output='defaultDate="'.$start_date.'"';
            
        } 

        if($type == 'Calendar') {
            
            $id = rand(1111, 9999);
            do_shortcode('[fooevents_calendar id="'.$id.'"  header="left: \'title\'; center: \'\'; right: \'prev,next\'" '.$defaultDate_output.' type="'.$type.'"]'); 
            
        }
        
        if($type == 'List') {
       
            do_shortcode('[fooevents_events_list  num="'.$number_of_events.'" type="'.$type.'" sort="'.$sort.'"]'); 
            
        }
        
        echo $after_widget; 
    }
    
    /**
     * Update widget options
     * 
     */
    function update($new_instance, $old_instance) 
    {                
        return $new_instance;
    }
    
    /**
     * Admin update form
     * 
     * @param array $instance
     */
    function form($instance) 
    {   
        
        $type = '';
        if(!empty($instance['type'])) {
            
            $type      = esc_attr($instance['type']);     
            
        }
        
        $title = '';
        if(!empty($instance['title'])) {
            
            $title      = esc_attr($instance['title']);  
            
        }
        
        $number_of_events = '';
        if(!empty($instance['number_of_events'])) {
            
            $number_of_events      = esc_attr($instance['number_of_events']);  
            
        }
        
        $start_date = '';
        if (!empty($instance['start_date'])) {
             
            $start_date  = esc_attr($instance['start_date']);  
            
        }
        
        $sort = '';
        if (!empty($instance['sort'])) {
             
            $sort  = esc_attr($instance['sort']);  
            
        }
        ?>  
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title','fooevents-calendar'); ?>:</label><br />
            <input type="text" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" value="<?php echo $title; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('type'); ?>"><?php _e('Layout Type','fooevents-calendar'); ?>:</label><br />
            <select name="<?php echo $this->get_field_name('type'); ?>" id="<?php echo $this->get_field_id('type'); ?>">
                <?php if(!empty($type)) { ?>
                    <option value="<?php echo $type; ?>">Current: <?php echo $type; ?></option>
                <?php } ?>                
                <option value="Calendar">Calendar</option>
                <option value="List">List view</option>
            </select>
        </p>
        <?php if($type=='Calendar') { ?>
            <p>
                <label for="<?php echo $this->get_field_id('start_date'); ?>"><?php _e('Default date of calendar view','fooevents-calendar'); ?>(optional):</label>
                <textarea placeholder="Example: 2016-09-01" name="<?php echo $this->get_field_name('start_date'); ?>" class="widefat" id="<?php echo $this->get_field_id('start_date'); ?>"><?php echo $start_date; ?></textarea>
                <span class="description">If empty, calendar will default to current date.</span>
            </p>   
        <?php } ?>
        <?php if($type=='List') { ?>
            <p>
                <label for="<?php echo $this->get_field_id('number_of_events'); ?>"><?php _e('Number of events to display','fooevents-calendar'); ?>:</label><br />
                <input type="text" name="<?php echo $this->get_field_name('number_of_events'); ?>" id="<?php echo $this->get_field_id('number_of_events'); ?>" value="<?php echo $number_of_events; ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('sort'); ?>"><?php _e('Sort','fooevents-calendar'); ?>:</label><br />
                <select name="<?php echo $this->get_field_name('sort'); ?>" id="<?php echo $this->get_field_id('sort'); ?>">
                    <?php if(!empty($sort)) { ?>
                        <option value="<?php echo $sort; ?>">Current: <?php echo strtoupper($sort); ?></option>
                    <?php } ?>                
                    <option value="asc">ASC</option>
                    <option value="desc">DESC</option>
                </select>
            </p>
        <?php } ?>
             
        
        <?php
    }
    
}

register_widget('fooevents_calendar_widget');