<?php

namespace theme_apoa\output\core\mainpage;

use moodle_url;

defined('MOODLE_INTERNAL') || die;



class events implements \templatable , \renderable {


    use mainpage_named_templatable;

    protected array $sections;


    protected string $itemclass;


    protected string $contentgenerator;

    public function __construct() {
        $this->sections = ['Previous Events' => 'past', 'Ongoing Events' => 'inprogress', 'Future Events' => 'future'];
    }
    
        
    
    public function export_for_template(\renderer_base $output) {

        $template = $this->get_content($output);

        return $template;

    }

    protected function get_content(\renderer_base $output) {
        global $CFG;

        $template = [];
        foreach ($this->sections as $key => $type) {
            $this->itemclass = "theme_apoa\\output\\core\\lists\\event_list";
            $subjumboclass = new $this->itemclass($type, $key);
            if ($subjumbolist = $subjumboclass->export_for_template($output)) {;
                
                $onlyalpha = preg_replace("/[^a-zA-Z0-9]+/", "", $key);
                $template[$onlyalpha] = ['content' => $subjumbolist,
                        'sectiontitle' => $key,
                        'sectionmore' => "more " . $key,
                        'sectionurl' => new moodle_url("/tag/index.php?", array('group' => $type, 'tag' => 'Events', 'tc' => 0))];
            }
            
        }

        return $template;
    }
    
}