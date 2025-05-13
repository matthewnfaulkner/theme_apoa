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
 *  Favourite exporter.
 *
 * @package     theme_apoa
 * @copyright   2025 Matthew Faulkner matthewfaulkner@apoaevents.com
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */  

namespace theme_apoa\external\exporters;

use core\external\exporter;
use renderer_base;
use moodle_url;

/**
 * Class Favourite
 * 
 * Exporter class for apoa favourites
 */

class favourite extends exporter {


    /**
     * Return the list of properties.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'id' => [
                'type' => PARAM_INT
            ],
            'username' => [
                'type' => PARAM_ALPHANUMEXT
            ],
            'description' => [
                'type' => PARAM_RAW,
            ],
            'descriptionformat' => [
                'type' => PARAM_INT,
            ],
            'favourited' => [
                'type' => PARAM_BOOL,
            ],
            'courseid' => [
                'type' => PARAM_INT,
            ],
        ];
    }

    /**
     * Return the list of additional properties.

     * @return array
     */
    protected static function define_other_properties() {
        return [
            'profileurl' => [
                'type' => PARAM_URL
            ]
        ];
    }

    /**
     * Returns a list of objects that are related.
     *
     * @return array
     */
    protected static function define_related() {
        return [
            'context' => 'context',
        ];
    }

    /**
     * Get the formatting parameters for the description.
     *
     * @return array
     */
    protected function get_format_parameters_for_description() {
        return [
            'component' => 'core_user',
            'filearea' => 'description',
            'itemid' => $this->data->id
        ];
    }

    /**
     * Get the additional values to inject while exporting.
     *
     * @param renderer_base $output The renderer.
     * @return array Keys are the property names, values are their values.
     */
    protected function get_other_values(renderer_base $output) {

        $profileurl = new moodle_url('/user/profile.php', ['id' => $this->data->id]);

        return [
            'profileurl' => $profileurl->out(false),
        ];
    }
}