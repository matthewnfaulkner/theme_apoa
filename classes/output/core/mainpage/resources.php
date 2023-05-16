<?php

namespace theme_apoa\output\core\mainpage;

use moodle_url;

defined('MOODLE_INTERNAL') || die;



class resources implements \templatable , \renderable {


    use mainpage_named_templatable;


    protected array $resources;
    


    protected string $contentgenerator;

    public function __construct() {
        $this->resources = ['Forum' => 'forum', "Member's Gallery" => 'gallery', 'Contact Us' => 'contact', 'Meetings' => 'meetings'];
    }
        
    
    public function export_for_template(\renderer_base $output) {

        $template = $this->get_content();

        return $template;

    }

    protected function get_content() {

        global $CFG;
        $template['resources'] = [];
        $placeholder = theme_apoa_get_file_from_setting('resources');
        foreach ($this->resources as $label=>$config) {
            if (!$img = theme_apoa_get_file_from_setting('resources' . $config)){
                $img = $placeholder;
            };
            if($id = get_config('theme_apoa', $config .'id')) {
                $link = new moodle_url('/course/index.php?categoryid=', array('categoryid' => $id));
            } else {
                $link = new moodle_url('user/contactsitesupport.php');
            }
            array_push($template['resources'], array('resourcename' => $label,
                        'resourcelink' => $link,
                        'resourceimg' => $img));
        }

        return $template;
    }
    
}