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

namespace local_enva\task;
defined('MOODLE_INTERNAL') || die();

global $CFG;
include_once($CFG->dirroot . '/local/enva/lib.php');
/**
 * Cron Task to ensure users are registered to the selected test course in any case
 * (this is useful if we change for example the test course)
 * @package local_enva
 */
class register_users_task  extends \core\task\scheduled_task {
    
    public function get_name () {
        return get_string('registerusersintest','local_enva');
    }
    
    /**
     * Make sure users keeps on being registered in the course
     */
    public function execute() {
        global $DB;
        $externalroleid = $DB->get_field('role','id',array ('shortname' => ENVA_EXTERNAL_ROLE_SHORTNAME));
        $userlist = get_role_users($externalroleid, \context_system::instance());
        
        // Then we make sure that each users in the external role is really
        // First, get the "selection"/quiz courseid
        $selcourseid = \local_enva\helper::get_test_course_id();
        
        foreach($userlist as $u) {
            \local_enva\user_registration::register_to_test_course($u->id);
            
            $ccompletion = new \completion_completion(array('course' => $selcourseid, 'userid' => $u->id));
            if ($ccompletion->is_complete()) {
                // The if the course is completed, then register to the other external courses
                \local_enva\user_registration::register_user_to_external_courses($u->id);
            }
        }
        
    }
}