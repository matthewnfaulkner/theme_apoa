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
 * Renderer for use with the badges output
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

namespace theme_apoa\output\core;

use moodle_url;
use html_writer;
use paging_bar;
use html_table;

defined('MOODLE_INTERNAL') || die();


require_once($CFG->dirroot . '/badges/renderer.php');

/**
 * Standard HTML output renderer for badges
 */
class badges_renderer extends \core_badges_renderer {

    // Prints action icons for the badge.
    public function print_badge_table_actions($badge, $context) {
        $actions = "";

        if (has_capability('moodle/badges:configuredetails', $context) && $badge->has_criteria()) {
            // Activate/deactivate badge.
            if ($badge->status == BADGE_STATUS_INACTIVE || $badge->status == BADGE_STATUS_INACTIVE_LOCKED) {
                // "Activate" will go to another page and ask for confirmation.
                $url = new \moodle_url('/badges/action.php');
                $url->param('id', $badge->id);
                $url->param('activate', true);
                $url->param('sesskey', sesskey());
                $return = new \moodle_url(qualified_me());
                $url->param('return', $return->out_as_local_url(false));
                $actions .= $this->output->action_icon($url, new \pix_icon('t/show', get_string('activate', 'badges'))) . " ";
            } else {
                $url = new \moodle_url(qualified_me());
                $url->param('lock', $badge->id);
                $url->param('sesskey', sesskey());
                $actions .= $this->output->action_icon($url, new \pix_icon('t/hide', get_string('deactivate', 'badges'))) . " ";
            }
        }

        // Award badge manually.
        if ($badge->has_manual_award_criteria() &&
                has_capability('moodle/badges:awardbadge', $context) &&
                $badge->is_active()) {
            $url = new \moodle_url('/badges/award.php', array('id' => $badge->id));
            $actions .= $this->output->action_icon($url, new \pix_icon('t/award', get_string('award', 'badges'))) . " ";
        }

        // Edit badge.
        if (has_capability('moodle/badges:configuredetails', $context)) {
            $url = new \moodle_url('/badges/edit.php', array('id' => $badge->id, 'action' => 'badge'));
            $actions .= $this->output->action_icon($url, new \pix_icon('t/edit', get_string('edit'))) . " ";
        }

        // Duplicate badge.
        if (has_capability('moodle/badges:createbadge', $context)) {
            $url = new \moodle_url('/badges/action.php', array('copy' => '1', 'id' => $badge->id, 'sesskey' => sesskey()));
            $actions .= $this->output->action_icon($url, new \pix_icon('t/copy', get_string('copy'))) . " ";
        }

        // Delete badge.
        if (has_capability('moodle/badges:deletebadge', $context)) {
            $url = new \moodle_url(qualified_me());
            $url->param('delete', $badge->id);
            $actions .= $this->output->action_icon($url, new \pix_icon('t/delete', get_string('delete'))) . " ";
        }

        // Delete badge.
        if (has_capability('local/credits:editcredits', $context)) {
            $url = new \moodle_url('/local/credits/index.php', array('badgeid' => $badge->id, 'type' => $badge->type));
            $actions .= $this->output->action_icon($url, new \pix_icon('m/USD', get_string('editcredit', 'local_credits'))) . " ";
        }

        return $actions;
    }

        /**
     * Render a table of badges.
     *
     * @param \core_badges\output\badge_management $badges
     * @return string
     */
    protected function render_badge_management(\core_badges\output\badge_management $badges) {
        $paging = new paging_bar($badges->totalcount, $badges->page, $badges->perpage, $this->page->url, 'page');

        // New badge button.
        $htmlnew = '';
        $htmlpagingbar = $this->render($paging);
        $table = new html_table();
        $table->attributes['class'] = 'table table-bordered table-striped';

        $sortbyname = $this->helper_sortable_heading(get_string('name'),
                'name', $badges->sort, $badges->dir);
        $sortbystatus = $this->helper_sortable_heading(get_string('status', 'badges'),
                'status', $badges->sort, $badges->dir);
        $table->head = array(
                $sortbyname,
                $sortbystatus,
                get_string('bcriteria', 'badges'),
                get_string('awards', 'badges'),
                get_string('actions')
            );
        $table->colclasses = array('name', 'status', 'criteria', 'awards', 'actions');

        foreach ($badges->badges as $b) {
            $style = !$b->is_active() ? array('class' => 'dimmed') : array();
            $forlink =  print_badge_image($b, $this->page->context) . ' ' .
                        html_writer::start_tag('span') . $b->name . html_writer::end_tag('span');
            $name = html_writer::link(new moodle_url('/badges/overview.php', array('id' => $b->id)), $forlink, $style);
            $status = $b->statstring;
            $criteria = self::print_badge_criteria($b, 'short');

            if (has_capability('moodle/badges:viewawarded', $this->page->context)) {
                $awards = html_writer::link(new moodle_url('/badges/recipients.php', array('id' => $b->id)), $b->awards);
            } else {
                $awards = $b->awards;
            }

            $actions = self::print_badge_table_actions($b, $this->page->context);

            $row = array($name, $status, $criteria, $awards, $actions);
            $table->data[] = $row;
        }
        $htmltable = html_writer::table($table);

        return $htmlnew . $htmlpagingbar . $htmltable . $htmlpagingbar;
    }

}
