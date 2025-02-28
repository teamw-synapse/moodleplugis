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

function group_member($userid, $courseid, $count = true) {

     global $DB, $COURSE, $USER, $CFG, $OUTPUT, $PAGE;

     $groupuser = $DB->get_record_sql(
            "SELECT g.name, c.groupmode 
            FROM mdl_groups g
            INNER JOIN mdl_groups_members gm ON gm.groupid = g.id
            INNER JOIN mdl_course c ON g.courseid = c.id
            WHERE gm.userid = :userid AND g.courseid = :courseid", 
            ['userid' => $userid, 'courseid' => $courseid]);

     $getcourse = $DB->get_record('course', ['id' => $courseid]);

	       if($count){

			if ($groupuser && $getcourse->groupmode == 1) {
	          
	           $studentcount = $DB->count_records_sql(
	               "SELECT COUNT(DISTINCT u.id) AS count FROM mdl_user u
	               INNER JOIN mdl_role_assignments ra ON ra.userid = u.id
	               INNER JOIN mdl_context ct ON ct.id = ra.contextid AND ct.contextlevel = 50
	               INNER JOIN mdl_course c ON c.id = ct.instanceid
	               INNER JOIN mdl_groups g ON g.courseid = c.id
	               INNER JOIN mdl_groups_members gm ON gm.groupid = g.id
	               WHERE ra.roleid = 5 AND c.id = :courseid AND gm.userid =
	               u.id AND g.id IN (SELECT g2.id FROM mdl_groups g2
	               INNER JOIN mdl_groups_members gm2 ON gm2.groupid = g2.id
	               WHERE gm2.userid = :userid AND g2.courseid = :courseid1)"
	               , ['courseid' => $courseid, 'courseid1' => $courseid, 'userid' => $userid]);
	      }

	          elseif (($groupuser || !$groupuser) && $getcourse->groupmode !=1) {

	           $studentcount = $DB->count_records_sql(
	               "SELECT COUNT(DISTINCT u.id) AS count 
	               FROM mdl_user u
	               INNER JOIN mdl_role_assignments ra ON ra.userid = u.id
	               INNER JOIN mdl_context ct ON ct.id = ra.contextid AND ct.contextlevel = 50
	               INNER JOIN mdl_course c ON c.id = ct.instanceid
	               WHERE ra.roleid = 5 AND c.id = :courseid", 
	               ['courseid' => $courseid]);
	       }

	       elseif (!$groupuser && $getcourse->groupmode == 1) {

	           $studentcount = 0;

	       }

	       return $studentcount;
		} 
		else {

			if ($groupuser && $getcourse->groupmode == 1) {

				$selectsql = "SELECT DISTINCT(u.id) as userid,cc.id as completionid, u.idnumber AS id_number, u.username AS user_name,CONCAT(u.firstname,' ',u.lastname) as studentname, u.email AS user_email, CASE WHEN cc.timecompleted IS NOT NULL THEN 'completed' ELSE 'not completed' END AS course_status ";

				$totalsql = "SELECT COUNT(DISTINCT(u.id)) ";

				$fromsql = " from mdl_user u
					INNER JOIN mdl_role_assignments ra ON ra.userid = u.id
					INNER JOIN mdl_context ct ON ct.id = ra.contextid AND ct.contextlevel = 50
					INNER JOIN mdl_course c ON c.id = ct.instanceid
					INNER JOIN mdl_groups g ON g.courseid = c.id
					INNER JOIN mdl_groups_members gm ON gm.groupid = g.id
	                    LEFT JOIN mdl_course_completions AS cc ON u.id = cc.userid AND c.id = cc.course
					WHERE ra.roleid = 5 AND c.id = :courseid AND gm.userid = 
					u.id AND g.id IN (SELECT g2.id FROM mdl_groups g2 INNER 
					JOIN mdl_groups_members gm2 ON gm2.groupid = g2.id 
					WHERE gm2.userid = :userid AND g2.courseid = :courseid1)";

				$countsql = $totalsql.$fromsql;
				$studentsql = $selectsql.$fromsql;
				$studentdata = array('countsql' => $countsql, 'studentsql' => $studentsql);
			}

			elseif (($groupuser || !$groupuser) && $getcourse->groupmode !=1) {
				$selectsql = "SELECT DISTINCT(u.id) as userid,cc.id as completionid, u.idnumber AS id_number, u.username AS user_name,CONCAT(u.firstname,' ',u.lastname) as studentname, u.email AS user_email, CASE WHEN cc.timecompleted IS NOT NULL THEN 'completed' ELSE 'not completed' END AS course_status ";
				$totalsql = " SELECT COUNT(DISTINCT(u.id))";
	           	$fromsql = " from mdl_user u
				 INNER JOIN mdl_role_assignments ra ON ra.userid = u.id
				 INNER JOIN mdl_context ct ON ct.id = ra.contextid AND ct.contextlevel = 50
				 INNER JOIN mdl_course c ON c.id = ct.instanceid
	                LEFT JOIN mdl_course_completions AS cc ON u.id = cc.userid AND c.id = cc.course
				 WHERE ra.roleid = 5 AND c.id = :courseid";

				$countsql = $totalsql.$fromsql;
				$studentsql = $selectsql.$fromsql;
				$studentdata = array('countsql' => $countsql, 'studentsql' => $studentsql);
	       }

	       elseif (!$groupuser && $getcourse->groupmode == 1) {

	           $studentdata = 0;

	       }

			return $studentdata;		
     }

}

