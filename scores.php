<?php
/**
 * @package   local_enva
 * @copyright 2018, CALL Learning SAS
 * @author Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');

$userid = optional_param('userid', null, PARAM_INT);

admin_externalpage_setup('envascores');

$url = new moodle_url('/local/enva/scores.php');
if ($userid) {
    $url->params(array('userid'=>$userid));
} else {
    $userid = $USER->id;
}

// This is a system level page
require_login();

// Start page output
echo $OUTPUT->header();
$ts = new \local_enva\tag_scores(\local_enva\helper::get_test_course_id(), $userid);
$tagscores = $ts->compute();
$cl = new \local_enva\course_list_by_score($userid, $tagscores);
$courselist = $cl->get_list();

echo $OUTPUT->container_start('content');


$table = new html_table();
foreach ($tagscores as $tname => $ts) {
    $table->data[] = array ( $tname,"{$ts['mark']}/{$ts['coef']}");
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
    $table->data[] = array ( $c->fullname,"{$c->score} for tags {$tags}");
}
echo html_writer::table($table);

echo $OUTPUT->container_end();

echo $OUTPUT->footer();