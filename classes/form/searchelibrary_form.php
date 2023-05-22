<?php


namespace theme_apoa\form;

require_once("$CFG->libdir/formslib.php");

class searchelibrary_form extends \moodleform {

    public function definition() {
        $mform = $this->_form;
        $categoryid = $this->_customdata['categoryid'];

        $search_options = array(
            'url' => 'Search by URL',
            'Journal and title' => 'Search by Journal and title',
        );
        $mform->addElement('select', 'search_option', 'Search Option:', $search_options);
        $mform->setType('search_option', PARAM_ALPHA);
        $mform->addRule('search_option', 'Please select a valid search option.', 'required');

        // Get list of categories to use as parents, with site as the first one.
        if ($categoryid) {
            // Editing an existing category
            $strsubmit = get_string('savechanges');
        }

        $elibraryid = get_config('theme_apoa' ,'elibraryid');
        $elibrary = \core_course_category::get($elibraryid);

        $options = array(0 => 'All');
        foreach($elibrary->get_children() as $journal){
            $options[$journal->id] = $journal->name;
        }

        $mform->addElement('radio', 'radio', 'By URL', 'By URL', 1);
        $mform->addElement('radio', 'radio', 'By Title', 'By Title', 2);
        $mform->setType('radio', PARAM_INT);
        $mform->setDefault('radio', 1);

        

        $mform->addElement('select', 'journal_select', 'Journal:', $options);
        $mform->setType('journal_select', PARAM_INT);
        $mform->addRule('title', 'Please enter a valid Journal.', 'required', null, 'client');

        $mform->addElement('text', 'title', 'Title:');
        $mform->setType('title', PARAM_TEXT);
        $mform->addRule('title', 'Please enter a valid URL.', 'required', null, 'client');

        $mform->addElement('text', 'url_search', 'URL:');
        $mform->setType('url_search', PARAM_URL);
        $mform->addRule('url_search', 'Please enter a valid URL.', 'required', null, 'client');

        $mform->addElement('hidden', 'categoryid', 0);
        $mform->setType('categoryid', PARAM_INT);
        $mform->setDefault('categoryid', $categoryid);

        $this->add_action_buttons(false, $strsubmit);

    }

    public function definition_after_data() {
        $mform = $this->_form;

        // Get the value of the search option
        $search_option = $mform->getElementValue('search_option');

        // Hide or display fields based on the search option
        if ($search_option == 'url') {
            $mform->removeElement('journal_select');
            $mform->removeElement('title');
        } else {
            $mform->removeElement('url_search');
        }
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

    public function renderf(){
        $elements = $this->_form->_elements;
        $elementsArray = array();

        foreach ($elements as $element) {
            $elementArray = array(
                'name' => $element->getName(),
                'label' => $element->getLabel(),
                'value' => $element->getValue(),
                'type' => $element->getType(),
                'html' => $element->toHtml(),
            );
            if (isset($elementsArray[$element->getName()])){
                $elementsArray[$element->getName()][] = $elementArray;
            }
            else{
                $elementsArray[$element->getName()][] = $elementArray;
            }
        }

        return $elementsArray;
    }
}