function group_activity($userid, $courseid, $count = true, $assignid,$search = null) {

     global $DB, $COURSE, $USER, $CFG, $OUTPUT, $PAGE;

     $groupuser = $DB->get_record_sql("SELECT g.name, c.groupmode FROM 
     	  mdl_groups g INNER JOIN mdl_groups_members gm ON gm.groupid = g.id
            INNER JOIN mdl_course c ON g.courseid = c.id
            WHERE gm.userid = :userid AND g.courseid = :courseid", 
            ['userid' => $userid, 'courseid' => $courseid]);


     $getassign = $DB->get_record('course_modules', ['course' => $courseid, 'instance' => $assignid]);

	     if($count){

			if ($groupuser && $getassign->groupmode == 1) {

	           	$assigncount = $DB->get_record_sql("SELECT a.id as assignid, a.name as assign_name, 
					c.fullname as course_name, cm.id as module_id,
					COUNT(DISTINCT asub.id) as submission_count,
					COUNT(DISTINCT u.id) as user_count,
					g.name from mdl_course as c 
					join mdl_assign as a on a.course = c.id 
					join mdl_course_modules as cm on cm.course = c.id 
					join mdl_modules as m on  m.id = cm.module
					join mdl_context as ctx on ctx.instanceid = c.id 
					join mdl_role_assignments as ra on ra.contextid = ctx.id
					join mdl_user as u on ra.userid = u.id
					join mdl_groups g ON g.courseid = c.id
					join mdl_groups_members gm ON gm.groupid = g.id and gm.userid = u.id
					LEFT JOIN mdl_assign_submission as asub ON asub.assignment = a.id AND asub.userid =u.id AND asub.status = 'submitted'
					where c.id = :courseid and cm.instance = a.id and m.name = 'assign' and ctx.contextlevel = 50 
					and ra.roleid = 5  AND a.id =:assignid AND g.id IN (SELECT g2.id FROM mdl_groups g2
					INNER JOIN mdl_groups_members gm2 ON gm2.groupid = g2.id
					WHERE gm2.userid = :userid AND g2.courseid = :courseid1)
					GROUP BY a.id, c.id", ['courseid' => $courseid, 'courseid1' => $courseid, 'userid' => $userid,'assignid' => $assignid]);
	      	}
	      	elseif (!$groupuser && $getassign->groupmode == 1) {

		      	 $assigncount = new stdClass();
		           $assigncount->submission_count = 0;
		           $assigncount->user_count = 0;

	       	}
	       	return $assigncount;
		}else {

			    $selectsql = "SELECT distinct asub.userid,
					CONCAT(u.firstname,' ', u.lastname) AS student_name,
					cm.id as module_id, u.id as userid, u.username AS User,
					u.idnumber AS student_id, c.shortname AS course,
					a.name AS assignment, 
					CASE
					WHEN asub.timemodified IS NOT NULL then DATE_FORMAT(FROM_UNIXTIME (asub.timemodified),'%e %b %Y - %H:%i') ELSE 'N/A' END AS submitteddate,
					CASE 
					WHEN ag.grade = -1 or ag.grade IS NULL THEN 'Not Graded' ELSE ag.grade END AS final_grade ";

				$totalsql = " SELECT COUNT(DISTINCT(asub.userid))";

				$totalassignsql = " SELECT COUNT(DISTINCT(u.id))";

				$fromsql = " FROM mdl_assign a 
					JOIN mdl_course_modules as cm on cm.course = a.course and a.id = cm.instance AND cm.module = 1
					JOIN mdl_course AS c ON cm.course = c.id
					JOIN mdl_context AS ctx ON ctx.instanceid = c.id AND ctx.contextlevel = 50
					JOIN mdl_role_assignments AS ra ON ra.contextid = ctx.id AND ra.roleid = 5 
					JOIN mdl_user AS u ON ra.userid = u.id ";

				$group1 = " JOIN mdl_groups g ON g.courseid = cm.course
					JOIN mdl_groups_members gm ON gm.groupid = g.id and gm.userid = u.id ";

				$group2 = " LEFT JOIN mdl_assign_submission asub on asub.userid =  gm.userid AND asub.assignment = a.id
					LEFT JOIN mdl_assign_grades as ag on asub.assignment = ag.assignment AND ag.userid = gm.userid ";

				$groupquiz = " LEFT JOIN mdl_assign_submission asub on asub.userid =  u.id AND asub.assignment = a.id
					LEFT JOIN mdl_assign_grades as ag on asub.assignment = ag.assignment AND ag.userid = u.id ";

				if($search){
				$group3 =	" WHERE c.id = :courseid AND a.id  = :assignid and (u.firstname like '%$search%' or u.lastname like '%$search%' or u.email like '%$search%' or u.username like '%$search%' or u.idnumber like '%$search%')";
				}else{
					$group3 =	" WHERE c.id = :courseid AND a.id  = :assignid ";
				}

				$group4 = " and asub.status = 'submitted' ";

				$group5 = " and g.id IN (SELECT g2.id FROM mdl_groups g2
					INNER JOIN mdl_groups_members gm2 ON gm2.groupid = g2.id
					WHERE gm2.userid = :userid AND g2.courseid = :courseid1) ";

				$group6 = " ORDER BY asub.timemodified ASC, u.username, c.shortname";
			if ($groupuser) {

				if ($getassign->groupmode == 1) {

					$countsql = $totalsql.$fromsql.$group1.$group2.$group3.$group4.$group5.$group6;
				     $assignsql = $selectsql.$fromsql.$group1.$group2.$group3.$group4.$group5.$group6;

				     $countusersql = $totalassignsql.$fromsql.$group1.$group2.$group3.$group5;
				     $assignusersql = $selectsql.$fromsql.$group1.$group2.$group3.$group5;
					
				}

				elseif($getassign->groupmode !== 1){

					$countsql = $totalsql.$fromsql.$groupquiz.$group3.$group4.$group6;
				     $assignsql = $selectsql.$fromsql.$groupquiz.$group3.$group4.$group6;

				     $countusersql = $totalassignsql.$fromsql.$groupquiz.$group3.$group6;
				     $assignusersql = $selectsql.$fromsql.$groupquiz.$group3.$group6;
				}
				
				$assigndata = array('countsql' => $countsql, 'assignsql' => $assignsql, 'countusersql' => $countusersql, 'assignusersql' => $assignusersql);
			}

			elseif (!$groupuser && $getassign->groupmode !== 1) {

		          $countsql = $totalsql.$fromsql.$groupquiz.$group3.$group4.$group6;
			     $assignsql = $selectsql.$fromsql.$groupquiz.$group3.$group4.$group6;

			     $countusersql = $totalassignsql.$fromsql.$groupquiz.$group3.$group6;
			     $assignusersql = $selectsql.$fromsql.$groupquiz.$group3.$group6;

			     $assigndata = array('countsql' => $countsql, 'assignsql' => $assignsql, 'countusersql' => $countusersql, 'assignusersql' => $assignusersql);
		     }

		     elseif (!$groupuser && $getassign->groupmode == 1) {

		          $assigndata = 0;

		     }


			return $assigndata;

     } 
		

}

