<?php
/**
 * @package   local_enva
 * @copyright 2018, CALL Learning SAS
 * @author Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function xmldb_local_enva_upgrade($oldversion) {
    global $CFG, $DB;
    $dbman = $DB->get_manager(); // loads ddl manager and xmldb classes

    $success = true;

    if ($oldversion < 2018062200) {
        $success = enva_setups();
        upgrade_plugin_savepoint(true, 2018062200, 'local','enva');
    }


    if ($oldversion < 2018062204) {
        $success = enva_setups();
        upgrade_plugin_savepoint(true, 2018062204, 'local','enva');
    }

    return $success;
}
