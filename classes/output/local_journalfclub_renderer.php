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



/**
 * Renderer for outputting the singleactivity course format.
 *
 * @package    format_singleactivity
 * @copyright  2013 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_journalclub_renderer extends \theme_apoa\output\core\course_renderer {


    public function __construct(\moodle_page $page, $target){
        global $COURSE, $PAGE;
        $PAGE->set_primary_active_tab('cc' . $COURSE->categoryid);
        parent::__construct($page, $target); 
    }

    public function render_journal_club($categoryid){
        $popular_articles = get_popular_courses_by_category($categoryid);
        $allarticles = get_chosen_courses_by_category($categoryid, $popular_articles);
        $courselist = new \local_journalclub\output\course_list($allarticles);
        $template = $courselist->export_for_template($this);
        return "<h1>hi</h1>";
    }
}
