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

defined('MOODLE_INTERNAL') || die();

// Load boost_union config.
require_once($CFG->dirroot . '/theme/boost_union/config.php');

$THEME->name = 'edunao123';
$THEME->parents = ['boost_union', 'boost'];

$THEME->scss = function($theme) {
    global $CFG;

    require_once($CFG->dirroot.'/theme/boost_union/lib.php');

    $scss = theme_boost_union_get_main_scss_content($theme);

    // Include post.scss from Edunao.
    $scss .= file_get_contents($CFG->dirroot . '/theme/edunao123/scss/edunao123/post.scss');

    return $scss;
};
