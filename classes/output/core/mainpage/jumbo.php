<?php

namespace theme_apoa\output\core\mainpage;

defined('MOODLE_INTERNAL') || die;

use block_accessreview\external\get_module_data;
use moodle_url;
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
        global $PAGE, $USER, $CFG, $DB;

        $component = 'theme_apoa';
        if($courseid = get_config($component, 'jumboid')){
            $course = get_course($courseid);
            $startdate = $course->startdate;
            $url = get_config($component, 'jumbolink');
        }
        else{
            $url = get_config($component, 'jumbolink');
        }
        if($announcementsid = get_config($component, 'jumboannouncementsid')){
            if($cm = get_coursemodule_from_id('forum', $announcementsid)){
                if($forum = $DB->get_record('forum', array('id' => $cm->instance))){
                    $course = get_course($cm->course);
                    $modcontext = \context_module::instance($cm->id);
                    $entityfactory = \mod_forum\local\container::get_entity_factory();
                    $forumentity = $entityfactory->get_forum_from_stdclass($forum, $modcontext, $cm, $course);
                    $discussionsummaries = mod_forum_get_discussion_summaries($forumentity, $USER, null, 0, 0, 1);
                    $firstdiscussionsummary = reset($discussionsummaries);
                    $firstdiscussion = $firstdiscussionsummary->get_discussion();
                    $firstposttext = $firstdiscussion->get_name();
                    $announcementlink = new moodle_url('/mod/forum/discuss.php', array('d' => $firstdiscussion->get_id()));
                }
            }
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
            'jumbostartdate' => $startdate,
            'jumboannouncement' => $firstposttext,
            'announcementlink' => $announcementlink
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