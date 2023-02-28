<?php

namespace theme_apoa\output\core\mainpage;

defined('MOODLE_INTERNAL') || die;


class mainpagecontainer implements \templatable , \renderable {


    use mainpage_named_templatable;


    protected string $sectionname;

     /** @var string the item output class name */
    protected string $itemclass;

    public function __construct(string $sectionname) {

        $this->sectionname = $sectionname;
        $this->itemclass = "theme_apoa\\output\\core\\mainpage\\$sectionname";
    }
    
        
    
    public function export_for_template(\renderer_base $output) {

        global $CFG;
        $item = new $this->itemclass();
        $template = $item->export_for_template($output);
        return $output->render_from_template($item->get_template_name($output), $template);

    }


    public function get_extra_classes() {
        
        switch ($this->sectionname){
            case 'jumbo':
                return 'main-page-container container-fluid';
            case 'subjumbo':
                return 'container main-page-container my-0';
            case 'events':
                return 'main-page-container container-fluid';
            case 'courses':
                return 'py-4 py-xl-5 main-page-container bg-primary';
            case 'sections':
                return 'text-primary py-4 py-xl-5 main-page-container px-5';
            case 'membership':
                return 'py-4 py-xl-5 bg-primary';
            case 'resources':
                return 'py-4 py-xl-5 main-page-container flex-column d-flex justify-content-center align-items-center px-sm-5';
            default:
                return '';
        }
    }
    
}