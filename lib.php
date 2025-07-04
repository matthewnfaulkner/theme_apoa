<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 *  Theme functions.
 *
 * @package     theme_apoa
 * @copyright   2025 Matthew Faulkner matthewfaulkner@apoaevents.com
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */                                                             


defined('MOODLE_INTERNAL') || die();


/**
 * get scss content from various sources
 *
 * @param stdClass $theme object of current theme.
 * @return string fetched scss concatenated into a string.
 */
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
    return $pre . "\n" . $scss . "\n" . $post . "\n" . $booststudio. "\n"  . $swiper;                                                                                                                  
}

/**
 * Parse pre scss from theme settings.
 *
 * @param stdClass $theme object representing current theme.
 * @return string settings parsed into valid scss string
 */
function theme_apoa_get_pre_scss($theme) {
    // Load the settings from the parent.                                                                                           
    $theme = theme_config::load('boost');                                                                                           
    // Call the parent themes get_pre_scss function.  
    if($jumbobgcolor = get_config('theme_apoa', 'jumbobgcolor')){
         $scss = '$jumbobgcolor: '. $jumbobgcolor . '!default;';
    }
   
    return $scss .= theme_boost_get_pre_scss($theme);                         
}

/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */

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
    preg_replace('/[0-9]+/', '', $filearea) === 'sectionlogo' || $filearea === 'resources' || $filearea === 'jumbobannerposter' || $filearea === 'jumbovideo' ||
    $filearea === 'about' || $filearea == 'mainmodalbg' || $filearea == 'mainmodalbgmobile'){
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

/**
 * Limit names of links added to navigation to 14 characters
 *
 * @param string $label label of link
 * @return string shortened label
 */
function theme_apoa_format_name_for_navigation(string $label) {

    $shortlabel= strlen($label) > 14 ? substr_replace($label, '...', 14) : $label;
    
    return $shortlabel;
}

/**
 * Construct secondary navigation based on current category
 *
 * @param navigation_node $parentnode
 * @param core_course_category $category
 * @param string $component name of the component
 * @return void
 */

function theme_apoa_get_secondary_nav_items(navigation_node $parentnode, core_course_category $category, string $component) {

    $courses = $category->get_courses(array('recursive' => false, 'limit' => 25));

    foreach($courses as $course){
        $parentnode->add(
            theme_apoa_format_name_for_navigation($course->get_formatted_fullname()) ,
            new \moodle_url('/course/view.php', ['id' => $course->id]),
            navigation_node::TYPE_COURSE,
            $course->shortname,
            navigation_node::TYPE_COURSE . $course->id 
        );
    }
    $subcategories = $category->get_children();

    foreach ($subcategories as $subcategory) {

        $nospacename = preg_replace("/[^a-zA-Z]+/", "", $subcategory->name);
        $name  = strpos(get_string($nospacename, $component), '[') ?  get_string($nospacename, $component) : $subcategory->name;
        if ($coursecount = $subcategory->get_courses_count() == 1 && $subcategory->get_children_count() == 0){

            if($courses = $subcategory->get_courses(array('limit' => 1))) {
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
            if(count($subsubcategories) > 1){
                $newnode->showchildreninsubmenu = true;
            }
        }
    }
}


/**
 * Extend course category navigation with additional nodes
 *
 * @param navigation_node $parentnode navigation node to attach new nodes to.
 * @param \context_coursecat $context context object for category.
 * @return void
 */

function theme_apoa_extend_navigation_category_settings(navigation_node $parentnode, context_coursecat $context) {
    global $USER, $PAGE;
    
    if($PAGE->theme->name !== 'apoa'){
        return;
    }

    $category = core_course_category::get($context->instanceid);
    $parents = preg_split('@/@', $category->path, -1, PREG_SPLIT_NO_EMPTY);

    $subrootcategory = core_course_category::get($parents[1]);


    $apoacatnav = $parentnode->add('Apoacatnav',
    null,
    navigation_node::TYPE_CONTAINER,
    'Apoacatnav',
    'apoacatnav');

    $elibraryid = get_config('theme_apoa', 'elibraryid');#

    if(has_capability('moodle/course:update', $context)) {
        $elibraryid = get_config('theme_apoa', 'elibraryid');
        if ($subrootcategory->id == $elibraryid){
            if($category->depth == 3){
            $apoacatnav->add(
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
        $apoacatnav->add(
            'Journal Clubs' ,
            new \moodle_url('/local/journalclub/index.php', ['id' => $category->id]),
            navigation_node::TYPE_COURSE,
            'Journal Clubs' ,
            navigation_node::TYPE_COURSE . 'jc'
        );
        $apoacatnav->add(
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

    theme_apoa_get_secondary_nav_items($apoacatnav, $subrootcategory, $component);
}

/**
 * Extend Course navigation with additional navigation nodes.
 *
 * @param navigation_node $parentnode navigation node to attach new nodes to.
 * @param stdClass $course course object
 * @param context_course $context context course object
 * @return void
 */
function theme_apoa_extend_navigation_course(navigation_node $parentnode, stdClass $course, context_course $context) {
    global $PAGE;

    if($PAGE->theme->name !== 'apoa'){
        return;
    }
    
    $apoanav = $parentnode->add('Apoanav',
    null,
    navigation_node::TYPE_CONTAINER,
    'Apoanav',
    'apoanav');

    $category = core_course_category::get($course->category);
    $rootcat = get_subroot_category($category);

    $parents = preg_split('@/@', $category->path, -1, PREG_SPLIT_NO_EMPTY);
    
    $PAGE->set_primary_active_tab(navigation_node::TYPE_CATEGORY. $rootcat->id);

    if($rootcat->id == $category->id || $category->depth <= 3){
        $PAGE->set_secondary_active_tab(navigation_node::TYPE_COURSE. $course->id);
    }
    else{
        $PAGE->set_secondary_active_tab(navigation_node::TYPE_CATEGORY. $parents[2]);
    }
    $component = 'theme_apoa';

    theme_apoa_get_secondary_nav_items($apoanav, $rootcat, $component);
    
}

/**
 * Given a setting name, retrieves file from setting if setting exists in theme.
 *
 * @param string $settingname name of the setting to retrieve from
 * @return string|null url of the retrieved file or null if not found
 */
function theme_apoa_get_file_from_setting($settingname) {

    $component = 'theme_apoa';

    $fs = get_file_storage();
    $syscontext = context_system::instance();
    $files = $fs->get_area_files($syscontext->id, $component, $settingname);
    $url = '';
    foreach ($files as $file){
        if (is_valid_video($file)){
            $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(),
            $file->get_filepath(), $file->get_filename(), false)->out();
        }else if ($file->is_valid_image()) {
            $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(),
            $file->get_filepath(), $file->get_filename(), false)->out();
        }
    }


    return $url;
}


/**
 * Returns if file is a valid video file
 *
 * @param \stored_file $file the file to check
 * @return boolean true if file is a video
 */
function is_valid_video(\stored_file $file) {
    $mimetype = $file->get_mimetype();
    if (!file_mimetype_in_typegroup($mimetype, 'web_video')) {
        return false;
    }
        // ok, GD likes this image
    return true;
}

/**
 * Returns courses with tag that matches criteria
 *
 * @param string $criteria
 * @return array[core_course_list_element] list of course list elements
 */
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

/**
 * Get parent category one below root of current category
 *
 * @param \core_course_category $category current category
 * @return \core_course_category one below root category
 */
function get_subroot_category(\core_course_category $category) {

    //depth of subroot
    $generation = 1;

    //already either root or subroot
    if ($category->depth <= 1) {
        return $category;
    }
    else {
        $parents = preg_split('@/@', $category->path, -1, PREG_SPLIT_NO_EMPTY);

        //get second value on path
        $rootcategory = \core_course_category::get($category->depth - $generation <= 0 ? reset($parents) : $parents[1]);
        return $rootcategory;
    }
}

/**
 * Undocumented function
 *
 * @param core_course_category $category category to find parent category of
 * @param integer $generation depth of parent to find
 * @return core_course_category parent category that matches depth
 */
function get_parent_category_by_generation(\core_course_category $category, int $generation) {

    //current category already at correct depth or higher.
    if ($category->depth <= $generation) {
        return $category;
    }
    else {
        $parents = preg_split('@/@', $category->path, -1, PREG_SPLIT_NO_EMPTY);
        //get value on path that matches generation
        $rootcategory = \core_course_category::get($category->depth - $generation <= 0 ? reset($parents) : $parents[$category->depth - $generation]);
        return $rootcategory;
    }
}

/**
 * Get url of journal
 *
 * @param int $categoryid id of category to look for link for
 * @return string|bool if found url of link, false otherwise
 */
function get_journal_link(int $categoryid){
    global $DB;

    if($journalhostandpath =  $DB->get_record('theme_apoa_journals', array('category' => $categoryid))){
        $journallink = $journalhostandpath->url . $journalhostandpath->path;     
        return $journallink;     
    }

    //no record
    return false;
}

/**
 * Define user preferences for theme
 *
 * @return array list of defined user preferences
 */
function theme_apoa_user_preferences(){
    return [
        //hide membership category not approved notification
        'theme_apoa_user_notapproved'=> [
                'type' =>   PARAM_INT,
                'null' => NULL_ALLOWED,
                'default' => 0,
                'permissioncallback' => [core_user::class, 'is_current_user'],
        ], 
        //hide not a subscriber notification
        'theme_apoa_user_nosub' => [
            'type' =>   PARAM_INT,
            'null' => NULL_ALLOWED,
            'default' => 0,
            'permissioncallback' => [core_user::class, 'is_current_user'],
            ], 
        ];
}
