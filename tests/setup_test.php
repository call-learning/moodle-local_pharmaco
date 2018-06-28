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
        // Delete role as it is usually created at install
        $externalroleid = $DB->get_field('role','id',array ('shortname' => ENVA_EXTERNAL_ROLE_SHORTNAME));
        if ($externalroleid) {
            delete_role($externalroleid);
        }
        
        // Recreate it
        create_external_training_role();
        $role = $DB->get_record('role', array('shortname'=> ENVA_EXTERNAL_ROLE_SHORTNAME));
        $this->assertSame( get_string('envaexternalrole_name','local_enva'), $role->name);
        $this->assertSame(get_string('envaexternalrole_desc','local_enva'), $role->description);
        $this->assertSame(ENVA_EXTERNAL_ROLE_ARCHETYPE, $role->archetype);
    }
    
    /*
     * Test the tag creation for course
     */
    public function test_external_tag_test() {
        global $DB;
        $this->resetAfterTest(true);
        //  Delete tag as it is usually created at install
        $existingtag = core_tag_tag::get_by_name(core_tag_collection::get_default(),ENVA_EXTERNAL_COURSE_TAG_NAME);
        core_tag_tag::delete_tags($existingtag->id);
        
        // Recreate it
        create_external_tag();
        $tag = core_tag_tag::get_by_name(core_tag_collection::get_default(),ENVA_EXTERNAL_COURSE_TAG_NAME, '*');
        $this->assertEquals(1, $tag->isstandard);
        $this->assertEquals(ENVA_EXTERNAL_COURSE_TAG_NAME, $tag->name);
    }
    
    public function test_defaults_setups() {
        global $CFG;
        $this->assertEquals(1, get_config('moodlecourse','enablecompletion'));
        $this->assertEquals(HOMEPAGE_MY, $CFG->defaulthomepage);
        $this->assertEquals(1, $CFG->enablecompletion);
    }
}