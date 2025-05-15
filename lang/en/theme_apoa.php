<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin strings are defined here.
 *
 * @package     theme_apoa
 * @category    string
 * @copyright   2023 Matthew Faulkner matthewnfaulkner@gmail.com
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'apoa online theme';
// The name of the second tab in the theme settings.                                                                                
$string['advancedsettings'] = 'Advanced settings';                                                                                  
// The brand colour setting.                                                                                                        
$string['brandcolor'] = 'Brand colour';                                                                                             
// The brand colour setting description.                                                                                            
$string['brandcolor_desc'] = 'The accent colour.';     
// A description shown in the admin theme selector.                                                                                 
$string['choosereadme'] = 'Theme apoa is a child theme of Boost. It adds the ability to upload background photos.';                
// Name of the settings pages.                                                                                                      
$string['configtitle'] = 'Apoa settings';                                                                                          
// Name of the first settings tab.                                                                                                  
$string['generalsettings'] = 'General settings';                                                                                    
// The name of our plugin.                                                                                                          
$string['pluginname'] = 'Apoa';                                                                                                    
// Preset files setting.                                                                                                            
$string['presetfiles'] = 'Additional theme preset files';                                                                           
// Preset files help text.                                                                                                          
$string['presetfiles_desc'] = 'Preset files can be used to dramatically alter the appearance of the theme. See <a href=https://docs.moodle.org/dev/Boost_Presets>Boost presets</a> for information on creating and sharing your own preset files, and see the <a href=http://moodle.net/boost>Presets repository</a> for presets that others have shared.';
// Preset setting.                                                                                                                  
$string['preset'] = 'Theme preset';                                                                                                 
// Preset help text.                                                                                                                
$string['preset_desc'] = 'Pick a preset to broadly change the look of the theme.';                                                  
// Raw SCSS setting.                                                                                                                
$string['rawscss'] = 'Raw SCSS';                                                                                                    
// Raw SCSS setting help text.                                                                                                      
$string['rawscss_desc'] = 'Use this field to provide SCSS or CSS code which will be injected at the end of the style sheet.';       
// Raw initial SCSS setting.                                                                                                        
$string['rawscsspre'] = 'Raw initial SCSS';                                                                                         
// Raw initial SCSS setting help text.                                                                                              
$string['rawscsspre_desc'] = 'In this field you can provide initialising SCSS code, it will be injected before everything else. Most of the time you will use this setting to define variables.';
// We need to include a lang string for each block region.                                                                          
$string['region-side-pre'] = 'Right';

$string['noactivesubscription'] = 'You don\'t currently have an active subscription, without one your account has limited access.
Click <a href="{$a}">HERE</a> to learn more.';
$string['federationpending'] = 'Still waiting for your federation to confirm your membership. Your experience will be limited until they do.';
$string['dontshowmessageagain'] = '(Don\'t show this message again)';
$string['membershipcategoryapprovalpending'] = 'You haven\'t yet been approved as a {$a}. Your experience will be limited until you are.';
$string['nomembershippending'] = "You currently are not a confirmed member of APOA.";

$string['Announcements'] = 'Announcements';
$string['Events'] = 'Events';
$string['Forum'] = 'Forum';
$string['Gallery'] = 'Gallery';
$string['Meetings'] = 'Meetings';
$string['Committee'] = 'Committee';
$string['Committees'] = 'Committees';
$string['About'] = 'About';
$string['Knee'] = 'Asia Pacific Knee Society';
$string['HandandUpperLimb'] = 'Asia Pacific Hand & Upper Limb Society';
$string['Hip'] = 'Asia Pacific Hip Society';
$string['InfectionSection'] = 'Asia Pacific Infection Section';
$string['FootandAnkle'] = 'Asia Pacific Foot and Ankle Society';
$string['OrthopaedicResearch'] = 'Asia Pacific Orthopaedic Research Society';
$string['Osteoporosis'] = 'Asia Pacific Osteoporosis Society';
$string['Paediatrics'] = 'Asia Pacific Paediatrics Society';
$string['Spine'] = 'Asia Pacific Spine Society';
$string['SportsInjury'] = 'Asia Pacific Sports Injury Section';
$string['Trauma'] = 'Asia Pacific Trauma Society';
$string['WAVES'] = "Asia Pacific Women's Advocacy";
$string['E-Library'] = "E-Library";
$string['Sections'] = "Sections";
$string['Newsletter'] = "Newsletter";
$string['APOA'] = "Asia Pacific Orthopaedic Association";
$string['Subscriptions'] = "Subscriptions";

