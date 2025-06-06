<?php

namespace theme_apoa\output\core\mainpage;

defined('MOODLE_INTERNAL') || die;



class mainpage implements \templatable , \renderable {


    use mainpage_named_templatable;

    protected array $sections;
    
    public function __construct() {
        $this->sections = ['jumbo', 'subjumbo', 'events', 'about', 'sections', 'membership', 'resources'];
    }
    
        
    public function export_for_template(\renderer_base $output) {

        $sections = ['jumbo', 'subjumbo', 'about','membership', 'events', 'sections', 'resources'];
        $containers = [];
        foreach ($sections as $section) {
            $item = new mainpagecontainer($section);
            array_push($containers, array(
                'data' =>  $item->export_for_template($output),
                'extraclasses' => $item->get_extra_classes(),
            ));
        }
        $template['containers'] = $containers;
        return $template;
    }


    public function get_sections(){
        return $this->sections;
    }
    
}