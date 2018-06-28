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

/**
 * This class contains the test cases for the tag_score class
 *
 */
class local_enva_tag_scores_testcase extends \local_enva\quiz_test_base {
    
    
    public function test_tagged_simple_quiz_results() {
        // We assume we have one course and one user
        $this->resetAfterTest();
        list ($user, $course) =
            $this->create_tagged_courses_and_quizes(8,1,array('délivrance','prescription'));
        $ts = new \local_enva\tag_scores($course->id, $user->id);
        $table = $ts->compute();
        $this->assertSame(array (
            'délivrance' => 1.0,
            'prescription' => 1.0,
        ),$table);
    }
    
    public function test_tagged_simple_quiz_results_with_failures() {
        // We assume we have one course and one user
        $this->resetAfterTest();
        list ($user, $course) =
            $this->create_tagged_courses_and_quizes(8,1,array('délivrance','prescription'),2);
        $ts = new \local_enva\tag_scores($course->id, $user->id);
        $table = $ts->compute();
        $this->assertSame(array (
            'délivrance' => 0.875,
            'prescription' => 0.875,
        ),$table);
    }

    
    public function test_tagged_several_quiz_results() {
        // We assume we have one course and one user
        $this->resetAfterTest();
        list ($user, $course) =
            $this->create_tagged_courses_and_quizes(8,5,array('délivrance','prescription', 'stupéfiant'));
        $ts = new \local_enva\tag_scores($course->id, $user->id);
        $table = $ts->compute();
        $this->assertSame(array (
            'délivrance' => 1.0,
            'prescription' => 1.0,
            'stupéfiant' => 1.0,
        ),$table);
    }
    
    protected function create_tagged_courses_and_quizes($num_questions_per_quiz, $num_quizzes, $tagnames,$numberfailuresperquiz = 0 ) {
    
        // Create a user
        $user = $this->getDataGenerator()->create_user();
    
        // Create a course.
        $course = $this->getDataGenerator()->create_course();
    
        // Enroll user in the course
        enrol_try_internal_enrol($course->id, $user->id);
        for($quizn = 0; $quizn < $num_quizzes; $quizn++) {
            $this->create_tagged_quizzes($num_questions_per_quiz, $tagnames, $user, $course,$numberfailuresperquiz);
        }
       
        return array($user,$course);
    }
}
/*
SELECT
    quba.id AS qubaid,
    quba.contextid,
    quba.component,
    quba.preferredbehaviour,
    qa.id AS questionattemptid,
    qa.questionusageid,
    qa.slot,
    qa.behaviour,
    qa.questionid,
    qa.variant,
    qa.maxmark,
    qa.minfraction,
    qa.maxfraction,
    qa.flagged,
    qa.questionsummary,
    qa.rightanswer,
    qa.responsesummary,
    qa.timemodified,
    qas.id AS attemptstepid,
    qas.sequencenumber,
    qas.state,
    qas.fraction,
    qas.timecreated,
    qas.userid,
    qasd.name,
    qasd.value

FROM      mdl_question_usages            quba
LEFT JOIN mdl_question_attempts          qa   ON qa.questionusageid    = quba.id
LEFT JOIN mdl_question_attempt_steps     qas  ON qas.questionattemptid = qa.id
LEFT JOIN mdl_question_attempt_step_data qasd ON qasd.attemptstepid    = qas.id

WHERE
    quba.id IN (SELECT quiza.uniqueid FROM mdl_quiz_attempts quiza WHERE quiza.quiz = 7 AND quiza.userid = 2 AND preview = 0 AND state IN ('finished', 'abandonned'))

ORDER BY
    quba.id,
    qa.slot,
    qas.sequencenumber
 


 */