$string['2025APOACongressAustralia'] = "24th APOA Congress Cairns";

$string['mainpagesettings'] = "Home Page Settings";
$string['jumbotitle'] = "Jumbotron title";
$string['jumbobgcolor'] = "Jumbotron Background Colour";
$string['jumbobgcolor_desc'] = "Select the color for the background of the jumbo tron, pick a color that matches the edges of the banner image.";
$string['jumboshowtext'] = "Show Jumbotron Text";
$string['jumboshowtext_desc'] = "Toggles whetehr to display title, description and logo overlay over banner.";
$string['jumbotitle_desc'] = "The main heading for the home page jumbo tron";
$string['jumbodescription'] = "Jumbotron subtext";
$string['jumbodescription_desc'] = "Smaller subtext displayed just below the main heading";
$string['jumbotag'] = "Jumbotron tag";
$string['jumbotag_desc'] = "tag for the content displayed in the jumbotron, usually 'Events'";
$string['jumbovideo'] = "Jumbotron video";
$string['jumbovideo_desc'] = "video to be displayed within the jumbotron as a picture in picture";
$string['jumbobanner'] = "Jumbotron Banner";
$string['jumbobanner_desc'] = "Video or image to be dispalyed prominently as background of the jumbotron";
$string['jumboposter'] = "Jumbotron Poster";
$string['jumboposter_desc'] = "Alternative image to be displayed in jumbotron if jumbo banner fails to load";
$string['jumbobannerlogo'] = "APOA Logo";
$string['jumbobannerlogo_desc'] = "APOA logo to be displayed on the right hand side of jumbotron";
$string['jumboid'] = 'Id of course for Jumbotron';
$string['jumboid_desc'] = 'Course ID of the course related to the jumbotron';
$string['jumbolink'] = 'Link for jumbotron';
$string['jumboid_desc'] = 'Link that determines where clicking the jumbotron will navigate to, if blank uses jumbo id';
$string['jumboposter'] = 'Jumbotron poster';
$string['jumboid_desc'] = 'Image displayed if jumbotron video fails to load';


$string['mainpageresources'] = 'Main page resources';
$string['resources_desc'] = "Placeholder image for resources tiles";
$string['resources'] = "Resources tiles image";
$string['resourceslink'] = "Resources tiles link";

$string['sectionsettings'] = "Section Settings";
$string['sectionlink'] = '{$a} Link';
$string['sectionlink_desc'] = 'Alternative link for category: {$a}. Default is category landing page.';
$string['sectionlogo_desc'] = "The logo for this section of the APOA";

$string['footersettings'] = 'Footer Settings';
$string['footercontact'] = 'Footer Contact Info';
$string['footerquicklinks'] = 'Footer Quick Links';

$string['categorysettings'] = "Category Settings";
$string['elibraryid'] = "Elibrary ID";
$string['elibraryid_desc'] = "Category ID for the Elibrary Category, can be found in the URL of the Elibrary landing page";
$string['newsletterid'] = "Newsletter ID";
$string['newsletter_desc'] = "Category ID for the Newsletter Category, can be found in the URL of the Newsletter landing page";
$string['APOAid'] = "APOA main category ID";
$string['APOAid_desc'] = "Category ID for the APOA main Category, can be found in the URL of the APOA main landing page";
$string['Sectionsid'] = "APOA main category ID";
$string['Sectionsid_desc'] = "Category ID for the Sections Category, can be found in the URL of the Sections landing page";
$string['someallowguest'] = "You can also explore the site as a guest";

