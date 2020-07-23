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
 * Unit tests for the course list
 *
 * @package   local_pharmaco
 * @copyright 2018-2020, SAS CALL Learning
 * @author Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_pharmaco\course_list_by_score;
use local_pharmaco\quiz_test_base;

defined('MOODLE_INTERNAL') || die();

global $CFG;

/**
 * This class contains the test cases for the course_list_by_score class
 *
 * @copyright 2018-2020, SAS CALL Learning
 * @author Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_pharmaco_course_list_by_score_testcase extends quiz_test_base {

    const MAX_COURSES = 8;

    const TAG_FOR_COURSES = array(
        array('prescription', 'preparation_extemporannées'),
        array('prescription', 'external_courses'),
        array('élimination_des_déchets', 'pharmacovigilance'));

    public function test_tagged_simple_quiz_results() {
        // We assume we have one course and one user.
        $this->resetAfterTest();

        // Create a user.
        $user = $this->getDataGenerator()->create_user();

        // Create MAX COURSES courses and tag them accordindly.
        $courses = [];
        for ($i = 0; $i < self::MAX_COURSES; $i++) {
            $c = $this->getDataGenerator()->create_course();
            if (isset(self::TAG_FOR_COURSES[$i])) {
                foreach (self::TAG_FOR_COURSES[$i] as $tagname) {
                    core_tag_tag::add_item_tag('core', 'course', $c->id, context_course::instance($c->id), $tagname);
                }
            }
            // Enrol user.
            enrol_try_internal_enrol($c->id, $user->id);
            $courses[] = $c;
        }

        $tagscores = array(
            'délivrance' => array('mark' => 1.0, 'coef' => 1.0),
            'prescription' => array('mark' => 1.0, 'coef' => 3.0),
            'élimination_des_déchets' => array('mark' => 2.0, 'coef' => 2.0),
            'pharmacovigilance' => array('mark' => 2.0, 'coef' => 4.0),
            'preparation_extemporannées' => array('mark' => 15.0, 'coef' => 0.0)
        );
        $cl = new course_list_by_score($user->id, $tagscores);
        $courselist = $cl->get_list();
        $results = array(
            'tc_8' => 0,
            'tc_7' => 0,
            'tc_6' => 0,
            'tc_5' => 0,
            'tc_4' => 0,
            'tc_3' => 0.75, // (1.0 + 0.5)/2.
            'tc_2' => 1 / 3, // 0.3 (external course does not count).
            'tc_1' => 1 / 3, // 0.333 (we ignore the tag with coef 0).
        );
        foreach ($courselist as $c) {
            $this->assertTrue(
                $results[$c->shortname] == $c->score,
                "The course {$c->fullname} should have a score of {$results[$c->shortname]}, but has a score of {$c->score}"
            );
        }

    }

}