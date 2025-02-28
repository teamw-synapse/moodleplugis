<?php

//namespace local_reports\external;
defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;

class block_create_facultydashboard_external extends external_api {

    public static function faculty_courses_parameters() {

       return new external_function_parameters([
                'options' => new external_value(PARAM_RAW, 'The paging data for the service'),
                'dataoptions' => new external_value(PARAM_RAW, 'The data for the service'),
                'offset' => new external_value(PARAM_INT, 'Number of items to skip from the begging of the result set',
                    VALUE_DEFAULT, 0),
                'limit' => new external_value(PARAM_INT, 'Maximum number of results to return',
                    VALUE_DEFAULT, 0),
                'contextid' => new external_value(PARAM_INT, 'contextid'),
                'filterdata' => new external_value(PARAM_RAW, 'The data for the service'),
            ]);
    }
    public static function faculty_courses($options,
        $dataoptions,
        $offset = 0,
        $limit = 0,
        $contextid,
        $filterdata) {

        global $OUTPUT, $CFG, $DB,$USER,$PAGE;
        require_once($CFG->dirroot . '/blocks/facultydashboard/lib.php');
        require_login();
        $PAGE->set_url('/blocks/facultydashboard/block_facultydashboard.php', array());
        $PAGE->set_context($contextid);
        $context = context_system::instance();
        // Parameter validation.
        $params = self::validate_parameters(
            self::faculty_courses_parameters(),
            [
                'options' => $options,
                'dataoptions' => $dataoptions,
                'offset' => $offset,
                'limit' => $limit,
                'contextid' => $contextid,
                'filterdata' => $filterdata
            ]
        );
        
        $output = $PAGE->get_renderer('block_facultydashboard');

        $offset = $params['offset'];
        $limit = $params['limit'];
        $decodedata = json_decode($params['dataoptions']);
        $filtervalues = json_decode($filterdata);
        // print_r($decodedata);
        $stable = new \stdClass();
        $stable->thead = true;
       
        $stable->thead = false;
        $stable->start = $offset;
        $stable->length = $limit;
        
        if($decodedata->userid > 0){
            $courseslist = $output->courses_list($stable,$filtervalues,$decodedata->userid);
        }else{
            $courseslist = $output->courses_list($stable,$filtervalues);
        }
        
        $totalcount = $courseslist['totalcount'];
        $data=$courseslist['data'];
        // print_r($courseslist);
        return [
            'totalcount' => $totalcount,
            'records' =>$data,
            'options' => $options,
            'dataoptions' => $dataoptions,
            'filterdata' => $filterdata,
        ];
            
    }


    public static function faculty_courses_returns() {

        return new external_single_structure([

         'options' => new external_value(PARAM_RAW, 'The paging data for the service'),
         'dataoptions' => new external_value(PARAM_RAW, 'The data for the service'),
         'totalcount' => new external_value(PARAM_INT, 'total number of accounts in system'),
         'filterdata' => new external_value(PARAM_RAW, 'The data for the service'),
         'records' => new external_multiple_structure(

                    new external_single_structure(

                        array(
                'coursepic' => new external_value(PARAM_RAW, 'Id of the faculty userid'),
                'imgurlflag' => new external_value(PARAM_RAW, 'Id of the faculty userid'),
                'userid' => new external_value(PARAM_RAW, 'Id of the faculty userid'),
                'courseid' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                'url' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                'studentcount' => new external_value(PARAM_RAW, 'user count of the faculty courses'),
                'startdate' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                'enddate' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                'catname' => new external_value(PARAM_RAW, 'catname'),
                'my_courses' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                'shortname' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                'my_coursesfull' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                'assign_count' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                'quiz_count' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                'forum_count' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                 )
                    )
                )
        ]);
    }
        
}