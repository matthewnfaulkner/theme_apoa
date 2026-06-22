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
 * External theme_apoa API
 *
 * @package    theme_apoa
 * @copyright  2025 Matthew Faulkner matthewfaulkner@apoaevents.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");
require_once('lib.php');

use theme_apoa\external\exporters\favourite as favourite_exporter;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_warnings;

/**
 * Theme class for external api methods.
 *
 * @package    theme_apoa
 * @copyright  2025 Matthew Faulkner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_apoa_external extends external_api {
    
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

   /**
     * Toggle the favouriting value for the discussion provided
     *
     * @param int $discussionid The discussion we need to favourite
     * @param bool $targetstate The state of the favourite value
     * @return array The exported discussion
     */
    public static function toggle_favourite_state(int $courseid, bool $targetstate) {
        global $PAGE, $USER;

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
            $ufservice->{$favouritefunction}('core_course', 'courses', $courseid, $coursecontext);
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
     * Defines the parameters for the cache_closed method
     *
     * @return external_function_parameters
     */
    public static function cache_closed_modal_parameters() {
        return new external_function_parameters(
            [
                'closemodal' => new external_value(PARAM_BOOL, 'The target state')
            ]
        );
    }
    /**
     * Toggle the favouriting value for the discussion provided
     *
     * @param int $closemodal The discussion we need to favourite
     * @return array result of close
     */
    public static function cache_closed_modal($closemodal) {    

        global $SESSION;

        $params = self::validate_parameters(self::cache_closed_modal_parameters(), [
            'closemodal' => $closemodal
        ]);

        $SESSION->mainmodalclosed = true;
        
        return array('success' => true);
        
        
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function cache_closed_modal_returns() {
        return new external_single_structure(array(
            'success' => new external_value(PARAM_BOOL, 'success')
            )
        );
    }

   /**
     * Defines the parameters for the get_jumbo_config method
     *
     * @return external_function_parameters
     */
    public static function get_jumbo_config_parameters() {
        return new external_function_parameters(array());
    }

    /**
     * Get configuration of jumbo for mobile
     *
     * @return array The exported jumbo
     */
    public static function get_jumbo_config() {    

        global $DB, $USER, $CFG;

        require_once($CFG->dirroot . '/theme/apoa/lib.php');
        
        $component = 'theme_apoa';
        $result = [];
        if($courseid = get_config($component, 'jumboid')){
            $course = get_course($courseid);
            $startdate = $course->startdate;
            
        }
        if($announcementsid = get_config($component, 'jumboannouncementsid')){
            if($cm = get_coursemodule_from_id('forum', $announcementsid)){
                if($forum = $DB->get_record('forum', array('id' => $cm->instance))){
                    $course = get_course($cm->course);
                    $modcontext = \context_module::instance($cm->id);
                    $entityfactory = \mod_forum\local\container::get_entity_factory();
                    $forumentity = $entityfactory->get_forum_from_stdclass($forum, $modcontext, $cm, $course);
                    $discussionsummaries = mod_forum_get_discussion_summaries($forumentity, $USER, null, 0, 0, 1);
                    $firstdiscussionsummary = reset($discussionsummaries);
                    if($firstdiscussionsummary){
                        $firstdiscussion = $firstdiscussionsummary->get_discussion();
                        $firstposttext = $firstdiscussion->get_name();
                        $announcementlink = new moodle_url('/mod/forum/discuss.php', array('d' => $firstdiscussion->get_id()));
                    }
                   
                }
            }
        }

        $slidecount = get_config($component, 'slidecount');
        $slides = [];
        for ($x = 1; $x <= $slidecount; $x++) {
            $slides[] = [
                'index' => $x,
                'slidecontent' => (string) get_config($component, 'slide' . $x),
                'slidelink' => (string) get_config($component, 'slidelink' . $x),
            ];
        }

        $result['config'] = [
            'slides' => $slides,
            'jumbostartdate' => $startdate,
            'jumboannouncement' => $firstposttext,
            'announcementlink' => $announcementlink? $announcementlink->out(): "",
            'announcementid' => $announcementsid,
        ];

        $result['warnings'] = array();
        return $result;
        
        
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function get_jumbo_config_returns() {
        return new external_single_structure(array(
            'config' => new external_single_structure(array(
                'slides' => new external_multiple_structure(
                    new external_single_structure(array(
                        'index' => new external_value(PARAM_INT, 'Slide position, starting at 1'),
                        'slidecontent' => new external_value(PARAM_RAW, 'Raw HTML content of the slide'),
                        'slidelink' => new external_value(PARAM_URL, 'URL the slide links to', VALUE_OPTIONAL),
                    )),
                    'List of jumbo slider slides'
                ),
                'jumbostartdate' => new external_value(PARAM_INT, 'Start date of Jumbo', VALUE_OPTIONAL),
                'jumboannouncement' => new external_value(PARAM_TEXT, 'first announcment text', VALUE_OPTIONAL),
                'announcementlink' => new external_value(PARAM_URL, 'URL announcmentes forum'),
                'announcementid' => new external_value(PARAM_INT, 'id of announcements', VALUE_OPTIONAL),
            )),
            'warnings' => new external_warnings(),
            )
        );
    }

}

