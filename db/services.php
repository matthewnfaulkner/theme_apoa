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
    )
);