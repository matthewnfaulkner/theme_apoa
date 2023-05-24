<?php

namespace theme_apoa\output;

use core_course_category;
use core_reportbuilder\local\filters\boolean_select;

defined('MOODLE_INTERNAL') || die;



class search_elibrary_bar implements \templatable {


    protected \theme_apoa\form\searchelibrary_form $mform;

    protected core_course_category $coursecat;

    protected bool $requestformapproved = false;

    public function __construct(core_course_category $coursecat) {

        global $DB;
        $a = $_POST;
        $this->coursecat = $coursecat;
        $urlortitle = $_POST['urlortitle'];
        $courscatid = $_POST['categoryid'];
        $radio = $_POST['radio'];
        $url_search = $_POST['url_search'];
        $journaltitle = $_POST['title_search']->journal_select;
        $title = $_POST['title_search']->title;
        $search = $_POST['submitbutton'];
        $request = $_POST['request'];

        $params = array(
            'noresult' => 1,
            'categoryid' => $this->coursecat->id,
            'urlortitle' => $urlortitle,
            'url_search' => $url_search,
            'title_search' => array(
                'journal_select' => $journaltitle,
                'title' => $title
            ),
        );
        $this->mform = new \theme_apoa\form\searchelibrary_form(null, $params);

        if ($this->mform->is_cancelled()) {
            return;
        } 
        if ($data = $this->mform->get_data()) {
            if($search){
                if(!$urlortitle){
                    if($course = $this->search_for_paper_by_url($data)){
                        $courseurl = new \moodle_url('/course/view.php');
                        $courseurl->param('id', $course->course);
                        redirect($courseurl);
                    }
                }else{
                    if($course = $this->search_for_paper_by_title($data)){
                        $courseurl = new \moodle_url('/course/view.php');
                        $courseurl->param('id', $course->id);
                        redirect($courseurl);
                    }
                }
            $this->mform->no_result();
            }
            if($request){
                $requestdata = new \stdClass();
                $requestdata->fullname = !$urlortitle ? $data->url_search :  $data->title_search['title'];
                $requestdata->shortname = substr($requestdata->fullname, 0, 50);
                $requestdata->category = $data->categoryid;
                $requestdata->summary_editor['text'] ='';
                $requestdata->summary_editor['format'] =1;
                $requestdata->reason = 'elibrary request';
                if(!$this->mform->has_user_submitted_too_often()){
                    $this->requestformapproved = true;
                    \course_request::create($requestdata);
                }

            }
            
        }
    }
    
    
    public function export_for_template(\renderer_base $output) {
        if(!$this->requestformapproved){
            $html = $this->mform->render();
            $out = $this->processHtmlString($html);
        }
        else{
            
            $out = $output->notification("Your request has been submitted and will be processed shortly.", \core\output\notification::NOTIFY_SUCCESS);
        }
        return $out;
    }


    function processHtmlString($html) {
        $dom = new \DOMDocument();
        $dom->loadHTML($html);
    
        $xpath = new \DOMXPath($dom);
    
        // Find the form element in the HTML
        $formElement = $xpath->query('//form')->item(0);
    
        if ($formElement !== null) {
            // Apply the style "width:100%" to the form element
            $formElement->setAttribute('style', 'width:100%');
    
            // Find all elements with class "col-form-label" in the form
            $colFormLabels = $xpath->query('.//div[contains(@class, "col-form-label")]', $formElement);
            
            foreach ($colFormLabels as $label) {
                $label->parentNode->removeChild($label);
            }

            $colElements = $xpath->query('.//div[contains(@class, "felement")]', $formElement);
            // Remove each col-form-label element from the form
            
            foreach ($colElements as $element){
                $oldClass = $element->getAttribute('class');
                $newClass = str_replace('col-md-9', 'col-12', $oldClass);
                $element->setAttribute('class', $newClass);
            }
        }
    
        // Get the modified HTML string
        $modifiedHtml = $dom->saveHTML();
    
        return $modifiedHtml;
    }

    protected function search_for_paper_by_url($data){
        global $DB;

        $url = $data->url_search;


        return  $DB->get_record('elibrary', array('linkurl' => $url));
        
        
    }

    protected function search_for_paper_by_title($data){
        global $DB;

        $journalid = $data->title_search['journal_select'];
        $title = $data->title_search['title'];

        $journal = core_course_category::get($journalid);
        $children = $journal->get_all_children_ids();
        $categories = $journalid . ',' . join(',', $children);
        $firstwordsoftitle = explode(' ', $title, 5);
        $like = join(' ', array_slice($firstwordsoftitle, 0, 4));
        $query = "SELECT * 
        FROM {course} c
        WHERE c.category IN ($categories) AND c.shortname LIKE  '".$like."%'";
        $params['title'] = $title;
        $params['categories'] = $categories;

        return $DB->get_record_sql($query, $params);
    }
    
}