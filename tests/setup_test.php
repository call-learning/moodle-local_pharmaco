<?php

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/local/enva/locallib.php');


class local_enva_setup_testcase extends advanced_testcase {
	
    /*
     * Test the role creation / update
     */
    public function test_external_role_test() {
        global $DB;
        $this->resetAfterTest(true);
        create_external_training_role();
        $role = $DB->get_record('role', array('shortname'=> ENVA_EXTERNAL_ROLE_SHORTNAME));
        $this->assertSame($role->name, get_string('envaexternalrole_name','local_enva'));
        $this->assertSame($role->description, get_string('envaexternalrole_desc','local_enva'));
        $this->assertSame($role->archetype, ENVA_EXTERNAL_ROLE_ARCHETYPE);
    }
	
}