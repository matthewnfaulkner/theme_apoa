<?php

namespace theme_apoa\output\core\mainpage;

defined('MOODLE_INTERNAL') || die;



class sections implements \templatable , \renderable {


    use mainpage_named_templatable;



    


    protected string $contentgenerator;

    public function __construct() {
    }
        
    
    public function export_for_template(\renderer_base $output) {

        global $CFG;

        $sectionid = get_config('theme_apoa', 'Sectionsid');
        $top = \core_course_category::get($sectionid);
        $categories = $top->get_children();
        $template = ['sections' => []];
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