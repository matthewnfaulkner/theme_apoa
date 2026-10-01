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
 * Previews a single main page slide, as saved in the slider settings.
 *
 * Renders the main page layout with only the previewed slide and empty space below.
 *
 * @package    theme_apoa
 * @copyright  2026 Matthew Faulkner matthewfaulkner@apoaevents.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/theme/apoa/lib.php');

$slide = required_param('slide', PARAM_INT);

require_admin();

$component = 'theme_apoa';
$slidecount = (int) get_config($component, 'slidecount');
if ($slide < 1 || $slide > $slidecount) {
    throw new moodle_exception('invalidslide', $component, '', $slide);
}

$PAGE->set_context(context_system::instance());
$PAGE->set_url(new moodle_url('/theme/apoa/preview_slider.php', ['slide' => $slide]));
$PAGE->set_pagelayout('frontpage');
$PAGE->set_pagetype('theme-apoa-preview-slider');
$PAGE->set_title(get_string('slidepreview', $component));
$PAGE->requires->js_call_amd('theme_apoa/mymodal', 'init');

$jumbomain = [
    'jumboslides' => [[
        'index' => 1,
        'slidecontent' => (string) get_config($component, 'slide' . $slide),
        'slidelink' => (string) get_config($component, 'slidelink' . $slide),
    ]],
];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('theme_apoa/mainpage/jumbo/jumbopreview', $jumbomain);
echo $OUTPUT->footer();
