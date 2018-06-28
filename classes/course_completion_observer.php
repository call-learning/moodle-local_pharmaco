<?php
/*
* This file is part of Totara LMS
*
* Copyright (C) 2010 onwards Totara Learning Solutions LTD
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
*/

/**
 * @package   local_enva
 * @copyright 2018, CALL Learning SAS
 * @author Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_enva;
defined('MOODLE_INTERNAL') || die();

class course_completion_observer {
    /**
     * Check if the selection course has been completed. If it has been completed
     * by an external user (belongs to the EXTERNAL role) then, we enroll him/her into the 8 courses
     */
    static public function completed(\core\event\course_completed $event) {
        $eventdata = $event->get_record_snapshot('course_completions', $event->objectid);
        $selcourseid = get_config('local_enva', 'coursetocomplete');
        if ($eventdata->course == $selcourseid) {
            // If the current user completed the test course, try to register to the other external courses
            user_registration::register_user_to_external_courses($eventdata->userid);
        }
        
        
    }
    
}