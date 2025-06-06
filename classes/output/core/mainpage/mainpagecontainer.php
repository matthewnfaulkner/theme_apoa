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
        $mainpage_cache = \cache::make('theme_apoa', 'main_page_cache');
        $key = $this->sectionname;
        $item = new $this->itemclass();

        $data = $mainpage_cache->get($key);

        if ($data){
            $template = $data;
        }
        else{
            $template = $item->export_for_template($output);
        }
        return $output->render_from_template($item->get_template_name($output), $template);

    }

    public function get_child_class(\renderer_base $output){

        $item = new $this->itemclass();
        $template = $item->export_for_template($output);
        return $template;
    }

    public function get_extra_classes() {
        
        switch ($this->sectionname){
            case 'jumbo':
                return 'main-page-container container-fluid';
            case 'subjumbo':
                return 'container main-page-container my-0';
            case 'events':
                return 'main-page-container container-fluid';
            case 'about':
                return 'main-page-container';
            case 'sections':
                return 'text-primary main-page-container px-5 ';
            case 'membership':
                return 'py-4 py-xl-5 bg-primary d-flex flex-row justify-content-center mw-100';
            case 'resources':
                return 'py-4 py-xl-5 main-page-container flex-column d-flex justify-content-center align-items-center px-sm-5';
            default:
                return '';
        }
    }
    
}