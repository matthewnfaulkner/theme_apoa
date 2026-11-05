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
        $cache->set('primarynav', \theme_apoa\navigation\primary_nav_data::get());
        $this->log_finish("Finished generating new navigation records");
    }
}
