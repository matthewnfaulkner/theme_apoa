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
 *  Defines Primary Navigation renderable
 *
 * @package     theme_apoa
 * @copyright   2025 Matthew Faulkner matthewfaulkner@apoaevents.com
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */                                       

namespace theme_apoa\navigation\output;

use renderer_base;
use custom_menu;
use stdClass;

/**
 * Primary navigation renderable
 *
 * This file combines primary nav, custom menu, lang menu and
 * usermenu into a standardized format for the frontend
 *
 * @package     theme_apoa
 * @category    navigation
 * @copyright   2025 Matthew Faulkner
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class primary extends \core\navigation\output\primary {

    /** @var moodle_page $page the moodle page that the navigation belongs to */
    private $page = null;

    private \cache $cache ;
    /**
     * primary constructor.
     * @param \moodle_page $page
     */
    public function __construct($page) {
        $this->page = $page;
    }

    /**
     * Combine the various menus into a standardized output.
     *
     * @param renderer_base|null $output
     * @return array
     */
    public function export_for_template(?renderer_base $output = null): array {
        if (!$output) {
            $output = $this->page->get_renderer('core');
        }

        $menudata = (object) array_merge($this->get_primary_nav(), $this->get_custom_menu($output));
        $moremenu = new \theme_apoa\navigation\output\more_menu($menudata, 'navbar-nav', false);
        $mobileprimarynav = array_merge($this->get_primary_nav(), $this->get_custom_menu($output));

        $languagemenu = new \core\output\language_menu($this->page);

        return [
            'mobileprimarynav' => $mobileprimarynav,
            'moremenu' => $moremenu->export_for_template($output),
            'lang' => !isloggedin() || isguestuser() ? $languagemenu->export_for_template($output) : [],
            'user' => $this->get_user_menu($output),
        ];
    }

    /**
     * Get the primary nav object and standardize the output
     *
     * @return array
     */
    protected function get_primary_nav($parent = null): array {
        $nodes = [];
        foreach ($this->page->primarynav->children as $node) {
            $childnodes = [];
 
            foreach($node->children as $child){
                $newchild = new \stdClass();
                $newchild->title = $child->get_title();
                $newchild->url = $child->action();
                $newchild->text = $child->text;
                $newchild->icon = $child->icon;
                $newchild->isactive = $child->isactive;
                $newchild->key = $child->key;
                $childnodes[] = $newchild;
                
            }
            $nodeObject = new \stdClass();
            $nodeObject->title = $node->get_title();
            $nodeObject->url = $node->action();
            $nodeObject->text = $node->text;
            $nodeObject->icon = $node->icon;
            $nodeObject->isactive = $node->isactive;
            $nodeObject->key = $node->key;
            $nodeObject->children = $childnodes;
            $nodeObject->haschildren = $childnodes ? true : false;
            $nodeObject->showchildreninsubmenu = $node->showchildreninsubmenu;
            $nodeObject->moremenuid = uniqid();
            $nodeObject->sort = $nodeObject->moremenuid;
            $nodes[] = $nodeObject;

        }

        return $nodes;
    }

    protected function navigation_node_to_std(\navigation_node $node){


    }
    /**
     * Custom menu items reside on the same level as the original nodes.
     * Fetch and convert the nodes to a standardised array.
     *
     * @param renderer_base $output
     * @return array
     */
    protected function get_custom_menu(renderer_base $output): array {
        global $CFG;

        // Early return if a custom menu does not exists.
        if (empty($CFG->custommenuitems)) {
            return [];
        }
        $custommenuitems = $CFG->custommenuitems;
        $currentlang = current_language();
        $custommenunodes = custom_menu::convert_text_to_menu_nodes($custommenuitems, $currentlang);
        $nodes = [];
        foreach ($custommenunodes as $node) {
            $nodes[] = $node->export_for_template($output);
        }

        return $nodes;
    }

    /**
     * Get/Generate the user menu.
     *
     * This is leveraging the data from user_get_user_navigation_info and the logic in $OUTPUT->user_menu()
     *
     * @param renderer_base $output
     * @return array
     */
    public function get_user_menu(renderer_base $output): array {
        global $CFG, $USER, $PAGE;
        require_once($CFG->dirroot . '/user/lib.php');

        $usermenudata = [];
        $submenusdata = [];
        $info = user_get_user_navigation_info($USER, $PAGE);
        if (isset($info->unauthenticateduser)) {
            $info->unauthenticateduser['content'] = get_string($info->unauthenticateduser['content']);
            $info->unauthenticateduser['url'] = get_login_url();
            return (array) $info;
        }
        // Gather all the avatar data to be displayed in the user menu.
        $usermenudata['avatardata'][] = [
            'content' => $info->metadata['useravatar'],
            'classes' => 'current'
        ];
        $usermenudata['userfullname'] = $info->metadata['realuserfullname'] ?? $info->metadata['userfullname'];

        // Logged in as someone else.
        if ($info->metadata['asotheruser']) {
            $usermenudata['avatardata'][] = [
                'content' => $info->metadata['realuseravatar'],
                'classes' => 'realuser'
            ];
            $usermenudata['metadata'][] = [
                'content' => get_string('loggedinas', 'moodle', $info->metadata['userfullname']),
                'classes' => 'viewingas'
            ];
        }

        // Gather all the meta data to be displayed in the user menu.
        $metadata = [
            'asotherrole' => [
                'value' => 'rolename',
                'class' => 'role role-##GENERATEDCLASS##',
            ],
            'userloginfail' => [
                'value' => 'userloginfail',
                'class' => 'loginfailures',
            ],
            'asmnetuser' => [
                'value' => 'mnetidprovidername',
                'class' => 'mnet mnet-##GENERATEDCLASS##',
            ],
        ];
        foreach ($metadata as $key => $value) {
            if (!empty($info->metadata[$key])) {
                $content = $info->metadata[$value['value']] ?? '';
                $generatedclass = strtolower(preg_replace('#[ ]+#', '-', trim($content)));
                $customclass = str_replace('##GENERATEDCLASS##', $generatedclass, ($value['class'] ?? ''));
                $usermenudata['metadata'][] = [
                    'content' => $content,
                    'classes' => $customclass
                ];
            }
        }

        $modifiedarray = array_map(function($value) {
            $value->divider = $value->itemtype == 'divider';
            $value->link = $value->itemtype == 'link';
            if (isset($value->pix) && !empty($value->pix)) {
                $value->pixicon = $value->pix;
                unset($value->pix);
            }
            return $value;
        }, $info->navitems);

        // Include the language menu as a submenu within the user menu.
        $languagemenu = new \core\output\language_menu($this->page);
        $langmenu = $languagemenu->export_for_template($output);
        if (!empty($langmenu)) {
            $languageitems = $langmenu['items'];
            // If there are available languages, generate the data for the the language selector submenu.
            if (!empty($languageitems)) {
                $langsubmenuid = uniqid();
                // Generate the data for the link to language selector submenu.
                $language = (object) [
                    'itemtype' => 'submenu-link',
                    'submenuid' => $langsubmenuid,
                    'title' => get_string('language'),
                    'divider' => false,
                    'submenulink' => true,
                ];

                // Place the link before the 'Log out' menu item which is either the last item in the menu or
                // second to last when 'Switch roles' is available.
                $menuposition = count($modifiedarray) - 1;
                if (has_capability('moodle/role:switchroles', $PAGE->context)) {
                    $menuposition = count($modifiedarray) - 2;
                }
                array_splice($modifiedarray, $menuposition, 0, [$language]);

                // Generate the data for the language selector submenu.
                $submenusdata[] = (object)[
                    'id' => $langsubmenuid,
                    'title' => get_string('languageselector'),
                    'items' => $languageitems,
                ];
            }
        }

        // Add divider before the last item.
        $modifiedarray[count($modifiedarray) - 2]->divider = true;
        $usermenudata['items'] = $modifiedarray;
        $usermenudata['submenus'] = array_values($submenusdata);

        return $usermenudata;
    }
}
