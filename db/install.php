<?php
/**
 * @package   local_enva
 * @copyright 2018, CALL Learning SAS
 * @author Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;
require_once($CFG->dirroot.'/local/enva/locallib.php');

function xmldb_local_enva_install() {
    global $CFG, $DB, $SITE;
    
    $dbman   = $DB->get_manager(); // Loads ddl manager and xmldb classes.
    $success = true;
    $success = create_external_training_role();
    $success = create_external_tag();
    return $success;
}
