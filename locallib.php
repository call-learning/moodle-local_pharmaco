<?php
/**
 * @package   local_enva
 * @copyright 2018, CALL Learning SAS
 * @author Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
global $CFG;

include_once ($CFG->dirroot.'/local/enva/lib.php');

/**
 * Create or resets the external role
 * @return bool
 * @throws coding_exception
 * @throws dml_exception
 */
function create_external_training_role() {
    global $DB;
    // Create role if it does not exist already
    $externalroleid = $DB->get_field('role','id',array ('shortname' => ENVA_EXTERNAL_ROLE_SHORTNAME));
    $rolearchetype = 'student';
    
    if (! $externalroleid )
    {
        // Create it !
        $externalroleid = create_role(
            get_string('envaexternalrole_name','local_enva'),
            ENVA_EXTERNAL_ROLE_SHORTNAME,
            get_string('envaexternalrole_desc','local_enva'),
            ENVA_EXTERNAL_ROLE_ARCHETYPE
        );
        set_role_contextlevels($externalroleid,array_merge(get_default_contextlevels('learner'),array(CONTEXT_SYSTEM)));
        
    } else {
        // Reset the role
        $role = new stdClass();
        $role->id           = $externalroleid;
        $role->name         = get_string('envaexternalrole_name','local_enva');
        $role->shortname    = ENVA_EXTERNAL_ROLE_SHORTNAME;
        $role->description  = get_string('envaexternalrole_desc','local_enva');
        $role->archetype    = ENVA_EXTERNAL_ROLE_ARCHETYPE;
        $DB->update_record('role', $role);
        reset_role_capabilities($externalroleid);
    }
    // We modifiy the role anyways
    $systemcapabilitieslist = array ( );
    
    foreach ($systemcapabilitieslist as $capa => $allow ) {
        assign_capability($capa, $allow, $externalroleid,context_system::instance(), true);
    }
    return true;
}

/**
 * Create a new tag if it does not exist. The purpose of this tag is to
 * mark courses as courses for external people (8 courses normally)
 */
function create_external_tag() {

}