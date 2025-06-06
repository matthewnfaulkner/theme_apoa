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
 *  Theme functions.
 *
 * @package     theme_apoa
 * @copyright   2025 Matthew Faulkner matthewfaulkner@apoaevents.com
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */                                       

namespace theme_apoa\navigation\output;

use renderable;
use renderer_base;
use templatable;
use flat_navigation_node;
use stdClass;

/**
 * more menu navigation renderable
 *
 * @package     theme_apoa
 * @category    navigation
 * @copyright   2025 Matthew Faulkner
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class more_menu implements renderable, templatable {

    protected $content;
    protected $navbarstyle;
    protected $haschildren;
    protected $istablist;

    /**
     * Constructor for this class.
     *
     * @param object $content Navigation objects.
     * @param string $navbarstyle class name.
     * @param bool $haschildren The content has children.
     * @param bool $istablist When true, the more menu should be rendered and behave with a tablist ARIA role.
     *                        If false, it's rendered with a menubar ARIA role. Defaults to false.
     */
    public function __construct(object $content, string $navbarstyle, bool $haschildren = true, bool $istablist = false) {
        $this->content = $content;
        $this->navbarstyle = $navbarstyle;
        $this->haschildren = $haschildren;
        $this->istablist = $istablist;
    }

    /**
     * Return data for rendering a template.
     *
     * @param renderer_base $output The output
     * @return array Data for rendering a template
     */
    public function export_for_template(renderer_base $output): array {
        $data = [
            'navbarstyle' => $this->navbarstyle,
            'istablist' => $this->istablist,
        ];
        if ($this->haschildren) {
            // The node collection doesn't have anything to render so exit now.
            if (!isset($this->content->children) || count($this->content->children) == 0) {
                return [];
            }
            // Find all nodes that have children and are defined to show the children in a submenu.
            // For each of these nodes we would like to display a dropdown menu and in order to achieve that
            // (as required by the template) we need to set the node's property 'moremenuid' to a new unique value and
            // 'haschildren' to true.
            foreach ($this->content->children as &$item) {
                if ($item->showchildreninsubmenu && isset($this->content->children) &&
                        count($this->content->children) > 0) {
                    $item->moremenuid = uniqid();
                    $item->haschildren = true;
                }
            }
            //array to store nodes for flat navigation
            $data['flatnavigation'] = [];

            //check for module related nodes and add under "activity menu" in flat nav.
            if($module_navigation = $this->content->children->find('modulemenu')){
                if($module_navigation->has_children()) {
                    $modmenu = new stdClass;
                    $modmenu->title = get_string('activitymenuheading', 'theme_apoa');;
                    $modmenu->id = "modmenu";
                    $modmenu->items = [];
                    $modmenu->offset = 'menuoffset-' . count($data['flatnavigation']) * 30;
                    foreach($module_navigation->children as $child){
                        $flatnode = new flat_navigation_node($child, false);
                        $modmenu->items[] = $flatnode;

                    }
                    $data['flatnavigation'][] = $modmenu;
                }
                $this->content->children->remove('modulemenu');
            }

            //check for course related nodes and add under "Action Menu" in flat nav
            if($course_navigation = $this->content->children->find('coursenavigation')){
                if($course_navigation->has_children()){
                    $coursemenu = new stdClass;
                    $coursemenu->title = get_string('coursemenuheading', 'theme_apoa');
                    $coursemenu->id = "coursemenu";
                    $coursemenu->items = [];
                    $coursemenu->offset = 'menuoffset-' . count($data['flatnavigation']) * 30;
                    foreach($course_navigation->children as $child){
                        $flatnode = new flat_navigation_node($child, false);
                        $coursemenu->items[] = $flatnode;

                    }
                    
                    $data['flatnavigation'][] = $coursemenu;
                }
                $this->content->children->remove('coursenavigation');
            }

            //Check for category related nodes and add to flat nav under "Category Menu"
            if($category_navigation = $this->content->children->find('categorynavigation')){
                if($category_navigation->has_children()){
                    $coursemenu = new stdClass;
                    $coursemenu->title = get_string('categorymenuheading', 'theme_apoa');
                    $coursemenu->id = "categorymenu";
                    $coursemenu->items = [];
                    $coursemenu->offset = 'menuoffset-' . count($data['flatnavigation']) * 30;
                    foreach($category_navigation->children as $child){
                        $flatnode = new flat_navigation_node($child, false);
                        $coursemenu->items[] = $flatnode;

                    }
                    
                    $data['flatnavigation'][] = $coursemenu;
                }
                $this->content->children->remove('categorynavigation');
            }

            $data['nodecollection'] = $this->content;
        } else {
            $data['nodearray'] = (array) $this->content;
        }
        $data['moremenuid'] = uniqid();

        return $data;
    }

}
