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

        $this->sections = [];
        $this->regions = ['elibrary', 'subjumbo1', 'subjumbo2', 'subjumbo3', 'sections'];
    }
        
    
    public function export_for_template(\renderer_base $output) {

        $template = $this->get_content($output);

        return $template;

    }

    protected function get_content(\renderer_base $output) {
        global $CFG;

        $template = [];
        foreach($this->regions as $region){
            $blockhelper = new frontpage_cache_helper($region);
            $template[$region] = ['blocks' => $blockhelper];
        }

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

        return $template;
    }
    
}