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
 * Builds the primary navigation items from the theme settings.
 *
 * @package     theme_apoa
 * @copyright   2025 Matthew Faulkner matthewfaulkner@apoaevents.com
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_apoa\navigation;

use navigation_node;

/**
 * Class primary_nav_data
 *
 * Each primary nav item is a category. With "include children" its dropdown lists the courses in the
 * category (by short name) and then its subcategories, each followed by its own courses and subcategories,
 * shown indented under it. Section categories (those with a "sectionlink" setting) are not expanded, so
 * Sections lists just the sections.
 *
 * The result is cached for all users (see \theme_apoa\task\update_primary_nav), so hidden courses and
 * categories are always left out.
 */
class primary_nav_data {

    /**
     * Get the primary nav items from the theme settings.
     *
     * @return \stdClass item name => item, see self::category_item()
     */
    public static function get(): \stdClass {
        $data = new \stdClass();

        $primarynavitemcount = get_config('theme_apoa', 'primarynavcount');

        for ($i = 1; $i <= $primarynavitemcount; $i++) {
            $primarynavitem = get_config('theme_apoa', 'primarynavitems' . $i);
            $includechildren = get_config('theme_apoa', 'primarynavitems' . $i . '_adv');

            if (!$category = \core_course_category::get($primarynavitem, IGNORE_MISSING, true)) {
                continue;
            }

            $item = self::category_item($category, false);
            if ($includechildren) {
                $item->children = self::children($category, true);
            }
            self::set_has_children($item);

            $data->{$category->name} = $item;
        }

        return $data;
    }

    /**
     * Courses then subcategories of a category, as nav items.
     *
     * @param \core_course_category $category
     * @param bool $includenested whether subcategories, other than sections, get their own courses and subcategories as children
     * @return array
     */
    protected static function children(\core_course_category $category, bool $includenested): array {
        $children = [];

        foreach (self::visible_courses($category) as $course) {
            $children[] = self::course_item($course);
        }

        foreach ($category->get_children(['sort' => ['sortorder' => 1]]) as $subcategory) {
            if (!$subcategory->visible || self::is_empty($subcategory)) {
                continue;
            }
            $subitem = self::category_item($subcategory, true);
            if ($includenested && !self::is_section($subcategory)) {
                $subitem->children = self::children($subcategory, false);
            }
            self::set_has_children($subitem);
            $children[] = $subitem;
        }

        return $children;
    }

    /**
     * Whether a category is a section, i.e. has a "sectionlink" setting (even an empty one).
     *
     * @param \core_course_category $category
     * @return bool
     */
    protected static function is_section(\core_course_category $category): bool {
        return get_config('theme_apoa', 'sectionlink' . $category->id) !== false;
    }

    /**
     * Whether a category has no visible courses or subcategories, e.g. one left behind after its course was moved up.
     *
     * @param \core_course_category $category
     * @return bool
     */
    protected static function is_empty(\core_course_category $category): bool {
        foreach ($category->get_children() as $subcategory) {
            if ($subcategory->visible) {
                return false;
            }
        }
        return !self::visible_courses($category);
    }

    /**
     * Visible courses directly in a category, in course order.
     *
     * @param \core_course_category $category
     * @return \core_course_list_element[]
     */
    protected static function visible_courses(\core_course_category $category): array {
        $courses = $category->get_courses(['recursive' => false, 'sort' => ['sortorder' => 1]]);
        return array_filter($courses, fn($course) => $course->visible);
    }

    /**
     * Nav item for a category.
     *
     * @param \core_course_category $category
     * @param bool $usesectionlink whether to link to the category's "sectionlink" setting if it has one
     * @return \stdClass
     */
    protected static function category_item(\core_course_category $category, bool $usesectionlink): \stdClass {
        $item = new \stdClass();
        $item->name = $category->name;
        $sectionlink = $usesectionlink ? get_config('theme_apoa', 'sectionlink' . $category->id) : '';
        $item->url = $sectionlink ?: "/course/index.php?categoryid={$category->id}";
        $item->type = navigation_node::TYPE_CATEGORY;
        $item->id = $category->id;
        $item->key = navigation_node::TYPE_CATEGORY . $category->id;
        $item->children = [];

        return $item;
    }

    /**
     * Nav item for a course, named by its short name.
     *
     * @param \core_course_list_element $course
     * @return \stdClass
     */
    protected static function course_item(\core_course_list_element $course): \stdClass {
        $item = new \stdClass();
        $item->name = $course->get_formatted_shortname();
        $item->url = "/course/view.php?id={$course->id}";
        $item->type = navigation_node::TYPE_COURSE;
        $item->id = $course->id;
        $item->key = navigation_node::TYPE_COURSE . $course->id;
        $item->children = [];
        $item->haschildren = false;
        $item->showchildreninsubmenu = false;

        return $item;
    }

    /**
     * Set haschildren and showchildreninsubmenu from the item's children.
     *
     * @param \stdClass $item
     */
    protected static function set_has_children(\stdClass $item): void {
        $item->haschildren = !empty($item->children);
        $item->showchildreninsubmenu = $item->haschildren;
    }
}
