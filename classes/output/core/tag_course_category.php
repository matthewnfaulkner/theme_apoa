<?php


namespace theme_apoa\output\core;

defined('MOODLE_INTERNAL') || die;

use core_course_category;

class theme_apoa_tag_course_category implements \templatable {


    protected \core_course_category $coursecat;

    protected array $coursesbytags;


    public function __construct(\core_course_category $coursecat, $coursesbytags) {
        $this->coursecat = $coursecat;
        $this->coursesbytags = $coursesbytags;
    }
    
        
    
    public function export_for_template(\renderer_base $output) {

        global $CFG;
        
        $template = [];
        $subcategories = $this->coursecat->get_children();
        $subcat = [];

        foreach($this->coursesbytags as $key=>$coursesbytag) {
            $subcatcourses = [];

            foreach ($coursesbytag as $course) {
                foreach ($course->get_course_overviewfiles() as $file) {
                    $isimage = $file->is_valid_image();
                    $img = \moodle_url::make_file_url("$CFG->wwwroot/pluginfile.php",
                        '/' . $file->get_contextid() . '/' . $file->get_component() . '/' .
                        $file->get_filearea() . $file->get_filepath() . $file->get_filename(), !$isimage);
                    if ($isimage) {
                        break;
                    }
                }
                $cat = core_course_category::get($course->category);
                $caturl = $cat->get_view_link();
                $url = course_get_url($course);
                array_push($subcatcourses, array(
                    'name' => $course->get_formatted_shortname(),
                    'url' => $url,
                    'img' => $img,
                    'caturl' => $caturl,
                    'cat' => $cat->name
                ));

            }
            $subcat[$key] = [];
            array_push($subcat[$key], array(
                'name' => $key,
                'courses' => $subcatcourses
            ));


        }
 
        return $subcat;
    }



    
}