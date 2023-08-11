<?php

namespace theme_apoa\output\core\mainpage;

defined('MOODLE_INTERNAL') || die;

use theme_apoa\helper\frontpage_cache_helper;

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
        global $PAGE;

        $component = 'theme_apoa';
        if($courseid = get_config($component, 'jumboid')){
            $course = get_course($courseid);
            $startdate = $course->startdate;
            $url = get_config($component, 'jumbolink');
        }
        else{
            $url = get_config($component, 'jumbolink');
        }
        $jumbomain = ['jumbotitle' => get_config($component, 'jumbotitle'),
            'jumbodescription' => get_config($component, 'jumbodescription'),
            'jumbovideoflag' => get_config($component, 'jumbovideoflag'),
            'jumbotag' => get_config($component, 'jumbotag'),
            'jumbobanner' => theme_apoa_get_file_from_setting('jumbobanner'),
            'jumbobannerposter' => theme_apoa_get_file_from_setting('jumbobannerposter'),
            'jumbovideo' => theme_apoa_get_file_from_setting('jumbovideo'),
            'jumbobannerlogo' => theme_apoa_get_file_from_setting('jumbobannerlogo'),
            'jumbourl' => $url,
            'jumbostartdate' => $startdate
        ];
        //$jumboside = new \theme_apoa\output\core\lists\course_list('course_list', 'sidejumbo');

        $region = 'sidejumbo';

        $indexes = [['index' => 0],
        ['index' => 1],
        ['index' => 2]];

        $blockhelper = new frontpage_cache_helper($region);

        $template[$region] =['blocks' => $blockhelper];

        $block = ['addblockbutton' => $output->addblockbutton($region),
                    'blocks' => $blockhelper,
                    'indexes' => $indexes];
        
        $template = ['jumbomain' => $jumbomain,
             $region => $block];
        return $template;

    }
    
}