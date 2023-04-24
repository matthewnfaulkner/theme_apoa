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

/**
 * External forum API
 *
 * @package    mod_forum
 * @copyright  2012 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

use mod_forum\local\exporters\post as post_exporter;
use mod_forum\local\exporters\discussion as discussion_exporter;
use theme_apoa\external\exporters\favourite as favourite_exporter;

class theme_apoa_external extends external_api {

   /**
     * Toggle the favouriting value for the discussion provided
     *
     * @param int $discussionid The discussion we need to favourite
     * @param bool $targetstate The state of the favourite value
     * @return array The exported discussion
     */
    public static function toggle_favourite_state($courseid, $targetstate) {
        global $DB, $PAGE, $USER;

        $params = self::validate_parameters(self::toggle_favourite_state_parameters(), [
            'courseid' => $courseid,
            'targetstate' => $targetstate
        ]);

        $usercontext = context_user::instance($USER->id);

        $coursecontext = context_course::instance($courseid);
        self::validate_context($coursecontext);
        $PAGE->set_context($coursecontext);

        $ufservice = \core_favourites\service_factory::get_service_for_user_context($usercontext);
        $isfavourited = $ufservice->favourite_exists('core_course', 'courses', $courseid, $coursecontext);

        $favouritefunction = $targetstate ? 'create_favourite' : 'delete_favourite';
        if ($isfavourited != (bool) $params['targetstate']) {
            $favourite = $ufservice->{$favouritefunction}('core_course', 'courses', $courseid, $coursecontext);
        }
        $data = (object) [
            'id' => $courseid,
            'username' => 'batman',
            'description' => 'Hello __world__!',
            'descriptionformat' => FORMAT_MARKDOWN,
            'favourited' => !$isfavourited,
            'courseid' => $courseid
        ];
        $related = ['context' => $coursecontext];
        $exporter = new favourite_exporter($data, $related);
        
        return $exporter->export($PAGE->get_renderer('core', 'course'));
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function toggle_favourite_state_returns() {
        return favourite_exporter::get_read_structure();
    }

     /**
     * Defines the parameters for the toggle_favourite_state method
     *
     * @return external_function_parameters
     */
    public static function toggle_favourite_state_parameters() {
        return new external_function_parameters(
            [
                'courseid' => new external_value(PARAM_INT, 'The discussion to subscribe or unsubscribe'),
                'targetstate' => new external_value(PARAM_BOOL, 'The target state')
            ]
        );
    }
}