$string['skiptoagreement'] = "Skip To Consent";
$string['tourelibrarystart'] = "Welcome to the E-Library";
$string['tourelibrarystartmessage'] = "It looks as though you're visiting the E-Library for the first time,
would you like a tour";
$string['tourelibraryintroduction'] = "What is the E-Library?";
$string['tourelibraryintroductionmessage'] = "The E-Library is a repository of research papers collated from participating journals across the Asia Pacific region covering all areas of Orthopaedics. With the intention of creating a hub for discussion and collaboration amongst the APOA community. The papers are organised by their journal, area of research and publication date.";
$string['tourelibrarynavigation'] = "Navigating the E-Library";
$string['tourelibrarynavigationmessage'] = "You can navigate the E-Library here. Choose a specific journal, 
search manually or check out on going journal clubs.";
$string['tourelibrarysearch'] = "Quick Search"; 
$string['tourelibrarysearchmessage'] = "Know the name of the paper? You can perform a quick search here.";
$string['tourelibraryfeatured'] = "Featured Article";
$string['tourelibraryfeaturedmessage'] = "Whichever paper currently has the most activity, that being active 
discussions, boosts, and views, is displayed prominently.";
$string['tourelibraryfeatured'] = "Featured Article";
$string['tourelibraryfeaturedmessage'] = "Whichever paper currently has the most activity, that being active 
discussions, boosts, and views, is displayed prominently here.";
$string['tourelibraryjournals'] = "Journals";
$string['tourelibraryjournalsmessage'] = "All the journals are show here, popular articles within each are displayed too.";
$string['tourelibraryarticle'] = "E-Library Article";
$string['tourelibraryarticlemessage'] = "This is an article within the E-Library click on the title to view the
full article";
$string['tourelibraryarticleissue'] = "Issue";
$string['tourelibraryarticleissuemessage'] = "You can also visit the issue the article is from and find
find other articles from the same issue";
$string['tourelibraryarticletag'] = "Tag";
$string['tourelibraryarticletagmessage'] = "Each article also includes a tag indicating the area of Orthopaedics
the article is concerned with. You can find articles with the same tag by clicking this.";
$string['tourelibraryarticlediscussion'] = "Discussion";
$string['tourelibraryarticlediscussionmessage'] = "Finally each article includes a discussion, clicking here
expands a small preview.";
//tour
$string['endtour'] = "End Tour";
$string['tourmainwelcometitle'] = "Welcome";
$string['tourmainwelcomemessage'] = "It looks like you've not been here before, would you like a quick tour to show you all the features of the site";
$string['tourmainintro'] = "Introduction";
$string['tourmainintromessage'] = "The new APOA online site aims to offer more value for its members than ever before. 
\nServing as a one stop shop for new, events, education and community.
\nTheir are tours like this one across the site so please explore to learn all about the features.
\nOr visit the blog for more video guides.";
$string['tourmainprimary'] = "Navigation";
$string['tourmainprimarymessage'] = "Here you can learn about the APOA, explore the 12 Sections of APOA, visit the E-Library, and learn about membership.";
$string['tourmainnotifications'] = "Notifications";
$string['tourmainnotificationsmessage'] = "Get notified when events start, forum replies, membership expiration and much more.";
$string['tourmainmessages'] = "Messages";
$string['tourmainmessagesmessage'] = "Here you can chat with other members.";
$string['tourmainprofile'] = "Profile";
$string['tourmainprofilemessage'] = "View and Edit your profile. Add a photo, set privacy settings, view active memberships.";

$string['tourmainjumbo'] = "Main Congress";
$string['tourmainjumbomessage'] = "Front and center is our annual meeting, find out all about it here.";

