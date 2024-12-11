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

defined('MOODLE_INTERNAL') || die();

$THEME->name = 'edunao';
$THEME->sheets = [];
$THEME->editor_sheets = [];
$THEME->parents = ['boost_union', 'boost'];
$THEME->enable_dock = false;
$THEME->yuicssmodules = [];
$THEME->extrascsscallback = 'theme_boost_union_get_extra_scss';
$THEME->prescsscallback = 'theme_boost_union_get_pre_scss';
$THEME->precompiledcsscallback = 'theme_boost_union_get_precompiled_css';
$THEME->rendererfactory = 'theme_overridden_renderer_factory';
$THEME->requiredblocks = '';
$THEME->addblockposition = BLOCK_ADDBLOCK_POSITION_FLATNAV;
$THEME->haseditswitch = true;
$THEME->removedprimarynavitems = explode(',', get_config('theme_boost_union', 'hidenodesprimarynavigation'));

$THEME->scss = function($theme) {
    global $CFG;

    require_once($CFG->dirroot.'/theme/boost_union/lib.php');

    $scss = theme_boost_union_get_main_scss_content($theme);

    // Include post.scss from Edunao.
    $scss .= file_get_contents($CFG->dirroot . '/theme/edunao/scss/edunao/post.scss');

    return $scss;
};

$THEME->layouts = [];
