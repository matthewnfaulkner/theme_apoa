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
 *  Defines task for updating main page cache
 *
 * @package     theme_apoa
 * @copyright   2025 Matthew Faulkner matthewfaulkner@apoaevents.com
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */                                       
namespace theme_apoa\task;

require_once($CFG->dirroot . '/theme/apoa/lib.php');
require_once($CFG->dirroot . '/theme/apoa/classes/output/core/course_category.php');
require_once($CFG->dirroot . '/theme/apoa/classes/output/core/tag_course_category.php');
require_once($CFG->dirroot . '/theme/apoa/classes/output/core/tag.php');
require_once($CFG->dirroot . '/theme/apoa/classes/output/core/lists/pagelist.php');

/**
 * Class update_mainpage
 * 
 * Defines a scheduled task to update the main page cache.
 */
class update_mainpage extends \core\task\scheduled_task {

    use \core\task\logging_trait;

    /**
     * main cache
     *
     * @var \cache
     */
    protected \cache $cache;

    /**
     * Cache for images
     *
     * @var \cache
     */
    protected \cache $image_cache;

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('main_page_cache', 'theme_apoa');
    }

    /**
     * Execute the task.
     */
    public function execute() {
        // Call your own api
        global $CFG;
        $this->cache = \cache::make('theme_apoa', 'main_page_cache');
        $this->log_start("Deleting old cache records.");
        $this->log_finish("Finished deleting old main page cache records");
        $this->populate_cache();
    }

    /**
     * Populate the cache with data describing the content of the main page.
     *
     * @return void
     */
    private function populate_cache(){
        global $OUTPUT;

        $mainpage =  new \theme_apoa\output\core\mainpage\mainpage;
        $sections = $mainpage->get_sections();
        foreach ($sections as $section){
            $mainpagecontainer = new \theme_apoa\output\core\mainpage\mainpagecontainer($section);
            $mainpagesection = $mainpagecontainer->get_child_class($OUTPUT);
            $this->cache->set($section, $mainpagesection);
            $this->log_start('Printing structure of new main page' . var_dump($mainpagesection));
        }

        $this->log_finish('End of new mainpage structure');
    }
}