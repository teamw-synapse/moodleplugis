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
 *
 * @package    block_userdashboard
 * @copyright  2024 VGPL
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = array(
  
    'block_userdashboard_data_for_mydashboard' => array(
        'classname'    => 'block_userdashboard\external',
        'methodname'   => 'data_for_mydashboard',
        'classpath'    => '',
        'description'  => 'Load the data for dashboard.',
        'type'         => 'read',
        'capabilities' => '',
        'ajax'         => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
     
    ),
    'block_userdashboard_data_for_profile' => array(
        'classname'    => 'block_userdashboard\external',
        'methodname'   => 'data_for_profile',
        'classpath'    => '',
        'description'  => 'Load the data for the profile.',
        'type'         => 'read',
        'capabilities' => '',
        'ajax'         => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
     
    ),
    'block_userdashboard_data_for_courses' => array(
        'classname'    => 'block_userdashboard\external',
        'methodname'   => 'data_for_courses',
        'classpath'    => '',
        'description'  => 'Load the data for the ourses.',
        'type'         => 'read',
        'capabilities' => '',
        'ajax'         => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),
    'block_userdashboard_data_for_assesments' => array(
        'classname'    => 'block_userdashboard\external',
        'methodname'   => 'data_for_assesments',
        'classpath'    => '',
        'description'  => 'Load the data for the assesments.',
        'type'         => 'read',
        'capabilities' => '',
        'ajax'         => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),
    'block_userdashboard_data_for_exams' => array(
        'classname'    => 'block_userdashboard\external',
        'methodname'   => 'data_for_exams',
        'classpath'    => '',
        'description'  => 'Load the data for the exams.',
        'type'         => 'read',
        'capabilities' => '',
        'ajax'         => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),
    'block_userdashboard_data_for_forums' => array(
        'classname'    => 'block_userdashboard\external',
        'methodname'   => 'data_for_forums',
        'classpath'    => '',
        'description'  => 'Load the data for the forums.',
        'type'         => 'read',
        'capabilities' => '',
        'ajax'         => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),
    'block_userdashboard_data_for_reports' => array(
        'classname'    => 'block_userdashboard\external',
        'methodname'   => 'data_for_reports',
        'classpath'    => '',
        'description'  => 'Load the data for the reports.',
        'type'         => 'read',
        'capabilities' => '',
        'ajax'         => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),
    'block_userdashboard_search_for_students' => array(
        'classname'    => 'block_userdashboard\external',
        'methodname'   => 'search_for_students',
        'classpath'    => '',
        'description'  => 'Load the data for the students.',
        'type'         => 'read',
        'capabilities' => '',
        'ajax'         => true,
    ),
    'block_userdashboard_recording_videoproctoringdata' => array(
        'classname'    => 'block_userdashboard\external',
        'methodname'   => 'recording_videoproctoringdata',
        'classpath'    => '',
        'description'  => 'recording_videoproctoringdata',
        'type'         => 'read',
        'capabilities' => '',
        'ajax'         => true,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    )
);

