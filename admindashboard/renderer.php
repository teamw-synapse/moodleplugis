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
 * local usermanagement rendrer
 *
 * @package    local_usermanagement
 * @copyright  2023 Kovida <kovida.in>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
// use core_component;
class block_admindashboard_renderer extends plugin_renderer_base {

	// public function admindashboard_userlist($stable,$selected_status,$selected_roles,$search,$type){
    //     global $USER,$DB,$CFG;
    //     $systemcontext = context_system::instance(); 
    //     $cparams = array();
    //   	if($selected_status || $selected_roles){
    //         $data = array(); 
    //         if(count($type) > 0){
    //             unset($type[0]);
    //             $tyepids = implode(',',$type);
    //         }
            
            
    //         // $courseids = $DB->get_records_sql("");          
    //         $selectsql = "SELECT DISTINCT u.id,u.idnumber,u.firstname,u.lastname,u.email,u.suspended,r.shortname as role
    //          FROM {user} as u  ";
    //         $countsql =  "SELECT count(DISTINCT u.id) FROM {user} as u  ";
    //         if($selected_roles){

    //             $selectsql .= "JOIN {role_assignments} ra ON ra.userid = u.id
    //                            join {context} as ctx on ctx.id = ra.contextid and ctx.instanceid IN (SELECT c.id FROM {course_categories} as cc join {course_categories} as cc1 on cc1.parent = cc.id join {course} as c on c.category = cc1.id where cc.parent in ($tyepids)) and ctx.contextlevel = 50
    //             			   JOIN {role} as r ON r.id = ra.roleid AND r.id IN ($selected_roles) ";
    //             $countsql .= "JOIN {role_assignments} ra ON ra.userid = u.id 
    //                           join {context} as ctx on ctx.id = ra.contextid and ctx.instanceid IN (SELECT c.id FROM {course_categories} as cc join {course_categories} as cc1 on cc1.parent = cc.id join {course} as c on c.category = cc1.id where cc.parent in ($tyepids)) and ctx.contextlevel = 50
    //             			   JOIN {role} as r ON r.id = ra.roleid AND r.id IN ($selected_roles) ";
    //         }
    //         $selectsql .= " WHERE u.deleted = 0";
    //         $countsql .= " WHERE u.deleted = 0";
    //         if($selected_status == 0){
    //           	$selectsql .= " AND u.suspended = 0";
    //             $countsql .= " AND u.suspended = 0";
    //         }else if($selected_status == 1){
    //            $selectsql .= " AND u.suspended IN ($selected_status)";
    //            $countsql .= " AND u.suspended IN ($selected_status)";
    //         }
    //          if($search){
    //            $selectsql .= " AND (u.firstname LIKE '%$search%' OR u.lastname LIKE '%$search%' OR u.email LIKE '%$search%' OR u.idnumber LIKE '%$search%')";
    //            $countsql .= " AND (u.firstname LIKE '%$search%' OR u.lastname LIKE '%$search%' OR u.email LIKE '%$search%' OR u.idnumber LIKE '%$search%')";
    //         }
    //         $active = " AND u.suspended = 0";
    //         $inactive = " AND u.suspended = 1";
    // 	}      
    //     $totalusers = $DB->count_records_sql($countsql,$cparams);
    //     $totalactiveusers = $DB->count_records_sql($countsql.$active,$cparams);
        	
        
    //     $totalinactiveusers = $DB->count_records_sql($countsql.$inactive,$cparams);
    //     $ordersql = " ORDER BY u.id DESC";
    //     $users = $DB->get_records_sql($selectsql.$ordersql,$cparams,$stable->start,$stable->length);     
      
    //     $data = array();
    //     foreach ($users as $user){
    //         $url = $CFG->wwwroot."/user/editadvanced.php?id=".$user->id;
    //         $record      = new stdClass();
    //         $record->id  = $user->id;
    //         $record->idnumber = $user->idnumber; 
    //         $record->url = $url; 
    //         $record->firstname = $user->firstname;
    //         $record->lastname = $user->lastname;                    
    //         $record->email = $user->email; 
    //         $record->role = $user->role;
    //         $record->suspendedstatus = $user->suspended != 0 ? 'fa fa-eye-slash mr-2' :'fas fa-eye mr-2';
    //         $record->suspendedmethod = $user->suspended != 0 ? 'unsuspend' :'suspend';
    //         $data[] = $record;
    //     }    

    //     return array('totalcount' => $totalusers,'data' => $data, 'totalactiveusers' => $totalactiveusers, 'totalinactiveusers' => $totalinactiveusers);
    // }

    public function admindashboard_userlist($stable,$selected_status,$selected_roles,$search,$type){
        global $USER,$DB,$CFG;
        $systemcontext = context_system::instance(); 
        $cparams = array();
        if($selected_status || $selected_roles){
            $data = array(); 
            if(count($type) > 0){
                unset($type[0]);
                $tyepids = implode(',',$type);
            }
            
            
            // $courseids = $DB->get_records_sql("");          
            // $selectsql = "SELECT DISTINCT u.id,u.idnumber,u.firstname,u.lastname,u.email,u.suspended,r.shortname as role
            //  FROM {user} as u  ";
             $selectsql = "SELECT DISTINCT ra.userid as id,u.idnumber,u.firstname,u.lastname,u.email,u.suspended,r.shortname as role,r.name as rolename ";
            // $countsql =  "SELECT count(DISTINCT u.id) FROM {user} as u  ";
             $countsql =  "SELECT count(DISTINCT ra.userid)  ";
            if($selected_roles){


                // $selectsql .= "JOIN {role_assignments} ra ON ra.userid = u.id
                //                join {context} as ctx on ctx.id = ra.contextid and ctx.instanceid IN (SELECT c.id FROM {course_categories} as cc join {course_categories} as cc1 on cc1.parent = cc.id join {course} as c on c.category = cc1.id where cc.parent in ($tyepids)) and ctx.contextlevel = 50
                //             JOIN {role} as r ON r.id = ra.roleid AND r.id IN ($selected_roles) ";

                $selectsql .= " FROM {course_categories} as cc join {course_categories} as cc1 on cc1.parent = cc.id join {course} as c ON cc1.id = c.category JOIN {context} as ctx ON ctx.instanceid = c.id AND ctx.contextlevel = 50 JOIN {role_assignments} as ra ON ra.contextid = ctx.id
                                                                join {user} as u on u.id = ra.userid 
                                                                join {role} as r on r.id = ra.roleid 
                                                                where  cc.parent in ($tyepids) AND r.id IN ($selected_roles) ";

                    $countsql .= " FROM {course_categories} as cc join {course_categories} as cc1 on cc1.parent = cc.id join {course} as c ON cc1.id = c.category JOIN {context} as ctx ON ctx.instanceid = c.id AND ctx.contextlevel = 50 JOIN {role_assignments} as ra ON ra.contextid = ctx.id
                                                                join {user} as u on u.id = ra.userid 
                                                                join {role} as r on r.id = ra.roleid 
                                                                where  cc.parent in ($tyepids) AND r.id IN ($selected_roles)  ";

                // $countsql .= "JOIN {role_assignments} ra ON ra.userid = u.id 
                //               join {context} as ctx on ctx.id = ra.contextid and ctx.instanceid IN (SELECT c.id FROM {course_categories} as cc join {course_categories} as cc1 on cc1.parent = cc.id join {course} as c on c.category = cc1.id where cc.parent in ($tyepids)) and ctx.contextlevel = 50
                //             JOIN {role} as r ON r.id = ra.roleid AND r.id IN ($selected_roles) ";
            }
            $selectsql .= " AND u.deleted = 0";
            $countsql .= " AND u.deleted = 0";
            if($selected_status == 0){
                $selectsql .= " AND u.suspended = 0";
                $countsql .= " AND u.suspended = 0";
            }else if($selected_status == 1){
               $selectsql .= " AND u.suspended IN ($selected_status)";
               $countsql .= " AND u.suspended IN ($selected_status)";
            }
             if($search){
               $selectsql .= " AND (u.firstname LIKE '%$search%' OR u.lastname LIKE '%$search%' OR u.email LIKE '%$search%' OR u.idnumber LIKE '%$search%')";
               $countsql .= " AND (u.firstname LIKE '%$search%' OR u.lastname LIKE '%$search%' OR u.email LIKE '%$search%' OR u.idnumber LIKE '%$search%')";
            }
            $active = " AND u.suspended = 0";
            $inactive = " AND u.suspended = 1";
        }      
        $totalusers = $DB->count_records_sql($countsql,$cparams);
        $totalactiveusers = $DB->count_records_sql($countsql.$active,$cparams);
            
        // echo $countsql.$active;
        $totalinactiveusers = $DB->count_records_sql($countsql.$inactive,$cparams);
        $ordersql = " ORDER BY u.id DESC";
        $users = $DB->get_records_sql($selectsql.$ordersql,$cparams,$stable->start,$stable->length);     
      
        $data = array();
        foreach ($users as $user){
            $url = $CFG->wwwroot."/user/editadvanced.php?id=".$user->id;
            $record      = new stdClass();
            $record->id  = $user->id;
            $record->idnumber = $user->idnumber; 
            $record->url = $url; 
            $record->firstname = $user->firstname;
            $record->lastname = $user->lastname;                    
            $record->email = $user->email; 
            $record->role = $user->rolename ? $user->rolename : $user->role;
            $record->suspendedstatus = $user->suspended != 0 ? 'fa fa-eye-slash mr-2' :'fas fa-eye mr-2';
            $record->suspendedmethod = $user->suspended != 0 ? 'unsuspend' :'suspend';
            $data[] = $record;
        }    

        return array('totalcount' => $totalusers,'data' => $data, 'totalactiveusers' => $totalactiveusers, 'totalinactiveusers' => $totalinactiveusers, 'courses' => $tyepids, 'roles' => $selected_roles, 'status' => $selected_status);
    }

