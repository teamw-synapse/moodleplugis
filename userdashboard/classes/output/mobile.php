<?php

namespace block_userdashboard\output;

defined('MOODLE_INTERNAL') || die();

class mobile {

    public static function view_dashboard() {
        global $OUTPUT, $PAGE, $USER, $DB;
        $totalcourses =  $DB->count_records_sql('SELECT 
                    count(c.id)
                    FROM mdl_user u
                    INNER JOIN {role_assignments} ra ON ra.userid = u.id
                    INNER JOIN {context} ct ON ct.id = ra.contextid
                    INNER JOIN {course} c ON c.id = ct.instanceid
                    INNER JOIN {role} r ON r.id = ra.roleid
                    WHERE ra.userid =:userid and ra.roleid = 5 AND c.visible = 1 AND ct.contextlevel = 50
                ',array('userid'=>$USER->id));
        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('block_userdashboard/maintabs', array('userid' => $USER->id)),
                ],
                'javascript' => array(
                    'url' => $CFG->dirroot.'/blocks/userdashboard/amd/build/userdashboard.min.js',
                ),
            ],
        ];
    }

}