<?php

namespace theme_apoa\output;

use core_course_category;

defined('MOODLE_INTERNAL') || die;



class search_elibrary_bar implements \templatable {


    protected \theme_apoa\form\searchelibrary_form $mform;

    protected core_course_category $coursecat;

    public function __construct(core_course_category $coursecat) {
        $this->coursecat = $coursecat;
        $radio = optional_param('radio', 0, PARAM_INT);
        $url_search = optional_param('url_search', 0, PARAM_URL);
        $journal_select = optional_param('journal_select', 0, PARAM_INT);
        $title = optional_param('title', 0, PARAM_TEXT);
        $params = array(
            'categoryid' => $this->coursecat->id,
            'radio' => $radio,
            'url_search' => $url_search,
            'journal_select' => $journal_select,
            'title' => $title,
        );
        $this->mform = new \theme_apoa\form\searchelibrary_form(null, $params);
        if ($this->mform->is_submitted()) {
            return;
        } else if ($data = $this->mform->get_data()) {
            return;
        }
    }
    
        
    
    public function export_for_template(\renderer_base $output) {
        return $this->mform->render();
    }



    
}