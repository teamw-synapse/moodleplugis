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
 * @package  block_facultydashboard
 * @copyright 
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


function block_facultydashboard_output_fragment_assignment_details($args){
 global $DB,$CFG,$PAGE,$OUTPUT;

           $myassign = $DB->get_records_sql("SELECT 
                 concat(a.id,'_',c.id) as sidi,
                 a.id as assignid,
                 a.name as assign_name,
                 c.fullname as course_name,
                 cm.id as module_id,
                (select count(DISTINCT asub.userid) from {assign_submission} as asub WHERE assignment = a.id and latest = 1 AND status = 'submitted') as submission_count,
                (SELECT COUNT(ra.userid) from {role_assignments} as ra 
                    join {role} as r on r.id = ra.roleid 
                    where ra.contextid = ctx.id and r.id = 5) as user_count
                from {course} as c 
                join {assign} as a on a.course = c.id 
                join {course_modules} as cm on cm.course = c.id 
                join {modules} as m on  m.id = cm.module
                join {context} as ctx on ctx.instanceid = c.id 
                join {role_assignments} as ra on ra.contextid = ctx.id
                where c.id =:courseid and cm.instance = a.id and m.name = 'assign' and ctx.contextlevel = 50 and ra.roleid = 5 GROUP BY a.id, c.id;", array('courseid'=>$args['courseid']));

            $assign_data = array();

            foreach ($myassign as $assign) {
                 $assign_url = $CFG->wwwroot.'/mod/assign/view.php?id='.$assign->module_id;

                 $assign_details = array();

                 $assign_details['assign_name'] = $assign->assign_name; 
                 $assign_details['course_name'] = $assign->course_name; 
                 $assign_details['user_count'] = $assign->user_count;
                 $assign_details['submission_count'] = $assign->submission_count;
                 $assign_details['assign_url'] = $assign_url;
                 $assign_details['assignid'] = $assign->assignid;
                 $assign_details['courseid'] = $args['courseid'];

                 $assign_data[] = $assign_details;

            }

            $assignmaindata =  $OUTPUT->render_from_template('block_facultydashboard/assignmentdisplay', array('assignment'=>$assign_data));
             return $assignmaindata;


            // print_r($myassign);
            // exit;

}

function block_facultydashboard_output_fragment_quiz_details($args){
 global $DB,$CFG,$PAGE,$OUTPUT;
    $myquiz = $DB->get_records_sql("SELECT concat(q.id,'_',c.id) as sidi,
                q.id as quizid,
                q.name as quiz_name,
                cm.id as module_id,
                c.fullname as course_name,
                
                (select count(DISTINCT qa.userid) from {quiz} as q 
                    join {quiz_attempts} as qa on q.id = qa.quiz
                    join {user} as u on qa.userid = u.id
                    join {role_assignments} as ra on u.id = ra.userid
                    where ra.roleid = 5 and qa.state = 'finished' and q.course = c.id and q.id = quizid) as submission_count,
                (SELECT COUNT(ra.userid) from {role_assignments} as ra 
                    join {role} as r on r.id = ra.roleid
                    where ra.contextid = ctx.id and r.id = 5) as user_count
                from {course} as c 
                join {quiz} as q on q.course = c.id
                join {course_modules} as cm on cm.course = c.id and cm.instance = q.id AND cm.module = (SELECT id FROM {modules} WHERE name = 'quiz')
                join {context} as ctx on ctx.instanceid = cm.course 
                join {role_assignments} as ra on ra.contextid = ctx.id
                where c.id = :courseid and cm.instance = q.id and ctx.contextlevel = 50 and ra.roleid = 5 GROUP BY q.id, c.id;", array('userid'=>$args['userid'], 'courseid'=>$args['courseid']));

            $quiz_data = array();

            foreach ($myquiz as $quiz) {

                $quiz_url = $CFG->wwwroot.'/mod/quiz/view.php?id='.$quiz->module_id;

                $quiz_details = array();
                
                 $quiz_details['quiz_name'] = $quiz->quiz_name; 
                 $quiz_details['course_name'] = $quiz->course_name; 
                 $quiz_details['user_count'] = $quiz->user_count;
                 $quiz_details['submission_count'] = $quiz->submission_count;
                 $quiz_details['quiz_url'] = $quiz_url;
                 $quiz_details['quizid'] = $quiz->quizid;
                 $quiz_details['courseid'] = $args['courseid'];

                 $quiz_data[] = $quiz_details;

            }

            $quizmaindata =  $OUTPUT->render_from_template('block_facultydashboard/quizdisplay', array('quiz'=>$quiz_data));
             return $quizmaindata;


}

function block_facultydashboard_output_fragment_user_details($args){
 global $DB,$CFG,$PAGE,$OUTPUT;


         $is_siteadmin = is_siteadmin($USER->id);

     
         $sql = 'SELECT DISTINCT(u.id),cc.id as completionid, u.idnumber AS id_number, u.username AS user_name, u.email AS user_email,
                     CASE WHEN cc.timecompleted IS NOT NULL THEN "completed" ELSE "not completed" END AS course_status
                 FROM {user_enrolments} AS ue
                 JOIN {enrol} AS e ON ue.enrolid = e.id
                 JOIN {course} AS c ON e.courseid = c.id
                 JOIN {user} AS u ON u.id = ue.userid
                 LEFT JOIN {course_completions} AS cc ON u.id = cc.userid AND c.id = cc.course
                 JOIN {role_assignments} AS ra ON ra.userid = u.id
                 JOIN {role} AS r ON r.id = ra.roleid';

         if ($args['type'] == 'faculty') {
             
             $sql .= ' WHERE r.id = 4 AND c.id = :courseid GROUP BY u.id';
             $params = array('courseid' => $args['courseid']);
         } else {
             
             $sql .= ' WHERE r.id = 5 AND c.id = :courseid GROUP BY u.id';
             $params = array('courseid' => $args['courseid']);
         }

    $myusers = $DB->get_records_sql($sql, $params);
            $user_data = array();

            foreach ($myusers as $myuser) {

                 $user_details = array();               
                 $user_details['id_number'] = $myuser->id_number; 
                 $user_details['user_name'] = $myuser->user_name; 
                 $user_details['user_email'] = $myuser->user_email;
                 $user_details['course_status'] = $myuser->course_status;

                 $user_data[] = $user_details;

            }

            $usermaindata =  $OUTPUT->render_from_template('block_facultydashboard/usersdisplay', array('users'=>$user_data));
             return $usermaindata;


}

function block_facultydashboard_output_fragment_forum_details($args){
 global $DB,$CFG,$PAGE,$OUTPUT;

            $myforums = $DB->get_records_sql("SELECT concat(f.id,'_',c.id) as sidi,
                f.name as forum_name,
                f.id as forumid,
                cm.id as module_id,
                c.fullname as course_name,
                (select COUNT(DISTINCT fds.id) from 
                     {context} as ctx  
                    JOIN {forum} as forum on ctx.instanceid = forum.course
                 join {forum_discussions} as fd on fd.forum = forum.id
                 join {forum_discussion_subs} as fds on fds.discussion = fd.id and fds.forum = forum.id
                 join {role_assignments} as ra on ra.contextid = ctx.id AND fds.userid = ra.userid AND ra.roleid = 5 and ctx.contextlevel = 50
                 where forum.course = c.id AND forum.id = f.id) as submission_count,
                (SELECT COUNT(ra.userid) from  {context} as ctx  
                    JOIN {role_assignments} as ra ON ra.contextid = ctx.id
                    join {role} as r on r.id = ra.roleid 
                    where  r.id = 5 AND ctx.instanceid = c.id) as user_count
                from {course} as c 
                join {forum} as f on f.course = c.id
                join {course_modules} as cm on cm.course = c.id 
                join {modules} as m on m.id = cm.module and m.name = 'forum'
                
                where f.type != 'news' and c.id = :courseid and cm.instance = f.id GROUP BY f.id, c.id;", array('courseid'=>$args['courseid']));

            $forum_data = array();

            foreach ($myforums as $myforum) {

                 $forum_url = $CFG->wwwroot.'/mod/forum/view.php?id='.$myforum->module_id;

                 $forum_details = array();               
                 $forum_details['forum_name'] = $myforum->forum_name; 
                 $forum_details['course_name'] = $myforum->course_name; 
                 $forum_details['user_count'] = $myforum->user_count;
                 $forum_details['submission_count'] = $myforum->submission_count;
                 $forum_details['forum_url'] = $forum_url;
                 $forum_details['forumid'] = $myforum->forumid;
                 $forum_details['courseid'] = $args['courseid'];

                 $forum_data[] = $forum_details;

            }

            $forummaindata =  $OUTPUT->render_from_template('block_facultydashboard/forumdisplay', array('forum'=>$forum_data));
             return $forummaindata;

}

function block_facultydashboard_output_fragment_assign_users($args){
 global $DB,$CFG,$PAGE,$OUTPUT;



$myassigndata = $DB->get_records_sql("SELECT distinct ra.id,
               CONCAT(u.firstname,' ', u.lastname) AS student_name,
               u.username AS User,
               u.idnumber AS student_id,
               c.shortname AS course,              
               a.name AS assignment,
               CASE
               WHEN asub.timemodified IS NOT NULL then DATE_FORMAT(FROM_UNIXTIME (asub.timemodified),'%e %b %Y - %H:%i') ELSE 'N/A' END AS 'submitted_date',
            
               CASE 
               WHEN ag.grade = -1 or ag.grade IS NULL THEN 'not_graded' ELSE ag.grade END AS final_grade
               FROM {assign} a 
               JOIN {course} AS c ON a.course = c.id                         
               JOIN {context} AS ctx ON ctx.instanceid = c.id AND ctx.contextlevel = 50
               JOIN {role_assignments} AS ra ON ra.contextid = ctx.id AND ra.roleid = 5
               JOIN {user} AS u ON ra.userid = u.id
               LEFT JOIN {assign_submission} asub on a.id = asub.assignment AND asub.userid =  ra.userid
               LEFT JOIN {assign_grades} as ag on asub.assignment = ag.assignment AND ag.userid = u.id
               JOIN {course_modules} as cm on cm.course = c.id and a.id = cm.instance
               JOIN {modules} as m on  m.id = cm.module and module = 1               
               WHERE c.id = :courseid AND a.id = :assignment
               ORDER BY u.id, a.id;", array('assignment'=>$args['assignid'], 'courseid'=>$args['courseid']));

            $assign_gradedata = array();

            foreach ($myassigndata as $myassign) {

                 $assign_grade = array();

                 $assign_grade['user_name'] = $myassign->student_name; 
                 $assign_grade['user_id'] = $myassign->student_id; 
                 $assign_grade['assign_name'] = $myassign->assignment;
                 //$assign_grade['status'] = $myassign->status;
                 $assign_grade['submitted_date'] = $myassign->submitted_date;
                 //$assign_grade['grade_status'] = $myassign->passorfail;
                 $assign_grade['final_grade'] = $myassign->final_grade;
                 
                 $assign_gradedata[] = $assign_grade;

            }

            $userassign =  $OUTPUT->render_from_template('block_facultydashboard/assignusers', array('assigndata'=>$assign_gradedata));
             return $userassign;

}

function block_facultydashboard_output_fragment_assign_subusers($args){
 global $DB,$CFG,$PAGE,$OUTPUT;



 $mysubdata = $DB->get_records_sql("SELECT distinct asub.id,
               CONCAT(u.firstname,' ', u.lastname) AS student_name,
               cm.id as module_id,
               u.id as userid,
               u.username AS User,
               u.idnumber AS student_id,
               c.shortname AS course,
               a.name AS assignment,
               DATE_FORMAT(FROM_UNIXTIME (asub.timemodified),'%e %b %Y - %H:%i') AS submitteddate,
               ag.grade as final_grade

               FROM {assign} a 
               JOIN {course} AS c ON a.course = c.id
               JOIN {context} AS ctx ON ctx.instanceid = c.id AND ctx.contextlevel = 50
               JOIN {role_assignments} AS ra ON ra.contextid = ctx.id AND ra.roleid = 5 
               JOIN {assign_submission} asub on a.id = asub.assignment AND asub.userid =  ra.userid
               JOIN {user} AS u ON asub.userid = u.id
               LEFT JOIN {assign_grades} as ag on asub.assignment = ag.assignment AND ag.userid = u.id
               JOIN {course_modules} as cm on cm.course = c.id and a.id = cm.instance
               JOIN {modules} as m on  m.id = cm.module and module = 1
               WHERE asub.status = 'submitted' and c.id = :courseid AND a.id  = :assignment
               ORDER BY asub.timemodified ASC, u.username, c.shortname;", array('assignment'=>$args['assignid'], 'courseid'=>$args['courseid']));

            $sub_gradedata = array();

            foreach ($mysubdata as $mysub) {

                 $marking_grade = $CFG->wwwroot.'/mod/assign/view.php?id='.$mysub->module_id.'&rownum=0&action=grader&userid='.$mysub->userid;

                 $assign_sub = array();

                 $assign_sub['student_name'] = $mysub->student_name; 
                 $assign_sub['student_id'] = $mysub->student_id; 
                 $assign_sub['assignment'] = $mysub->assignment;            
                 $assign_sub['submitteddate'] = $mysub->submitteddate;
                 $assign_sub['final_grade'] = $mysub->final_grade;
                 $assign_sub['marking_grade'] = $marking_grade;
                 
                 $sub_gradedata[] = $assign_sub;

            }

            $userassign =  $OUTPUT->render_from_template('block_facultydashboard/assignsub', array('subdata'=>$sub_gradedata));
             return $userassign;


}

function block_facultydashboard_output_fragment_quiz_enrol($args){
 global $DB,$CFG,$PAGE,$OUTPUT;



 $myquizdata = $DB->get_records_sql("SELECT DISTINCT
               u.username as student_name,
               u.idnumber as student_id,
               c.fullname as course_name,
               q.name as quiz_name,
               q.id as quizid,               
               CASE
               WHEN qa.timemodified IS NOT NULL then DATE_FORMAT(FROM_UNIXTIME (qa.timemodified),'%e %b %Y - %H:%i') ELSE 'not_submitted' END AS 'submitted_date',
               CASE
               WHEN qz.grade IS NOT NULL then qz.grade ELSE 'not_graded' END AS 'final_grade'
               from {course} as c
               JOIN {course_modules} as cm on cm.course = c.id
               JOIN {modules} as m on  m.id = cm.module
               JOIN {context} as ctx on ctx.instanceid = c.id 
               JOIN {role_assignments} as ra on ra.contextid = ctx.id
               JOIN {user} as u on u.id = ra.userid
               LEFT JOIN {quiz} as q on q.course = c.id
               LEFT JOIN {quiz_attempts} as qa on qa.userid = u.id and q.id = qa.quiz
               LEFT JOIN {quiz_grades} as qz on qz.userid = u.id and qz.quiz = q.id
                               
               where c.id = :courseid and q.id = :quizid and ctx.contextlevel = 50 and ra.roleid = 5 
               ORDER BY u.id, q.id;", array('quizid'=>$args['quizid'], 'courseid'=>$args['courseid']));

            $quiz_userdata = array();

            foreach ($myquizdata as $myquiz) {

                 $quiz_enrol = array();

                 $quiz_enrol['student_name'] = $myquiz->student_name; 
                 $quiz_enrol['student_id'] = $myquiz->student_id; 
                 $quiz_enrol['course_name'] = $myquiz->course_name;            
                 $quiz_enrol['quiz_name'] = $myquiz->quiz_name;
                 $quiz_enrol['submitted_date'] = $myquiz->submitted_date;
                 $quiz_enrol['final_grade'] = $myquiz->final_grade;
                 
                 $quiz_userdata[] = $quiz_enrol;

            }

            $quizusers =  $OUTPUT->render_from_template('block_facultydashboard/quizusers', array('quizdata'=>$quiz_userdata));
             return $quizusers;


}

function block_facultydashboard_output_fragment_quiz_subusers($args){
 global $DB,$CFG,$PAGE,$OUTPUT;



 $myquizsubs = $DB->get_records_sql("SELECT DISTINCT
               u.username as student_name,
               u.idnumber as student_id,
               c.fullname as course_name,
               q.name as quiz_name,
               qa.id as attemptid,
               q.id as quizid,               
               CASE
               WHEN qa.timemodified IS NOT NULL then DATE_FORMAT(FROM_UNIXTIME (qa.timemodified),'%e %b %Y - %H:%i') ELSE 'not_submitted' END AS 'submitted_date',
               CASE
               WHEN qz.grade IS NOT NULL then qz.grade ELSE 'not_graded' END AS 'final_grade'
               from {course} as c
               JOIN {course_modules} as cm on cm.course = c.id and cm.deletioninprogress = 0
               JOIN {modules} as m on  m.id = cm.module
               JOIN {context} as ctx on ctx.instanceid = c.id 
               JOIN {role_assignments} as ra on ra.contextid = ctx.id
               JOIN {user} as u on u.id = ra.userid
               LEFT JOIN {quiz} as q on q.course = c.id
               LEFT JOIN {quiz_attempts} as qa on qa.userid = u.id and q.id = qa.quiz
               LEFT JOIN {quiz_grades} as qz on qz.userid = u.id and qz.quiz = q.id
                               
               where qa.state = 'finished' and c.id = :courseid and q.id = :quizid and ctx.contextlevel = 50 and ra.roleid = 5 
               ORDER BY qa.timemodified ASC, u.id, q.id;", array('quizid'=>$args['quizid'], 'courseid'=>$args['courseid']));

            $quiz_subdata = array();

            foreach ($myquizsubs as $myquizsub) {

                 $marking_grade = $CFG->wwwroot.'/mod/quiz/review.php?attempt='.$myquizsub->attemptid;

                 $quiz_subs = array();

                 $quiz_subs['student_name'] = $myquizsub->student_name; 
                 $quiz_subs['student_id'] = $myquizsub->student_id; 
                 $quiz_subs['course_name'] = $myquizsub->course_name;            
                 $quiz_subs['quiz_name'] = $myquizsub->quiz_name;
                 $quiz_subs['submitted_date'] = $myquizsub->submitted_date;
                 $quiz_subs['final_grade'] = $myquizsub->final_grade;
                 $quiz_subs['marking_grade'] = $marking_grade;
                 
                 $quiz_subdata[] = $quiz_subs;

            }

            $quizsubs =  $OUTPUT->render_from_template('block_facultydashboard/quizsub', array('quizgrade'=>$quiz_subdata));
             return $quizsubs;


}

function block_facultydashboard_output_fragment_forum_enrol($args){
 global $DB,$CFG,$PAGE,$OUTPUT;



 $myforumdata = $DB->get_records_sql("SELECT u.username as student_name,
                u.idnumber as student_id,
                c.fullname as course_name,
                f.name as forum,
                CASE
                WHEN fp.modified IS NOT NULL then DATE_FORMAT(FROM_UNIXTIME (fp.modified),'%e %b %Y - %H:%i') ELSE 'not_submitted' END AS 'submission_date',
                CASE
                WHEN fg.grade IS NOT NULL then fg.grade ELSE 'not_graded' END AS 'final_grade'
                from {course} as c                                 
                join {context} as ctx on ctx.instanceid = c.id 
                join {role_assignments} as ra on ra.contextid = ctx.id
                join {user} as u on u.id = ra.userid
                join {forum} as f on f.course = c.id
                left join {forum_discussions} as fd on f.id = fd.forum
                left join {forum_posts} as fp on fp.discussion = fd.id and fp.userid  = u.id
                left join {forum_grades} as fg on ra.userid = fg.userid and fg.forum = f.id
               
                where  c.id = :courseid and f.id = :forumid and ctx.contextlevel = 50 and ra.roleid = 5 and f.type != 'news'
                 GROUP BY f.name, u.id;", array('forumid'=>$args['forumid'], 'courseid'=>$args['courseid']));

            $forum_userdata = array();

            foreach ($myforumdata as $myforum) {

                 $forum_enrol = array();

                 $forum_enrol['student_name'] = $myforum->student_name; 
                 $forum_enrol['student_id'] = $myforum->student_id; 
                 $forum_enrol['course_name'] = $myforum->course_name;            
                 $forum_enrol['forum'] = $myforum->forum;
                 $forum_enrol['submission_date'] = $myforum->submission_date;
                 $forum_enrol['final_grade'] = $myforum->final_grade;
                 
                 $forum_userdata[] = $forum_enrol;

            }

            $forumusers =  $OUTPUT->render_from_template('block_facultydashboard/forumusers', array('forumdata'=>$forum_userdata));
             return $forumusers;


}

function block_facultydashboard_output_fragment_forum_sub($args){
 global $DB,$CFG,$PAGE,$OUTPUT;

        $myforumsubs = $DB->get_records_sql("SELECT u.username as student_name,u.idnumber as student_id, u.id as userid, c.fullname as course_name,
            c.id as courseid, cm.id as instanceid, ctx.id as contextid, f.name asforum,
            f.id as forumid,
            CASE
            WHEN fp.modified IS NOT NULL then DATE_FORMAT(FROM_UNIXTIME (fp.modified),'%e %b %Y - %H:%i') ELSE 'not_submitted' END AS 'submission_date',
            (SELECT IF(grade IS NOT NULL, grade, 'Not Graded') FROM mdl_forum_grades WHERE forum = f.id AND userid = ra.userid ) as final_grade

            FROM mdl_user as u
            JOIN mdl_forum_discussion_subs as fds ON u.id = fds.userid
            JOIN mdl_forum_discussions as fd on fd.id = fds.discussion AND fd.forum = fds.forum
            join mdl_forum_posts as fp on fp.discussion = fd.id and fp.userid  = u.id
            JOIN mdl_forum as f on f.id = fd.forum
            JOIN mdl_course_modules as cm ON cm.course = f.course AND cm.instance = f.id AND cm.module = (SELECT id FROM mdl_modules WHERE name='forum')
            JOIN mdl_course as c ON c.id = cm.course
            join mdl_context as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50
            join mdl_role_assignments as ra on ra.contextid = ctx.id and ra.userid = fds.userid WHERE ra.roleid = 5 and f.type != 'news' AND f.id = :forumid AND f.course = :courseid", array('forumid'=>$args['forumid'], 'courseid'=>$args['courseid']));
 
            $forum_subdata = array();

            foreach ($myforumsubs as $myforumsub) {

                 $marking_grade = $CFG->wwwroot.'/mod/forum/view.php?id='.$myforumsub->instanceid;

                 $forum_sub = array();

                 $forum_sub['student_name'] = $myforumsub->student_name; 
                 $forum_sub['student_id'] = $myforumsub->student_id; 
                 $forum_sub['course_name'] = $myforumsub->course_name;            
                 $forum_sub['forum'] = $myforumsub->forum;
                 $forum_sub['submission_date'] = $myforumsub->submission_date;
                 $forum_sub['final_grade'] = $myforumsub->final_grade ? $myforumsub->final_grade : 'Not Graded';
                 $forum_sub['marking_grade'] = $marking_grade;
                 $forum_sub['cmid'] = $myforumsub->instanceid;
                 $forum_sub['courseid'] = $myforumsub->courseid;
                 $forum_sub['contextid'] = $myforumsub->contextid;
                 
                 $forum_subdata[] = $forum_sub;

            }

            $forumsubs =  $OUTPUT->render_from_template('block_facultydashboard/forumgradedata', array('forumgrade'=>$forum_subdata));
             return $forumsubs;

}

function get_courses_image($courses_record){
    global $CFG,$DB;
    $course = new core_course_list_element($courses_record);
    $imgurlflag = false;
    $imageurl = '';
    foreach ($course->get_course_overviewfiles() as $file) {
        if ($file->is_valid_image()) {
            $imagepath = '/' . $file->get_contextid() .
                    '/' . $file->get_component() .
                    '/' . $file->get_filearea() .
                    $file->get_filepath() .
                    $file->get_filename();
            $imageurl = file_encode_url($CFG->wwwroot . '/pluginfile.php', $imagepath,
                    false);
                // print_r($imageurl); exit;

        }
    }

    if(!$imageurl){
        $summary = $DB->get_record('course_sections',array('course' => $courses_record->id,'section' => 0));
        $context = $DB->get_record('context',array('instanceid' => $courses_record->id,'contextlevel' => 50));
       
        $html = $summary->summary;
        // print_r($html);exit;
        if($html){
            $doc = new DOMDocument();
            $doc->loadHTML($html);
            $xpath = new DOMXPath($doc);
            $imageurl = $xpath->evaluate("string(//img/@src)");
            // print_r($imageurl);
            if($imageurl){
                $urlumg = explode('@@PLUGINFILE@@/',$imageurl);
                // print_r($urlumg);
                $imageurl = $CFG->wwwroot."/pluginfile.php/$context->id/course/section/$summary->id/$urlumg[1]";
            } else {

                $catname = $DB->get_records_sql('select cc.name from {course_categories} as cc
                    JOIN {course} as c on cc.id = c.category
                    where cc.parent != 0 and c.id = :id', array('id' => $courses_record->id));
                if($catname) {

                     $category_name = reset($catname)->name;

                    if (str_contains($category_name, 'MBA') ) {
                        $imageurl = $imageurl = $CFG->wwwroot.'/blocks/facultydashboard/cards/mba.png';
                        $imgurlflag = true;
                    } elseif (str_contains($category_name, 'ICA')) {
                        $imageurl = $CFG->wwwroot . '/blocks/facultydashboard/cards/bits.jpg';
                        $imgurlflag = true;
                    } elseif (str_contains($category_name, 'MITS')) {
                        $imageurl = $CFG->wwwroot . '/blocks/facultydashboard/cards/mits.jpg';
                        $imgurlflag = true;
                    }
                }
            }
            
        } else {

            $catname = $DB->get_records_sql('select cc.name from {course_categories} as cc
                JOIN {course} as c on cc.id = c.category
                where cc.parent != 0 and c.id = :id', array('id' => $courses_record->id));
            if($catname) {

                 $category_name = reset($catname)->name;

                 if (str_contains($category_name, 'MBA') ) {
                    $imageurl = $imageurl = $CFG->wwwroot.'/blocks/facultydashboard/cards/mba.png';
                    $imgurlflag = true;
                } elseif (str_contains($category_name, 'ICA')) {
                    $imageurl = $CFG->wwwroot . '/blocks/facultydashboard/cards/bits.jpg';
                    $imgurlflag = true;
                } elseif (str_contains($category_name, 'MITS')) {
                    $imageurl = $CFG->wwwroot . '/blocks/facultydashboard/cards/mits.jpg';
                    $imgurlflag = true;
                }
            }
        }
    }

    return array('imageurl' => $imageurl,'imgurlflag' => $imgurlflag);
}




