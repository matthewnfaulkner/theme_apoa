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
 * Update preferences for user hiding warning notifications
 *
 * @module     theme_apoa/hidewarnings
 * @copyright  2024 Matthew Faulkner <matthewfaulkner@apoaevents.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['core/ajax', 'core/notification'], function(Ajax, Notification){

    /**
     * Register event listeners for the notification hide
     *
     * @param {string} name Should the default action of the event be prevented
     * @param {int} userid Should the default action of the event be prevented
     */
    var init = function(name, userid){
        let query = '[data-warning-notification="' + name + '"]';
        let parent  = document.querySelector(query);
        let link = parent.querySelector('.dontshowagain');
        const d = new Date();
        let time = d.getTime();
        link.addEventListener('click', e =>{
            e.preventDefault();
            let close = parent.querySelector('.close');
            return Ajax.call([{
                methodname: 'core_user_set_user_preferences',
                args: {
                    'preferences': [{
                        'name': name,
                        'value': time,
                        'userid': userid
                    }]
                }
            }])[0].done(function(){
                close.click();
            }).fail(function(){
                Notification.exception(new Error('Failed to set preference'));
            });
        });
    };
    return{
        init: init
    };
});