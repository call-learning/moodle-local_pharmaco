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

global $CFG;

require_once("$CFG->libdir/externallib.php");


/**
 * Course list manager
 */
class course_manager extends \external_api {
	
	/**
	 * Returns description of method parameters
	 * @return \external_function_parameters
	 */
	public static function get_matching_courses_parameters() {
		return new \external_function_parameters(
					array(
						'coursename' => new \external_value(PARAM_TEXT,'Part of the course to search for'),
					)
		);
	}
	
	/**
	 *
	 * Returns description of the returned values
	 * @return \external_multiple_structure
	 */
	public static function get_matching_courses_returns() {
		return new \external_multiple_structure (
				new \external_single_structure(
						array (
                            'id' => new \external_value(PARAM_TEXT,'Course ID'),
                            'fullname' => new \external_value(PARAM_TEXT,'Name of the course'),
                        )
				)
			);
	}
	
	public static function  get_matching_courses($coursename) {
		global $DB;
        $sql = "SELECT id, fullname FROM {course} WHERE "
            .$DB->sql_like('fullname',':coursename',false )
            ." LIMIT ". self::MAX_RETURN_VALUES;
		$courses =  $DB->get_records_sql($sql, array('coursename'=>"%{$coursename}%"));
		return $courses;
	}	
	const MAX_RETURN_VALUES = 1000;

}
