<?php

defined('MOODLE_INTERNAL') || die();

$functions = array(
        'local_enva_get_matching_courses' => array(
				'classname'    => 'local_enva\course_manager',
				'methodname'   => 'get_matching_courses',
				'description'  => 'Get matching course providing a course name',
				'type'         => 'read',
				'ajax'         => true,
		),
);
