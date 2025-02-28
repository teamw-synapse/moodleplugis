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
 * block user dashboard rendrer
 *
 * @package    user dashboard
 * @copyright  2024 Vivenns Global
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
// use core_component;
class block_userdashboard_renderer extends plugin_renderer_base {


    public function usercourses_list($stable,$filterdata, $userid = 0, $search = '') {
        global $DB, $PAGE,$USER,$CFG,$OUTPUT;
        require_once($CFG->dirroot . '/blocks/facultydashboard/lib.php');
        $systemcontext = context_system::instance(); 
        $params = array();
        $userid = $userid > 0 ? $userid : $USER->id;
        $countsql = "SELECT count(c.id) ";
        $selectsql = "SELECT 
                    c.id, c.fullname, c.shortname, cc.name as categoryname,c.startdate,c.enddate ";    
        $formsql  = "FROM mdl_user u
                    INNER JOIN {role_assignments} ra ON ra.userid = u.id
                    INNER JOIN {context} ct ON ct.id = ra.contextid
                    INNER JOIN {course} c ON c.id = ct.instanceid
                    INNER JOIN {course_categories} cc ON cc.id = c.category
                    WHERE ra.userid =:userid and ra.roleid = 5 AND c.visible = 1 AND ct.contextlevel = 50 ";
        $params['userid'] = $userid;
        if($search){
            $formsql  .= " AND (c.fullname LIKE '%$search%' OR c.shortname LIKE '%$search%') ";
        }
        $totalcourses = $DB->count_records_sql($countsql.$formsql,$params);

        // $ordersql = " ORDER BY c.id DESC";
        $mycourses = $DB->get_records_sql($selectsql.$formsql,$params,$stable->start,$stable->length);
        $data = array();
        // $maindata = array();
        foreach($mycourses as $course){
            $coursedata =  array();
            $courseimage = get_courses_image($course);
            $coursedata['coursepic'] = $courseimage['imageurl'];
            $coursedata['imgurlflag'] = $courseimage['imgurlflag'];
            $coursedata['courseid'] = $course->id;
            $coursedata['coursename'] = $course->fullname;
            $coursedata['coursecode'] = $course->shortname;
            $coursedata['categoryname'] = $course->categoryname;
            $coursedata['courseurl'] = $CFG->wwwroot.'/course/view.php?id='.$course->id;
            $progress1 = \core_completion\progress::get_course_progress_percentage($course, $userid);
            $progress = round($progress1, 2);
            // $comppercent = number_format($progress, 0);
            if($course->enddate > 0 && $course->startdate > 0){
                $totaldatediff = $course->enddate - $course->startdate;
                $totaldays = round($totaldatediff / (60 * 60 * 24));
                $now =  time();
                $datediff = $now-$course->startdate;
                $currentdays = round($datediff / (60 * 60 * 24));
            }else{
                $currentdays = 0;
                $totaldays = 0;
            }
            $coursedata['totaldays'] = $totaldays;
            $coursedata['currentdays'] = $currentdays;
            $coursedata['progress'] = $progress ? $progress : 0;
            $data[] = $coursedata;
        }
        // return $maindata;
        return array('totalcount' => $totalcourses,'data' => $data);
    }


