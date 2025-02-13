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
 * Theme Edunao - Language pack
 *
 * @package    theme_edunao123
 * @copyright  2024 Mako Digital <admin@mako.digital>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This line protects the file from being accessed by a URL directly.                                                               
defined('MOODLE_INTERNAL') || die();

/**
 * Callback immediately after require_login succeeds.
 *
 * This is an implementation of a legacy callback that will only be called in older Moodle versions.
 * It will not be called in Moodle 4.4+ that contain the hook core\hook\output\before_http_headers,
 */
function theme_edunao123_after_require_login() {
    global $SERVER;

    $url = $_SERVER['PHP_SELF'];

    $redirected = [
        '/user/preferences.php' => 'restrict_preferences',
        '/admin/tool/certificate/my.php' => 'restrict_my_certificates'
    ];
    
    if (in_array($url, array_keys($redirected))) {
        $restrict = get_config('theme_edunao123', $redirected[$url]);

        if (!is_siteadmin() && $restrict) {
            redirect('/user/profile.php');
        }
    }
}

function theme_edunao123_myprofile_navigation(core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course) {
    global $USER;

    // Check that we have a valid user.
    $user = \core_user::get_user($user->id, '*', MUST_EXIST);

    // Create the category.
    $categoryname = get_string('mycertificates', 'theme_edunao123');
    $category = new core_user\output\myprofile\category('mycertificates', $categoryname, 'contact');
    $tree->add_category($category);

    // If we are viewing certificates that are not for the currently logged in user then do a capability check.
    if (($user->id != $USER->id) && !\tool_certificate\permission::can_view_list($user->id)) {
        return;
    }

    // Check if there are certificates to display.
    if (\tool_certificate\certificate::count_issues_for_user($user->id) == 0) {
        return;
    }

    // Prepare my certificates table.
    $page = optional_param('page', 0, PARAM_INT);
    $perpage = optional_param('perpage', \tool_certificate\certificate::ISSUES_PER_PAGE, PARAM_INT);
    $pageurl = new moodle_url('/user/profile.php', ['userid' => $user->id, 'page' => $page, 'perpage' => $perpage]);

    $table = new \theme_edunao123\output\coursecertificate\my_certificates_table($user->id, null);
    $table->define_baseurl($pageurl);

    // Do not display the same heading twice
    $config = get_config('theme_edunao123');
    $title = '';
    if ($config->hide_category_title) {
        $title = \html_writer::tag('h3', get_string('mycertificates', 'theme_edunao123'), array('class' => 'lead'));
    }

    // Add content to the category.
    $node = new core_user\output\myprofile\node(
        'mycertificates',
        '',
        $title,
        null,
        null,
        $table->output_html($perpage)
    );
    $tree->add_node($node);
}

function theme_edunao123_css_postprocess($css, $config) {
    global $CFG, $PAGE;

    if (str_contains($css, '.impossible-editor')) {
        $urls = $PAGE->theme->css_urls($PAGE);
        foreach ($urls as $url) {
            $css .= file_get_contents($url);
        }
        $css .= " /* All the site CSS added successfully */ ";
    }

    return $css;
}
