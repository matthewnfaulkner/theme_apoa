<?php

// Every file should have GPL and copyright in the header - we skip it in tutorials but you should not skip it for real.

// This line protects the file from being accessed by a URL directly.                                                               
defined('MOODLE_INTERNAL') || die();

// We will add callbacks here as we add features to our theme.

define('PRIMARY_CATEGORY_DEPTH', 2);
define('SECONDARY_CATEGORY_DEPTH', 3);


function theme_apoa_get_main_scss_content($theme) {                                                                                
    global $CFG;                                                                                                                    
                                                                                                                                    
    $scss = '';                                                                                                                     
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;                                                 
    $fs = get_file_storage();                                                                                                       
                                                                                                                                    
    $context = context_system::instance();                                                                                          
    if ($filename == 'default.scss') {                                                                                              
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');                                        
    } else if ($filename == 'plain.scss') {                                                                                         
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/plain.scss');                                          
    } else if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_boost', 'preset', 0, '/', $filename))) {              
        $scss .= $presetfile->get_content();                                                                                        
    } else {                                                                                                                        
        // Safety fallback - maybe new installs etc.                                                                                   
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');                                        
    }                                                                                                                               
    
    $pre = file_get_contents($CFG->dirroot . '/theme/apoa/scss/pre.scss');
    $post = file_get_contents($CFG->dirroot . '/theme/apoa/scss/post.scss');
    $booststudio = file_get_contents($CFG->dirroot . '/theme/apoa/scss/bootstudio.scss');
    $swiper = file_get_contents($CFG->dirroot . '/theme/apoa/scss/swiper.scss');
    return $pre . "\n" . $scss . "\n" . $post . "\n" . $booststudio. "\n" . $swiper;                                                                                                                  
}


function theme_apoa_get_pre_scss($theme) {
    // Load the settings from the parent.                                                                                           
    $theme = theme_config::load('boost');                                                                                           
    // Call the parent themes get_pre_scss function.                                                                                
    return theme_boost_get_pre_scss($theme);                         
}

function theme_apoa_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    if ($context->contextlevel == CONTEXT_SYSTEM && ($filearea === 'logo' || $filearea === 'backgroundimage' ||
        $filearea === 'loginbackgroundimage')) {
        $theme = theme_config::load('boost');
        // By default, theme files must be cache-able by both browsers and proxies.
        if (!array_key_exists('cacheability', $options)) {
            $options['cacheability'] = 'public';
        }
        return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
    }else if ($context->contextlevel == CONTEXT_SYSTEM && $filearea === 'jumbobanner' || $filearea === 'jumbobannerlogo' || 
    preg_replace('/[0-9]+/', '', $filearea) === 'sectionlogo' || $filearea === 'resources' || $filearea === 'jumbobannerposter' ||
    $filearea === 'about'){
        $theme = theme_config::load('apoa');
        // By default, theme files must be cache-able by both browsers and proxies.
        if (!array_key_exists('cacheability', $options)) {
            $options['cacheability'] = 'public';
        }
        return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
    } else {
        send_file_not_found();
    }
}


function theme_apoa_get_secondary_nav_items(navigation_node $parentnode, array $subcategories, string $component) {

    foreach ($subcategories as $subcategory) {
    
        $nospacename = preg_replace("/[^a-zA-Z0-9]+/", "", $subcategory->name);
        $name  = strpos(get_string($nospacename, $component), '[') ?  get_string($nospacename, $component) : $subcategory->name;
        if ($coursecount = $subcategory->get_courses_count() == 1 && $subcategory->get_children_count() == 0){
            if($courses = $subcategory->get_courses($limit = 1)) {
                $course = reset($courses);
                $parentnode->add(
                    $name ,
                    new \moodle_url('/course/view.php', ['id' => $course->id]),
                    navigation_node::TYPE_COURSE,
                    $name ,
                    navigation_node::TYPE_COURSE . $course->id 
                );
            }
            
        }else {
            $newnode = $parentnode->add(
                $name ,
                new \moodle_url('/course/index.php', ['categoryid' => $subcategory->id]),
                navigation_node::TYPE_CATEGORY,
                $name,
                navigation_node::TYPE_CATEGORY . $subcategory->id 
            );
            $courses = $subcategory->get_courses();
            foreach ($courses as $course){
                $newnode->add(
                    $course->shortname,
                    new \moodle_url('/course/view.php', ['id' => $course->id]),
                    navigation_node::TYPE_COURSE,
                    $course->shortname,
                    navigation_node::TYPE_COURSE . $course->id 
                );
            }
            $subsubcategories = $subcategory->get_children();
            foreach ($subsubcategories as $subsubcategory){
                $newnode->add(
                    $subsubcategory->name,
                    new \moodle_url('/course/index.php', ['categoryid' => $subsubcategory->id]),
                    navigation_node::TYPE_CATEGORY,
                    $subsubcategory->name,
                    navigation_node::TYPE_CATEGORY .$subsubcategory->id 
                );
            }
            if(count($courses) > 1){
                $newnode->showchildreninsubmenu = true;
            }
        }
    }
}

