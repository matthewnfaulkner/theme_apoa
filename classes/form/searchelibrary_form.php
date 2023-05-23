<?php


namespace theme_apoa\form;

require_once("$CFG->libdir/formslib.php");

class searchelibrary_form extends \moodleform {

    public function definition() {
        $mform = $this->_form;
        $categoryid = $this->_customdata['categoryid'];


        // Get list of categories to use as parents, with site as the first one.
        if ($categoryid) {
            // Editing an existing category
            $strsubmit = get_string('Search for paper');
        }

        $elibraryid = get_config('theme_apoa' ,'elibraryid');
        $elibrary = \core_course_category::get($elibraryid);

        $options = array(0 => 'All Journals');
        foreach($elibrary->get_children() as $journal){
            $options[$journal->id] = $journal->name;
        }

        
        $myarray = array();
        $myarray[] = $mform->createElement('radio', 'yesno', null, 'By URL', 1);
        $myarray[] = $mform->createElement('radio', 'yesno', null, 'By Title', 2);
        //$myarray[] = $mform->createElement('submit', 'submitbutton', 'search');
        $mform->addGroup($myarray, 'radioar', '', array(' '), false);
        $mform->setDefault('yesno', 1);
        /*$mform->addElement('radio', 'radio', null, 'By URL', 1);
        $mform->addElement('radio', 'radio', null, 'By Title', 2);
        $mform->setType('radio', PARAM_INT);
        $mform->setDefault('radio', 1);*/
        

        $url_search = $mform->addElement('text', 'url_search', 'URL:', array('placeholder' => "Search by URL"));
        $mform->setType('url_search', PARAM_URL);
        //$mform->addRule('url_search', 'Please enter a valid URL.', 'required', null);
        
        $mform->addGroup([$url_search], 'url_search');

        $mform->hideif('url_search', 'yesno', 'eq', 2);

        


        $journal_select = $mform->createElement('select', 'journal_select', 'Journal:', $options, array('placeholder' => "Select Journal"));


        
        $mform->setType('journal_select', PARAM_INT);
        $mform->addRule('title', 'Please enter a valid Journal.', 'required', null, 'client');


        $title = $mform->createElement('text', 'title', 'Title:', array('placeholder' => "Search by title"));

        $mform->setType('title', PARAM_TEXT);
        $mform->addRule('title', 'Please enter a valid URL.', 'required', null, 'client');

        $mform->addGroup([$journal_select, $title], 'title_search');

        $mform->hideIf('title_search', 'yesno', 'eq', 1);

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
        /*if ($search_option == 'url') {
            $mform->removeElement('journal_select');
            $mform->removeElement('title');
        } else {
            $mform->removeElement('url_search');
        }*/
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Validate URL field
        $url = $data['url'];
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $errors['url'] = 'Please enter a valid URL.';
        }

        // Perform additional custom validation
        if ($data['radio'] == '1' && empty($data['url_search'])) {
            $errors['url_search'] = 'Please enter a value.';
        }

        return $errors;
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