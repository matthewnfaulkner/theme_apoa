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
 *  Search Elibrary form.
 *
 * @package     theme_apoa
 * @copyright   2025 Matthew Faulkner matthewfaulkner@apoaevents.com
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */                                                             


namespace theme_apoa\form;

use stdClass;

require_once("$CFG->libdir/formslib.php");

/**
 * Class searchelibrary_form 
 * 
 * Form for searching the elibrary for articles
 */
class searchelibrary_form extends \moodleform {

    /**
     * Journal object
     *
     * @var stdClass
     */
    protected stdClass $journal;

    /**
     * category context
     *
     * @var \context_coursecat
     */
    protected \context_coursecat $context;


    /**
     * Define form fields
     *
     * @return void
     */
    public function definition() {
        $mform = $this->_form;
        
        $categoryid = $this->_customdata['categoryid'];        

        if(isset($this->_customdata['journal'])){
            $this->journal = $this->_customdata['journal'];
        }   
        


        $elibraryid = get_config('theme_apoa' ,'elibraryid');
        $elibrary = \core_course_category::get($elibraryid);

        $options = array($elibraryid => 'All Journals');
        foreach($elibrary->get_children() as $journal){
            $options[$journal->id] = $journal->name;
        }


        $journal_select = $mform->createElement('select', 'journal_select', 'Journal:', $options, array('placeholder' => "Select Journal"));
        $mform->setType('journal_select', PARAM_INT);

        $title = $mform->createElement('text', 'title', 'Title:', array('placeholder' => "Search by title"));


         $mform->addGroup([$journal_select, $title], 'title_search_group');
        $mform->setType('title', PARAM_TEXT);


        $mform->addElement('hidden', 'categoryid', $categoryid);
        $mform->setType('categoryid', PARAM_INT);
        $mform->setDefault('categoryid', $categoryid);

        
        $this->add_action_buttons(false, "Search");

        $mform->hideif('request', 'noresult', 'eq', 0);
    }

    /**
     * Extra definitions after data added.
     *
     * @return void
     */
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

    /**
     * Public getter for journal param
     *
     * @return void
     */
    public function get_journal(){
        return $this->journal;
    }


    /**
     * Limit number of form submissions
     *
     * @return boolean
     */
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

    /**
     * Handle errors for no result 
     *
     * @return void
     */
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

        $title = $data['title_search_group']['title'];

        if ($urlortitle && !$title) {
            $errors['title_search_group']= 'Please enter a title.';
        }

        return $errors;
    }

}