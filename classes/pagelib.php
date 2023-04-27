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
 * This file contains the moodle_page class. There is normally a single instance
 * of this class in the $PAGE global variable. This class is a central repository
 * of information about the page we are building up to send back to the user.
 *
 * @package core
 * @category page
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


class apoa_page extends moodle_page{

    /**
     * Returns the secondary navigation object
     *
     * @todo MDL-74939 Remove support for old 'local\views\secondary' class location
     * @return secondary
     */
    protected function magic_get_secondarynav() {
        if ($this->_secondarynav === null) {
            $class = 'theme_apoa\navigation\views\secondary';
            // Try and load a custom class first.
            if (class_exists("mod_{$this->activityname}\\navigation\\views\\secondary")) {
                $class = "mod_{$this->activityname}\\navigation\\views\\secondary";
            } else if (class_exists("mod_{$this->activityname}\\local\\views\\secondary")) {
                // For backwards compatibility, support the old location for this class (it was in a
                // 'local' namespace which shouldn't be used for core APIs).
                debugging("The class mod_{$this->activityname}}\\local\\views\\secondary uses a deprecated " .
                        "namespace. Please move it to mod_{$this->activityname}\\navigation\\views\\secondary.",
                        DEBUG_DEVELOPER);
                $class = "mod_{$this->activityname}\\local\\views\\secondary";
            }

            $this->_secondarynav = new $class($this);
            $this->_secondarynav->initialise();
        }
        return $this->_secondarynav;
    }

    protected function magic_get_primarynav() {
        if ($this->_primarynav === null) {
            $class = 'theme_apoa\navigation\views\primary';
            // Try and load a custom class first.
            if (class_exists("mod_{$this->activityname}\\navigation\\views\\primary")) {
                $class = "mod_{$this->activityname}\\navigation\\views\\primary";
            } else if (class_exists("mod_{$this->activityname}\\local\\views\\primary")) {
                // For backwards compatibility, support the old location for this class (it was in a
                // 'local' namespace which shouldn't be used for core APIs).
                debugging("The class mod_{$this->activityname}}\\local\\views\\primary uses a deprecated " .
                        "namespace. Please move it to mod_{$this->activityname}\\navigation\\views\\primary.",
                        DEBUG_DEVELOPER);
                $class = "mod_{$this->activityname}\\local\\views\\primary";
            }

            $this->_primarynav = new $class($this);
            $this->_primarynav->initialise();
        }
        return $this->_primarynav;
    }

}
