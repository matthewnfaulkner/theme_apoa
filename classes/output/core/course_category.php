<?php

namespace theme_apoa\output\core;

use theme_apoa_tag_tag;
use theme_apoa\output\core\listitems\course_list_item;

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
        
        $subcat = ['category' => []];

        foreach($this->categories as $subcategory) {
            $options = array('recursive' => True, 'summary' => 1, 'limit' => $this->limit);
            $courselist = $subcategory->get_courses($options);
            $subcatcourses = [];
            
            foreach ($courselist as $index => $course) {
                $courselistitem = new course_list_item($course, $index, false);
                $subcatcourses[] = $courselistitem->export_for_template($output);
            }
        
            if ($subcatcourses) {
                $caturl = $subcategory->get_view_link();
                array_push($subcat['category'],  array(
                    'catname' => $subcategory->name,
                    'caturl' => $caturl,
                    'courses' => $subcatcourses)
                );
            }
        }
 
        return $subcat;
    }



    
}