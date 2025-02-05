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
 * A drawer based layout for the boost theme.
 *
 * @package   theme_boost
 * @copyright 2021 Bas Brands
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\output\notification;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/behat/lib.php');
require_once($CFG->dirroot . '/course/lib.php');

// Add block button in editing mode.
$addblockbutton = $OUTPUT->addblockbutton();
$PAGE->set_include_region_main_settings_in_header_actions(true);

user_preference_allow_ajax_update('drawer-open-index', PARAM_BOOL);
user_preference_allow_ajax_update('drawer-open-block', PARAM_BOOL);

if (isloggedin()) {
    $courseindexopen = (get_user_preferences('drawer-open-index', true) == true);
    $blockdraweropen = (get_user_preferences('drawer-open-block') == true);
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

$isdashboard = $PAGE->pagelayout == 'mydashboard';
$blockshtmlcontent = $OUTPUT->blocks('content');

$hasblocks = (strpos($blockshtml, 'data-block=') !== false || !empty($addblockbutton));
if (!$hasblocks) {
    $blockdraweropen = false;
}


$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$forceblockdraweropen = $OUTPUT->firstview_fakeblocks();


$courseindex = '';
$coursenavigation = '';

$secondarynavigation = false;
$overflow = '';
if (is_siteadmin($USER->id)) {
    if ($PAGE->has_secondary_navigation()) {
        $tablistnav = $PAGE->has_tablist_secondary_navigation();
        $moremenu = new \theme_apoa\navigation\output\more_menu($PAGE->secondarynav, 'nav-tabs', true, $tablistnav);
        if($secondarynavigation = $moremenu->export_for_template($OUTPUT)) {
            $coursenavigation .= $OUTPUT->render_from_template('theme_apoa/flat_navigation', $secondarynavigation);
        }
        $overflowdata =null;
        if (!is_null($overflowdata)) {
            $overflow = $overflowdata->export_for_template($OUTPUT);
        }
    }
}else{
    if ($PAGE->has_secondary_navigation()) {
        $tablistnav = $PAGE->has_tablist_secondary_navigation();
        $moremenu = new \theme_apoa\navigation\output\more_menu($PAGE->secondarynav, 'nav-tabs', true, $tablistnav);
        if($secondarynavigation = $moremenu->export_for_template($OUTPUT)) {
            $coursenavigation .= $OUTPUT->render_from_template('theme_apoa/flat_navigation', $secondarynavigation);
        }
        $overflowdata=null;
        if (!is_null($overflowdata)) {
            $overflow = $overflowdata->export_for_template($OUTPUT);
        }
    }

}


$courseindex = core_course_drawer();
if (!$courseindex && !$coursenavigation) {
    $courseindexopen = false;   
}

if($coursenavigation){
    $indexoffset = "indexoffset-" . count($secondarynavigation['flatnavigation']) * 30;
}

$needdrawer = $coursenavigation || $courseindex;


$PAGE->requires->css('/mod/lightboxgallery/assets/skins/sam/gallery-lightbox-skin.css');
$PAGE->requires->yui_module('moodle-mod_lightboxgallery-lightbox', 'M.mod_lightboxgallery.init');
 
$primary = new \theme_apoa\navigation\output\primary($PAGE);

$renderer = $PAGE->get_renderer('core');
$primarymenu = $primary->export_for_template($renderer);


if ($COURSE->format == 'apoapage') {
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

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'sitenamefull' => format_string($SITE->fullname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'courseindexopen' => $courseindexopen,
    'needdrawer' => $needdrawer,
    'blockdraweropen' => $blockdraweropen,
    'courseindex' => $courseindex,
    'coursenavigation' => $coursenavigation,
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
    'indexoffset' => $indexoffset,
];

echo $OUTPUT->render_from_template('theme_apoa/page', $templatecontext);
