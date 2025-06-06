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
 * Task schedule configuration for the theme_apoa plguin
 * 
 * @package   theme_apoa
 * @copyright 2024, Matthew Faulkner <matthewfaulkner@apoaevents.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$tasks = [
    [
        'classname' => 'theme_apoa\task\update_primary_nav',
        'blocking' => 0,
        'minute' => '30',
        'hour' => '*',
        'day' => '*',
        'month' => '1*',
    ],
    [
        'classname' => 'theme_apoa\task\update_mainpage',
        'blocking' => 0,
        'minute' => '1',
        'hour' => '*',
        'day' => '*',
        'month' => '1*',
    ],
    [
        'classname' => 'theme_apoa\task\refresh_warning_preferences',
        'blocking' => 0,
        'minute' => '*',
        'hour' => '*',
        'day' => '*',
        'month' => '1*',
    ],
];