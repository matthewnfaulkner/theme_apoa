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
 * Contains class core_tag_tag
 *
 * @package   core_tag
 * @copyright  2015 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Represents one tag and also contains lots of useful tag-related methods as static functions.
 *
 * Tags can be added to any database records.
 * $itemtype refers to the DB table name
 * $itemid refers to id field in this DB table
 * $component is the component that is responsible for the tag instance
 * $context is the affected context
 *
 * BASIC INSTRUCTIONS :
 *  - to "tag a blog post" (for example):
 *        core_tag_tag::set_item_tags('post', 'core', $blogpost->id, $context, $arrayoftags);
 *
 *  - to "remove all the tags on a blog post":
 *        core_tag_tag::remove_all_item_tags('post', 'core', $blogpost->id);
 *
 * set_item_tags() will create tags that do not exist yet.
 *
 * @property-read int $id
 * @property-read string $name
 * @property-read string $rawname
 * @property-read int $tagcollid
 * @property-read int $userid
 * @property-read int $isstandard
 * @property-read string $description
 * @property-read int $descriptionformat
 * @property-read int $flag 0 if not flagged or positive integer if flagged
 * @property-read int $timemodified
 *
 * @package   core_tag
 * @copyright  2015 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_apoa_tag_tag extends \core_tag_tag {

     /**
     * Looking in all tag collections for the tag with the given name
     *
     * @param string $name tag name
     * @param string $returnfields
     * @return array array of core_tag_tag instances
     */
    public static function guess_by_name($name, $returnfields='id, name, rawname, tagcollid') {
        global $DB;
        if (empty($name)) {
            return array();
        }
        $tagcolls = core_tag_collection::get_collections();
        list($sql, $params) = $DB->get_in_or_equal(array_keys($tagcolls), SQL_PARAMS_NAMED);
        $params['name'] = core_text::strtolower($name);
        $tags = $DB->get_records_select('tag', 'name = :name AND tagcollid ' . $sql, $params, '', $returnfields);
        if (count($tags) > 1) {
            // Sort in the same order as tag collections.
            $tagcolls = core_tag_collection::get_collections();
            uasort($tags, function($a, $b) use ($tagcolls) {
                return $tagcolls[$a->tagcollid]->sortorder < $tagcolls[$b->tagcollid]->sortorder ? -1 : 1;
            });
        }
        $rv = array();
        foreach ($tags as $id => $tag) {
            $rv[$id] = new static($tag);
        }
        return $rv;
    }

    
    /**
     * Find all items tagged with a tag of a given type ('post', 'user', etc.)
     *
     * @param    string   $component component responsible for tagging. For BC it can be empty but in this case the
     *                    query will be slow because DB index will not be used.
     * @param    string   $itemtype  type to restrict search to
     * @param    int      $limitfrom (optional, required if $limitnum is set) return a subset of records, starting at this point.
     * @param    int      $limitnum  (optional, required if $limitfrom is set) return a subset comprising this many records.
     * @param    string   $subquery additional query to be appended to WHERE clause, refer to the itemtable as 'it'
     * @param    array    $params additional parameters for the DB query
     * @return   array of matching objects, indexed by record id, from the table containing the type requested
     */
    public function get_tagged_items($component, $itemtype, $limitfrom = '', $limitnum = '', $subquery = '', $orderby = '', $params = array()) {
        global $DB;

        if (empty($itemtype) || !$DB->get_manager()->table_exists($itemtype)) {
            return array();
        }
        $params = $params ? $params : array();

        $query = "SELECT it.*
                    FROM {".$itemtype."} it INNER JOIN {tag_instance} tt ON it.id = tt.itemid
                   WHERE tt.itemtype = :itemtype AND tt.tagid = :tagid";
        $params['itemtype'] = $itemtype;
        $params['tagid'] = $this->id;
        if ($component) {
            $query .= ' AND tt.component = :component';
            $params['component'] = $component;
        }
        if ($subquery) {
            $query .= ' AND ' . $subquery;
        }
        $query .= ' ORDER BY it.' . $orderby;

        return $DB->get_records_sql($query, $params, $limitfrom, $limitnum);
    }

    /**
     * Find all items tagged with a tag of a given type ('post', 'user', etc.)
     *
     * @param    string   $component component responsible for tagging. For BC it can be empty but in this case the
     *                    query will be slow because DB index will not be used.
     * @param    string   $itemtype  type to restrict search to
     * @param    int      $limitfrom (optional, required if $limitnum is set) return a subset of records, starting at this point.
     * @param    int      $limitnum  (optional, required if $limitfrom is set) return a subset comprising this many records.
     * @param    string   $subquery additional query to be appended to WHERE clause, refer to the itemtable as 'it'
     * @param    array    $params additional parameters for the DB query
     * @return   array of matching objects, indexed by record id, from the table containing the type requested
     */
    public static function get_all_tags($courseid, $component, $itemtype, $limitfrom = '', $limitnum = '', $subquery = '', $orderby = '', $params = array()) {
        global $DB;

        if (empty($itemtype) || !$DB->get_manager()->table_exists($itemtype)) {
            return array();
        }
        $params = $params ? $params : array();

        $query = "SELECT it*
                    FROM {".$itemtype."} it INNER JOIN {tag_instance} tt ON ON it.id = tt.itemid
                   WHERE tt.itemtype = :itemtype AND tt.itemid = :itemid AND tt.itemid IN
                   (SELECT tt.itemid FROM tt INNER JOIN it ON it.id = tt.itemid
                   WHERE item.id = :itemid)";
        $params['itemid'] = $courseid;
        if ($component) {
            $query .= ' AND tt.component = :component';
            $params['component'] = $component;
        }
        if ($subquery) {
            $query .= ' AND ' . $subquery;
        }
        $query .= ' ORDER BY tt.' . $orderby;

        return $DB->get_records_sql($query, $params, $limitfrom, $limitnum);
    }

    /**
     * Find all items tagged with a tag of a given type ('post', 'user', etc.)
     *
     * @param    string   $component component responsible for tagging. For BC it can be empty but in this case the
     *                    query will be slow because DB index will not be used.
     * @param    string   $itemtype  type to restrict search to
     * @param    int      $limitfrom (optional, required if $limitnum is set) return a subset of records, starting at this point.
     * @param    int      $limitnum  (optional, required if $limitfrom is set) return a subset comprising this many records.
     * @param    string   $subquery additional query to be appended to WHERE clause, refer to the itemtable as 'it'
     * @param    array    $params additional parameters for the DB query
     * @return   array of matching objects, indexed by record id, from the table containing the type requested
     */
    public static function get_all_courses_with_same_tags($courseid, $component, $itemtype, $limitfrom = '', $limitnum = '', $subquery = '', $orderby = '', $params = array()) {
        global $DB;

        if (empty($itemtype) || !$DB->get_manager()->table_exists($itemtype)) {
            return array();
        }
        $params = $params ? $params : array();
        $params['itemtype'] = $itemtype;
        $params['courseid'] = $courseid;


        $query = "SELECT it.*, t.name as tagname, t.id as tagid, t.tagcollid, t.rawname, cc.id as categoryid, cc.name as categoryname,
        cc.path as categorypath
        FROM {".$itemtype."} it INNER JOIN {tag_instance} tt ON it.id = tt.itemid INNER JOIN {tag} t ON t.id = tt.tagid 
                INNER JOIN {course_categories} cc ON cc.id = it.category
                WHERE tt.itemtype = :itemtype AND tt.tagid IN
                (SELECT tt.tagid FROM {".$itemtype."} it INNER JOIN {tag_instance} tt ON tt.itemid = it.id
                WHERE it.id = :courseid)";
        $params['itemtype'] = $itemtype;
        $params['courseid'] = $courseid;
        if ($component) {
            $query .= ' AND tt.component = :component';
            $params['component'] = $component;
        }
        if ($subquery) {
            $query .= ' AND ' . $subquery;
        }
        $query .= ' ORDER BY it.' . $orderby;
        
        return $DB->get_records_sql($query, $params, $limitfrom, $limitnum);
    }
}
