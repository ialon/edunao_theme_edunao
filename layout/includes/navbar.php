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
 * Theme Edunao - Navbar layout include.
 *
 * @package    theme_edunao123
 * @copyright  2024 Mako Digital <admin@mako.digital>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Include the template content for the navbar.
require_once(__DIR__ . '/../../../boost_union/layout/includes/navbar.php');

$templatecontext['navbardisplayclasses']['toggle'] = 'd-block d-md-none';
$templatecontext['navbardisplayclasses']['logolink'] = 'd-none d-md-flex';

// Hide main navbar.
$hide = get_config('theme_edunao123', 'hide_mainnavbar');
if ($hide) {
    $templatecontext['mobileprimarynav'] = [];
    if (isset($templatecontext['primarymoremenu']['nodearray'])) {
        $templatecontext['primarymoremenu']['nodearray'] = [];
    }

    $templatecontext['navbardisplayclasses']['toggle'] = 'd-none d-md-none';
    $templatecontext['navbardisplayclasses']['logolink'] = 'd-flex d-md-flex';
}
