<?php
// This file is part of Moodle - http://moodle.org/
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License.

// defined('MOODLE_INTERNAL') || die(); // Ensure this file is included within Moodle 
require_once(__DIR__ . '/../../config.php');
global $CFG,$DB,$USER;
require_once($CFG->libdir . '/csvlib.class.php');

$tyepids =  optional_param('courses','',PARAM_RAW);
$selected_roles =  optional_param('roles','',PARAM_RAW);
$selected_status =  optional_param('status','',PARAM_RAW);

$selectsql = "SELECT DISTINCT ra.userid as id,u.idnumber,u.firstname,u.lastname,u.email,u.suspended,r.shortname as role,r.name as rolename, c.id as courseid, u.id as userid, r.id as roleid ";

$selectsql .= " FROM {course_categories} as cc join {course_categories} as cc1 on cc1.parent = cc.id 
    join {course} as c ON cc1.id = c.category 
    JOIN {context} as ctx ON ctx.instanceid = c.id AND ctx.contextlevel = 50 
    JOIN {role_assignments} as ra ON ra.contextid = ctx.id
    join {user} as u on u.id = ra.userid 
    join {role} as r on r.id = ra.roleid 
    where  cc.parent in ($tyepids) AND r.id IN ($selected_roles) ";

$selectsql .= " AND u.deleted = 0";
$selectsql .= " AND u.suspended IN ($selected_status)";
$selectsql .= " ORDER BY u.id DESC";
$users = $DB->get_records_sql($selectsql,array());

$filename = 'user_data_export_' . date('Ymd_His'); 
$csvexport = new csv_export_writer();
$csvexport->set_filename($filename);

$csvexport->add_data(['ID Number', 'First Name', 'Last Name', 'Email', 'Role']);

foreach ($users as $user) {
    $csvexport->add_data([
        $user->idnumber,
        $user->firstname,
        $user->lastname,
        $user->email,
        $user->rolename
    ]);
}

$csvexport->download_file();
exit;