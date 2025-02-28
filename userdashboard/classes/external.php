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
 * @package    block_userdashboard
 * @copyright  2024 VGPL
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_userdashboard;
defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");

use context;
use context_system;
use context_course;
use context_helper;
use context_user;
use coding_exception;
use external_api;
use external_function_parameters;
use external_value;
use external_format_value;
use external_single_structure;
use external_multiple_structure;
use invalid_parameter_exception;
use required_capability_exception;
use moodle_url;

use core_cohort\external\cohort_summary_exporter;


/**
 *
 * @copyright  VGPL 2024
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external extends external_api {

    /**
     * Returns the description of the 
     data_for_mydashboard_parameters.
     *
     * @return external_function_parameters.
     */
    public static function data_for_mydashboard_parameters() {
        $tab = new external_value(PARAM_TEXT, 'tab');
        $userid = new external_value(PARAM_INT, 'userid');
        $params = array(
            'tab' => $tab,
            'userid' => $userid
        );
        return new external_function_parameters($params);
    }


    /**
     * Data to render in the related mydashboard section.
     *
     * @param int $tab
     * @return array mydashboard list.
     */
    public static function data_for_mydashboard($tab,$userid) {
        global $USER,$DB;

        $params = self::validate_parameters(self::data_for_mydashboard_parameters(), array(
            'tab' => $tab,
            'userid' => $userid
        ));
        $totalcourses =  $DB->count_records_sql('SELECT 
                    count(c.id)
                    FROM mdl_user u
                    INNER JOIN {role_assignments} ra ON ra.userid = u.id
                    INNER JOIN {context} ct ON ct.id = ra.contextid
                    INNER JOIN {course} c ON c.id = ct.instanceid
                    INNER JOIN {role} r ON r.id = ra.roleid
                    WHERE ra.userid =:userid and ra.roleid = 5 AND c.visible = 1 AND ct.contextlevel = 50
                ',array('userid'=>$userid));
        $inprogresscount =  $DB->count_records_sql('
                    SELECT count(cc.id) as count from {course_completions} as cc
                    JOIN {course} as c ON c.id = cc.course
                    where  cc.userid =:userid and cc.timecompleted Is NULL AND c.visible = 1
        ',array('userid'=>$userid));

        $completedcount =  $DB->count_records_sql('
                    SELECT count(cc.id) as count from {course_completions} as cc
                    JOIN {course} as c ON c.id = cc.course
                    where  cc.userid =:userid and cc.timecompleted IS NOT NULL AND c.visible = 1
        ',array('userid'=>$userid));
        $inprogresscount = $totalcourses-$completedcount;
       // $totalassesments =  $DB->count_records_sql('
       //              SELECT count(a.id) as count from {assign} as a
       //              JOIN {course_modules} as cm ON cm.course = a.course AND a.id = cm.instance AND cm.module = 1
       //              where cm.visible = 1
       //  ',array());

        $totalassesments =  $DB->count_records_sql('
                    SELECT count(a.id) as count 
                    FROM mdl_user u
                    INNER JOIN {role_assignments} ra ON ra.userid = u.id
                    INNER JOIN {context} ct ON ct.id = ra.contextid
                    INNER JOIN {course} c ON c.id = ct.instanceid
                    INNER JOIN {course_modules} cm ON cm.course = c.id
                    INNER JOIN {assign} a ON a.course = cm.course AND cm.instance = a.id AND cm.module = (SELECT id FROM {modules} WHERE name = "assign")
                    WHERE ra.userid =:userid and ra.roleid = 5 AND cm.visible = 1 AND c.visible = 1 AND ct.contextlevel = 50
        ',array('userid'=>$userid));

        $inprogressasscount =  $DB->count_records_sql('
                    SELECT count(cmc.id) as count from {assign} as a
                    JOIN {course_modules} as cm ON cm.course = a.course AND a.id = cm.instance AND cm.module = 1
                    JOIN {course_modules_completion} as cmc ON cmc.coursemoduleid = cm.id
                    where cm.visible = 1 AND cmc.userid =:userid AND cmc.completionstate = 0
        ',array('userid'=>$userid));

        $completedasscount =  $DB->count_records_sql('
                    SELECT count(cmc.id) as count from {assign} as a
                    JOIN {course_modules} as cm ON cm.course = a.course AND a.id = cm.instance AND cm.module = 1
                    JOIN {course_modules_completion} as cmc ON cmc.coursemoduleid = cm.id
                    where cm.visible = 1 AND cmc.userid =:userid AND cmc.completionstate > 0
        ',array('userid'=>$userid));

        $inprogressasscount = $totalassesments-$completedasscount;

        $totalexams =  $DB->count_records_sql('
                    SELECT count(q.id) as count FROM mdl_user u
                    INNER JOIN {role_assignments} ra ON ra.userid = u.id
                    INNER JOIN {context} ct ON ct.id = ra.contextid
                    INNER JOIN {course} c ON c.id = ct.instanceid
                    INNER JOIN {course_modules} cm ON cm.course = c.id
                    INNER JOIN {quiz} q ON q.course = cm.course AND cm.instance = q.id AND cm.module = (SELECT id FROM {modules} WHERE name = "quiz")
                    WHERE ra.userid =:userid and ra.roleid = 5 AND cm.visible = 1 AND c.visible = 1 AND ct.contextlevel = 50
        ',array('userid'=>$userid));

        $inprogressexamscount =  $DB->count_records_sql('
                    SELECT count(cmc.id) as count from {quiz} as a
                    JOIN {course_modules} as cm ON cm.course = a.course AND a.id = cm.instance
                    JOIN {modules} as m on cm.module = m.id
                    JOIN {course_modules_completion} as cmc ON cmc.coursemoduleid = cm.id
                    where cm.visible = 1 AND m.name = "quiz" AND cmc.userid =:userid AND cmc.completionstate = 0
        ',array('userid'=>$userid));

        $completedexamscount =  $DB->count_records_sql('
                    SELECT count(cmc.id) as count from {quiz} as a
                    JOIN {course_modules} as cm ON cm.course = a.course AND a.id = cm.instance
                    JOIN {course_modules_completion} as cmc ON cmc.coursemoduleid = cm.id
                    JOIN {modules} as m on cm.module = m.id
                    where cm.visible = 1 AND m.name = "quiz" AND cmc.userid =:userid AND cmc.completionstate > 0
        ',array('userid'=>$userid));
        $inprogressexamscount = $totalexams-$completedexamscount;

        $totalforums =  $DB->count_records_sql('
                    SELECT count(fd.id) as count FROM mdl_user u
                    INNER JOIN {role_assignments} ra ON ra.userid = u.id
                    INNER JOIN {context} ct ON ct.id = ra.contextid
                    INNER JOIN {course} c ON c.id = ct.instanceid
                    INNER JOIN {course_modules} cm ON cm.course = c.id
                    INNER JOIN {forum} f ON f.course = cm.course AND cm.instance = f.id AND cm.module = (SELECT id FROM {modules} WHERE name = "forum") AND f.type != "news"
                    JOIN mdl_forum_discussions AS fd ON fd.forum = f.id AND fd.course = c.id
                    WHERE ra.userid =:userid and ra.roleid = 5 AND cm.visible = 1 AND c.visible = 1 AND ct.contextlevel = 50
        ',array('userid'=>$userid));

        $inprogressforumcount =  0;

        $completedforums =  $DB->count_records_sql('
                    SELECT count(fd.id) as count FROM mdl_user u
                    INNER JOIN {role_assignments} ra ON ra.userid = u.id
                    INNER JOIN {context} ct ON ct.id = ra.contextid
                    INNER JOIN {course} c ON c.id = ct.instanceid
                    INNER JOIN {course_modules} cm ON cm.course = c.id
                    INNER JOIN {forum} f ON f.course = cm.course AND cm.instance = f.id AND cm.module = (SELECT id FROM {modules} WHERE name = "forum")
                    JOIN mdl_forum_discussions AS fd ON fd.forum = f.id AND fd.course = c.id
                    JOIN mdl_forum_posts AS fp ON fp.discussion = fd.id AND fp.parent != 0 AND fp.userid = ra.userid
                    WHERE ra.userid =:userid and ra.roleid = 5 AND cm.visible = 1 AND c.visible = 1 AND ct.contextlevel = 50
        ',array('userid'=>$userid));

        $inprogressforumcount = $totalforums-$completedforums;
        $coursepercentage = $completedcount>0 ? ($completedcount/$totalcourses) * 100 : 0;
        $assespercentage = $completedasscount>0 ? ($completedasscount/$totalassesments) * 100 : 0;
        $exampercentage = $completedexamscount>0 ? ($completedexamscount/$totalexams) * 100 : 0;
        $forumpercentage = $completedforums>0 ? ($completedforums/$totalforums) * 100 : 0;


        $data = array('totalcourses' => $totalcourses,'inprogresscount' => $inprogresscount,'completedcount' => $completedcount,'totalassesments' => $totalassesments,'inprogressasscount' => $inprogressasscount,'completedasscount' => $completedasscount,'totalexams' => $totalexams,'inprogressexamscount' => $inprogressexamscount,'completedexamscount' => $completedexamscount,'coursepercentage' => round($coursepercentage), 'assespercentage' =>round($assespercentage), 'exampercentage' =>round($exampercentage),'totalforums' => $totalforums,'inprogressforumcount' => $inprogressforumcount, 'completedforums' => $completedforums,'forumpercentage'=>round($forumpercentage));
        // print_r($data);exit;
        return $data;
    }

    /**
     * Returns description of data_for_mydashboard_returns() result value.
     *
     * @return external_description
     */
   public static function data_for_mydashboard_returns() {
     

        return new external_single_structure(array (
    
            'totalcourses' => new external_value(PARAM_INT, 'Number of enrolled courses.', VALUE_OPTIONAL),           
            'inprogresscount'=>  new external_value(PARAM_INT, 'Number of inprogress course count.'),  
            'completedcount'=>  new external_value(PARAM_INT, 'Number of complete course count.'), 
            'totalassesments'=>  new external_value(PARAM_INT, 'Number of assesments count.'), 
            'inprogressasscount'=>  new external_value(PARAM_INT, 'Number of inprogress assesments count.'),  
            'completedasscount'=>  new external_value(PARAM_INT, 'Number of complete assesments count.'), 
            'totalexams'=>  new external_value(PARAM_INT, 'Number of exams count.'),
            'inprogressexamscount'=>  new external_value(PARAM_INT, 'Number of inprogress exams count.'),  
            'completedexamscount'=>  new external_value(PARAM_INT, 'Number of complete exams count.'),
            'coursepercentage'=>  new external_value(PARAM_RAW, 'course percentage'),
            'assespercentage'=>  new external_value(PARAM_RAW, 'assesment percentage.'),  
            'exampercentage'=>  new external_value(PARAM_RAW, 'exam percentage'),
            'totalforums'=>  new external_value(PARAM_RAW, 'totalforums'),
            'forumpercentage'=>  new external_value(PARAM_RAW, 'forum percentage'),
            'inprogressforumcount'=>  new external_value(PARAM_INT, 'Number of inprogress forums count.'),  
            'completedforums'=>  new external_value(PARAM_INT, 'Number of complete forums count.'),
        ));

    }


    /**
     * Returns the description of the 
     data_for_profile_parameters.
     *
     * @return external_function_parameters.
     */
    public static function data_for_profile_parameters() {
        $tab = new external_value(PARAM_TEXT, 'tab');
        $userid = new external_value(PARAM_INT, 'userid');
        $params = array(
            'tab' => $tab,
            'userid' => $userid
        );
        return new external_function_parameters($params);
    }


    /**
     * Data to render in the related profile section.
     *
     * @param int $tab
     * @return array profile list.
     */
    public static function data_for_profile($tab, $userid) {
        global $CFG,$PAGE,$USER,$DB,$OUTPUT;

        $params = self::validate_parameters(self::data_for_profile_parameters(), array(
            'tab' => $tab,
            'userid' => $userid
        ));

        $user = $DB->get_record('user',array('id' => $userid));

        if (isloggedin() && !isguestuser() && $user->picture > 0) {
            $usercontext = context_user::instance($user->id, IGNORE_MISSING);

            $url = moodle_url::make_pluginfile_url($usercontext->id, 'user', 'icon', null, '/', "f$1"). '?rev=' . $user->picture;
        }else{
            $url = $CFG->wwwroot.'/blocks/userdashboard/test.png';
        }
        $firstaccess = $user->firstaccess > 0 ? date('d-m-Y H:i:s',$user->firstaccess) : 'NA';
        $lastaccess = $user->lastaccess > 0 ? date('d-m-Y H:i:s',$user->lastaccess) : 'NA';
        $data = array('name' => $user->firstname.' '.$user->lastname,'email' => $user->email, 'phone1' => $user->phone1, 'firstaccess' => $firstaccess,'lastaccess' => $lastaccess, 'studentid' => $user->idnumber,'address' => $user->address, 'profile' => $url);
        return $data;
    }

    /**
     * Returns description of data_for_profile_returns() result value.
     *
     * @return external_description
     */
   public static function data_for_profile_returns() {
     

        return new external_single_structure(array (
    
            'name' => new external_value(PARAM_TEXT, 'Name of student.'),           
            'email'=>  new external_value(PARAM_TEXT, 'Email of student.'),
            'phone1'=>  new external_value(PARAM_TEXT, 'phone1 of student.'),
            'address'=>  new external_value(PARAM_TEXT, 'address of student.'),
            'studentid'=>  new external_value(PARAM_TEXT, 'id of student.'),
            'firstaccess'=>  new external_value(PARAM_TEXT, 'firstaccess of student.'),
            'lastaccess'=>  new external_value(PARAM_TEXT, 'lastaccess of student.'),
            'profile'=>  new external_value(PARAM_TEXT, 'profile of student.')
        ));

    }


    public static function data_for_courses_parameters() {

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
    public static function data_for_courses($options,
        $dataoptions,
        $offset = 0,
        $limit = 0,
        $contextid,
        $filterdata) {

        global $OUTPUT, $CFG, $DB,$USER,$PAGE;
        // require_once($CFG->dirroot . '/blocks/facultydashboard/lib.php');
        require_login();
        $PAGE->set_url('/blocks/userdashboard/block_userdashboard.php', array());
        $PAGE->set_context($contextid);
        $context = context_system::instance();
        // Parameter validation.
        $params = self::validate_parameters(
            self::data_for_courses_parameters(),
            [
                'options' => $options,
                'dataoptions' => $dataoptions,
                'offset' => $offset,
                'limit' => $limit,
                'contextid' => $contextid,
                'filterdata' => $filterdata
            ]
        );
        
        $output = $PAGE->get_renderer('block_userdashboard');

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
            $courseslist = $output->usercourses_list($stable,$filtervalues,$decodedata->userid,$decodedata->coursesearch);
        }else{
            $courseslist = $output->usercourses_list($stable,$filtervalues);
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


    public static function data_for_courses_returns() {

        return new external_single_structure([

         'options' => new external_value(PARAM_RAW, 'The paging data for the service'),
         'dataoptions' => new external_value(PARAM_RAW, 'The data for the service'),
         'totalcount' => new external_value(PARAM_INT, 'total number of accounts in system'),
         'filterdata' => new external_value(PARAM_RAW, 'The data for the service'),
         'records' => new external_multiple_structure(

                    new external_single_structure(

                        array(
                            'courseid' => new external_value(PARAM_INT, 'course id'),    
                            'coursename'=>  new external_value(PARAM_RAW, 'course name'),  
                            'coursecode'=>  new external_value(PARAM_RAW, 'course code'), 
                            'categoryname'=>  new external_value(PARAM_RAW, 'category name') ,
                            'courseurl'=>  new external_value(PARAM_RAW, 'courseurl'),
                            'progress'=>  new external_value(PARAM_RAW, 'progress'),  
                            'currentdays' => new external_value(PARAM_RAW, 'currentdays'),
                            'totaldays' => new external_value(PARAM_RAW, 'totaldays'),
                            'coursepic' => new external_value(PARAM_RAW, 'coursepic'),
                            'imgurlflag' => new external_value(PARAM_RAW, 'imgurlflag'),
                             )
                    )
                )
        ]);
    }


    public static function data_for_assesments_parameters() {

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
    public static function data_for_assesments($options,
        $dataoptions,
        $offset = 0,
        $limit = 0,
        $contextid,
        $filterdata) {

        global $OUTPUT, $CFG, $DB,$USER,$PAGE;
        // require_once($CFG->dirroot . '/blocks/facultydashboard/lib.php');
        require_login();
        $PAGE->set_url('/blocks/userdashboard/block_userdashboard.php', array());
        $PAGE->set_context($contextid);
        $context = context_system::instance();
        // Parameter validation.
        $params = self::validate_parameters(
            self::data_for_assesments_parameters(),
            [
                'options' => $options,
                'dataoptions' => $dataoptions,
                'offset' => $offset,
                'limit' => $limit,
                'contextid' => $contextid,
                'filterdata' => $filterdata
            ]
        );
        
        $output = $PAGE->get_renderer('block_userdashboard');

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
            $assignlist = $output->userassign_list($stable,$filtervalues,$decodedata->userid,$decodedata->assesmentsearch);
        }else{
            $assignlist = $output->userassign_list($stable,$filtervalues);
        }
        
        $totalcount = $assignlist['totalcount'];
        $data=$assignlist['data'];
        // print_r($courseslist);
        return [
            'totalcount' => $totalcount,
            'records' =>$data,
            'options' => $options,
            'dataoptions' => $dataoptions,
            'filterdata' => $filterdata,
        ];
            
    }


    public static function data_for_assesments_returns() {

        return new external_single_structure([

         'options' => new external_value(PARAM_RAW, 'The paging data for the service'),
         'dataoptions' => new external_value(PARAM_RAW, 'The data for the service'),
         'totalcount' => new external_value(PARAM_INT, 'total number of accounts in system'),
         'filterdata' => new external_value(PARAM_RAW, 'The data for the service'),
         'records' => new external_multiple_structure(

                    new external_single_structure(

                        array (
                
                            'moduleid' => new external_value(PARAM_INT, 'module id'),          
                            'assesmentname'=>  new external_value(PARAM_RAW, 'assesmentname'),  
                            'coursename'=>  new external_value(PARAM_RAW, 'coursename'), 
                            'startdate'=>  new external_value(PARAM_RAW, 'startdate'), 
                            'duedate'=>  new external_value(PARAM_RAW, 'duedate') ,
                            'assignurl'=>  new external_value(PARAM_RAW, 'assignurl'),
                            'status'=>  new external_value(PARAM_RAW, 'status') ,
                            'grade'=>  new external_value(PARAM_RAW, 'grade'),
                            'courseurl'=>  new external_value(PARAM_RAW, 'courseurl'),
                        )
                   )
                )
            ]);
    }


     public static function data_for_exams_parameters() {

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
    public static function data_for_exams($options,
        $dataoptions,
        $offset = 0,
        $limit = 0,
        $contextid,
        $filterdata) {

        global $OUTPUT, $CFG, $DB,$USER,$PAGE;
        require_login();
        $PAGE->set_url('/blocks/userdashboard/block_userdashboard.php', array());
        $PAGE->set_context($contextid);
        $context = context_system::instance();
        // Parameter validation.
        $params = self::validate_parameters(
            self::data_for_exams_parameters(),
            [
                'options' => $options,
                'dataoptions' => $dataoptions,
                'offset' => $offset,
                'limit' => $limit,
                'contextid' => $contextid,
                'filterdata' => $filterdata
            ]
        );
        
        $output = $PAGE->get_renderer('block_userdashboard');

        $offset = $params['offset'];
        $limit = $params['limit'];
        $decodedata = json_decode($params['dataoptions']);
        $filtervalues = json_decode($filterdata);
        $stable = new \stdClass();
        $stable->thead = true;
       
        $stable->thead = false;
        $stable->start = $offset;
        $stable->length = $limit;
        
        if($decodedata->userid > 0){
            $examlist = $output->userexam_list($stable,$filtervalues,$decodedata->userid,$decodedata->examsearch);
        }else{
            $examlist = $output->userexam_list($stable,$filtervalues);
        }
        
        $totalcount = $examlist['totalcount'];
        $data = $examlist['data'];
        
        return [
            'totalcount' => $totalcount,
            'records' =>$data,
            'options' => $options,
            'dataoptions' => $dataoptions,
            'filterdata' => $filterdata,
        ];
            
    }


    public static function data_for_exams_returns() {

        return new external_single_structure([

         'options' => new external_value(PARAM_RAW, 'The paging data for the service'),
         'dataoptions' => new external_value(PARAM_RAW, 'The data for the service'),
         'totalcount' => new external_value(PARAM_INT, 'total number of accounts in system'),
         'filterdata' => new external_value(PARAM_RAW, 'The data for the service'),
         'records' => new external_multiple_structure(

                    new external_single_structure(

                        array (
                            'moduleid' => new external_value(PARAM_INT, 'module id'),          
                            'examname'=>  new external_value(PARAM_RAW, 'examname'),  
                            'coursename'=>  new external_value(PARAM_RAW, 'coursename'), 
                            'startdate'=>  new external_value(PARAM_RAW, 'startdate'), 
                            'enddate'=>  new external_value(PARAM_RAW, 'enddate') ,
                            'examurl'=>  new external_value(PARAM_RAW, 'examurl'),
                            'status'=>  new external_value(PARAM_RAW, 'status') ,
                            'grade'=>  new external_value(PARAM_RAW, 'grade'),
                            'courseurl'=>  new external_value(PARAM_RAW, 'courseurl'),
                        )
                   )
                )
        ]);
    }


     public static function data_for_forums_parameters() {

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
    public static function data_for_forums($options,
        $dataoptions,
        $offset = 0,
        $limit = 0,
        $contextid,
        $filterdata) {

        global $OUTPUT, $CFG, $DB,$USER,$PAGE;
        require_login();
        $PAGE->set_url('/blocks/userdashboard/block_userdashboard.php', array());
        $PAGE->set_context($contextid);
        $context = context_system::instance();
        // Parameter validation.
        $params = self::validate_parameters(
            self::data_for_forums_parameters(),
            [
                'options' => $options,
                'dataoptions' => $dataoptions,
                'offset' => $offset,
                'limit' => $limit,
                'contextid' => $contextid,
                'filterdata' => $filterdata
            ]
        );
        
        $output = $PAGE->get_renderer('block_userdashboard');

        $offset = $params['offset'];
        $limit = $params['limit'];
        $decodedata = json_decode($params['dataoptions']);
        $filtervalues = json_decode($filterdata);
        $stable = new \stdClass();
        $stable->thead = true;
       
        $stable->thead = false;
        $stable->start = $offset;
        $stable->length = $limit;
        
        if($decodedata->userid > 0){
            $examlist = $output->userforum_list($stable,$filtervalues,$decodedata->userid,$decodedata->forumsearch);
        }else{
            $examlist = $output->userforum_list($stable,$filtervalues);
        }
        
        $totalcount = $examlist['totalcount'];
        $data = $examlist['data'];
        
        return [
            'totalcount' => $totalcount,
            'records' =>$data,
            'options' => $options,
            'dataoptions' => $dataoptions,
            'filterdata' => $filterdata,
        ];
    }


    public static function data_for_forums_returns() {

        return new external_single_structure([

         'options' => new external_value(PARAM_RAW, 'The paging data for the service'),
         'dataoptions' => new external_value(PARAM_RAW, 'The data for the service'),
         'totalcount' => new external_value(PARAM_INT, 'total number of accounts in system'),
         'filterdata' => new external_value(PARAM_RAW, 'The data for the service'),
         'records' => new external_multiple_structure(

                    new external_single_structure(

                        array (
                            'moduleid' => new external_value(PARAM_INT, 'module id'),          
                            'forumname'=>  new external_value(PARAM_RAW, 'forumname'),  
                            'coursename'=>  new external_value(PARAM_RAW, 'coursename'), 
                            'discussion'=>  new external_value(PARAM_RAW, 'discussion'), 
                            'forumurl'=>  new external_value(PARAM_RAW, 'forumurl'),
                            'discussionurl'=>  new external_value(PARAM_RAW, 'discussionurl'),
                            'courseurl'=>  new external_value(PARAM_RAW, 'courseurl'),
                            'status'=>  new external_value(PARAM_RAW, 'status') ,
                            'grade'=>  new external_value(PARAM_RAW, 'grade'),
                            'posts'=>  new external_value(PARAM_INT, 'posts'),
                        )
                   )
                )
        ]);
    }


    /**
     * Returns the description of the 
     data_for_reports_parameters.
     *
     * @return external_function_parameters.
     */
    public static function data_for_reports_parameters() {
        $tab = new external_value(PARAM_TEXT, 'tab');
        $userid = new external_value(PARAM_INT, 'userid');
        $courseid = new external_value(PARAM_INT, 'courseid');
        $graphval = new external_value(PARAM_TEXT, 'graphval');
        $params = array(
            'tab' => $tab,
            'userid' => $userid,
            'courseid' => $courseid,
            'graphval' => $graphval
        );
        return new external_function_parameters($params);
    }


    /**
     * Data to render in the related reports section.
     *
     * @param int $tab
     * @return array reports list.
     */
    public static function data_for_reports($tab, $userid, $courseid, $graphval) {
        global $USER,$DB, $CFG;

        $params = self::validate_parameters(self::data_for_reports_parameters(), array(
            'tab' => $tab,
            'userid' => $userid,
            'courseid' => $courseid,
            'graphval' =>$graphval
        ));

       
         $data =  array();
        $maindata = array();

        $coursesql = "SELECT 
                    c.id, c.fullname as coursename, c.shortname as coursecode
                    FROM mdl_user u
                    INNER JOIN {role_assignments} ra ON ra.userid = u.id and ra.roleid = 5
                    INNER JOIN {context} ct ON ct.id = ra.contextid 
                    INNER JOIN {course} c ON c.id = ct.instanceid
                    WHERE ra.userid =:userid AND c.visible = 1 AND ct.contextlevel = 50
                ";
                $courseparams = array('userid'=>$userid);
                // if($courseid > 0){
                //     $coursesql .= " AND c.id =:courseid ";
                //     $courseparams['courseid'] = $courseid;
                // }

        $totalactivecourses =  $DB->get_records_sql($coursesql,$courseparams);

        $newcoursedata =  array();
        $selectedcourse = '';
        foreach($totalactivecourses as $key => $totalactivecourse){
            $newcoursessdata =  array();
            $newcoursessdata['id'] = $totalactivecourse->id;
            $newcoursessdata['coursename'] = $totalactivecourse->coursename.' ('.$totalactivecourse->coursecode.')';
            if($totalactivecourse->id == $courseid){
                $selectedcourse = true;
            }else{
                $selectedcourse = '';
            }
            $newcoursessdata['selectedcourse'] = $selectedcourse;
            $newcoursedata[] = $newcoursessdata;
        }
        $maindata['courses'] = $newcoursedata;

        $assesssql = "SELECT 
                    cm.id as moduleid,a.id as assignid,c.id, c.fullname as coursename, c.shortname as coursecode, a.name as assesmentname, a.allowsubmissionsfromdate as startdate,a.duedate,ag.grade as grade
                    FROM mdl_user u
                    INNER JOIN {role_assignments} ra ON ra.userid = u.id
                    INNER JOIN {context} ct ON ct.id = ra.contextid
                    INNER JOIN {course} c ON c.id = ct.instanceid
                    INNER JOIN {course_modules} cm ON cm.course = c.id
                    INNER JOIN {assign} a ON a.course = cm.course AND cm.instance = a.id AND cm.module = (SELECT id FROM mdl_modules WHERE name = 'assign')
                    INNER JOIN {assign_grades} as ag ON ag.userid = ra.userid AND ag.assignment = a.id
                    WHERE ra.userid =:userid and ra.roleid = 5 AND cm.visible = 1 AND c.visible = 1 AND ct.contextlevel = 50
                ";
            $assesparams = array('userid'=>$userid);
                if($courseid > 0){
                    $assesssql .= " AND c.id =:courseid ";
                    $assesparams['courseid'] = $courseid;
                }
        $totalassesments =  $DB->get_records_sql($assesssql,$assesparams);

        $newdata =  array();
        foreach($totalassesments as $key => $totalassesment){
            $newassigndata =  array();
            $newassigndata['moduleid'] = $totalassesment->moduleid;
            $newassigndata['coursename'] = $totalassesment->coursename;
            $newassigndata['assesmentname'] = $totalassesment->assesmentname;
            $newassigndata['grade'] = $totalassesment->grade > 0 ? $totalassesment->grade : '--';
            $newdata[] = $newassigndata;
        }
        $maindata['assesments'] = $newdata;

        $forumsql ="SELECT 
                    cm.id as moduleid,f.id as forumid,c.id, c.fullname as coursename, c.shortname as coursecode, f.name as forumname, fg.grade as grade
                    FROM mdl_user u
                    INNER JOIN {role_assignments} ra ON ra.userid = u.id
                    INNER JOIN {context} ct ON ct.id = ra.contextid
                    INNER JOIN {course} c ON c.id = ct.instanceid
                    INNER JOIN {course_modules} cm ON cm.course = c.id
                    INNER JOIN {forum} f ON f.course = cm.course AND cm.instance = f.id AND cm.module = (SELECT id FROM mdl_modules WHERE name = 'forum') AND f.type != 'news'
                    INNER JOIN {forum_grades} as fg ON fg.userid = ra.userid AND fg.forum = f.id
                    WHERE ra.userid =:userid and ra.roleid = 5 AND cm.visible = 1 AND c.visible = 1 AND ct.contextlevel = 50
                ";

        $forumparams = array('userid'=>$userid);
                if($courseid > 0){
                    $forumsql .= " AND c.id =:courseid ";
                    $forumparams['courseid'] = $courseid;
                }
        $totalforums =  $DB->get_records_sql($forumsql,$forumparams);

        $newfordata =  array();
        foreach($totalforums as $key => $totalforum){
            $newforumdata =  array();
            $newforumdata['moduleid'] = $totalforum->moduleid;
            $newforumdata['coursename'] = $totalforum->coursename;
            $newforumdata['forumname'] = $totalforum->forumname;
            $newforumdata['grade'] = $totalforum->grade > 0 ? $totalforum->grade : '--';
            $newfordata[] = $newforumdata;
        }
        $maindata['forums'] = $newfordata;

        $examsql = "SELECT 
                    cm.id as moduleid,q.id as quizid,c.id, c.fullname as coursename, c.shortname as coursecode, q.name as examname, q.timeopen as startdate,q.timeclose as enddate,qg.grade as grade
                    FROM mdl_user u
                    INNER JOIN {role_assignments} ra ON ra.userid = u.id
                    INNER JOIN {context} ct ON ct.id = ra.contextid
                    INNER JOIN {course} c ON c.id = ct.instanceid
                    INNER JOIN {course_modules} cm ON cm.course = c.id
                    INNER JOIN {quiz} q ON q.course = cm.course AND cm.instance = q.id AND cm.module = (SELECT id FROM mdl_modules WHERE name = 'quiz')
                    INNER JOIN {quiz_grades} as qg ON qg.userid = ra.userid AND qg.quiz = q.id
                    WHERE ra.userid =:userid and ra.roleid = 5 AND cm.visible = 1 AND c.visible = 1 AND ct.contextlevel = 50
                ";
                $examparams = array('userid'=>$userid);
                if($courseid > 0){
                    $examsql .= " AND c.id =:courseid ";
                    $examparams['courseid'] = $courseid;
                }
        $totalexams =  $DB->get_records_sql($examsql,$examparams);

        $newexdata =  array();
        foreach($totalexams as $key => $totalexam){
            $newexamdata =  array();
            $newexamdata['moduleid'] = $totalexam->moduleid;
            $newexamdata['coursename'] = $totalexam->coursename;
            $newexamdata['examname'] = $totalexam->examname;
            $newexamdata['grade'] = $totalexam->grade > 0 ? $totalexam->grade : '--';
            $newexdata[] = $newexamdata;
        }
        $maindata['exams'] = $newexdata;

        $maindata['graphval'] = $graphval;
        $bargraphval = '';
        $piegraphval = '';
        $linegraphval = '';
        if($graphval == 'bar'){
            $bargraphval = true;
        }elseif($graphval == 'pie'){
            $piegraphval = true;
        }elseif($graphval == 'line'){
            $linegraphval = true;
        }
        $maindata['graphs'] = array(
                                array('id' => 'bar','value' => 'Bar Chart','selected' => $bargraphval),
                                array('id' => 'pie' ,'value'=> 'Pie Chart','selected' =>$piegraphval),
                                array('id' => 'line' ,'value'=> 'Line Chart','selected' =>$linegraphval)
                            );
        // print_r($maindata);exit;
        return $maindata;
    }

    /**
     * Returns description of data_for_reports_returns() result value.
     *
     * @return external_description
     */
   public static function data_for_reports_returns() {
     

        return new external_single_structure([
            'forums' => new external_multiple_structure(
                            new external_single_structure(
                                            array (
                                    
                                                'moduleid' => new external_value(PARAM_INT, 'moduleid id'),          
                                                'forumname'=>  new external_value(PARAM_RAW, 'forumname'),  
                                                'coursename'=>  new external_value(PARAM_RAW, 'coursename'), 
                                                'grade'=>  new external_value(PARAM_RAW, 'grade') ,

                                            )
                                        )
                        ),

            'courses' => new external_multiple_structure(
                            new external_single_structure(
                                            array (
                                    
                                                'id' => new external_value(PARAM_INT, 'id'),          
                                                'coursename'=>  new external_value(PARAM_RAW, 'coursename'), 
                                                'selectedcourse'=>  new external_value(PARAM_RAW, 'selectedcourse'),   
                                            )
                                        )
                        ),
            'assesments' => new external_multiple_structure(
                            new external_single_structure(
                                            array (
                                    
                                                'moduleid' => new external_value(PARAM_INT, 'moduleid id'),          
                                                'assesmentname'=>  new external_value(PARAM_RAW, 'assesmentname'),  
                                                'coursename'=>  new external_value(PARAM_RAW, 'coursename'), 
                                                'grade'=>  new external_value(PARAM_RAW, 'grade') ,

                                            )
                                        )
                        ),
            'exams' => new external_multiple_structure(
                            new external_single_structure(
                                            array (
                                    
                                                'moduleid' => new external_value(PARAM_INT, 'moduleid id'),          
                                                'examname'=>  new external_value(PARAM_RAW, 'examname'),  
                                                'coursename'=>  new external_value(PARAM_RAW, 'coursename'), 
                                                'grade'=>  new external_value(PARAM_RAW, 'grade') ,

                                            )
                                        )
                        ),
            'graphs' => new external_multiple_structure(
                            new external_single_structure(
                                            array (
                                    
                                                'id' => new external_value(PARAM_RAW, 'id'),          
                                                'value'=>  new external_value(PARAM_RAW, 'value'),  
                                                'selected'=>  new external_value(PARAM_RAW, 'selected'), 
                                            )
                                        )
                        ),
            'graphval' => new external_value(PARAM_RAW, 'graphval')
        ]);


    } 

        /**
     * Returns the description of the 
     data_for_profile_parameters.
     *
     * @return external_function_parameters.
     */
    public static function recording_videoproctoringdata_parameters() {
        $attemptid = new external_value(PARAM_INT, 'attemptid');
        $base64 = new external_value(PARAM_RAW, 'base64');
        $cmid = new external_value(PARAM_INT, 'cmid');
        $page = new external_value(PARAM_INT, 'page');

        $params = array(
            'attemptid' => $attemptid,
            'base64' => $base64,
            'cmid' => $cmid,
            'page' => $page
        );
        return new external_function_parameters($params);
    }


    /**
     * Data to render in the related profile section.
     *
     * @param int $tab
     * @return array profile list.
     */
    public static function recording_videoproctoringdata($attemptid, $base64, $cmid, $page) {
        global $USER,$DB, $CFG;

        $params = self::validate_parameters(self::recording_videoproctoringdata_parameters(), array(
            'attemptid' => $attemptid,
            'base64' => $base64,
            'cmid' => $cmid,
            'page' => $page
        ));

        if (!is_dir($CFG->dirroot.'/mod/quiz/recordings/'.$USER->id.'/'.$cmid.'/'.$attemptid)) {
            $val = make_writable_directory($CFG->dirroot.'/mod/quiz/recordings/'.$USER->id.'/'.$cmid.'/'.$attemptid, true);
        }
        if (is_dir($CFG->dirroot.'/mod/quiz/recordings/'.$USER->id.'/'.$cmid.'/'.$attemptid)) {
            $filename = 'recordings-'.$USER->id.'-'.$cmid.'-'.$attemptid.'-'.$page.'.mp4';
            $success = file_put_contents($CFG->dirroot.'/mod/quiz/recordings/'.$USER->id.'/'.$cmid.'/'.$attemptid.'/'.$filename, base64_decode($base64));
        }

        $data = array('status' => $success);
        return $data;
    }

    /**
     * Returns description of data_for_profile_returns() result value.
     *
     * @return external_description
     */
    public static function recording_videoproctoringdata_returns() {
        return new external_single_structure(array (
            'status' => new external_value(PARAM_RAW, 'record status.')           
        ));
    }

    public static function search_for_students_parameters() {
        return new external_function_parameters(
            array(
                
                'search' => new external_value(PARAM_RAW, ' contextid
                    ')
               
            )
        );
    }


    public static function search_for_students($search) {
                global $DB, $CFG, $USER,$COURSE;

            $params = self::validate_parameters(self::search_for_students_parameters(),
                                            ['search' => $search]);

            $users =  $DB->get_records_sql("SELECT 
                u.id,u.firstname,u.lastname FROM mdl_user u 
                INNER JOIN {role_assignments} ra ON ra.userid = u.id
                INNER JOIN {context} ct ON ct.id = ra.contextid
                INNER JOIN {course} c ON c.id = ct.instanceid
                INNER JOIN {role} r ON r.id = ra.roleid
                WHERE ra.roleid in (5) AND c.visible = 1 AND ct.contextlevel = 50 AND ra.userid > 2 AND u.suspended = 0 AND u.deleted = 0 AND (u.idnumber like '%$search%' or u.email like '%$search%' or u.firstname like '%$search%' or u.lastname like '%$search%' or u.username like '%$search%') ");
            $data = [];
            foreach ($users as $user) {
                 $subdata = array();
                 $subdata['studentid'] = $user->id;
                 $subdata['studentname'] = $user->firstname.' '.$user->lastname;
     
                $data[] =$subdata;
            }


            $return = [];
            $return['users'] = $data;

           return $return;
    }





    public static function search_for_students_returns() {
        return new external_single_structure (
            array(
               'users' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'studentid' => new external_value(PARAM_RAW, 'id of the student'),
                            'studentname' => new external_value(PARAM_RAW, 'name of the student'),
                          )
                    )
                ),
            )
        );
    } 

}
