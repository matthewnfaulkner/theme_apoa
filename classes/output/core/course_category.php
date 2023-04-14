<?php

namespace theme_apoa\output\core;

use theme_apoa_tag_tag;

defined('MOODLE_INTERNAL') || die;



class theme_apoa_course_category implements \templatable {


    protected array $categories;

    protected int $limit;


    public function __construct(array $categories, int $limit) {
        $this->categories = $categories;
        $this->limit = $limit;
    }
    
        
    
    public function export_for_template(\renderer_base $output) {

        global $CFG;
        
        //$subcategories = $this->coursecat->get_children();
        $subcat = ['category' => []];

        foreach($this->categories as $subcategory) {
            $options = array('recursive' => True, 'summary' => 1, 'limit' => $this->limit);
            $courselist = $subcategory->get_courses($options);
            $subcatcourses = [];
            $includedate = False;

            if ($subcategory->id == get_config('theme_apoa', 'elibraryid')) {
                $includedate = True;
            }
            
            foreach ($courselist as $course) {
                if ($tag = reset(\theme_apoa_tag_tag::get_item_tags('core', 'course', $course->id))){
                    $tagname = $tag->get_display_name();
                    $tagurl = $tag->get_view_url();
                }
                else{
                    $tagname = '';
                    $tagurl = '';
                };
                foreach ($course->get_course_overviewfiles() as $file) {
                    $isimage = $file->is_valid_image();
                    $img = \moodle_url::make_file_url("$CFG->wwwroot/pluginfile.php",
                        '/' . $file->get_contextid() . '/' . $file->get_component() . '/' .
                        $file->get_filearea() . $file->get_filepath() . $file->get_filename(), !$isimage);
                    if ($isimage) {
                        break;
                    }
                }

                if ($includedate) {
                    $date = $course->startdate;
                }
                else{
                    $date = null;
                }

                $summary = str_replace('<p', '<p class="card-summary-hide-mobile"', $course->summary);
                $url = course_get_url($course);
                array_push($subcatcourses, array(
                    'name' => $course->get_formatted_shortname(),
                    'summary' => $summary,
                    'url' => $url,
                    'img' => $img,
                    'tag' => $tagname,
                    'tagurl' => $tagurl,
                    'date' => $date
                ));

            }
            if ($subcatcourses) {
                $caturl = $subcategory->get_view_link();
                array_push($subcat['category'],  array(
                    'name' => $subcategory->name,
                    'url' => $caturl,
                    'courses' => $subcatcourses)
                );
            }
            


        }
 
        return $subcat;
    }



    
}