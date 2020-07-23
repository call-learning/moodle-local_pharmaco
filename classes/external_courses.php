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
 * External user functions
 *
 * @package   local_pharmaco
 * @copyright 2018-2020, SAS CALL Learning
 * @author Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_pharmaco;

use context_course;
use core_tag_collection;
use core_tag_tag;

defined('MOODLE_INTERNAL') || die();

/**
 * Class to manage courses assigned to external users
 *
 * @copyright 2018-2020, SAS CALL Learning
 * @author Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external_courses {
    /**
     * @var string|string $externalcoursetag
     */
    private $externalcoursetag;

    /**
     * External_courses constructor.
     *
     * We provide the tag as parameter so we are not dependent on the value here
     *
     * @param string $externalcoursetag
     */
    public function __construct($externalcoursetag) {
        $this->externalcoursetag = $externalcoursetag;
    }

    /**
     * Register given user to the external courses
     * @param int $userid
     */
    public function enrol_user_into_external_courses($userid) {
        global $CFG;
        require_once($CFG->libdir . '/enrollib.php');

        $courselist = $this->get_external_courses_list();
        foreach ($courselist as $course) {
            $context = context_course::instance($course->id);
            if (!is_enrolled($context, $userid)) {
                enrol_try_internal_enrol($course->id, $userid);
            }
        }
    }

    /**
     * Get the list of all course tagged as external courses
     */
    public function get_external_courses_list() {
        $tagobject = core_tag_tag::get_by_name(core_tag_collection::get_default(), $this->externalcoursetag);
        return $tagobject->get_tagged_items('core', 'course');
    }

}