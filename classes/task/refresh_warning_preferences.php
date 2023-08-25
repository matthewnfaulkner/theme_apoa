<?php

namespace theme_apoa\task;


/**
 * An example of a scheduled task.
 */
class refresh_warning_preferences extends \core\task\scheduled_task {

    use \core\task\logging_trait;

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('refresh_warning_preferences', 'theme_apoa');
    }

    /**
     * Execute the task.
     */
    public function execute() {
        // Call your own api
        global $DB;

        $cutoff = time() - 604800000;
        $this->log_start("Deleting old preferences");

        $select = 'name = ? AND value < ?';
        $DB->delete_records_select("user_preferences", $select, ['theme_apoa_user_nosub', $cutoff]);

        $DB->delete_records_select("user_preferences", $select, ['theme_apoa_user_notapproved', $cutoff]);

        $this->log_finish("Finished Deleting old preferences");
    }

}