function groupquiz_activity($userid, $courseid, $count = true, $quizid,$search = null) {

     global $DB, $COURSE, $USER, $CFG, $OUTPUT, $PAGE;

     $groupuser = $DB->get_record_sql("SELECT g.name, c.groupmode FROM 
     	  mdl_groups g INNER JOIN mdl_groups_members gm ON gm.groupid = g.id
            INNER JOIN mdl_course c ON g.courseid = c.id
            WHERE gm.userid = :userid AND g.courseid = :courseid", 
            ['userid' => $userid, 'courseid' => $courseid]);


     $getquiz = $DB->get_record('course_modules', ['course' => $courseid, 'instance' => $quizid]);

	       if($count){

			if ($groupuser && $getquiz->groupmode == 1) {

	           $quizcount = $DB->get_record_sql("SELECT
			    q.id AS quizid, q.name AS quiz_name, cm.id AS module_id,
			    c.fullname AS course_name,
			    (SELECT COUNT(DISTINCT qa.userid)
			        FROM mdl_quiz_attempts qa
			        JOIN mdl_user u ON qa.userid = u.id
			        JOIN mdl_role_assignments ra ON u.id = ra.userid
			        JOIN mdl_groups_members gm ON gm.userid = u.id
			        JOIN mdl_groups g ON g.id = gm.groupid
			        WHERE qa.quiz = q.id
			          AND qa.state = 'finished'
			          AND ra.roleid = 5
			          AND g.id IN (
			              SELECT g2.id
			              FROM mdl_groups g2
			              JOIN mdl_groups_members gm2 ON gm2.groupid = g2.id
			              WHERE gm2.userid = :userid
			                AND g2.courseid = c.id
			          )
			    ) AS submission_count,
			    (SELECT COUNT(DISTINCT ra.userid)
			        FROM mdl_role_assignments ra
			        JOIN mdl_user u ON ra.userid = u.id
			        JOIN mdl_groups_members gm ON gm.userid = u.id
			        JOIN mdl_groups g ON g.id = gm.groupid
			        WHERE ra.contextid = ctx.id
			          AND ra.roleid = 5
			          AND g.id IN (
			              SELECT g2.id
			              FROM mdl_groups g2
			              JOIN mdl_groups_members gm2 ON gm2.groupid = g2.id
			              WHERE gm2.userid = :userid1 AND g2.courseid = c.id)
			    ) AS user_count
			FROM mdl_course c
			JOIN mdl_quiz q ON q.course = c.id
			JOIN mdl_course_modules cm ON cm.course = c.id
			  AND cm.instance = q.id
			  AND cm.module = (SELECT id FROM mdl_modules WHERE name = 'quiz')
			JOIN mdl_context ctx ON ctx.instanceid = cm.course
			WHERE c.id = :courseid AND q.id = :quizid AND ctx.contextlevel = 50
			GROUP BY q.id, c.id", ['courseid' => $courseid, 'userid' => $userid,'userid1' => $userid,'quizid' => $quizid]);
	      }

	      elseif (!$groupuser && $getquiz->groupmode == 1) {

	      	 $quizcount = new stdClass();
	           $quizcount->submission_count = 0;
	           $quizcount->user_count = 0;

	       }

	       return $quizcount;
		}

		else {

				$selectsql = "SELECT DISTINCT u.username as student_name,u.id as userid, u.idnumber as student_id, c.fullname as course_name,
					q.name as quiz_name, qa.id as attemptid, q.id as quizid,
					CASE
					WHEN qa.timemodified IS NOT NULL then DATE_FORMAT(FROM_UNIXTIME (qa.timemodified),'%e %b %Y - %H:%i') ELSE 'Not Submitted' END AS 'submitted_date',
					CASE
					WHEN qz.grade IS NOT NULL then qz.grade ELSE 'Not Graded' END AS 'final_grade'";

				$totalsql = " SELECT count(DISTINCT qa.userid) ";
				$totalquizsql = " SELECT count(DISTINCT u.id) ";

				$fromsql = " from mdl_course as c
					JOIN mdl_course_modules as cm on cm.course = c.id and cm.deletioninprogress = 0
					JOIN mdl_modules as m on  m.id = cm.module
					JOIN mdl_context as ctx on ctx.instanceid = c.id 
					JOIN mdl_role_assignments as ra on ra.contextid = ctx.id
					JOIN mdl_user as u on u.id = ra.userid
					LEFT JOIN mdl_quiz as q on q.course = c.id
					LEFT JOIN mdl_quiz_attempts as qa on qa.userid = u.id and q.id = qa.quiz
					LEFT JOIN mdl_quiz_grades as qz on qz.userid = u.id and qz.quiz = q.id";

				$group1 = " JOIN mdl_groups g ON g.courseid = c.id
					JOIN mdl_groups_members gm ON gm.groupid = g.id and gm.userid = u.id";
				$where = " where";

				$group2 =	" qa.state = 'finished' and"; 

				if($search){

					$group3 = "  (u.firstname like '%$search%' or u.lastname like '%$search%' or u.email like '%$search%' or u.username like '%$search%' or u.idnumber like '%$search%') and c.id = :courseid and q.id = :quizid and ctx.contextlevel = 50 and ra.roleid = 5";
				}else{

					$group3 = " c.id = :courseid and q.id = :quizid and ctx.contextlevel = 50 and ra.roleid = 5";
				}
				

				$group4 = " and g.id IN (SELECT g2.id FROM mdl_groups g2
						INNER JOIN mdl_groups_members gm2 ON gm2.groupid = g2.id
						WHERE gm2.userid = :userid AND g2.courseid = :courseid1)";

				$group5 = " ORDER BY qa.timemodified ASC, u.id, q.id";

			if ($groupuser) {

				if ($getquiz->groupmode == 1) {

					$countsql = $totalsql.$fromsql.$group1.$where.$group2.$group3.$group4.$group5;
				     $quizsql = $selectsql.$fromsql.$group1.$where.$group2.$group3.$group4.$group5;
				     $countquizusersql = $totalquizsql.$fromsql.$group1.$where.$group3.$group4.$group5;
				     $quizusersql = $selectsql.$fromsql.$group1.$where.$group3.$group4.$group5;
					
				}

				elseif($getquiz->groupmode !== 1){

					$countsql = $totalsql.$fromsql.$where.$group2.$group3.$group5;
				     $quizsql = $selectsql.$fromsql.$where.$group2.$group3.$group5;
				     $countquizusersql = $totalquizsql.$fromsql.$where.$group3.$group5;
				     $quizusersql = $selectsql.$fromsql.$where.$group3.$group5;
				}
				
				$quizdata = array('countsql' => $countsql, 'quizsql' => $quizsql, 'countquizusersql' => $countquizusersql, 'quizusersql' => $quizusersql);
			}

			elseif (!$groupuser && $getquiz->groupmode !== 1) {

		         $countsql = $totalsql.$fromsql.$where.$group2.$group3.$group5;
			    $quizsql = $selectsql.$fromsql.$where.$group2.$group3.$group5;
			    $countquizusersql = $totalquizsql.$fromsql.$where.$group3.$group5;
			    $quizusersql = $selectsql.$fromsql.$where.$group3.$group5;

			    $quizdata = array('countsql' => $countsql, 'quizsql' => $quizsql, 'countquizusersql' => $countquizusersql, 'quizusersql' => $quizusersql);

		     }

		     elseif (!$groupuser && $getquiz->groupmode == 1) {

		          $quizdata = 0;

		     }


			return $quizdata;

     } 
		

}

