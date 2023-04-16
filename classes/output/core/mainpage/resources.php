<?php

namespace theme_apoa\output\core\mainpage;

defined('MOODLE_INTERNAL') || die;



class resources implements \templatable , \renderable {


    use mainpage_named_templatable;



    


    protected string $contentgenerator;

    public function __construct() {
    }
        
    
    public function export_for_template(\renderer_base $output) {

        $template = $this->get_content();

        return $template;

    }

    protected function get_content() {

        global $CFG;

        $img = theme_apoa_get_file_from_setting('resources');
        
        return array('resourceimg' => $img);

    }
    
}