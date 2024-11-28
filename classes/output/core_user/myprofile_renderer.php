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

namespace theme_edunao\output\core_user;

use \core_user\output\myprofile\renderer;
use \core_user\output\myprofile\category;

// This line protects the file from being accessed by a URL directly.                                                               
defined('MOODLE_INTERNAL') || die();

class myprofile_renderer extends renderer {
    /**
     * Render a category.
     *
     * @param category $category
     *
     * @return string
     */
    public function render_category(category $category) {
        $config = get_config('theme_edunao');
        $allowed_categories = explode(',', $config->myprofile_categories);

        if (!in_array($category->name, $allowed_categories)) {
            return '';
        }

        $classes = $category->classes;
        if (empty($classes)) {
            $return = \html_writer::start_tag('section',
                array('class' => 'node_category card d-inline-block w-100 mb-3'));
            $return .= \html_writer::start_tag('div', array('class' => 'card-body'));
        } else {
            $return = \html_writer::start_tag('section',
                array('class' => 'node_category card d-inline-block w-100 mb-3' . $classes));
            $return .= \html_writer::start_tag('div', array('class' => 'card-body'));
        }

        if ($config->display_category_title) {
            $return .= \html_writer::tag('h3', $category->title, array('class' => 'lead'));
        }

        $nodes = $category->nodes;
        if (empty($nodes)) {
            // No nodes, nothing to render.
            return '';
        }
        $return .= \html_writer::start_tag('ul');
        foreach ($nodes as $node) {
            $return .= $this->render($node);
        }
        $return .= \html_writer::end_tag('ul');
        $return .= \html_writer::end_tag('div');
        $return .= \html_writer::end_tag('section');
        return $return;
    }
}