function groupforumdisc_activity($userid, $courseid, $forumid, $groupmode) {

	// print_r($groupmode); exit;

	global $DB, $COURSE, $USER, $CFG, $OUTPUT, $PAGE;

			$groupuser = $DB->get_record_sql("SELECT g.name, c.groupmode FROM 
				  mdl_groups g INNER JOIN mdl_groups_members gm ON gm.groupid = g.id
			       INNER JOIN mdl_course c ON g.courseid = c.id
			       WHERE gm.userid = :userid AND g.courseid = :courseid", 
			       ['userid' => $userid, 'courseid' => $courseid]);

			$selectsql = "SELECT  f.id as forumid, "; 

			$groupsql1 = " (select count(ra.id) from mdl_context as ctx
				join mdl_role_assignments as ra on ra.contextid = ctx.id
				JOIN mdl_user AS u ON ra.userid = u.id
				join mdl_role as r on r.id = ra.roleid
				JOIN mdl_groups g ON g.courseid = cm.course
				JOIN mdl_groups_members gm ON gm.groupid = g.id and gm.userid = u.id
				where r.shortname = 'student' and ctx.instanceid = c.id and ctx.contextlevel = 50 and g.id IN (SELECT g2.id FROM mdl_groups g2
				INNER JOIN mdl_groups_members gm2 ON gm2.groupid = g2.id
				WHERE gm2.userid = :userid AND g2.courseid = :courseid1)) as user_count,";

			$groupsql2 = " (select count(distinct(fd.id)) from mdl_context as ctx
				JOIN mdl_role_assignments as ra on ra.contextid = ctx.id
				JOIN mdl_role as r on r.id = ra.roleid
				JOIN mdl_user AS u ON ra.userid = u.id
				join mdl_forum_discussions as fd on fd.forum = f.id
				JOIN mdl_groups g ON g.courseid = cm.course AND fd.groupid = g.id
				JOIN mdl_groups_members gm ON gm.groupid = g.id and gm.userid = u.id
				where r.shortname = 'student' and ctx.instanceid = c.id and ctx.contextlevel = 50 and g.id IN (SELECT g2.id FROM mdl_groups g2
				INNER JOIN mdl_groups_members gm2 ON gm2.groupid = g2.id
				WHERE gm2.userid = :userid2 AND g2.courseid = :courseid2)) as discussion_count";

			$groupsql3 = " (select count(ra.id) from mdl_context as ctx
                  join mdl_role_assignments as ra on ra.contextid = ctx.id
                  join mdl_role as r on r.id = ra.roleid
                  where r.shortname = 'student' and ctx.instanceid = c.id and ctx.contextlevel = 50) as user_count,";

			$groupsql4 = " (select count(distinct(fd.id)) from mdl_context as ctx
                  join mdl_role_assignments as ra on ra.contextid = ctx.id
                  join mdl_role as r on r.id = ra.roleid
                  join mdl_forum_discussions as fd on fd.forum = f.id
                  where r.shortname = 'student' and ctx.instanceid = c.id and ctx.contextlevel = 50) as discussion_count";

               $fromsql = " from mdl_course as c
				join mdl_forum as f on f.course = c.id
				join mdl_course_modules as cm on cm.course = c.id 
				join mdl_modules as m on m.id = cm.module and m.name = 'forum'
				where f.type != 'news' and c.id = :courseid and cm.instance = f.id AND f.id =:forumid";
			
			$groupby = " GROUP BY f.id, c.id";
			
	     if ($groupuser) {

	     	if ($groupmode == 1) {

			     $forumdiscsql = $selectsql.$groupsql1.$groupsql2.$fromsql.$groupby;
	     	}

	     	elseif ($groupmode !== 1) {

			     $forumdiscsql = $selectsql.$groupsql3.$groupsql4.$fromsql.$groupby;
	     	}

	     	$discdata = array('forumdiscsql' => $forumdiscsql);

	     }

	     elseif (!$groupuser && $groupmode !== 1) {

			$forumdiscsql = $selectsql.$groupsql3.$groupsql4.$fromsql.$groupby;

			$discdata = array('forumdiscsql' => $forumdiscsql);
	     }

	     elseif (!$groupuser && $groupmode == 1) {

	     	$discdata = 0;

	     }

	     return $discdata;
	
	
}

