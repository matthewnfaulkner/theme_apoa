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
 *  Override core moodle_page class.
 *
 * @package     theme_apoa
 * @copyright   2025 Matthew Faulkner matthewfaulkner@apoaevents.com
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */                                       


defined('MOODLE_INTERNAL') || die();


class apoa_page extends moodle_page{

    /**
     * Returns the secondary navigation object
     *
     * @todo MDL-74939 Remove support for old 'local\views\secondary' class location
     * @return secondary
     */
    protected function magic_get_secondarynav() {
        if ($this->_secondarynav === null) {
            $class = 'theme_' . $this->theme->name .'\navigation\views\secondary';
            // Try and load a custom class first.
            if (class_exists("mod_{$this->activityname}\\navigation\\views\\secondary")) {
                $class = "mod_{$this->activityname}\\navigation\\views\\secondary";
            } else if (class_exists("mod_{$this->activityname}\\local\\views\\secondary")) {
                // For backwards compatibility, support the old location for this class (it was in a
                // 'local' namespace which shouldn't be used for core APIs).
                debugging("The class mod_{$this->activityname}}\\local\\views\\secondary uses a deprecated " .
                        "namespace. Please move it to mod_{$this->activityname}\\navigation\\views\\secondary.",
                        DEBUG_DEVELOPER);
                $class = "mod_{$this->activityname}\\local\\views\\secondary";
            }
            $class = 'theme_' . $this->theme->name .'\navigation\views\secondary';
            
            if(!class_exists($class)) {
                foreach($this->theme->parents as $parent) {
                    $class = 'theme_' . $parent . '\navigation\views\secondary';
                    if(class_exists($class)){
                        break;
                    }
                }
            }
            $this->_secondarynav = new $class($this);
            $this->_secondarynav->initialise();
        }
        return $this->_secondarynav;
    }

    /**
     * Return primary navigation object
     *
     * @return void
     */
    protected function magic_get_primarynav() {
        if ($this->_primarynav === null) {
            $class = 'theme_apoa\navigation\views\primary';
            // Try and load a custom class first.
            if (class_exists("mod_{$this->activityname}\\navigation\\views\\primary")) {
                $class = "mod_{$this->activityname}\\navigation\\views\\primary";
            } else if (class_exists("mod_{$this->activityname}\\local\\views\\primary")) {
                // For backwards compatibility, support the old location for this class (it was in a
                // 'local' namespace which shouldn't be used for core APIs).
                debugging("The class mod_{$this->activityname}}\\local\\views\\primary uses a deprecated " .
                        "namespace. Please move it to mod_{$this->activityname}\\navigation\\views\\primary.",
                        DEBUG_DEVELOPER);
                $class = "mod_{$this->activityname}\\local\\views\\primary";
            }

            $this->_primarynav = new theme_apoa\navigation\views\primary($this);
            $this->_primarynav->initialise();
        }
        return $this->_primarynav;
    }


        /**
     * This function ensures that the category of the current course has been
     * loaded, and if not, the function loads it now.
     *
     * @return void
     * @throws coding_exception
     */
    protected function ensure_category_loaded() {
        global $args;
        if (is_array($this->_categories)) {
            return; // Already done.
        }
        if (is_null($this->_course)) {
            throw new coding_exception('Attempt to get the course category for this page before the course was set.');
        }
        //category not set
        if ($this->_course->category == 0) {

            //check for context in url
            $ctx = optional_param('ctx', null, PARAM_INT);

            //check for category id in args
            $categoryid = $args['categoryid'];
            if($ctx && ($context = context::instance_by_id($ctx, IGNORE_MISSING)) && $context->contextlevel == CONTEXT_COURSECAT){
                if($category = core_course_category::get($context->instanceid, IGNORE_MISSING)){
                    $parent_categoryids = array_reverse($category->get_parents());
                    $this->_categories = [$category->id => $category];
                    //add parent categories to categories
                    foreach($parent_categoryids as $parent_id) {
                        if($parent_cat = core_course_category::get($parent_id, IGNORE_MISSING)){
                            $this->_categories[$parent_id] = $parent_cat;
                        }
                    }
                }
                else{
                    $this->_categories = array();
                }
               
            }
            //we have found the category return it
            else if($categoryid) {
                $this->_categories[$categoryid] = core_course_category::get($categoryid, IGNORE_MISSING);
            }
            //get via context
            else if (($context = context::instance_by_id($this->context->id, IGNORE_MISSING)) && $context->contextlevel == CONTEXT_COURSECAT) {
                $this->_categories[$context->instanceid] = core_course_category::get($context->instanceid, MUST_EXIST);
            }
            //cannot ascertain category
            else{
                $this->_categories = array();
            }
            
        } else {
            $this->load_category($this->_course->category);
        }
    }


}
