<?php


$functions = array(

    'theme_apoa_toggle_favourite_state' => array(
        'classname' => 'theme_apoa_external',
        'methodname' => 'toggle_favourite_state',
        'classpath' => 'theme/apoa/externallib.php',
        'description' => 'toggles the favourite state of a course.',
        'type' => 'write',
        'ajax' => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),

    'theme_apoa_cache_closed_modal' => array(
        'classname' => 'theme_apoa_external',
        'methodname' => 'cache_closed_modal',
        'classpath' => 'theme/apoa/externallib.php',
        'description' => 'keeps modal from popping up during the session',
        'type' => 'read',
        'loginrequired' => false,
        'ajax' => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),
    'theme_apoa_get_jumbo_config' => array(
        'classname' => 'theme_apoa_external',
        'methodname' => 'get_jumbo_config',
        'classpath' => 'theme/apoa/externallib.php',
        'description' => 'sends jumbo config to mobile users',
        'type' => 'read',
        'ajax' => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    )
);