function groupdiscsub_activity($userid, $courseid, $forumid, $search = null) {

	global $DB, $COURSE, $USER, $CFG, $OUTPUT, $PAGE;

		$groupuser = $DB->get_record_sql("SELECT g.name, c.groupmode FROM 
			  mdl_groups g INNER JOIN mdl_groups_members gm ON gm.groupid = g.id
		       INNER JOIN mdl_course c ON g.courseid = c.id
		       WHERE gm.userid = :userid AND g.courseid = :courseid", 
		       ['userid' => $userid, 'courseid' => $courseid]);

		$getforum = $DB->get_record('course_modules', ['course' => $courseid, 'instance' => $forumid]);

		$totalsql = "SELECT count(DISTINCT fd.id)";

		$selectsql = "SELECT fd.id, c.fullname as course_name, c.id as courseid,";

	     $selectsql1 = " (select count(ra.id) from mdl_context as ctx 
			JOIN mdl_role_assignments as ra on ra.contextid = ctx.id 
			JOIN mdl_role as r on r.id = ra.roleid
			JOIN mdl_groups g ON g.courseid = cm.course
			JOIN mdl_groups_members gm ON gm.groupid = g.id and gm.userid = ra.userid
			where r.shortname = 'student' and ctx.instanceid = c.id and ctx.contextlevel = 50 and g.id IN (SELECT g2.id FROM mdl_groups g2
			INNER JOIN mdl_groups_members gm2 ON gm2.groupid = g2.id
			WHERE gm2.userid = :userid AND g2.courseid = :courseid)) as user_count,";

		$selectsql2 = " (select count(ra.id) from mdl_context as ctx join mdl_role_assignments as ra on ra.contextid = ctx.id join mdl_role as r on r.id = ra.roleid
                 where r.shortname = 'student' and ctx.instanceid = c.id and ctx.contextlevel = 50) as user_count,";	

		$selectsql3 = " f.name as forum, f.id as forumid, fd.id as discussion_id, fd.name as forum_discussion,";

		$selectsql4 = " (SELECT COUNT(DISTINCT fpp.userid) FROM mdl_forum_posts AS fpp 
                 JOIN mdl_forum_discussions AS fd_sub ON fpp.discussion = fd_sub.id 
                 JOIN mdl_context AS ctx_sub ON ctx_sub.instanceid = c.id AND ctx_sub.contextlevel = 50 
                 JOIN mdl_role_assignments AS ra_sub ON ra_sub.contextid = ctx_sub.id 
                 JOIN mdl_role AS r_sub ON ra_sub.roleid = r_sub.id 
                 WHERE r_sub.shortname = 'student' AND fd_sub.id = fd.id AND fpp.parent != 0 AND fpp.userid != :userid1) AS submission_count";

		$selectsql5 = " (SELECT COUNT(DISTINCT fpp.userid) FROM mdl_forum_posts AS fpp 
			JOIN mdl_forum_discussions AS fd_sub ON fpp.discussion = fd_sub.id 
			JOIN mdl_context AS ctx_sub ON ctx_sub.instanceid = c.id AND ctx_sub.contextlevel = 50 
			JOIN mdl_role_assignments AS ra_sub ON ra_sub.contextid = ctx_sub.id 
			JOIN mdl_role AS r_sub ON ra_sub.roleid = r_sub.id
			JOIN mdl_groups g ON g.courseid = cm.course
			JOIN mdl_groups_members gm ON gm.groupid = g.id and gm.userid = ra.userid 
			WHERE r_sub.shortname = 'student' AND fd_sub.id = fd.id AND fpp.parent != 0 AND fpp.userid != :userid1 and g.id IN (SELECT g2.id FROM mdl_groups g2
			INNER JOIN mdl_groups_members gm2 ON gm2.groupid = g2.id
			WHERE gm2.userid = :userid2 AND g2.courseid = :courseid1)) AS submission_count";

		$fromsql = " FROM mdl_course as c 
			JOIN mdl_course_modules as cm ON c.id = cm.course and cm.module = (SELECT id FROM mdl_modules WHERE name='forum')
			JOIN mdl_forum as f on f.course = c.id
			JOIN mdl_forum_discussions as fd on fd.forum = f.id and fd.course = c.id
			JOIN mdl_forum_posts as fp on fp.discussion = fd.id
			JOIN mdl_context as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50
			JOIN mdl_role_assignments as ra on ra.contextid = ctx.id";

		$fromsql1 = " JOIN mdl_groups g ON g.courseid = cm.course AND 
			fd.groupid = g.id
			JOIN mdl_groups_members gm ON gm.groupid = g.id and gm.userid = ra.userid";

		if($search){
			$wheresql = " WHERE  fd.name like '%$search%' and f.type != 'news' AND f.id = :forumid and g.id IN (SELECT g2.id FROM mdl_groups g2 INNER JOIN mdl_groups_members gm2 ON gm2.groupid = g2.id
			WHERE gm2.userid = :userid3 AND g2.courseid = :courseid2)";

			$wheresql1 = " WHERE fd.name like '%$search%' and f.type != 'news' AND f.id = :forumid AND c.id = :courseid";

		}else{

			$wheresql = " WHERE  f.type != 'news' AND f.id = :forumid and g.id IN (SELECT g2.id FROM mdl_groups g2 INNER JOIN mdl_groups_members gm2 ON gm2.groupid = g2.id
			WHERE gm2.userid = :userid3 AND g2.courseid = :courseid2)";

			$wheresql1 = " WHERE f.type != 'news' AND f.id = :forumid AND c.id = :courseid";

		}
		
		
		if($search){

			$wheresql2 = " and fd.name like '%$search%' GROUP by fd.id";
		}
		else{

		    $wheresql2 = " GROUP by fd.id";
		}

		if($groupuser) {

			if($getforum->groupmode == 1) {

				$discsub = $selectsql.$selectsql1.$selectsql3.$selectsql5.$fromsql.$fromsql1.$wheresql.$wheresql2;
				$discsubcount = $totalsql.$fromsql.$fromsql1.$wheresql;

			}

			elseif ($getforum->groupmode !== 1) {

				$discsub = $selectsql.$selectsql2.$selectsql3.$selectsql4.$fromsql.$wheresql1.$wheresql2 ;
				$discsubcount = $totalsql.$fromsql.$wheresql1;

			}

			$discsubdata = array('discsubcount' => $discsubcount, 'discsub' => $discsub);

		}

		elseif (!$groupuser && $getforum->groupmode !== 1) {

			$discsub = $selectsql.$selectsql2.$selectsql3.$selectsql4.$fromsql.$wheresql1.$wheresql2 ;
			$discsubcount = $totalsql.$fromsql.$wheresql1;

			$discsubdata = array('discsubcount' => $discsubcount, 'discsub' => $discsub);
	     }

	     elseif (!$groupuser && $getforum->groupmode == 1) {

	     	$discsubdata = 0;

	     }

	     return $discsubdata;
	

}

