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
 * Returns the deep link resource via a POST to the platform.
 *
 * @package     theme_apoa
 * @copyright   2025 Matthew Faulkner
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use block_accessreview\external\get_module_data;
use core\http_client;
use enrol_lti\local\ltiadvantage\lib\launch_cache_session;
use enrol_lti\local\ltiadvantage\lib\issuer_database;
use enrol_lti\local\ltiadvantage\repository\application_registration_repository;
use enrol_lti\local\ltiadvantage\repository\deployment_repository;
use enrol_lti\local\ltiadvantage\repository\published_resource_repository;
use Packback\Lti1p3\ImsStorage\ImsCookie;
use Packback\Lti1p3\LtiDeepLinkResource;
use Packback\Lti1p3\LtiLineitem;
use Packback\Lti1p3\LtiMessageLaunch;
use Packback\Lti1p3\LtiServiceConnector;
use Packback\Lti1p3\LtiConstants;

global $CFG, $DB, $PAGE, $USER, $SESSION;

require_once('../../config.php');
require_once($CFG->dirroot .'/enrol/lti/lib.php');
require_once($CFG->libdir . '/filelib.php');
require_login(null, false);

confirm_sesskey();
$launchid = required_param('launchid', PARAM_TEXT);
$modules = optional_param_array('modules', [], PARAM_INT);
$grades = optional_param_array('grades', [], PARAM_INT);
$urls = optional_param_array('urls', [], PARAM_URL);
$urlsid = optional_param_array('urlsid', [], PARAM_INT);

$sesscache = new launch_cache_session();
$issdb = new issuer_database(new application_registration_repository(), new deployment_repository());
$cookie = new ImsCookie();
$serviceconnector = new LtiServiceConnector($sesscache, new http_client());
$messagelaunch = LtiMessageLaunch::fromCache($launchid, $issdb, $sesscache, $serviceconnector);

if (!$messagelaunch->isDeepLinkLaunch()) {
    throw new coding_exception('Configuration can only be accessed as part of a content item selection deep link '.
        'launch.');
}
$sesscache->purge();

// Get the selected resources and create the resource link content items to post back.
$resourcerepo = new published_resource_repository();
$resources = $resourcerepo->find_all_by_ids_for_user($modules, $USER->id);


$contentitems = [];
foreach ($resources as $resource) {

    $contentitem = LtiDeepLinkResource::new()
        ->setUrl($CFG->wwwroot . '/enrol/lti/launch.php')
        ->setCustomParams(['id' => $resource->get_uuid()])
        ->setTitle($resource->get_name());

    // If the activity supports grading, and the user has selected it, then include line item information.
    if ($resource->supports_grades() && in_array($resource->get_id(), $grades)) {
        require_once($CFG->libdir . '/gradelib.php');

        $lineitem = LtiLineitem::new()
            ->setScoreMaximum($resource->get_grademax())
            ->setResourceId($resource->get_uuid());

        $contentitem->setLineitem($lineitem);
    }
    $urlkey = array_search($resource->get_id(), $urlsid);
    if($urlkey !== false){
        $url = $urls[$urlkey];
        require_once($CFG->dirroot . '/mod/freepapervote/lib.php');
        $contextid = $resource->get_contextid();
        $context = $DB->get_record('context', array('id' => $contextid));

        if($context->contextlevel == CONTEXT_MODULE){
          if($cm = get_coursemodule_from_id('freepapervote', $context->instanceid, $resource->get_courseid())){

            $olpage = mod_freepapervote\helper::get_openlearningpage($url);

            $freepapervote = new stdClass();
            $freepapervote->linkurl = $url;
            $freepapervote->modid = $cm->instance;

            
            // Authenticate the platform user, which could be an instructor, an admin or a learner.
            // Auth code needs to be told about consumer secrets for the purposes of migration, since these reside in enrol_lti.
            $launchdata = $messagelaunch->getLaunchData();
            // To authenticate, we need the resource's account provisioning mode for the given LTI role.
            
            // Check if the "https://purl.imsglobal.org/spec/lti-dl/claim/deep_linking_settings" key exists
            if (isset($launchdata[LtiConstants::DL_DEEP_LINK_SETTINGS])) {
                // Check if the "deep_link_return_url" key exists within the deep_linking_settings sub-array
                if (isset($launchdata[LtiConstants::DL_DEEP_LINK_SETTINGS]['deep_link_return_url'])) {
                    // Extract the "deep_link_return_url"
                    $deepLinkReturnUrl = $launchdata[LtiConstants::DL_DEEP_LINK_SETTINGS]['deep_link_return_url'];
                    
                    // Find the position of the last '/' character
                    $parsed = explode('/', $deepLinkReturnUrl);
                    $empty  = array_pop($parsed);
                    $freepapervote->resourcelinkid  = array_pop($parsed);
                }
            }
            if($DB->sql_regex_supported()){
                $select = 'resourcelinkid ' . $DB->sql_regex() . ' :pattern';
                $firsthalf = reset(explode(':', $freepapervote->resourcelinkid));
                $params = ['pattern' => "$firsthalf:[a-zA-Z0-9]+"];
                
                $tags = [];

                if($jsonresponse = mod_freepapervote\helper::get_openlearningpage($url)){
                    $tagobjects =  mod_freepapervote\helper::parse_openlearning_tags($jsonresponse['tags'], $cm->instance);
                }

                if($resourceid = $DB->get_record_select('enrol_lti_resource_link', $select, $params, 'id')){
                    $freepapervote->resourceid = $resourceid->id;

                    
                    if($resourcelink = $DB->get_record('freepapervote_resource_link', array('resourceid' => $resourceid->id), 'id')){

                        $id = $resourcelink->id;
                        $resourcetags = $DB->get_records('freepapervote_resource_tags', array('resourceid' => $resourceid->id), 'tagid');

                        /*if($tagstodelete = array_diff_key($resourcetags, $tagobjects)){
                            list($deletesql, $deleteparams) = $DB->get_in_or_equal(array_keys($tagstodelete), SQL_PARAMS_QM);
                            $deleteparams[] = $id;

                            $DB->delete_records_select('freepapervote_resource_tags', "tagid $deletesql AND resourecid = ?", $deleteparams);
                        }*/
                        $freepapervote->id = $resourcelink->id;
                        $DB->update_record('freepapervote_resource_link', $freepapervote);
                    }
                    else{
                        $id = $DB->insert_record('freepapervote_resource_link', $freepapervote);    
                    }
                }
                else{
                    $freepapervote->resourceid = 0;
                    $id = $DB->insert_record('freepapervote_resource_link', $freepapervote);
                    \cache_helper::purge_by_event('newunlinkedresourceadded');
                }

                foreach($tagobjects as $tagobject) {
                    if(!$DB->record_exists('freepapervote_resource_tags', array('tagid' => $tagobject->id, 'resourceid' => $id))){
                        $tag = new stdClass();
                        $tag->tagid = $tagobject->id;
                        $tag->resourceid = $id;
                        $tag->timecreated = time();
                        $DB->insert_record('freepapervote_resource_tags', $tag);
                    }
                }
            }
          }
        }
    }


    $contentitems[] = $contentitem;
}



global $USER, $CFG, $OUTPUT;
$PAGE->set_context(context_system::instance());
$url = new moodle_url('/enrol/lti/configure.php');
$PAGE->set_url($url);
$PAGE->set_pagelayout('popup');
echo $OUTPUT->header();
$dl = $messagelaunch->getDeepLink();
$dl->outputResponseForm($contentitems);

