<?php

namespace theme_apoa\output\core\mainpage;

use core_course_category;

defined('MOODLE_INTERNAL') || die;



class about implements \templatable , \renderable {


    use mainpage_named_templatable;





    public function __construct() {
    }
        
    
    public function export_for_template(\renderer_base $output) {

        $template = $this->get_content();

        return $template;

    }

    protected function get_content() {
        
        $template = [];

        $img = theme_apoa_get_file_from_setting('about');
        
        if($aboutid = get_config('theme_apoa', 'aboutid')) {
            $aboutcat = core_course_category::get($aboutid);
            $url = $aboutcat->get_view_link();
            $template = array('abouttitle' => $aboutcat->name,
                            'abouturl' => $url,
                            'aboutdesc' => $aboutcat->description,
                            'aboutimg' => $img);
        }
        return $template;
    }
}