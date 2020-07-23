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
 * Upgrade scripts
 * @package   local_pharmaco
 * @copyright 2018-2020, SAS CALL Learning
 * @author Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;
/**
 * Upgrade
 *
 * @param $oldversion
 * @return bool
 * @throws coding_exception
 * @throws dml_exception
 * @throws downgrade_exception
 * @throws upgrade_exception
 */
function xmldb_local_pharmaco_upgrade($oldversion) {
    global $CFG, $DB;
    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    $success = true;

    if ($oldversion < 2018062200) {
        $success = pharmaco_setups();
        upgrade_plugin_savepoint(true, 2018062200, 'local', 'pharmaco');
    }

    if ($oldversion < 2018062204) {
        $success = pharmaco_setups();
        upgrade_plugin_savepoint(true, 2018062204, 'local', 'pharmaco');
    }

    return $success;
}
