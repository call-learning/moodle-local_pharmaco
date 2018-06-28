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
 * This class contains the test cases for the course_list_by_score class
 *
 */
class local_enva_course_list_by_score_testcase extends \local_enva\quiz_test_base {
    
    const MAX_COURSES = 8;
    
    const TAG_FOR_COURSES = array(
        array('prescription', 'preparation_extemporannées'),
        array('prescription','external_courses'),
        array('élimination_des_déchets', 'pharmacovigilance'));
    
    public function test_tagged_simple_quiz_results() {
        // We assume we have one course and one user
        $this->resetAfterTest();
        
        // Create a user
        $user = $this->getDataGenerator()->create_user();
        
        // Create MAX COURSES courses and tag them accordindly
        $courses = [];
        for ($i = 0; $i < self::MAX_COURSES; $i++) {
            $c = $this->getDataGenerator()->create_course();
            if (isset(self::TAG_FOR_COURSES[$i])) {
                foreach (self::TAG_FOR_COURSES[$i] as $tagname) {
                    \core_tag_tag::add_item_tag('core', 'course', $c->id, \context_course::instance($c->id), $tagname);
                }
            }
            // Enrol user
            enrol_try_internal_enrol($c->id, $user->id);
            $courses[] = $c;
        }
        
        $tag_scores = array(
            'délivrance' => 0.2,
            'prescription' => 0.3,
            'élimination_des_déchets' => 1.0,
            'pharmacovigilance' => 0.5,
            'preparation_extemporannées' => 0.0
        );
        $cl = new \local_enva\course_list_by_score($user->id, $tag_scores);
        $courselist = $cl->get_list();
        $results = array(
            'tc_8' => 0,
            'tc_7' => 0,
            'tc_6' => 0,
            'tc_5' => 0,
            'tc_4' => 0,
            'tc_3' => 0.75, // (1.0 + 0.5)/2
            'tc_2' => 0.3, // 0.3
            'tc_1' => 0.15, // (0.3+0)/2
        );
        foreach($courselist as $c) {
            $this->assertTrue(
                $results[$c->shortname] == $c->score,
                "The course {$c->fullname} should have a score of {$results[$c->shortname]}, but has a score of {$c->score}"
            );
        }
        
    }
    
}