function groupforumusers_activity($userid, $courseid, $forumid, $search = null) {

	global $DB, $COURSE, $USER, $CFG, $OUTPUT, $PAGE;

		$groupuser = $DB->get_record_sql("SELECT g.name, c.groupmode FROM 
			  mdl_groups g INNER JOIN mdl_groups_members gm ON gm.groupid = g.id
		       INNER JOIN mdl_course c ON g.courseid = c.id
		       WHERE gm.userid = :userid AND g.courseid = :courseid", 
		       ['userid' => $userid, 'courseid' => $courseid]);

		$getforum = $DB->get_record('course_modules', ['course' => $courseid, 'instance' => $forumid]);

		$totalsql = "SELECT COUNT(DISTINCT(u.id))";

		$selectsql = "SELECT u.id as userid,u.username as student_name,
			u.idnumber as student_id,
			c.fullname as course_name,
			f.name as forum,
			CASE
			WHEN fp.modified IS NOT NULL then DATE_FORMAT(FROM_UNIXTIME (fp.modified),'%e %b %Y - %H:%i') ELSE 'NA' END AS 'submission_date',
			CASE
			WHEN fg.grade IS NOT NULL then fg.grade ELSE 'NA' END AS 'final_grade'";

		$fromsql = " from mdl_course as c 
		     join mdl_context as ctx on ctx.instanceid = c.id 
			join mdl_role_assignments as ra on ra.contextid = ctx.id
			join mdl_user as u on u.id = ra.userid
			join mdl_forum as f on f.course = c.id
			join mdl_course_modules as cm on cm.course = c.id
			join mdl_modules as m on m.id = cm.module and m.name = 'forum'
			left join mdl_forum_discussions as fd on f.id = fd.forum
			left join mdl_forum_posts as fp on fp.discussion = fd.id and fp.userid  = u.id
			left join mdl_forum_grades as fg on ra.userid = fg.userid and fg.forum = f.id";

		$fromsql1 = " JOIN mdl_groups g ON g.courseid = cm.course
			JOIN mdl_groups_members gm ON gm.groupid = g.id and gm.userid = u.id";

		if($search){
			
			$wheresql = " where  c.id = :courseid and f.id = :forumid and ctx.contextlevel = 50 and ra.roleid = 5 and f.type != 'news' and (u.firstname like '%$search%' or u.lastname like '%$search%' or u.email like '%$search%' or u.username like '%$search%' or u.idnumber like '%$search%')";

		}	
		else{

			$wheresql = " where  c.id = :courseid and f.id = :forumid and ctx.contextlevel = 50 and ra.roleid = 5 and f.type != 'news'";
		}
		

		$wheresql1 = " and g.id IN (SELECT g2.id FROM mdl_groups g2
			INNER JOIN mdl_groups_members gm2 ON gm2.groupid = g2.id
			WHERE gm2.userid = :userid AND g2.courseid = :courseid1)";

		$groupsql = " GROUP BY f.name, u.id";

		if($groupuser){

			if ($getforum->groupmode == 1){

			  $forumusersql = $selectsql.$fromsql.$fromsql1.$wheresql.$wheresql1.$groupsql;
			  $forumusersqlcount = $totalsql.$fromsql.$fromsql1.$wheresql.$wheresql1;
			}

			elseif ($getforum->groupmode !== 1){

			  $forumusersql = $selectsql.$fromsql.$wheresql.$groupsql;
			  $forumusersqlcount = $totalsql.$fromsql.$wheresql;

			}

			$forumuserdata = array('forumusersqlcount' => $forumusersqlcount, 'forumusersql' => $forumusersql);
			// print_r($forumuserdata); exit;

		}

		elseif (!$groupuser && $getforum->groupmode !== 1){

			$forumusersql = $selectsql.$fromsql.$wheresql.$groupsql;
			$forumusersqlcount = $totalsql.$fromsql.$wheresql;

			$forumuserdata = array('forumusersqlcount' => $forumusersqlcount, 'forumusersql' => $forumusersql);

		}

		return $forumuserdata;
		// print_r($forumuserdata); exit;


}

