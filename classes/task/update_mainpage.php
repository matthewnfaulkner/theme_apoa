<?php

namespace theme_apoa\task;


require_once($CFG->dirroot . '/theme/apoa/lib.php');
require_once($CFG->dirroot . '/theme/apoa/classes/output/core/course_category.php');
require_once($CFG->dirroot . '/theme/apoa/classes/output/core/tag_course_category.php');
require_once($CFG->dirroot . '/theme/apoa/classes/output/core/tag.php');
require_once($CFG->dirroot . '/theme/apoa/classes/output/core/lists/pagelist.php');

/**
 * An example of a scheduled task.
 */
class update_mainpage extends \core\task\scheduled_task {

    use \core\task\logging_trait;


    protected \cache $cache;

    protected \cache $image_cache;
    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('main_page_cache', 'theme_apoa');
    }

    /**
     * Execute the task.
     */
    public function execute() {
        // Call your own api
        global $CFG;
        $this->cache = \cache::make('theme_apoa', 'main_page_cache');
        $this->log_start("Deleting old cache records.");
        //$this->cache->delete('mainpagecontent');
        $this->log_finish("Finished deleting old main page cache records");


        $this->populate_cache();
        $this->log_start($CFG->wwwroot, var_dump($_SERVER));
    }

    private function populate_cache(){
        global $OUTPUT;

        $mainpage =  new \theme_apoa\output\core\mainpage\mainpage;
        $sections = $mainpage->get_sections();
        foreach ($sections as $section){
            $mainpagecontainer = new \theme_apoa\output\core\mainpage\mainpagecontainer($section);
            $mainpagesection = $mainpagecontainer->get_child_class($OUTPUT);
            $this->cache->set($section, $mainpagesection);
            $this->log_start('Printing structure of new main page' . var_dump($mainpagesection));
        }

        $this->log_finish('End of new mainpage structure');
    }
}