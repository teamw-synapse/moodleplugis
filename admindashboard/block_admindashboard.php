<?php
use block_online_users\fetcher;
require_once($CFG->libdir . '/formslib.php');
require_once(__DIR__. '/../../config.php');
class block_admindashboard extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_admindashboard');
    }

    public function get_content() {
      global $DB, $USER,$CFG, $OUTPUT, $PAGE;     
      require_once($CFG->dirroot . '/blocks/admindashboard/classes/form/dashboard_form.php');
      require_once($CFG->dirroot . '/blocks/admindashboard/classes/form/courses_form.php');
      require_once($CFG->dirroot . '/blocks/admindashboard/classes/form/enroll_form.php');
      require_once($CFG->dirroot . '/blocks/admindashboard/classes/form/userenroll_form.php');
      require_once($CFG->dirroot . '/blocks/admindashboard/classes/form/unenroll_form.php');
      $systemcontext = $context = context_system::instance();
      if ($this->content !== null) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;

            }

          $this->content         = new stdClass();
          $this->content->items  = array();
          $this->content->icons  = array();
     
              if (! empty($this->config->text)) {
                  $this->content->text = '';
                  }
                  else{

                      $activecoursecount = $DB->count_records_sql("SELECT count(cc.id) FROM {course_categories} as cc  where cc.id in (79,81,14) and cc.visible = 1");

                       $inactivecoursecount = $DB->count_records_sql("SELECT count(cc.id) FROM {course_categories} as cc  where cc.id in (79,81,14) and cc.visible = 0");

                       $activevetcoursecount = $DB->count_records_sql("SELECT count(cc.id) FROM {course_categories} as cc  where cc.id in (30,195,240,241,242,243,48,380) and cc.visible = 1");
                        $inactivevetcoursecount = $DB->count_records_sql("SELECT count(cc.id) FROM {course_categories} as cc  where cc.id in (30,195,240,241,242,243,48,380) and cc.visible = 0");

                      $activeunicount = $DB->count_records_sql("SELECT count(c.id) FROM {course_categories} as cc join {course_categories} as cc1 on cc1.parent = cc.id join {course} as c on c.category = cc1.id where cc.parent in (79,81,14) and c.visible = 1");

                       $inactiveunicount = $DB->count_records_sql("SELECT count(c.id) FROM {course_categories} as cc join {course_categories} as cc1 on cc1.parent = cc.id join {course} as c on c.category = cc1.id where cc.parent in (79,81,14) and c.visible = 0");

                       $activevetunicount = $DB->count_records_sql("SELECT count(c.id) FROM {course_categories} as cc join {course_categories} as cc1 on cc1.parent = cc.id join {course} as c on c.category = cc1.id where cc.parent in (30,195,240,241,242,243,48,380) and c.visible = 1");
                       $inactivevetunicount = $DB->count_records_sql("SELECT count(c.id) FROM {course_categories} as cc join {course_categories} as cc1 on cc1.parent = cc.id join {course} as c on c.category = cc1.id where cc.parent in (30,195,240,241,242,243,48,380) and c.visible = 0");

                      $inactivestudentcount = $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                                  join {course_categories} as cc1 on cc1.parent = cc.id
                                  join {course} as c on c.category = cc1.id
                                  join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                                  join {role_assignments} as ra on ra.contextid = ctx.id
                                  join {role} as r on r.id = ra.roleid
                                  JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                  where cc.parent in (79,81,14) and r.shortname = 'student' and u.suspended = 1");

                      $activestudentcount = $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                                  join {course_categories} as cc1 on cc1.parent = cc.id
                                  join {course} as c on c.category = cc1.id
                                  join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                                  join {role_assignments} as ra on ra.contextid = ctx.id
                                  join {role} as r on r.id = ra.roleid
                                  JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                  where cc.parent in (79,81,14) and r.shortname = 'student' and u.suspended = 0");

                      

                      $activevetstudentcount = $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                                  join {course_categories} as cc1 on cc1.parent = cc.id
                                  join {course} as c on c.category = cc1.id
                                  join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                                  join {role_assignments} as ra on ra.contextid = ctx.id
                                  join {role} as r on r.id = ra.roleid
                                  JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                  where cc.parent in (30,195,240,241,242,243,48,380) and r.shortname = 'student' and u.suspended = 0");
                       $inactivevetstudentcount = $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                                  join {course_categories} as cc1 on cc1.parent = cc.id
                                  join {course} as c on c.category = cc1.id
                                  join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                                  join {role_assignments} as ra on ra.contextid = ctx.id
                                  join {role} as r on r.id = ra.roleid
                                  JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                  where cc.parent in (30,195,240,241,242,243,48,380) and r.shortname = 'student' and u.suspended = 1");

                       $activeteachercount = $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                                  join {course_categories} as cc1 on cc1.parent = cc.id
                                  join {course} as c on c.category = cc1.id
                                  join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                                  join {role_assignments} as ra on ra.contextid = ctx.id
                                  join {role} as r on r.id = ra.roleid
                                  JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                  where cc.parent in (79,81,14) and r.shortname = 'vit_trainer' and u.suspended = 0");

                       $inactiveteachercount = $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                                  join {course_categories} as cc1 on cc1.parent = cc.id
                                  join {course} as c on c.category = cc1.id
                                  join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                                  join {role_assignments} as ra on ra.contextid = ctx.id
                                  join {role} as r on r.id = ra.roleid
                                  JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                  where cc.parent in (79,81,14) and r.shortname = 'vit_trainer' and u.suspended = 1");

                       $activevetteachercount = $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                                  join {course_categories} as cc1 on cc1.parent = cc.id
                                  join {course} as c on c.category = cc1.id
                                  join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                                  join {role_assignments} as ra on ra.contextid = ctx.id
                                  join {role} as r on r.id = ra.roleid
                                  JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                  where cc.parent in (30,195,240,241,242,243,48,380) and r.shortname = 'vit_trainer' and u.suspended = 0");
                       $inactivevetteachercount = $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                                  join {course_categories} as cc1 on cc1.parent = cc.id
                                  join {course} as c on c.category = cc1.id
                                  join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                                  join {role_assignments} as ra on ra.contextid = ctx.id
                                  join {role} as r on r.id = ra.roleid
                                  JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                  where cc.parent in (30,195,240,241,242,243,48,380) and r.shortname = 'vit_trainer' and u.suspended = 1");
                  

                       

                       $activeuser =  $activevetteachercount +  $activeteachercount + $activevetstudentcount + $activestudentcount;
                       $inactiveuser = $inactivevetteachercount + $inactiveteachercount + $inactivevetstudentcount + $inactivestudentcount;

                       $activestudent =  $activevetstudentcount + $activestudentcount;
                       $inactivestudent = $inactivevetstudentcount + $inactivestudentcount;
                       $activeteacher = $activevetteachercount +  $activeteachercount;
                       $inactiveteacher = $inactivevetteachercount + $inactiveteachercount;


                         //onlineusers count
                         $now = time();
                         $timetoshowusers = 300; //Seconds default
                          if (isset($CFG->block_online_users_timetosee)) {
                              $timetoshowusers = $CFG->block_online_users_timetosee * 60;
                          }
                         $onlineusers = new fetcher(null, $now, $timetoshowusers, 1,
                         true, 1);
                         $usercount = $onlineusers->count_users();

                 if(is_siteadmin()){
                    $this->content->text = '<!DOCTYPE html>
                    <html lang="en">
                    <head>
                      <meta charset="UTF-8">
                      <meta name="viewport" content="width=device-width, initial-scale=1.0">
                      <title>Course Information Cards</title>
                      
                    </head>
                    <body>

                    <div class="container my-4">
                      <div class="row g-3">
                        <!-- Total Users Card -->
                        <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                          <div class="card text-center h-100" style="background: linear-gradient(to bottom, #e74c3c, #c0392b); color: #ffffff; border: none; cursor: pointer;" >
                            <div class="card-body">
                              <i class="fas fa-users fa-3x mb-3"></i>
                              <h5 class="card-title">Total Users</h5>
                              <p class="card-title">Online Users:('.$usercount.')</p>
                              <div class="row text-justify">
                                <div class="col-6 mb-2">
                                <h5 class="card-title">Active</h5>
                                  
                                </div>
                                <div class="col-6 mb-2">
                                <h5 class="card-title">InActive</h5>
                                 
                                </div>
                                <div class="col-6 mb-2">
                                  <p class="card-text small"><strong>Total:-</strong><strong>'.$activeuser.'</strong></p>
                                </div>
                                <div class="col-6 mb-2">
                                  <p class="card-text small"><strong>Total:-</strong><strong>'.$inactiveuser.'</strong></p>
                                </div>
                                <div class="col-6 mb-2">
                                  <p class="card-text small"><strong>Students:-</strong><strong>'.$activestudent.'</strong></p>
                                </div>
                                <div class="col-6 mb-2">
                                  <p class="card-text small"><strong>Students:-</strong><strong>'.$inactivestudent.'</strong></p>
                                </div>
                                <div class="col-6 mb-2">
                                  <p class="card-text small"><strong>Faculty:-</strong><strong>'.$activeteacher.'</strong></p>
                                </div>
                                <div class="col-6 mb-2">
                                  <p class="card-text small"><strong>Faculty:-</strong><strong>'.$inactiveteacher.'</strong></p>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>

                        <!-- HE Courses Card -->
                        <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                          <div class="card text-center h-100" style="background: linear-gradient(to bottom, #176ae7, #125ab2); color: #ffffff; border: none; cursor: pointer;">
                            <div class="card-body">
                              <i class="fas fa-book-open fa-3x mb-3"></i>
                              <h5 class="card-title">HE Courses</h5>
                              <div class="row text-justify">
                                <div class="col-6 mb-2">
                                  <h5 class="card-title">Active</h5>
                                </div>
                                <div class="col-6 mb-2">
                                   <h5 class="card-title">InActive</h5>
                                </div>
                                <div class="col-6 mb-2">
                                  <p class="card-text small"><strong>Courses:-</strong><strong>'.$activecoursecount.'</strong></p>
                                </div>
                                <div class="col-6 mb-2">
                                  <p class="card-text small"><strong>Courses:-</strong><strong>'.$inactivecoursecount.'</strong></p>
                                </div>
                                <div class="col-6 mb-2">
                                  <p class="card-text small"><strong>Units:-</strong><strong>'.$activeunicount.'</strong></p>
                                </div>
                                 <div class="col-6 mb-2">
                                  <p class="card-text small"><strong>Units:-</strong><strong>'.$inactiveunicount.'</strong></p>
                                </div>
                                <div class="col-6 mb-2">
                                  <p class="card-text small"><strong>Students:-</strong><strong>'.$activestudentcount.'</strong></p>
                                </div>
                                 <div class="col-6 mb-2">
                                  <p class="card-text small"><strong>Students:-</strong><strong>'.$inactivestudentcount.'</strong></p>
                                </div>
                                <div class="col-6 mb-2">
                                  <p class="card-text small"><strong>Faculty:-</strong><strong>'.$activeteachercount.'</strong></p>
                                </div>
                                <div class="col-6 mb-2">
                                  <p class="card-text small"><strong>Faculty:-</strong><strong>'.$inactiveteachercount.'</strong></p>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>

                        <!-- VET Courses Card -->
                        <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                          <div class="card text-center h-100" style="background: linear-gradient(to bottom, #2ecc71, #27ae60); color: #ffffff; border: none; cursor: pointer;">
                            <div class="card-body">
                              <i class="fas fa-graduation-cap fa-3x mb-3"></i>
                              <h5 class="card-title">VET Courses</h5>
                              <div class="row text-justify justify-content-center">
                                  <div class="col-6 justify-content-between mb-2">
                                                       <h5 class="card-title">Active</h5>
                                                    </div>
                                                    <div class="col-6  mb-2">
                                                       <h5 class="card-title">InActive</h5>
                                                    </div>
                                                    <div class="col-6  mb-2">
                                                      <p class="card-text small "><strong>Courses:-</strong><strong>'.$activevetcoursecount.'</strong></p>
                                                    </div>
                                                    <div class="col-6  mb-2">
                                                      <p class="card-text small "><strong>Courses:-</strong><strong>'.$inactivevetcoursecount.'</strong></p>
                                                    </div>
                                                    <div class="col-6  mb-2">
                                                      <p class="card-text small "><strong>Units:-</strong><strong>'.$activevetunicount.'</strong></p>
                                                    </div>
                                                    <div class="col-6  mb-2">
                                                      <p class="card-text small "><strong>Units:-</strong><strong>'.$inactivevetunicount.'</strong></p>
                                                    </div>
                                                    <div class="col-6  mb-2">
                                                      <p class="card-text small " ><strong>Students:-</strong><strong>'.$activevetstudentcount.'</strong></p>
                                                    </div>
                                                    <div class="col-6  mb-2">
                                                      <p class="card-text small "><strong>Students:-</strong><strong>'.$inactivevetstudentcount.'</strong></p>
                                                    </div>
                                                    <div class="col-6  mb-2">
                                                      <p class="card-text small "><strong>Faculty:-</strong><strong>'.$activevetteachercount.'</strong></p>
                                                    </div>
                                                    <div class="col-6  mb-2">
                                                      <p class="card-text small "><strong>Faculty:-</strong><strong>'.$inactivevetteachercount.'</strong></p>
                                                    </div>                               
                              </div>
                            </div>
                          </div>
                        </div>

                      </div>
                    </div>

                    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
                    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
                    </body>
                    </html>

                    ';
                  }else{


                    $userunittype = $DB->get_record('local_rolemanagement',array('userid'=>$USER->id,'dashboardview' => 1));
                    if($userunittype){

                        $u_unittype=explode(',', $userunittype->mastercoursetype);
                        foreach ($u_unittype as $type) {
                           
                          if(str_contains($type,'ICA70112')  &&  !has_capability('block/admindashboard:bits', $systemcontext)){
                               $bitsactivecourses = $DB->count_records_sql("SELECT count(DISTINCT(c.id)) from {role_assignments} as ra 
                                                  join {role} as r on r.id = roleid
                                                  join {context} as ctx on ctx.id = ra.contextid and ctx.contextlevel = 50
                                                  join {course} as c on c.id = ctx.instanceid
                                                  join {course_categories} as cc1 on cc1.id = c.category
                                                  join {course_categories} as cc on cc.id = cc1.parent
                                                  where ra.userid = :userid and cc.parent = 14 and c.visible = 1",array('userid' => $USER->id));

                               $bitsinactivecourses = $DB->count_records_sql("SELECT count(DISTINCT(c.id)) from {role_assignments} as ra 
                                                  join {role} as r on r.id = roleid
                                                  join {context} as ctx on ctx.id = ra.contextid and ctx.contextlevel = 50
                                                  join {course} as c on c.id = ctx.instanceid
                                                  join {course_categories} as cc1 on cc1.id = c.category
                                                  join {course_categories} as cc on cc.id = cc1.parent
                                                  where ra.userid = :userid and cc.parent = 14 and c.visible = 0",array('userid' => $USER->id));
                                   
                            $bitsactivestudentcount =  $studentcount = $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                              join {course_categories} as cc1 on cc1.parent = cc.id
                              join {course} as c on c.category = cc1.id
                              join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                              join {role_assignments} as ra on ra.contextid = ctx.id
                              join {role} as r on r.id = ra.roleid
                              JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                  where  cc.parent = 14 and  c.id in (SELECT c.id from {role_assignments} as ra 
                                join {role} as r on r.id = roleid
                                join {context} as ctx on ctx.id = ra.contextid and ctx.contextlevel = 50
                                join {course} as c on c.id = ctx.instanceid
                                                  where ra.userid = $USER->id) and r.shortname = 'student' and u.suspended = 0");
                            $bitsinactivestudentcount =  $studentcount = $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                              join {course_categories} as cc1 on cc1.parent = cc.id
                              join {course} as c on c.category = cc1.id
                              join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                              join {role_assignments} as ra on ra.contextid = ctx.id
                              join {role} as r on r.id = ra.roleid
                              JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                  where  cc.parent = 14 and  c.id in (SELECT c.id from {role_assignments} as ra 
                                join {role} as r on r.id = roleid
                                join {context} as ctx on ctx.id = ra.contextid and ctx.contextlevel = 50
                                join {course} as c on c.id = ctx.instanceid
                                                  where ra.userid = $USER->id) and r.shortname = 'student' and u.suspended = 1");

                                    $bitsactiveteachercount =  $studentcount = $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                              join {course_categories} as cc1 on cc1.parent = cc.id
                              join {course} as c on c.category = cc1.id
                              join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                              join {role_assignments} as ra on ra.contextid = ctx.id
                              join {role} as r on r.id = ra.roleid
                              JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                  where  cc.parent = 14 and c.id in (SELECT c.id from {role_assignments} as ra 
                                                  join {role} as r on r.id = roleid
                                                  join {context} as ctx on ctx.id = ra.contextid and ctx.contextlevel = 50
                                                  join {course} as c on c.id = ctx.instanceid
                                                  where ra.userid = $USER->id) and r.shortname = 'vit_trainer' and u.suspended = 0");

                                    $bitsinactiveteachercount =  $studentcount = $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                              join {course_categories} as cc1 on cc1.parent = cc.id
                              join {course} as c on c.category = cc1.id
                              join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                              join {role_assignments} as ra on ra.contextid = ctx.id
                              join {role} as r on r.id = ra.roleid
                              JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                  where  cc.parent = 14 and c.id in (SELECT c.id from {role_assignments} as ra 
                                                  join {role} as r on r.id = roleid
                                                  join {context} as ctx on ctx.id = ra.contextid and ctx.contextlevel = 50
                                                  join {course} as c on c.id = ctx.instanceid
                                                  where ra.userid = $USER->id) and r.shortname = 'vit_trainer' and u.suspended = 1");

                                     $bitsfacilitatorcount =  $studentcount = $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                              join {course_categories} as cc1 on cc1.parent = cc.id
                              join {course} as c on c.category = cc1.id
                              join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                              join {role_assignments} as ra on ra.contextid = ctx.id
                              join {role} as r on r.id = ra.roleid
                              JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                  where  cc.parent = 14 and c.id in (SELECT c.id from {role_assignments} as ra 
                                                  join {role} as r on r.id = roleid
                                                  join {context} as ctx on ctx.id = ra.contextid and ctx.contextlevel = 50
                                                  join {course} as c on c.id = ctx.instanceid
                                                  where ra.userid = $USER->id) and r.shortname = 'unit_facilitator'");

                           
                          }else{

                             if(str_contains($type,'ICA70112')){



                                  $bitsactivecourses = $DB->count_records_sql("SELECT count(DISTINCT(c.id)) from 
                                                         {course}  as c
                                                        join {course_categories} as cc1 on cc1.id = c.category
                                                        join {course_categories} as cc on cc.id = cc1.parent
                                                        where  cc.parent = 14 and c.visible = 1");
                                  $bitsinactivecourses = $DB->count_records_sql("SELECT count(DISTINCT(c.id)) from 
                                                         {course}  as c
                                                        join {course_categories} as cc1 on cc1.id = c.category
                                                        join {course_categories} as cc on cc.id = cc1.parent
                                                        where  cc.parent = 14 and c.visible = 0");

                                         
                                  $bitsactivestudentcount =  $studentcount = $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                                    join {course_categories} as cc1 on cc1.parent = cc.id
                                    join {course} as c on c.category = cc1.id
                                    join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                                    join {role_assignments} as ra on ra.contextid = ctx.id
                                    join {role} as r on r.id = ra.roleid
                                    JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                        where  cc.parent = 14  and r.shortname = 'student' and u.suspended = 0");

                                   $bitsinactivestudentcount =  $studentcount = $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                                    join {course_categories} as cc1 on cc1.parent = cc.id
                                    join {course} as c on c.category = cc1.id
                                    join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                                    join {role_assignments} as ra on ra.contextid = ctx.id
                                    join {role} as r on r.id = ra.roleid
                                    JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                        where  cc.parent = 14  and r.shortname = 'student' and u.suspended = 1");

                                          $bitsactiveteachercount =  $studentcount = $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                                    join {course_categories} as cc1 on cc1.parent = cc.id
                                    join {course} as c on c.category = cc1.id
                                    join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                                    join {role_assignments} as ra on ra.contextid = ctx.id
                                    join {role} as r on r.id = ra.roleid
                                    JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                        where  cc.parent = 14 and r.shortname = 'vit_trainer'and u.suspended = 0");

                                          $bitsinactiveteachercount =  $studentcount = $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                                    join {course_categories} as cc1 on cc1.parent = cc.id
                                    join {course} as c on c.category = cc1.id
                                    join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                                    join {role_assignments} as ra on ra.contextid = ctx.id
                                    join {role} as r on r.id = ra.roleid
                                    JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                        where  cc.parent = 14 and r.shortname = 'vit_trainer' and u.suspended = 1");

                                           $bitsfacilitatorcount =  $studentcount = $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                                    join {course_categories} as cc1 on cc1.parent = cc.id
                                    join {course} as c on c.category = cc1.id
                                    join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                                    join {role_assignments} as ra on ra.contextid = ctx.id
                                    join {role} as r on r.id = ra.roleid
                                    JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                        where  cc.parent = 14  and r.shortname = 'unit_facilitator'");

                            }

                          }

                          if(str_contains($type,'MBA9118')  &&  !has_capability('block/admindashboard:mba', $systemcontext)){

                            $mbaactivecourses = $DB->count_records_sql("SELECT count(DISTINCT(c.id)) from {role_assignments} as ra 
                                                  join {role} as r on r.id = roleid
                                                  join {context} as ctx on ctx.id = ra.contextid and ctx.contextlevel = 50
                                                  join {course} as c on c.id = ctx.instanceid
                                                  join {course_categories} as cc1 on cc1.id = c.category
                                                  join {course_categories} as cc on cc.id = cc1.parent
                                                  where ra.userid = :userid and cc.parent = 79 and c.visible = 1",array('userid' => $USER->id));
                            $mbainactivecourses = $DB->count_records_sql("SELECT count(DISTINCT(c.id)) from {role_assignments} as ra 
                                                  join {role} as r on r.id = roleid
                                                  join {context} as ctx on ctx.id = ra.contextid and ctx.contextlevel = 50
                                                  join {course} as c on c.id = ctx.instanceid
                                                  join {course_categories} as cc1 on cc1.id = c.category
                                                  join {course_categories} as cc on cc.id = cc1.parent
                                                  where ra.userid = :userid and cc.parent = 79 and c.visible = 0",array('userid' => $USER->id));

                                   
                            $mbaactivestudentcount =  $studentcount = $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                              join {course_categories} as cc1 on cc1.parent = cc.id
                              join {course} as c on c.category = cc1.id
                              join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                              join {role_assignments} as ra on ra.contextid = ctx.id
                              join {role} as r on r.id = ra.roleid
                              JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                  where  cc.parent = 79 and  c.id in (SELECT c.id from {role_assignments} as ra 
                                join {role} as r on r.id = roleid
                                join {context} as ctx on ctx.id = ra.contextid and ctx.contextlevel = 50
                                join {course} as c on c.id = ctx.instanceid
                                                  where ra.userid = $USER->id) and r.shortname = 'student' and u.suspended = 0");
                            $mbainactivestudentcount =  $studentcount = $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                              join {course_categories} as cc1 on cc1.parent = cc.id
                              join {course} as c on c.category = cc1.id
                              join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                              join {role_assignments} as ra on ra.contextid = ctx.id
                              join {role} as r on r.id = ra.roleid
                              JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                  where  cc.parent = 79 and  c.id in (SELECT c.id from {role_assignments} as ra 
                                join {role} as r on r.id = roleid
                                join {context} as ctx on ctx.id = ra.contextid and ctx.contextlevel = 50
                                join {course} as c on c.id = ctx.instanceid
                                                  where ra.userid = $USER->id) and r.shortname = 'student' and u.suspended = 1");

                                $mbaactiveteachercount =  $studentcount = $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                              join {course_categories} as cc1 on cc1.parent = cc.id
                              join {course} as c on c.category = cc1.id
                              join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                              join {role_assignments} as ra on ra.contextid = ctx.id
                              join {role} as r on r.id = ra.roleid
                              JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                  where  cc.parent = 79 and c.id in (SELECT c.id from {role_assignments} as ra 
                                                  join {role} as r on r.id = roleid
                                                  join {context} as ctx on ctx.id = ra.contextid and ctx.contextlevel = 50
                                                  join {course} as c on c.id = ctx.instanceid
                                                  where ra.userid = $USER->id) and r.shortname = 'vit_trainer' and u.suspended = 0");
                               $mbainactiveteachercount =  $studentcount = $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                              join {course_categories} as cc1 on cc1.parent = cc.id
                              join {course} as c on c.category = cc1.id
                              join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                              join {role_assignments} as ra on ra.contextid = ctx.id
                              join {role} as r on r.id = ra.roleid
                              JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                  where  cc.parent = 79 and c.id in (SELECT c.id from {role_assignments} as ra 
                                                  join {role} as r on r.id = roleid
                                                  join {context} as ctx on ctx.id = ra.contextid and ctx.contextlevel = 50
                                                  join {course} as c on c.id = ctx.instanceid
                                                  where ra.userid = $USER->id) and r.shortname = 'vit_trainer' and u.suspended = 1");

                                     $mbafacilitatorcount =  $studentcount = $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                              join {course_categories} as cc1 on cc1.parent = cc.id
                              join {course} as c on c.category = cc1.id
                              join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                              join {role_assignments} as ra on ra.contextid = ctx.id
                              join {role} as r on r.id = ra.roleid
                              JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                  where  cc.parent = 79 and c.id in (SELECT c.id from {role_assignments} as ra 
                                                  join {role} as r on r.id = roleid
                                                  join {context} as ctx on ctx.id = ra.contextid and ctx.contextlevel = 50
                                                  join {course} as c on c.id = ctx.instanceid
                                                  where ra.userid = $USER->id) and r.shortname = 'unit_facilitator'");

                                    

                          }else{

                              if(str_contains($type,'MBA9118')){



                                  $mbaactivecourses = $DB->count_records_sql("SELECT count(DISTINCT(c.id)) from 
                                                         {course}  as c
                                                        join {course_categories} as cc1 on cc1.id = c.category
                                                        join {course_categories} as cc on cc.id = cc1.parent
                                                        where  cc.parent = 79 and c.visible = 1");
                                   $mbainactivecourses = $DB->count_records_sql("SELECT count(DISTINCT(c.id)) from 
                                                         {course}  as c
                                                        join {course_categories} as cc1 on cc1.id = c.category
                                                        join {course_categories} as cc on cc.id = cc1.parent
                                                        where  cc.parent = 79 and c.visible = 0");

                                         
                                  $mbaactivestudentcount =  $studentcount = $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                                    join {course_categories} as cc1 on cc1.parent = cc.id
                                    join {course} as c on c.category = cc1.id
                                    join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                                    join {role_assignments} as ra on ra.contextid = ctx.id
                                    join {role} as r on r.id = ra.roleid
                                    JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                        where  cc.parent = 79  and r.shortname = 'student' and u.suspended = 0");
                                  $mbainactivestudentcount =  $studentcount = $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                                    join {course_categories} as cc1 on cc1.parent = cc.id
                                    join {course} as c on c.category = cc1.id
                                    join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                                    join {role_assignments} as ra on ra.contextid = ctx.id
                                    join {role} as r on r.id = ra.roleid
                                    JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                        where  cc.parent = 79  and r.shortname = 'student' and u.suspended = 1");

                                    $mbaactiveteachercount =  $studentcount = $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                                    join {course_categories} as cc1 on cc1.parent = cc.id
                                    join {course} as c on c.category = cc1.id
                                    join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                                    join {role_assignments} as ra on ra.contextid = ctx.id
                                    join {role} as r on r.id = ra.roleid
                                    JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                        where  cc.parent = 79 and r.shortname = 'vit_trainer' and u.suspended = 0");
                                    $mbainactiveteachercount =  $studentcount = $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                                    join {course_categories} as cc1 on cc1.parent = cc.id
                                    join {course} as c on c.category = cc1.id
                                    join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                                    join {role_assignments} as ra on ra.contextid = ctx.id
                                    join {role} as r on r.id = ra.roleid
                                    JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                        where  cc.parent = 79 and r.shortname = 'vit_trainer' and u.suspended = 1");

                                           $mbafacilitatorcount =  $studentcount = $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                                    join {course_categories} as cc1 on cc1.parent = cc.id
                                    join {course} as c on c.category = cc1.id
                                    join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                                    join {role_assignments} as ra on ra.contextid = ctx.id
                                    join {role} as r on r.id = ra.roleid
                                    JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                        where  cc.parent = 79  and r.shortname = 'unit_facilitator'");

                            }

                          }
                          if(str_contains($type,'MITS9118') &&  !has_capability('block/admindashboard:mits', $systemcontext)){

                           

                            $mitsactivecourses = $DB->count_records_sql("SELECT count(DISTINCT(c.id)) from {role_assignments} as ra 
                                                  join {role} as r on r.id = roleid
                                                  join {context} as ctx on ctx.id = ra.contextid and ctx.contextlevel = 50
                                                  join {course} as c on c.id = ctx.instanceid
                                                  join {course_categories} as cc1 on cc1.id = c.category
                                                  join {course_categories} as cc on cc.id = cc1.parent
                                                  where ra.userid = :userid and cc.parent = 81 and c.visible = 1",array('userid' => $USER->id));
                            $mitsinactivecourses = $DB->count_records_sql("SELECT count(DISTINCT(c.id)) from {role_assignments} as ra 
                                                  join {role} as r on r.id = roleid
                                                  join {context} as ctx on ctx.id = ra.contextid and ctx.contextlevel = 50
                                                  join {course} as c on c.id = ctx.instanceid
                                                  join {course_categories} as cc1 on cc1.id = c.category
                                                  join {course_categories} as cc on cc.id = cc1.parent
                                                  where ra.userid = :userid and cc.parent = 81 and c.visible = 0",array('userid' => $USER->id));

                                   
                            $mitsactivestudentcount =  $studentcount = $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                              join {course_categories} as cc1 on cc1.parent = cc.id
                              join {course} as c on c.category = cc1.id
                              join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                              join {role_assignments} as ra on ra.contextid = ctx.id
                              join {role} as r on r.id = ra.roleid
                              JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                  where  cc.parent = 81 and  c.id in (SELECT c.id from {role_assignments} as ra 
                                join {role} as r on r.id = roleid
                                join {context} as ctx on ctx.id = ra.contextid and ctx.contextlevel = 50
                                join {course} as c on c.id = ctx.instanceid
                                                  where ra.userid = $USER->id) and r.shortname = 'student' and u.suspended = 0");
                            $mitsinactivestudentcount =  $studentcount = $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                              join {course_categories} as cc1 on cc1.parent = cc.id
                              join {course} as c on c.category = cc1.id
                              join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                              join {role_assignments} as ra on ra.contextid = ctx.id
                              join {role} as r on r.id = ra.roleid
                              JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                  where  cc.parent = 81 and  c.id in (SELECT c.id from {role_assignments} as ra 
                                join {role} as r on r.id = roleid
                                join {context} as ctx on ctx.id = ra.contextid and ctx.contextlevel = 50
                                join {course} as c on c.id = ctx.instanceid
                                                  where ra.userid = $USER->id) and r.shortname = 'student' and u.suspended = 1");

                              $mitsactiveteachercount =  $studentcount = $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                              join {course_categories} as cc1 on cc1.parent = cc.id
                              join {course} as c on c.category = cc1.id
                              join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                              join {role_assignments} as ra on ra.contextid = ctx.id
                              join {role} as r on r.id = ra.roleid
                              JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                  where  cc.parent = 81 and c.id in (SELECT c.id from {role_assignments} as ra 
                                                  join {role} as r on r.id = roleid
                                                  join {context} as ctx on ctx.id = ra.contextid and ctx.contextlevel = 50
                                                  join {course} as c on c.id = ctx.instanceid
                                                  where ra.userid = $USER->id) and r.shortname = 'vit_trainer' and u.suspended = 0");

                             $mitsinactiveteachercount =  $studentcount = $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                              join {course_categories} as cc1 on cc1.parent = cc.id
                              join {course} as c on c.category = cc1.id
                              join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                              join {role_assignments} as ra on ra.contextid = ctx.id
                              join {role} as r on r.id = ra.roleid
                              JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                  where  cc.parent = 81 and c.id in (SELECT c.id from {role_assignments} as ra 
                                                  join {role} as r on r.id = roleid
                                                  join {context} as ctx on ctx.id = ra.contextid and ctx.contextlevel = 50
                                                  join {course} as c on c.id = ctx.instanceid
                                                  where ra.userid = $USER->id) and r.shortname = 'vit_trainer' and u.suspended = 1");

                                     $mitsfacilitatorcount =  $studentcount = $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                              join {course_categories} as cc1 on cc1.parent = cc.id
                              join {course} as c on c.category = cc1.id
                              join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                              join {role_assignments} as ra on ra.contextid = ctx.id
                              join {role} as r on r.id = ra.roleid
                              JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                  where  cc.parent = 81 and c.id in (SELECT c.id from {role_assignments} as ra 
                                                  join {role} as r on r.id = roleid
                                                  join {context} as ctx on ctx.id = ra.contextid and ctx.contextlevel = 50
                                                  join {course} as c on c.id = ctx.instanceid
                                                  where ra.userid = $USER->id) and r.shortname = 'unit_facilitator'");

                          }else{

                            if(str_contains($type,'MITS9118')){



                                  $mitsactivecourses = $DB->count_records_sql("SELECT count(DISTINCT(c.id)) from 
                                                         {course}  as c
                                                        join {course_categories} as cc1 on cc1.id = c.category
                                                        join {course_categories} as cc on cc.id = cc1.parent
                                                        where  cc.parent = 81 and c.visible = 1");

                                  $mitsinactviecourses = $DB->count_records_sql("SELECT count(DISTINCT(c.id)) from 
                                                         {course}  as c
                                                        join {course_categories} as cc1 on cc1.id = c.category
                                                        join {course_categories} as cc on cc.id = cc1.parent
                                                        where  cc.parent = 81 and c.visible = 0 ");

                                         
                                  $mitsactivestudentcount =  $studentcount = $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                                    join {course_categories} as cc1 on cc1.parent = cc.id
                                    join {course} as c on c.category = cc1.id
                                    join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                                    join {role_assignments} as ra on ra.contextid = ctx.id
                                    join {role} as r on r.id = ra.roleid
                                    JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                        where  cc.parent = 81  and r.shortname = 'student'");

                                          $mitsteachercount =  $studentcount = $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                                    join {course_categories} as cc1 on cc1.parent = cc.id
                                    join {course} as c on c.category = cc1.id
                                    join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                                    join {role_assignments} as ra on ra.contextid = ctx.id
                                    join {role} as r on r.id = ra.roleid
                                    JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                        where  cc.parent = 81 and r.shortname = 'vit_trainer' and u.suspended = 0");

                                          $mitsinactivestudentcount =  $studentcount = $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                                    join {course_categories} as cc1 on cc1.parent = cc.id
                                    join {course} as c on c.category = cc1.id
                                    join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                                    join {role_assignments} as ra on ra.contextid = ctx.id
                                    join {role} as r on r.id = ra.roleid
                                    JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                        where  cc.parent = 81  and r.shortname = 'student'");

                                          $mitsinactiveteachercount =   $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                                    join {course_categories} as cc1 on cc1.parent = cc.id
                                    join {course} as c on c.category = cc1.id
                                    join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                                    join {role_assignments} as ra on ra.contextid = ctx.id
                                    join {role} as r on r.id = ra.roleid
                                    JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                        where  cc.parent = 81 and r.shortname = 'vit_trainer' and u.suspended = 1");

                                           $mitsactiveteachercount =   $DB->count_records_sql("SELECT count(DISTINCT(ra.userid)) FROM {course_categories} cc 
                                    join {course_categories} as cc1 on cc1.parent = cc.id
                                    join {course} as c on c.category = cc1.id
                                    join {context} as ctx on ctx.instanceid = c.id and ctx.contextlevel = 50 
                                    join {role_assignments} as ra on ra.contextid = ctx.id
                                    join {role} as r on r.id = ra.roleid
                                    JOIN {user} as u ON u.id = ra.userid AND u.deleted = 0
                                        where  cc.parent = 81 and r.shortname = 'vit_trainer' and u.suspended = 1");

                                          

                            }

                          }
                        }
                        
                    

                     $this->content->text = '<!DOCTYPE html>
                    <html lang="en">
                    <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Course Information Cards</title>
                    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"> -->
                    <!-- <link href="https://cdnjs.cloudflare.com/ajax/<li></li>ibs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet"> -->
                    </head>
                    <body>
                    <div class="container my-3">
                    <div class="row g-3">
                    <!-- MBA Card -->
                    <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                    <div class="card text-center h-100" style="background: linear-gradient(to bottom, #e74c3c, #e74c3c); color: #ffffff; border: none;">
                    <div class="card-body ">
                    <i class="fas fa-user-graduate fa-3x mb-3"></i>
                    <h5 class="card-title mb-3">MBA</h5>
                    <div class="row g-0 text-start text-justify">
                    <div class="col-6"><h5 class="card-title mb-3">Active</h5>
                    </div>
                    <div class="col-6"><h5 class="card-title mb-3">InActive</h5></div>
                    <div class="col-6"><p class="card-text small"><strong>Units:</strong> <strong>'.$mbaactivecourses.'</strong></p></div>
                    <div class="col-6"><p class="card-text small"><strong>Units:</strong> <strong>'.$mbainactivecourses.'</strong></p></div>
                    <div class="col-6"><p class="card-text small"><strong>Students:</strong> <strong>'.$mbaactivestudentcount.'</strong></p></div>
                    <div class="col-6"><p class="card-text small"><strong>Students:</strong> <strong>'.$mbainactivestudentcount.'</strong></p></div>
                    <div class="col-6"><p class="card-text small"><strong>Faculty:</strong> <strong>'.$mbaactiveteachercount.'</strong></p></div>
                    <div class="col-6"><p class="card-text small"><strong>Faculty:</strong> <strong>'.$mbainactiveteachercount.'</strong></p></div>

                    </div>
                    </div>
                    </div>
                    </div>
                    <!-- BITS Card -->
                    <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                    <div class="card text-center h-100" style="background: linear-gradient(to bottom, #176ae7, #176ae7); color: #ffffff; border: none;">
                    <div class="card-body">
                    <i class="fas fa-book-open fa-3x mb-3"></i>
                    <h5 class="card-title mb-3">BITS</h5>
                    <div class="row g-0 text-justify">
                    <div class="col-6">
                    <p class="card-text"><h5 class="card-title mb-3">Active</h5></p>
                    </div>
                    <div class="col-6">
                    <p class="card-text"><h5 class="card-title mb-3">InActive</h5></p>
                    </div>
                    <div class="col-6">
                    <p class="card-text small"><strong>Units:</strong> <strong>'.$bitsactivecourses.'</strong></p>
                    </div>
                    <div class="col-6">
                    <p class="card-text small"><strong>Units:</strong> <strong>'.$bitsinactivecourses.'</strong></p>
                    </div>
                    <div class="col-6">
                    <p class="card-text small"><strong>Students:</strong> <strong>'.$bitsactivestudentcount.'</strong></p>
                    </div>
                    <div class="col-6">
                    <p class="card-text small"><strong>Students:</strong> <strong>'.$bitsinactivestudentcount.'</strong></p>
                    </div>
                    <div class="col-6">
                    <p class="card-text small"><strong>Faculty:</strong> <strong>'.$bitsactiveteachercount.'</strong></p>
                    </div>
                    <div class="col-6">
                    <p class="card-text small"><strong>Faculty:</strong> <strong>'.$bitsinactiveteachercount.'</strong></p>
                    </div>

                    </div>
                    </div>
                    </div>
                    </div>
                    <!-- MITS Card -->
                    <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                    <div class="card text-center h-100" style="background: linear-gradient(to bottom, #2ecc71, #2ecc71); color: #ffffff; border: none;">
                    <div class="card-body">
                    <i class="fas fa-graduation-cap fa-3x mb-3"></i>
                    <h5 class="card-title mb-3">MITS</h5>
                    <div class="row g-0 text-justify">
                    <div class="col-6">
                    <p class="card-text"><h5 class="card-title mb-3">Active</h5> </p>
                    </div>
                    <div class="col-6">
                    <p class="card-text"><h5 class="card-title mb-3">InActive</h5></p>
                    </div>
                    <div class="col-6"><p class="card-text small"><strong>Units:</strong> <strong>'.$mitsactivecourses.'</strong></p></div>
                    <div class="col-6"><p class="card-text small"><strong>Units:</strong> <strong>'.$mitsinactivecourses.'</strong></p></div>
                    <div class="col-6"><p class="card-text small"><strong>Students:</strong> <strong>'.$mitsactivestudentcount.'</strong></p>
                    </div>
                    <div class="col-6"><p class="card-text small"><strong>Students:</strong> <strong>'.$mitsinactivestudentcount.'</strong></p>
                    </div>
                    <div class="col-6"><p class="card-text small"><strong>Faculty:</strong> <strong>'.$mitsactiveteachercount.'</strong></p>
                    </div>
                    <div class="col-6"><p class="card-text small"><strong>Faculty:</strong> <strong>'.$mitsinactiveteachercount.'</strong></p>
                    </div>

                    </div>
                    </div>
                    </div>
                    </div>
                     
                      </div>
                    </div>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
                    </body>
                    </html>
                    ';
                  }else{
                     $this->content->text = '';
                  }
                }



                  $this->content->text .= '<ul class="nav nav-pills mb-3 nav-justified" id="pills-tab" role="tablist">';

                   if(is_siteadmin()|| has_capability('block/admindashboard:bits', $systemcontext) || has_capability('block/admindashboard:mits', $systemcontext) || has_capability('block/admindashboard:mba', $systemcontext)){
                    $this->content->text .= '<li class="nav-item" role="presentation">
                      <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#usermanagement" type="button" role="tab" aria-controls="pills-home" aria-selected="true">User Management</button>
                    </li>';
                     $this->content->text .= ' <li class="nav-item" role="presentation">
                      <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#coursemanagement" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Course Management</button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="pills-contact-tab" data-bs-toggle="pill" data-bs-target="#enrollmanagement" type="button" role="tab" aria-controls="pills-contact" aria-selected="false">Enroll Management</button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="pills-contacts-tab" data-bs-toggle="pill" data-bs-target="#unenrollmanagement" type="button" role="tab" aria-controls="pills-contacts" aria-selected="false">Unenroll Management</button>
                    </li>
                  </ul>';
                  }else{

                     $this->content->text .= ' <li class="nav-item" role="presentation">
                      <button class="nav-link active " id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#coursemanagement" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Course Management</button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="pills-contact-tab" data-bs-toggle="pill" data-bs-target="#enrollmanagement" type="button" role="tab" aria-controls="pills-contact" aria-selected="false">Enroll Management</button>
                    </li>
                    <li class="nav-item" role="presentation">
                      <button class="nav-link" id="pills-contacts-tab" data-bs-toggle="pill" data-bs-target="#unenrollmanagement" type="button" role="tab" aria-controls="pills-contacts" aria-selected="false">Unenroll Management</button>
                    </li>
                  </ul>';
                  }

                   

                  //Instantiate simplehtml_form 
                  
                 if(is_siteadmin()|| has_capability('block/admindashboard:bits', $systemcontext) || has_capability('block/admindashboard:mits', $systemcontext) || has_capability('block/admindashboard:mba', $systemcontext)){


                  $mform = new simplehtml_form(null, array());


               $this->content->text .='<div class="tab-content" id="pills-tabContent">';

                $this->content->text .=  "<div id ='usermanagement' class='collapse active show'>";    
                          $this->content->text .= $mform->render();

                  $error = "<div class='error'>";
                  $error .= "</div>";
                  $this->content->text .= $error;
               
                  $this->content->text .= $OUTPUT->render_from_template('block_admindashboard/displayuser',array());
                  $this->content->text .= '</div>'; 

                   $mform2 = new courses_form(null, array());
                  $this->content->text .=  "<div id ='coursemanagement' class='collapse'>";  
                  $this->content->text .=$mform2->render();
                  $this->content->text .= $OUTPUT->render_from_template('block_admindashboard/displaycourse',array());
                  $this->content->text .= '</div>';


                   $mform3 = new enroll_form(null, array());

                  $this->content->text .=  "<div id ='enrollmanagement' class='collapse'>";  
                  $this->content->text .=$mform3->render();
                  $mform4 = new userenroll_form(null, array());
                  $this->content->text .= $mform4->render();

                  

                  $text = "<div class='generaltable'>";
                  $text .= "<div class='enrollresultsetdata'>";
                  $text .= "</div>";
                  $text .= "</div>";
                 

                 
                 $this->content->text .= $text;
                 $this->content->text .= '</div>';


                  $this->content->text .=  "<div id ='unenrollmanagement' class='collapse'>"; 
                  $mform5 = new unenroll_form(null, array()); 
                  $this->content->text .=$mform5->render();
                  
                  $text = "<div class='generaltable'>";
                  $text .= "<div class='unenrollresultsetdata'>";
                  $text .= "</div>";
                   $text .= "</div>";

                 
                 $this->content->text .= $text;
                  
                   $this->content->text .= '</div>';  
                  


                  }else{

                     $mform2 = new courses_form(null, array());
                  $this->content->text .=  "<div id ='coursemanagement' class='collapse active show'>";  
                  $this->content->text .=$mform2->render();
                  $this->content->text .= $OUTPUT->render_from_template('block_admindashboard/displaycourse',array());
                  $this->content->text .= '</div>';


                   $mform3 = new enroll_form(null, array());

                  $this->content->text .=  "<div id ='enrollmanagement' class='collapse'>";  
                  $this->content->text .=$mform3->render();
                  $mform4 = new userenroll_form(null, array());
                  $this->content->text .= $mform4->render();

                  

                  $text = "<div class='generaltable'>";
                  $text .= "<div class='enrollresultsetdata'>";
                  $text .= "</div>";
                  $text .= "</div>";
                 

                 
                 $this->content->text .= $text;
                 $this->content->text .= '</div>';


                  $this->content->text .=  "<div id ='unenrollmanagement' class='collapse'>"; 
                  $mform5 = new unenroll_form(null, array()); 
                  $this->content->text .=$mform5->render();
                  
                  $text = "<div class='generaltable'>";
                  $text .= "<div class='unenrollresultsetdata'>";
                  $text .= "</div>";
                   $text .= "</div>";

                 
                 $this->content->text .= $text;
                  
                   $this->content->text .= '</div>';

                  }               

                return $this->content;
            }     

      public function get_required_javascript(){
        
            $this->page->requires->js('/blocks/admindashboard/js/template.js');
            $this->page->requires->js_call_amd('block_admindashboard/main','load');
            $this->page->requires->js_call_amd('block_admindashboard/mycourses','load');
            $this->page->requires->js_call_amd('block_admindashboard/getgroup','load');
            $this->page->requires->js_call_amd('block_admindashboard/getassign','load');
            $this->page->requires->js_call_amd('block_admindashboard/getquiz','load');
            $this->page->requires->js_call_amd('block_admindashboard/getuser','load');
            $this->page->requires->js_call_amd('block_admindashboard/getforum','load');
            $this->page->requires->js_call_amd('block_admindashboard/assignuser','load');
            $this->page->requires->js_call_amd('block_admindashboard/assignsub','load');
            $this->page->requires->js_call_amd('block_admindashboard/quizuser','load');
            $this->page->requires->js_call_amd('block_admindashboard/quizsub','load');
            $this->page->requires->js_call_amd('block_admindashboard/forumuser','load');
            $this->page->requires->js_call_amd('block_admindashboard/forumsub','load');
            $this->page->requires->js_call_amd('block_admindashboard/forumdisc','load');
            
      }
}
