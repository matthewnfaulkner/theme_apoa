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
 * Page for creating or editing course category name/parent/description.
 *
 * When called with an id parameter, edits the category with that id.
 * Otherwise it creates a new category with default parent from the parent
 * parameter, which may be 0.
 *
 * @package    core_course
 * @copyright  2007 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/theme/apoa/lib.php');

use \theme_apoa\form\editelibrary_form as editelibrary_form;
require_login();

$id = optional_param('id', 0, PARAM_INT);

$journalstable = 'theme_apoa_journals';
$viewurl = new moodle_url('/course/index.php?');
$manageurl = new moodle_url('/theme/apoa/editelibrary.php?');
$url = new moodle_url('/course/editcategory.php');
if ($id) {
    $coursecat = core_course_category::get($id, MUST_EXIST, true);
    $subrootcat = get_subroot_category($coursecat);

    $elibraryid = get_config('theme_apoa', 'elibraryid');

    if($elibraryid == $subrootcat->id && $coursecat->depth == SECONDARY_CATEGORY_DEPTH){
        $journal = $DB->get_record($journalstable, array('category'=>$id));
    }
    else{
        $viewurl->param('categoryid', $id);
        redirect($viewurl);
    }

    $context = context_coursecat::instance($id);
    navigation_node::override_active_url(new moodle_url('/course/index.php', ['categoryid' => $category->id]));
    $PAGE->navbar->add(get_string('settings'));
    $PAGE->set_primary_active_tab('home');
    $PAGE->set_secondary_active_tab('edit');

    $url->param('id', $id);
    $strtitle = "edit" . $coursecat->get_formatted_name() . "settings";
    $itemid = 0; // Initialise itemid, as all files in category description has item id 0.
    $title = $strtitle;
    $fullname = $coursecat->get_formatted_name();

} else {
    $context = context_system::instance();
    $fullname = $SITE->fullname;
    $title = "$SITE->shortname: $strtitle";
}

require_capability('moodle/category:manage', $context);

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($title);
$PAGE->set_heading($fullname);

$params = array(
    'categoryid' =>$id,
    'context' => $context,
    'name' => $coursecat->name
);

if($journal){
    $params = array(    
        'categoryid' =>$id,
        'name' => $journal->name,
        'url' => $journal->url . $journal->path,
        'context' => $context,
    );
}

$mform = new editelibrary_form(null, $params);

if($journal){
    $url= array('url' => $journal->url . $journal->path);
    $mform->set_data($url);
}

if ($mform->is_cancelled()) {
    if ($id) {
        $viewurl->param('categoryid', $id);
    }
    redirect($manageurl);
} else if ($data = $mform->get_data()) {

    $parts = parse_url($data->url);

    if (isset($parts['scheme']) && isset($parts['host'])) {
        $scheme = $parts['scheme'];
        $host = $parts['host'];
        $url = $scheme . '://' . $host;
    } else {
        // Handle case when scheme or host is missing
        $url = '';
    }
    $path = $parts['path'].$parts['query'];
    if($journal){

        $journal->url = $url;
        $journal->path = $path;

        $DB->update_record($journalstable, $journal);
    }
    else{
        $journal = new stdClass();
        $journal->name = $coursecat->name;
        $journal->category = $id;
        $journal->url = $url;
        $journal->path = $path;

        $DB->insert_record($journalstable, $journal);
    }
    $viewurl->param('categoryid', $coursecat->id);
    redirect($viewurl);
}

echo $OUTPUT->header();
echo $OUTPUT->heading($strtitle);
$mform->display();
echo $OUTPUT->footer();
