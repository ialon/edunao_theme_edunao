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
 * Theme Edunao - Settings file
 *
 * @package    theme_edunao
 * @copyright  2024 Mako Digital <admin@mako.digital>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/user/profile/lib.php');

if (!during_initial_install() && $ADMIN->fulltree) {
    global $USER;

    $settings = new theme_boost_admin_settingspage_tabs('themesettingedunao', get_string('configtitle', 'theme_edunao'));
    $page = new admin_settingpage('themeedunao', get_string('generalsettings', 'theme_edunao'));


    // Select which categories to display in user profile page
    $tree = core_user\output\myprofile\manager::build_tree($USER, true);
    $categories = [];
    foreach ($tree->categories as $category) {
        $categories[$category->name] = $category->title;
    }

    $name = 'theme_edunao/myprofile_categories';
    $title = get_string('myprofile_categories', 'theme_edunao');
    $description = get_string('myprofile_categories_desc', 'theme_edunao');
    $setting = new admin_setting_configmulticheckbox($name, $title, $description, ['contact' => 1], $categories);
    $page->add($setting);


    // Display category title in user profile page
    $yesnooption = [
        1 => get_string('yes'),
        0 => get_string('no')
    ];

    $name = 'theme_edunao/display_category_title';
    $title = get_string('display_category_title', 'theme_edunao');
    $description = get_string('display_category_title_desc', 'theme_edunao');
    $setting = new admin_setting_configselect($name, $title, $description, 0, $yesnooption);
    $page->add($setting);


    $settings->add($page);
}
