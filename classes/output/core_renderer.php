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

namespace theme_apoa\output;

use core_course_category;
use moodle_url;
use html_writer;
use get_string;


defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/local/subscriptions/lib.php');
require_once($CFG->dirroot . '/auth/apoa/lib.php');
/**
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package    theme_boost
 * @copyright  2012 Bas Brands, www.basbrands.nl
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer extends \core_renderer {


    /**
     * Returns HTML to display a "Turn editing on/off" button in a form.
     *
     * @param moodle_url $url The URL + params to send through when clicking the button
     * @param string $method
     * @return string HTML the button
     */
    public function edit_button(moodle_url $url, string $method = 'post') {
        if ($this->page->theme->haseditswitch) {
            return;
        }
        $url->param('sesskey', sesskey());
        if ($this->page->user_is_editing()) {
            $url->param('edit', 'off');
            $editstring = get_string('turneditingoff');
        } else {
            $url->param('edit', 'on');
            $editstring = get_string('turneditingon');
        }
        $button = new \single_button($url, $editstring, $method, ['class' => 'btn btn-primary']);
        //return $this->render_single_button($button);
    }

    /**
     * Renders the "breadcrumb" for all pages in boost.
     *
     * @return string the HTML for the navbar.
     */
    public function navbar(): string {
        $newnav = new \theme_apoa\apoanavbar($this->page);
        return $this->render_from_template('core/navbar', $newnav);
    }

    /**
     * Wrapper for header elements.
     *
     * @return string HTML to display the main header.
     */
    public function full_header() {
        $pagetype = $this->page->pagetype;
        $homepage = get_home_page();
        $homepagetype = null;
        // Add a special case since /my/courses is a part of the /my subsystem.
        if ($homepage == HOMEPAGE_MY || $homepage == HOMEPAGE_MYCOURSES) {
            $homepagetype = 'my-index';
        } else if ($homepage == HOMEPAGE_SITE) {
            $homepagetype = 'site-index';
        }
        if ($this->page->include_region_main_settings_in_header_actions() &&
                !$this->page->blocks->is_block_present('settings')) {
            // Only include the region main settings if the page has requested it and it doesn't already have
            // the settings block on it. The region main settings are included in the settings block and
            // duplicating the content causes behat failures.
            $this->page->add_header_action(html_writer::div(
                $this->region_main_settings_menu(),
                'd-print-none',
                ['id' => 'region-main-settings-menu']
            ));
        }

        $header = new \stdClass();
        $header->settingsmenu = $this->context_header_settings_menu();
        $header->contextheader = $this->context_header();
        $header->hasnavbar = empty($this->page->layout_options['nonavbar']);
        $header->navbar = $this->navbar();
        $header->pageheadingbutton = $this->page_heading_button();
        $header->courseheader = $this->course_header();
        $header->headeractions = $this->page->get_header_actions();
        if (!empty($pagetype) && !empty($homepagetype) && $pagetype == $homepagetype) {
            $header->welcomemessage = \core_user::welcome_message();
        }
        
        return $this->render_from_template('theme_apoa/full_header', $header);
    }

        /**
     * This is an optional menu that can be added to a layout by a theme. It contains the
     * menu for the most specific thing from the settings block. E.g. Module administration.
     *
     * @return string
     */
    public function region_main_settings_menu() {
        $context = $this->page->context;
        $menu = new \action_menu();

        if ($context->contextlevel == CONTEXT_MODULE) {

            $this->page->navigation->initialise();
            $node = $this->page->navigation->find_active_node();
            $buildmenu = false;
            // If the settings menu has been forced then show the menu.
            if ($this->page->is_settings_menu_forced()) {
                $buildmenu = true;
            } else if (!empty($node) && ($node->type == \navigation_node::TYPE_ACTIVITY ||
                            $node->type == \navigation_node::TYPE_RESOURCE)) {

                $items = $this->page->navbar->get_items();
                $navbarnode = end($items);
                // We only want to show the menu on the first page of the activity. This means
                // the breadcrumb has no additional nodes.
                if ($navbarnode && ($navbarnode->key === $node->key && $navbarnode->type == $node->type)) {
                    $buildmenu = true;
                }
            }
            if ($buildmenu) {
                // Get the course admin node from the settings navigation.
                $node = $this->page->settingsnav->find('modulesettings', \navigation_node::TYPE_SETTING);
                if ($node) {
                    // Build an action menu based on the visible nodes from this navigation tree.
                    $this->build_action_menu_from_navigation($menu, $node);
                }
            }

        } else if ($context->contextlevel == CONTEXT_COURSECAT) {
            // For course category context, show category settings menu, if we're on the course category page.
            if ($this->page->pagetype === 'course-index-category') {
                $node = $this->page->settingsnav->find('categorysettings', \navigation_node::TYPE_CONTAINER);
                if ($node) {
                    // Build an action menu based on the visible nodes from this navigation tree.
                    $this->build_action_menu_from_navigation($menu, $node);
                }
            }

        }  else if ($context->contextlevel == CONTEXT_COURSE) {
            // For course category context, show category settings menu, if we're on the course category page.

            if ($this->page->pagetype === 'course-view-' . $this->page->course->format) {
                $settingsnode = $this->page->settingsnav->find('courseadmin', \navigation_node::TYPE_COURSE);
                if ($settingsnode) {
                    // Build an action menu based on the visible nodes from this navigation tree.
                    $skipped = $this->build_action_menu_from_navigation($menu, $settingsnode, false, true);

                    // We only add a list to the full settings menu if we didn't include every node in the short menu.
                    if ($skipped) {
                        $text = get_string('morenavigationlinks');
                        $url = new moodle_url('/course/admin.php', array('courseid' => $this->page->course->id));
                        $link = new \action_link($url, $text, null, null, new \pix_icon('t/edit', $text));
                        $menu->add_secondary_action($link);
                    }
                }
            }

        } else {
            $items = $this->page->navbar->get_items();
            $navbarnode = end($items);

            if ($navbarnode && ($navbarnode->key === 'participants')) {
                $node = $this->page->settingsnav->find('users', \navigation_node::TYPE_CONTAINER);
                if ($node) {
                    // Build an action menu based on the visible nodes from this navigation tree.
                    $this->build_action_menu_from_navigation($menu, $node);
                }

            }
        }
        return $this->render($menu);
    }


    /**s
     * Renders the context header for the page.
     *
     * @param array $headerinfo Heading information.
     * @param int $headinglevel What 'h' level to make the heading.
     * @return string A rendered context header.
     */
    public function context_header_cat($headerinfo = null, $headinglevel = 1): string {
        global $DB, $USER, $CFG, $SITE, $PAGE;
        require_once($CFG->dirroot . '/user/lib.php');
        $context = $this->page->context;
        $heading = null;
        $imagedata = null;
        $subheader = null;
        $userbuttons = null;


        if ($context->contextlevel != CONTEXT_USER && $context->contextlevel != CONTEXT_SYSTEM ){
            $id = $context->instanceid;
            if ($context->contextlevel == CONTEXT_COURSECAT) {
                $category = core_course_category::get($id);
                
            }
            else if ($context->contextlevel == CONTEXT_COURSE || $context->contextlevel == CONTEXT_MODULE ){
                $category = core_course_category::get($this->page->course->category);
                if($context->contextlevel == CONTEXT_MODULE){
                    $cm = $PAGE->cm;
                    if($cm){
                        $modname = $cm->modname;
                        if($modname == 'elibrary' || $modname == 'pdf' || $modname == 'committee'){
                            if(!$PAGE->user_is_editing()){
                                $courseurl = new moodle_url($CFG->wwwroot . '/course/view.php', array('id' => $PAGE->course->id));
                                redirect($courseurl);
                            }
                        }
                    }
                }
            }
            if($rootcategory = get_subroot_category($category)) {
                $PAGE->set_primary_active_tab($rootcategory->name . $rootcategory->id );
                $headerinfo['heading'] = $rootcategory->name;
                $url = $rootcategory->get_view_link();
                $header = new \theme_apoa\output\core\header($rootcategory->name, $rootcategory->id, $url);
                return $this->render_from_template('theme_apoa/header', $header->export_for_template($this));
                //$imagedata = html_writer::img(theme_apoa_get_file_from_setting('sectionlogo'), "", array('height'=>'100px'));
            };   
        }

        // Make sure to use the heading if it has been set.
        if (isset($headerinfo['heading'])) {
            $heading = $headerinfo['heading'];
        } else {
            $heading = $this->page->heading;
        }

        // The user context currently has images and buttons. Other contexts may follow.
        if ((isset($headerinfo['user']) || $context->contextlevel == CONTEXT_USER) && $this->page->pagetype !== 'my-index') {
            if (isset($headerinfo['user'])) {
                $user = $headerinfo['user'];
            } else {
                // Look up the user information if it is not supplied.
                $user = $DB->get_record('user', array('id' => $context->instanceid));
            }

            // If the user context is set, then use that for capability checks.
            if (isset($headerinfo['usercontext'])) {
                $context = $headerinfo['usercontext'];
            }

            // Only provide user information if the user is the current user, or a user which the current user can view.
            // When checking user_can_view_profile(), either:
            // If the page context is course, check the course context (from the page object) or;
            // If page context is NOT course, then check across all courses.
            $course = ($this->page->context->contextlevel == CONTEXT_COURSE) ? $this->page->course : null;

            if (user_can_view_profile($user, $course)) {
                // Use the user's full name if the heading isn't set.
                if (empty($heading)) {
                    $heading = fullname($user);
                }

                $imagedata = $this->user_picture($user, array('size' => 100));

                // Check to see if we should be displaying a message button.
                if (!empty($CFG->messaging) && has_capability('moodle/site:sendmessage', $context)) {
                    $userbuttons = array(
                        'messages' => array(
                            'buttontype' => 'message',
                            'title' => get_string('message', 'message'),
                            'url' => new moodle_url('/message/index.php', array('id' => $user->id)),
                            'image' => 'message',
                            'linkattributes' => \core_message\helper::messageuser_link_params($user->id),
                            'page' => $this->page
                        )
                    );

                    if ($USER->id != $user->id) {
                        $iscontact = \core_message\api::is_contact($USER->id, $user->id);
                        $contacttitle = $iscontact ? 'removefromyourcontacts' : 'addtoyourcontacts';
                        $contacturlaction = $iscontact ? 'removecontact' : 'addcontact';
                        $contactimage = $iscontact ? 'removecontact' : 'addcontact';
                        $userbuttons['togglecontact'] = array(
                                'buttontype' => 'togglecontact',
                                'title' => get_string($contacttitle, 'message'),
                                'url' => new moodle_url('/message/index.php', array(
                                        'user1' => $USER->id,
                                        'user2' => $user->id,
                                        $contacturlaction => $user->id,
                                        'sesskey' => sesskey())
                                ),
                                'image' => $contactimage,
                                'linkattributes' => \core_message\helper::togglecontact_link_params($user, $iscontact),
                                'page' => $this->page
                            );
                    }

                    $this->page->requires->string_for_js('changesmadereallygoaway', 'moodle');
                }
            } else {
                $heading = null;
            }
        }

        $prefix = null;

        $contextheader = new \context_header($heading, $headinglevel, $imagedata, $userbuttons, $prefix);
        return $this->render_context_header($contextheader);
    }

    public function context_header($headerinfo = null, $headinglevel = 1) {
        global $DB, $USER, $CFG, $SITE;
        require_once($CFG->dirroot . '/user/lib.php');
        $context = $this->page->context;
        $heading = null;
        $imagedata = null;
        $subheader = null;
        $userbuttons = null;

        // Make sure to use the heading if it has been set.
        if (isset($headerinfo['heading'])) {
            $heading = $headerinfo['heading'];
        } else {
            $heading = $this->page->heading;
        }

        if ($context->contextlevel == CONTEXT_SYSTEM){
            $heading = '';
        }
        if ($context->contextlevel == CONTEXT_COURSE){
            $heading = '';
        }
        if ($context->contextlevel == CONTEXT_COURSECAT){
            if($context->depth <= 3) {
                $heading = ''; 
            }
        }


        // The user context currently has images and buttons. Other contexts may follow.
        if ((isset($headerinfo['user']) || $context->contextlevel == CONTEXT_USER) && $this->page->pagetype !== 'my-index') {
            if (isset($headerinfo['user'])) {
                $user = $headerinfo['user'];
            } else {
                // Look up the user information if it is not supplied.
                $user = $DB->get_record('user', array('id' => $context->instanceid));
            }

            // If the user context is set, then use that for capability checks.
            if (isset($headerinfo['usercontext'])) {
                $context = $headerinfo['usercontext'];
            }

            // Only provide user information if the user is the current user, or a user which the current user can view.
            // When checking user_can_view_profile(), either:
            // If the page context is course, check the course context (from the page object) or;
            // If page context is NOT course, then check across all courses.
            $course = ($this->page->context->contextlevel == CONTEXT_COURSE) ? $this->page->course : null;

            if (user_can_view_profile($user, $course)) {
                // Use the user's full name if the heading isn't set.
                if (empty($heading)) {
                    $heading = fullname($user);
                }

                $imagedata = $this->user_picture($user, array('size' => 100));
                
                // Check to see if we should be displaying a message button.
                if (!empty($CFG->messaging) && has_capability('moodle/site:sendmessage', $context)) {
                    $userbuttons = array(
                        'messages' => array(
                            'buttontype' => 'message',
                            'title' => get_string('message', 'message'),
                            'url' => new moodle_url('/message/index.php', array('id' => $user->id)),
                            'image' => 'message',
                            'linkattributes' => \core_message\helper::messageuser_link_params($user->id),
                            'page' => $this->page
                        )
                    );

                    if ($USER->id != $user->id) {
                        $iscontact = \core_message\api::is_contact($USER->id, $user->id);
                        $contacttitle = $iscontact ? 'removefromyourcontacts' : 'addtoyourcontacts';
                        $contacturlaction = $iscontact ? 'removecontact' : 'addcontact';
                        $contactimage = $iscontact ? 'removecontact' : 'addcontact';
                        $userbuttons['togglecontact'] = array(
                                'buttontype' => 'togglecontact',
                                'title' => get_string($contacttitle, 'message'),
                                'url' => new moodle_url('/message/index.php', array(
                                        'user1' => $USER->id,
                                        'user2' => $user->id,
                                        $contacturlaction => $user->id,
                                        'sesskey' => sesskey())
                                ),
                                'image' => $contactimage,
                                'linkattributes' => \core_message\helper::togglecontact_link_params($user, $iscontact),
                                'page' => $this->page
                            );
                    }
                }
            } else {
                $heading = null;
            }
        }


        $contextheader = new \context_header($heading, $headinglevel, $imagedata, $userbuttons);
        return $this->render_context_header($contextheader);
    }

        /**
     * Renders the login form.
     *
     * @param \core_auth\output\login $form The renderable.
     * @return string
     */
    public function render_login(\core_auth\output\login $form) {
        global $CFG, $SITE;
        
        $context = $form->export_for_template($this);
        $authplugin = signup_is_enabled();
        if($authplugin->multipath){
            $context->signuppaths = [];
            $paths = $authplugin->get_paths();
            foreach($paths as $path){
                $pathurl = new moodle_url('/login/signup.php', array('path' => $path['path']));
                $context->signuppaths[] = array(
                    'signupurl' => $pathurl->out(false), 
                    'signupdesc' => $path['desc'],
                    'signuptitle' => $path['title']
                );
            }   
        }
        $context->errorformatted = $this->error_text($context->error);
        $url = $this->get_logo_url();
        if ($url) {
            $url = $url->out(false);
        }
        $instructions = get_config('theme_apoa', 'logininstructions');
        $context->instructions = format_text($instructions);
        $context->logourl = $url;
        $context->sitename = format_string($SITE->fullname, true,
                ['context' => \context_course::instance(SITEID), "escape" => false]);

        return $this->render_from_template('theme_apoa/loginform', $context);
    }


     /**
      * Renders the header bar.
      *
      * @param context_header $contextheader Header bar object.
      * @return string HTML for the header bar.
      */
    protected function render_context_header(\context_header $contextheader) {

        // Generate the heading first and before everything else as we might have to do an early return.
        if (!isset($contextheader->heading)) {
            $heading = $this->heading($this->page->heading, $contextheader->headinglevel, 'h2');
        } else {
            $heading = $this->heading($contextheader->heading, $contextheader->headinglevel, 'h2 mb-0');
        }

        // All the html stuff goes here.
        $html = html_writer::start_div('page-context-header');

        // Image data.
        if (isset($contextheader->imagedata)) {
            // Header specific image.
            $html .= html_writer::div($contextheader->imagedata, 'page-header-image mr-2');
        }

        // Headings.
        if (isset($contextheader->prefix)) {
            $prefix = html_writer::div($contextheader->prefix, 'text-muted text-uppercase small line-height-3');
            $heading = $prefix . $heading;
        }
        $html .= html_writer::tag('div', $heading, array('class' => 'page-header-headings'));

        // Buttons.
        if (isset($contextheader->additionalbuttons)) {
            $html .= html_writer::start_div('btn-group header-button-group');
            foreach ($contextheader->additionalbuttons as $button) {
                if (!isset($button->page)) {
                    // Include js for messaging.
                    if ($button['buttontype'] === 'togglecontact') {
                        \core_message\helper::togglecontact_requirejs();
                    }
                    if ($button['buttontype'] === 'message') {
                        \core_message\helper::messageuser_requirejs();
                    }
                    $image = $this->pix_icon($button['formattedimage'], $button['title'], 'moodle', array(
                        'class' => 'iconsmall',
                        'role' => 'presentation'
                    ));
                    $image .= html_writer::span($button['title'], 'header-button-title');
                } else {
                    $image = html_writer::empty_tag('img', array(
                        'src' => $button['formattedimage'],
                        'role' => 'presentation'
                    ));
                }
                $html .= html_writer::link($button['url'], html_writer::tag('span', $image), $button['linkattributes']);
            }
            $html .= html_writer::end_div();
        }
        $html .= html_writer::end_div();

        return $html;
    }

    /**
     * See if this is the first view of the current cm in the session if it has fake blocks.
     *
     * (We track up to 100 cms so as not to overflow the session.)
     * This is done for drawer regions containing fake blocks so we can show blocks automatically.
     *
     * @return boolean true if the page has fakeblocks and this is the first visit.
     */
    public function firstview_fakeblocks(): bool {
        global $SESSION;

        $firstview = false;
        if ($this->page->cm) {
            if (!$this->page->blocks->region_has_fakeblocks('side-pre')) {
                return false;
            }
            if (!property_exists($SESSION, 'firstview_fakeblocks')) {
                $SESSION->firstview_fakeblocks = [];
            }
            if (array_key_exists($this->page->cm->id, $SESSION->firstview_fakeblocks)) {
                $firstview = false;
            } else {
                $SESSION->firstview_fakeblocks[$this->page->cm->id] = true;
                $firstview = true;
                if (count($SESSION->firstview_fakeblocks) > 100) {
                    array_shift($SESSION->firstview_fakeblocks);
                }
            }
        }
        return $firstview;
    }



     /**
     * See if this is the first view of the current cm in the session if it has fake blocks.
     *
     * (We track up to 100 cms so as not to overflow the session.)
     * This is done for drawer regions containing fake blocks so we can show blocks automatically.
     *
     * @return string true if the page has fakeblocks and this is the first visit.
     */
    public function main_page_content() {
        
        global $PAGE;
        $PAGE->requires->js_call_amd('theme_apoa/mymodal', 'init');
        $PAGE->requires->js_call_amd('theme_apoa/tablistcycle', 'init');

        $output =  new \theme_apoa\output\core\mainpage\mainpage;
        $template = $output->export_for_template($this);
        return $this->render_from_template('theme_apoa/mainpage/mainpage', $template);
        
    }

    public function view_on_mobile() {
        global $CFG, $USER, $SESSION, $DB;

        $page = $this->page;
        $context = $page->context;
        $contextid = $context->id;

        $mobileapplinkid = 'mobilelinkappid' . $contextid;

        if(!get_config('theme_apoa', 'viewinappbutton')) {
            return '';
        }

        if(!$context->contextlevel == CONTEXT_COURSE && !$context->contextlevel == CONTEXT_MODULE){
            return '';
        }

            require_once($CFG->dirroot .'/vendor/autoload.php');

            if(!class_exists('\Detection\MobileDetect')){
                return;
            }

            $detect = new \Detection\MobileDetect();
    
            if(!$detect->isMobile()){
                return '';
            };

            if($context->contextlevel == CONTEXT_COURSE) {
                $course = $page->course;
                $redirectpath = "/course/view.php?id=$course->id";
            }
            else if($context->contextlevel == CONTEXT_MODULE){
                $cm = $page->cm;
                $modname = $cm->modname;
                $redirectpath = "/mod/$modname/view.php?id=$cm->id"; 
            }
            else{
                return '';
            }
            

            if(!$branchApiKey = get_config('theme_apoa', 'branchapikey')){
                return '';
            }

            $parsedurl = parse_url($CFG->wwwroot);
            
            if(isloggedin() && !isguestuser()){
                $deeplink_path = $parsedurl['scheme'] . "://$USER->username@" . $parsedurl['host'] . "?redirect=$redirectpath";
            }
            else{
                $deeplink_path = $parsedurl['scheme'] . "://" . $parsedurl['host'] . "?redirect=$redirectpath";
            }

            $data = array(
                'branch_key' => $branchApiKey,
                'channel' => 'web',
                'feature' => 'login',
                'stage' => 'existing_user',
                'tags' => ['dynamic_link'],
                'data' => array(    
                    '$deeplink_path' => $deeplink_path,
                )
            );

            $options = array(
                'http' => array(
                    'header'  => "Content-Type: application/json\r\n" .
                                "Authorization: Bearer " . $branchApiKey . "\r\n",
                    'method'  => 'POST',
                    'content' => json_encode($data),
                ),
            );

            $context  = stream_context_create($options);
            $result = file_get_contents('https://api2.branch.io/v1/url', false, $context);
            
            if ($result === FALSE) {
                return '';
            }

            $decoded = json_decode($result);
            $SESSION->$mobileapplinkid = $decoded->url; 

            return $this->render_from_template('theme_apoa/view_on_mobile', array('appurl' => $SESSION->$mobileapplinkid));
        
    }
    
         /**
     * See if this is the first view of the current cm in the session if it has fake blocks.
     *
     * (We track up to 100 cms so as not to overflow the session.)
     * This is done for drawer regions containing fake blocks so we can show blocks automatically.
     *
     * @return string true if the page has fakeblocks and this is the first visit.
     */
    public function main_page_modal() {
        
        global $PAGE, $SESSION;

        if (!isset($SESSION->mainmodalclosed)){
            $PAGE->requires->js_call_amd('theme_apoa/mainmodal', 'init', array(true));
        }

        $template = [
            'mainmodal' => get_config('theme_apoa', 'mainmodaltoggle'),
            'mainmodalbg' => theme_apoa_get_file_from_setting('mainmodalbg'),
            'mainmodalbgmobile' => theme_apoa_get_file_from_setting('mainmodalbgmobile'),
            'mainmodalcontent' => get_config('theme_apoa', 'mainmodalcontent'),
            'mainmodallink' => get_config('theme_apoa', 'mainmodallink')

        ]
        ;
        return $this->render_from_template('theme_apoa/mainpage/mainmodal', $template);
        
    }

    /**
     * This is an optional menu that can be added to a layout by a theme. It contains the
     * menu for the course administration, only on the course main page.
     *
     * @return string
     */
    public function context_header_settings_menu() {
        $context = $this->page->context;
        $menu = new \action_menu();

        $items = $this->page->navbar->get_items();
        $currentnode = end($items);

        $showcoursemenu = false;
        $showfrontpagemenu = false;
        $showusermenu = false;

        // We are on the course home page.
        if (($context->contextlevel == CONTEXT_COURSE) &&
                !empty($currentnode) &&
                ($currentnode->type == \navigation_node::TYPE_COURSE || $currentnode->type == \navigation_node::TYPE_SECTION)) {
            $showcoursemenu = true;
        }

        $courseformat = course_get_format($this->page->course);
        // This is a single activity course format, always show the course menu on the activity main page.
        if ($context->contextlevel == CONTEXT_COURSE &&
                !$courseformat->has_view_page()) {
            
            $course = $this->page->course;
            $this->page->navigation->initialise();
            $activenode = $this->page->navigation->find_active_node();
            $showcoursemenu = true;
            // If the settings menu has been forced then show the menu.
            if ($this->page->is_settings_menu_forced()) {
                $showcoursemenu = true;
            } else if (!empty($activenode) && ($activenode->type == \navigation_node::TYPE_ACTIVITY ||
                            $activenode->type == \navigation_node::TYPE_RESOURCE)) {

                // We only want to show the menu on the first page of the activity. This means
                // the breadcrumb has no additional nodes.
                if ($currentnode && ($currentnode->key == $activenode->key && $currentnode->type == $activenode->type)) {
                    $showcoursemenu = true;
                }
            }
        }

        // This is the site front page.
        if ($context->contextlevel == CONTEXT_COURSE &&
                !empty($currentnode) &&
                $currentnode->key === 'home') {
            $showfrontpagemenu = true;
        }

        // This is the user profile page.
        if ($context->contextlevel == CONTEXT_USER &&
                !empty($currentnode) &&
                ($currentnode->key === 'myprofile')) {
            $showusermenu = true;
        }

        if ($showfrontpagemenu) {
            $settingsnode = $this->page->settingsnav->find('frontpage', \navigation_node::TYPE_SETTING);
            if ($settingsnode) {
                // Build an action menu based on the visible nodes from this navigation tree.
                $skipped = $this->build_action_menu_from_navigation($menu, $settingsnode, false, true);

                // We only add a list to the full settings menu if we didn't include every node in the short menu.
                if ($skipped) {
                    $text = get_string('morenavigationlinks');
                    $url = new moodle_url('/course/admin.php', array('courseid' => $this->page->course->id));
                    $link = new \action_link($url, $text, null, null, new \pix_icon('t/edit', $text));
                    $menu->add_secondary_action($link);
                }
            }
        } else if ($showcoursemenu) {
            $settingsnode = $this->page->settingsnav->find('courseadmin', \navigation_node::TYPE_COURSE);
            if ($settingsnode) {
                // Build an action menu based on the visible nodes from this navigation tree.
                $skipped = $this->build_action_menu_from_navigation($menu, $settingsnode, false, true);

                // We only add a list to the full settings menu if we didn't include every node in the short menu.
                if ($skipped) {
                    $text = get_string('morenavigationlinks');
                    $url = new moodle_url('/course/admin.php', array('courseid' => $this->page->course->id));
                    $link = new \action_link($url, $text, null, null, new \pix_icon('t/edit', $text));
                    $menu->add_secondary_action($link);
                }
            }
        } else if ($showusermenu) {
            // Get the course admin node from the settings navigation.
            $settingsnode = $this->page->settingsnav->find('useraccount', \navigation_node::TYPE_CONTAINER);
            if ($settingsnode) {
                // Build an action menu based on the visible nodes from this navigation tree.
                $this->build_action_menu_from_navigation($menu, $settingsnode);
            }
        }

        return $this->render($menu);
    }

    public function footer_socialmedia() {
        $socials = ['facebook', 'instagram', 'twitter', 'linkedin'];
        $template = [];
        foreach ($socials as $social) {
            $link = get_config('theme_apoa', $social . 'link');
            $path = get_config('theme_apoa', $social . 'path');

            if($link === "" || $path === "") {
                continue;
            }

            $template[] = array(
                'sociallink' => $link,
                'socialpath' => $path
            ); 
        }

        return $template;
    }

    public function footer_contact_info() {

        $formatoptions = new \stdClass;
        $formatoptions->noclean = true;
        $formatoptions->overflowdiv = true;
        $content = format_text(get_config('theme_apoa', 'footercontact'), FORMAT_PLAIN, $formatoptions);
        return $content;
    }

    public function footer_quick_links() {
        $setting = get_config('theme_apoa', 'footerquicklinks');
        $lines = explode("\n", $setting);
        $template = [];
        foreach ($lines as $line) {
            list($label, $rawlink) = explode('|', $line);
            $link = new moodle_url($rawlink);
            array_push($template, array('quickname' => $label, 'quicklink' => $link));
        }
        return $template;
    }

    /**
     * Renders an mform element from a template.
     *
     * @param HTML_QuickForm_element $element element
     * @param bool $required if input is required field
     * @param bool $advanced if input is an advanced field
     * @param string $error error message to display
     * @param bool $ingroup True if this element is rendered as part of a group
     * @return mixed string|bool
     */
    public function mform_element($element, $required, $advanced, $error, $ingroup) {
        if(method_exists($element, 'get_template_name')){
            $templatename = $element->get_template_name($this);
        }
        else{
            $templatename = 'core_form/element-' . $element->getType();
                if ($ingroup) {
                    $templatename .= "-inline";
                }
        }
        try {
            // We call this to generate a file not found exception if there is no template.
            // We don't want to call export_for_template if there is no template.
            \core\output\mustache_template_finder::get_template_filepath($templatename);

            if ($element instanceof \templatable) {
                $elementcontext = $element->export_for_template($this);

                $helpbutton = '';
                if (method_exists($element, 'getHelpButton')) {
                    $helpbutton = $element->getHelpButton();
                }
                $label = $element->getLabel();
                $text = '';
                if (method_exists($element, 'getText')) {
                    // There currently exists code that adds a form element with an empty label.
                    // If this is the case then set the label to the description.
                    if (empty($label)) {
                        $label = $element->getText();
                    } else {
                        $text = $element->getText();
                    }
                }

                // Generate the form element wrapper ids and names to pass to the template.
                // This differs between group and non-group elements.
                if ($element->getType() === 'group') {
                    // Group element.
                    // The id will be something like 'fgroup_id_NAME'. E.g. fgroup_id_mygroup.
                    $elementcontext['wrapperid'] = $elementcontext['id'];

                    // Ensure group elements pass through the group name as the element name.
                    $elementcontext['name'] = $elementcontext['groupname'];
                } else {
                    // Non grouped element.
                    // Creates an id like 'fitem_id_NAME'. E.g. fitem_id_mytextelement.
                    $elementcontext['wrapperid'] = 'fitem_' . $elementcontext['id'];
                }

                $context = array(
                    'element' => $elementcontext,
                    'label' => $label,
                    'text' => $text,
                    'required' => $required,
                    'advanced' => $advanced,
                    'helpbutton' => $helpbutton,
                    'error' => $error,
                    'valid' => $elementcontext['valid']
                );
                return $this->render_from_template($templatename, $context);
            }
        } catch (\Exception $e) {
            // No template for this element.
            return false;
        }
    }

    /**
     * Allows plugins to add a notification just before main body
     *
     * @return string
     */
    public function display_notification_before_main() {
        global  $USER;
        $output = '';

        if(!isloggedin() || isguestuser()){
            return;
        }

        // Give subsystems an opportunity to inject extra html content. The callback
        // must always return a string containing valid html.
        foreach (\core_component::get_core_subsystems() as $name => $path) {
            if ($path) {
                $output .= component_callback($name, 'display_notification_before_main', [], '');
            }
        }

        // Give plugins an opportunity to inject extra html content. The callback
        // must always return a string containing valid html.
        $pluginswithfunction = get_plugins_with_function('display_notification_before_main', 'lib.php');
        foreach ($pluginswithfunction as $plugins) {
            foreach ($plugins as $function) {
                list($message, $preference) = $function();
                if(!get_user_preferences($preference)){
                    if($message !== null) {
                        $notification = new \theme_apoa\output\notification($message, 'special', true);
                        $notification->set_extra_classes(['error']);
                        $notification->set_name_and_user($preference, $USER->id);
                        $output .= $this->render_from_template($notification->get_template_name(), $notification->export_for_template($this));
                    }
                }
            }
        }   

        return $output;

    }

    
    public function has_active_subscription(){
        global $CFG, $USER;
    
        if (isloggedin() && !isguestuser()) {
            $preference = 'theme_apoa_user_nosub';
            if(!get_user_preferences($preference)){
                $redirect=  $CFG->wwwroot . '/local/subscriptions/index.php';
                if(!user_has_active_subscription()){
                    $message = get_string('noactivesubscription', 'theme_apoa', $redirect);
                    $notification = new \theme_apoa\output\notification($message, 'special', true);
                    $notification->set_extra_classes(['error']);
                    $notification->set_name_and_user($preference, $USER->id);
                    return $this->render_from_template($notification->get_template_name(), $notification->export_for_template($this));
                }
            }
        }
    }

    public function has_membership_category_approved()
    {   
        global $USER;
        if (isloggedin() && !isguestuser()) {
            $preference = 'theme_apoa_user_notapproved';
                if(!get_user_preferences($preference)){
                    $membershipfields = is_membership_category_approved();
                    if(!$membershipfields['membership_category_approved']){
                        if($membershipfields['membership_category'] == "Federation Fellow"){
                            $message = get_string('federationpending', 'theme_apoa');
                        }
                        else if($membershipfields['membership_category'] == "No Membership"){
                            $message = get_string('nomembershippending', 'theme_apoa');
                        }
                        else{
                            $message = get_string('membershipcategoryapprovalpending', 'theme_apoa', $membershipfields['membership_category']);
                        }
                    
                    $notification = new \theme_apoa\output\notification($message, 'special', true);
                    $notification->set_extra_classes(['error']);
                    $notification->set_name_and_user($preference, $USER->id);
                    return $this->render_from_template($notification->get_template_name(), $notification->export_for_template($this));
                }
            }
        }
    }


    /**
     * Returns HTML to display a continue button that goes to a particular URL.
     *
     * @param string|moodle_url $url The url the button goes to.
     * @return string the HTML to output.
     */
    public function continue_button($url) {
        if (!($url instanceof moodle_url)) {
            $url = new moodle_url($url);
        }
        
        if($url->compare(new moodle_url('/login/index.php'), URL_MATCH_BASE)){
            $button = new \single_button($url, get_string('login'), 'get', \single_button::BUTTON_PRIMARY);
        }else{
            $button = new \single_button($url, get_string('continue'), 'get', \single_button::BUTTON_PRIMARY);
        }
        
        $button->class = 'continuebutton';

        return $this->render($button);
    }


        /**
     * Return the moodle_url for an image.
     *
     * The exact image location and extension is determined
     * automatically by searching for gif|png|jpg|jpeg, please
     * note there can not be diferent images with the different
     * extension. The imagename is for historical reasons
     * a relative path name, it may be changed later for core
     * images. It is recommended to not use subdirectories
     * in plugin and theme pix directories.
     *
     * There are three types of images:
     * 1/ theme images  - stored in theme/mytheme/pix/,
     *                    use component 'theme'
     * 2/ core images   - stored in /pix/,
     *                    overridden via theme/mytheme/pix_core/
     * 3/ plugin images - stored in mod/mymodule/pix,
     *                    overridden via theme/mytheme/pix_plugins/mod/mymodule/,
     *                    example: image_url('comment', 'mod_glossary')
     *
     * @param string $imagename the pathname of the image
     * @param string $component full plugin name (aka component) or 'theme'
     * @return moodle_url
     */
    public function image_url($imagename, $component = 'moodle') {
        global $COURSE;
        if($imagename == 'i/rsssitelogo'){
            return $this->page->theme->image_url($imagename, $component);
        }
        return $this->page->theme->image_url($imagename, $component);
    }

}
    



