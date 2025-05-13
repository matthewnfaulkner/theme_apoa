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
 *  Theme functions.
 *
 * @package     theme_apoa
 * @copyright   2025 Matthew Faulkner matthewfaulkner@apoaevents.com
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */                                                             

namespace theme_apoa\helper;

defined('MOODLE_INTERNAL') || die;

/**
 * Class frontpage_cache_helper
 * 
 * @package theme_apoa
 * 
 * Helper class for adding blocks to the front page.
 */
class frontpage_cache_helper {

    /**
     * name of block region
     *
     * @var string
     */
    protected string $region;

    /**
     * Constructor
     *
     * @param string $region name of block region
     */
    public function __construct(string $region)
    {
        $this->region = $region;
    }

    /**
     * Get blocks for this classes region
     *
     * @return string block content
     */
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

    /**
     * Get resources blocks for given region
     *
     * @return string block content
     */
    public function get_block_region_resources()  {
        global $OUTPUT;
        $content = $OUTPUT->addblockbutton($this->region);
        $content .= $OUTPUT->blocks('resources', ['d-flex', 'flex-column', 'flex-md-row', 'flex-wrap', 'w-100', 'justify-content-center']);
        return $content;
    }
}
