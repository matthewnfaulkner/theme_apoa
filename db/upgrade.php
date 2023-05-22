<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file keeps track of upgrades to the feedback module.
 *
 * Sometimes, changes between versions involve
 * alterations to database structures and other
 * major things that may break installations.
 *
 * The upgrade function in this file will attempt
 * to perform all the necessary actions to upgrade
 * your older installation to the current version.
 *
 * If there's something it cannot do itself, it
 * will tell you what you need to do.
 *
 * The commands in here will all be database-neutral,
 * using the methods of database_manager class
 *
 * Please do not forget to use upgrade_set_timeout()
 * before any action that may take longer time to finish.
 *
 * @package   mod_feedback
 * @copyright Andreas Grabs
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_theme_apoa_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();
    
    // Automatically generated Moodle v3.6.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.7.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.8.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.9.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.0.0 release upgrade line.
    // Put any upgrade step following this.
    if ($oldversion < 20230208017) {

        // Define field id to be added to journals.
        $table = new xmldb_table('theme_apoa_journals');
        $field = new xmldb_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);

        // Conditionally launch add field id.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'id');

        // Conditionally launch add field name.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('address', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'name');

        // Conditionally launch add field address.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('category', XMLDB_TYPE_INTEGER, '4', null, null, null, null, 'address');

        // Conditionally launch add field category.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Apoa savepoint reached.
        upgrade_plugin_savepoint(true, 20230208017, 'theme', 'apoa');
    }

    if ($oldversion < 20230208022) {

        // Rename field url on table journals to NEWNAMEGOESHERE.
        $table = new xmldb_table('theme_apoa_journals');
        $field = new xmldb_field('url', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'name');

        // Launch rename field url.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

         // Define field path to be added to journals.

    
         $field = new xmldb_field('path', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'category');
 
         // Conditionally launch add field path.
         if (!$dbman->field_exists($table, $field)) {
             $dbman->add_field($table, $field);
         }

        // Apoa savepoint reached.
        upgrade_plugin_savepoint(true, 20230208022, 'theme', 'apoa');
    }

    if ($oldversion < 20230208023) {

        // Define key category (unique) to be added to journals.
        $table = new xmldb_table('theme_apoa_journals');
        $key = new xmldb_key('category', XMLDB_KEY_UNIQUE, ['category']);

        // Launch add key category.
        $dbman->add_key($table, $key);

        // Apoa savepoint reached.
        upgrade_plugin_savepoint(true, 20230208023, 'theme', 'apoa');
    }


    return true;
}
