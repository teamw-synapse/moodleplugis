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
 * xp leve Reports a Moodle block for creating customizable reports
 *
 * @copyright  2024 
 * @package    block_xp_level
 * @author     Bhupathi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir . '/csvlib.class.php');
$courseid = required_param('courseid',PARAM_INT);
function get_leveldata($courseid) {
    global $CFG, $DB; 

    $xplevels = $DB->get_records_sql("
        SELECT 
            CONCAT(u.firstname, ' ', u.lastname) AS student_name, 
            c.shortname AS course_name, 
            bxp.lvl AS levels, 
            bxp.xp AS points, 
            bxc.levelsdata 
        FROM mdl_course AS c
        JOIN mdl_context AS ct ON ct.instanceid = c.id AND ct.contextlevel = 50
        JOIN mdl_role_assignments AS ra ON ra.contextid = ct.id AND ra.roleid = 5
        JOIN mdl_user AS u ON u.id = ra.userid AND u.deleted = 0
        LEFT JOIN mdl_block_xp AS bxp ON c.id = bxp.courseid AND u.id = bxp.userid
        JOIN mdl_block_xp_config AS bxc ON c.id = bxc.courseid 
        WHERE c.id = :courseid
    ", array('courseid' => $courseid));

    $data = [];

    foreach ($xplevels as $xplevel) {
        $xpdata['student_name'] = $xplevel->student_name;
        $xpdata['course_name'] = $xplevel->course_name;
        $xpdata['points'] = $xplevel->points;
        $levlsdata = json_decode($xplevel->levelsdata);
        
        if ($returval = isValueBetween($xplevel->points, $levlsdata->xp)) {
            $xpdata['levels'] = $returval['level'];
            $xpdata['levelsdata'] = 'next level in '.$returval['points'];
        } else {
            $xpdata['levels'] = $returval['level'];;
            $xpdata['levelsdata'] = 'next level in '.$returval['points'];
        }
        
        $data[] = $xpdata;
    }

    return $data;
}

function isValueBetween($value, $array) {
    // Sort the array to ensure correct order
    sort($array);
    
    // Loop through the array to check if value is between two consecutive values
    for ($i = 0; $i < count($array) - 1; $i++) {
        if ($value > $array[$i] && $value < $array[$i + 1]) {
            return array('level' => $i + 1, 'points' => $array[$i + 1] - $value);
        }
    }
    return array('level' => '-', 'points' => $array[1]);
}


function export_to_csv($data) {
    $filename = 'xp_level_report_' . date('Ymd_His'); 
    $csvexport = new csv_export_writer();
    $csvexport->set_filename($filename);

    $csvexport->add_data(['First name/Last name', 'Unit Name', 'Level', 'Total', 'Progress']);

    foreach ($data as $row) {
        $csvexport->add_data([$row['student_name'], $row['course_name'], $row['levels'], $row['points'], $row['levelsdata']]);
    }

    $csvexport->download_file();
    exit;
}

$data = get_leveldata($courseid);
export_to_csv($data);
die();