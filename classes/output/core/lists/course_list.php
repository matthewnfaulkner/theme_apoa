<?php

namespace theme_apoa\output\core\lists;

use moodle_url;
use stdClass;
use theme_apoa\output\core\listitems\course_list_item;

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

    protected stdClass $featuredcourse;

    protected bool $iselibrary = false;
    
    protected \context $context;

    protected \context_coursecat $category_context;


    public function __construct(string $listtype, string $criteria, \core_course_category $category = null) {
 
        global $CFG, $PAGE;


        $this->context = $PAGE->context;

        $this->redirecturl = new moodle_url($CFG->wwwroot);

        $this->listtype = $listtype;
        $this->criteria = $criteria;

        $this->set_tag_from_criteria($this->criteria);

        if (!$category) {
            $setcategory = 'set_category_for_' . $this->listtype;
            $this->$setcategory();
        }else {
            $this->category = $category;
            $this->subcategories = $category->get_children();
        }

        $this->category_context = \context_coursecat::instance($this->category->id);
        
        if (isset($this->category)) {
            $settingname = 'elibraryid';
            if($this->category->id == get_config('theme_apoa', $settingname)){
                $this->iselibrary = True;
            };
        }

        $setcourses = 'set_courses_for_' . $this->listtype;
        $this->$setcourses($this->criteria);

        $setredirecturl = 'set_url_for_' . $this->listtype;
        $this->$setredirecturl();

    
    }
        
    
    public function export_for_template(\renderer_base $output) {

        global $PAGE;

        $template = [];
        $store = array();
        $loopcounter = 0;
        if(isset($this->subcategories)){
            if($this->iselibrary){
                $store['toppages'] = array('subcategorycourses' => [], 
                                    'firsttab' => $this->iselibrary, 
                                    'categoryid' => "0", 
                                    'categorytitle' => "Popular Papers",
                                    'categoryurl' => "", 
                                    'hascourses' => false);
                if($this->context->id == $this->category_context->id){
                    if(isset($this->featuredcourse)){
                        $featuredlistitem = new course_list_item($this->featuredcourse, 0, $this->iselibrary);
                        $render = $featuredlistitem->export_for_template($output);
                        $store['toppages']['featuredcourse'] = $render;
                    }
                    else{
                        $store['toppages']['nocoursemessage'] = "No current Journal Clubs";
                        $store['toppages']['previousfuture'] = True;
                        $store['toppages']['previousjournalclubslink'] = new moodle_url('/tag/index.php', array('group' => 'past', 'tag' => 'Journal Club', 'tc' => 0));
                        $store['toppages']['previousjournalclubstext'] = "See Previous Journal Clubs";
                        $store['toppages']['futurejournalclubslink'] = new moodle_url('/tag/index.php', array('group' => 'future', 'tag' => 'Journal Club', 'tc' => 0));
                        $store['toppages']['futurejournalclubstext'] = "See Upcoming Journal Clubs";
                    }
                    $store['toppages']['categorytitle'] = 'Journal Club';
                    $store['toppages']['categoryurl'] = new moodle_url('/tag/index.php', array('tag' => 'Journal Club', 'tc' => 0));
                }
                $loopcounter += 1;
            }
            foreach ($this->subcategories as $subcategory) {
                $store[$subcategory->id] = array('subcategorycourses' => [], 
                                'firsttab' => !$loopcounter, 
                                'categoryid' => $subcategory->id, 
                                'categorytitle' => $subcategory->name,
                                'categoryurl' => $subcategory->get_view_link(), 
                                $subcategory->name => $subcategory->name,
                                'hascourses' => false);
                $loopcounter += 1;
            }
        }
        
        if(isset($this->courses)) {
            $firstthree = 0;
            foreach ($this->courses as $course){
                if (isset($course->root)) {
                    $index = count($store[$course->root]['subcategorycourses']);
                    $jumbosidelistitem = new course_list_item($course, $index, $this->iselibrary);
                    $render = $jumbosidelistitem->export_for_template($output);
                    array_push($store[$course->root]['subcategorycourses'], $render);
                    $store[$course->root]['hascourses'] = true;
                    if($firstthree < 3 && $this->iselibrary){
                        $render['first'] = !$firstthree;
                        $render['itemrootid'] = 0;
                        $render['itemindex'] = $firstthree+2;
                        array_push($store['toppages']['subcategorycourses'], $render);
                        $firstthree += 1;
                    }
                    
                } else {
                    $index = count($store);
                    $jumbosidelistitem = new course_list_item($course, $index, $this->iselibrary);
                    array_push($store, $jumbosidelistitem->export_for_template($output));
                }
            }
        }
        $template = array_values($store);
        return  $template;

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
    
        global $DB;
        
        $id = $this->category->id;
        $children = $this->category->get_all_children_ids();
        $conditions = join(', ', $children);
        $query = "SELECT c.* 
                FROM {course} c
                WHERE c.category IN (". $conditions .")
                ORDER BY c.startdate DESC
                LIMIT 3";
        $records = $DB->get_records_sql($query);
        $this->courses = $records;
    
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
            $this->courses = $this->tag->get_tagged_items('core', 'course', '0', '3', '', 'timecreated DESC');
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

        global $DB;
        $sql = [];

        $favquery = "(SELECT c.*, count
            FROM {course} c
            LEFT JOIN (
                SELECT f.itemid, COUNT(*) as count
                FROM {favourite} f
                WHERE f.component = 'core_course'
                GROUP BY f.itemid
                ORDER BY count DESC
                ) AS top
                ON c.id = top.itemid)";      
        foreach ($this->subcategories as $subcategory) {
            $id = $subcategory->id;
            $children = $subcategory->get_all_children_ids();
            $conditions = join(', ', $children);
            $query = "(SELECT c.*, ". $id ." AS root 
                    FROM ". $favquery . " AS c 
                    WHERE c.category IN (". $conditions .")
                    ORDER BY count, RAND()
                    LIMIT 3)";
            array_push($sql, $query);   
            $record = $DB->get_records_sql($query);
        }
        
        $union = join(' UNION ', $sql);
        $massivequery = "SELECT a.* FROM (" . $union . ") a";
        $limit = count($sql) * 3 + 1;
        $records = $DB->get_records_sql($massivequery, null, 0, $limit);
        $this->courses = $records;


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
        global $DB;
        $sql = [];
        
        $showinvisible = has_capability('moodle/course:viewhiddencourses', $this->context);

        if (!$this->subcategories) {
            $conditions = array('category' => $this->category->id);
            if(!$showinvisible){
                $conditions['visible'] = 1;
            }
            $this->courses = $DB->get_records('course', $conditions, 'sortorder ASC');
            return;
        }

        if($this->iselibrary){
            if($tags = \theme_apoa_tag_tag::guess_by_name('Journal Club')){
                $tag = reset($tags);
                $params = ['startlimit' => time(), 'endlimit' => time()];
                $subquery = "it.startdate < :startlimit AND it.enddate > :endlimit";
                if(!$showinvisible){
                    $conditions['visible'] = 1;
                    $subquery = $subquery .= "AND it.visible = 1";
                }
                if($taggedcourses = $tag->get_tagged_items('core', 'course', 0, 1, $subquery, 'startdate', $params)){
                    $this->featuredcourse = reset($taggedcourses);
                }
            }
        }
        

        foreach ($this->subcategories as $subcategory) {
            $id = $subcategory->id;
            $conditions = $id;
            if($children = $subcategory->get_all_children_ids()){
                $conditions .= ',' . join(', ', $children);
            };
            if(!$showinvisible){
                $visiblequery = "AND c.visible = 1";
            }
            $query = "(SELECT c.*, ". $id ." AS root 
                    FROM {course} AS c 
                    WHERE c.category IN (". $conditions .") $visiblequery
                    ORDER BY c.sortorder ASC
                    LIMIT 3)";
            array_push($sql, $query);   
        }
        $union = join(' UNION ', $sql);
        $massivequery = "SELECT a.* FROM (" . $union . ") a ORDER BY a.startdate DESC";
        $limit = count($sql) * 3 + 1;
        $records = $DB->get_records_sql($massivequery, null, 0, $limit);
        $this->courses = $records;
    }

    public function delete_course_from_courselist($courseid) {
        if(array_key_exists($courseid, $this->courses)){
            unset($this->courses[$courseid]);
            return true;
        }
        return false;
    }
}
