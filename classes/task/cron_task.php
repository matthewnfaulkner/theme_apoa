<?php

namespace theme_apoa\task;

/**
 * An example of a scheduled task.
 */
class cron_task extends \core\task\scheduled_task {

    use \core\task\logging_trait;
    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('populate_navigation', 'theme_apoa');
    }

    /**
     * Execute the task.
     */
    public function execute() {
        // Call your own api
        $cache = \cache::make('theme_apoa', 'navigation_cache');
        $this->log_start("Deleting old cache records.");
        //$cache->delete('primarynav');
        $this->log_finish("Finished deleting old navigation cache records");

        $this->log_start("Generating new navigation records");
        $cache->set('primarynav', $this->populate_cache());
        $this->log_finish("Finished generating new navigation records");
    }

    private function populate_cache(){

        $data = new \stdClass();

        $primarynavitemcount = get_config('theme_apoa', 'primarynavcount');

        for ($i = 1; $i <= $primarynavitemcount; $i++) {
            $primarynavitem = get_config('theme_apoa', 'primarynavitems' . $i);
            $includechildren = get_config('theme_apoa', 'primarynavitems' . $i . '_adv');
            
            $category = \core_course_category::get($primarynavitem);


            if($includechildren) {
                $data->{$category->name} = $this->get_cache_struct_for_section($category, true);
            }else {
                $data->{$category->name} = $this->get_cache_struct_for_section($category, false);
            }
            
        }

        return $data;

    }

    private function get_cache_struct_for_section($category, $includeChildren) {
    
        $sections = new \stdClass();
        $sections->name = $category->name;
        $sections->url = "/course/index.php?categoryid={$category->id}";
        $sections->type = \navigation_node::TYPE_CATEGORY;
        $sections->id = $category->id;
        $sections->children = [];
        $sections->key = \navigation_node::TYPE_CATEGORY . $category->id;

        if ($includeChildren){
            $subcategories = $category->get_children();
            foreach ($subcategories as $subcategory) {
                $subsections = new \stdClass;
                if($sectionlink = get_config('theme_apoa', 'sectionlink' . $subcategory->id)){
                    $subsections->url = new \moodle_url($sectionlink);
                }
                else{
                    $subsections->url = new \moodle_url("/course/index.php?categoryid={$subcategory->id}");
                }
                $subsections->name = $subcategory->name;
                $subsections->type = \navigation_node::TYPE_CATEGORY;
                $subsections->id = $subcategory->id;
                $subsections->key = \navigation_node::TYPE_CATEGORY . $subcategory->id;

                $sections->children[] = $subsections;

            }
        }
        $sections->children ? $sections->haschildren = true : $sections->haschildren = false;
        $sections->haschildren ? $sections->showchildreninsubmenu = true : $sections->showchildreninsubmenu = false ;
        return $sections;

    } 
}