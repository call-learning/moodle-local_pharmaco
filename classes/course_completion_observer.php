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
 * Course completion observer
 *
 * @package   local_pharmaco
 * @copyright 2018-2020, SAS CALL Learning
 * @author Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_pharmaco;

use coding_exception;
use core\event\course_completed;

defined('MOODLE_INTERNAL') || die();

/**
 * Class course_completion_observer
 *
 * @copyright 2018-2020, SAS CALL Learning
 * @author Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_completion_observer {

    /**
     * Check if the selection course has been completed.
     *
     * If it has been completed
     * by an external user (belongs to the EXTERNAL role) then, we enroll him/her into the 8 courses
     *
     * @param course_completed $event
     * @throws coding_exception
     */
    static public function completed(course_completed $event) {
        $eventdata = $event->get_record_snapshot('course_completions', $event->objectid);
        $selcourseid = helper::get_test_course_id();
        if ($eventdata->course == $selcourseid) {
            // If the current user completed the test course, try to register to the other external courses.
            user_registration::register_user_to_external_courses($eventdata->userid);
        }

    }

}