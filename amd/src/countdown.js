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
 * Handles countdown on frontpage for congress.
 *
 * @module     theme_apoa/countdown
 * @copyright  2024 Matthew Faulkner <matthewfaulkner@apoaevents.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function($){


    var init = function(countDownDate){

        countDownDate = countDownDate * 1000;

            /**
         * Calculate how much time is left and display it.
         *
         * @return {null}
         */
        function getCountdown(){

            var now = new Date().getTime();
            // Find the distance between now and the count down date
            var distance = countDownDate - now;
            // Time calculations for days, hours, minutes and seconds
            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
            // Display the result in the element with id="demo"
            $('#jumbocountdowndays').html(days);
            $('#jumbocountdownhours').html(hours);
            $('#jumbocountdownminutes').html(minutes);
            $('#jumbocountdownseconds').html(seconds);

            if (distance < 0) {
                clearInterval(x);
                $('#jumbocountdowndays').html(0);
                $('#jumbocountdownhours').html(0);
                $('#jumbocountdownminutes').html(0);
                $('#jumbocountdownseconds').html(0);
                }
        }

        getCountdown();
        // Select the element with class name "countdown"
        const countdownElement = document.querySelector('.countdown');
        // Remove the class "d-none" from the selected element
        countdownElement.classList.remove('d-none');
        // Update the count down every 1 second
        var x = setInterval(function() {
            getCountdown();
        }, 1000);
    };
    return{
        init: init
    };
});