<?php

namespace theme_apoa\output\core\lists;

use moodle_url;
use stdClass;

defined('MOODLE_INTERNAL') || die;



class course_list implements \templatable , \renderable {


    use \theme_apoa\output\core\mainpage\mainpage_named_templatable;


    protected string $criteria;

    protected string $listtype;

    protected string $contentgenerator;

    protected array $courses;

    protected \core_tag_tag $tag;

    protected \core_course_category $category;

    public array $subcategories;

    public \moodle_url $redirecturl;


    public function __construct(string $listtype, string $criteria, \core_course_category $category = null) {
 
        global $CFG;

        $this->redirecturl = new moodle_url($CFG->wwwroot);

        $this->listtype = $listtype;
        $this->criteria = $criteria;

        $this->set_tag_from_criteria($this->criteria);

        if (!$category) {
            $setcategory = 'set_category_for_' . $this->listtype;
            $this->$setcategory();
        }else {
            $this->category = $category;
        }

        $setcourses = 'set_courses_for_' . $this->listtype;
        $this->$setcourses($this->criteria);

        $setredirecturl = 'set_url_for_' . $this->listtype;
        $this->$setredirecturl();

    }
        
    
    public function export_for_template(\renderer_base $output) {

        $template = [];
        if(isset($this->courses)) {
            $index = 1;
            foreach ($this->courses as $course){
                $jumbosidelistitem = new \theme_apoa\output\core\listitems\course_list_item($course, $index);
                array_push($template, $jumbosidelistitem->export_for_template($output));
                $index += 1;
            }
        }
        if($template){
            reset($template);
            $firstkey = array_key_first($template);
            $template[$firstkey]['first'] = true;
        }
        return $template;

    }

    protected function set_tag_from_criteria(string $criteria) {
        $tags = \theme_apoa_tag_tag::guess_by_name($criteria);
        $courses = [];
        if ($tags) {
            $this->tag = reset($tags);
        }
    }

    protected function set_category_for_mainpage() {
        return;
    }

    protected function set_url_for_mainpage() {
        return;
    }
    
    protected function set_courses_for_mainpage(string $criteria) {
    
        return get_courses_for_course_list($criteria);
    
    }
    
    protected function set_category_for_newsletter() {
        $settingname = 'newsletterid';
        $categoryid = get_config('theme_apoa', $settingname);
        $this->category =   \core_course_category::get($categoryid);

    }
    
    protected function set_url_for_newsletter() {
        $this->redirecturl = $this->category->get_view_link();
    }

    protected function set_courses_for_newsletter(string $criteria) {
    
        $options = array('recursive' => 1, 'limit' => 3);
        $this->courses = $this->category->get_courses($options);
    
    }
    
    protected function set_category_for_course_list() {
       return;
    }
    
    protected function set_url_for_course_list() {
        if (isset($this->tag)) {
            $this->redirecturl = $this->tag->get_view_url();
        }
    }

    protected function set_courses_for_course_list(string $criteria) {
        if (isset($this->tag)) {
            $rawcourses = $this->tag->get_tagged_items('core', 'course', '0', '3', '', 'timecreated DESC');
            foreach ($rawcourses as $rawcourse){
                $this->courses[$rawcourse->id] = new \core_course_list_element($rawcourse);
            }
        }
    }
    
    
    protected function set_category_for_elibrary() {
        $settingname = 'elibraryid';
        $categoryid = get_config('theme_apoa', $settingname);
        $this->category = \core_course_category::get($categoryid);
        $this->subcategories = $this->category->get_children();
        
    }
    
    protected function set_url_for_elibrary() {
        $this->redirecturl = $this->category->get_view_link();
    }

    protected function set_courses_for_elibrary() {

        $options = array('recursive' => 1, 'limit' => 3, 'summary' => 1, 'sort' => array('startdate' => 1));
        $this->courses = $this->category->get_courses($options);
    }

    protected function set_category_for_category() {
        $settingname = 'elibraryid';
        $categoryid = get_config('theme_apoa', $settingname);
        $this->category = \core_course_category::get($categoryid);
        $this->subcategories = $this->category->get_children();
        
    }
    
    protected function set_url_for_category() {
        $this->redirecturl = $this->category->get_view_link();
    }

    protected function set_courses_for_category() {

        $options = array('recursive' => 1, 'limit' => 3, 'summary' => 1, 'sort' => array('startdate' => 1));
        $this->courses = $this->category->get_courses($options);
    }
}