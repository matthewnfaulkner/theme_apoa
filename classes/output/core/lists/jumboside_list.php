<?php

namespace theme_apoa\output\core\lists;

use stdClass;

defined('MOODLE_INTERNAL') || die;



class jumboside_list implements \templatable , \renderable {


    use \theme_apoa\output\core\mainpage\mainpage_named_templatable;



    protected string $contentgenerator;

    protected array $courses;

    protected \core_tag_tag $tag;


    public function __construct() {
 
        $tags = \theme_apoa_tag_tag::guess_by_name('Mainpage');
        $this->tag = reset($tags);
        $rawcourses = $this->tag->get_tagged_items('core', 'course', '0', '3', '', 'timecreated DESC');
        foreach ($rawcourses as $rawcourse){
            $this->courses[$rawcourse->id] = new \core_course_list_element($rawcourse);
        }
    }
        
    
    public function export_for_template(\renderer_base $output) {

        $a = new stdClass;
        $items = [$a,$a,$a];
        $template = [];
        foreach ($this->courses as $course){
            $jumbosidelistitem = new \theme_apoa\output\core\listitems\jumboside_list_item($course, $this->tag);
            array_push($template, $jumbosidelistitem->export_for_template($output));
        }
        return $template;

    }

    protected function get_content() {
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
        $jumboside = [['jumbosidetitle' => 'title',
            'jumbosidetag' => 'subjumbotag',
            'jumbosidecat' => 'subjumbocat',
            'jumbosideimg' => 'subjumboimg',
            'jumbosideurl' => 'subjumbourl'
            ]];
        
        $template = ['jumbomain' => $jumbomain,
            'jumboside' => $jumboside];
        return $template;

    }
    
}