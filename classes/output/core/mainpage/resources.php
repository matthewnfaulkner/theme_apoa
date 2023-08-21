<?php

namespace theme_apoa\output\core\mainpage;

use moodle_url;

defined('MOODLE_INTERNAL') || die;

use theme_apoa\helper\frontpage_cache_helper;

class resources implements \templatable , \renderable {


    use mainpage_named_templatable;


    protected array $resources;
    


    protected string $contentgenerator;

    public function __construct() {
        $this->resources = ['Newsletter' => 'newsletter', 'Forum' => 'forum', 
        'Memberships' => 'membership', "Member's Gallery" => 'gallery', 
        'Contact Us' => 'contact', 'Meetings' => 'meetings', 
        'Blog' => 'blog', 'Educational Videos' => 'eduvideos',
        ];
    }
        
    
    public function export_for_template(\renderer_base $output) {

        $region = 'resources';

        $blockhelper = new frontpage_cache_helper($region);

        $template[$region] =['addblockbutton' => $output->addblockbutton($region), 'blocks' => $blockhelper];

        return $template;

    }

    protected function get_content() {

        /*
        $template['resources'] = [];
        $placeholder = theme_apoa_get_file_from_setting('resources');
        foreach ($this->resources as $label=>$config) {
            if (!$img = theme_apoa_get_file_from_setting('resources' . $config)){
                $img = $placeholder;
            };
            if($path = get_config('theme_apoa', 'resources' . $config . 'link')){
                $link = new moodle_url($path);
            }
            else{
                $link =  new moodle_url('/user/contactsitesupport.php');
            }
            array_push($template['resources'], array('resourcename' => $label,
                        'resourcelink' => $link,
                        'resourceimg' => $img));
        }

        return $template;*/

        $region = 'resources';

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