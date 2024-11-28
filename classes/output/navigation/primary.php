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
    /**
     * Combine the various menus into a standardized output.
     *
     * Modifications compared to the original function:
     * * Build the smart menus and its items as navigation nodes.
     * * Generate the nodes for different locations based on the menus locations.
     * * Combine the smart menus nodes with core primary menus.
     *
     * @param renderer_base|null $output
     * @return array
     */
    public function export_for_template(?renderer_base $output = null): array {
        global $DB;

        // Create smart menu cache.
        $cache = \cache::make('theme_boost_union', 'smartmenus');

        // Check if the smart menus are already there in the cache.
        if (!$cache->get(\theme_boost_union\smartmenu::CACHE_MENUSLIST)) {
            // If the smart menu feature is not installed at all, use the parent function.
            // This will help to avoid hickups during a theme upgrade.
            $dbman = $DB->get_manager();
            if (!$dbman->table_exists('theme_boost_union_menus')) {
                return parent::export_for_template($output);
            }
        }

        if (!$output) {
            $output = $this->page->get_renderer('core');
        }

        // Generate the menus and its items into nodes.
        $smartmenus = smartmenu::build_smartmenu();
        // Smartmenus not created, then fallback to core navigation.
        if (empty($smartmenus)) {
            return parent::export_for_template($output);
        }

        // Get the menus for the main menu.
        $mainmenu = smartmenu::get_menus_forlocation(\theme_boost_union\smartmenu::LOCATION_MAIN, $smartmenus);

        // Separate the menus for the menubar.
        $menubarmenus = smartmenu::get_menus_forlocation(\theme_boost_union\smartmenu::LOCATION_MENU, $smartmenus);

        // Separate the menus for the user menus.
        $locationusermenus = smartmenu::get_menus_forlocation(\theme_boost_union\smartmenu::LOCATION_USER, $smartmenus);

        // Separate the menus for the bottom menu.
        $locationbottom = smartmenu::get_menus_forlocation(\theme_boost_union\smartmenu::LOCATION_BOTTOM, $smartmenus);

        // Merge the smart menu nodes which contain the main menu location with the primary and custom menu nodes.
        $mainsmartmenumergedcustom = array_merge($this->get_custom_menu($output), $mainmenu);
        $menudata = (object) $this->merge_primary_and_custom($this->get_primary_nav(), $mainsmartmenumergedcustom);
        $moremenu = new \core\navigation\output\more_menu((object) $menudata, 'navbar-nav', false);

        // Menubar.
        // Items of menus only added in the menubar.
        // Removed the menu nodes from menubar, each item will be displayed as menu in menubar.
        if (!empty($menubarmenus)) {
            $menubarmoremenu = new \core\navigation\output\more_menu((object) $menubarmenus, 'navbar-nav-menu-bar', false);
        }

        // Bottom bar.
        // Include the menu navigation menus to the mobile menu when the bottom bar doesn't have any menus.
        $mergecustombottommenus = array_merge($this->get_custom_menu($output), $locationbottom);
        $mobileprimarynav = (!empty($locationbottom))
            ? $this->merge_primary_and_custom($this->get_primary_nav(), $mergecustombottommenus, true)
            : $this->merge_primary_and_custom($this->get_primary_nav(), $mainsmartmenumergedcustom, true);

        if (!empty($mobileprimarynav)) {
            $bottombar = new \core\navigation\output\more_menu((object) $mobileprimarynav, 'navbar-nav-bottom-bar', false);
            $bottombardata = $bottombar->export_for_template($output);
            $bottombardata['drawer'] = (!empty($locationbottom)) ? true : false;
        }

        // Usermenu.
        // Merge the smartmenu nodes which contains the location for user menu, with the default core user menu nodes.
        $languagemenu = new \core\output\language_menu($this->page);
        $usermenu = $this->get_user_menu($output);
        $this->build_usermenus($usermenu, $locationusermenus);

        // Check if any of the smartmenus are going to be included on the page.
        // This is used as flag to include the smart menu's JS file in mustache templates later
        // as well as for controlling the smart menu SCSS.
        $includesmartmenu = (!empty($mainmenu) || !empty($menubarmenus) || !empty($locationusermenus) || !empty($locationbottom));

        return [
            'mobileprimarynav' => $mobileprimarynav,
            'moremenu' => $moremenu->export_for_template($output),
            'menubar' => isset($menubarmoremenu) ? $menubarmoremenu->export_for_template($output) : false,
            'lang' => !isloggedin() || isguestuser() ? $languagemenu->export_for_template($output) : [],
            'user' => $usermenu ?? [],
            'bottombar' => $bottombardata ?? false,
            'includesmartmenu' => $includesmartmenu ? true : false,
        ];
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

        if (!is_siteadmin() && $restrict) {
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
