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
 * Course list by score
 *
 * @package   local_pharmaco
 * @copyright 2018-2020, SAS CALL Learning
 * @author Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_pharmaco;

use completion_info;
use core_completion\progress;
use core_tag_tag;

defined('MOODLE_INTERNAL') || die();

/**
 * Utility class to build an array of courses
 *
 * Course are orted by their scored tags(see @tag_scores for the way we get the scores relatives to the tags)
 * TODO: if the computation is too long, we might need to cache this information
 *
 * @copyright 2018-2020, SAS CALL Learning
 * @author Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_list_by_score {
    /**
     * @var array $tagscorearray
     */
    private $tagscorearray;
    /**
     * @var int $userid
     */
    private $userid;

    /**
     * course_list_by_score constructor.
     *
     * @param int $userid
     * @param array $tagscorearray
     */
    public function __construct($userid, $tagscorearray) {
        $this->userid = $userid;
        $this->tagscorearray = $tagscorearray;
    }

    /**
     * Get a list of courses the user is registered into and calculate the average of their
     * scores depending on the tags they are assigned to
     *
     * @param string $sortkey : a key (sql) on which we sort the course list
     *  (can be 'visible DESC, sortorder ASC', visible DESC, fullname ASC...),
     * @return array an array of courses with additional data such as tags and calculated scores
     */
    public function get_list($sortkey = 'visible DESC, sortorder ASC') {

        $courses = enrol_get_users_courses($this->userid, true, '*', $sortkey);
        foreach ($courses as $i => $c) {
            // Change within the loop.
            $course = $c;
            $this->add_completion_info($course);
            $this->add_tags($course);
            $this->compute_score($course);
            $courses[$i] = $course;
        }
        return $courses;
    }

    /**
     * Add completion info
     *
     * @param object $course
     */
    protected function add_completion_info(&$course) {

        // Add the course completion info.
        $completion = new completion_info($course);

        // First, let's make sure completion is enabled.
        if (!$completion->is_enabled()) {
            return;
        }

        $percentage = progress::get_course_progress_percentage($course);
        if (!is_null($percentage)) {
            $percentage = floor($percentage);
        }

        $course->completed = $completion->is_course_complete($this->userid);
        $course->progress = $percentage;

    }

    /**
     * Add tags
     *
     * @param object $course
     */
    protected function add_tags(&$course) {
        $course->tags = core_tag_tag::get_item_tags('core', 'course', $course->id);
    }

    /**
     * Compute score
     *
     * @param object $course
     */
    protected function compute_score(&$course) {
        $scorepercent = 0;

        // We receive a tagscore array that is composed of mark weighed by its coef, and the total coef
        // So the mean of this mark is mark/coef (will give a mark between 0 and 1).
        if (!empty($course->tags)) {
            foreach ($course->tags as $ctag) {
                if (array_key_exists($ctag->rawname, $this->tagscorearray)) {
                    $tagscore = $this->tagscorearray[$ctag->rawname];
                    if ($tagscore['coef'] > 0) {
                        if ($scorepercent == 0) {
                            $scorepercent = $tagscore['mark'] / $tagscore['coef'];
                        } else {
                            $scorepercent = ($scorepercent + $tagscore['mark'] / $tagscore['coef']) / 2;
                        }
                    }
                }
            }
        }
        $course->score = $scorepercent;
    }
}