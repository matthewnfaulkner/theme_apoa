<?php

namespace theme_apoa\output\core\lists;

use stdClass;

defined('MOODLE_INTERNAL') || die;



class course_list implements \templatable , \renderable {


    use \theme_apoa\output\core\mainpage\mainpage_named_templatable;


    protected string $criteria;

    protected string $listtype;

    protected string $contentgenerator;

    protected array $courses;

    protected \core_tag_tag $tag;


    public function __construct(string $listtype, string $criteria) {
 
        $this->listtype = $listtype;
        $this->criteria = $criteria;
        $functionname = 'get_courses_for_' . $this->listtype;
        $this->courses = $functionname($this->criteria);
    }
        
    
    public function export_for_template(\renderer_base $output) {

        $a = new stdClass;
        $template = [];
        foreach ($this->courses as $course){
            $jumbosidelistitem = new \theme_apoa\output\core\listitems\course_list_item($course);
            array_push($template, $jumbosidelistitem->export_for_template($output));
        }
        reset($template);
        $firstkey = array_key_first($template);
        $template[$firstkey]['first'] = true;
        return $template;

    }

    
}