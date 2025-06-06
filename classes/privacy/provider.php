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
 * Privacy Subsystem implementation for theme_apoa.
 *
 * @package    theme_apoa
 * @copyright  2024 Matthew Faulkner <matthewfaulkner@apoaevents.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_apoa\privacy;

use \core_privacy\local\metadata\collection;

defined('MOODLE_INTERNAL') || die();

/**
 * The apoa theme stores a user preference data.
 *
 * @copyright  2024 Matthew Faulkner <matthewfaulkner@apoaevents.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    // This plugin has data.
    \core_privacy\local\metadata\provider,
    // This plugin has some sitewide user preferences to export.
    \core_privacy\local\request\user_preference_provider {

    /** The user preferences for the course index. */
    const  USER_NOTAPPROVED= 'theme_apoa_user_notapproved';

    /** The user preferences for the blocks drawer. */
    const USER_NOSUB = 'theme_apoa_user_nosub';

    /**
     * Returns meta data about this system.
     *
     * @param  collection $items The initialised item collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $items) : collection {
        $items->add_user_preference(self::USER_NOTAPPROVED, 'privacy:metadata:preference:themeapoausernotapproved');
        $items->add_user_preference(self::USER_NOSUB, 'privacy:metadata:preference:themeapoausernosub');
        return $items;
    }

    /**
     * Store all user preferences for the plugin.
     *
     * @param int $userid The userid of the user whose data is to be exported.
     */
    public static function export_user_preferences(int $userid) {

        $usernotapproved = get_user_preferences(self::USER_NOTAPPROVED, null, $userid);

        if (isset($usernotapproved)) {
            $preferencestring = get_string('privacy:drawerindexclosed', 'theme_boost');
            if ($usernotapproved == 1) {
                $preferencestring = get_string('privacy:drawerindexopen', 'theme_boost');
            }
            \core_privacy\local\request\writer::export_user_preference(
                'theme_apoa',
                self::USER_NOTAPPROVED,
                $usernotapproved,
                $preferencestring
            );
        }

        $usernotsubbed = get_user_preferences(self::USER_NOSUB, null, $userid);

        if (isset($usernotsubbed)) {
            $preferencestring = get_string('privacy:drawerblockclosed', 'theme_boost');
            if ($usernotsubbed == 1) {
                $preferencestring = get_string('privacy:drawerblockopen', 'theme_boost');
            }
            \core_privacy\local\request\writer::export_user_preference(
                'theme_apoa',
                self::USER_NOSUB,
                $usernotsubbed,
                $preferencestring
            );
        }
    }
}
