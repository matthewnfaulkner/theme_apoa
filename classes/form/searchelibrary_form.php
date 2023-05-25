<?php


namespace theme_apoa\form;

use stdClass;

require_once("$CFG->libdir/formslib.php");

class searchelibrary_form extends \moodleform {

    protected stdClass $journal;

    protected \context_coursecat $context;

    public function definition() {
        $mform = $this->_form;
        

        $categoryid = $this->_customdata['categoryid'];
        $noresult = $this->_customdata['noresult'];

        

        if(isset($this->_customdata['journal'])){
            $this->journal = $this->_customdata['journal'];
        }   
        


        $elibraryid = get_config('theme_apoa' ,'elibraryid');
        $elibrary = \core_course_category::get($elibraryid);

        $options = array($elibraryid => 'All Journals');
        foreach($elibrary->get_children() as $journal){
            $options[$journal->id] = $journal->name;
        }

        
        
        $myarray = array();
        $myarray[] = $mform->createElement('radio', 'urlortitle', null, 'By URL', 0);
        $myarray[] = $mform->createElement('radio', 'urlortitle', null, 'By Title', 1);
        $myarray[] = $mform->createElement('submit', 'submitbutton', 'Search for Paper');
        $myarray[] = $mform->createElement('submit', 'request', 'Request');

        $mform->addGroup($myarray, 'radioar', '', array(' '), false);
        $mform->addHelpButton('urlortitle', 'urlortitle_help', 'theme_apoa', 'Select whether to search by URL or Paper Title');
        $mform->setDefault('urlortitle', 0);
        

        $url_search = $mform->createElement('text', 'url_search', 'URL:', array('placeholder' => "Search by URL"));
        $mform->setType('url_search', PARAM_URL);
        //$mform->addRule('url_search', 'Please enter a valid URL.', 'required', null);
        
        $mform->addGroup([$url_search], 'url_search_group');

        $mform->hideif('url_search_group', 'urlortitle', 'eq', 1);

        $journal_select = $mform->createElement('select', 'journal_select', 'Journal:', $options, array('placeholder' => "Select Journal"));

        $mform->setType('journal_select', PARAM_INT);


        $title = $mform->createElement('text', 'title', 'Title:', array('placeholder' => "Search by title"));


        $mform->addGroup([$journal_select, $title], 'title_search_group');
        $mform->setType('title', PARAM_TEXT);

        $mform->hideIf('title_search_group', 'urlortitle', 'eq', 0);

        $mform->addElement('hidden', 'categoryid', $categoryid);
        $mform->setType('categoryid', PARAM_INT);
        $mform->setDefault('categoryid', $categoryid);

        

        //$this->add_action_buttons(false, $strsubmit);

        $mform->hideif('request', 'noresult', 'eq', 0);
    }

    public function definition_after_data(){
        $mform = $this->_form;
        $mform->addElement('hidden', 'noresult');
        $mform->setType('noresult', PARAM_INT);
        $mform->setDefault('noresult', 0);
        if($this->is_submitted()){
            $this->context = \context_coursecat::instance($this->_customdata['categoryid']);
            if(has_capability('moodle/course:request', $this->context)){
                $mform->_submitValues['noresult'] = 1;
                $mform->setDefault('noresult', 1);
            }
        }
    }

    public function get_journal(){
        return $this->journal;
    }


    public function has_user_submitted_too_often(){
        global $USER, $DB;

        $userreqeusts = $DB->get_records('course_request', array('requester' => $USER->id));

        if(count($userreqeusts) > 5){
            $this->_form->_errors['url_search_group'] = "You have submitted too many requests, please allow time for your previouse requests to be processed";
            $this->_form->_errors['title_search_group'] = "You have submitted too many requests, please allow time for your previouse requests to be processed.";
        
            return true;
        }
        return false;
    }


    public function no_result(){
        $this->_form->_errors['url_search_group'] = "We don't currently have that paper.";
        $this->_form->_errors['title_search_group'] = "We don't currently have that paper.";
        if(isset($this->context)){
            if(has_capability('moodle/course:request', $this->context)){
                $this->_form->_errors['url_search_group'] .= "If you'd like you can submit a request for it.";
                $this->_form->_errors['title_search_group'] .= "If you'd like you can submit a request for it.";
            }

        }
    }

    public function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);
        
        // Validate URL field
        $urlortitle = $data['urlortitle'];
        $url = $data['url_search_group']['url_search'];
        if($url && !$urlortitle){
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                $errors['url_search_group'] = 'Please enter a valid URL.';
            }
            $parts = parse_url($url);
            if (isset($parts['scheme']) && isset($parts['host'])) {
                $scheme = $parts['scheme'];
                $host = $parts['host'];
                $url = $scheme . '://' . $host;
            } else {
                // Handle case when scheme or host is missing
                $url = '';
            }
            
            $validhost = $DB->get_record('theme_apoa_journals', array('url' => $url));
            
            if(!$validhost){
                $errors['url_search_group'] = 'This is not a from a journal we support';
            }else{
                $this->_customdata['journal'] = $validhost;
            }
        }

        $journal = $data['title_search_group']['journal_select'];
        $title = $data['title_search_group']['title'];

        if ($urlortitle && !$title) {
            $errors['title_search_group']= 'Please enter a title.';
        }

        return $errors;
        // Additional validation for the path fiel
        // Perform any necessary validation for the path field

    }

}