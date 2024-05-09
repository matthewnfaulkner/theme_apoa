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
        $this->regions = ['elibrary', 'subjumbo1', 'subjumbo2', 'subjumbo3', 'sections'];
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
        $sectionid = get_config('theme_apoa', 'Sectionsid');
        $top = \core_course_category::get($sectionid);
        $categories = $top->get_children();
        $template['sections'] = [];
        foreach ($categories as $category){
        $elibraryid = get_config('theme_apoa', 'elibraryid');
        $newsletterid = get_config('theme_apoa', 'newsletterid');
        if ($category->id != $elibraryid && $category->id != $newsletterid) {
            $sectionname = $category->name;
            $img = theme_apoa_get_file_from_setting('sectionlogo' . $category->id);
            $url = new \moodle_url($CFG->wwwroot . "/course/index.php?categoryid=" . $category->id);
            array_push($template['sections'], array('sectionname' => $sectionname,
            'sectionimg' => $img,
            'sectionurl' => $url));
            }
        }
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