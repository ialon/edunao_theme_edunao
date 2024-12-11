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
 * Theme Edunao - Core renderer
 *
 * @package    theme_edunao
 * @copyright  2024 Mako Digital <admin@mako.digital>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_edunao\output;

class core_renderer extends \theme_boost_union\output\core_renderer {
    /**
     * Renders empty "breadcrumb" for all pages in edunao.
     *
     * @return string the HTML for the navbar.
     */
    public function navbar(): string {
        global $SERVER;
    
        $url = $_SERVER['PHP_SELF'];

        $hide = get_config('theme_edunao', 'hide_breadcrumbs');

        if ($hide && str_contains($url, "/user/")) {
            return '';
        } else {
            return parent::navbar();
        }
    }
}
