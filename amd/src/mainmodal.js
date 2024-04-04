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
 * @copyright  2019 Peter Dias <peter@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/ajax'], function($, Ajax){


    var init = function(loggedin){

        let modal = $('#mainModal');

        $(document).ready(function(){
            let options = {
                'backdrop' : true,
                'keyboard' : true,
                'focus'    : true,
                'show'     :true,
            };
            modal.modal(options);
        });
        if(loggedin) {
        modal.on('hidden.bs.modal', function () {
            return Ajax.call([{
                methodname: 'theme_apoa_cache_closed_modal',
                args: {
                    closemodal: true,
                }
            }])[0].done(function(response){
                return response.success;
            }).fail(function(){
                Notification.exception(new Error('Failed to cache close modal'));
            });
          });
        }
    };
    return{
        init: init
    };
});