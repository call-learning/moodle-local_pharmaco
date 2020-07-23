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
 * Installation script
 *
 * @package   local_pharmaco
 * @copyright 2018-2020, SAS CALL Learning
 * @author Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;
global $CFG;
require_once($CFG->dirroot . '/local/pharmaco/locallib.php');

/**
 * Installation for pharmaco script
 *
 * @return bool
 * @throws coding_exception
 * @throws dml_exception
 */
function xmldb_local_pharmaco_install() {
    global $CFG, $DB, $SITE;

    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.
    $success = true;
    $success = pharmaco_setups();
    return $success;
}
