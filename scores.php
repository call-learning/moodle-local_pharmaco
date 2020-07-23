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

use local_pharmaco\course_list_by_score;
use local_pharmaco\helper;
use local_pharmaco\tag_scores;

require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');

$userid = optional_param('userid', null, PARAM_INT);

admin_externalpage_setup('pharmacoscores');

$url = new moodle_url('/local/pharmaco/scores.php');
if ($userid) {
    $url->params(array('userid' => $userid));
} else {
    $userid = $USER->id;
}

// This is a system level page.
require_login();

// Start page output.
echo $OUTPUT->header();
$ts = new tag_scores(helper::get_test_course_id(), $userid);
$tagscores = $ts->compute();
$cl = new course_list_by_score($userid, $tagscores);
$courselist = $cl->get_list();

echo $OUTPUT->container_start('content');

$table = new html_table();
foreach ($tagscores as $tname => $ts) {
    $rawmean = $ts['mark'] / $ts['coef'];
    $table->data[] = array($tname, "{$ts['mark']}/{$ts['coef']}: {$rawmean}");
}

echo html_writer::table($table);

echo $OUTPUT->container_end();

echo $OUTPUT->container_start('content');

$table = new html_table();
foreach ($courselist as $c) {
    $tags = '[';
    foreach ($c->tags as $t) {
        $tags .= $t->name . ',';
    }
    $tags .= ']';
    $table->data[] = array($c->fullname, "{$c->score} for tags {$tags}");
}
echo html_writer::table($table);

echo $OUTPUT->container_end();

echo $OUTPUT->footer();