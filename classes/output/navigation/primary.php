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
 * Theme Edunao - Primary navigation render.
 *
 * @package    theme_edunao
 * @copyright  2024 Mako Digital <admin@mako.digital>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_edunao\output\navigation;

use renderer_base;
use theme_edunao\smartmenu;

class primary extends \theme_boost_union\output\navigation\primary {
    /** @var \moodle_page $page the moodle page that the navigation belongs to */
    private $page = null;

    /**
     * primary constructor.
     * @param \moodle_page $page
     */
    public function __construct($page) {
        $this->page = $page;
        parent::__construct($page);
    }

    /**
     * Get/Generate the user menu.
     *
     * Modifications compared to the original function:
     * * Add a 'Set preferred language' link to the lang menu if the addpreferredlang setting is enabled in Boost Union.
     *
     * @param renderer_base $output
     * @return array
     */
    public function get_user_menu(renderer_base $output): array {
        $parentoutput = parent::get_user_menu($output);
        
        // Remove link to preferences page
        $restrict = get_config('theme_edunao', 'restrict_preferences');

        if (!is_siteadmin() && (isset($parentoutput) && isset($parentoutput['items'])) && $restrict) {
            $newitems = [];
            foreach ($parentoutput['items'] as $key => $item) {
                if ($item->itemtype == 'link' && $item->titleidentifier == 'preferences,moodle') {
                    continue;
                }
                $newitems[] = $item;
            }
            $parentoutput['items'] = $newitems;
        }

        // Return the output.
        return $parentoutput;
    }
}
