<?php

namespace theme_apoa\output\core\mainpage;

defined('MOODLE_INTERNAL') || die;



class jumbo implements \templatable , \renderable {


    use mainpage_named_templatable;



    protected string $contentgenerator;

    public function __construct() {
    }
        
    
    public function export_for_template(\renderer_base $output) {

        $template = $this->get_content($output);

        return $template;

    }

    protected function get_content(\renderer_base $output) {
        global $CFG;

        $component = 'theme_apoa';
        $url = $CFG->wwwroot . '/course/view.php?id=' . get_config($component, 'jumboid');
        $jumbomain = ['jumbotitle' => get_config($component, 'jumbotitle'),
            'jumbodescription' => get_config($component, 'jumbodescription'),
            'jumbovideoflag' => get_config($component, 'jumbovideoflag'),
            'jumbotag' => get_config($component, 'jumbotag'),
            'jumbobanner' => theme_apoa_get_file_from_setting('jumbobanner'),
            'jumbovideo' => theme_apoa_get_file_from_setting('jumbovideo'),
            'jumbobannerlogo' => theme_apoa_get_file_from_setting('jumbobannerlogo'),
            'jumbourl' => $url
        ];
        $jumboside = new \theme_apoa\output\core\lists\course_list('course_list', 'Mainpage');

        $template = ['jumbomain' => $jumbomain,
            'jumboside' => $jumboside->export_for_template($output)];
        return $template;

    }
    
}