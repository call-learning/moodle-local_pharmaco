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
 * @package   local_pharmaco
 * @copyright 2018-2020, SAS CALL Learning
 * @author Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

use local_pharmaco\admin_setting_course;

/**Setup Page*/

if ($hassiteconfig) {
    global $CFG;
    $pharmacosettings = new admin_settingpage('local_pharmaco', get_string('pharmacosettings', 'local_pharmaco'));
    $ADMIN->add('localplugins', $pharmacosettings);

    $pharmacosettings->add(new admin_setting_course(
        'local_pharmaco/coursetocomplete',
        get_string('setting:coursetocomplete', 'local_pharmaco'),
        get_string('setting:coursetocomplete_desc', 'local_pharmaco'),
        '1'
    ));

    $ADMIN->add('localplugins',
        new admin_externalpage('pharmacoscores', get_string('pharmacoscores', 'local_pharmaco'),
            "$CFG->wwwroot/local/pharmaco/scores.php", 'moodle/site:config')
    );

}

