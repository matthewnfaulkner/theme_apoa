<?php

namespace theme_apoa\output\core\mainpage;

defined('MOODLE_INTERNAL') || die;

use \theme_apoa\helper\frontpage_cache_helper as frontpage_cache_helper;

class subjumbo implements \templatable , \renderable {


    use mainpage_named_templatable;

    protected array $sections;

    protected array $regions;
    
     /** @var string the item output class name */
    protected string $itemclass;

    public function __construct() {

        $this->sections = ['Section Highlights' => 'course_list', 'Newsletter' => 'newsletter', 'E-library' => 'elibrary'];
        $this->regions = ['elibrary', 'subjumbo1', 'subjumbo2'];
    }
        
    
    public function export_for_template(\renderer_base $output) {

        $template = $this->get_content($output);

        return $template;

    }

    protected function get_content(\renderer_base $output) {
        global $CFG, $PAGE;

        $template = [];
        foreach($this->regions as $region){
            $blockhelper = new frontpage_cache_helper($region);
            $template[$region] = ['blocks' => $blockhelper];
        }
        $type = 'elibrary';
        $key = 'E-library';
        $this->itemclass = "theme_apoa\\output\\core\\lists\\course_list";
        $subjumboclass = new $this->itemclass($type, $key);
        $subjumbolist = $subjumboclass->export_for_template($output);
        $onlyalpha = preg_replace("/[^a-zA-Z0-9]+/", "", $key);
        $template[$onlyalpha] = ['content' => $subjumbolist,
                'sectiontitle' => $key,
                'sectionmore' => "more " . $key,
                'sectionurl' => $subjumboclass->redirecturl];
        /*foreach ($this->sections as $key => $type) {
            $this->itemclass = "theme_apoa\\output\\core\\lists\\course_list";
            $subjumboclass = new $this->itemclass($type, $key);
            $subjumbolist = $subjumboclass->export_for_template($output);
            $onlyalpha = preg_replace("/[^a-zA-Z0-9]+/", "", $key);
            $template[$onlyalpha] = ['content' => $subjumbolist,
                    'sectiontitle' => $key,
                    'sectionmore' => "more " . $key,
                    'sectionurl' => $subjumboclass->redirecturl];
            
        }*/

        return $template;
    }
    
}