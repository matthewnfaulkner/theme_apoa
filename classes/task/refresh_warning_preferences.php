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
 *  Defines task to refresh warning preferences
 *
 * @package     theme_apoa
 * @copyright   2025 Matthew Faulkner matthewfaulkner@apoaevents.com
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */                                       
namespace theme_apoa\task;


/**
 * Class refresh_warning_preferences
 * 
 * Creates a scheduled task that refreshes a user warning 
 * preferences after a certain period of time
 * 
 */
class refresh_warning_preferences extends \core\task\scheduled_task {

    use \core\task\logging_trait;

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('refresh_warning_preferences', 'theme_apoa');
    }

    /**
     * Execute the task.
     */
    public function execute() {
        
        //define cutoff as now + 1 month
        $cutoff = time() + 2629743;
        $this->log_start("Deleting old preferences");

        //get functions that that call function
        $pluginswithfunction = get_plugins_with_function('clear_apoa_notification_preferences', 'lib.php');
        foreach ($pluginswithfunction as $plugins) {
            foreach ($plugins as $function) {
                $function($cutoff);
            }
        }   

        $this->log_finish("Finished Deleting old preferences");
    }

}