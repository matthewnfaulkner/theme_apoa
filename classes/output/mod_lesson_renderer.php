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
 * Moodle renderer used to display special elements of the lesson module
 *
 * @package mod_lesson
 * @copyright  2009 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

namespace theme_apoa\output;

defined('MOODLE_INTERNAL') || die();



class mod_lesson_renderer extends \mod_lesson_renderer {

    /**
     * Returns HTML to display a continue button
     * @param lesson $lesson
     * @param int $lastpageseen
     * @return string
     */
    public function continue_links(\lesson $lesson, $lastpageseenid) {
        global $USER;
        if(is_guest($lesson->context, $USER)){
            redirect(new \moodle_url('/mod/lesson/view.php', array('id'=> $lesson->cm->id, 'pageid' => $lesson->firstpageid, 'startlastseen'=>'no')));
        }
        global $CFG;
        $output = $this->output->box(get_string('youhaveseen','lesson'), 'generalbox boxaligncenter');
        $output .= $this->output->box_start('center');

        $yeslink = \html_writer::link(new \moodle_url('/mod/lesson/view.php', array('id' => $this->page->cm->id,
            'pageid' => $lastpageseenid, 'startlastseen' => 'yes')), get_string('yes'), array('class' => 'btn btn-primary'));
        $output .= \html_writer::tag('span', $yeslink, array('class'=>'lessonbutton standardbutton'));
        $output .= '&nbsp;';

        $nolink = \html_writer::link(new \moodle_url('/mod/lesson/view.php', array('id' => $this->page->cm->id,
            'pageid' => $lesson->firstpageid, 'startlastseen' => 'no')), get_string('no'), array('class' => 'btn btn-secondary'));
        $output .= \html_writer::tag('span', $nolink, array('class'=>'lessonbutton standardbutton'));

        $output .= $this->output->box_end();
        return $output;
    }

    
}
