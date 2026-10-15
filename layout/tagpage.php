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
 * A tag page layout for apoa theme
 *
 * @package   theme_apoa
 * @copyright 2025 Matthew Faulkner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/behat/lib.php');
require_once($CFG->dirroot . '/course/lib.php');

// Add block button in editing mode.
$addblockbutton = $OUTPUT->addblockbutton();


if (isloggedin()) {
    $courseindexopen = (get_user_preferences('drawer-open-index', true) == true);
    $blockdraweropen = (get_user_preferences('drawer-open-block') == true);
} else {
    $courseindexopen = false;
    $blockdraweropen = false;
}

if (defined('BEHAT_SITE_RUNNING')) {
    $blockdraweropen = true;
}

$extraclasses = ['uses-drawers'];
if ($courseindexopen) {
    $extraclasses[] = 'drawer-open-index';
}

$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = (strpos($blockshtml, 'data-block=') !== false || !empty($addblockbutton));
if (!$hasblocks) {
    $blockdraweropen = false;
}
$courseindex = core_course_drawer();
if (!$courseindex) {
    $courseindexopen = false;
}

$blockshtml = $OUTPUT->blocks('journalclub-index');
$searchblock = $OUTPUT->custom_block_region('content');
$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$forceblockdraweropen = $OUTPUT->firstview_fakeblocks();

$secondarynavigation = false;
$overflow = '';
if (is_siteadmin($USER->id)) {
    if ($PAGE->has_secondary_navigation()) {
        $tablistnav = $PAGE->has_tablist_secondary_navigation();
        $moremenu = new \core\navigation\output\more_menu($PAGE->secondarynav, 'nav-tabs', true, $tablistnav);
        $secondarynavigation = $moremenu->export_for_template($OUTPUT);
        //$overflowdata = $PAGE->secondarynav->get_overflow_menu_data();
        $overflowdata =null;
        if (!is_null($overflowdata)) {
            $selectmenu = new \core\output\select_menu(
                'tertiarynavigation',
                $overflowdata->urls,
                $overflowdata->selected,
            );
            $selectmenu->set_label($overflowdata->label, $overflowdata->labelattributes);
            $overflow = $selectmenu->export_for_template($OUTPUT);
        }
    }
}else{
    if ($PAGE->has_secondary_navigation()) {
        $tablistnav = $PAGE->has_tablist_secondary_navigation();
        $moremenu = new \core\navigation\output\more_menu($PAGE->secondarynav, 'nav-tabs', true, $tablistnav);
        $secondarynavigation = $moremenu->export_for_template($OUTPUT);
        //$overflowdata = $PAGE->secondarynav->get_overflow_menu_data();
        $overflowdata=null;
        if (!is_null($overflowdata)) {
            $selectmenu = new \core\output\select_menu(
                'tertiarynavigation',
                $overflowdata->urls,
                $overflowdata->selected,
            );
            $selectmenu->set_label($overflowdata->label, $overflowdata->labelattributes);
            $overflow = $selectmenu->export_for_template($OUTPUT);
        }
    }

}

$PAGE->requires->js_call_amd('mod_lightboxgallery/lightbox', 'init');






$primary = new \theme_apoa\navigation\output\primary($PAGE);


$renderer = $PAGE->get_renderer('core');
$primarymenu = $primary->export_for_template($renderer);


if ($COURSE->format == 'apoapage') {
    // These class names don't match their files, so the autoloader can't find them.
    require_once(__DIR__ . '/../classes/output/core/tag.php');
    require_once(__DIR__ . '/../classes/output/core/lists/pagelist.php');
    $sidebar = new \theme_apoa\output\core\lists\theme_apoa_pagelist($COURSE);
    $sidebaroutput = $sidebar->export_for_template($renderer);
}else{
    $sidebaroutput = '';
}



$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions() && !$PAGE->has_secondary_navigation();
// If the settings menu will be included in the header then don't add it here.
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;

$header = $PAGE->activityheader;
$headercontent = $header->export_for_template($renderer);

$coursefullname = ($PAGE->course?->fullname) ? format_string(
    $PAGE->course->fullname,
    true,
    ['context' => context_course::instance($PAGE->course->id), 'escape' => false],
) : '';
$courseurl = $PAGE->course ? new \core\url('/course/view.php', ['id' => $PAGE->course->id]) : null;

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'sitenamefull' => format_string($SITE->fullname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'coursefullname' => $coursefullname,
    'courseurl' => $courseurl ? $courseurl->out(false) : null,
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'courseindexopen' => $courseindexopen,
    'blockdraweropen' => $blockdraweropen,
    'courseindex' => $courseindex,
    'primarymoremenu' => $primarymenu['moremenu'],
    'secondarymoremenu' => $secondarynavigation ?: false,
    'mobileprimarynav' => $primarymenu['mobileprimarynav'],
    'usermenu' => $primarymenu['user'],
    'langmenu' => $primarymenu['lang'],
    'forceblockdraweropen' => $forceblockdraweropen,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'overflow' => $overflow,
    'headercontent' => $headercontent,
    'addblockbutton' => $addblockbutton,
    'sidebar' => $sidebaroutput,
    'searchblock' => $searchblock,
];

echo $OUTPUT->render_from_template('theme_apoa/page', $templatecontext);