    public function userassign_list($stable,$filterdata, $userid = 0,$search='') {
        global $DB, $PAGE,$USER,$CFG,$OUTPUT;
        require_once($CFG->dirroot . '/blocks/facultydashboard/lib.php');
        $systemcontext = context_system::instance(); 
        $params = array();
        $userid = $userid > 0 ? $userid : $USER->id;
        $countsql = "SELECT count(a.id) ";
        $selectsql = "SELECT cm.id as moduleid,a.id as assignid,c.id, c.fullname as coursename, c.shortname as coursecode, a.name as assesmentname, a.allowsubmissionsfromdate as startdate,a.duedate, a.grade as grademethod ";    
        $formsql  = "FROM mdl_user u
                    INNER JOIN {role_assignments} ra ON ra.userid = u.id
                    INNER JOIN {context} ct ON ct.id = ra.contextid
                    INNER JOIN {course} c ON c.id = ct.instanceid
                    INNER JOIN {course_modules} cm ON cm.course = c.id
                    INNER JOIN {assign} a ON a.course = cm.course AND cm.instance = a.id AND cm.module = 1
                    WHERE ra.userid =:userid and ra.roleid = 5 AND cm.visible = 1 AND c.visible = 1 AND ct.contextlevel = 50 ";
        $params['userid'] = $userid;
        if($search){
            $formsql .= " AND (a.name LIKE '%$search%' OR c.fullname LIKE '%$search%' OR c.shortname LIKE '%$search%') ";
        }
        // echo $countsql.$formsql;
        $totalassign = $DB->count_records_sql($countsql.$formsql,$params);
        // echo $selectsql.$formsql;
        // $ordersql = " ORDER BY c.id DESC";
        $totalassesments = $DB->get_records_sql($selectsql.$formsql,$params,$stable->start,$stable->length);
        $data = array();
        foreach($totalassesments as $key => $assesment){
            $assigndata =  array();
            $substatus = $DB->get_record('assign_submission',array('assignment' => $assesment->assignid,'userid' => $userid));
            if($assesment->grademethod == 0){
                $grade = '';
            }else if($assesment->grademethod > 0){
                $grade = $DB->get_field('assign_grades','grade',array('assignment' => $assesment->assignid,'userid' => $userid));
                $grade = $grade ? round($grade, 2) : '';
            }else if($assesment->grademethod < 0){
                $assign_grade= $DB->get_record_sql("SELECT gi.grademax from  mdl_grade_items as gi 
                                     where gi.courseid = $assesment->id and gi.iteminstance =$assesment->assignid and gi.itemmodule = 'assign'");
                $assign_grader = $DB->get_field_sql("SELECT MAX(grade) as grade FROM mdl_assign_grades WHERE assignment =:assignment AND userid=:userid LIMIT 1",array('assignment' => $assesment->assignid,'userid' => $userid));
                if($assign_grader>0){
                    $gradeletter =  $DB->get_record_sql("SELECT l.letter FROM mdl_grade_letters l
                                     join mdl_context x on l.contextid = x.id
                                     WHERE x.contextlevel = 50
                                     and x.instanceid =$assesment->id and l.lowerboundary <= (100/($assign_grade->grademax/$assign_grader))
                                     ORDER BY x.id desc, lowerboundary desc limit 1");
                    $grade = $gradeletter->letter;
                }else{
                    $grade = '';
                }
            }
            
            $assigndata['moduleid'] = $assesment->moduleid;
            $assigndata['assesmentname'] = $assesment->assesmentname;
            $assigndata['coursename'] = $assesment->coursename;
            $assigndata['startdate'] = $assesment->startdate > 0?userdate($assesment->startdate,'%d/%m/%Y'):'NA';
            $assigndata['duedate'] = $assesment->duedate > 0?userdate($assesment->duedate,'%d/%m/%Y'):'NA';
            $assigndata['status'] = $substatus->status == 'submitted' ? 'Submitted' : 'Not Submitted';
            if($substatus->status == 'submitted' && $grade){
                $grade = $grade;
            }else if($substatus->status == 'submitted' && !$grade){
                $grade = 'Not Graded';
            }else{
                $grade = '--';
            }
            $assigndata['grade'] = $grade;
            $assigndata['assignurl'] = $CFG->wwwroot.'/mod/assign/view.php?id='.$assesment->moduleid;
            $assigndata['courseurl'] = $CFG->wwwroot.'/course/view.php?id='.$assesment->id;
            $data[] = $assigndata;
        }

        return array('totalcount' => $totalassign,'data' => $data);
    }

    public function userexam_list($stable,$filterdata, $userid = 0, $search = '') {
        global $DB, $PAGE,$USER,$CFG,$OUTPUT;
        require_once($CFG->dirroot . '/blocks/facultydashboard/lib.php');
        $systemcontext = context_system::instance(); 
        $params = array();
        $userid = $userid > 0 ? $userid : $USER->id;
        $countsql = "SELECT count(q.id) ";
        $selectsql = "SELECT cm.id as moduleid,c.id, q.id as quizid,c.fullname as coursename, c.shortname as coursecode, q.name as examname, q.timeopen as startdate,q.timeclose as enddate ";    
        $formsql  = "FROM mdl_user u
                    INNER JOIN {role_assignments} ra ON ra.userid = u.id
                    INNER JOIN {context} ct ON ct.id = ra.contextid
                    INNER JOIN {course} c ON c.id = ct.instanceid
                    INNER JOIN {course_modules} cm ON cm.course = c.id
                    INNER JOIN {quiz} q ON q.course = cm.course AND cm.instance = q.id AND cm.module = (SELECT id FROM mdl_modules WHERE name = 'quiz')
                    WHERE ra.userid =:userid and ra.roleid = 5 AND cm.visible = 1 AND c.visible = 1 AND ct.contextlevel = 50 ";
        if($search){
            $formsql  .= " AND (q.name LIKE '%$search%' OR c.fullname LIKE '%$search%' OR c.shortname LIKE '%$search%') ";
        }
        $params['userid'] = $userid;
        // echo $countsql.$formsql;
        $totalexams = $DB->count_records_sql($countsql.$formsql,$params);

        // $ordersql = " ORDER BY c.id DESC";
        // echo $selectsql.$formsql;
        $totalquizes = $DB->get_records_sql($selectsql.$formsql,$params,$stable->start,$stable->length);
        $data =  array();
        foreach($totalquizes as $key => $exam){
            $examdata =  array();
            $grade = $DB->get_field('quiz_grades','grade',array('quiz' => $exam->quizid,'userid' => $userid));
            $status = $DB->get_field_sql("SELECT id FROM {course_modules_completion} WHERE coursemoduleid =:coursemoduleid AND completionstate > 0 AND userid =:userid",array('coursemoduleid' => $exam->moduleid,'userid' => $userid));
            $examdata['moduleid'] = $exam->moduleid;
            $examdata['examname'] = $exam->examname;
            $examdata['coursename'] = $exam->coursename;
            $examdata['startdate'] = $exam->startdate > 0?userdate($exam->startdate,'%d/%m/%Y'):'NA';
            $examdata['enddate'] = $exam->enddate > 0 ?userdate($exam->enddate,'%d/%m/%Y'):'NA';
            $examdata['status'] = $status ? 'Submitted' : 'Not Submitted';
            if($examdata['status'] == 'Submitted' && $grade){
                $grade = round($grade, 2);
            }else if($examdata['status'] == 'Submitted' && !$grade){
                $grade = 'Not Graded';
            }else{
                $grade = '--';
            }
            $examdata['grade'] = $grade;
            $examdata['examurl'] = $CFG->wwwroot.'/mod/quiz/view.php?id='.$exam->moduleid;
            $examdata['courseurl'] = $CFG->wwwroot.'/course/view.php?id='.$exam->id;
            $data[] = $examdata;
        }

        return array('totalcount' => $totalexams, 'data' => $data);
    }


    public function userforum_list($stable,$filterdata, $userid = 0, $search = '') {
        global $DB, $PAGE,$USER,$CFG,$OUTPUT;
        require_once($CFG->dirroot . '/blocks/facultydashboard/lib.php');
        $systemcontext = context_system::instance(); 
        $params = array();
        $userid = $userid > 0 ? $userid : $USER->id;

        $countsql = "SELECT count(fd.id) ";
        $selectsql = "SELECT fd.id as discussionid,cm.id as moduleid,c.id, f.id as forumid,c.fullname as coursename, c.shortname as coursecode, f.name as forumname, fd.name as discussion, f.course as courseid ";    
        $formsql  = " FROM mdl_user u
                    INNER JOIN {role_assignments} ra ON ra.userid = u.id
                    INNER JOIN {context} ct ON ct.id = ra.contextid
                    INNER JOIN {course} c ON c.id = ct.instanceid
                    INNER JOIN {course_modules} cm ON cm.course = c.id
                    INNER JOIN {forum} f ON f.course = cm.course AND cm.instance = f.id AND cm.module = (SELECT id FROM {modules} WHERE name = 'forum') AND f.type != 'news'
                    JOIN mdl_forum_discussions AS fd ON fd.forum = f.id AND fd.course = c.id
                    WHERE ra.userid =:userid and ra.roleid = 5 AND cm.visible = 1 AND c.visible = 1 AND ct.contextlevel = 50 ";
        if($search){
            $formsql  .= " AND (fd.name LIKE '%$search%' OR f.name LIKE '%$search%' OR c.fullname LIKE '%$search%' OR c.shortname LIKE '%$search%') ";
        }
        $params['userid'] = $userid;
        // echo $countsql.$formsql;
        $totalforums = $DB->count_records_sql($countsql.$formsql,$params);

        // $ordersql = " ORDER BY c.id DESC";
        // echo $selectsql.$formsql;
        $totalforumsdata = $DB->get_records_sql($selectsql.$formsql,$params,$stable->start,$stable->length);
        // print_r($totalforumsdata);exit;
        $data =  array();
        foreach($totalforumsdata as $key => $forum){
            $forumdata =  array();
            $grade = $DB->get_field('forum_grades','grade',array('forum' => $forum->forumid,'userid' => $userid));
            $status = $DB->get_field_sql("SELECT id FROM {course_modules_completion} WHERE coursemoduleid =:coursemoduleid AND completionstate > 0 AND userid =:userid",array('coursemoduleid' => $forum->moduleid,'userid' => $userid));
            $posts = $DB->count_records_sql("SELECT COUNT(id) FROM mdl_forum_posts WHERE discussion = :discussionid AND userid =:userid AND parent > 0 ",array('discussionid' => $forum->discussionid,'userid' => $userid));
            // print_r($posts);
            $forumdata['moduleid'] = $forum->moduleid;
            $forumdata['forumname'] = $forum->forumname;
            $forumdata['coursename'] = $forum->coursename;
            $forumdata['discussion'] = $forum->discussion;
            $forumdata['status'] = $status ? 'Submitted' : 'Not Submitted';

            if($forumdata['status'] == 'Submitted' && $grade){
                $grade = round($grade, 2);
            }else if($forumdata['status'] == 'Submitted' && !$grade){
                $grade = 'Not Graded';
            }else{
                $grade = '--';
            }

            $forumdata['grade'] = $grade;
            $forumdata['posts'] = $posts > 0 ? $posts : 0;
            $forumdata['forumurl'] = $CFG->wwwroot.'/mod/forum/view.php?id='.$forum->moduleid;
            $forumdata['discussionurl'] = $CFG->wwwroot.'/mod/forum/discuss.php?d='.$forum->discussionid;
            $forumdata['courseurl'] = $CFG->wwwroot.'/course/view.php?id='.$forum->courseid;

            $data[] = $forumdata;
        }

        return array('totalcount' => $totalforums, 'data' => $data);
    }
}