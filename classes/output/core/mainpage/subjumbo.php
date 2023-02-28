<?php

namespace theme_apoa\output\core\mainpage;

defined('MOODLE_INTERNAL') || die;



class subjumbo implements \templatable , \renderable {


    use mainpage_named_templatable;

    protected array $sections;
    
     /** @var string the item output class name */
     protected string $itemclass;

    public function __construct() {

        $this->sections = ['Mainpage' => 'course_list', 'Newsletter' => 'course_list', 'E-library' => 'course_list'];
    }
        
    
    public function export_for_template(\renderer_base $output) {

        $template = $this->get_content($output);

        return $template;

    }

    protected function get_content(\renderer_base $output) {
        global $CFG;

        $template = [];
        foreach ($this->sections as $key => $type) {
            $this->itemclass = "theme_apoa\\output\\core\\lists\\" . $type;
            $subjumboclass = new $this->itemclass($type, $key);
            $subjumbolist = $subjumboclass->export_for_template($output);
            $template[$key] = ['content' => $subjumbolist,
                    'sectiontitle' => $key,
                    'sectionmore' => "more " . $key];
            
        }

        return $template;
    }
    
}