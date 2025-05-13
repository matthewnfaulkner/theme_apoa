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
 *  Defines external functions for apoa theme.
 *
 * @package     theme_apoa
 * @copyright   2025 Matthew Faulkner matthewfaulkner@apoaevents.com
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */                                       

$functions = array(

    'theme_apoa_toggle_favourite_state' => array(
        'classname' => 'theme_apoa_external',
        'methodname' => 'toggle_favourite_state',
        'classpath' => 'theme/apoa/externallib.php',
        'description' => 'toggles the favourite state of a course.',
        'type' => 'write',
        'ajax' => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),

    'theme_apoa_cache_closed_modal' => array(
        'classname' => 'theme_apoa_external',
        'methodname' => 'cache_closed_modal',
        'classpath' => 'theme/apoa/externallib.php',
        'description' => 'keeps modal from popping up during the session',
        'type' => 'read',
        'loginrequired' => false,
        'ajax' => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),
    'theme_apoa_get_jumbo_config' => array(
        'classname' => 'theme_apoa_external',
        'methodname' => 'get_jumbo_config',
        'classpath' => 'theme/apoa/externallib.php',
        'description' => 'sends jumbo config to mobile users',
        'type' => 'read',
        'ajax' => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    )
);