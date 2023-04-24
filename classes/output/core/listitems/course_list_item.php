<?php

namespace theme_apoa\output\core\listitems;

use stdClass;

defined('MOODLE_INTERNAL') || die;



class course_list_item implements \templatable , \renderable {


    use \theme_apoa\output\core\mainpage\mainpage_named_templatable;



    protected \core_course_list_element $course;

    protected int $index;

    public function __construct(\core_course_list_element $course, $index) {

        $this->course = $course;
        $this->index = $index;
        
    }
        
    
    public function export_for_template(\renderer_base $output) {

        global $CFG;

        $coursecat = \core_course_category::get($this->course->category);

        if($tag = reset(\theme_apoa_tag_tag::get_item_tags('core', 'course', $this->course->id))) {
            $tagurl = $tag->get_view_url();
            $tagname = $tag->get_display_name();
        }
        else{
            $tagurl = '';
            $tagname = '';
        }

        $rootcat = get_subroot_category($coursecat);
        $rootcat = get_parent_category_by_generation($coursecat, 2);
        $wwwroot = $CFG->wwwroot;

        $itemurl = $wwwroot . "/course/view.php?id=" . $this->course->id;
        $caturl  = $coursecat->get_view_link();
        $rooturl  = $rootcat->get_view_link();

        $itemdesc = $this->course->description;
        $itemsummary = $this->course->summary;
        
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
            "itemdescription" => $itemdesc,
            "itemsummary" => $itemsummary,
            "itemroot" => $rootcat->name,
            "itemrootid" => $rootcat->id,
            "itemurl" => $itemurl,
            "itemcaturl" => $caturl,
            "itemrooturl" => $rooturl,
            "itemimg" => $img,
            'itemtag' => $tagname,
            'itemtagurl' => $tagurl,
            'itemindex' => $this->index
        ];

        return $template;

    }

    
}