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
 * Overridden core tag renderer.
 *
 * @package     theme_apoa
 * @copyright   2025 Matthew Faulkner matthewfaulkner@apoaevents.com
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */                                                        


namespace theme_apoa\output\core;


defined('MOODLE_INTERNAL') || die;

use html_writer;

require_once($CFG->dirroot . '/tag/classes/renderer.php');


class tag_renderer extends \core_tag_renderer {

    /**
     * Renders the tag index page, default index page overridden with blocks
     *
     * @param core_tag_tag $tag
     * @param \core_tag\output\tagindex[] $entities
     * @param int $tagareaid
     * @param bool $exclusivemode if set to true it means that no other entities tagged with this tag
     *             are displayed on the page and the per-page limit may be bigger
     * @param int $fromctx context id where the link was displayed, may be used by callbacks
     *            to display items in the same context first
     * @param int $ctx context id where to search for records
     * @param bool $rec search in subcontexts as well
     * @param int $page 0-based number of page being displayed
     * @return string
     */
    public function tag_index_page($tag, $entities, $tagareaid, $exclusivemode, $fromctx, $ctx, $rec, $page){
        $this->page->set_pagetype('tag-index');
        $this->page->blocks->add_region('content');
        $this->page->force_lock_all_blocks();
        return '';
    }

    /**
     * Renders the tag index page
     *
     * @param core_tag_tag $tag
     * @param \core_tag\output\tagindex[] $entities
     * @param int $tagareaid
     * @param bool $exclusivemode if set to true it means that no other entities tagged with this tag
     *             are displayed on the page and the per-page limit may be bigger
     * @param int $fromctx context id where the link was displayed, may be used by callbacks
     *            to display items in the same context first
     * @param int $ctx context id where to search for records
     * @param bool $rec search in subcontexts as well
     * @param int $page 0-based number of page being displayed
     * @return string
     */
    public function tag_index_pgage($tag, $entities, $tagareaid, $exclusivemode, $fromctx, $ctx, $rec, $page) {
        global $CFG;

        $this->page->requires->js_call_amd('core/tag', 'initTagindexPage');

        $tagname = $tag->get_display_name();
        $systemcontext = \context_system::instance();

        if ($tag->flag > 0 && has_capability('moodle/tag:manage', $systemcontext)) {
            $tagname = '<span class="flagged-tag">' . $tagname . '</span>';
        }

        $rv = '';
        $rv .= $this->output->heading($tagname, 2);

        $rv .= $this->tag_links($tag);

        if ($desciption = $tag->get_formatted_description()) {
            $rv .= $this->output->box($desciption, 'generalbox tag-description');
        }
        
        $relatedtagslimit = 10;
        $relatedtags = $tag->get_related_tags();
        $taglist = new \core_tag\output\taglist($relatedtags, get_string('relatedtags', 'tag'),
                'tag-relatedtags', $relatedtagslimit);
        $rv .= $this->output->render_from_template('core_tag/taglist',
                $taglist->export_for_template($this->output));

        // Display quick menu of the item types (if more than one item type found).
        $entitylinks = array();
        foreach ($entities as $entity) {
            if (!empty($entity->hascontent)) {
                $entitylinks[] = '<li><a href="#'.$entity->anchor.'">' .
                        \core_tag_area::display_name($entity->component, $entity->itemtype) . '</a></li>';
            }
        }

        if (count($entitylinks) > 1) {
            $rv .= '<div class="tag-index-toc"><ul class="inline-list">' . join('', $entitylinks) . '</ul></div>';
        } else if (!$entitylinks) {
            $rv .= '<div class="tag-noresults">' . get_string('noresultsfor', 'tag', $tagname) . '</div>';
        }

        // Display entities tagged with the tag.
        $content = '';
        foreach ($entities as $entity) {
            if (!empty($entity->hascontent)) {
                $content .= $this->output->render_from_template('theme_apoa/tag/index', $entity->export_for_template($this->output));
            }
        }

        if ($exclusivemode) {
            $rv .= $content;
        } else if ($content) {
            $rv .= \html_writer::div($content, 'tag-index-items');
        }

        // Display back link if we are browsing one tag area.
        if ($tagareaid) {
            $url = $tag->get_view_url(0, $fromctx, $ctx, $rec);
            $rv .= '<div class="tag-backtoallitems">' .
                    \html_writer::link($url, get_string('backtoallitems', 'tag', $tag->get_display_name())) .
                    '</div>';
        }

        return $rv;
    }
}