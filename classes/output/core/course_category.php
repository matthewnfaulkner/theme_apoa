<?php

namespace theme_apoa\output\core;


class theme_apoa_course_category implements \templatable {


    protected \core_course_category $coursecat;


    public function __construct(\core_course_category $coursecat) {
        $this->coursecat = $coursecat;
    }
    
        
    
    public function export_for_template(\renderer_base $output) {

        global $CFG;
        
        $template = [];
        $subcategories = $this->coursecat->get_children();
        $subcat = [];

        foreach($subcategories as $subcategory) {
            $courselist = $subcategory->get_courses();
            $subcatcourses = [];

            foreach ($courselist as $course) {
                foreach ($course->get_course_overviewfiles() as $file) {
                    $isimage = $file->is_valid_image();
                    $img = \moodle_url::make_file_url("$CFG->wwwroot/pluginfile.php",
                        '/' . $file->get_contextid() . '/' . $file->get_component() . '/' .
                        $file->get_filearea() . $file->get_filepath() . $file->get_filename(), !$isimage);
                    if ($isimage) {
                        break;
                    }
                }
                $url = course_get_url($course);
                array_push($subcatcourses, array(
                    'name' => $course->get_formatted_shortname(),
                    'url' => $url,
                    'img' => $img
                ));

            }
            $subcat[$subcategory->name] = [];
            array_push($subcat[$subcategory->name], array(
                'name' => $subcategory->name,
                'courses' => $subcatcourses
            ));


        }
 
        return $subcat;
    }

    
}