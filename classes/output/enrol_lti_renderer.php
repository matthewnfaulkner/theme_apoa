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
 * Renderer class for LTI enrolment
 *
 * @package    enrol_lti
 * @copyright  2016 John Okely <john@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_apoa\output;

defined('MOODLE_INTERNAL') || die();

use core\output\notification;
use enrol_lti\local\ltiadvantage\repository\application_registration_repository;
use enrol_lti\local\ltiadvantage\repository\deployment_repository;
use Packback\Lti1p3\LtiMessageLaunch;
use plugin_renderer_base;

/**
 * Renderer class for LTI enrolment
 *
 * @package    enrol_lti
 * @copyright  2016 John Okely <john@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrol_lti_renderer extends \enrol_lti\output\renderer {

    /**
     * Render the content item selection (deep linking 2.0) view
     *
     * This view is a form containing a list of courses and modules which, once selected and submitted, will result in
     * a list of LTI Resource Link Content Items being sent back to the platform, allowing resource link creation to
     * take place.
     *
     * @param LtiMessageLaunch $launch the launch data.
     * @param array $resources array of published resources available to the current user.
     * @return string html
     */
    public function render_published_resource_selection_view(LtiMessageLaunch $launch, array $resources): string {
        global $CFG;
        $context = [
            'action' => $CFG->wwwroot . '/theme/apoa/lti_configure.php',
            'launchid' => $launch->getLaunchId(),
            'hascontent' => !empty($resources),
            'sesskey' => sesskey(),
            'courses' => []
        ];
        foreach ($resources as $resource) {
            $context['courses'][$resource->get_courseid()]['fullname'] = $resource->get_coursefullname();
            if (!$resource->is_course()) {
                $context['courses'][$resource->get_courseid()]['modules'][] = [
                    'name' => $resource->get_name(),
                    'id' => $resource->get_id(),
                    'lineitem' => $resource->supports_grades()
                ];
                if (empty($context['courses'][$resource->get_courseid()]['shared_course'])) {
                    $context['courses'][$resource->get_courseid()]['shared_course'] = false;
                }
            } else {
                $context['courses'][$resource->get_courseid()]['shared_course'] = $resource->is_course();
                $context['courses'][$resource->get_courseid()]['id'] = $resource->get_id();
                $context['courses'][$resource->get_courseid()]['lineitem'] = $resource->supports_grades();
            }
        }
        $context['courses'] = array_values($context['courses']); // Reset keys for use in the template.
        return parent::render_from_template('enrol_lti/local/ltiadvantage/content_select', $context);
    }

}
