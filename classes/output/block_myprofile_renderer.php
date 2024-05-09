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
 * myprofile block rendrer
 *
 * @package    block_myprofile
 * @copyright  2018 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_myprofile\output;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/mod/committee/lib.php');
require_once($CFG->dirroot . '/local/subscriptions/lib.php');

use plugin_renderer_base;

/**
 * myprofile block renderer
 *
 * @package    block_myprofile
 * @copyright  2018 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {

    /**
     * Return the main content for the block myprofile.
     *
     * @param myprofile $myprofile The myprofile renderable
     * @return string HTML string
     */
    public function render_myprofile(myprofile $myprofile) {

        global $USER, $CFG;

        $template = $myprofile->export_for_template($this);

        $usercommittees = committee_get_user_committees($USER->id);

        $committeemenu = new \mod_committee\output\committee_menu($usercommittees);
    
        $template->usercommittees = $this->render($committeemenu);

        $template->usermembershipnumbers = array_values(local_subscriptions_get_user_membershipnumber($USER->id));

        
        if(!empty($CFG->enabledashboard)){
            $template->dashboard = true;
            $template->dashboardurl = new \moodle_url('/my/');
        }

        return $this->render_from_template('block_myprofile/myprofile', $template);
    }
}
