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
 * Handle discussion subscription toggling on a discussion list in
 * the forum view.
 *
 * @module     theme_apoa/favourite_toggle
 * @copyright  2024 Matthew Faulkner <matthewfaulkner@apoaevents.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
        'jquery',
        'core/templates',
        'core/notification',
        'theme_apoa/repository',
        'mod_forum/selectors',
        'core/str',
    ], function(
        $,
        Templates,
        Notification,
        Repository,
        Selectors,
        String
    ) {

    /**
     * Register event listeners for the subscription toggle.
     *
     * @param {object} root The discussion list root element
     * @param {boolean} preventDefault Should the default action of the event be prevented
     * @param {function} callback Success callback
     */
    var registerEventListeners = function(root, preventDefault, callback) {
        root.on('click', Selectors.favourite.toggle, function(e) {
            var toggleElement = $(this);
            var courseid = toggleElement.data('courseid');
            var subscriptionState = toggleElement.data('targetstate');
            Repository.setFavouriteDiscussionState(courseid, subscriptionState)
                .then(function(context) {
                    return callback(toggleElement, context);
                })
                .then(function() {
                    return String.get_string("favouriteupdated", "forum")
                        .done(function(s) {
                            return Notification.addNotification({
                                message: s,
                                type: "info"
                            });
                        });
                })
                .catch(Notification.exception);

            if (preventDefault) {
                e.preventDefault();
            }
        });
    };

    return {
        init: registerEventListeners
    };
});
