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
 * @package   local_pharmaco
 * @copyright 2018-2020, SAS CALL Learning
 * @author Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_pharmaco\quiz_test_base;
use local_pharmaco\tag_scores;

defined('MOODLE_INTERNAL') || die();

global $CFG;

/**
 * This class contains the test cases for the tag_score class
 *
 * @copyright 2018-2020, SAS CALL Learning
 * @author Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_pharmaco_tag_scores_testcase extends quiz_test_base {

    public function test_tagged_simple_quiz_results() {
        // We assume we have one course and one user.
        $this->resetAfterTest();
        list ($user, $course) =
            $this->create_tagged_courses_and_quizes(8, 1, array('délivrance', 'prescription'));
        $ts = new tag_scores($course->id, $user->id);
        $table = $ts->compute();
        $this->assertSame(array(
            'délivrance' => array('mark' => 4.0, 'coef' => 4.0),
            'prescription' => array('mark' => 4.0, 'coef' => 4.0),
        ), $table);
    }

    protected function create_tagged_courses_and_quizes($numquestionsperquiz, $numquizzes, $tagnames,
        $numberfailuresperquiz = 0) {

        // Create a user.
        $user = $this->getDataGenerator()->create_user();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Enroll user in the course.
        enrol_try_internal_enrol($course->id, $user->id);
        for ($quizn = 0; $quizn < $numquizzes; $quizn++) {
            $this->create_tagged_quizzes($numquestionsperquiz, $tagnames, $user, $course, $numberfailuresperquiz);
        }

        return array($user, $course);
    }

    public function test_tagged_simple_quiz_results_with_failures() {
        // We assume we have one course and one user.
        $this->resetAfterTest();
        list ($user, $course) =
            $this->create_tagged_courses_and_quizes(8, 1, array('délivrance', 'prescription'), 2);
        $ts = new tag_scores($course->id, $user->id);
        $table = $ts->compute();
        $this->assertSame(array(
            'délivrance' => array('mark' => 3.0, 'coef' => 4.0),
            'prescription' => array('mark' => 3.0, 'coef' => 4.0),
        ), $table);
    }

    public function test_tagged_several_quiz_results() {
        // We assume we have one course and one user.
        $this->resetAfterTest();
        list ($user, $course) =
            $this->create_tagged_courses_and_quizes(8, 5, array('délivrance', 'prescription', 'stupéfiant'));
        $ts = new tag_scores($course->id, $user->id);
        $table = $ts->compute();
        $this->assertSame(array(
            'délivrance' => array('mark' => 15.0, 'coef' => 15.0),
            'prescription' => array('mark' => 15.0, 'coef' => 15.0),
            'stupéfiant' => array('mark' => 10.0, 'coef' => 10.0),
        ), $table);
    }
}
