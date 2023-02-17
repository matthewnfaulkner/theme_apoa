<?php

// Every file should have GPL and copyright in the header - we skip it in tutorials but you should not skip it for real.

// This line protects the file from being accessed by a URL directly.                                                               
defined('MOODLE_INTERNAL') || die();

// We will add callbacks here as we add features to our theme.


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
    return $pre . "\n" . $scss . "\n" . $post . "\n" . $booststudio;                                                                                                                  
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
    } else {
        send_file_not_found();
    }
}

function theme_apoa_extend_navigation_category_settings(navigation_node $parentnode, context_coursecat $context) {
    global $USER;
    if(!is_siteadmin($USER->id)) {
        $parentnode->children = new navigation_node_collection;
    }

    $home = $parentnode->add(
        get_string('home'),
        new \moodle_url('/course/index.php', ['categoryid' => $context->instanceid]),
        navigation_node::TYPE_CUSTOM,
        get_string('home'),
        get_string('home')
    );
    $home = $parentnode->add(
        get_string('leadership'),
        new \moodle_url('/course/index.php', ['categoryid' => $context->instanceid])
    );
    $home = $parentnode->add(
        get_string('about'),
        new \moodle_url('/course/index.php', ['categoryid' => $context->instanceid])
    );
    $home = $parentnode->add(
        get_string('forum'),
        new \moodle_url('/course/index.php', ['categoryid' => $context->instanceid])
    );
}   
