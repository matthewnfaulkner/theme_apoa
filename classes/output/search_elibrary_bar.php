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
        if ($this->mform->is_cancelled()) {
            return;
        } else if ($data = $this->mform->get_data()) {
            $this->mform->validation($data, null);
            return;
        }
    }
    
        
    
    public function export_for_template(\renderer_base $output) {
        $html = $this->mform->render();

        $out = $this->processHtmlString($html);
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
    
            // Remove each col-form-label element from the form
            foreach ($colFormLabels as $label) {
                $label->parentNode->removeChild($label);
            }
        }
    
        // Get the modified HTML string
        $modifiedHtml = $dom->saveHTML();
    
        return $modifiedHtml;
    }
    
    protected function extract_form_elements($html){

        $doc = new \DOMDocument();
        $doc->loadHTML($html);

        $template = [];
        $xpath = new \DOMXPath($doc);

        // Get the form element
        $formElement = $xpath->query('//form')->item(0);

        // Get form attributes
        $formAttributes = $this->get_attributes($formElement);

        $formId = $formAttributes['id'];
        $gethidden = "//form[@id='$formId']//input[@type='hidden']";
        $hiddeninputs = $xpath->query($gethidden);

        foreach($hiddeninputs as $hiddeninput) {
            $formAttributes['hiddeninputs'][] = $this->get_attributes($hiddeninput);
        }

        $expression = "//div[contains(@class, 'form-group')]";
        $formgroups = $xpath->query($expression);


        foreach($formgroups as $formgroup){


            $fields =  ".//input | .//select";

            
            $formgroupAttributes = $this->get_attributes($formgroup);

            $inputs = $xpath->query($fields, $formgroup);

            $formgroupinputs = [];
            foreach($inputs as $input) {
                $parentElement = $input->parentNode;
                $inputAttributes = $this->get_attributes($input);
                $inputAttributes[$inputAttributes['name']] = $inputAttributes['name']; 
                if ($parentElement->nodeName === 'label') {
                    $inputAttributes['label'] = trim($parentElement->nodeValue);
                }

                $formgroupinputs[] = $inputAttributes;

            }
            $formgroupAttributes['inputs'] = $formgroupinputs;
            $formgroupname = $formgroupAttributes['id'];
            $formgroupAttributes[$formgroupname] = $formgroupname;
            $groups[] = $formgroupAttributes;
            
        }
        $formAttributes['groups'] = $groups;
        $template = $formAttributes;

        return $template;
        }
    

    protected function get_attributes($element) {
        $elementAttributes = [];
        foreach($element->attributes as $attribute){
            $elementAttributes[$attribute->name] = $attribute->value;
        }
        return $elementAttributes;
    }
}