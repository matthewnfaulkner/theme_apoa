<?php

namespace theme_apoa\output\core\listitems;

use moodle_url;
use stdClass;

defined('MOODLE_INTERNAL') || die;



class course_list_item implements \templatable , \renderable {


    use \theme_apoa\output\core\mainpage\mainpage_named_templatable;



    protected \stdclass $course;

    protected \core_course_list_element $courselistelement;

    protected int $index;

    protected bool $iselibrary;

    protected \cache $image_cache;

    public function __construct(\stdClass $course, $index, $iselibrary) {
        
        $this->course = $course;
        $this->courselistelement = new \core_course_list_element($course);
        $this->index = $index;
        $this->iselibrary = $iselibrary;
        $this->image_cache = \cache::make('theme_apoa', 'image_cache');

    }
        
    
    public function export_for_template(\renderer_base $output) {

        global $CFG, $USER, $PAGE;

        $coursecat = \core_course_category::get($this->course->category);

        if($tag = reset(\theme_apoa_tag_tag::get_item_tags('core', 'course', $this->course->id))) {
            $tagurl = $tag->get_view_url();
            $tagname = $tag->get_display_name();
        }
        else{
            $tagurl = '';
            $tagname = '';
        }

        $rootcat = get_parent_category_by_generation($coursecat, 2);

        if ($this->iselibrary) {
            //require($CFG->dirroot . 'mod/forum/lib.php');
            require_once($CFG->dirroot.'/mod/forum/lib.php');
            $forum = forum_get_course_forum($this->course->id, 'social');
            if ($forum) {
                $coursemodule = get_coursemodule_from_instance('forum', $forum->id);
                $modcontext = \context_module::instance($coursemodule->id);
                $entityfactory = \mod_forum\local\container::get_entity_factory();
                $forumentity = $entityfactory->get_forum_from_stdclass($forum, $modcontext, $coursemodule, $this->course);
                $discussionsummaries = mod_forum_get_discussion_summaries($forumentity, $USER, null, 0, 0, 0);
                $discussionsummariesrendererable = new \format_apoapage\output\discussiontopics($PAGE, $discussionsummaries);
                $discussionsummariesrender = $discussionsummariesrendererable->export_for_template(($output));
                $discussionsummary = $discussionsummariesrender['discussionlist'];
                $topdiscussionsummary['forumpost'] = reset($discussionsummary);
                
                $topdiscussionsummary['forumurl'] = new moodle_url('/mod/forum/view.php', [
                    'id' => $coursemodule->id,
                ]);
            }
        }
        $wwwroot = $CFG->wwwroot;

        $itemurl = $wwwroot . "/course/view.php?id=" . $this->course->id;
        $caturl  = $coursecat->get_view_link();
        $rooturl  = $rootcat->get_view_link();

        $itemdesc = $this->course->summary;
        $itemsummary = $this->course->summary;
        $key = md5($course->id);


        foreach ($this->courselistelement->get_course_overviewfiles() as $file) {
            $isimage = $file->is_valid_image();
            $imgurl = \moodle_url::make_file_url("$CFG->wwwroot/pluginfile.php",
                '/' . $file->get_contextid() . '/' . $file->get_component() . '/' .
                $file->get_filearea() . $file->get_filepath() . $file->get_filename(), !$isimage);
            if ($isimage) {
                break;
            }
        }
        if($file){
            $filecontents = file_get_contents($imgurl);
            $imgurl  = 'data:image/jpeg;base64,' . base64_encode($filecontents);
        }



        $template = ["itemtitle" => $this->course->shortname,
            "itemcat" => $coursecat->name,
            "itemdescription" => $itemdesc,
            "itemsummary" => $itemsummary,
            "itemroot" => $rootcat->name,
            "itemrootid" => $rootcat->id,
            "itemurl" => $itemurl,
            "itemcaturl" => $caturl,
            "itemrooturl" => $rooturl,
            "itemimg" => $imgurl,
            'itemtag' => $tagname,
            'itemtagurl' => $tagurl,
            'itemindex' => $this->index,
            'first' => !$this->index,
            'forum' => $topdiscussionsummary,
            'count' => $this->course->count,
            'itemstartdate' => $this->course->startdate,
            'itemenddate' => $this->course->enddate
        ];

        return $template;

    }

    
}