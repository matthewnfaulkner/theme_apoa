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
 * The configuration for theme_apoa is defined here.
 *
 * @package     theme_apoa
 * @copyright   2023 Matthew Faulkner matthewnfaulkner@gmail.com
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Every file should have GPL and copyright in the header - we skip it in tutorials but you should not skip it for real.

// This line protects the file from being accessed by a URL directly.                                                               
defined('MOODLE_INTERNAL') || die();

// $THEME is defined before this page is included and we can define settings by adding properties to this global object.            
                                                                                                                                    
// The first setting we need is the name of the theme. This should be the last part of the component name, and the same             
// as the directory name for our theme.                                                                                             
$THEME->name = 'apoa';                                                                                                             
                                                                                                                                    
// This setting list the style sheets we want to include in our theme. Because we want to use SCSS instead of CSS - we won't        
// list any style sheets. If we did we would list the name of a file in the /style/ folder for our theme without any css file      
// extensions.                                                                                                                      
$THEME->sheets = [];                                                                                                                
$THEME->layouts = [
    // Most backwards compatible layout without the blocks.

    // The site home page.
    'base' => array(
        'file' => 'page.php',
        'regions' => array('content'),
    ),
    'frontpage' => array(
        'file' => 'frontpage.php',
        'regions' => array('jumbo', 'sidejumbo', 'elibrary', 'subjumbo1', 'subjumbo2', 'subjumbo3', 'resources'),
        'defaultregion' => 'sidejumbo',
        'options' => array('nonavbar' => false),
    ),
    'coursecategory' => array(
        'file' => 'page.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre',
        'options' => array('nonavbar' => false,)
    ),
    'incourse' => array(
        'file' => 'mod.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre',
        'options' => array('nonavbar' => false),
    ),
    'course' => array(
        'file' => 'page.php',
        'regions' => array('side-pre', 'content', 'subscriptions'),
        'defaultregion' => 'side-pre',
        'options' => array('nonavbar' => false,)
    ),
    // Server administration scripts.
    'admin' => array(
        'file' => 'page.php',
        'regions' => array('side-pre', 'content'),
        'defaultregion' => 'side-pre',
    ),
    'standard' => array(
        'file' => 'drawers.php',
        'regions' => array('side-pre', 'content'),
        'defaultregion' => 'content',
        'options' => array('nonavbar' => false,)
    ),
    'landingpage' => array(
        'file' => 'page.php',
        'regions' => array('side-pre', 'content'),
        'defaultregion' => 'content',
        'options' => array('nonavbar' => false,)
    ),
    // My dashboard page.
    'mydashboard' => array(
        'file' => 'drawers.php',
        'regions' => array('side-pre', 'content'),
        'defaultregion' => 'side-pre',
        'options' => array('nonavbar' => true, 'langmenu' => true),
    ),
    // The pagelayout used for reports.
    'report' => array(
        'file' => 'page.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre',
    ),
    // Standard layout with blocks.
    'standard' => array(
        'file' => 'page.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre',
    ),
];   
$topchildren = core_course_category::top()->get_children();
if (empty($topchildren)) {
    throw new moodle_exception('cannotviewcategory', 'error');
}

$THEME->editor_sheets = [];                                                                                                                                                                              
$THEME->parents = ['boost'];                                                                                                                        
$THEME->enable_dock = false;                                                                                                                          
$THEME->yuicssmodules = array();                                                                                                       
$THEME->rendererfactory = 'theme_overridden_renderer_factory';                                                                              
$THEME->requiredblocks = '';   
$THEME->addblockposition = BLOCK_ADDBLOCK_POSITION_FLATNAV;
$THEME->precsscallback = 'theme_apoa_get_pre_scss';
$THEME->removedprimarynavitems = ['courses', ''];
$THEME->scss = function($theme) {
    return theme_apoa_get_main_scss_content($theme);
};

