<?php


namespace theme_apoa\form;

require_once("$CFG->libdir/formslib.php");

class editelibrary_form extends \moodleform {

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