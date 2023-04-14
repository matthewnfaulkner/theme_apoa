<?php

// Every file should have GPL and copyright in the header - we skip it in tutorials but you should not skip it for real.

// This line protects the file from being accessed by a URL directly.                                                               
defined('MOODLE_INTERNAL') || die();                                                                                                
                                                                                                                                    
// This is used for performance, we don't need to know about these settings on every page in Moodle, only when                      
// we are looking at the admin settings pages.                                                                                      
if ($ADMIN->fulltree) {                                                                                                             
                                                                                                                                    
    // Boost provides a nice setting page which splits settings onto separate tabs. We want to use it here.                         
    $settings = new theme_boost_admin_settingspage_tabs('themesettingapoa', get_string('configtitle', 'theme_apoa'));             
                                                                                                                                    
    // Each page is a tab - the first is the "General" tab.                                                                         
    $page = new admin_settingpage('theme_apoa_general', get_string('generalsettings', 'theme_apoa'));                             
                                                                                                                                    
    // Replicate the preset setting from boost.                                                                                     
    $name = 'theme_apoa/preset';                                                                                                   
    $title = get_string('preset', 'theme_apoa');                                                                                   
    $description = get_string('preset_desc', 'theme_apoa');                                                                        
    $default = 'default.scss';                                                                                                      
                                                                                                                                    
    // We list files in our own file area to add to the drop down. We will provide our own function to                              
    // load all the presets from the correct paths.                                                                                 
    $context = context_system::instance();                                                                                          
    $fs = get_file_storage();                                                                                                       
    $files = $fs->get_area_files($context->id, 'theme_apoa', 'preset', 0, 'itemid, filepath, filename', false);                    
                                                                                                                                    
    $choices = [];                                                                                                                  
    foreach ($files as $file) {                                                                                                     
        $choices[$file->get_filename()] = $file->get_filename();                                                                    
    }                                                                                                                               
    // These are the built in presets from Boost.                                                                                   
    $choices['default.scss'] = 'default.scss';                                                                                      
    $choices['plain.scss'] = 'plain.scss';                                                                                          
                                                                                                                                    
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);                                     
    $setting->set_updatedcallback('theme_reset_all_caches');                                                                        
    $page->add($setting);                                                                                                           
                                                                                                                                    
    // Preset files setting.                                                                                                        
    $name = 'theme_apoa/presetfiles';                                                                                              
    $title = get_string('presetfiles','theme_apoa');                                                                               
    $description = get_string('presetfiles_desc', 'theme_poa');                                                                   
                                                                                                                                    
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'preset', 0,                                         
        array('maxfiles' => 20, 'accepted_types' => array('.scss')));                                                               
    $page->add($setting);     

    // Variable $brand-color.                                                                                                       
    // We use an empty default value because the default colour should come from the preset.                                        
    $name = 'theme_apoa/brandcolor';                                                                                               
    $title = get_string('brandcolor', 'theme_apoa');                                                                               
    $description = get_string('brandcolor_desc', 'theme_apoa');                                                                    
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');                                               
    $setting->set_updatedcallback('theme_reset_all_caches');                                                                        
    $page->add($setting);                                                                                                           
                                                                                                                                    
    // Must add the page after definiting all the settings!                                                                         
    $settings->add($page);                                                                                                          
                                                                                                                                    
    // Advanced settings.                                                                                                           
    $page = new admin_settingpage('theme_apoa_advanced', get_string('advancedsettings', 'theme_apoa'));                           
                                                                                                                                    
    // Raw SCSS to include before the content.                                                                                      
    $setting = new admin_setting_configtextarea('theme_apoa/scsspre',                                                              
        get_string('rawscsspre', 'theme_apoa'), get_string('rawscsspre_desc', 'theme_apoa'), '', PARAM_RAW);                      
    $setting->set_updatedcallback('theme_reset_all_caches');                                                                        
    $page->add($setting);                                                                                                           
                                                                                                                                    
    // Raw SCSS to include after the content.                                                                                       
    $setting = new admin_setting_configtextarea('theme_apoa/scss', get_string('rawscss', 'theme_apoa'),                           
        get_string('rawscss_desc', 'theme_apoa'), '', PARAM_RAW);                                                                  
    $setting->set_updatedcallback('theme_reset_all_caches');                                                                        
    $page->add($setting);                                                                                                           
                                                                                                                                    
    $settings->add($page); 
    
    // Advanced settings.                                                                                                           
    $page = new admin_settingpage('theme_apoa_mainpage', get_string('mainpagesettings', 'theme_apoa'));                           
                                                                                                                                    
    // Raw SCSS to include before the content.                                                                                      
    $setting = new admin_setting_configtext('theme_apoa/jumbotitle',                                                              
        get_string('jumbotitle', 'theme_apoa'), get_string('jumbotitle_desc', 'theme_apoa'), '', PARAM_RAW);                      
    $setting->set_updatedcallback('theme_reset_all_caches');                                                                        
    $page->add($setting);                                                                                                           
                                                                                                                                    
    // Raw SCSS to include after the content.                                                                                       
    $setting = new admin_setting_configtextarea('theme_apoa/jumbodescription', get_string('jumbodescription', 'theme_apoa'),                           
        get_string('jumbodescription_desc', 'theme_apoa'), '', PARAM_RAW);                                                                  
    $setting->set_updatedcallback('theme_reset_all_caches');                                                                        
    $page->add($setting);
    
    $setting = new admin_setting_configtext('theme_apoa/jumbotag', get_string('jumbotag', 'theme_apoa'),                           
        get_string('jumbotag_desc', 'theme_apoa'), '', PARAM_RAW);                                                                  
    $setting->set_updatedcallback('theme_reset_all_caches');                                                                        
    $page->add($setting);  

    $setting = new admin_setting_configtext('theme_apoa/jumboid', get_string('jumboid', 'theme_apoa'),                           
        get_string('jumboid_desc', 'theme_apoa'), '', PARAM_INT);                                                                  
    $setting->set_updatedcallback('theme_reset_all_caches');                                                                        
    $page->add($setting);  

    $setting = new admin_setting_configcheckbox('theme_apoa/jumbovideoflag', get_string('jumbovideoflag', 'theme_apoa'),
        get_string('jumboflag_desc', 'theme_apoa'), '');
    $setting->set_updatedcallback('theme_reset_all_caches');                                                                        
    $page->add($setting);  

    $setting = new admin_setting_configstoredfile('theme_apoa/jumbovideo', get_string('jumbovideo', 'theme_apoa'),
        get_string('jumbovideo_desc', 'theme_apoa'), 'jumbovideo', 0,
            array('maxfiles' => 1, 'accepted_types' => array('.mp4')));
    $setting->set_updatedcallback('theme_reset_all_caches');                                                                        
    $page->add($setting);  

    $setting = new admin_setting_configstoredfile('theme_apoa/jumbobanner', get_string('jumbobanner', 'theme_apoa'),
        get_string('jumbobanner_desc', 'theme_apoa'), 'jumbobanner', 0,
            array('maxfiles' => 1, 'accepted_types' => array('.jpg', '.png', '.mp4', '.webm')));
    $setting->set_updatedcallback('theme_reset_all_caches');                                                                        
    $page->add($setting); 

    $setting = new admin_setting_configstoredfile('theme_apoa/jumbobannerlogo', get_string('jumbobannerlogo', 'theme_apoa'),
        get_string('jumbobannerlogo_desc', 'theme_apoa'), 'jumbobannerlogo', 0,
            array('maxfiles' => 1, 'accepted_types' => array('.jpg', '.png')));
    $setting->set_updatedcallback('theme_reset_all_caches');                                                                        
    $page->add($setting); 

    $settings->add($page);       

    // Advanced settings.                                                                                                           
    $page = new admin_settingpage('theme_apoa_sections', get_string('sectionssettings', 'theme_apoa'));

    $top = core_course_category::top();
    $onebelowcategories = $top->get_children();

    foreach ($onebelowcategories as $category) {
        $setting = new admin_setting_configstoredfile('theme_apoa/sectionlogo' . $category->id, $category->name,
            get_string('sectionlogo_desc', 'theme_apoa'), 'sectionlogo' . $category->id, 0,
                array('maxfiles' => 1, 'accepted_types' => array('.jpg', '.png')));
        $setting->set_updatedcallback('theme_reset_all_caches');                                                                        
        $page->add($setting);                                                                                                                               

    }

    $settings->add($page);

    // Advanced settings.                                                                                                           
    $page = new admin_settingpage('theme_apoa_categories', get_string('categorysettings', 'theme_apoa'));

    $setting = new admin_setting_configtext('theme_apoa/elibraryid', get_string('elibraryid', 'theme_apoa'),                           
        get_string('elibraryid_desc', 'theme_apoa'), '', PARAM_INT);                                                                  
    $setting->set_updatedcallback('theme_reset_all_caches');                                                                        
    $page->add($setting);  

    $setting = new admin_setting_configtext('theme_apoa/newsletterid', get_string('elibraryid', 'theme_apoa'),                           
        get_string('elibraryid_desc', 'theme_apoa'), '', PARAM_INT);                                                                  
    $setting->set_updatedcallback('theme_reset_all_caches');                                                                        
    $page->add($setting);  

    $settings->add($page);       
}