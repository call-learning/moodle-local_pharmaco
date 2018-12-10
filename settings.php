<?php
/**
 * @package   local_enva
 * @copyright 2018, CALL Learning SAS
 * @author Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**Setup Page*/

if ($hassiteconfig) {
    global $CFG;
    $envasettings = new admin_settingpage('local_enva', get_string('envasettings', 'local_enva'));
    $ADMIN->add('localplugins', $envasettings);
    
    
    
    $envasettings->add(new \local_enva\admin_setting_course(
        'local_enva/coursetocomplete',
        get_string('setting:coursetocomplete', 'local_enva'),
        get_string('setting:coursetocomplete_desc', 'local_enva'),
        '1'
    ));
    
    $ADMIN->add('localplugins',
        new admin_externalpage('envascores',get_string('envascores','local_enva'), "$CFG->wwwroot/local/enva/scores.php", 'moodle/site:config')
    );
    
    
}

