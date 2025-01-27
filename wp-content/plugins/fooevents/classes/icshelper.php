<?php if ( ! defined( 'ABSPATH' ) ) exit;
class FooEvents_ICS_helper {
    
    public $data;
    public $name;
    public $Config;

    function __construct($Config) {
        
        $this->Config = $Config;
        
    }
    
    /**
     * Builds add to calendar .ics file
     * 
     * @param string $start
     * @param string $end
     * @param string $name
     * @param string $description
     * @param string $location
     */
    function build_ICS($start,$end,$name,$description,$location = '') {
        
        $this->name = $name;
        
        if(empty($this->name)) {
            
            $this->name = 'Event';
            
        }
        
        $start = (string) date("Ymd\THis",strtotime($start));
        $end = (string) date("Ymd\THis",strtotime($end));
        
        $this->data .= "VERSION:2.0\nMETHOD:PUBLISH\nBEGIN:VEVENT\nDTSTART:".$start."\nDTEND:".$end."\nLOCATION:".$location."\nTRANSP: OPAQUE\nSEQUENCE:0\nUID:\nDTSTAMP:".date("Ymd\THis")."\nSUMMARY:".$name."\nDESCRIPTION:".$description."\nPRIORITY:1\nCLASS:PUBLIC\nBEGIN:VALARM\nTRIGGER:-PT10080M\nACTION:DISPLAY\nDESCRIPTION:Reminder\nEND:VALARM\nEND:VEVENT\n";
    
    }
    
    /**
     * Saves ICS file.
     * 
     */
    function save() {
        
        file_put_contents($this->name.".ics",$this->data);
        
    }
    
    /**
     * Download the ICS file.
     * 
     */
    function show() {
        
        $data = "BEGIN:VCALENDAR\n".$this->data."END:VCALENDAR\n";
        
        header("Content-type:text/calendar");
        header('Content-Disposition: attachment; filename="'.$this->name.'.ics"');
        Header('Content-Length: '.strlen($data));
        Header('Connection: close');
        echo $data;
        
    }
}