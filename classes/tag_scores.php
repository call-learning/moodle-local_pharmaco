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
use mod_quiz\question\qubaids_for_users_attempts;

defined('MOODLE_INTERNAL') || die();
global $CFG;
include_once ($CFG->dirroot.'/question/engine/lib.php');
include_once ($CFG->dirroot.'/mod/quiz/locallib.php');
/**
 *  Class to build an array of tags vs scores for a given course
 *  We check in every quiz of the course what is the score and we match it to the tags for each quiz
 *  TODO: if the computation is too long, we might need to cache this information
 */
class tag_scores {
    private $courseid;
    private $userid;
    
    public function __construct($courseid, $userid) {
        $this->courseid = $courseid;
        $this->userid = $userid;
    }
    /**
     * Get a list of tag and their matching score for a given user/course
     * @return an associative array with the tag rawname and the score as an average percentage scored for this tag
     */
    public function compute() {
        $modinfo = \course_modinfo::instance($this->courseid,$this->userid);
        $allquizzesmods = $modinfo->get_instances_of('quiz');
        $dm = new \question_engine_data_mapper();
        $tagtable = array();
        foreach($allquizzesmods as $qmod) {
            // For each question / slot get the tag + the score
            $qubas = $dm->load_questions_usages_by_activity(
                new \mod_quiz\question\qubaids_for_users_attempts($qmod->instance, $this->userid));
            foreach ($qubas as $quba) {
                foreach ($quba->get_attempt_iterator() as $qa) {
                    $question = $qa->get_question();
                    $tagarray = \core_tag_tag::get_item_tags('core_question', 'question', $question->id);
                    if (!empty($tagarray)) {
                        $mark =  $qa->get_mark();
                        $markfract = $qa->get_fraction(); // Question fraction is the percentage for this question
                        $coef =  $qa->get_max_mark(); // This is really the question weight, not the max, the max mark is
                        // obtained using max_fraction/min_fraction
                        
                        $certainty = $qa->get_last_behaviour_var('certainty');
                        if ($certainty) {
                            // We are sure now that we have a CBM question engine for this question, so we will have to make sure we
                            // set the mark to a value between 0 and 1 (the range is -6 to 3)
                            $minmark = $qa->get_min_fraction();
                            $maxmark = $qa->get_max_fraction();
                            $markfract = ($markfract - $minmark)/($maxmark-$minmark);
                        }
                        // For other types of questions, mark should be a float between 0 and 1
                        
                        // We then multiply the mark by the coef, so the total mark is mark/coef
                        // We also do this for each tag
                        foreach($tagarray as $tag) {
                            if (!isset($tagtable[$tag->rawname])) {
                                $tagtable[$tag->rawname] = array('mark' => $markfract * $coef, 'coef' => $coef);
                            } else {
                                $tagtable[$tag->rawname]['mark'] += $markfract * $coef;
                                $tagtable[$tag->rawname]['coef'] += $coef;
                            }
                        }
                    }
                }
            }
        }
        return $tagtable;
    }

}