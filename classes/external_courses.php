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
defined('MOODLE_INTERNAL') || die();

/**
 *  Class to manage courses assigned to external users
 */
class external_courses {
    private $externalcoursetag;
    
    /**
     * External_courses constructor.
     * We provide the tag as parameter so we are not dependent on the value here
     * @param string $externalcoursetag
     */
    public function __construct(string $externalcoursetag) {
        $this->externalcoursetag = $externalcoursetag;
    }
    /**
     * Get the list of all course tagged as external courses
     */
    public function get_external_courses_list() {
        $tagobject = \core_tag_tag::get_by_name(\core_tag_collection::get_default(), $this->externalcoursetag);
        return $tagobject->get_tagged_items('core','course');
    }
    /**
     * Register given user to the external courses
     */
    public function enrol_user_into_external_courses($userid) {
        global $CFG;
        include_once($CFG->libdir.'/enrollib.php');
        
        $courselist = $this->get_external_courses_list();
        foreach( $courselist as $course ) {
            $context = context_course::instance($course->id);
            if ( !is_enrolled($context,$userid) ) {
                enrol_try_internal_enrol($course->id, $userid);
            }
        }
    }

}