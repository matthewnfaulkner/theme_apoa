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
 * Contains class block_rss_client\output\block_renderer_html
 *
 * @package   block_rss_client
 * @copyright 2015 Howard County Public School System
 * @author    Brendan Anderson <brendan_anderson@hcpss.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_apoa\output;

defined('MOODLE_INTERNAL') || die();

/**
 * Renderer for RSS Client block
 *
 * @package   block_rss_client
 * @copyright 2015 Howard County Public School System
 * @author    Brendan Anderson <brendan_anderson@hcpss.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_rss_client_renderer extends \block_rss_client\output\renderer {

    /**
     * Render an RSS feeds block
     *
     * @param \templatable $block
     * @return string
     */
    public function render_block(\templatable $block) {
        $data = $block->export_for_template($this);

        $first = true;
        foreach($data['feeds'] as $key => $feeds) {
            if(empty($feeds['items'])){
                $data['feeds'][$key] = [];
            }else{
                $data['feeds'][$key]['first'] = $first;
                $first = false;
            }
        }
        
        return $this->render_from_template('block_rss_client/block', $data);
    }

}
