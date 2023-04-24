<?php

namespace theme_apoa\external\exporters;

use core\external\exporter;
use renderer_base;
use moodle_url;

class favourite extends exporter {


    /**
     * Return the list of properties.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'id' => [
                'type' => PARAM_INT
            ],
            'username' => [
                'type' => PARAM_ALPHANUMEXT
            ],
            'description' => [
                'type' => PARAM_RAW,
            ],
            'descriptionformat' => [
                'type' => PARAM_INT,
            ],
            'favourited' => [
                'type' => PARAM_BOOL,
            ],
            'courseid' => [
                'type' => PARAM_INT,
            ],
        ];
    }

    /**
     * Return the list of additional properties.

     * @return array
     */
    protected static function define_other_properties() {
        return [
            'profileurl' => [
                'type' => PARAM_URL
            ]
        ];
    }

    /**
     * Returns a list of objects that are related.
     *
     * @return array
     */
    protected static function define_related() {
        return [
            'context' => 'context',
        ];
    }

    /**
     * Get the formatting parameters for the description.
     *
     * @return array
     */
    protected function get_format_parameters_for_description() {
        return [
            'component' => 'core_user',
            'filearea' => 'description',
            'itemid' => $this->data->id
        ];
    }

    /**
     * Get the additional values to inject while exporting.
     *
     * @param renderer_base $output The renderer.
     * @return array Keys are the property names, values are their values.
     */
    protected function get_other_values(renderer_base $output) {
        $statuses = [];

        $profileurl = new moodle_url('/user/profile.php', ['id' => $this->data->id]);

        return [
            'profileurl' => $profileurl->out(false),
        ];
    }
}