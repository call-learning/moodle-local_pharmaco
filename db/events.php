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
        'callback' => '\local_enva\course_completion_observer::completed',
    ),
    array (
        'eventname' =>'\core\event\role_assigned',
        'callback' => '\local_enva\user_registration::register_to_test_course_when_assigned',
    )
);