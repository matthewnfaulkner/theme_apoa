<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 *  Updates the cache with the primary navigation view
 *
 * @package     theme_apoa
 * @copyright   2025 Matthew Faulkner matthewfaulkner@apoaevents.com
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */                                       
namespace theme_apoa\task;

/**
 * Class update_primary_nav
 * 
 * defines a scheduled task that updates the view of the primary navigation
 * and adds it to the navigation cache.
 */
class update_primary_nav extends \core\task\scheduled_task {

    use \core\task\logging_trait;
    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('populate_navigation', 'theme_apoa');
    }

    /**
     * Execute the task.
     */
    public function execute() {
        // Call your own api
        $cache = \cache::make('theme_apoa', 'navigation_cache');
        $this->log_start("Deleting old cache records.");
        $this->log_finish("Finished deleting old navigation cache records");

        $this->log_start("Generating new navigation records");
        $cache->set('primarynav', $this->populate_cache());
        $this->log_finish("Finished generating new navigation records");
    }

    private function populate_cache(){

        $data = new \stdClass();

        $primarynavitemcount = get_config('theme_apoa', 'primarynavcount');

        //get primary nav items from settings
        for ($i = 1; $i <= $primarynavitemcount; $i++) {
            $primarynavitem = get_config('theme_apoa', 'primarynavitems' . $i);
            $includechildren = get_config('theme_apoa', 'primarynavitems' . $i . '_adv');
            
            $category = \core_course_category::get($primarynavitem);

            //check if setting includes children categories
            if($includechildren) {
                $data->{$category->name} = $this->get_cache_struct_for_section($category, true);
            }else {
                $data->{$category->name} = $this->get_cache_struct_for_section($category, false);
            }
            
        }

        return $data;

    }

    /**
     * Construct naviagtion view object to be stored in cache
     *
     * @param core_course_category $category the current course category to add a node for
     * @param bool $includeChildren whether to include children of the category
     * @return stdClass View of primary nav object.
     */
    private function get_cache_struct_for_section(\core_course_category $category, bool $includeChildren) {
    
        $sections = new \stdClass();
        $sections->name = $category->name;
        $sections->url = "/course/index.php?categoryid={$category->id}";
        $sections->type = \navigation_node::TYPE_CATEGORY;
        $sections->id = $category->id;
        $sections->children = [];
        $sections->key = \navigation_node::TYPE_CATEGORY . $category->id;

        //get children if required
        if ($includeChildren){
            $subcategories = $category->get_children();
            foreach ($subcategories as $subcategory) {
                $subsections = new \stdClass;
                if($sectionlink = get_config('theme_apoa', 'sectionlink' . $subcategory->id)){
                    $subsections->url = new \moodle_url($sectionlink);
                }
                else{
                    $subsections->url = new \moodle_url("/course/index.php?categoryid={$subcategory->id}");
                }
                $subsections->name = $subcategory->name;
                $subsections->type = \navigation_node::TYPE_CATEGORY;
                $subsections->id = $subcategory->id;
                $subsections->key = \navigation_node::TYPE_CATEGORY . $subcategory->id;

                $sections->children[] = $subsections;

            }
        }
        $sections->children ? $sections->haschildren = true : $sections->haschildren = false;

        //if children set nodes showchildreninsubmenu to true
        $sections->haschildren ? $sections->showchildreninsubmenu = true : $sections->showchildreninsubmenu = false ;

        return $sections;

    } 
}