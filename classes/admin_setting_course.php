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

namespace local_pharmaco;
defined('MOODLE_INTERNAL') || die();
global $CFG;

use admin_setting;
use MoodleQuickForm_course;

require_once($CFG->libdir . '/adminlib.php');

class admin_setting_course extends admin_setting {

    /**
     * Config text constructor
     *
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in
     *     config_plugins.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param string $defaultsetting
     * @param mixed $paramtype int means PARAM_XXX type, string is a allowed format in regex
     * @param int $size default field size
     */
    public function __construct($name, $visiblename, $description, $defaultsetting) {
        if (!$defaultsetting) {
            global $DB;
            $defaultsetting = '1';
        }
        parent::__construct($name, $visiblename, $description, $defaultsetting);
    }

    public function write_setting($data) {
        $validated = $this->validate($data);
        if ($validated !== true) {
            return $validated;
        }
        return ($this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin'));
    }

    /**
     * Validate data before storage
     *
     * @param string data
     * @return mixed true if ok string if error found
     */
    public function validate($data) {
        return true;
    }

    /**
     * Return an XHTML string for the setting
     *
     * @return string Returns an XHTML string
     */
    public function output_html($data, $query = '') {
        global $CFG, $DB;

        require_once($CFG->libdir . '/form/course.php');
        // Get the selected option to be displayed.

        $courseautocomplete = new MoodleQuickForm_course($this->get_full_name(), $this->visiblename);
        $currentselected = $this->get_setting();
        if ($currentselected) {
            $course = $DB->get_record('course', array('id' => $currentselected));
            $courseautocomplete->setValue($course->id);
        }
        $return = $courseautocomplete->toHtml();
        return format_admin_setting($this, $this->visiblename, $return, $this->description, true, '', null, $query);

    }

    /**
     * Return the setting
     *
     * @return mixed returns config if successful else null
     */
    public function get_setting() {
        return $this->config_read($this->name);
    }
}
