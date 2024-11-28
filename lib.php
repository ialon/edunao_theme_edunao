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
 * @package    theme_edunao
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
function theme_edunao_after_require_login() {
    global $SERVER;

    $url = $_SERVER['PHP_SELF'];
    
    if ($url == '/user/preferences.php') {
        $restrict = get_config('theme_edunao', 'restrict_preferences');

        if (!is_siteadmin() && $restrict) {
            redirect('/user/profile.php');
        }
    }
}
