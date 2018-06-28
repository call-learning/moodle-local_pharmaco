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
/*            $quizobj = \quiz::create($qmod->instance, $this->userid);
            $quizobj->preload_questions();
            $quizobj->load_questions();
            $quba = new \question_usage_by_activity('mod_quiz',$quizobj->get_context());
*/
            //$qbaids = new \mod_quiz\question\qubaids_for_users_attempts($quizobj->get_quizid(),$this->userid);
            // For each question / slot get the tag + the score
            $qubas = $dm->load_questions_usages_by_activity(
                new \mod_quiz\question\qubaids_for_users_attempts($qmod->instance, $this->userid));
            foreach ($qubas as $quba) {
                foreach ($quba->get_attempt_iterator() as $qa) {
                    $question = $qa->get_question();
                    $tagarray = \core_tag_tag::get_item_tags('core_question', 'question', $question->id);
                    $tag = reset($tagarray);
                    if ($tag) {
                        $mark = $quba->get_question_mark($qa->get_slot());
                        $maxmark = $quba->get_question_max_mark($qa->get_slot());
                        if ($maxmark > 0) {
                            $markpercent = $mark / $maxmark;
                            if (!isset($tagtable[$tag->rawname])) {
                                $tagtable[$tag->rawname] = $markpercent;
                            } else {
                                $tagtable[$tag->rawname] = ($tagtable[$tag->rawname] + $markpercent) / 2;
                            }
                        }
                    }
                }
            }
        }
        return $tagtable;
    }

}