function theme_apoa_extend_navigation_category_settings(navigation_node $parentnode, context_coursecat $context) {
    global $USER, $PAGE;
    
    $category = core_course_category::get($context->instanceid);
    $parents = preg_split('@/@', $category->path, -1, PREG_SPLIT_NO_EMPTY);

    $subrootcategory = core_course_category::get($parents[1]);
    $subcategories = $subrootcategory->get_children();
    $elibraryid = get_config('theme_apoa', 'elibraryid');#

    if(!has_capability('moodle/course:update', $context)) {
        $parentnode->children = new navigation_node_collection;
    }
    else{
        $elibraryid = get_config('theme_apoa', 'elibraryid');
        if ($subrootcategory->id == $elibraryid){
            if($category->depth == 3){
            $parentnode->add(
                'Journal Settings' ,
                new \moodle_url('/theme/apoa/editelibrary.php', ['id' => $category->id]),
                navigation_node::TYPE_COURSE,
                'Journal Settings' ,
                navigation_node::TYPE_COURSE . 0 
            );
            }
        }
    }
    if ($subrootcategory->id == $elibraryid){
        $parentnode->add(
            'Journal Clubs' ,
            new \moodle_url('/local/journalclub/index.php', ['id' => $category->id]),
            navigation_node::TYPE_COURSE,
            'Journal Clubs' ,
            navigation_node::TYPE_COURSE . 'jc'
        );
        $parentnode->add(
            'Search' ,
            new \moodle_url('/local/journalclub/search.php', ['category' => $category->id]),
            navigation_node::TYPE_CATEGORY,
            'Search' ,
            navigation_node::TYPE_CATEGORY . 'js'
        );
    }
    $category = core_course_category::get($context->instanceid);
 
    $PAGE->set_primary_active_tab(navigation_node::TYPE_CATEGORY. $subrootcategory->id);
    $PAGE->set_secondary_active_tab(navigation_node::TYPE_CATEGORY. $parents[2]);
    $component = 'theme_apoa';

    theme_apoa_get_secondary_nav_items($parentnode, $subcategories, $component);
}

function theme_apoa_extend_navigation_course(navigation_node $parentnode, stdClass $course, context_course $context) {
    global $USER, $PAGE;
    
    if(!has_capability('moodle/course:update', $context)) {
        $parentnode->children = new navigation_node_collection;
    }
    $category = core_course_category::get($course->category);
    $rootcat = get_subroot_category($category);

    $parents = preg_split('@/@', $category->path, -1, PREG_SPLIT_NO_EMPTY);

    $PAGE->set_primary_active_tab(navigation_node::TYPE_CATEGORY. $rootcat->id);
    $category->depth > 3 ? $PAGE->set_secondary_active_tab(navigation_node::TYPE_CATEGORY. $parents[2]) : $PAGE->set_secondary_active_tab(navigation_node::TYPE_COURSE. $course->id);
    $subcategories = $rootcat->get_children();
    $component = 'theme_apoa';

    theme_apoa_get_secondary_nav_items($parentnode, $subcategories, $component);
    
}

function theme_apoa_extend_navigation(global_navigation $nav) {
    global $CFG, $DB;
    $topchildren = core_course_category::top()->get_children();
    if (empty($topchildren)) {
        throw new moodle_exception('cannotviewcategory', 'error');
    }
    $category = reset($topchildren);
    $myurl = "Sections|/course/index\n";
    foreach ($topchildren as $category) {
        $id = $category->id;
        $name = $category->name;
        $myurl .= "-{$name}|/course/index?categoryid={$id}\n";
    }
    $CFG->custommenuitems = "";
};


