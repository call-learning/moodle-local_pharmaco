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
 * Unit tests for the tag score class which build an array of scores
 * for each tag on a given course
 *
 * @package   local_enva
 * @category  phpunit
 * @copyright 2018, CALL Learning SAS
 * @author Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/local/enva/locallib.php');

/**
 * This class contains the test cases for the tag_score class
 *
 */
class local_course_completion_observer_testcase extends advanced_testcase {
    
    const MAX_COURSES = 8;
    
    public function test_user_completed_course() {
        global $DB;
        // We assume we have one course and one user
        $this->resetAfterTest();
        // Create a user
        $user = $this->getDataGenerator()->create_user();
        // Create a course.
        $course = $this->getDataGenerator()->create_course();
        set_config('coursetocomplete',$course->id,'local_enva');
        
        // Now assign the user to the external role and check the user is registered
        $externalroleid = $DB->get_field('role','id',array ('shortname' => ENVA_EXTERNAL_ROLE_SHORTNAME));
        role_assign($externalroleid,$user->id,context_system::instance()); // Should trigger the test course assignment
        $context = context_course::instance($course->id);
        // Create external courses
        $externalcourses = [];
        // Create a courses.
        for($i = 0; $i < self::MAX_COURSES; $i++)  {
            $c =  $this->getDataGenerator()->create_course();
            core_tag_tag::add_item_tag('core', 'course', $c->id,
                context_course::instance($c->id), ENVA_EXTERNAL_COURSE_TAG_NAME); // Tag courses as if external
            $externalcourses[] = $c;
        }
        
        //  Now let's complete this course
        $ccompletion = new completion_completion(array('course' => $course->id, 'userid' => $user->id));
        $ccompletion->mark_complete(time());
        
        // User should now be registered in the courses tagged as external
        foreach($externalcourses as $c) {
            $context = context_course::instance($c->id);
            $this->assertTrue(is_enrolled($context, $user->id));
        }
    
    }
}
