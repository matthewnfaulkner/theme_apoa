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

namespace theme_apoa\output;



use renderable;
use renderer_base;
use templatable;


class favourite implements renderable, templatable {

    /** @var moodle_page $page the moodle page that the navigation belongs to */
    private $page = null;

    /** @var array $page the moodle page that the navigation belongs to */
    private bool $favourited = false;

    private int $courseid;


    /**
     * primary constructor.
     * @param \moodle_page $page
     */
    public function __construct($course) {

        global $USER;

        /*if(is_enrolled($course->context, $USER)){

        };*/

        $this->courseid = $course->id;

        $usercontext = \context_user::instance($USER->id);
        $context = \context_course::instance($course->id);

        $ufservice = \core_favourites\service_factory::get_service_for_user_context($usercontext);

        if($fav = $ufservice->get_favourite('core_course', 'courses', $course->id, $context)) {
            $this->favourited = True;
        }

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


        return array('favourited' => $this->favourited,
            'courseid'    => $this->courseid);
    }
}
