<?php

namespace theme_apoa\output\core\lists;

defined('MOODLE_INTERNAL') || die;

use core_course_category;
use stdClass;

class event_list implements \templatable {

    protected string $tense;

    protected string $listtype;

    protected string $contentgenerator;

    protected array $courses;

    protected \core_tag_tag $tag;

    protected \core_course_category $category;

    protected bool $iselibrary = false;

    public array $subcategories;

    public \moodle_url $redirecturl;

    protected int $now;

    

    public function __construct(string $listtype, string $tense, \core_course_category $category = null) {
        global $CFG;

        $this->redirecturl = new \moodle_url($CFG->wwwroot);

        $this->listtype = $listtype;
        $this->tense = $tense;

        $this->now = time();
        $this->set_tag_from_criteria('Events');

        if ($category) {
            $this->category = $category;
        }

        $setcourses = 'set_courses_for_' . $this->listtype;
        $this->$setcourses();

    }
    
    protected function set_tag_from_criteria(string $criteria) {
        $tags = \theme_apoa_tag_tag::guess_by_name($criteria);
        $courses = [];
        if ($tags) {
            $this->tag = reset($tags);
        }
    }
    
    protected function set_courses_for_inprogress() {

            $subquery = 'it.startdate <= ' . $this->now .' AND it.enddate > ' . $this->now;
            $this->courses = $this->tag->get_tagged_items('core', 'course', '', '', $subquery, 'startdate');
    }

    protected function set_courses_for_past() {

            $subquery = 'it.enddate < ' . $this->now;
            $this->courses = $this->tag->get_tagged_items('core', 'course', '0', '3', $subquery, 'enddate DESC');
        
    }

    protected function set_courses_for_future() {

            $subquery = 'it.startdate >= ' . $this->now;
            $this->courses = $this->tag->get_tagged_items('core', 'course', '0', '3', $subquery, 'startdate');
    }
    
    public function export_for_template(\renderer_base $output) {

        global $CFG;
        
        $template = [];
        $index = 0;

        foreach($this->courses as $course) {
            $eventlistitem = new \theme_apoa\output\core\listitems\course_list_item($course, $index, false);
            $render = $eventlistitem->export_for_template($output);
            array_push($template, $render);
            $index += 1;
        }
        
 
        return $template;
    }



    
}