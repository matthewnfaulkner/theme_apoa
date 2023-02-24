<?php

namespace theme_apoa\output\core\listitems;

use stdClass;

defined('MOODLE_INTERNAL') || die;



class jumboside_list_item implements \templatable , \renderable {


    use \theme_apoa\output\core\mainpage\mainpage_named_templatable;



    protected \core_course_list_element $course;

    protected \core_tag_tag $tag;

    public function __construct(\core_course_list_element $course, $tag) {

        $this->course = $course;
        $this->tag = $tag;

        
    }
        
    
    public function export_for_template(\renderer_base $output) {

        global $CFG;

        $coursecat = \core_course_category::get($this->course->category);
        $rootcat = $coursecat->get_parent_coursecat();
        
        while ($rootcat->get_parent_coursecat() != \core_course_category::top()){
            $rootcat = $rootcat->get_parent_coursecat();
        }

        $wwwroot = $CFG->wwwroot;

        $itemurl = $wwwroot . "/course/view.php?id=" . $this->course->id;
        $caturl  = $coursecat->get_view_link();
        $rooturl  = $rootcat->get_view_link();

        foreach ($this->course->get_course_overviewfiles() as $file) {
            $isimage = $file->is_valid_image();
            $img = \moodle_url::make_file_url("$CFG->wwwroot/pluginfile.php",
                '/' . $file->get_contextid() . '/' . $file->get_component() . '/' .
                $file->get_filearea() . $file->get_filepath() . $file->get_filename(), !$isimage);
            if ($isimage) {
                break;
            }
        }

        $template = ["itemtitle" => $this->course->shortname,
            "itemcat" => $coursecat->name,
            "itemroot" => $rootcat->name,
            "itemurl" => $itemurl,
            "itemcaturl" => $caturl,
            "itemrooturl" => $rooturl,
            "itemimg" => $img
        ];

        return $template;

    }

    
}