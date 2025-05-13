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
 *  From for updating jounral object
 *
 * @package     theme_apoa
 * @copyright   2025 Matthew Faulkner matthewfaulkner@apoaevents.com
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */                                                             


namespace theme_apoa\form;

require_once("$CFG->libdir/formslib.php");

/**
 * Class editelibrary_form 
 * 
 * Form for updating properties of an Elibrary Journal
 */
class editelibrary_form extends \moodleform {


    /**
     * Form definintions
     *
     * @return void
     */
    public function definition() {
        $mform = $this->_form;

        $categoryid = $this->_customdata['categoryid'];

        // Get list of categories to use as parents, with site as the first one.
        if ($categoryid) {
            // Editing an existing category
            $strsubmit = get_string('savechanges');
        }

        $mform->addElement('text', 'url', 'URL:');
        $mform->setType('url', PARAM_URL);
        $mform->addRule('url', 'Please enter a valid URL.', 'required', null, 'client');
        
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);
        $mform->setDefault('id', $categoryid);

        $this->add_action_buttons(true, $strsubmit);

    }

    /**
     *
     * Server side rules do not work for uploaded files, implement serverside rules here if needed.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Validate URL field
        $url = $data['url'];
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $errors['url'] = 'Please enter a valid URL.';
        }

        // Additional validation for the path fiel
        // Perform any necessary validation for the path field

        return $errors;
    }
}