	public function admindashboard_courselist($stable,$maincategoryid,$catagory, $subcatagory,$unitid,$search = '') {

     	global $USER,$DB,$CFG;
     	require_once($CFG->dirroot . '/blocks/facultydashboard/lib.php');
        $systemcontext = context_system::instance();

        if($maincategoryid && $catagory && $subcatagory && $unitid){

            $courseids = explode(",",$unitid);

            $data = array();
            $sql = "SELECT c.id, c.fullname as course_name,c.startdate as startdate,c.enddate as enddate,(SELECT COUNT(id) FROM mdl_assign WHERE course = c.id) as assign_count,
                (SELECT COUNT(id) FROM mdl_quiz WHERE course = c.id) as quiz_count,
                (SELECT COUNT(id) FROM mdl_forum WHERE course = c.id and type != 'news') as forum_count

                FROM mdl_course_categories as ct
                JOIN mdl_course as c ON c.category = ct.id WHERE c.id in ($unitid) ";
            $countsql = "SELECT COUNT(c.id) 
                FROM mdl_course_categories as ct
                JOIN mdl_course as c ON c.category = ct.id WHERE c.id in ($unitid) ";

           
            if($search){
                $sql .= " AND c.id like '%$search%' or c.fullname like '%$search%' or c.shortname like '%$search%'";
                $countsql .= " AND c.id like '%$search%' or c.fullname like '%$search%' or c.shortname like '%$search%'";
            }
            
            $mycourses  = $DB->get_records_sql($sql, $data,$stable->start,$stable->length);
            $totalusers = $DB->count_records_sql($countsql,$data);

        }elseif($maincategoryid && $catagory && $subcatagory){
             $data = array();
            $sql = "SELECT c.id, c.fullname as course_name,c.startdate as startdate,c.enddate as enddate,(SELECT COUNT(id) FROM {assign} WHERE course = c.id) as assign_count,
                (SELECT COUNT(id) FROM {quiz} WHERE course = c.id) as quiz_count,
                (SELECT COUNT(id) FROM {forum} WHERE course = c.id and type != 'news') as forum_count";
             $countsql = "SELECT COUNT(c.id) ";
             //course coradinatior code start 
            if(is_siteadmin()|| has_capability('block/admindashboard:bits', $systemcontext) || has_capability('block/admindashboard:mits', $systemcontext) || has_capability('block/admindashboard:mba', $systemcontext)){
             $sql .= " FROM {course_categories} as ct
                JOIN {course} as c ON c.category = ct.id WHERE ct.id = $subcatagory ";
           
            $countsql .="FROM {course_categories} as ct
                JOIN {course} as c ON c.category = ct.id WHERE ct.id = $subcatagory";
            }else{

                $sql .= " from {role_assignments} as ra 
                                                  join {role} as r on r.id = roleid
                                                  join {context} as ctx on ctx.id = ra.contextid and ctx.contextlevel = 50
                                                  join {course} as c on c.id = ctx.instanceid
                                                  join {course_categories} as cc1 on cc1.id = c.category
                                                   where ra.userid = $USER->id and cc1.id = $subcatagory";

                 $countsql .=" from {role_assignments} as ra 
                                                  join {role} as r on r.id = roleid
                                                  join {context} as ctx on ctx.id = ra.contextid and ctx.contextlevel = 50
                                                  join {course} as c on c.id = ctx.instanceid
                                                  join {course_categories} as cc1 on cc1.id = c.category
                                                   where ra.userid = $USER->id and cc1.id = $subcatagory";

            }
            //course coradinatior code start 
            if($search){
                $sql .= " AND (c.id like '%$search%' or c.fullname like '%$search%' or c.shortname like '%$search%')";
                $countsql .= " AND (c.id like '%$search%' or c.fullname like '%$search%' or c.shortname like '%$search%')";
            }
            $mycourses  = $DB->get_records_sql($sql, $data,$stable->start,$stable->length);
            $totalusers = $DB->count_records_sql($countsql,$data);
        }else{
             $data = array();
            $sql = "SELECT c.id, c.fullname as course_name,c.startdate as startdate,c.enddate as enddate,(SELECT COUNT(id) FROM {assign} WHERE course = c.id) as assign_count,
                (SELECT COUNT(id) FROM mdl_quiz WHERE course = c.id) as quiz_count,
                (SELECT COUNT(id) FROM mdl_forum WHERE course = c.id and type != 'news') as forum_count";
             $countsql = "SELECT COUNT(c.id)";
             //course coradinatior code start 
              if(is_siteadmin()|| has_capability('block/admindashboard:bits', $systemcontext) || has_capability('block/admindashboard:mits', $systemcontext) || has_capability('block/admindashboard:mba', $systemcontext)){
             $sql .=" FROM {course_categories} as ct2
                join {course_categories} as ct on ct.parent = ct2.id
                JOIN {course} as c ON c.category = ct.id WHERE ct2.id = $catagory ";
           
            $countsql .= " FROM {course_categories} as ct2
                join {course_categories} as ct on ct.parent = ct2.id
                JOIN {course} as c ON c.category = ct.id WHERE ct2.id = $catagory";
            }else{

                 $sql .= " from {role_assignments} as ra 
                                                  join {role} as r on r.id = roleid
                                                  join {context} as ctx on ctx.id = ra.contextid and ctx.contextlevel = 50
                                                  join {course} as c on c.id = ctx.instanceid
                                                  join {course_categories} as cc1 on cc1.id = c.category
                                                  join {course_categories} as cc on cc.id = cc1.parent
                                                   where ra.userid = $USER->id and cc.id = $catagory";

                 $countsql .=" from {role_assignments} as ra 
                                                  join {role} as r on r.id = roleid
                                                  join {context} as ctx on ctx.id = ra.contextid and ctx.contextlevel = 50
                                                  join {course} as c on c.id = ctx.instanceid
                                                  join {course_categories} as cc1 on cc1.id = c.category
                                                  join {course_categories} as cc on cc.id = cc1.parent
                                                   where ra.userid = $USER->id and cc.id = $catagory";

            }
            //course coradinatior code start 
            if($search){
                $sql .= " AND (c.id like '%$search%' or c.fullname like '%$search%' or c.shortname like '%$search%')";
                $countsql .= " AND (c.id like '%$search%' or c.fullname like '%$search%' or c.shortname like '%$search%')";
            }


            $mycourses  = $DB->get_records_sql($sql, $data,$stable->start,$stable->length);
            $totalusers = $DB->count_records_sql($countsql,$data);
        }
        $selectcourse = array();

        
        foreach ($mycourses as $courses) {
                $url = $CFG->wwwroot."/course/view.php?id=".$courses->id;
                $studentcount =  $DB->get_record_sql('
                    SELECT 
                    count(u.id) as studentcount
                    FROM mdl_user u
                    INNER JOIN {role_assignments} ra ON ra.userid = u.id
                    INNER JOIN {context} ct ON ct.id = ra.contextid
                    INNER JOIN {course} c ON c.id = ct.instanceid
                    INNER JOIN {role} r ON r.id = ra.roleid
                    WHERE c.id =:course and ra.roleid = 5
                ',array('course'=>$courses->id));

                $trainercount =  $DB->get_record_sql('
                    SELECT 
                    count(u.id) as trainercount
                    FROM mdl_user u
                    INNER JOIN {role_assignments} ra ON ra.userid = u.id
                    INNER JOIN {context} ct ON ct.id = ra.contextid AND ct.contextlevel = 50
                    INNER JOIN {course} c ON c.id = ct.instanceid
                    INNER JOIN {role} r ON r.id = ra.roleid
                    WHERE c.id =:course and ra.roleid = 4 AND ra.userid != 2
                ',array('course'=>$courses->id));

                 $catname = $DB->get_record_sql("SELECT cc.name from {course_categories} as cc join {course} as c on c.category =  cc.id where c.id =:courseid",array('courseid' => $courses->id)); 
                $record      = new stdClass();
                $record->courseid = $courses->id;
                $record->catname = $catname->name;  
                $courseimage = get_courses_image($courses);
                // print_r($courseimage);exit;
                $record->coursepic = $courseimage['imageurl'];
                $record->imgurlflag = $courseimage['imgurlflag'];
                $record->url = $url; 
                $record->studentcount = $studentcount->studentcount;
                $record->trainercount = $trainercount->trainercount;
                $record->startdate = $courses->startdate>0?userdate($courses->startdate,'%d/%m/%Y'):'Not Available';; 
                $record->enddate = $courses->enddate>0?userdate($courses->enddate,'%d/%m/%Y'):'Not Available'; 
                $record->course_name = $courses->course_name; 
                $record->assign_count = $courses->assign_count; 
                $record->quiz_count= $courses->quiz_count;  
                $record->forum_count= $courses->forum_count; 
                $record->userid= $USER->id;           
				
				 $coursetitle = $courses->course_name;

                $max = 25;
                if(strlen($coursetitle) > $max) {
                $coursestring =  substr($coursetitle, 0, $max). "....";
                } else {
                  $coursestring = $coursetitle;
                 }
              
                $record->my_courses = $coursestring; 
                $record->my_coursesfull = $coursetitle;
                $data[] = $record;

        }
       return array('totalcount' => $totalusers,'data' => $data);
    }

     public function get_assignments($filter = false, $courseid, $userid){
        global $USER;

        $systemcontext = \context_system::instance();

        $options = array('targetID' => 'manage_assignments','perPage' => 5, 'cardClass' => 'w_one', 'viewType' => 'card');
        
        $options['methodName']='block_admin_assign_details';
        $options['templateName']='block_admindashboard/assignmentdisplay'; 
        $options = json_encode($options);

        $dataoptions = json_encode(array('userid' =>$userid,'contextid' => $systemcontext->id,'courseid' => $courseid));
        $filterdata = json_encode(array());

        $context = [
                'targetID' => 'manage_assignments',
                'options' => $options,
                'dataoptions' => $dataoptions,
                'filterdata' => $filterdata
        ];

        if($filter){
            return  $context;
        }else{
            return  $this->render_from_template('local_rolemanagement/cardPaginate', $context);
        }
    }

    public function get_assigndetails($stable,$courseid,$userid,$search = null ){
          global $USER,$DB,$CFG;

            require_once($CFG->dirroot . '/blocks/facultydashboard/groupuser.php');
 
             $systemcontext = context_system::instance();
             $userid = $userid > 0 ? $userid : $USER->id; 

            $context = context_course::instance($courseid);
            $roles = get_user_roles($context, $userid, true);
            $role = key($roles);
            $rolename = $roles[$role]->shortname;

             if($search){
                 $totalassigns = $DB->count_records_sql("SELECT count(a.id) from {assign} as a where a.course = :courseid and a.name like '%$search%'",array('courseid' =>$courseid));
            }else{
               $totalassigns = $DB->count_records_sql("SELECT count(a.id) from {assign} as a where a.course = :courseid",array('courseid' =>$courseid));
            }

            $sql = "SELECT 
                concat(a.id,'_',c.id) as sidi,
                a.id as assignid,
                a.name as assign_name,
                c.fullname as course_name,
                cm.id as module_id,
                (select count(DISTINCT asub.userid) from mdl_assign_submission as asub JOIN mdl_user_enrolments AS ue ON asub.userid = ue.userid JOIN mdl_enrol AS e ON ue.enrolid = e.id
                    WHERE asub.assignment = a.id and asub.latest = 1 AND asub.status = 'submitted' and e.courseid = c.id and ue.status = 0) as submission_count,
                (SELECT COUNT(ura.userid) from mdl_role_assignments as ura 
                    join mdl_role as ur on ur.id = ura.roleid 
                    where ura.contextid = ctx.id and ur.id = 5) as user_count
                from mdl_course as c 
                join mdl_assign as a on a.course = c.id 
                join mdl_course_modules as cm on cm.course = c.id 
                join mdl_modules as m on  m.id = cm.module
                join mdl_context as ctx on ctx.instanceid = c.id 
                left join mdl_role_assignments as ra on ra.contextid = ctx.id and ra.roleid = 5";
             if($search){
              $sql .= " where c.id =:courseid and cm.instance = a.id and m.name = 'assign' and ctx.contextlevel = 50  and a.name like '%$search%' GROUP BY a.id, c.id";
             }else{
              $sql .= " where c.id =:courseid and cm.instance = a.id and m.name = 'assign' and ctx.contextlevel = 50  GROUP BY a.id, c.id, a.name, c.fullname, cm.id, ctx.id";
              
             }
             

             $params = array('courseid'=>$courseid);


              $myassign = $DB->get_records_sql($sql, $params,$stable->start, $stable->length);

            $assign_data = array();

            foreach ($myassign as $assign) {
                 $assign_url = $CFG->wwwroot.'/mod/assign/view.php?id='.$assign->module_id;

                 $assign_details = array();

                

                 $assign_details['assign_name'] = $assign->assign_name; 
                 $assign_details['course_name'] = $assign->course_name;

                 if(($rolename == 'vit_trainer')){
                     $assigncount = group_activity($userid, $courseid, true, $assign->assignid, $assign->module_id);
                  }

                 if($assigncount && ($rolename == 'vit_trainer')) {
                    $assign_details['user_count'] = $assigncount->user_count;
                    $assign_details['submission_count'] = $assigncount->submission_count;
                 }
                 else {
                    $assign_details['user_count'] = $assign->user_count;
                    $assign_details['submission_count'] = $assign->submission_count;
                 }
                 
                 $assign_details['assign_url'] = $assign_url;
                 $assign_details['assignid'] = $assign->assignid;
                 $assign_details['courseid'] = $courseid;
                 $assign_details['userid'] = $userid;

                 $assign_data[] = $assign_details;

            }

           

       
       
      return array('totalcount' => $totalassigns,'data' => $assign_data);
    }

    public function get_assignment_users($filter = false, $courseid,$assignid,$userid){
        global $USER;

        $systemcontext = \context_system::instance();

        $options = array('targetID' => 'manage_assignments_users','perPage' => 5, 'cardClass' => 'w_one', 'viewType' => 'card');
        
        $options['methodName']='block_admin_assign_users';
        $options['templateName']='block_admindashboard/assignusers'; 
        $options = json_encode($options);

        $dataoptions = json_encode(array('userid' =>$userid,'contextid' => $systemcontext->id,'courseid' => $courseid,'assignid' => $assignid));
        $filterdata = json_encode(array());

        $context = [
                'targetID' => 'manage_assignments_users',
                'options' => $options,
                'dataoptions' => $dataoptions,
                'filterdata' => $filterdata
        ];

        if($filter){
            return  $context;
        }else{
            return  $this->render_from_template('local_rolemanagement/cardPaginate', $context);
        }
    }


    public function get_assignusers($stable,$courseid,$userid,$assignid,$search = null ){
        global $USER,$DB,$CFG;

          require_once($CFG->dirroot . '/blocks/facultydashboard/groupuser.php');

           $systemcontext = context_system::instance();

          $context = context_course::instance($courseid);
          $userid = $userid > 0 ? $userid : $USER->id; 
            $roles = get_user_roles($context, $userid, true);
            $role = key($roles);
            $rolename = $roles[$role]->shortname;

           if($rolename == 'vit_trainer')
          {
          
          if($search){
            $sql = group_activity($userid, $courseid, false, $assignid,$search);
          }else{
             $sql = group_activity($userid, $courseid, false, $assignid);
          }
                  

           $params = array('courseid' => $courseid, 'userid' => $userid, 'courseid1' => $courseid, 'assignid' => $assignid);

           $totalusers = $DB->count_records_sql($sql['countusersql'],$params);
          
           if($sql != 0){
              $myassigndata = $DB->get_records_sql($sql['assignusersql'], $params,$stable->start, $stable->length);
           }else{

              $myassigndata = array();
           }
         }else{

          if($search){

             $totalusers = $DB->count_records_sql("SELECT count(ra.userid) from {course} AS c                        
               JOIN {context} AS ctx ON ctx.instanceid = c.id AND ctx.contextlevel = 50
               JOIN {role_assignments} AS ra ON ra.contextid = ctx.id AND ra.roleid = 5
               join {user} as u on u.id = ra.userid
               where c.id =:courseid   and (u.firstname like '%$search%' or u.lastname like '%$search%' or u.email like '%$search%' or u.username like '%$search%' or u.idnumber like '%$search%')
               ",array('courseid' =>$courseid));

              
            

          }else{
            $totalusers = $DB->count_records_sql("SELECT count(c.id) from {course} AS c                        
               JOIN {context} AS ctx ON ctx.instanceid = c.id AND ctx.contextlevel = 50
               JOIN {role_assignments} AS ra ON ra.contextid = ctx.id AND ra.roleid = 5
               where c.id =:courseid
               ",array('courseid' =>$courseid)); 
          }
           

             $sql ="SELECT distinct ra.id,
               CONCAT(u.firstname,' ', u.lastname) AS student_name,
               u.username AS User,
               u.idnumber AS student_id,
               c.shortname AS course,              
               a.name AS assignment,
               u.id as userid,
               CASE
               WHEN asub.timemodified IS NOT NULL then DATE_FORMAT(FROM_UNIXTIME (asub.timemodified),'%e %b %Y - %H:%i') ELSE 'N/A' END AS 'submitteddate',
            
               CASE 
               WHEN ag.grade = -1 or ag.grade IS NULL THEN 'Not Graded' ELSE ag.grade END AS final_grade
               FROM {assign} a 
               JOIN {course} AS c ON a.course = c.id                         
               JOIN {context} AS ctx ON ctx.instanceid = c.id AND ctx.contextlevel = 50
               JOIN {role_assignments} AS ra ON ra.contextid = ctx.id AND ra.roleid = 5
               JOIN {user} AS u ON ra.userid = u.id
               LEFT JOIN {assign_submission} asub on a.id = asub.assignment AND asub.userid =  ra.userid
               LEFT JOIN {assign_grades} as ag on asub.assignment = ag.assignment AND ag.userid = u.id            
               ";


             if($search){

               $sql .= "WHERE c.id = :courseid AND a.id = :assignment and (u.firstname like '%$search%' or u.lastname like '%$search%' or u.email like '%$search%' or u.username like '%$search%' or u.idnumber like '%$search%')
               ORDER BY u.id, a.id";

             }else{
              $sql .= "WHERE c.id = :courseid AND a.id = :assignment
               ORDER BY u.id, a.id";
             }

             $params = array('assignment'=>$assignid, 'courseid'=>$courseid);

              $myassigndata = $DB->get_records_sql($sql, $params,$stable->start, $stable->length);
         } 


          $assign_gradedata = array();

          foreach ($myassigndata as $myassign) {

               $assign_grade = array();

               $assign_grade['user_name'] = $myassign->student_name; 
               $assign_grade['user_id'] = $myassign->student_id; 
               $assign_grade['assign_name'] = $myassign->assignment;
               $assign_grade['submitted_date'] = $myassign->submitteddate;
               $assign_grade['final_grade'] = $myassign->final_grade;
               $assign_grade['url'] = $CFG->wwwroot.'/user/profile.php?id='.$myassign->userid;
               
               $assign_gradedata[] = $assign_grade;

          } 
     
    return array('totalcount' => $totalusers,'data' => $assign_gradedata);
  }

     public function get_assignment_subusers($filter = false, $courseid,$assignid,$userid){
        global $USER;

        $systemcontext = \context_system::instance();

        $options = array('targetID' => 'manage_assignments_subusers','perPage' => 5, 'cardClass' => 'w_one', 'viewType' => 'card');
        
        $options['methodName']='block_admin_assign_subusers';
        $options['templateName']='block_admindashboard/assignsub'; 
        $options = json_encode($options);

        $dataoptions = json_encode(array('userid' =>$userid,'contextid' => $systemcontext->id,'courseid' => $courseid,'assignid' => $assignid));
        $filterdata = json_encode(array());

        $context = [
                'targetID' => 'manage_assignments_subusers',
                'options' => $options,
                'dataoptions' => $dataoptions,
                'filterdata' => $filterdata
        ];

        if($filter){
            return  $context;
        }else{
            return  $this->render_from_template('local_rolemanagement/cardPaginate', $context);
        }
    }


    public function get_assignsubusers($stable,$courseid,$userid,$assignid,$search = null ){
        global $USER,$DB,$CFG;

        require_once($CFG->dirroot . '/blocks/facultydashboard/groupuser.php');

           $systemcontext = context_system::instance();

            $context = context_course::instance($courseid);
            $userid = $userid > 0 ? $userid : $USER->id; 
            $roles = get_user_roles($context, $userid, true);
            $role = key($roles);
            $rolename = $roles[$role]->shortname;


           if($rolename == 'vit_trainer'){ 

                if($search){
                $sql = group_activity($userid, $courseid, false, $assignid,$search);
              }else{
                 $sql = group_activity($userid, $courseid, false, $assignid);
              }           

               $params = array('courseid' => $courseid, 'userid' => $userid, 'courseid1' => $courseid, 'assignid' => $assignid);

               $totalusers = $DB->count_records_sql($sql['countsql'],$params);

               if($sql != 0){
                  $mysubdata = $DB->get_records_sql($sql['assignsql'], $params,$stable->start, $stable->length);
               }else{

                  $mysubdata = array();
               }
           }else{

            if($search){

                 $totalusers = $DB->count_records_sql("SELECT count(DISTINCT asub.userid) from {assign_submission} as asub
                  join {user} as u on u.id = asub.userid
                  WHERE assignment =:assignid  and latest = 1 AND status = 'submitted' and  (u.firstname like '%$search%' or u.lastname like '%$search%' or u.email like '%$search%' or u.username like '%$search%' or u.idnumber like '%$search%')
               ",array('assignid' =>$assignid)); 
            }else{

               $totalusers = $DB->count_records_sql("SELECT count(DISTINCT asub.userid) from {assign_submission} as asub WHERE assignment =:assignid  and latest = 1 AND status = 'submitted'
               ",array('assignid' =>$assignid)); 
            }

            
            
             $sql = "SELECT distinct asub.id,
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
                           ";

             if($search){

              $sql .= " WHERE asub.status = 'submitted' and c.id = :courseid AND a.id  = :assignment and (u.firstname like '%$search%' or u.lastname like '%$search%' or u.email like '%$search%' or u.username like '%$search%' or u.idnumber like '%$search%')
                           ORDER BY asub.timemodified ASC, u.username, c.shortname ";

             }else{

                 $sql .= " WHERE asub.status = 'submitted' and c.id = :courseid AND a.id  = :assignment
                           ORDER BY asub.timemodified ASC, u.username, c.shortname ";
             }

             $params = array('assignment'=>$assignid, 'courseid'=>$courseid);

             $mysubdata = $DB->get_records_sql($sql, $params,$stable->start, $stable->length);

           }

          $sub_gradedata = array();

          foreach ($mysubdata as $mysub) {

               // $sql = group_activity($USER->id, $courseid, false, $assignid, $moduleid);

               $marking_grade = $CFG->wwwroot.'/mod/assign/view.php?id='.$mysub->module_id.'&rownum=0&action=grader&userid='.$mysub->userid;

               $assign_sub = array();

               $assign_sub['student_name'] = $mysub->student_name; 
               $assign_sub['student_id'] = $mysub->student_id; 
               $assign_sub['assignment'] = $mysub->assignment;            
               $assign_sub['submitteddate'] = $mysub->submitteddate;
               $assign_sub['final_grade'] = $mysub->final_grade > 0 ? $mysub->final_grade : 'Not Graded';
               $assign_sub['marking_grade'] = $marking_grade;
               $assign_sub['url'] = $CFG->wwwroot.'/user/profile.php?id='.$mysub->userid;
               
               $sub_gradedata[] = $assign_sub;

          }

    return array('totalcount' => $totalusers,'data' => $sub_gradedata);
  }


     public function get_usersdetails($filter = false, $courseid,$userid){
        global $USER;

        $systemcontext = \context_system::instance();

        $options = array('targetID' => 'manage_userdetails','perPage' => 5, 'cardClass' => 'w_one', 'viewType' => 'card');
        
        $options['methodName']='block_admin_manage_userdetails';
        $options['templateName']='block_admindashboard/usersdisplay'; 
        $options = json_encode($options);

        $dataoptions = json_encode(array('userid' =>$userid,'contextid' => $systemcontext->id,'courseid' => $courseid));
        $filterdata = json_encode(array());

        $context = [
                'targetID' => 'manage_userdetails',
                'options' => $options,
                'dataoptions' => $dataoptions,
                'filterdata' => $filterdata
        ];

        if($filter){
            return  $context;
        }else{
            return  $this->render_from_template('local_rolemanagement/cardPaginate', $context);
        }
    }


    public function get_assignuserdetails($stable,$courseid,$userid,$search =null ){
        global $USER,$DB,$CFG;

      require_once($CFG->dirroot . '/blocks/facultydashboard/groupuser.php');

           $systemcontext = context_system::instance();
           $context = context_course::instance($courseid);
            $roles = get_user_roles($context, $userid, true);
            $role = key($roles);
            $rolename = $roles[$role]->shortname;
          
          if($rolename == 'vit_trainer'){  
           $sql = group_member($userid, $courseid, false);

           $params = array('courseid' => $courseid, 'userid' => $userid, 'courseid1' => $courseid);
           $totalusers = $DB->count_records_sql($sql['countsql'],$params);
           if($sql != 0){
              $myusers = $DB->get_records_sql($sql['studentsql'], $params,$stable->start, $stable->length);
           }else{
              $myusers = array();
           }
           }else{

               $sql = 'SELECT DISTINCT(u.id) as userid,cc.id as completionid, u.idnumber AS id_number, u.username AS user_name, u.email AS user_email,CONCAT(u.firstname," ",u.lastname) as studentname,
                     CASE WHEN cc.timecompleted IS NOT NULL THEN "completed" ELSE "not completed" END AS course_status
                 
                 from {course} AS c                 
                 join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                 JOIN {role_assignments} AS ra ON ra.contextid = ctx.id
                 JOIN {role} AS r ON r.id = ra.roleid
                 JOIN {user} AS u ON u.id = ra.userid
                 LEFT JOIN {course_completions} AS cc ON u.id = cc.userid AND c.id = cc.course';

             if($search){


                 $totalusers = $DB->count_records_sql("SELECT count(c.id) from {course} AS c                        
               JOIN {context} AS ctx ON ctx.instanceid = c.id AND ctx.contextlevel = 50
               JOIN {role_assignments} AS ra ON ra.contextid = ctx.id AND ra.roleid = 5
               join {user} as u on u.id = ra.userid
               where c.id =:courseid
               ",array('courseid' =>$courseid)); 

                $sql .= " WHERE r.shortname = 'student' AND c.id = :courseid and (u.firstname like '%$search%' or u.lastname like '%$search%' or u.email like '%$search%' or u.username like '%$search%' or u.idnumber like '%$search%') GROUP BY u.id";

             }else{

                $totalusers = $DB->count_records_sql("SELECT count(c.id) from {course} AS c                        
               JOIN {context} AS ctx ON ctx.instanceid = c.id AND ctx.contextlevel = 50
               JOIN {role_assignments} AS ra ON ra.contextid = ctx.id AND ra.roleid = 5
               where c.id =:courseid
               ",array('courseid' =>$courseid)); 

                $sql .= ' WHERE r.shortname = "student" AND c.id = :courseid GROUP BY u.id';

             }
             
            $params = array('courseid' => $courseid);
            $myusers = $DB->get_records_sql($sql, $params,$stable->start, $stable->length);
           }
           
           $user_data = array();

          foreach ($myusers as $myuser) {

               $user_details = array();               
               $user_details['id_number'] = $myuser->id_number; 
               $user_details['user_name'] = $myuser->studentname; 
               $user_details['user_email'] = $myuser->user_email;
               $user_details['userid'] = $myuser->userid;
               $user_details['url'] = $CFG->wwwroot.'/user/profile.php?id='.$myuser->userid;
               $user_details['course_status'] = $myuser->course_status;

               $user_data[] = $user_details;

          }
     
    return array('totalcount' => $totalusers,'data' => $user_data);
  }


    public function get_quizdetails($stable,$courseid,$userid ){
          global $USER,$DB,$CFG;
 
             $options = array('targetID' => 'manage_quiz_details','perPage' => 5, 'cardClass' => 'w_one', 'viewType' => 'card');
         $systemcontext = \context_system::instance();
        $options['methodName']='block_admin_quiz_details';
        $options['templateName']='block_admindashboard/quizdisplay'; 
        $options = json_encode($options);

        $dataoptions = json_encode(array('userid' =>$userid,'contextid' => $systemcontext->id,'courseid' => $courseid));
        $filterdata = json_encode(array());

        $context = [
                'targetID' => 'manage_quiz_details',
                'options' => $options,
                'dataoptions' => $dataoptions,
                'filterdata' => $filterdata
        ];

        if($filter){
            return  $context;
        }else{
            return  $this->render_from_template('local_rolemanagement/cardPaginate', $context);
        }
    }



     public function get_quizdetailsdata($stable,$courseid,$userid,$search = null ){
          global $USER,$DB,$CFG;
          require_once($CFG->dirroot . '/blocks/facultydashboard/groupuser.php');
 
            $systemcontext = context_system::instance();
            $userid = $userid > 0 ? $userid : $USER->id; 

             $context = context_course::instance($courseid);
            $roles = get_user_roles($context, $userid, true);
            $role = key($roles);
            $rolename = $roles[$role]->shortname; 

            $sql = "SELECT concat(q.id,'_',c.id) as sidi,
                        q.id as quizid, q.name as quiz_name, cm.id as module_id,
                        c.fullname as course_name, (select count(DISTINCT qa.userid) from {quiz} as q join {quiz_attempts} as qa on q.id = qa.quiz join {user} as u on qa.userid = u.id
                            join {role_assignments} as ra on u.id = ra.userid AND ra.contextid = ctx.id
                            where ra.roleid = 5 and qa.state = 'finished' and q.course = c.id and q.id = quizid) as submission_count,
                        (SELECT COUNT(ra.userid) from {role_assignments} as ra 
                            join {role} as r on r.id = ra.roleid
                            where ra.contextid = ctx.id and r.id = 5) as user_count
                        from {course} as c 
                        join {quiz} as q on q.course = c.id
                        join {course_modules} as cm on cm.course = c.id and cm.instance = q.id AND cm.module = (SELECT id FROM {modules} WHERE name = 'quiz')
                        join {context} as ctx on ctx.instanceid = cm.course 
                        left join {role_assignments} as ra on ra.contextid = ctx.id
                       ";

             

            $quiz_data = array();
            if($search){

               $totalquiz = $DB->count_records_sql("SELECT count(a.id) from {quiz} as a
                where a.course = :courseid and a.name like '%$search%'",array('courseid' =>$courseid));

               $sql .= " where c.id = :courseid and cm.instance = q.id and ctx.contextlevel = 50  and q.name like '%$search%' GROUP BY q.id, c.id";

            }else{
                 $totalquiz = $DB->count_records_sql("SELECT count(a.id) from {quiz} as a where a.course = :courseid",array('courseid' =>$courseid));
                 $sql .= " where c.id = :courseid and cm.instance = q.id and ctx.contextlevel = 50  GROUP BY q.id, c.id";
            }

            $params = array('courseid'=>$courseid);

           

            $myquiz = $DB->get_records_sql($sql, $params,$stable->start, $stable->length);

            

            foreach ($myquiz as $quiz) {

                $quiz_url = $CFG->wwwroot.'/mod/quiz/view.php?id='.$quiz->module_id;

                 $quiz_details = array();

                 
                 if($rolename == 'vit_trainer')
                 {
                  $quizcount = groupquiz_activity($userid, $courseid, true, $quiz->quizid, $quiz->module_id);
                 }
                  
                
                 $quiz_details['quiz_name'] = $quiz->quiz_name; 
                 $quiz_details['course_name'] = $quiz->course_name;

                 if($quizcount) {
                    $quiz_details['user_count'] = $quizcount->user_count;
                    $quiz_details['submission_count'] = $quizcount->submission_count;
                 }
                 else {
                    $quiz_details['user_count'] = $quiz->user_count;
                    $quiz_details['submission_count'] = $quiz->submission_count;
                 } 
                 
                 $quiz_details['quiz_url'] = $quiz_url;
                 $quiz_details['quizid'] = $quiz->quizid;
                 $quiz_details['courseid'] = $courseid;
                 $quiz_details['userid'] = $userid;

                 $quiz_data[] = $quiz_details;

            }
           

       
       
      return array('totalcount' => $totalquiz,'data' => $quiz_data);
    }


     public function get_quiz_enrol($stable,$courseid,$quizid,$userid  ){
          global $USER,$DB,$CFG;
 
             $options = array('targetID' => 'manage_quiz_enrol','perPage' => 5, 'cardClass' => 'w_one', 'viewType' => 'card');
        
        $options['methodName']='block_admin_quiz_enrol';
        $options['templateName']='block_admindashboard/quizusers'; 
        $options = json_encode($options);

        $dataoptions = json_encode(array('userid' =>$userid,'contextid' => $systemcontext->id,'courseid' => $courseid,'quizid' => $quizid));
        $filterdata = json_encode(array());

        $context = [
                'targetID' => 'manage_quiz_enrol',
                'options' => $options,
                'dataoptions' => $dataoptions,
                'filterdata' => $filterdata
        ];

        if($filter){
            return  $context;
        }else{
            return  $this->render_from_template('local_rolemanagement/cardPaginate', $context);
        }
    }



    public function get_quiz_enroldata($stable,$courseid,$userid,$quizid,$search = null ){
        global $USER,$DB,$CFG;

        require_once($CFG->dirroot . '/blocks/facultydashboard/groupuser.php');

           $systemcontext = context_system::instance();
            $userid = $userid > 0 ? $userid : $USER->id;
            $context = context_course::instance($courseid);
            $roles = get_user_roles($context, $userid, true);
            $role = key($roles);
            $rolename = $roles[$role]->shortname; 

            if($rolename == 'vit_trainer') 
            {

             if($search){
                    $sql = groupquiz_activity($userid, $courseid, false, $quizid,$search);
            }else{
                    $sql = groupquiz_activity($userid, $courseid, false, $quizid);
            }
           // print_r($sql); exit;

           $params = array('courseid' => $courseid, 'userid' =>  $userid, 'courseid1' => $courseid, 'quizid' => $quizid);

           $totalusers = $DB->count_records_sql($sql['countquizusersql'],$params);

           if($sql != 0){
              $myquizdata = $DB->get_records_sql($sql['quizusersql'], $params,$stable->start, $stable->length);
           }else{

              $myquizdata = array();
           }
         }else{

            $sql = "SELECT DISTINCT
               CONCAT(u.firstname,' ', u.lastname) as student_name,
               u.idnumber as student_id,
               c.fullname as course_name,
               q.name as quiz_name,
               q.id as quizid,
                u.id as userid,               
               CASE
               WHEN qa.timemodified IS NOT NULL then DATE_FORMAT(FROM_UNIXTIME (qa.timemodified),'%e %b %Y - %H:%i') ELSE 'NA' END AS 'submitted_date',
               CASE
               WHEN qa.timemodified IS NOT NULL then 'Submitted' ELSE 'Not Submitted' END AS 'status',
               CASE
               WHEN qz.grade IS NOT NULL then qz.grade ELSE 'NA' END AS 'final_grade'
               from {course} as c
               JOIN {course_modules} as cm on cm.course = c.id
               JOIN {modules} as m on  m.id = cm.module
               JOIN {context} as ctx on ctx.instanceid = c.id 
               JOIN {role_assignments} as ra on ra.contextid = ctx.id
               JOIN {user} as u on u.id = ra.userid
               LEFT JOIN {quiz} as q on q.course = c.id
               LEFT JOIN {quiz_attempts} as qa on qa.userid = u.id and q.id = qa.quiz
               LEFT JOIN {quiz_grades} as qz on qz.userid = u.id and qz.quiz = q.id
                               ";

            $params = array('quizid'=>$quizid, 'courseid'=>$courseid);

          if($search){

            $totalusers = $DB->count_records_sql("SELECT count(c.id) from {course} AS c                        
               JOIN {context} AS ctx ON ctx.instanceid = c.id AND ctx.contextlevel = 50
               JOIN {role_assignments} AS ra ON ra.contextid = ctx.id AND ra.roleid = 5
               join {user} as u on u.id = ra.userid and (u.firstname like '%$search%' or u.lastname like '%$search%' or u.email like '%$search%' or u.username like '%$search%' or u.idnumber like '%$search%')
               where c.id =:courseid
               ",array('courseid' =>$courseid)); 
             $sql .= "where c.id = :courseid and q.id = :quizid and ctx.contextlevel = 50 and ra.roleid = 5  and (u.firstname like '%$search%' or u.lastname like '%$search%' or u.email like '%$search%' or u.username like '%$search%' or u.idnumber like '%$search%')
               ORDER BY u.id, q.id";

          }else{

            $totalusers = $DB->count_records_sql("SELECT count(c.id) from {course} AS c                        
               JOIN {context} AS ctx ON ctx.instanceid = c.id AND ctx.contextlevel = 50
               JOIN {role_assignments} AS ra ON ra.contextid = ctx.id AND ra.roleid = 5
               where c.id =:courseid
               ",array('courseid' =>$courseid)); 

            $sql .= "where c.id = :courseid and q.id = :quizid and ctx.contextlevel = 50 and ra.roleid = 5 
               ORDER BY u.id, q.id";

          }                   
            $myquizdata = $DB->get_records_sql($sql, $params,$stable->start, $stable->length);
         }

          $quiz_userdata = array();

          foreach ($myquizdata as $myquiz) {

               $quiz_enrol = array();

               $quiz_enrol['student_name'] = $myquiz->student_name; 
               $quiz_enrol['student_id'] = $myquiz->student_id; 
               $quiz_enrol['course_name'] = $myquiz->course_name;            
               $quiz_enrol['quiz_name'] = $myquiz->quiz_name;
               $quiz_enrol['submitted_date'] = $myquiz->submitted_date;
               $quiz_enrol['final_grade'] = $myquiz->final_grade;
               $quiz_enrol['status'] = $myquiz->status;
               $quiz_enrol['url'] = $CFG->wwwroot.'/user/profile.php?id='.$myquiz->userid;
               
               $quiz_userdata[] = $quiz_enrol;

          }
     
     
    return array('totalcount' => $totalusers,'data' => $quiz_userdata);
  }



     public function get_quiz_subusers($stable,$courseid,$userid,$quizid ){
          global $USER,$DB,$CFG;
 
             $options = array('targetID' => 'manage_quiz_subusers','perPage' => 5, 'cardClass' => 'w_one', 'viewType' => 'card');
        
        $options['methodName']='block_admin_quiz_subusers';
        $options['templateName']='block_admindashboard/quizsub'; 
        $options = json_encode($options);

        $dataoptions = json_encode(array('userid' =>$userid,'contextid' => $systemcontext->id,'courseid' => $courseid,'quizid' => $quizid));
        $filterdata = json_encode(array());

        $context = [
                'targetID' => 'manage_quiz_subusers',
                'options' => $options,
                'dataoptions' => $dataoptions,
                'filterdata' => $filterdata
        ];

        if($filter){
            return  $context;
        }else{
            return  $this->render_from_template('local_rolemanagement/cardPaginate', $context);
        }
    }






    public function get_quiz_subusersdata($stable,$courseid,$userid,$quizid,$search = null){
         global $USER,$DB,$CFG;

           $systemcontext = context_system::instance();

           require_once($CFG->dirroot . '/blocks/facultydashboard/groupuser.php');

            $userid = $userid > 0 ? $userid : $USER->id;
            $context = context_course::instance($courseid);
            $roles = get_user_roles($context, $userid, true);
            $role = key($roles);
            $rolename = $roles[$role]->shortname; 

          if($rolename == 'vit_trainer'){

             if($search){
                    $sql = groupquiz_activity($userid, $courseid, false, $quizid,$search);
            }else{
                    $sql = groupquiz_activity($userid, $courseid, false, $quizid);
            }
           // print_r($sql); exit;

           $params = array('courseid' => $courseid, 'userid' =>  $userid, 'courseid1' => $courseid, 'quizid' => $quizid);

           $totalusers = $DB->count_records_sql($sql['countsql'],$params);

           if($sql != 0){
              $myquizsubs = $DB->get_records_sql($sql['quizsql'], $params,$stable->start, $stable->length);
           }else{

              $myquizsubs = array();
           }

          }else{


            $sql = "SELECT DISTINCT
               CONCAT(u.firstname,' ', u.lastname) as student_name,
               u.idnumber as student_id,
               c.fullname as course_name,
               q.name as quiz_name,
               qa.id as attemptid,
               q.id as quizid,
               u.id as userid,                
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
               LEFT JOIN {quiz_grades} as qz on qz.userid = u.id and qz.quiz = q.id";

               $params = array('quizid'=>$quizid , 'courseid'=>$courseid);


            if($search){
              $totalusers = $DB->count_records_sql("SELECT count(DISTINCT qa.userid) from {quiz} as q 
                    join {quiz_attempts} as qa on q.id = qa.quiz
                    join {user} as u on qa.userid = u.id
                    join {role_assignments} as ra on u.id = ra.userid
                    where ra.roleid = 5 and qa.state = 'finished' and q.course =:courseid and q.id =:quizid  and (u.firstname like '%$search%' or u.lastname like '%$search%' or u.email like '%$search%' or u.username like '%$search%' or u.idnumber like '%$search%')
               ",array('courseid' =>$courseid,'quizid' => $quizid));

              $sql .= " where qa.state = 'finished' and c.id = :courseid and q.id = :quizid and ctx.contextlevel = 50 and ra.roleid = 5 and (u.firstname like '%$search%' or u.lastname like '%$search%' or u.email like '%$search%' or u.username like '%$search%' or u.idnumber like '%$search%')
               ORDER BY qa.timemodified ASC, u.id, q.id";

            }else{
              $totalusers = $DB->count_records_sql("SELECT count(DISTINCT qa.userid) from {quiz} as q 
                    join {quiz_attempts} as qa on q.id = qa.quiz
                    join {user} as u on qa.userid = u.id
                    join {role_assignments} as ra on u.id = ra.userid
                    where ra.roleid = 5 and qa.state = 'finished' and q.course =:courseid and q.id =:quizid 
               ",array('courseid' =>$courseid,'quizid' => $quizid)); 
              $sql .= " where qa.state = 'finished' and c.id = :courseid and q.id = :quizid and ctx.contextlevel = 50 and ra.roleid = 5 
               ORDER BY qa.timemodified ASC, u.id, q.id";
            }

             $myquizsubs = $DB->get_records_sql($sql,$params,$stable->start, $stable->length);

          }

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
               $quiz_subs['url'] = $CFG->wwwroot.'/user/profile.php?id='.$myquizsub->userid;
               
               $quiz_subdata[] = $quiz_subs;

          }
          
     
     
    return array('totalcount' => $totalusers,'data' => $quiz_subdata);
  }



    public function get_forum_details($stable,$courseid,$userid){
          global $USER,$DB,$CFG;
 
             $options = array('targetID' => 'manage_forum_details','perPage' => 5, 'cardClass' => 'w_one', 'viewType' => 'card');
        
        $options['methodName']='block_admin_forum_details';
        $options['templateName']='block_admindashboard/forumdisplay'; 
        $options = json_encode($options);

        $dataoptions = json_encode(array('userid' =>$userid,'contextid' => $systemcontext->id,'courseid' => $courseid));
        $filterdata = json_encode(array());

        $context = [
                'targetID' => 'manage_forum_details',
                'options' => $options,
                'dataoptions' => $dataoptions,
                'filterdata' => $filterdata
        ];

        if($filter){
            return  $context;
        }else{
            return  $this->render_from_template('local_rolemanagement/cardPaginate', $context);
        }
    }



     public function get_forumdetailsdata($stable,$courseid,$userid,$search = null){
          global $USER,$DB,$CFG;

          require_once($CFG->dirroot . '/blocks/facultydashboard/groupuser.php');
 
             $systemcontext = context_system::instance(); 
            $userid = $userid > 0 ? $userid : $USER->id; 
            $context = context_course::instance($courseid);
            $roles = get_user_roles($context, $userid, true);
            $role = key($roles);
            $rolename = $roles[$role]->shortname;
             
            if($rolename == 'vit_trainer')
            {

              $myforums = $DB->get_records_sql("SELECT f.id as forumid,f.name,cm.id as moduleid,c.fullname as coursename,cm.groupmode  FROM {forum} as f

             JOIN mdl_course_modules as cm ON cm.course = f.course AND f.id = cm.instance AND cm.module = (SELECT id FROM mdl_modules WHERE name = 'forum') 
             JOIN mdl_course as c ON c.id = cm.course 
             WHERE cm.course = :courseid AND f.type IN ('general', 'qanda')", ['courseid' => $courseid],$stable->start, $stable->length);

             $totalforum = $DB->count_records_sql("SELECT COUNT(DISTINCT(id)) FROM {forum} WHERE course = :courseid AND type IN ('general', 'qanda')", ['courseid' => $courseid]);
        
            $params = array('courseid' => $courseid, 'userid' => $userid, 'userid2' => $userid, 'courseid1' => $courseid, 'courseid2' => $courseid);
          }else{
             $sql = "SELECT f.name as name, f.id as forumid, cm.id as moduleid,
                  c.fullname as coursename, (select count(ra.id) from {context} as ctx
                  join {role_assignments} as ra on ra.contextid = ctx.id
                  join {role} as r on r.id = ra.roleid
                  where r.shortname = 'student' and ctx.instanceid = c.id and ctx.contextlevel = 50) as user_count,
                  (select count(distinct(fd.id)) from  {forum_discussions} as fd 
                  where fd.forum = f.id) as discussion_count 
                  from {course} as c 
                  join {forum} as f on f.course = c.id
                  join {course_modules} as cm on cm.course = c.id 
                  join {modules} as m on m.id = cm.module and m.name = 'forum'";

             $params = array('courseid'=>$courseid); 

             if($search){

               $totalforum = $DB->count_records_sql("SELECT count(a.id) from {forum} as a where a.course = :courseid and a.name like '%$search%'",array('courseid' =>$courseid));
               $sql .= " where f.type != 'news' and c.id = :courseid and cm.instance =f.id and f.name like '%$search%' GROUP BY f.id, c.id";


             }else{

               $totalforum = $DB->count_records_sql("SELECT count(a.id) from {forum} as a where a.course = :courseid",array('courseid' =>$courseid));
                $sql .= " where f.type != 'news' and c.id = :courseid and cm.instance =f.id  GROUP BY f.id, c.id";

             }

            
            
             $myforums = $DB->get_records_sql($sql,$params,$stable->start, $stable->length);
          }
             


            $forum_data = array();

            foreach ($myforums as $myforum) {

                 $forum_url = $CFG->wwwroot.'/mod/forum/view.php?id='.$myforum->moduleid;

                 $forum_details = array();               
                 $forum_details['forum_name'] = $myforum->name; 
                 $forum_details['course_name'] = $myforum->coursename; 

                 
                if($rolename == 'vit_trainer')
                {

                 $sql = groupforumdisc_activity($userid, $courseid, $myforum->forumid,$myforum->groupmode, false);
               

                if($sql != 0){

                    $params = array('courseid' => $courseid, 'userid' => $userid, 'userid2' => $userid, 'courseid1' => $courseid, 'courseid2' => $courseid, 'forumid' => $myforum->forumid);
                    $countdata = $DB->get_record_sql($sql['forumdiscsql'], $params);

                } else {

                  $countdata = array();
                }
                 $forum_details['user_count'] = $countdata->user_count;
                 $forum_details['discussion_count'] = $countdata->discussion_count;
                } else {
                  $forum_details['user_count'] = $myforum->user_count;
                 $forum_details['discussion_count'] = $myforum->discussion_count;
                }
             
                 $forum_details['forum_url'] = $forum_url;
                 $forum_details['forumid'] = $myforum->forumid;
                 $forum_details['courseid'] = $courseid;
                 $forum_details['userid'] = $userid;
                 $forum_data[] = $forum_details;

            }

       
      return array('totalcount' => $totalforum,'data' => $forum_data);
    }

     public function get_forum_enrol($stable,$courseid,$forumid,$userid ){
          global $USER,$DB,$CFG;
 
        $options = array('targetID' => 'manage_forum_enrol','perPage' => 5, 'cardClass' => 'w_one', 'viewType' => 'card');
        
        $options['methodName']='block_admin_forum_enrol';
        $options['templateName']='block_admindashboard/forumusers'; 
        $options = json_encode($options);

        $dataoptions = json_encode(array('userid' =>$userid,'contextid' => $systemcontext->id,'courseid' => $courseid,'forumid' => $forumid));
        $filterdata = json_encode(array());

        $context = [
                'targetID' => 'manage_forum_enrol',
                'options' => $options,
                'dataoptions' => $dataoptions,
                'filterdata' => $filterdata
        ];

        if($filter){
            return  $context;
        }else{
            return  $this->render_from_template('local_rolemanagement/cardPaginate', $context);
        }
    }



     public function get_forumenroldata($stable,$courseid,$forumid,$userid,$search=null ){
          global $USER,$DB,$CFG;

          require_once($CFG->dirroot . '/blocks/facultydashboard/groupuser.php');
 
            $systemcontext = context_system::instance();
            $userid = $userid > 0 ? $userid : $USER->id;
            $context = context_course::instance($courseid);
            $roles = get_user_roles($context, $userid, true);
            $role = key($roles);
            $rolename = $roles[$role]->shortname;

            if($rolename == 'vit_trainer')
          {
          
          if($search){
            $sql = groupforumusers_activity($userid, $courseid, $forumid,$search);
          }else{
             $sql = groupforumusers_activity($userid, $courseid, $forumid);
          }

          // print_r($sql); exit;


           $params = array('courseid' => $courseid, 'userid' => $userid, 'courseid1' => $courseid, 'forumid' => $forumid);

           $totalusers = $DB->count_records_sql($sql['forumusersqlcount'],$params);
          
           if($sql != 0){
              $myforumdata = $DB->get_records_sql($sql['forumusersql'], $params,$stable->start, $stable->length);
           }else{

              $myforumdata = array();
           }
         }

         else{

            $sql = "SELECT CONCAT(u.firstname,' ', u.lastname) as student_name,
                u.idnumber as student_id,
                c.fullname as course_name,
                u.id as userid,
                f.name as forum,
                CASE
                WHEN fp.modified IS NOT NULL then DATE_FORMAT(FROM_UNIXTIME (fp.modified),'%e %b %Y - %H:%i') ELSE 'NA' END AS 'submission_date',
                CASE
                WHEN fg.grade IS NOT NULL then fg.grade ELSE 'NA' END AS 'final_grade'
                from {course} as c                                 
                join {context} as ctx on ctx.instanceid = c.id 
                join {role_assignments} as ra on ra.contextid = ctx.id
                join {user} as u on u.id = ra.userid
                join {forum} as f on f.course = c.id
                left join {forum_discussions} as fd on f.id = fd.forum
                left join {forum_posts} as fp on fp.discussion = fd.id and fp.userid  = u.id
                left join {forum_grades} as fg on ra.userid = fg.userid and fg.forum = f.id";

                $params = array('forumid'=>$forumid, 'courseid'=>$courseid);

            if($search){

               $totalusers = $DB->count_records_sql("SELECT count(c.id) from {course} AS c                        
               JOIN {context} AS ctx ON ctx.instanceid = c.id AND ctx.contextlevel = 50
               JOIN {role_assignments} AS ra ON ra.contextid = ctx.id AND ra.roleid = 5
               join {user} as u on u.id = ra.userid 
               where c.id =:courseid and (u.firstname like '%$search%' or u.lastname like '%$search%' or u.email like '%$search%' or u.username like '%$search%' or u.idnumber like '%$search%') 
               ",array('courseid' =>$courseid));
               $sql .= " where  c.id = :courseid and f.id = :forumid and ctx.contextlevel = 50 and ra.roleid = 5 and f.type != 'news' and (u.firstname like '%$search%' or u.lastname like '%$search%' or u.email like '%$search%' or u.username like '%$search%' or u.idnumber like '%$search%')
                 GROUP BY f.name, u.id";

            }else{

               $totalusers = $DB->count_records_sql("SELECT count(c.id) from {course} AS c                        
               JOIN {context} AS ctx ON ctx.instanceid = c.id AND ctx.contextlevel = 50
               JOIN {role_assignments} AS ra ON ra.contextid = ctx.id AND ra.roleid = 5
               where c.id =:courseid
               ",array('courseid' =>$courseid));

               $sql .= " where  c.id = :courseid and f.id = :forumid and ctx.contextlevel = 50 and ra.roleid = 5 and f.type != 'news'
                 GROUP BY f.name, u.id";

            }

            $myforumdata = $DB->get_records_sql($sql,$params,$stable->start, $stable->length);
        }

            $forum_userdata = array();

            foreach ($myforumdata as $myforum) {

                 $forum_enrol = array();

                 $forum_enrol['student_name'] = $myforum->student_name; 
                 $forum_enrol['student_id'] = $myforum->student_id; 
                 $forum_enrol['course_name'] = $myforum->course_name;            
                 $forum_enrol['forum'] = $myforum->forum;
                 $forum_enrol['submission_date'] = $myforum->submission_date;
                 $forum_enrol['status'] = $myforum->submission_date !='NA' ? 'Submitted' : 'Not Submitted';
                 $forum_enrol['final_grade'] = $myforum->final_grade;
                 $forum_enrol['url'] = $CFG->wwwroot.'/user/profile.php?id='.$myforum->userid;
                 
                 $forum_userdata[] = $forum_enrol;

            }


           
       
       
      return array('totalcount' => $totalusers,'data' => $forum_userdata);
    }




    public function get_forum_discussions($stable,$courseid,$forumid,$userid ){
          global $USER,$DB,$CFG;
 
             $options = array('targetID' => 'manage_forum_discussions','perPage' => 5, 'cardClass' => 'w_one', 'viewType' => 'card');
        
        $options['methodName']='block_admindashboard_forum_discussions';
        $options['templateName']='block_admindashboard/forumdisc'; 
        $options = json_encode($options);

        $dataoptions = json_encode(array('userid' =>$userid,'contextid' => $systemcontext->id,'courseid' => $courseid,'forumid' => $forumid));
        $filterdata = json_encode(array());

        $context = [
                'targetID' => 'manage_forum_discussions',
                'options' => $options,
                'dataoptions' => $dataoptions,
                'filterdata' => $filterdata
        ];

        if($filter){
            return  $context;
        }else{
            return  $this->render_from_template('local_rolemanagement/cardPaginate', $context);
        }
    }


    public function get_forum_sub($stable,$courseid,$forumid,$discussionid,$userid){
          global $USER,$DB,$CFG;
 
             $options = array('targetID' => 'manage_forum_sub','perPage' => 5, 'cardClass' => 'w_one', 'viewType' => 'card');
        
        $options['methodName']='block_admin_forum_sub';
        $options['templateName']='block_admindashboard/forumgradedata'; 
        $options = json_encode($options);

        $dataoptions = json_encode(array('userid' =>$userid,'contextid' => $systemcontext->id,'courseid' => $courseid,'forumid' => $forumid, 'discussionid' => $discussionid));
        $filterdata = json_encode(array());

        $context = [
                'targetID' => 'manage_forum_sub',
                'options' => $options,
                'dataoptions' => $dataoptions,
                'filterdata' => $filterdata
        ];

        if($filter){
            return  $context;
        }else{
            return  $this->render_from_template('local_rolemanagement/cardPaginate', $context);
        }
    }


     public function get_forum_discussion($stable,$courseid,$userid,$forumid,$search = null){
          global $USER,$DB,$CFG;

           require_once($CFG->dirroot . '/blocks/facultydashboard/groupuser.php');
 
            $systemcontext = context_system::instance();
           
            $context = context_course::instance($courseid);
            $roles = get_user_roles($context, $userid, true);
            $role = key($roles);
            $rolename = $roles[$role]->shortname;

            if($rolename == 'vit_trainer'){

                if($search){
                    $sql = groupdiscsub_activity($userid, $courseid, $forumid, $search);
                }else{
                   $sql = groupdiscsub_activity($userid, $courseid, $forumid); 
                }

                $params = array('courseid' => $courseid,'userid' => $userid, 'userid1' => $userid,'userid2' => $userid,'userid3' => $userid,'courseid1' => $courseid,'courseid2' => $courseid, 'forumid' => $forumid);

                $totalforumdisc = $DB->count_records_sql($sql['discsubcount'],$params);

               if($sql != 0){
                  $forumdiscs = $DB->get_records_sql($sql['discsub'], $params,$stable->start, $stable->length);
               }else{

                  $forumdiscs = array();
               }
            }
            else 
            {
             $sql = "SELECT fd.id, c.fullname as course_name, c.id as courseid, (select count(ra.id) from mdl_context as ctx join mdl_role_assignments as ra on ra.contextid = ctx.id join mdl_role as r on r.id = ra.roleid
                 where r.shortname = 'student' and ctx.instanceid = c.id and ctx.contextlevel = 50) as user_count, f.name as forum, f.id as forumid, fd.id as discussion_id, fd.name as forum_discussion, (SELECT COUNT(DISTINCT fpp.userid) FROM mdl_forum_posts AS fpp 
                 JOIN mdl_forum_discussions AS fd_sub ON fpp.discussion = fd_sub.id 
                 JOIN mdl_context AS ctx_sub ON ctx_sub.instanceid = c.id AND ctx_sub.contextlevel = 50 
                 JOIN mdl_role_assignments AS ra_sub ON ra_sub.contextid = ctx_sub.id 
                 JOIN mdl_role AS r_sub ON ra_sub.roleid = r_sub.id 
                 WHERE r_sub.shortname = 'student' AND fd_sub.id = fd.id AND fpp.parent != 0 AND fpp.userid != $USER->id) AS submission_count  
                 FROM mdl_course as c 
                 JOIN mdl_course_modules as cm ON c.id = cm.course and cm.module = (SELECT id FROM mdl_modules WHERE name='forum')
                 JOIN mdl_forum as f on f.course = c.id
                 JOIN mdl_forum_discussions as fd on fd.forum = f.id and fd.course = c.id
                 JOIN mdl_forum_posts as fp on fp.discussion = fd.id
                 JOIN mdl_context as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50
                 left JOIN mdl_role_assignments as ra on ra.contextid = ctx.id";


             if($search){
                 $totalforumdisc = $DB->count_records_sql("SELECT count(fd.id) FROM mdl_course as c
                JOIN mdl_forum as f on f.course = c.id
                JOIN mdl_forum_discussions as fd on fd.forum = f.id and fd.course = c.id
                 WHERE f.id = :forumid AND c.id = :courseid and fd.name like '%$search%'",array('forumid' => $forumid, 'courseid' =>$courseid));
                 $sql .= " WHERE f.type != 'news' AND f.id = :forumid AND c.id = :courseid and fd.name like '%$search%' GROUP by fd.id";
             }else{
                 $totalforumdisc = $DB->count_records_sql("SELECT count(fd.id) FROM mdl_course as c
                JOIN mdl_forum as f on f.course = c.id
                JOIN mdl_forum_discussions as fd on fd.forum = f.id and fd.course = c.id
                 WHERE f.id = :forumid AND c.id = :courseid",array('forumid' => $forumid, 'courseid' =>$courseid));
                 $sql .= " WHERE f.type != 'news' AND f.id = :forumid AND c.id = :courseid GROUP by fd.id";
             }

              $params = array('forumid'=>$forumid, 'courseid'=>$courseid);

              $forumdiscs = $DB->get_records_sql($sql,$params,$stable->start, $stable->length);
         }
 
            $forum_discdata = array();

            foreach ($forumdiscs as $forumdisc) {

                 $discussion_url = $CFG->wwwroot.'/mod/forum/discuss.php?d='.$forumdisc->discussion_id;

                 $forum_disc = array();
                 $forum_disc['course_name'] = $forumdisc->course_name;
                 $forum_disc['user_count'] = $forumdisc->user_count; 
                 $forum_disc['forum'] = $forumdisc->forum; 
                 $forum_disc['forum_discussion'] = $forumdisc->forum_discussion;
                 $forum_disc['courseid'] = $forumdisc->courseid;
                 $forum_disc['forumid'] = $forumdisc->forumid;
                 $forum_disc['discussion_id'] = $forumdisc->discussion_id;
                 $forum_disc['submission_count'] = $forumdisc->submission_count;
                 $forum_disc['discussion_url'] = $discussion_url;
                 $forum_disc['userid'] = $userid;
                 $forum_discdata[] = $forum_disc;
            }
        
       
          return array('totalcount' => $totalforumdisc,'data' => $forum_discdata);
        }


     public function get_forumsubdata($stable,$courseid,$forumid,$discussionid,$userid,$search = null){
        global $USER,$DB,$CFG;
 
             $systemcontext = context_system::instance(); 

             $sql = "SELECT DISTINCT(fp.userid), 
                CONCAT(u.firstname,' ', u.lastname) as student_name,u.idnumber as student_id,
                u.id as userid, c.fullname as course_name, c.id as courseid,
                cm.id as instanceid, ctx.id as contextid, f.name as forum,
                f.id as forumid, fd.name as discussion, fd.id as discussion_id, 
                DATE_FORMAT(FROM_UNIXTIME (fp.modified),'%e %b %Y - %H:%i') as submission_date, (SELECT IF(grade IS NOT NULL, grade, 'Not Graded') FROM {forum_grades} WHERE forum = f.id AND userid = ra.userid ) as final_grade
                FROM {course} as c 
                JOIN {course_modules} as cm ON c.id = cm.course and cm.module = (SELECT id FROM {modules} WHERE name='forum')
                JOIN {forum} as f on f.course = cm.course
                JOIN {forum_discussions} as fd on fd.forum = f.id and fd.course = c.id
                JOIN {forum_posts} as fp on fp.discussion = fd.id
                JOIN {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50
                JOIN {role_assignments} as ra on ra.contextid = ctx.id and ra.roleid = 5
                JOIN {user} as u on u.id = ra.userid AND fp.userid = ra.userid";

             if($search){
                 $totalusers = $DB->count_records_sql("SELECT count(DISTINCT fp.userid) from {forum_discussions} as fd
                    join {forum_posts} as fp on fp.discussion = fd.id and fp.parent != 0
                    join {context} as ctx on ctx.instanceid = :courseid  and ctx.contextlevel = 50
                    join {role_assignments} as ra on ra.contextid = ctx.id
                    join {user} as u on u.id = ra.userid
                    where fd.id = :discussionid and ra.roleid = 5 and fd.forum =:forumid and (u.firstname like '%$search%' or u.lastname like '%$search%' or u.email like '%$search%' or u.username like '%$search%' or u.idnumber like '%$search%')
               ",array('discussionid' => $discussionid ,'forumid'=>$forumid,'courseid' =>$courseid));

                 $sql .= " WHERE ra.roleid = 5 and f.type != 'news' AND fp.parent != 0 and fd.id = :discussionid and f.id = :forumid AND f.course = :courseid and (u.firstname like '%$search%' or u.lastname like '%$search%' or u.email like '%$search%' or u.username like '%$search%' or u.idnumber like '%$search%')
                group by fp.userid ORDER BY fp.modified DESC, fp.id DESC ";

             }else{
                 $totalusers = $DB->count_records_sql("SELECT count(DISTINCT fp.userid) from {forum_discussions} as fd
                    join {forum_posts} as fp on fp.discussion = fd.id and fp.parent != 0
                    join {context} as ctx on ctx.instanceid = :courseid  and ctx.contextlevel = 50
                    join {role_assignments} as ra on ra.contextid = ctx.id
                    where fd.id = :discussionid and ra.roleid = 5 and fd.forum =:forumid
              
               ",array('discussionid' => $discussionid ,'forumid'=>$forumid,'courseid' =>$courseid));    

                 $sql .= " WHERE ra.roleid = 5 and f.type != 'news' AND fp.parent != 0 and fd.id = :discussionid and f.id = :forumid AND f.course = :courseid 
                group by fp.userid ORDER BY fp.modified DESC, fp.id DESC";
             }

            
             $params = array('discussionid' => $discussionid , 'forumid'=>$forumid, 'courseid'=>$courseid);
         $myforumsubs = $DB->get_records_sql($sql,$params,$stable->start, $stable->length);
         // print_r($myforumsubs); exit;

            $forum_subdata = array();

            foreach ($myforumsubs as $myforumsub) {

                 // $marking_grade = $CFG->wwwroot.'/mod/forum/view.php?id='.$myforumsub->instanceid;

                 $forum_sub = array();

                 $forum_sub['student_name'] = $myforumsub->student_name; 
                 $forum_sub['student_id'] = $myforumsub->student_id; 
                 $forum_sub['course_name'] = $myforumsub->course_name;          
                 $forum_sub['forum'] = $myforumsub->forum;
                 $forum_sub['submission_date'] = $myforumsub->submission_date;
                 $forum_sub['final_grade'] = $myforumsub->final_grade ? $myforumsub->final_grade : 'Not Graded';
                 $forum_sub['cmid'] = $myforumsub->instanceid;
                 $forum_sub['courseid'] = $myforumsub->courseid;
                 $forum_sub['contextid'] = $myforumsub->contextid;
                 $forum_sub['discussion'] = $myforumsub->discussion;
                 $forum_sub['url'] = $CFG->wwwroot.'/user/profile.php?id='.$myforumsub->userid;
                 
                 $forum_subdata[] = $forum_sub;

            }
       
      return array('totalcount' => $totalusers,'data' => $forum_subdata);
     }

}