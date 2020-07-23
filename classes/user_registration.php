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
 * Manages user registration
 *
 * @package   local_pharmaco
 * @copyright 2018-2020, SAS CALL Learning
 * @author Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_pharmaco;

use coding_exception;
use context_course;
use core\event\role_assigned;
use dml_exception;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/local/pharmaco/lib.php');

/**
 * Class user_registration: manages all aspect of External user registration in courses
 *
 * @copyright 2018-2020, SAS CALL Learning
 * @author Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_registration {
    /**
     * If the user has been assigned to an external role, then register this user onto the test course
     *
     * @param role_assigned $event
     * @throws coding_exception
     * @throws dml_exception
     */
    static public function register_to_test_course_when_assigned(role_assigned $event) {
        global $DB;
        $eventdata = $event->get_record_snapshot('role_assignments', $event->other['id']);
        // Check if the assigned role is the external role, if not ignore the rest of the process.
        $externalroleid = $DB->get_field('role', 'id', array('shortname' => PHARMACO_EXTERNAL_ROLE_SHORTNAME));
        if ($externalroleid != $eventdata->roleid) {
            return;
        }
        self::register_to_test_course($event->relateduserid);
    }

    /**
     * If the user has been assigned to an external role, then register this user onto the test course
     *
     * @param int $userid
     * @throws dml_exception
     */
    static public function register_to_test_course($userid) {
        global $DB;
        // First, get the "selection"/quiz courseid.
        $selcourseid = helper::get_test_course_id();
        if ($selcourseid) {
            $isexternaluser = helper::is_user_external_role($userid);
            if ($isexternaluser) {
                $context = context_course::instance($selcourseid);
                if (!is_enrolled($context, $userid)) {
                    $studentroleid = $DB->get_field('role', 'id', array('shortname' => 'student'));
                    // This is required for the completion module: the user should be a student.
                    enrol_try_internal_enrol($selcourseid, $userid, $studentroleid);
                }
            }
        }

    }

    /**
     * Register user to external courses (courses tagged with the external tag) if this user is an
     * external user
     *
     * @param int $userid
     * @throws dml_exception
     */
    static public function register_user_to_external_courses($userid) {
        $isexternaluser = helper::is_user_external_role($userid);

        // Here we enrol the user onto the external courses
        // First, get the "selection"/quiz courseid.
        if ($isexternaluser) {
            $extcourses = new external_courses(PHARMACO_EXTERNAL_COURSE_TAG_NAME);
            $extcourses->enrol_user_into_external_courses($userid);
        }
    }
}