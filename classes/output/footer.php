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
 * Renderable footer
 *
 * @package   theme_apoa
 * @copyright 2024 Matthew Faulkner <matthewfaulkner@apoaevents.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_apoa\output;

use moodle_page;
use renderable;
use renderer_base;
use templatable;


class footer implements renderable, templatable {

    private string $contact;
    /**
     * primary constructor.
     * @param \moodle_page $page
     */
    public function __construct(moodle_page $page) {
        
        $this->contact = get_config('theme_apoa', 'footercontact');

    }

    /**
     * Combine the various menus into a standardized output.
     *
     * @param renderer_base|null $output
     * @return array
     */
    public function export_for_template(?renderer_base $output = null) {

        $template = [
            'facebooklink'  => get_config('theme_apoa', 'facebooklink'),
            'instagramlink' => get_config('theme_apoa', 'instagramlink'),
            'twitterlink'   => get_config('theme_apoa', 'twitterlink'),
            'linkedinlink'  => get_config('theme_apoa', 'linkedinlink'),      
        ];
        return $template;
    }
}