function theme_apoa_get_file_from_setting($settingname) {

    $component = 'theme_apoa';
    $filename = get_config($component, $settingname);

    $fs = get_file_storage();
    $syscontext = context_system::instance();
    $files = $fs->get_area_files($syscontext->id, $component, $settingname);
    
    foreach ($files as $file){
        if (is_valid_video($file)){
            $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(),
            $file->get_filepath(), $file->get_filename(), false);
        }else if ($file->is_valid_image()) {
            $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(),
            $file->get_filepath(), $file->get_filename(), false);
        }
    }

    return $url;
}



function is_valid_video(\stored_file $file) {
    $mimetype = $file->get_mimetype();
    if (!file_mimetype_in_typegroup($mimetype, 'web_video')) {
        return false;
    }
        // ok, GD likes this image
    return true;
}

function get_category_for_mainpage() {
    return core_course_category::top();
}

function get_courses_for_mainpage(string $criteria) {

    return get_courses_for_course_list($criteria);

}

function get_category_for_newsletter() {
    $settingname = 'newsletterid';
    $categoryid = get_config('theme_apoa', $settingname);
    $category = core_course_category::get($categoryid);
    return $category;
}

function get_courses_for_newsletter(string $criteria) {

    $settingname = 'newsletterid';
    $categoryid = get_config('theme_apoa', $settingname);
    $category = core_course_category::get($categoryid);
    $options = array('recursive' => 1, 'limit' => 3);
    $courses = $category->get_courses($options);
    return $courses;

}

function get_category_for_course_list() {
    $settingname = 'elibraryid';
    $categoryid = get_config('theme_apoa', $settingname);
    $category = core_course_category::get($categoryid);
    return $category;
}

function get_courses_for_course_list(string $criteria) {
    $tags = \theme_apoa_tag_tag::guess_by_name($criteria);
    $courses = [];
    if ($tags) {
        $tag = reset($tags);
        $rawcourses = $tag->get_tagged_items('core', 'course', '0', '3', '', 'timecreated DESC');
        foreach ($rawcourses as $rawcourse){
            $courses[$rawcourse->id] = new \core_course_list_element($rawcourse);
        }
    }
    return $courses;
}


function get_category_for_elibrary() {
    $settingname = 'elibraryid';
    $categoryid = get_config('theme_apoa', $settingname);
    $category = core_course_category::get($categoryid);
    return $category;
}

function get_courses_for_elibrary() {
    $settingname = 'elibraryid';
    $categoryid = get_config('theme_apoa', $settingname);
    $category = core_course_category::get($categoryid);
    $options = array('recursive' => 1, 'limit' => 3, 'summary' => 1);
    $courses = $category->get_courses($options);
    return $courses;
}

function get_subroot_category(\core_course_category $category) {

    $generation = 1;
    if ($category->depth <= 1) {
        return $category;
    }
    else {
        $parents = preg_split('@/@', $category->path, -1, PREG_SPLIT_NO_EMPTY);
        $rootcategory = \core_course_category::get($category->depth - $generation <= 0 ? reset($parents) : $parents[1]);
        return $rootcategory;
    }
}

function get_parent_category_by_generation(\core_course_category $category, int $generation) {

    if ($category->depth <= 1) {
        return $category;
    }
    else {
        $parents = preg_split('@/@', $category->path, -1, PREG_SPLIT_NO_EMPTY);
        $rootcategory = \core_course_category::get($category->depth - $generation <= 0 ? reset($parents) : $parents[$category->depth - $generation]);
        return $rootcategory;
    }
}

function get_category_path(\core_course_category $category) {

    if ($category->depth <= 1) {
        return [$category->id];
    }
    else {
        $parents = preg_split('@/@', $category->path, -1, PREG_SPLIT_NO_EMPTY);
        return $parents;
    }
}
    
function get_journal_link($categoryid){
    global $DB;

    if($journalhostandpath =  $DB->get_record('theme_apoa_journals', array('category' => $categoryid))){
        $journallink = $journalhostandpath->url . $journalhostandpath->path;     
        return $journallink;     
    }
    return false;
}