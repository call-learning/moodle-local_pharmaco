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

namespace local_enva;

defined('MOODLE_INTERNAL') || die();

/**
 * This class contains the test cases for the tag_score class
 *
 */
abstract class quiz_test_base extends \advanced_testcase {
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
    protected function create_tagged_quizzes($num_questions_per_quiz, $tagnames, $user, $course, $numberfailuresperquiz = 0 ) {
        $timenow = time(); // Update time now, in case the server is running really slowly.
        // Create the quizzes

        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $quiz = $quizgenerator->create_instance(array('course' => $course->id, 'questionsperpage' => 0,
            'grade' => 100.0, 'sumgrades' => 2, 'preferredbehaviour' => 'immediatefeedback'));
        $cm = get_coursemodule_from_instance('quiz', $quiz->id);
        // Create the questions.
        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category();
        $questions = array();
        for ($i = 0; $i < $num_questions_per_quiz ; $i++) {
            $sa = $generator->create_question('shortanswer', null, array('category' => $cat->id));
            $tagname =  $tagnames[$i % count($tagnames)];

            // Add the tag to the question
            \core_tag_tag::add_item_tag('core_question', 'question', $sa->id,
                \context::instance_by_id($cat->contextid), $tagname);
            $questions[] = $sa;
            // Add the question to the quiz
            quiz_add_quiz_question($sa->id, $quiz);
        }
        /* Create the helper objects */
        $quizobj = new \quiz($quiz, $cm, $course);
        $quba = \question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);

        // Create and save the quiz attempt
        $quizattempt = quiz_create_attempt($quizobj, 1, null, $timenow, false, $user->id);
        $quizattempt = quiz_start_new_attempt($quizobj, $quba, $quizattempt, 1, $timenow);
        $quizattempt = quiz_attempt_save_started($quizobj, $quba, $quizattempt); // Save attempt and get a unique id
        // Answer all questions right
        $quizattemptobj = \quiz_attempt::create($quizattempt->id);
        $slottableresponses = [];
        foreach ($quba->get_slots() as $slot) {
            $correctresponse = $quba->get_correct_response($slot);
            if ($numberfailuresperquiz>0) {
                $numberfailuresperquiz --;
                $slottableresponses[$slot] = array('answer'=>'incorrectresponse');
            } else {
                $slottableresponses[$slot] = $correctresponse;
            }
        }
        $quizattemptobj->process_submitted_actions($timenow + 300, false, $slottableresponses);
        $quizattemptobj->process_finish($timenow + 600, false);
        $quizattemptobj->process_finish($timenow, false); // Save quiz + question states

    }
}
