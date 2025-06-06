<?php

namespace theme_apoa\output\core\mainpage;

use moodle_url;

defined('MOODLE_INTERNAL') || die;



class membership implements \templatable , \renderable {


    use mainpage_named_templatable;



   


    protected string $contentgenerator;

    public function __construct() {
    }
        
    
    public function export_for_template(\renderer_base $output) {

        $template = $this->get_content();

        return $template;

    }

    protected function get_content() {
        return array('loginurl' => new moodle_url('login/index.php'),
                'membershipsurl' => new moodle_url('auth/apoa/signup.php'));
    }
    
}