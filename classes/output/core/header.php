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

namespace theme_apoa\output\core;

use core_course_category;
use renderable;
use renderer_base;
use templatable;
use custom_menu;
use moodle_url;

/**
 * more menu navigation renderable
 *
 * @package     core
 * @category    navigation
 * @copyright   2021 onwards Adrian Greeve
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class header implements renderable, templatable {

    protected $title;
    protected $category;
    protected $haschildren;
    protected $istablist;
    protected $imgurl;
    protected moodle_url $url;
    /**
     * Constructor for this class.
     *
     * @param object $content Navigation objects.
     * @param string $navbarstyle class name.
     * @param bool $haschildren The content has children.
     * @param bool $istablist When true, the more menu should be rendered and behave with a tablist ARIA role.
     *                        If false, it's rendered with a menubar ARIA role. Defaults to false.
     */
    public function __construct(string $title, string $category, moodle_url $url, $haschildren = true, bool $istablist = false) {
        $this->title = $title;
        $this->category = $category;
        $this->url = $url;
        $this->imgurl = theme_apoa_get_file_from_setting('sectionlogo' . $category);
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
        $title = str_replace(' ', '', $this->title);
        $data = [
            'title' => get_string($title, 'theme_apoa'),
            'imgurl' => $this->imgurl,
            'linkurl' => $this->url,
        ];

        return $data;
    }

}
