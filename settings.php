<?php

// Every file should have GPL and copyright in the header - we skip it in tutorials but you should not skip it for real.

// This line protects the file from being accessed by a URL directly.                                                               

use core_adminpresets\local\setting\adminpresets_admin_setting_configmultiselect;
use core_adminpresets\local\setting\adminpresets_admin_setting_configmultiselect_with_loader;

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
                         
    $setting = new admin_setting_configtextarea('theme_apoa/logininstructions',                                                              
        get_string('logininstructions', 'theme_apoa'), get_string('logininstructions_desc', 'theme_apoa'), '', PARAM_TEXT);                      
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
    $setting = new admin_setting_configtextarea('theme_apoa/mainpagenotification',                                                              
        get_string('mainpagenotification', 'theme_apoa'), get_string('mainpagenotification_desc', 'theme_apoa'), '', PARAM_RAW);                      
    $setting->set_updatedcallback('theme_reset_all_caches');                                                                        
    $page->add($setting);   

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
    
    $setting = new admin_setting_configtext('theme_apoa/jumbolink',                                                              
        get_string('jumbolink', 'theme_apoa'), get_string('jumbolink_desc', 'theme_apoa'), '', PARAM_TEXT);                      
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

    $setting = new admin_setting_configstoredfile('theme_apoa/jumbobannerposter', get_string('jumbobannerposter', 'theme_apoa'),
        get_string('jumbobannerposter_desc', 'theme_apoa'), 'jumbobannerposter', 0,
            array('maxfiles' => 1, 'accepted_types' => array('.jpg', '.png')));
    $setting->set_updatedcallback('theme_reset_all_caches');                                                                        
    $page->add($setting); 

    $setting = new admin_setting_configstoredfile('theme_apoa/jumbobannerlogo', get_string('jumbobannerlogo', 'theme_apoa'),
        get_string('jumbobannerlogo_desc', 'theme_apoa'), 'jumbobannerlogo', 0,
            array('maxfiles' => 1, 'accepted_types' => array('.jpg', '.png')));
    $setting->set_updatedcallback('theme_reset_all_caches');                                                                        
    $page->add($setting); 

    $setting = new admin_setting_configtext('theme_apoa/jumboannouncementsid', get_string('jumboid', 'theme_apoa'),                           
        get_string('jumboid_desc', 'theme_apoa'), '', PARAM_INT);                                                                  
    $setting->set_updatedcallback('theme_reset_all_caches');                                                                        
    $page->add($setting);

    $settings->add($page);
    
    $page = new admin_settingpage('theme_apoa_primary_navigation', get_string('navigation', 'theme_apoa'));  
    
    $setting = new admin_setting_configtext('theme_apoa/primarynavcount', get_string('primarynavcount', 'theme_apoa'),
                            get_string('primarynavcount_desc', 'theme_apoa'), 8, PARAM_INT);
    $setting->set_updatedcallback('theme_reset_all_caches');                                                                        
    $page->add($setting);           
    
    $primarynavcount = get_config('theme_apoa', 'primarynavcount');
    for ($i = 1; $i <= $primarynavcount; $i++) {
        $setting = new admin_setting_configselect_with_advanced('theme_apoa/primarynavitems' . $i, get_string('primarynavitems', 'theme_apoa' , $i),
                                        get_string('primarynavitems_desc', 'theme_apoa'), [], core_course_category::make_categories_list());

        $setting->set_updatedcallback('theme_reset_all_caches');                                                                        
        $page->add($setting); 
    }

    $settings->add($page);

    // Advanced settings.                                                                                                           
    $page = new admin_settingpage('theme_apoa_resources', get_string('mainpageresources', 'theme_apoa'));    


    $setting = new admin_setting_configstoredfile('theme_apoa/resourcesforum', get_string('resources', 'theme_apoa'),
        get_string('jumbobannerlogo_desc', 'theme_apoa'), 'resources', 0,
            array('maxfiles' => 1, 'accepted_types' => array('.jpg', '.png')));
    $setting->set_updatedcallback('theme_reset_all_caches');   
                                                                                 
    $page->add($setting); 

    $setting = new admin_setting_configtext('theme_apoa/resourcesforumlink', get_string('resources', 'theme_apoa'),
        get_string('jumbobannerlogo_desc', 'theme_apoa'), '', PARAM_URL);
    $setting->set_updatedcallback('theme_reset_all_caches');   
                                                                                 
    $page->add($setting); 

    $setting = new admin_setting_configstoredfile('theme_apoa/resourcesnewsletter', get_string('resources', 'theme_apoa'),
        get_string('jumbobannerlogo_desc', 'theme_apoa'), 'resources', 0,
            array('maxfiles' => 1, 'accepted_types' => array('.jpg', '.png')));
    $setting->set_updatedcallback('theme_reset_all_caches');   
                                                                                 
    $page->add($setting); 

    $setting = new admin_setting_configtext('theme_apoa/resourcesnewsletterlink', get_string('resources', 'theme_apoa'),
        get_string('jumbobannerlogo_desc', 'theme_apoa'), '', PARAM_URL);
    $setting->set_updatedcallback('theme_reset_all_caches');   
                                                                                 
    $page->add($setting); 

    $setting = new admin_setting_configstoredfile('theme_apoa/resourcesmembership', get_string('resources', 'theme_apoa'),
        get_string('jumbobannerlogo_desc', 'theme_apoa'), 'resources', 0,
            array('maxfiles' => 1, 'accepted_types' => array('.jpg', '.png')));
    $setting->set_updatedcallback('theme_reset_all_caches');   
                                                                                 
    $page->add($setting); 

    $setting = new admin_setting_configtext('theme_apoa/resourcesmembershiplink', get_string('resources', 'theme_apoa'),
        get_string('jumbobannerlogo_desc', 'theme_apoa'), '', PARAM_URL);
    $setting->set_updatedcallback('theme_reset_all_caches');   
                                                                                 
    $page->add($setting); 

    $setting = new admin_setting_configstoredfile('theme_apoa/resourcesgallery', get_string('resources', 'theme_apoa'),
        get_string('jumbobannerlogo_desc', 'theme_apoa'), 'resources', 0,
            array('maxfiles' => 1, 'accepted_types' => array('.jpg', '.png')));
    $setting->set_updatedcallback('theme_reset_all_caches');   
                                                                                 
    $page->add($setting); 

    $setting = new admin_setting_configtext('theme_apoa/resourcesgallerylink', get_string('resources', 'theme_apoa'),
        get_string('jumbobannerlogo_desc', 'theme_apoa'), '', PARAM_URL);
    $setting->set_updatedcallback('theme_reset_all_caches');   
                                                                                 
    $page->add($setting); 

    $setting = new admin_setting_configstoredfile('theme_apoa/resourcescontact', get_string('resources', 'theme_apoa'),
        get_string('jumbobannerlogo_desc', 'theme_apoa'), 'resources', 0,
            array('maxfiles' => 1, 'accepted_types' => array('.jpg', '.png')));
    $setting->set_updatedcallback('theme_reset_all_caches');   
                                                                                 
    $page->add($setting); 

    $setting = new admin_setting_configtext('theme_apoa/resourcescontactlink', get_string('resources', 'theme_apoa'),
        get_string('jumbobannerlogo_desc', 'theme_apoa'), '', PARAM_URL);
    $setting->set_updatedcallback('theme_reset_all_caches');   
                                                                                 
    $page->add($setting); 
    
    $setting = new admin_setting_configstoredfile('theme_apoa/resourcesmeetings', get_string('resources', 'theme_apoa'),
        get_string('jumbobannerlogo_desc', 'theme_apoa'), 'resources', 0,
            array('maxfiles' => 1, 'accepted_types' => array('.jpg', '.png')));
    $setting->set_updatedcallback('theme_reset_all_caches');   
                                                                                 
    $page->add($setting); 

    $setting = new admin_setting_configtext('theme_apoa/resourcesmeetingslink', get_string('resources', 'theme_apoa'),
        get_string('jumbobannerlogo_desc', 'theme_apoa'), '', PARAM_URL);
    $setting->set_updatedcallback('theme_reset_all_caches');   
                                                                                 
    $page->add($setting); 

    $setting = new admin_setting_configstoredfile('theme_apoa/resourcesblog', get_string('resources', 'theme_apoa'),
        get_string('jumbobannerlogo_desc', 'theme_apoa'), 'resources', 0,
            array('maxfiles' => 1, 'accepted_types' => array('.jpg', '.png')));
    $setting->set_updatedcallback('theme_reset_all_caches');   
                                                                                 
    $page->add($setting); 

    $setting = new admin_setting_configtext('theme_apoa/resourcesbloglink', get_string('resources', 'theme_apoa'),
        get_string('jumbobannerlogo_desc', 'theme_apoa'), '', PARAM_URL);
    $setting->set_updatedcallback('theme_reset_all_caches');   
                                                                                 
    $page->add($setting); 

    $setting = new admin_setting_configstoredfile('theme_apoa/about', get_string('about', 'theme_apoa'),
        get_string('jumbobannerlogo_desc', 'theme_apoa'), 'about', 0,
            array('maxfiles' => 1, 'accepted_types' => array('.jpg', '.png')));
    $setting->set_updatedcallback('theme_reset_all_caches');   
                                                                         
    $page->add($setting); 

    $settings->add($page);       

    // Advanced settings.                                                                                                           
    $page = new admin_settingpage('theme_apoa_sections', get_string('sectionsettings', 'theme_apoa'));

    if($sectionsid = get_config('theme_apoa', 'Sectionsid')) {
        $sectioncat = core_course_category::get($sectionsid);
        $onebelowcategories = $sectioncat->get_children();
        
        foreach ($onebelowcategories as $category) {
            $setting = new admin_setting_configstoredfile('theme_apoa/sectionlogo' . $category->id, $category->name,
                get_string('sectionlogo_desc', 'theme_apoa'), 'sectionlogo' . $category->id, 0,
                    array('maxfiles' => 1, 'accepted_types' => array('.jpg', '.png')));
            $setting->set_updatedcallback('theme_reset_all_caches');                                                                        
            $page->add($setting);      
            $setting = new admin_setting_configtext('theme_apoa/sectionlink' . $category->id, get_string('sectionlink', 'theme_apoa', $category->name),
                get_string('sectionlink_desc', 'theme_apoa', $category->name), '', PARAM_URL);
            $page->add($setting);                                                                                                                         

        }
    }
    $settings->add($page);

    // Advanced settings.                                                                                                           
    $page = new admin_settingpage('theme_apoa_categories', get_string('categorysettings', 'theme_apoa'));
    
    $setting = new admin_setting_configtext('theme_apoa/APOAid', get_string('APOAid', 'theme_apoa'),                           
        get_string('APOAid_desc', 'theme_apoa'), '', PARAM_INT);                                                                  
    $setting->set_updatedcallback('theme_reset_all_caches');                                                                        
    $page->add($setting);  

    $setting = new admin_setting_configtext('theme_apoa/Sectionsid', get_string('Sectionsid', 'theme_apoa'),                           
        get_string('Sectionsid_desc', 'theme_apoa'), '', PARAM_INT);                                                                  
    $setting->set_updatedcallback('theme_reset_all_caches');                                                                        
    $page->add($setting); 

    if ($apoaid = get_config('theme_apoa', 'APOAid')) {

        $apoacat = core_course_category::get($apoaid);
        $onebelowcategories = $apoacat->get_children();

        foreach ($onebelowcategories as $category) {
            $nospacename = strtolower(preg_replace("/[^a-zA-Z0-9]+/", "", $category->name));
            
            $setting = new admin_setting_configtext('theme_apoa/'. $nospacename .'id', $category->name,
                '', $category->id, PARAM_INT);

            $page->add($setting);                                                                                                                               

        }
    }
    $settings->add($page); 
 

    // Advanced settings.                                                                                                           
    $page = new admin_settingpage('theme_apoa_footer', get_string('footersettings', 'theme_apoa'));

    $setting = new admin_setting_configtextarea('theme_apoa/footercontact', get_string('footercontact', 'theme_apoa'),                           
        get_string('footercontact_desc', 'theme_apoa'), '', PARAM_TEXT);                                                                  
    $setting->set_updatedcallback('theme_reset_all_caches');                                                                        
    $page->add($setting);  

    $setting = new admin_setting_configtextarea('theme_apoa/footerquicklinks', get_string('footercontact', 'theme_apoa'),                           
        get_string('footercontact_desc', 'theme_apoa'), '', PARAM_TEXT);                                                                  
    $setting->set_updatedcallback('theme_reset_all_caches');                                                                        
    $page->add($setting);  

    $settings->add($page); 
}