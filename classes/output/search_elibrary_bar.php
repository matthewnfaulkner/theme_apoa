<?php

namespace theme_apoa\output;

use core_course_category;

defined('MOODLE_INTERNAL') || die;



class search_elibrary_bar implements \templatable {


    protected \theme_apoa\form\searchelibrary_form $mform;

    protected core_course_category $coursecat;

    public function __construct(core_course_category $coursecat) {
        $this->coursecat = $coursecat;
        $params = array(
            'categoryid' => $this->coursecat->id
        );
        $this->mform = new \theme_apoa\form\searchelibrary_form(null, $params);
    }
    
        
    
    public function export_for_template(\renderer_base $output) {
        $template = $this->mform->render();
        
    }



    
}