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

namespace theme_apoa\admin;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/adminlib.php');

/**
 * Hidden admin setting that tidies up the main page slides after the slider page is saved.
 *
 * Moodle writes settings in page order, so this must be the last setting added to the
 * slider page. By then every slide's content, link, position, hidden and delete flag has been saved.
 * It removes slides flagged for deletion and renumbers the rest 1..n in display order,
 * so slide numbers, positions and headings all match.
 *
 * @package    theme_apoa
 * @copyright  2026 Matthew Faulkner matthewfaulkner@apoaevents.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class setting_slidecleanup extends \admin_setting {

    /**
     * Constructor.
     *
     * @param string $name unique name of the setting
     */
    public function __construct($name) {
        $this->nosave = false;
        parent::__construct($name, '', '', '');
    }

    /**
     * Nothing is stored for this setting.
     *
     * @return bool always true so the setting never shows as unset
     */
    public function get_setting() {
        return true;
    }

    /**
     * Nothing to upgrade.
     *
     * @return bool always true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Removes deleted slides and renumbers the remaining slides in display order.
     *
     * @param mixed $data unused
     * @return string empty string, no errors
     */
    public function write_setting($data) {
        $component = 'theme_apoa';

        $slidecount = (int) get_config($component, 'slidecount');

        $kept = [];
        foreach (theme_apoa_get_slide_order() as $x) {
            if (get_config($component, 'slidedelete' . $x)) {
                continue;
            }
            $kept[] = [
                'slide' => (string) get_config($component, 'slide' . $x),
                'slidelink' => (string) get_config($component, 'slidelink' . $x),
                'slidehidden' => (int) get_config($component, 'slidehidden' . $x),
            ];
        }

        // Always keep at least one slide so the slider and count select stay valid.
        if (!$kept) {
            $kept[] = ['slide' => '', 'slidelink' => '', 'slidehidden' => 0];
        }

        foreach ($kept as $index => $slide) {
            $x = $index + 1;
            set_config('slide' . $x, $slide['slide'], $component);
            set_config('slidelink' . $x, $slide['slidelink'], $component);
            set_config('slideorder' . $x, $x, $component);
            set_config('slidehidden' . $x, $slide['slidehidden'], $component);
            set_config('slidedelete' . $x, 0, $component);
        }

        for ($x = count($kept) + 1; $x <= $slidecount; $x++) {
            unset_config('slide' . $x, $component);
            unset_config('slidelink' . $x, $component);
            unset_config('slideorder' . $x, $component);
            unset_config('slidehidden' . $x, $component);
            unset_config('slidedelete' . $x, $component);
        }

        if (count($kept) != $slidecount) {
            set_config('slidecount', count($kept), $component);
        }

        theme_reset_all_caches();

        return '';
    }

    /**
     * Outputs only a hidden field so Moodle includes this setting when the page is saved.
     *
     * @param mixed $data unused
     * @param string $query unused
     * @return string hidden input HTML
     */
    public function output_html($data, $query = '') {
        return \html_writer::empty_tag('input', [
            'type' => 'hidden',
            'name' => $this->get_full_name(),
            'value' => 1,
        ]);
    }
}
