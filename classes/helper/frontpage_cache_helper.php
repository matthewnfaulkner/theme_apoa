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
 * subscriptions block rendrer
 *
 * @package    block_subscriptions
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace theme_apoa\helper;

defined('MOODLE_INTERNAL') || die;



/**
 * subscriptions block renderer
 *
 * @package    block_subscriptions
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class frontpage_cache_helper {

    protected string $region;

    public function __construct(string $region)
    {
        $this->region = $region;
    }


    public function get_blocks() {
        global $PAGE, $OUTPUT;
        $bm = $PAGE->blocks;
        $bm->load_blocks();
        $content = $OUTPUT->addblockbutton($this->region);
        if($bm->is_known_region($this->region)){
            if($blocks = $PAGE->blocks->get_content_for_region($this->region, $OUTPUT)){
                foreach($blocks as $index => $block){
                    $content .= $OUTPUT->block($block, $this->region);
                }
            }
        };
        return $content;
    }

    public function get_block_region_resources()  {
        global $OUTPUT;
        $content = $OUTPUT->addblockbutton($this->region);
        $content .= $OUTPUT->blocks('resources', ['d-flex', 'flex-column', 'flex-md-row', 'flex-wrap', 'w-100', 'justify-content-center']);
        return $content;
    }
}
