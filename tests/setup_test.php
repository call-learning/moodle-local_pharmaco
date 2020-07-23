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
 * Unit tests (generic)
 *
 * @package   local_pharmaco
 * @copyright 2018-2020, SAS CALL Learning
 * @author Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/local/pharmaco/locallib.php');

/**
 * This class contains the test cases for the generic setup class
 *
 * @copyright 2018-2020, SAS CALL Learning
 * @author Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_pharmaco_setup_testcase extends advanced_testcase {

    /**
     * Test the role creation / update
     */
    public function test_external_role_test() {
        global $DB;
        $this->resetAfterTest(true);
        // Delete role as it is usually created at install.
        $externalroleid = $DB->get_field('role', 'id', array('shortname' => PHARMACO_EXTERNAL_ROLE_SHORTNAME));
        if ($externalroleid) {
            delete_role($externalroleid);
        }

        // Recreate it.
        create_external_training_role();
        $role = $DB->get_record('role', array('shortname' => PHARMACO_EXTERNAL_ROLE_SHORTNAME));
        $this->assertSame(get_string('pharmacoexternalrole_name', 'local_pharmaco'), $role->name);
        $this->assertSame(get_string('pharmacoexternalrole_desc', 'local_pharmaco'), $role->description);
        $this->assertSame(PHARMACO_EXTERNAL_ROLE_ARCHETYPE, $role->archetype);
    }

    /*
     * Test the tag creation for course.
     */
    public function test_external_tag_test() {
        $this->resetAfterTest(true);
        // Delete tag as it is usually created at install.
        $existingtag = core_tag_tag::get_by_name(core_tag_collection::get_default(), PHARMACO_EXTERNAL_COURSE_TAG_NAME);
        core_tag_tag::delete_tags($existingtag->id);

        // Recreate it.
        create_external_tag();
        $tag = core_tag_tag::get_by_name(core_tag_collection::get_default(), PHARMACO_EXTERNAL_COURSE_TAG_NAME, '*');
        $this->assertEquals(1, $tag->isstandard);
        $this->assertEquals(PHARMACO_EXTERNAL_COURSE_TAG_NAME, $tag->name);
    }

    public function test_defaults_setups() {
        global $CFG;
        $this->assertEquals(1, get_config('moodlecourse', 'enablecompletion'));
        $this->assertEquals(HOMEPAGE_MY, $CFG->defaulthomepage);
        $this->assertEquals(1, $CFG->enablecompletion);
    }
}