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
 * Forum repository class to encapsulate all of the AJAX requests that subscribe or unsubscribe
 * can be sent for forum.
 *
 * @module     theme_apoa/repository
 * @copyright  2024 Matthew Faulkner <matthewfaulkner@apoaevents.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['core/ajax'], function(Ajax) {


    /**
     * Set the favourite state for a discussion in a forum.
     *
     * @param {number} courseid ID of the forum the discussion belongs to
     * @param {null|date} targetState Set the favourite state. True == favourited; false == unfavourited.
     * @return {object} jQuery promise
     */
    var setFavouriteDiscussionState = function(courseid, targetState) {
        var request = {
            methodname: 'theme_apoa_toggle_favourite_state',
            args: {
                courseid: courseid,
                targetstate: targetState
            }
        };
        return Ajax.call([request])[0];
    };



    return {
        setFavouriteDiscussionState: setFavouriteDiscussionState,
    };
});
