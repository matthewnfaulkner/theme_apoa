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
require_once('lib.php');

use mod_forum\local\exporters\post as post_exporter;
use mod_forum\local\exporters\discussion as discussion_exporter;
use theme_apoa\external\exporters\favourite as favourite_exporter;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_warnings;

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

    

       /**
     * Toggle the favouriting value for the discussion provided
     *
     * @param int $discussionid The discussion we need to favourite
     * @param bool $targetstate The state of the favourite value
     * @return array The exported discussion
     */
    public static function cache_closed_modal($closemodal) {    

        $params = self::validate_parameters(self::cache_closed_modal_parameters(), [
            'closemodal' => $closemodal
        ]);
        if(!isloggedin() || isguestuser()){
            return array('success' => false);
        }
        $modalcache = \cache::make('theme_apoa', 'modal_cache');

        $modalcache->set('hasopened', true);
        
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
     * Defines the parameters for the toggle_favourite_state method
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
     * @param int $discussionid The discussion we need to favourite
     * @param bool $targetstate The state of the favourite value
     * @return array The exported discussion
     */
    public static function get_jumbo_config() {    

        global $OUTPUT, $DB, $USER;

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

        $result['config'] = [
            'jumbotitle' => get_config($component, 'jumbotitle'),
            'jumbodescription' => get_config($component, 'jumbodescription'),
            'jumbovideoflag' => get_config($component, 'jumbovideoflag'),
            'jumbotag' => get_config($component, 'jumbotag'),
            'jumbobanner' => theme_apoa_get_file_from_setting('jumbobanner'),
            'jumbobannerposter' => theme_apoa_get_file_from_setting('jumbobannerposter'),
            'jumbovideo' => theme_apoa_get_file_from_setting('jumbovideo'),
            'jumbobannerlogo' => theme_apoa_get_file_from_setting('jumbobannerlogo'),
            'jumbourl' => get_config($component, 'jumbolink'),
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
                'jumbotitle' => new external_value(PARAM_TEXT, 'Title of Jumbo'),
                'jumbodescription' => new external_value(PARAM_TEXT, 'Description of Jumbo'),
                'jumbovideoflag' => new external_value(PARAM_BOOL, 'Flag to show video or not'),
                'jumbotag' => new external_value(PARAM_TEXT, 'Tag of Jumbo'),
                'jumbobanner' => new external_value(PARAM_URL, 'URL of Jumbo banner image'),
                'jumbobannerposter' => new external_value(PARAM_URL, 'URL of Jumbo banner image if video fails to load'),
                'jumbovideo' => new external_value(PARAM_URL, 'URL of Jumbo banner video'),
                'jumbobannerlogo' => new external_value(PARAM_URL, 'URL of site logo'),
                'jumbourl' => new external_value(PARAM_URL, 'URL of Jumbo link'),
                'jumbostartdate' => new external_value(PARAM_INT, 'Start date of Jumbo'),
                'jumboannouncement' => new external_value(PARAM_TEXT, 'first announcment text'),
                'announcementlink' => new external_value(PARAM_URL, 'URL announcmentes forum'),
                'announcementid' => new external_value(PARAM_INT, 'id of announcements'),
            )),
            'warnings' => new external_warnings(),
            )
        );
    }

     /**
     * Defines the parameters for the toggle_favourite_state method
     *
     * @return external_function_parameters
     */
    public static function get_jumbo_config_parameters() {
        return new external_function_parameters(array());
    }
}
