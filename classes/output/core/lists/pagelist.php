<?php

namespace theme_apoa\output\core\lists;

defined('MOODLE_INTERNAL') || die;

use core_course_category;
use stdClass;

class theme_apoa_pagelist implements \templatable {


    

    protected stdClass $course;

    protected array $courses;

    protected \core_course_category $coursecat;

    public function __construct(stdClass $COURSE) {
        $this->course = $COURSE;
        $this->courses = array();
        $this->coursecat = \core_course_category::get($this->course->category);
        $subroot = get_subroot_category($this->coursecat);
        $categoryids = $subroot->get_all_children_ids();

        $subquery = '';
        foreach ($categoryids as $categoryid) {
            $params['coursecat' . $categoryid] = $categoryid;
            $subquery .= ':coursecat' . $categoryid .',';
        }
        $subquery = rtrim($subquery, ',');
        if ($subquery) {
            $rawcourses = \theme_apoa_tag_tag::get_all_courses_with_same_tags($this->course->id, 'core', 'course', '1', '3', 'it.id != ' . $this->course->id, 'timecreated DESC', $params);
        }
        foreach ($rawcourses as $rawcourse){
            $this->courses[$rawcourse->id] = new \core_course_list_element($rawcourse);
        }

    }
    
        
    
    public function export_for_template(\renderer_base $output) {

        global $CFG;
        
        $template['sidebaritems'] = [];


        foreach ($this->courses as $course){
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
            array_push($template['sidebaritems'], array(
                'name' => $course->get_formatted_shortname(),
                'url' => $url,
                'img' => $img,
                'cat' => $this->coursecat->name
            ));
        }
        
 
        return $template;
    }



    
}