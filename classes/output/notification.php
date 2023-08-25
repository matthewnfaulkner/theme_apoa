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
 * Notification renderable component.
 *
 * @package    core
 * @copyright  2015 Jetha Chan
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_apoa\output;

/**
 * Data structure representing a notification.
 *
 * @copyright 2015 Jetha Chan
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.9
 * @package core
 * @category output
 */
class notification extends \core\output\notification {


    private string $name;

    private int $userid;
    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return array data context for a mustache template
     */

    public function set_name_and_user($name, $userid){
        $this->name = $name;
        $this->userid = $userid;
    }

    public function export_for_template(\renderer_base $output) {
        return array(
            'message'       => clean_text($this->message),
            'extraclasses'  => implode(' ', $this->extraclasses),
            'announce'      => $this->announce,
            'closebutton'   => $this->closebutton,
            'issuccess'         => $this->messagetype === 'success',
            'isinfo'            => $this->messagetype === 'info',
            'iswarning'         => $this->messagetype === 'warning',
            'iserror'           => $this->messagetype === 'error',
            'specialtype'       => $this->name,
            'userid'            => $this->userid

        );
    }

    public function get_template_name() {
        $templatemappings = [
            // Current types mapped to template names.
            'success'           => 'core/notification_success',
            'info'              => 'core/notification_info',
            'warning'           => 'core/notification_warning',
            'error'             => 'core/notification_error',
            'special'           => 'theme_apoa/notification_warning_apoa'
        ];

        if (isset($templatemappings[$this->messagetype])) {
            return $templatemappings[$this->messagetype];
        }
        return $templatemappings['error'];
    }
}
