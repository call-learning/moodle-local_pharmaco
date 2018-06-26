<?php
/**
 * @package   local_enva
 * @copyright 2018, CALL Learning SAS
 * @author Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$observers = array (
    array (
        'eventname' =>'\core\event\course_completed',
        'callback' => '\local\course_completion::completed',
    )
);