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

        $topchildren = \core_course_category::top()->get_children();
            if (empty($topchildren)) {
                throw new \moodle_exception('cannotviewcategory', 'error');
            }
    
            $data = new \stdClass();
            
            foreach ($topchildren as $child) {
    
                $name = $child->name;
                switch ($name) {
                    case 'Sections':
                       
                        $data->$name = $this->get_cache_struct_for_section($child, true);
                        break;
                    case 'APOA':
                        $apoacategories = $child->get_children();
                        foreach ($apoacategories as $apoacategory) {
                            $subname = $apoacategory->name;
                            switch ($apoacategory->name){
                                case 'About':
                                case 'Committees':
                                    $data->$subname = $this->get_cache_struct_for_section($apoacategory, true);
                                    break;
                                case 'E-Library':
                                    $data->$subname = $this->get_cache_struct_for_section($apoacategory, false);
                                    break;
                                case 'Newsletter':
                                    $data->$subname = $this->get_cache_struct_for_section($apoacategory, false);
                                    break;
                                default:
                                    break;
                                }
                        }
                        break;
                    default:
                            break;
                    }
            }
            $this->log_start('Printing structure of new navigation' . var_dump($data));
            $this->log_finish('End of new navigation structure');
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
                $subsections->url = "/course/index.php?categoryid={$subcategory->id}";
                $subsections->type = \navigation_node::TYPE_CATEGORY;
                $subsections->id = $subcategory->id;
                $subsections->key = \navigation_node::TYPE_CATEGORY . $subcategory->id;

                $sections->children[] = $subsections;

            }
        }
        $sections->children ? $sections->haschildren = true : $sections->haschilren = false;
        $sections->haschildren ? $sections->showchildreninsubmenu = true : $sections->showchildreninsubmenu = false ;
        return $sections;

    } 
}