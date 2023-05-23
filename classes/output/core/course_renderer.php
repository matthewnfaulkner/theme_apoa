<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace theme_apoa\output\core;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/theme/apoa/classes/output/core/course_category.php');
require_once($CFG->dirroot . '/theme/apoa/classes/output/core/tag_course_category.php');
require_once($CFG->dirroot . '/theme/apoa/classes/output/core/tag.php');
require_once($CFG->dirroot . '/theme/apoa/classes/output/core/lists/pagelist.php');


use moodle_url;
use html_writer;
use get_string;



require_once($CFG->dirroot . '/course/renderer.php');


use \coursecat_helper as coursecat_helper;
use \lang_string as lang_string;
use \core_course_category as core_course_category;
/**
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package    theme_boost
 * @copyright  2012 Bas Brands, www.basbrands.nl
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_renderer extends \core_course_renderer {


    /**
     * Renders html to print list of courses tagged with particular tag
     *
     * @param int $tagid id of the tag
     * @param bool $exclusivemode if set to true it means that no other entities tagged with this tag
     *             are displayed on the page and the per-page limit may be bigger
     * @param int $fromctx context id where the link was displayed, may be used by callbacks
     *            to display items in the same context first
     * @param int $ctx context id where to search for records
     * @param bool $rec search in subcontexts as well
     * @param array $displayoptions
     * @return string empty string if no courses are marked with this tag or rendered list of courses
     */
    public function get_courses_by_cat_and_tag(\core_course_category $coursecat, \coursecat_helper $chelper, $exclusivemode = true, $ctx = 0, $rec = true, $displayoptions = null, $options = array()) {
        global $CFG;
        global $DB;
        $recursive = !empty($options['recursive']);
        $offset = !empty($options['offset']) ? $options['offset'] : 0;
        $limit = !empty($options['limit']) ? $options['limit'] : null;
        $sortfields = !empty($options['sort']) ? $options['sort'] : array('sortorder' => 1);

        if (empty($displayoptions)) {
            $displayoptions = array();
        }
        $showcategories = !core_course_category::is_simple_site();
        $displayoptions += array('limit' => $CFG->coursesperpage, 'offset' => 0);
        $tagnames = ['Announcements', 'Events'];
        $courses = array();

        $coursecatcache = \cache::make('core', 'coursecat');
        $cachekey = 'l-'. $coursecat->id. '-'. (!empty($options['recursive']) ? 'r' : '').
                 '-'. serialize($sortfields);
        $cntcachekey = 'lcnt-'. $coursecat->id. '-'. (!empty($options['recursive']) ? 'r' : '');

        $ids = $coursecatcache->get($cachekey);
        
        $categoryids = $coursecat->get_all_children_ids();
        array_push($categoryids, $coursecat->id);

        $subquery = '';
        foreach ($categoryids as $categoryid) {
            $params['coursecat' . $categoryid] = $categoryid;
            $subquery .= ':coursecat' . $categoryid .',';
        }
        $subquery = rtrim($subquery, ',');

        foreach($tagnames as $tagname) {
            $tags = \theme_apoa_tag_tag::guess_by_name($tagname);
            if($tags) {
                $tag = reset($tags);
                $taggedcourses = $tag->get_tagged_items('core', 'course', '', '', 'it.category IN ('. $subquery .')', 'sortorder', $params);
                foreach ($taggedcourses as $taggedcourse) {
                    $courses[$tagname][$taggedcourse->id]= new \core_course_list_element($taggedcourse);
                }
            }
        }
        return $courses;
        
    }

    
  
    /**
     * Returns HTML to display a course category as a part of a tree
     *
     * This is an internal function, to display a particular category and all its contents
     * use {@link core_course_renderer::course_category()}
     *
     * @param coursecat_helper $chelper various display options
     * @param core_course_category $coursecat
     * @param int $depth depth of this category in the current tree
     * @return string
     */
    protected function coursecat_category(coursecat_helper $chelper, $coursecat, $depth) {
        // open category tag
        $classes = array('category');
        if (empty($coursecat->visible)) {
            $classes[] = 'dimmed_category';
        }
        if ($chelper->get_subcat_depth() > 0 && $depth >= $chelper->get_subcat_depth()) {
            // do not load content
            $categorycontent = '';
            $classes[] = 'notloaded';
            if ($coursecat->get_children_count() ||
                    ($chelper->get_show_courses() >= self::COURSECAT_SHOW_COURSES_COLLAPSED && $coursecat->get_courses_count())) {
                $classes[] = 'with_children';
                $classes[] = 'collapsed';
            }
        } else {
            // load category content
            $categorycontent = $this->coursecat_category_content($chelper, $coursecat, $depth);
            $classes[] = 'loaded';
            if (!empty($categorycontent)) {
                $classes[] = 'with_children';
                // Category content loaded with children.
                $this->categoryexpandedonload = true;
            }
        }

        // Make sure JS file to expand category content is included.
        $this->coursecat_include_js();

        $content = html_writer::start_tag('div', array(
            'class' => join(' ', $classes),
            'data-categoryid' => $coursecat->id,
            'data-depth' => $depth,
            'data-showcourses' => $chelper->get_show_courses(),
            'data-type' => self::COURSECAT_TYPE_CATEGORY,
        ));

        // category name
        $categoryname = $coursecat->get_formatted_name();
        $categoryname = html_writer::link(new moodle_url('/course/index.php',
                array('categoryid' => $coursecat->id)),
                $categoryname);
        if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_COUNT
                && ($coursescount = $coursecat->get_courses_count())) {
            $categoryname .= html_writer::tag('span', ' ('. $coursescount.')',
                    array('title' => get_string('numberofcourses'), 'class' => 'numberofcourse'));
        }
        $content .= html_writer::start_tag('div', array('class' => 'info'));

        $content .= html_writer::tag(($depth > 1) ? 'h4' : 'h3', $categoryname, array('class' => 'categoryname aabtn'));
        $content .= html_writer::end_tag('div'); // .info

        // add category content to the output
        $content .= html_writer::tag('div', $categorycontent, array('class' => 'content'));

        $content .= html_writer::end_tag('div'); // .category

        // Return the course category tree HTML
        return $content;
    }

    /**
     * Renders HTML to display particular course category - list of it's subcategories and courses
     *
     * Invoked from /course/index.php
     *
     * @param int|stdClass|core_course_category $category
     */
    public function course_category($category) {
        global $CFG, $USER, $PAGE;

        
        $usertop = core_course_category::user_top();

        if (empty($category)) {
            $coursecat = $usertop;
        } else if (is_object($category) && $category instanceof core_course_category) {
            $coursecat = $category;
        } else {
            $coursecat = core_course_category::get(is_object($category) ? $category->id : $category);
        }

        $parent = $coursecat->get_parent_coursecat();


        if (!$parent->depth == core_course_category::top()->depth && !is_siteadmin($USER) && False){
            redirect(new moodle_url('/course/index.php?categoryid=' . $parent->id));
        }
        else{

            $site = get_site();
            //$actionbar = new \core_course\output\category_action_bar($this->page, $coursecat);
            //$output = $this->render_from_template('core_course/category_actionbar', $actionbar->export_for_template($this));
            $output = "";
            if (core_course_category::is_simple_site()) {
                // There is only one category in the system, do not display link to it.
                $strfulllistofcourses = get_string('fulllistofcourses');
                $this->page->set_title("$site->shortname: $strfulllistofcourses");
            } else if (!$coursecat->id || !$coursecat->is_uservisible()) {
                $strcategories = get_string('categories');
                $this->page->set_title("$site->shortname: $strcategories");
            } else {
                $strfulllistofcourses = get_string('fulllistofcourses');
                $this->page->set_title("$site->shortname: $strfulllistofcourses");
            }

            // Print current category description
            $chelper = new coursecat_helper();
            
            // Prepare parameters for courses and categories lists in the tree
            $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_AUTO)
                    ->set_attributes(array('class' => 'category-browse category-browse-'.$coursecat->id));
            
            $coursedisplayoptions = array();
            $catdisplayoptions = array();
            $browse = optional_param('browse', null, PARAM_ALPHA);
            $perpage = optional_param('perpage', $CFG->coursesperpage, PARAM_INT);
            $page = optional_param('page', 0, PARAM_INT);
            $baseurl = new moodle_url('/course/index.php');
            if ($coursecat->id) {
                $baseurl->param('categoryid', $coursecat->id);
            }
            if ($perpage != $CFG->coursesperpage) {
                $baseurl->param('perpage', $perpage);
            }
            $coursedisplayoptions['limit'] = $perpage;
            $catdisplayoptions['limit'] = $perpage;
            if ($browse === 'courses' || !$coursecat->get_children_count()) {
                $coursedisplayoptions['offset'] = $page * $perpage;
                $coursedisplayoptions['paginationurl'] = new moodle_url($baseurl, array('browse' => 'courses'));
                $catdisplayoptions['nodisplay'] = true;
                $catdisplayoptions['viewmoreurl'] = new moodle_url($baseurl, array('browse' => 'categories'));
                $catdisplayoptions['viewmoretext'] = new lang_string('viewallsubcategories');
            } else if ($browse === 'categories' || !$coursecat->get_courses_count()) {
                $coursedisplayoptions['nodisplay'] = true;
                $catdisplayoptions['offset'] = $page * $perpage;
                $catdisplayoptions['paginationurl'] = new moodle_url($baseurl, array('browse' => 'categories'));
                $coursedisplayoptions['viewmoreurl'] = new moodle_url($baseurl, array('browse' => 'courses'));
                $coursedisplayoptions['viewmoretext'] = new lang_string('viewallcourses');
            } else {
                // we have a category that has both subcategories and courses, display pagination separately
                $coursedisplayoptions['viewmoreurl'] = new moodle_url($baseurl, array('browse' => 'courses', 'page' => 1));
                $catdisplayoptions['viewmoreurl'] = new moodle_url($baseurl, array('browse' => 'categories', 'page' => 1));
            }
            $chelper->set_courses_display_options($coursedisplayoptions)->set_categories_display_options($catdisplayoptions);

            // Display course category tree.
            if ($coursecat->depth <= 2) {
                if ($description = $chelper->get_category_formatted_description($coursecat)) {
                }
                if ($coursecat->name == 'E-Library') {
                    $output .= $this->render_subcategory($chelper, $coursecat);
                }
                else if ($coursecat->name == 'Newsletter') {
                    $output .= $this->render_subcategory($chelper, $coursecat);
                }
                else if ($coursecat->name == 'About') {
                    $output .= $this->render_subcategory($chelper, $coursecat);
                }
                else if ($coursecat->name == 'Forum') {
                    $course = reset($coursecat->get_courses(array('limit' => 1)));
                    redirect($CFG->wwwroot . "/course/view.php?id=" . $course->__get('id'));
                }
                else if ($coursecat->name == 'Gallery') {
                    $course = reset($coursecat->get_courses(array('limit' => 1)));
                    redirect($CFG->wwwroot . "/course/view.php?id=" . $course->__get('id'));
                }
                else if ($coursecat->name == 'Meetings') {
                    $course = reset($coursecat->get_courses(array('limit' => 1)));
                    redirect($CFG->wwwroot . "/course/view.php?id=" . $course->__get('id'));
                }
                else {
                    $output .= $this->render_root_cat($chelper, $coursecat);
                }
            }
            else if ($coursecat->has_courses()) {
                //$courses = $coursecat->get_courses($options = array('limit' => 5));
                if($coursecat->get_courses_count() == 1){
                    $course = reset($coursecat->get_courses());
                    redirect($CFG->wwwroot . "/course/view.php?id=" . $course->__get('id'));
                };
                $output .= $this->render_course_cat($chelper, $coursecat);
            }
            else if ($coursecat->has_children()) {
                $sort = array('sortorder' => 1);
                $limit = 1;
                $options = array('sort' => $sort, 'limit' => $limit);
                $subcat = reset($coursecat->get_children($options));
                $output .= $this->render_subcategory($chelper, $coursecat);
                //$courses = $subcat->get_courses($options = array('limit' => 5));
            }
            //$output .= $this->coursecat_tree($chelper, $coursecat);


            return $output;
        }
    }

    protected function render_course_cat(coursecat_helper $chelper, core_course_category $coursecat){

        $renderer = new theme_apoa_course_category([$coursecat], 5);
        $output = $this->render_from_template('theme_apoa/categorycourselist', $renderer->export_for_template($this));
        return $output;
    }

    protected function render_root_cat(coursecat_helper $chelper, core_course_category $coursecat){
        
        $render['description'] = $chelper->get_category_formatted_description($coursecat);
        $render['sectiontitle'] = $coursecat->name;
        $courselist = new \theme_apoa\output\core\lists\course_list('category', $coursecat->name, $coursecat);

        $featuredrender = [];
        $taggedrender = [];
        $children = $coursecat->get_all_children_ids();
        $conditions = join(',', $children);
        if($featuredtag = reset(\theme_apoa_tag_tag::guess_by_name('Featured'))){
            $subquery = 'it.category IN (' . $conditions . ')';
            if($featuredcourses = $featuredtag->get_tagged_items('core', 'course', 0, 1, $subquery, 'startdate')){
                $featuredcourse = reset($featuredcourses);
                $courselist->delete_course_from_courselist($featuredcourse->id);
                $featuredcourselistitem = new \theme_apoa\output\core\listitems\course_list_item($featuredcourse, 0, false);
                $featuredrender['subcategorycourses'] = $featuredcourselistitem->export_for_template($this);
                $featuredrender['categorytitle'] = 'Featured';
                $featuredrender['Featured'] = 'Featured';
            }
        };
        if($featuredtag = reset(\theme_apoa_tag_tag::guess_by_name($coursecat->name))){
            $settingname = 'elibraryid';
            if($elibrary = core_course_category::get(get_config('theme_apoa', $settingname))){
                $children = $elibrary->get_all_children_ids();
                $conditions = join(',', $children);
                $subquery = 'it.category IN (' . $conditions . ')';
                if($taggedcourses = $featuredtag->get_tagged_items('core', 'course', 0, 3, $subquery, 'startdate')){
                    $counter = 0;
                    $taggedrender['subcategorycourses'] = [];
                    foreach ($taggedcourses as $taggedcourse){
                        $taggedcourselistitem = new \theme_apoa\output\core\listitems\course_list_item($taggedcourse, $counter, true);
                        $taggedcourserender = $taggedcourselistitem->export_for_template($this);
                        array_push($taggedrender['subcategorycourses'], $taggedcourserender);
                        $counter += 1;
                    }
                    $taggedrender['categorytitle'] = 'Related Papers';
                    $taggedrender['Elibrary'] = 'Elibrary';
                    $taggedrender['categoryid'] = $elibrary->id;
                }
                
            };
            
        };
        
        $render['categorylist'] = $courselist->export_for_template($this);
        if($featuredrender){
            array_push($render['categorylist'], $featuredrender);
        }
        if ($taggedrender) {
            array_push($render['categorylist'], $taggedrender);
        }
        $output = $this->render_from_template('theme_apoa/sectionlanding',$render);
        return $output;
    }

    protected function render_course_tag_and_cat(core_course_category $coursecat, $courses, coursecat_helper $chelper){

        $renderer = new theme_apoa_tag_course_category($coursecat, $courses);
        $output = $this->render_from_template('theme_apoa/section-landing', $renderer->export_for_template($this));
        return $output;
    }

    protected function render_subcategory_list(coursecat_helper $chelper, core_course_category $coursecat) {
        $subcategories = $coursecat->get_children();
        $renderer = new theme_apoa_course_category($subcategories, 1);
        $output = $this->render_from_template('theme_apoa/category', $renderer->export_for_template($this));
        return $output;
    }

    protected function render_subcategory(coursecat_helper $chelper, core_course_category $coursecat) {
        $output = '';
        if ($coursecat->id == get_config('theme_apoa', 'elibraryid')){
            $searchbar = new \theme_apoa\output\search_elibrary_bar($coursecat);
            $searchbarout['elementsarray'] = $searchbar->export_for_template($this);
            $output .= $searchbarout['elementsarray'];
            //$render['elibrarysearch'] = $searchbarout['elementsarray'];
        }
        $render['description'] = $chelper->get_category_formatted_description($coursecat);
        $render['sectiontitle'] = $coursecat->name;

        $courselist = new \theme_apoa\output\core\lists\course_list('category', $coursecat->name, $coursecat);
        
        $render['categorylist'] = $courselist->export_for_template($this);
        $output .= $this->render_from_template('theme_apoa/category',$render);
        return $output;
    }

}

