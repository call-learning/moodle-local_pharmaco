<?php
/**
* @package   local_enva
* @copyright 2018, CALL Learning SAS
* @author Laurent David <laurent@call-learning.fr>
* @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

namespace local_enva;
defined('MOODLE_INTERNAL') || die();

global $CFG;
include_once($CFG->dirroot . '/local/enva/lib.php');

class helper {
    /**
     * Utility function to check if the provided user is an external user
     * @param $userid
     * @return bool
     * @throws \dml_exception
     */
    static public function is_user_external_role($userid) {
        $userroles = get_user_roles(\context_system::instance(), $userid);
        foreach($userroles as $r) {
            if ($r->shortname == ENVA_EXTERNAL_ROLE_SHORTNAME) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Get test course identifier as setup in the plugin settings
     */
    static public function get_test_course_id() {
        return get_config('local_enva','coursetocomplete');
    }
}