$string['tourmainsidejumbo'] = "Announcements";
$string['tourmainsidejumbomessage'] = "View out latest announcements, hover/tap to enlarge and find out more. ";

$string['tourmainelibrary'] = "E-Library";
$string['tourmainelibrarymessage'] = "The E-Library is a repository of Orthopaedic research papers from all over the Asia Pacific region. 
Shown here are the current most popular papers. Don't see one you like, explore more in the E-library.";

$string['tourmainevents'] = "Events";
$string['tourmaineventsmessage'] = "Past, present or future events are shown here.";

$string['tourmainsections'] = "Sections";
$string['tourmainsectionsmessage'] = "The APOA has 12 sections, each has its own space on the site, click a section's logo to visit them.";

$string['tourmainabout'] = "About";
$string['tourmainaboutmessage'] = "Learn all about the APOA.
";
$string['tourmainmembership'] = "Membership";
$string['tourmainmembershipmessage'] = "Membership grants you full access to the site and all its features. Interested? Find out more here.";



$string['tourmainresources'] = "Resources";
$string['tourmainresourcesmessage'] = "Here are some additional resources you might find interesting.
\nCheckout the latest newsletter, 
\nAsk a question in the APOA forum, 
\nread the blog,
\nview the gallery and more.";

$string['navigation'] = 'Primary Navbar Settings';
$string['primarynavcount'] = 'Primary navigation items';
$string['primarynavcount_desc'] = 'Number of items to add to the navigation';
$string['primarynavitems'] = 'Primary Navigation Item {$a}';
$string['primarynavitems_desc'] = 'The category to add to primary navigation in position {$a}';

$string['logininstructions'] = 'Login Instructions';
$string['logininstructions_desc'] = 'Enter Instructions to be displayed on the login page.'; 

$string['categorymenuheading'] = 'Category Menu';
$string['activitymenuheading'] = 'Activity Menu';
$string['coursemenuheading'] = 'Action Menu';
$string['join'] = 'Join APOA';


$string['mainpagenotification'] = 'Main Page Notification';
$string['mainpagenotification_desc'] = 'This notification is displayed at the top of the main page for all.';
$string['jumbovideoflag'] = 'Jumbo Video Enabled';
$string['jumbovideoflag_desc'] = 'Enable or Disable the jumbo video.';

$string['jumbobannerposter'] = 'Mainpage Jumbo Banner';
$string['jumbobannerposter_desc'] = 'This image is displayed prominently on the main page jumbo.';

$string['mainmodalsettings'] = 'Main Page Modal Settings';
$string['mainmodaltoggle'] = 'Toggel Main Page Modal';
$string['mainmodaltoggle_desc'] = 'Toggle whether the main page modal is displayed or not.';

$string['mainmodalbg'] = 'Main Modal Image';
$string['mainmodalbg_desc'] = 'Background image of the main page modal. 16:9 aspect ratio.';

$string['mainmodalbgmobile'] = 'Main Modal Image Mobile';
$string['mainmodalbgmobile_desc'] = 'Background image of the main page modal for mobile, square aspect ratio.';

$string['mainmodallink'] = 'Main Modal Link';
$string['mainmodallink_desc'] = 'URL to addd a link the to main modal';

$string['viewinappbutton'] = 'View in App Button';
$string['viewinappbutton_desc'] = 'Toggles a button displayed in the footer when the user is on mobile. Button opents current page in the app.';


$string['linkedinlink']  = "Link to Linkedin";
$string['linkedinpath']  = "HTML <SVG> for Linkedin Logo";
$string['facebooklink']  = "Link to Facebook";
$string['facebookpath']  = "HTML <SVG> for Facebook Logo";
$string['twitterlink']   = "Link to Twitter";
$string['twitterpath']   = "HTML <SVG> for Twitter Logo";
$string['instagramlink'] = "Link to Instagram";
$string['instagrampath'] = "HTML <SVG> for Instagram Logo";