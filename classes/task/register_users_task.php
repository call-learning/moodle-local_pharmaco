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
 * @package   local_pharmaco
 * @copyright 2018, CALL Learning SAS
 * @author Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_pharmaco\task;

use completion_completion;
use context_system;
use core\task\scheduled_task;
use local_pharmaco\helper;
use local_pharmaco\user_registration;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/local/pharmaco/lib.php');
require_once($CFG->dirroot . '/completion/completion_completion.php');

/**
 * Cron Task to ensure users are registered to the selected test course in any case
 * (this is useful if we change for example the test course)
 *
 * @copyright 2018-2020, SAS CALL Learning
 * @author Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class register_users_task extends scheduled_task {

    public function get_name() {
        return get_string('registerusersintest', 'local_pharmaco');
    }

    /**
     * Make sure users keeps on being registered in the course
     */
    public function execute() {
        global $DB;
        $externalroleid = $DB->get_field('role', 'id', array('shortname' => PHARMACO_EXTERNAL_ROLE_SHORTNAME));
        $userlist = get_role_users($externalroleid, context_system::instance());

        // Then we make sure that each users in the external role is really
        // First, get the "selection"/quiz courseid.
        $selcourseid = helper::get_test_course_id();

        foreach ($userlist as $u) {
            user_registration::register_to_test_course($u->id);

            $ccompletion = new completion_completion(array('course' => $selcourseid, 'userid' => $u->id));
            if ($ccompletion->is_complete()) {
                // The if the course is completed, then register to the other external courses.
                user_registration::register_user_to_external_courses($u->id);
            }
        }

    }
}