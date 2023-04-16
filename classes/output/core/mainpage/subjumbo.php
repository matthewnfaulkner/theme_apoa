<?php

namespace theme_apoa\output\core\mainpage;

defined('MOODLE_INTERNAL') || die;



class subjumbo implements \templatable , \renderable {


    use mainpage_named_templatable;

    protected array $sections;
    
     /** @var string the item output class name */
     protected string $itemclass;

    public function __construct() {

        $this->sections = ['Section Highlights' => 'course_list', 'Newsletter' => 'newsletter', 'E-library' => 'elibrary'];
    }
        
    
    public function export_for_template(\renderer_base $output) {

        $template = $this->get_content($output);

        return $template;

    }

    protected function get_content(\renderer_base $output) {
        global $CFG;

        $template = [];
        foreach ($this->sections as $key => $type) {
            $this->itemclass = "theme_apoa\\output\\core\\lists\\course_list";
            $subjumboclass = new $this->itemclass($type, $key);
            $subjumbolist = $subjumboclass->export_for_template($output);

            if (isset($subjumboclass->subcategories)){
                $elibrarysubs = [];
                foreach ($subjumboclass->subcategories as $subcategory) {
                    array_push($elibrarysubs, array('categorytitle' => $subcategory->name,
                    'categoryurl' => $subcategory->get_view_link()));
                }
            }

            $onlyalpha = preg_replace("/[^a-zA-Z0-9]+/", "", $key);
            $template[$onlyalpha] = ['content' => $subjumbolist,
                    'sectiontitle' => $key,
                    'sectionmore' => "more " . $key,
                    'sectionurl' => $subjumboclass->redirecturl,
                    'subcategories' => $elibrarysubs];
            
        }

        return $template;
    }
    
}