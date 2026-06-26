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
 * Live upvote/downvote AJAX handler for the Reddit-style rating widget.
 *
 * Intercepts clicks on vote buttons, posts to rate_ajax.php, and updates
 * the score and button states in-place without a page reload.
 *
 * @module     theme_apoa/rating_vote
 * @copyright  2024 Matthew Faulkner <matthewfaulkner@apoaevents.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['core/config'], function(Config) {
    'use strict';

    const RATING_UNSET = -999;
    const RATE_AJAX_URL = Config.wwwroot + '/rating/rate_ajax.php';

    /**
     * Convert Moodle's raw key-sum aggregate to a true net score.
     *
     * Moodle SUM aggregate sums the 1-based scale keys, not the label values.
     * For a 2-item numeric scale (keys 1/-1 → labels -1/+1) the formula is:
     *   net = rawAggregate * (v2 - v1) / (k2 - k1)
     *       + count * (v1*k2 - v2*k1) / (k2 - k1)
     *
     * @param {Number} rawAggregate The raw SUM aggregate returned by Moodle.
     * @param {Number} count The number of ratings included in the aggregate.
     * @param {Object} scaledata The scale metadata (minkey, maxkey, minlabel, maxlabel, isnumeric, aggregation).
     * @return {Number} The true net score.
     */
    function computeNetScore(rawAggregate, count, scaledata) {
        if (count === 0) {
            return 0;
        }
        const AGGREGATE_SUM = 5;
        if (!scaledata.isnumeric || scaledata.aggregation !== AGGREGATE_SUM) {
            return rawAggregate;
        }
        const k1 = scaledata.minkey;
        const k2 = scaledata.maxkey;
        const v1 = scaledata.minlabel;
        const v2 = scaledata.maxlabel;
        const dk = k2 - k1;
        return Math.round(rawAggregate * (v2 - v1) / dk + count * (v1 * k2 - v2 * k1) / dk);
    }

    /**
     * Format a net score for display, prefixing positive values with a plus sign.
     *
     * @param {Number} net The net score.
     * @return {String} The formatted score.
     */
    function formatScore(net) {
        return (net > 0 ? '+' : '') + net;
    }

    /**
     * Handle a click on a vote button by posting the rating via AJAX and updating the widget.
     *
     * @param {Event} e The click event.
     */
    function handleVoteClick(e) {
        const btn = e.target.closest('[data-action="vote"]');
        if (!btn) {
            return;
        }
        const widget = btn.closest('[data-region="vote-widget"]');
        if (!widget) {
            return;
        }

        e.preventDefault();

        const params = JSON.parse(widget.dataset.params);
        const scaledata = JSON.parse(widget.dataset.scaledata);
        const ratingValue = parseInt(btn.dataset.rating, 10);

        // Disable buttons during request to prevent double-submission.
        const allBtns = widget.querySelectorAll('[data-action="vote"]');
        allBtns.forEach(function(b) { b.disabled = true; });

        const body = new URLSearchParams(Object.assign({}, params, {rating: ratingValue}));

        fetch(RATE_AJAX_URL, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: body.toString(),
        })
        .then(function(response) {
            if (!response.ok) {
                throw new Error('Network error');
            }
            return response.json();
        })
        .then(function(data) {
            if (data.error) {
                throw new Error(data.error);
            }

            // Update displayed score.
            const scoreEl = widget.querySelector('.ratingaggregate');
            if (scoreEl) {
                const net = computeNetScore(
                    parseFloat(data.aggregate || 0),
                    parseInt(data.count || 0, 10),
                    scaledata
                );
                scoreEl.textContent = formatScore(net);
            }

            // Update active states and toggle values for next click.
            const upBtn = widget.querySelector('.upvote-btn');
            const downBtn = widget.querySelector('.downvote-btn');
            const upActive = (ratingValue === scaledata.maxkey);
            const downActive = (ratingValue === scaledata.minkey);

            if (upBtn) {
                upBtn.classList.toggle('vote-active', upActive);
                upBtn.dataset.rating = upActive ? RATING_UNSET : scaledata.maxkey;
            }
            if (downBtn) {
                downBtn.classList.toggle('vote-active', downActive);
                downBtn.dataset.rating = downActive ? RATING_UNSET : scaledata.minkey;
            }
        })
        .catch(function(err) {
            window.console.error('Rating AJAX error:', err);
        })
        .finally(function() {
            allBtns.forEach(function(b) { b.disabled = false; });
        });
    }

    return {
        init: function() {
            document.addEventListener('click', handleVoteClick);
        }
    };
});
