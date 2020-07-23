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
 * Local lib
 *
 * @package   local_pharmaco
 * @copyright 2018-2020, SAS CALL Learning
 * @author Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
global $CFG;

require_once($CFG->dirroot . '/local/pharmaco/lib.php');

/**
 * Create or resets the external role
 *
 * @return bool
 * @throws coding_exception
 * @throws dml_exception
 */
function create_external_training_role() {
    global $DB;
    // Create role if it does not exist already.
    $externalroleid = $DB->get_field('role', 'id', array('shortname' => PHARMACO_EXTERNAL_ROLE_SHORTNAME));

    if (!$externalroleid) {
        // Create it !
        $externalroleid = create_role(
            get_string('pharmacoexternalrole_name', 'local_pharmaco'),
            PHARMACO_EXTERNAL_ROLE_SHORTNAME,
            get_string('pharmacoexternalrole_desc', 'local_pharmaco'),
            PHARMACO_EXTERNAL_ROLE_ARCHETYPE
        );
        set_role_contextlevels($externalroleid, array_merge(get_default_contextlevels('learner'), array(CONTEXT_SYSTEM)));

    } else {
        // Reset the role.
        $role = new stdClass();
        $role->id = $externalroleid;
        $role->name = get_string('pharmacoexternalrole_name', 'local_pharmaco');
        $role->shortname = PHARMACO_EXTERNAL_ROLE_SHORTNAME;
        $role->description = get_string('pharmacoexternalrole_desc', 'local_pharmaco');
        $role->archetype = PHARMACO_EXTERNAL_ROLE_ARCHETYPE;
        $DB->update_record('role', $role);
        reset_role_capabilities($externalroleid);
    }
    // We modifiy the role anyways.
    $systemcapabilitieslist = array();

    foreach ($systemcapabilitieslist as $capa => $allow) {
        assign_capability($capa, $allow, $externalroleid, context_system::instance(), true);
    }
    return true;
}

/**
 * Create a new tag if it does not exist.
 *
 * The purpose of this tag is to mark courses as courses for external people (8 courses normally)
 * This will be part of the default tag collection
 */
function create_external_tag() {
    global $DB;

    core_tag_tag::create_if_missing(core_tag_collection::get_default(), array(PHARMACO_EXTERNAL_COURSE_TAG_NAME), true);
    return true;
}

/**
 * Set the side global preferences
 *
 * @return bool
 */
function setup_preferences() {
    set_config("defaulthomepage", HOMEPAGE_MY);
    set_config("enablecompletion", 1, "moodlecourse");
    set_config("enablecompletion", 1);
    return true;
}

function setup_dashboard_block() {
    global $PAGE, $CFG;
    require_once($CFG->dirroot . '/my/lib.php');
    // Get the default Dashboard block.
    $defaultmy = my_get_page(null, MY_PAGE_PRIVATE);
    $rname = 'content';

    $defaultmypage = new moodle_page();
    $defaultmypage->set_pagetype('my-index');
    $defaultmypage->set_subpage($defaultmy->id);
    $defaultmypage->set_url(new moodle_url('/'));

    $defaultmybm = $defaultmypage->blocks;
    $defaultmybm->add_regions(array($rname), false);
    $defaultmybm->set_default_region($rname);
    // Backup global PAGE.
    $oldpage = $PAGE;
    $PAGE = $defaultmypage;
    $defaultmybm->load_blocks();

    // Delete unceessary blocks.
    $centralblocks = $defaultmybm->get_blocks_for_region($rname);
    foreach ($centralblocks as $cb) {
        if ($cb->name() == 'pharmaco') {
            blocks_delete_instance($cb->instance);
        }
    }
    // Add the modified block.
    $defaultmybm->add_block('pharmaco', $rname, 0, true, $defaultmypage->pagetype, $defaultmypage->subpage);
    $defaultmybm->load_blocks();
    // Restore global PAGE.
    $PAGE = $oldpage;
    my_reset_page_for_all_users();
    return true;
}

/**
 * Global setup function calling all setup function of this module
 *
 * @return bool
 * @throws coding_exception
 * @throws dml_exception*
 */
function pharmaco_setups() {
    return create_external_tag() && create_external_training_role() && setup_preferences() && setup_dashboard_block();
}