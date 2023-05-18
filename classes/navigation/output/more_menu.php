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

namespace theme_apoa\navigation\output;

use renderable;
use renderer_base;
use templatable;
use custom_menu;
use stdClass;

/**
 * more menu navigation renderable
 *
 * @package     core
 * @category    navigation
 * @copyright   2021 onwards Adrian Greeve
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class more_menu extends \core\navigation\output\more_menu {

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
        $collection = new stdClass();
        if ($this->haschildren) {

                // Find all nodes that have children and are defined to show the children in a submenu.
                // For each of these nodes we would like to display a dropdown menu and in order to achieve that
                // (as required by the template) we need to set the node's property 'moremenuid' to a new unique value and
                // 'haschildren' to true.
                $count = 0;
                foreach ($this->content as &$item) {
                    if (!isset($item->children) || count($item->children) == 0) {
                        $collection->$count = $item;
                    }
                    if ($item->showchildreninsubmenu && isset($item->children) &&
                            count($item->children) > 0) {
                        $item->moremenuid = uniqid();
                        $item->haschildren = true;
                        $collection->$count = $item;
                    }
                    $count += 1;
                }

                $data['nodecollection'] = $collection;
                $data['nodearray'] = (array) $collection;
        } 
            
        
        $data['moremenuid'] = uniqid();

        return $data;
    }

}
