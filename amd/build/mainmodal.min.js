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
 * Show the main page modal and remember when it is closed.
 *
 * Moodle 5 uses Bootstrap 5, which no longer has jQuery's $(...).modal(), so the modal is
 * opened through Bootstrap's data API instead.
 *
 * @module     theme_apoa/mainmodal
 * @copyright  2024 Matthew Faulkner <matthewfaulkner@apoaevents.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/ajax', 'core/notification'], function($, Ajax, Notification) {

    /**
     * @param {boolean} loggedin is user logged in
     */
    var init = function(loggedin) {
        $(function() {
            var modal = document.getElementById('mainModal');
            if (!modal) {
                return; // The main modal is switched off in the theme settings.
            }

            var trigger = document.createElement('button');
            trigger.type = 'button';
            trigger.hidden = true;
            trigger.setAttribute('data-bs-toggle', 'modal');
            trigger.setAttribute('data-bs-target', '#mainModal');
            document.body.appendChild(trigger);
            trigger.click();
            trigger.remove();

            // Only update the preference if the user is logged in.
            if (loggedin) {
                modal.addEventListener('hidden.bs.modal', function() {
                    Ajax.call([{
                        methodname: 'theme_apoa_cache_closed_modal',
                        args: {closemodal: true}
                    }])[0].fail(Notification.exception);
                }, {once: true});
            }
        });
    };

    return {
        init: init
    };
});
