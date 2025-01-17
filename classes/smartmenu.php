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
 * Menu controller for managing menus and menu items. Build menu for different locations.
 *
 * @package    theme_boost_union
 * @copyright  2023 bdecent GmbH <https://bdecent.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_edunao123;

use cache;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/theme/boost_union/smartmenus/menulib.php');

/**
 * The menu controller handles actions related to managing menus.
 *
 * This controller provides methods for listing available menus, creating new menus,
 * updating existing menus, deleting menus, and sorting the order of menus.
 *
 * @package    theme_boost_union
 * @copyright  2023 bdecent GmbH <https://bdecent.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class smartmenu extends \theme_boost_union\smartmenu {
    /**
     * Initialize the build of smart menus, Fetch the list of menus and init the build for each menu.
     *
     * @return array An array of SmartMenu nodes.
     */
    public static function build_smartmenu() {
        global $USER;

        $nodes = [];

        // Verify the language changes in user session, if changed than purge the menus and items cache for the user session.
        self::verify_lang_session_changes();

        $cache = cache::make('theme_boost_union', 'smartmenus');
        // Fetch the list of menus from cache.
        $topmenus = $cache->get(self::CACHE_MENUSLIST);
        // Get top level menus, store the menus to cache.
        if (empty($topmenus)) {
            $topmenus = self::get_menus();
            $cache->set(self::CACHE_MENUSLIST, $topmenus);
        }

        if (empty($topmenus)) {
            // Still menus are not created.
            return false;
        }

        // Test the flag to purge the cache is set for this user.
        $removecache = (get_user_preferences('theme_boost_union_menu_purgesessioncache', false) == true);

        foreach ($topmenus as $menu) {
            // Need to purge the menus for user, remove the cache before build.
            if ($removecache) {
                // Purge the menu cache for this user.
                $cachekey = "{$menu->id}_u_{$USER->id}";
                $cache->delete($cachekey);
            }

            if ($node = self::instance($menu)->build($removecache)) {
                if (isset($node->menudata)) {
                    $nodes[] = $node;
                } else {
                    $nodes = array_merge($nodes, array_values((array) $node));
                }
            }
        }

        // Menus are purged in the build method when needed, then clear the user preference of purge cache.
        \theme_boost_union\smartmenu_helper::clear_user_cachepreferencemenu();

        return $nodes;
    }

    /**
     * Verifies and handles changes in the session language.
     * Clears cached smart menus and items when the user changes the language using the language menu.
     *
     * @return void
     */
    protected static function verify_lang_session_changes() {
        global $SESSION, $DB, $COURSE, $USER;
        // Make sure the lang is updated for the session.
        if ($lang = optional_param('lang', '', PARAM_SAFEDIR)) {
            // Confirm the cache is not already purged for this language change. To avoid multiple purge.
            if (!isset($SESSION->prevlang) || $SESSION->prevlang != $lang) {
                // Set the purge cache preference for this session user. Cache will purged in the build_smartmenu method.
                \theme_boost_union\smartmenu_helper::set_user_purgecache($USER->id);
                $SESSION->prevlang = $lang; // Save this lang for verification.

                // Update user with new language.
                $context = \context_course::instance($COURSE->id);
                if (isloggedin() && ($context instanceof \context_course && !is_guest($context))) {
                    $user = $DB->get_record('user', ['id' => $USER->id], '*', MUST_EXIST);
                    $user->lang = $lang;
                    $DB->update_record('user', $user);

                    // Refresh user for $USER variable.
                    $USER = get_complete_user_data('id', $user->id);
                }
            }
        }
    }
}
