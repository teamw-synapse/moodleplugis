<?php

//namespace local_reports\external;
defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;

class block_select_category_external extends external_api {

    public static function select_term_parameters() {
        return new external_function_parameters(
            array(
                'id' => new external_value(PARAM_RAW, ' categoryid'),
                'subid' => new external_value(PARAM_RAW, ' subcategoryid'),
                'unitid' => new external_value(PARAM_RAW, ' courseid')
            )
        );
    } 

    public static function select_term($id, $subid, $unitid) {
        global $DB,$USER;
         $systemcontext = $context = context_system::instance();
        $params = self::validate_parameters(self::select_term_parameters(),
                  [ 'id' => $id,'subid' => $subid, 'unitid' => $unitid]);
        $courselist = array();
        $catlist = array();
        $grouplist = array();
        $userslist = array();
        $rolelist = array();
        $data = array();
        if($id)
        {
            if(!is_siteadmin()){

                //course coradinatior code start 
                if(has_capability('block/admindashboard:bits', $systemcontext) || has_capability('block/admindashboard:mits', $systemcontext) || has_capability('block/admindashboard:mba', $systemcontext))
                {
                    $categories = $DB->get_records_sql("SELECT * from {course_categories} where parent = :id ",array('id' =>$params['id'] ));
                }else{

                     $categories = $DB->get_records_sql("SELECT cc.* from {role_assignments} as ra 
                                                  join {role} as r on r.id = roleid
                                                  join {context} as ctx on ctx.id = ra.contextid and ctx.contextlevel = 50
                                                  join {course} as c on c.id = ctx.instanceid
                                                  join {course_categories} as cc1 on cc1.id = c.category
                                                  join {course_categories} as cc on cc.id = cc1.parent
                                                  where ra.userid = :userid and cc.parent = :id",array('userid' => $USER->id,'id' => $params['id']));

                }
                //course coradinatior code start 
                

            }else{
                $categories = $DB->get_records_sql("SELECT * from {course_categories} where parent = :id ",array('id' =>$params['id'] ));   
            }
                     
            foreach ($categories as $category){

                $subdata = array();
                $subdata['categoryid'] = $category->id;
                $subdata['categoryname'] = $category->name;     
                $catlist[] = $subdata;
            }
        }
        $data['category'] = $catlist;

        if($subid)
        {
            if(!is_siteadmin()){
                //course coradinatior code start 
                if(has_capability('block/admindashboard:bits', $systemcontext) || has_capability('block/admindashboard:mits', $systemcontext) || has_capability('block/admindashboard:mba', $systemcontext))
                {
                    $courses  = $DB->get_records_sql("SELECT * from {course_categories} where parent = :subid ",array('subid' =>$params['subid'] ));
                }else{

                 $courses = $DB->get_records_sql("SELECT cc1.* from {role_assignments} as ra 
                                                  join {role} as r on r.id = roleid
                                                  join {context} as ctx on ctx.id = ra.contextid and ctx.contextlevel = 50
                                                  join {course} as c on c.id = ctx.instanceid
                                                  join {course_categories} as cc1 on cc1.id = c.category
                                                  where ra.userid = :userid and cc1.parent = :id",array('userid' => $USER->id,'id' => $params['subid']));
             }
             //course coradinatior code start 
            }else{            
                $courses  = $DB->get_records_sql("SELECT * from {course_categories} where parent = :subid ",array('subid' =>$params['subid'] ));
            }
            
            foreach ($courses as $course) {
                 $subdata = array();
                 $subdata['categoryid'] = $course->id;
                 $subdata['categoryname'] = $course->name;
     
                $courselist[] =$subdata;
            }
        }
        $data['ccategory'] = $courselist;

        if($unitid !=0){

            $groups  = $DB->get_records_sql("SELECT * from {groups} where courseid IN ($unitid)",array());

            
            foreach ($groups as $group) {
                 $subdata = array();
                 $subdata['groupid'] = $group->id;
                 $subdata['groupname'] = $group->name;
     
                $grouplist[] =$subdata;
            }

            $users  = $DB->get_records_sql("SELECT * from {user} as u WHERE u.id > 2 AND u.id NOT IN(
                                                SELECT ra.userid FROM {role_assignments} as ra
                                                JOIN {context} as ct ON ct.id = ra.contextid AND ct.contextlevel = 50
                                                JOIN {course} as c ON c.id = ct.instanceid
                                                     where c.id IN ($unitid) AND ra.roleid = 5) ",array());

            
            foreach ($users as $user) {
                 $subdata = array();
                 $subdata['studentid'] = $user->id;
                 $subdata['studentname'] = $user->firstname.' '.$user->lastname;
     
                $userslist[] =$subdata;
            }


            $roles  = $DB->get_records_sql("SELECT * from {role}",array());

            
            foreach ($roles as $role) {
                 $subdata = array();
                 $subdata['roleid'] = $role->id;
                 $subdata['rolename'] = $role->shortname;
     
                 $rolelist[] =$subdata;
            }

             

        }
        $data['groups'] = $grouplist;
        $data['users'] = $userslist;
        $data['roles'] = $rolelist;      


        return $data;            
    }


    public static function select_term_returns() {
       return new external_single_structure (
            array(

                'category' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'categoryid' => new external_value(PARAM_RAW, 'id of the category'),
                            'categoryname' => new external_value(PARAM_RAW, 'name of the category'),
                          )
                    )
                ),
                'ccategory' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'categoryid' => new external_value(PARAM_RAW, 'id of the course'),
                            'categoryname' => new external_value(PARAM_RAW, 'name of the course'),
                          )
                    )
                ),
                'groups' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'groupid' => new external_value(PARAM_RAW, 'id of the group'),
                            'groupname' => new external_value(PARAM_RAW, 'name of the group'),
                          )
                    )
                ),
                'users' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'studentid' => new external_value(PARAM_RAW, 'id of the student'),
                            'studentname' => new external_value(PARAM_RAW, 'name of the student'),
                          )
                    )
                ),
                'roles' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'roleid' => new external_value(PARAM_RAW, 'id of the role'),
                            'rolename' => new external_value(PARAM_RAW, 'name of the role'),
                          )
                    )
                )
            )
        );
    }


    public static function select_courses_parameters() {
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

    public static function select_courses($options,
        $dataoptions,
        $offset = 0,
        $limit = 0,
        $contextid,
        $filterdata) {
        global $DB, $CFG, $USER,$PAGE;
          require_login();
        $PAGE->set_url('/local/rolemanagement/index.php', array());
        $PAGE->set_context($contextid);
        $context = context_system::instance();
        $params = self::validate_parameters(self::select_courses_parameters(),
                  [
                'options' => $options,
                'dataoptions' => $dataoptions,
                'offset' => $offset,
                'limit' => $limit,
                'contextid' => $contextid,
                'filterdata' => $filterdata
            ]);

        $output = $PAGE->get_renderer('block_admindashboard');
        $offset = $params['offset'];
        $limit = $params['limit'];
        $decodedata = json_decode($params['dataoptions']);
        $filtervalues = json_decode($filterdata);         
           
        $stable = new \stdClass();
        $stable->thead = true;           
        $stable->thead = false;
        $stable->start = $offset;
        $stable->length = $limit;
        if($decodedata->search){
             $userslist = $output->admindashboard_courselist($stable,$decodedata->maincategoryid,$decodedata->catagory,$decodedata->subcatagory,$decodedata->unitid,$decodedata->search);

        }else{
             $userslist = $output->admindashboard_courselist($stable,$decodedata->maincategoryid,$decodedata->catagory,$decodedata->subcatagory,$decodedata->unitid);
        }
       
        $totalcount = $userslist['totalcount'];
        $data=$userslist['data'];


        return[             
            'totalcount' => $totalcount,
            'records' => $data,
            'options' => $options,
            'dataoptions' => $dataoptions,
            'filterdata' => $filterdata,
        ];

   }

public static function select_courses_returns() {

             return new external_single_structure([
                'options' => new external_value(PARAM_RAW, 'The paging data for the service'),
                'dataoptions' => new external_value(PARAM_RAW, 'The data for the service'),
                'totalcount' => new external_value(PARAM_INT, 'total number of accounts in system'),
                'filterdata' => new external_value(PARAM_RAW, 'The data for the service'),
                'records' =>  new external_multiple_structure(
                        new external_single_structure(
                             array(
                                'courseid' => new external_value(PARAM_RAW, 'id of the course'),
                                'catname' => new external_value(PARAM_RAW, 'id of the course'),
                                'url' => new external_value(PARAM_RAW, 'url of the course'),
                                'course_name' => new external_value(PARAM_RAW, 'name of the course'),
                                'studentcount' => new external_value(PARAM_RAW, 'count of the student'),
                                'trainercount' => new external_value(PARAM_RAW, 'count of the trainer'),
                                'startdate' => new external_value(PARAM_RAW, 'course start date'),
                                'enddate' => new external_value(PARAM_RAW, 'course end date'),
                                'assign_count' => new external_value(PARAM_RAW, 'count of assign'),
                                'quiz_count' => new external_value(PARAM_RAW, 'count of quiz'),
                                'forum_count' => new external_value(PARAM_RAW, 'count of forums'),
                                'userid' => new external_value(PARAM_RAW, 'userid'),
                                'coursepic' => new external_value(PARAM_RAW, 'count of quiz'),
                                'imgurlflag' => new external_value(PARAM_RAW, 'Id of the faculty userid'),
                                'my_courses' => new external_value(PARAM_RAW, 'count of forums'),
                                'my_coursesfull' => new external_value(PARAM_RAW, 'userid'),
                              )
                        )
                    )
            ]);
       
    }

    public static function user_enrollment_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_RAW, 'The id of the courseid'),
                'userid' => new external_value(PARAM_RAW, ' he id of the userid
                    '),
                'groupid' => new external_value(PARAM_RAW, ' he id of the groupid
                    '),
                'roleid' => new external_value(PARAM_RAW, ' he id of the roleid
                    '),
            )
        );
    }

    public static function user_enrollment($courseid, $userid, $groupid, $roleid) {
        global $DB, $CFG, $USER,$COURSE;
        require_once($CFG->dirroot.'/group/lib.php');
        $params = self::validate_parameters(self::user_enrollment_parameters(),
                  ['courseid' => $courseid, 'userid' => $userid, 'groupid' => $groupid, 'roleid' => $roleid]);
        $course = $DB->get_record('course', array('id'=>$courseid));
        $course_counted = $DB->count_records('course', array('id'=>$courseid));
        if($course_counted > 0 && $userid  && $roleid) 
        {
            $users_data = $DB->get_records_sql("SELECT * FROM mdl_user WHERE id IN($userid)", array());
            $roles_data = $DB->get_records_sql("SELECT * FROM mdl_role WHERE id IN($roleid)", array());
            // $role_data  = $DB->get_record('role', array('id' => $roleid));
           
            $instance = $DB->get_record('enrol',array('courseid'=>$courseid,'enrol'=>'manual'));
           
            $enrol = enrol_get_plugin('manual');
            $maindata = array();
            if($instance){
                $data = array();
                foreach($users_data as $key => $user_data){
                    $userenrol = $DB->get_record('user_enrolments',array('enrolid' =>$instance->id , 'userid' => $user_data->id));
                    $userdatalist =  array();
                    $userdatalist['studentname'] = $user_data->firstname.' '.$user_data->lastname;
                    $userdatalist['unitname'] = $course->fullname;
                    if(!$userenrol){
                        $roledatalist =  array();
                        foreach($roles_data as $key => $role_data){
                            $enrol->enrol_user($instance, $user_data->id, $role_data->id);
                            $DB->get_record('user_enrolments',array('enrolid' =>$instance->id , 'userid' => $user_data->id));
                            
                            $roledatalist[] = $role_data->shortname;

                            if (!empty($groupid)) {
                                //$group = $DB->get_record('groups', array('id' => $groupid));
                                 $groups = $DB->get_records_sql("SELECT * FROM mdl_groups WHERE id IN($groupid)", array());
                                //Add the user to the group
                                foreach($groups as $key => $group){
                                    groups_add_member($group->id, $user_data->id);
                                }
                            }
                        }
                        $userdatalist['roles'] =implode(',', $roledatalist);
                        $userdatalist['status'] ='200';
                        $userdatalist['message'] ='Student enrolled successfully';
                    }else{
                        // groups_remove_member($groupid,$userid);
                        // return array('status' => '400' , 'message' => 'Already Student enrolled');
                        $userdatalist['roles'] = ''; 
                        $userdatalist['status'] ='400';
                        $userdatalist['message'] ='Already Student enrolled';
                    }
                    $data[] = $userdatalist;
                }
                $maindata['users'] = $data;
                $maindata['methodstatus'] = 'Enrolment method enabled';
            }else{
                $maindata['users'] = array();
                $maindata['methodstatus'] = 'Enrolment method not enabled';
            } 
        }else{
            $maindata = array();
            $maindata['users'] = array();
            $maindata['methodstatus'] = 'Please select unit and user';
        }
        // print_r($maindata);exit;
        return $maindata;
    }

    public static function user_enrollment_returns() {
       return new external_single_structure (
            array(
                'users' => new external_multiple_structure(
                                    new external_single_structure(
                                        array(
                                            'studentname' => new external_value(PARAM_RAW, 'studentname'),
                                            'unitname' => new external_value(PARAM_RAW, 'unitname'),
                                            'roles' => new external_value(PARAM_RAW, 'roles'),
                                            'status' => new external_value(PARAM_RAW, 'status'),
                                            'message' => new external_value(PARAM_RAW, 'message')
                                          )
                                    )
                                
                ),
                'methodstatus' =>  new external_value(PARAM_RAW, 'status of course')
            )
        );
    }

    /**
     * Describes the parameters for submit_create_group_form webservice.
     * @return external_function_parameters
     */
    public static function submit_create_group_form_parameters() {
        return new external_function_parameters(
            array(
                'contextid' => new external_value(PARAM_INT, 'The context id for the course'),
                'jsonformdata' => new external_value(PARAM_RAW, 'The data from the create group form, encoded as a json array')
            )
        );
    }

    /**
     * Submit the create group form.
     *
     * @param int $contextid The context id for the course.
     * @param string $jsonformdata The data from the form, encoded as a json array.
     * @return int new group id.
     */
    public static function submit_create_group_form($contextid, $jsonformdata) {
        global $CFG, $USER;

        require_once($CFG->dirroot . '/group/lib.php');
        require_once($CFG->dirroot . '/blocks/admindashboard/classes/form/group_form.php');

        // We always must pass webservice params through validate_parameters.
        $params = self::validate_parameters(self::submit_create_group_form_parameters(),
                                            ['contextid' => $contextid, 'jsonformdata' => $jsonformdata]);

        $context = context::instance_by_id($params['contextid'], MUST_EXIST);

        // We always must call validate_context in a webservice.
        self::validate_context($context);
        require_capability('moodle/course:managegroups', $context);

        list($ignored, $course) = get_context_info_array($context->id);
        $serialiseddata = json_decode($params['jsonformdata']);

        $data = array();
        parse_str($serialiseddata, $data);

        $warnings = array();

        $editoroptions = [
            'maxfiles' => EDITOR_UNLIMITED_FILES,
            'maxbytes' => $course->maxbytes,
            'trust' => false,
            'context' => $context,
            'noclean' => true,
            'subdirs' => false
        ];
        $group = new stdClass();
        $group->courseid = $course->id;
        $group = file_prepare_standard_editor($group, 'description', $editoroptions, $context, 'group', 'description', null);

        // The last param is the ajax submitted data.
        $mform = new group_form(null, array('editoroptions' => $editoroptions,'courseid' => $course->id,'group' => $group), 'post', '', null, true, $data);

        $validateddata = $mform->get_data();

        if ($validateddata) {
            // Do the action.
            $groupid = groups_create_group($validateddata, $mform, $editoroptions);
        } else {
            // Generate a warning.
            throw new moodle_exception('erroreditgroup', 'group');
        }

        return $groupid;
    }

    /**
     * Returns description of method result value.
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function submit_create_group_form_returns() {
        return new external_value(PARAM_INT, 'group id');
    }
        

     public static function users_list_parameters() {
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

    public static function users_list($options,
        $dataoptions,
        $offset = 0,
        $limit = 0,
        $contextid,
        $filterdata) {
        global $DB, $CFG, $USER,$PAGE;

        require_login();
        $PAGE->set_url('/local/rolemanagement/index.php', array());
        $PAGE->set_context($contextid);
        $context = context_system::instance();
        $params = self::validate_parameters(self::users_list_parameters(),
                   [
                'options' => $options,
                'dataoptions' => $dataoptions,
                'offset' => $offset,
                'limit' => $limit,
                'contextid' => $contextid,
                'filterdata' => $filterdata
            ]);


         $output = $PAGE->get_renderer('block_admindashboard');


          $offset = $params['offset'];
            $limit = $params['limit'];
            $decodedata = json_decode($params['dataoptions']);
            $filtervalues = json_decode($filterdata);

           
           
            $stable = new \stdClass();
            $stable->thead = true;
           
            $stable->thead = false;
            $stable->start = $offset;
            $stable->length = $limit;
            $userslist = $output->admindashboard_userlist($stable,$decodedata->selected_status,$decodedata->selected_roles,$decodedata->search,$decodedata->selected_type);
            $totalcount = $userslist['totalcount'];
            $data=$userslist['data'];
            $totalactiveusers = $userslist['totalactiveusers'];
            $totalinactiveusers = $userslist['totalinactiveusers'];

            $courses = $userslist['courses'];
            $roles = $userslist['roles'];
            $status = $userslist['status'];

            
             return [
             
            'totalcount' => $totalcount,
            'totalactiveusers' => $totalactiveusers,
            'totalinactiveusers' => $totalinactiveusers,
            'courses' => $courses,
            'roles' => $roles,
            'status' => $status,
            'records' => $data,
            'options' => $options,
            'dataoptions' => $dataoptions,
            'filterdata' => $filterdata,
            ];
    }

        public static function users_list_returns() {


             return new external_single_structure([
            'options' => new external_value(PARAM_RAW, 'The paging data for the service'),
            'totalactiveusers' => new external_value(PARAM_RAW, 'The paging data for the service'),
            'totalinactiveusers' => new external_value(PARAM_RAW, 'The paging data for the service'),
            'courses' => new external_value(PARAM_RAW, 'The paging data for the service'),
            'status' => new external_value(PARAM_RAW, 'The paging data for the service'),
            'roles' => new external_value(PARAM_RAW, 'The paging data for the service'),
            'dataoptions' => new external_value(PARAM_RAW, 'The data for the service'),
            'totalcount' => new external_value(PARAM_INT, 'total number of accounts in system'),
            'filterdata' => new external_value(PARAM_RAW, 'The data for the service'),
            'records' =>  new external_multiple_structure(
                    new external_single_structure(
                         array(
                            'id' => new external_value(PARAM_RAW, 'id of the user'),
                            'idnumber' => new external_value(PARAM_RAW, 'id of the user'),
                            'url' => new external_value(PARAM_RAW, 'url of the user profile'),
                            'firstname' => new external_value(PARAM_RAW, 'name of the user'),
                            'lastname' => new external_value(PARAM_RAW, 'lastname'),
                            'email' => new external_value(PARAM_RAW, 'email'),
                            'role' => new external_value(PARAM_RAW, 'role'),
                            'suspendedstatus' => new external_value(PARAM_RAW, 'email'),
                            'suspendedmethod' => new external_value(PARAM_RAW, 'email'),
                            
                          )
                    )
                )
         ]);
        
    }



     public static function assign_details_parameters() {
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

    public static function assign_details($options,
        $dataoptions,
        $offset = 0,
        $limit = 0,
        $contextid,
        $filterdata) {

        global $DB, $CFG, $USER,$COURSE,$PAGE;

                 // require_once($CFG->dirroot . '/local/course_management/lib.php');
        require_login();
        // $PAGE->set_url('/local/learningpath/index.php', array());
        $PAGE->set_context($contextid);

        $output = $PAGE->get_renderer('block_admindashboard');

            $params = self::validate_parameters(self::assign_details_parameters(),
                                           [
                'options' => $options,
                'dataoptions' => $dataoptions,
                'offset' => $offset,
                'limit' => $limit,
                'contextid' => $contextid,
                'filterdata' => $filterdata
            ]);

             $offset = $params['offset'];
            $limit = $params['limit'];
            $decodedata = json_decode($params['dataoptions']);
            $filtervalues = json_decode($filterdata);

           
            $stable = new \stdClass();
            $stable->thead = true;
           
            $stable->thead = false;
            $stable->start = $offset;
            $stable->length = $limit;
            if($decodedata->search)
            {
                 $assignlist = $output->get_assigndetails($stable,$decodedata->courseid,$decodedata->userid,$decodedata->search);
                 
            }else{
                $assignlist = $output->get_assigndetails($stable,$decodedata->courseid,$decodedata->userid); 
                
            }
            $totalcount = $assignlist['totalcount'];
            $data=$assignlist['data'];

         

        return [
                    
                    'totalcount' => $totalcount,
                    'records' => $data,
                    'options' => $options,
                    'dataoptions' => $dataoptions,
                    'filterdata' => $filterdata,
                    ];

           
          

            
        }


    public static function assign_details_returns() {
        return new external_single_structure([
             
            'options' => new external_value(PARAM_RAW, 'The paging data for the service'),
            'dataoptions' => new external_value(PARAM_RAW, 'The data for the service'),
            'totalcount' => new external_value(PARAM_INT, 'total number of accounts in system'),
            'filterdata' => new external_value(PARAM_RAW, 'The data for the service'),
            'records' =>  new external_multiple_structure(
                    new external_single_structure(
                            array(
                    'assign_name' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'course_name' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'user_count' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'submission_count' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'assign_url' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    
                    'assignid' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'courseid' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'userid' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                   
                   
                )
                    )
                )
         ]);
    }

      public static function assign_users_parameters() {
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

    public static function assign_users($options,
        $dataoptions,
        $offset = 0,
        $limit = 0,
        $contextid,
        $filterdata) {

        global $DB, $CFG, $USER,$COURSE,$PAGE;

                 // require_once($CFG->dirroot . '/local/course_management/lib.php');
        require_login();
        // $PAGE->set_url('/local/learningpath/index.php', array());
        $PAGE->set_context($contextid);

        $output = $PAGE->get_renderer('block_admindashboard');

            $params = self::validate_parameters(self::assign_users_parameters(),
                                           [
                'options' => $options,
                'dataoptions' => $dataoptions,
                'offset' => $offset,
                'limit' => $limit,
                'contextid' => $contextid,
                'filterdata' => $filterdata
            ]);

             $offset = $params['offset'];
            $limit = $params['limit'];
            $decodedata = json_decode($params['dataoptions']);
            $filtervalues = json_decode($filterdata);


            $stable = new \stdClass();
            $stable->thead = true;
           
            $stable->thead = false;
            $stable->start = $offset;
            $stable->length = $limit;
            if($decodedata->search)
            {
                $assignlist = $output->get_assignusers($stable,$decodedata->courseid,$decodedata->userid,$decodedata->assignid,$decodedata->search);
               
            }else{
                 $assignlist = $output->get_assignusers($stable,$decodedata->courseid,$decodedata->userid,$decodedata->assignid);
               
            }
            $totalcount = $assignlist['totalcount'];
            $data=$assignlist['data'];

         

        return [
                    
                    'totalcount' => $totalcount,
                    'records' => $data,
                    'options' => $options,
                    'dataoptions' => $dataoptions,
                    'filterdata' => $filterdata,
                    ];

           
          

            
        }


    public static function assign_users_returns() {
        return new external_single_structure([
             
            'options' => new external_value(PARAM_RAW, 'The paging data for the service'),
            'dataoptions' => new external_value(PARAM_RAW, 'The data for the service'),
            'totalcount' => new external_value(PARAM_INT, 'total number of accounts in system'),
            'filterdata' => new external_value(PARAM_RAW, 'The data for the service'),
            'records' =>  new external_multiple_structure(
                    new external_single_structure(
                            array(
                    'user_name' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'user_id' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'assign_name' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'submitted_date' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'final_grade' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'url' => new external_value(PARAM_RAW, 'Id of the faculty courses'),                  
                   
                   
                )
                    )
                )
         ]);
    }


     public static function assign_subusers_parameters() {
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

    public static function assign_subusers($options,
        $dataoptions,
        $offset = 0,
        $limit = 0,
        $contextid,
        $filterdata) {

        global $DB, $CFG, $USER,$COURSE,$PAGE;

                 // require_once($CFG->dirroot . '/local/course_management/lib.php');
        require_login();
        // $PAGE->set_url('/local/learningpath/index.php', array());
        $PAGE->set_context($contextid);

        $output = $PAGE->get_renderer('block_admindashboard');

            $params = self::validate_parameters(self::assign_subusers_parameters(),
                                           [
                'options' => $options,
                'dataoptions' => $dataoptions,
                'offset' => $offset,
                'limit' => $limit,
                'contextid' => $contextid,
                'filterdata' => $filterdata
            ]);

             $offset = $params['offset'];
            $limit = $params['limit'];
            $decodedata = json_decode($params['dataoptions']);
            $filtervalues = json_decode($filterdata);


            $stable = new \stdClass();
            $stable->thead = true;
           
            $stable->thead = false;
            $stable->start = $offset;
            $stable->length = $limit;
            if($decodedata->search)
            {
                 $assignlist = $output->get_assignsubusers($stable,$decodedata->courseid,$decodedata->userid,$decodedata->assignid,$decodedata->search);
               
            }else{
                  $assignlist = $output->get_assignsubusers($stable,$decodedata->courseid,$decodedata->userid,$decodedata->assignid);
               
            };
            $totalcount = $assignlist['totalcount'];
            $data=$assignlist['data'];


        return [
                    
                    'totalcount' => $totalcount,
                    'records' => $data,
                    'options' => $options,
                    'dataoptions' => $dataoptions,
                    'filterdata' => $filterdata,
                    ];

           
          

            
        }
         public static function assign_subusers_returns() {
        return new external_single_structure([
             
            'options' => new external_value(PARAM_RAW, 'The paging data for the service'),
            'dataoptions' => new external_value(PARAM_RAW, 'The data for the service'),
            'totalcount' => new external_value(PARAM_INT, 'total number of accounts in system'),
            'filterdata' => new external_value(PARAM_RAW, 'The data for the service'),
            'records' =>  new external_multiple_structure(
                    new external_single_structure(
                            array(
                    'student_name' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'student_id' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'assignment' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'submitteddate' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'final_grade' => new external_value(PARAM_RAW, 'Id of the faculty courses'),                  
                   'marking_grade' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                   'url' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                   
                )
                    )
                )
         ]);
    }



     public static function userdetails_parameters() {
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

    public static function userdetails($options,
        $dataoptions,
        $offset = 0,
        $limit = 0,
        $contextid,
        $filterdata) {

        global $DB, $CFG, $USER,$COURSE,$PAGE;

                 // require_once($CFG->dirroot . '/local/course_management/lib.php');
        require_login();
        // $PAGE->set_url('/local/learningpath/index.php', array());
        $PAGE->set_context($contextid);

        $output = $PAGE->get_renderer('block_admindashboard');

            $params = self::validate_parameters(self::userdetails_parameters(),
                                           [
                'options' => $options,
                'dataoptions' => $dataoptions,
                'offset' => $offset,
                'limit' => $limit,
                'contextid' => $contextid,
                'filterdata' => $filterdata
            ]);

             $offset = $params['offset'];
            $limit = $params['limit'];
            $decodedata = json_decode($params['dataoptions']);
            $filtervalues = json_decode($filterdata);


            $stable = new \stdClass();
            $stable->thead = true;
           
            $stable->thead = false;
            $stable->start = $offset;
            $stable->length = $limit;
             if($decodedata->search){
                $assignlist = $output->get_assignuserdetails($stable,$decodedata->courseid,$decodedata->userid,$decodedata->search);
            }else{
                $assignlist = $output->get_assignuserdetails($stable,$decodedata->courseid,$decodedata->userid);
            }
            $totalcount = $assignlist['totalcount'];
            $data=$assignlist['data'];


        return [
                    
                    'totalcount' => $totalcount,
                    'records' => $data,
                    'options' => $options,
                    'dataoptions' => $dataoptions,
                    'filterdata' => $filterdata,
                    ];

           
          

            
        }


    public static function userdetails_returns() {
        return new external_single_structure([
             
            'options' => new external_value(PARAM_RAW, 'The paging data for the service'),
            'dataoptions' => new external_value(PARAM_RAW, 'The data for the service'),
            'totalcount' => new external_value(PARAM_INT, 'total number of accounts in system'),
            'filterdata' => new external_value(PARAM_RAW, 'The data for the service'),
            'records' =>  new external_multiple_structure(
                    new external_single_structure(
                            array(
                    'id_number' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'user_name' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'user_email' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'userid' => new external_value(PARAM_RAW, 'Id of the user'),
                    'url' => new external_value(PARAM_RAW, 'url of the user'),
                    'course_status' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    
                   
                )
                    )
                )
         ]);
    }


     public static function quiz_details_parameters() {
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

    public static function quiz_details($options,
        $dataoptions,
        $offset = 0,
        $limit = 0,
        $contextid,
        $filterdata) {

        global $DB, $CFG, $USER,$COURSE,$PAGE;
        require_login();
        
        $PAGE->set_context($contextid);

        $output = $PAGE->get_renderer('block_admindashboard');

            $params = self::validate_parameters(self::quiz_details_parameters(),
                                           [
                'options' => $options,
                'dataoptions' => $dataoptions,
                'offset' => $offset,
                'limit' => $limit,
                'contextid' => $contextid,
                'filterdata' => $filterdata
            ]);

             $offset = $params['offset'];
            $limit = $params['limit'];
            $decodedata = json_decode($params['dataoptions']);
            $filtervalues = json_decode($filterdata);


            $stable = new \stdClass();
            $stable->thead = true;
           
            $stable->thead = false;
            $stable->start = $offset;
            $stable->length = $limit;
            if($decodedata->search)
            {
                $assignlist = $output->get_quizdetailsdata($stable,$decodedata->courseid,$decodedata->userid,$decodedata->search);
            }else{
               $assignlist = $output->get_quizdetailsdata($stable,$decodedata->courseid,$decodedata->userid); 
            }
            $totalcount = $assignlist['totalcount'];
            $data=$assignlist['data'];


        return [
                    
                    'totalcount' => $totalcount,
                    'records' => $data,
                    'options' => $options,
                    'dataoptions' => $dataoptions,
                    'filterdata' => $filterdata,
                    ];

           
          

            
        }


    public static function quiz_details_returns() {
        return new external_single_structure([
             
            'options' => new external_value(PARAM_RAW, 'The paging data for the service'),
            'dataoptions' => new external_value(PARAM_RAW, 'The data for the service'),
            'totalcount' => new external_value(PARAM_INT, 'total number of accounts in system'),
            'filterdata' => new external_value(PARAM_RAW, 'The data for the service'),
            'records' =>  new external_multiple_structure(
                    new external_single_structure(
                            array(
                    'quiz_name' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'course_name' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'user_count' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'submission_count' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'quiz_url' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'quizid' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'courseid' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'userid' => new external_value(PARAM_RAW, 'Id of the faculty courses'),                    
                   
                )
                    )
                )
         ]);
    }


    public static function quiz_enrol_parameters() {
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

    public static function quiz_enrol($options,
        $dataoptions,
        $offset = 0,
        $limit = 0,
        $contextid,
        $filterdata) {

        global $DB, $CFG, $USER,$COURSE,$PAGE;

        require_login();
        
        $PAGE->set_context($contextid);

        $output = $PAGE->get_renderer('block_admindashboard');

            $params = self::validate_parameters(self::quiz_enrol_parameters(),
                                           [
                'options' => $options,
                'dataoptions' => $dataoptions,
                'offset' => $offset,
                'limit' => $limit,
                'contextid' => $contextid,
                'filterdata' => $filterdata
            ]);

             $offset = $params['offset'];
            $limit = $params['limit'];
            $decodedata = json_decode($params['dataoptions']);
            $filtervalues = json_decode($filterdata);


            $stable = new \stdClass();
            $stable->thead = true;
           
            $stable->thead = false;
            $stable->start = $offset;
            $stable->length = $limit;
            if($decodedata->search){
                $assignlist = $output->get_quiz_enroldata($stable,$decodedata->courseid,$decodedata->userid,$decodedata->quizid,$decodedata->search);
            }else{
                $assignlist = $output->get_quiz_enroldata($stable,$decodedata->courseid,$decodedata->userid,$decodedata->quizid);
            }
            $totalcount = $assignlist['totalcount'];
            $data=$assignlist['data'];


        return [
                    
                    'totalcount' => $totalcount,
                    'records' => $data,
                    'options' => $options,
                    'dataoptions' => $dataoptions,
                    'filterdata' => $filterdata,
                    ];

           
          

            
        }


    public static function quiz_enrol_returns() {
        return new external_single_structure([
             
            'options' => new external_value(PARAM_RAW, 'The paging data for the service'),
            'dataoptions' => new external_value(PARAM_RAW, 'The data for the service'),
            'totalcount' => new external_value(PARAM_INT, 'total number of accounts in system'),
            'filterdata' => new external_value(PARAM_RAW, 'The data for the service'),
            'records' =>  new external_multiple_structure(
                    new external_single_structure(
                            array(
                    'student_name' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'student_id' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'course_name' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'quiz_name' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'submitted_date' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'final_grade' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'status' => new external_value(PARAM_RAW, 'status'),
                    'url' => new external_value(PARAM_RAW, 'status'),
                )
                    )
                )
         ]);
    }





        public static function quiz_subusers_parameters() {
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

    public static function quiz_subusers($options,
        $dataoptions,
        $offset = 0,
        $limit = 0,
        $contextid,
        $filterdata) {

        global $DB, $CFG, $USER,$COURSE,$PAGE;

                
        require_login();
        
        $PAGE->set_context($contextid);

        $output = $PAGE->get_renderer('block_admindashboard');

            $params = self::validate_parameters(self::quiz_subusers_parameters(),
                                           [
                'options' => $options,
                'dataoptions' => $dataoptions,
                'offset' => $offset,
                'limit' => $limit,
                'contextid' => $contextid,
                'filterdata' => $filterdata
            ]);

             $offset = $params['offset'];
            $limit = $params['limit'];
            $decodedata = json_decode($params['dataoptions']);
            $filtervalues = json_decode($filterdata);


            $stable = new \stdClass();
            $stable->thead = true;
           
            $stable->thead = false;
            $stable->start = $offset;
            $stable->length = $limit;
            if($decodedata->search){
                 $assignlist = $output->get_quiz_subusersdata($stable,$decodedata->courseid,$decodedata->userid,$decodedata->quizid,$decodedata->search);
            }else{
                 $assignlist = $output->get_quiz_subusersdata($stable,$decodedata->courseid,$decodedata->userid,$decodedata->quizid);
            }
            $totalcount = $assignlist['totalcount'];
            $data=$assignlist['data'];


        return [
                    
                    'totalcount' => $totalcount,
                    'records' => $data,
                    'options' => $options,
                    'dataoptions' => $dataoptions,
                    'filterdata' => $filterdata,
                    ];

           
          

            
        }


    public static function quiz_subusers_returns() {
        return new external_single_structure([
             
            'options' => new external_value(PARAM_RAW, 'The paging data for the service'),
            'dataoptions' => new external_value(PARAM_RAW, 'The data for the service'),
            'totalcount' => new external_value(PARAM_INT, 'total number of accounts in system'),
            'filterdata' => new external_value(PARAM_RAW, 'The data for the service'),
            'records' =>  new external_multiple_structure(
                    new external_single_structure(
                            array(
                    'student_name' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'student_id' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'course_name' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'quiz_name' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'submitted_date' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'final_grade' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'marking_grade' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'url' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                                      
                   
                )
                    )
                )
         ]);
    }


     public static function forum_details_parameters() {
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

    public static function forum_details($options,
        $dataoptions,
        $offset = 0,
        $limit = 0,
        $contextid,
        $filterdata) {

        global $DB, $CFG, $USER,$COURSE,$PAGE;

        require_login();
        
        $PAGE->set_context($contextid);

        $output = $PAGE->get_renderer('block_admindashboard');

            $params = self::validate_parameters(self::forum_details_parameters(),
                                           [
                'options' => $options,
                'dataoptions' => $dataoptions,
                'offset' => $offset,
                'limit' => $limit,
                'contextid' => $contextid,
                'filterdata' => $filterdata
            ]);

             $offset = $params['offset'];
            $limit = $params['limit'];
            $decodedata = json_decode($params['dataoptions']);
            $filtervalues = json_decode($filterdata);


            $stable = new \stdClass();
            $stable->thead = true;
           
            $stable->thead = false;
            $stable->start = $offset;
            $stable->length = $limit;
            if($decodedata->search){
                $assignlist = $output->get_forumdetailsdata($stable,$decodedata->courseid,$decodedata->userid,$decodedata->search);
            }else{
              $assignlist = $output->get_forumdetailsdata($stable,$decodedata->courseid,$decodedata->userid);  
            }
            $totalcount = $assignlist['totalcount'];
            $data=$assignlist['data'];


        return [
                    
                    'totalcount' => $totalcount,
                    'records' => $data,
                    'options' => $options,
                    'dataoptions' => $dataoptions,
                    'filterdata' => $filterdata,
                    ];

           
          

            
        }


    public static function forum_details_returns() {
        return new external_single_structure([
             
            'options' => new external_value(PARAM_RAW, 'The paging data for the service'),
            'dataoptions' => new external_value(PARAM_RAW, 'The data for the service'),
            'totalcount' => new external_value(PARAM_INT, 'total number of accounts in system'),
            'filterdata' => new external_value(PARAM_RAW, 'The data for the service'),
            'records' =>  new external_multiple_structure(
                    new external_single_structure(
                            array(
                    'forum_name' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'course_name' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'user_count' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'discussion_count' => new external_value(PARAM_RAW, 'discussion_count of the faculty courses'),
                    'forum_url' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'forumid' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'courseid' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                     'userid' => new external_value(PARAM_RAW, 'Id of the faculty courses'),                    
                   
                )
                    )
                )
         ]);
    }



     public static function forum_enrol_parameters() {
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

    public static function forum_enrol($options,
        $dataoptions,
        $offset = 0,
        $limit = 0,
        $contextid,
        $filterdata) {

        global $DB, $CFG, $USER,$COURSE,$PAGE;

        require_login();
        
        $PAGE->set_context($contextid);

        $output = $PAGE->get_renderer('block_admindashboard');

            $params = self::validate_parameters(self::forum_enrol_parameters(),
                                           [
                'options' => $options,
                'dataoptions' => $dataoptions,
                'offset' => $offset,
                'limit' => $limit,
                'contextid' => $contextid,
                'filterdata' => $filterdata
            ]);

             $offset = $params['offset'];
            $limit = $params['limit'];
            $decodedata = json_decode($params['dataoptions']);
            $filtervalues = json_decode($filterdata);


            $stable = new \stdClass();
            $stable->thead = true;
           
            $stable->thead = false;
            $stable->start = $offset;
            $stable->length = $limit;
            if($decodedata->search){
                $assignlist = $output->get_forumenroldata($stable,$decodedata->courseid,$decodedata->forumid,$decodedata->userid,$decodedata->search);
            }else{
               $assignlist = $output->get_forumenroldata($stable,$decodedata->courseid,$decodedata->forumid,$decodedata->userid); 
            }
            $totalcount = $assignlist['totalcount'];
            $data=$assignlist['data'];


        return [
                    
                    'totalcount' => $totalcount,
                    'records' => $data,
                    'options' => $options,
                    'dataoptions' => $dataoptions,
                    'filterdata' => $filterdata,
                    ];

           
          

            
        }


   public static function forum_enrol_returns() {
        return new external_single_structure([
             
            'options' => new external_value(PARAM_RAW, 'The paging data for the service'),
            'dataoptions' => new external_value(PARAM_RAW, 'The data for the service'),
            'totalcount' => new external_value(PARAM_INT, 'total number of accounts in system'),
            'filterdata' => new external_value(PARAM_RAW, 'The data for the service'),
            'records' =>  new external_multiple_structure(
                    new external_single_structure(
                            array(
                    'student_name' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'student_id' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'course_name' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'forum' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'submission_date' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'status' => new external_value(PARAM_RAW, 'status'),
                    'url' => new external_value(PARAM_RAW, 'status'),
                    'final_grade' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                                      
                   
                )
                    )
                )
         ]);
    }




     public static function forum_sub_parameters() {
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

    public static function forum_sub($options,
        $dataoptions,
        $offset = 0,
        $limit = 0,
        $contextid,
        $filterdata) {

        global $DB, $CFG, $USER,$COURSE,$PAGE;

        require_login();
        
        $PAGE->set_context($contextid);

        $output = $PAGE->get_renderer('block_admindashboard');

            $params = self::validate_parameters(self::forum_sub_parameters(),
                                           [
                'options' => $options,
                'dataoptions' => $dataoptions,
                'offset' => $offset,
                'limit' => $limit,
                'contextid' => $contextid,
                'filterdata' => $filterdata
            ]);

             $offset = $params['offset'];
            $limit = $params['limit'];
            $decodedata = json_decode($params['dataoptions']);
            $filtervalues = json_decode($filterdata);


            $stable = new \stdClass();
            $stable->thead = true;
           
            $stable->thead = false;
            $stable->start = $offset;
            $stable->length = $limit;
            // print_r($decodedata); exit;
           if($decodedata->search){
                $assignlist = $output->get_forumsubdata($stable,$decodedata->courseid,$decodedata->forumid, $decodedata->discussionid,$decodedata->userid,$decodedata->search);
            }else{

                $assignlist = $output->get_forumsubdata($stable,$decodedata->courseid,$decodedata->forumid, $decodedata->discussionid,$decodedata->userid);
            }
            $totalcount = $assignlist['totalcount'];
            $data=$assignlist['data'];


        return [
                    
                    'totalcount' => $totalcount,
                    'records' => $data,
                    'options' => $options,
                    'dataoptions' => $dataoptions,
                    'filterdata' => $filterdata,
                    ];

            
        }


    public static function forum_sub_returns() {
        return new external_single_structure([
             
            'options' => new external_value(PARAM_RAW, 'The paging data for the service'),
            'dataoptions' => new external_value(PARAM_RAW, 'The data for the service'),
            'totalcount' => new external_value(PARAM_INT, 'total number of accounts in system'),
            'filterdata' => new external_value(PARAM_RAW, 'The data for the service'),
            'records' =>  new external_multiple_structure(
                    new external_single_structure(
                            array(
                    'student_name' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'student_id' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'course_name' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'forum' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'submission_date' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'final_grade' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'cmid' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'courseid' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'contextid' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'discussion' => new external_value(PARAM_RAW, 'discussion of the faculty courses'),
                    'url' => new external_value(PARAM_RAW, 'discussion of the faculty courses'),
                                      
                   
                )
                    )
                )
         ]);
    }



    public static function suspenduser_parameters() {
        return new external_function_parameters(
            array(
                
                'id' => new external_value(PARAM_RAW, ' contextid
                    '),
                'contextid' => new external_value(PARAM_RAW, ' contextid
                    '),
                'type' => new external_value(PARAM_RAW, ' contextid
                    ')
            )
        );
    }


     public static function suspenduser($id,$contextid,$type) {
                global $DB, $CFG, $USER,$COURSE;

            $params = self::validate_parameters(self::suspenduser_parameters(),
                                            ['id' => $id,'contextid' => $contextid,'type' => $type]);


           
                                  $cat1 = new stdClass();
                                  $cat1->id                = $params['id'];
                                  $cat1->suspended           = $type == 'suspend' ? 1 : 0;            
                                  $cat1->timemodified      = time();
                                  $catid = $DB->update_record('user', $cat1);

                                  return $params['id'];

    }





    public static function suspenduser_returns() {
        return new external_value(PARAM_INT, 'id');
    }


    public static function forum_discussions_parameters() {
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

    public static function forum_discussions($options,
        $dataoptions,
        $offset = 0,
        $limit = 0,
        $contextid,
        $filterdata) {

        global $DB, $CFG, $USER,$COURSE,$PAGE;

        require_login();
        
        $PAGE->set_context($contextid);

        $output = $PAGE->get_renderer('block_admindashboard');

            $params = self::validate_parameters(self::forum_discussions_parameters(),
                                           [
                'options' => $options,
                'dataoptions' => $dataoptions,
                'offset' => $offset,
                'limit' => $limit,
                'contextid' => $contextid,
                'filterdata' => $filterdata
            ]);

             $offset = $params['offset'];
            $limit = $params['limit'];
            $decodedata = json_decode($params['dataoptions']);
            $filtervalues = json_decode($filterdata);


            $stable = new \stdClass();
            $stable->thead = true;
           
            $stable->thead = false;
            $stable->start = $offset;
            $stable->length = $limit;
            if($decodedata->search){
                $assignlist = $output->get_forum_discussion($stable,$decodedata->courseid,$decodedata->userid,$decodedata->forumid,$decodedata->search);
            }else{

               $assignlist = $output->get_forum_discussion($stable,$decodedata->courseid,$decodedata->userid,$decodedata->forumid); 
            }
            $totalcount = $assignlist['totalcount'];
            $data=$assignlist['data'];


        return [
                    
                    'totalcount' => $totalcount,
                    'records' => $data,
                    'options' => $options,
                    'dataoptions' => $dataoptions,
                    'filterdata' => $filterdata,
                    ];

           
          

            
        }


    public static function forum_discussions_returns() {
        return new external_single_structure([
             
            'options' => new external_value(PARAM_RAW, 'The paging data for the service'),
            'dataoptions' => new external_value(PARAM_RAW, 'The data for the service'),
            'totalcount' => new external_value(PARAM_INT, 'total number of accounts in system'),
            'filterdata' => new external_value(PARAM_RAW, 'The data for the service'),
            'records' =>  new external_multiple_structure(
                    new external_single_structure(
                            array(
                    'course_name' => new external_value(PARAM_RAW, 'Id of the faculty courses'),
                    'user_count' => new external_value(PARAM_RAW, 'user_count of the faculty courses'),                   
                    'forum' => new external_value(PARAM_RAW, 'forum of the faculty courses'),
                    'forum_discussion' => new external_value(PARAM_RAW, 'forum_discussion of the faculty courses'),
                    'courseid' => new external_value(PARAM_RAW, 'courseid of the faculty courses'),
                    'forumid' => new external_value(PARAM_RAW, 'forumid of the faculty courses'),
                    'discussion_id' => new external_value(PARAM_RAW, 'discussion_id of the faculty courses'),
                    'submission_count' => new external_value(PARAM_RAW, 'submission_count of the faculty courses'),
                    'discussion_url' => new external_value(PARAM_RAW, 'discussion_url of the faculty courses'),
                    'userid' => new external_value(PARAM_RAW, 'discussion_url of the faculty courses'),
                                    
                   
                )
                    )
                )
         ]);
    }


    public static function loadcourses_parameters() {
        return new external_function_parameters(
            array(
                
                'search' => new external_value(PARAM_RAW, ' contextid
                    ')
               
            )
        );
    }


     public static function loadcourses($search) {
           global $DB, $CFG, $USER,$COURSE;

            $params = self::validate_parameters(self::loadcourses_parameters(),
                                            ['search' => $search]);
            $systemcontext = $context = context_system::instance();
            if(is_siteadmin())
            {
               $courses =  $DB->get_records_sql("SELECT * from {course} where fullname like '%$search%' or shortname like '%$search%'");  
           }elseif(!has_capability('block/admindashboard:mba', $systemcontext) && !has_capability('block/admindashboard:mits', $systemcontext) && !has_capability('block/admindashboard:bits', $systemcontext)){

                $courses =  $DB->get_records_sql("SELECT c.* from {user_enrolments} as ue 
                join {enrol}  as e on e.id = ue.enrolid
                join {course} as c on c.id = e.courseid 
                where (c.fullname like '%$search%' or c.shortname like '%$search%') and ue.userid =:userid",array('userid' => $USER->id)); 

           }else{

               $DB->get_record('local_rolemanagement',array('userid'=>$USER->id,'dashboardview' => 1));

               $u_unittype=explode(',', $userunittype->mastercoursetype);

               $courses = $DB->get_records_sql("SELECT c.* from 
                                                         {course}  as c
                                                        join {course_categories} as cc1 on cc1.id = c.category
                                                        join {course_categories} as cc on cc.id = cc1.parent
                                                        where  cc.parent = 14");
           }
           
            $data = [];
            foreach ($courses as $course) {
                $subdata = [];
                $subdata['courseid'] = $course->id;
                $subdata['coursename'] = $course->shortname;
                 $data[] = $subdata;
            }

            $return = [];
            $return['courses'] = $data;

           return $return;



}





    public static function loadcourses_returns() {
        return new external_single_structure (
            array(
                'courses' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'courseid' => new external_value(PARAM_RAW, 'id of the course'),
                            'coursename' => new external_value(PARAM_RAW, 'name of the course'),
                          )
                    )
                )
            )
        );
    }

 public static function loadusers_parameters() {
        return new external_function_parameters(
            array(
                
                'search' => new external_value(PARAM_RAW, ' contextid
                    ')
               
            )
        );
    }


     public static function loadusers($search) {
                global $DB, $CFG, $USER,$COURSE;

            $params = self::validate_parameters(self::loadusers_parameters(),
                                            ['search' => $search]);

            $users =  $DB->get_records_sql("SELECT * from {user} where (idnumber like '%$search%' or email like '%$search%' or firstname like '%$search%' or lastname like '%$search%' or username like '%$search%') AND deleted = 0 AND suspended = 0 ");
            $data = [];
            foreach ($users as $user) {
                 $subdata = array();
                 $subdata['studentid'] = $user->id;
                 $subdata['studentname'] = $user->firstname.' '.$user->lastname;
                 $subdata['studentidnumber'] = $user->idnumber;
     
                $data[] =$subdata;
            }


            $return = [];
            $return['users'] = $data;

           return $return;


}





    public static function loadusers_returns() {
        return new external_single_structure (
            array(
               'users' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'studentid' => new external_value(PARAM_RAW, 'id of the student'),
                            'studentname' => new external_value(PARAM_RAW, 'name of the student'),
                            'studentidnumber' => new external_value(PARAM_RAW, 'name of the student'),
                          )
                    )
                ),
            )
        );
    }


public static function loadfaculty_parameters() {
        return new external_function_parameters(
            array(
                
                'search' => new external_value(PARAM_RAW, ' contextid
                    ')
               
            )
        );
    }


     public static function loadfaculty($search) {
                global $DB, $CFG, $USER,$COURSE;

            $params = self::validate_parameters(self::loadfaculty_parameters(),
                                            ['search' => $search]);

            $users =  $DB->get_records_sql("SELECT u.id, u.firstname, u.* from {user} as u
                INNER JOIN {role_assignments} ra ON ra.userid = u.id
                INNER JOIN {context} ct ON ct.id = ra.contextid
                INNER JOIN {course} c ON c.id = ct.instanceid
                INNER JOIN {role} r ON r.id = ra.roleid
                where ra.roleid in (3,4,9,20,17) AND c.visible = 1 AND ct.contextlevel = 50  AND (u.email like '%$search%' or u.firstname like '%$search%' or u.lastname like '%$search%' or u.username like '%$search%')");
            $data = [];
            foreach ($users as $user) {
                 $subdata = array();
                 $subdata['facultyid'] = $user->id;
                 $subdata['facultyname'] = $user->firstname.' '.$user->lastname;
     
                $data[] =$subdata;
            }

            $return = [];
            $return['users'] = $data;

           return $return;


}





    public static function loadfaculty_returns() {
        return new external_single_structure (
            array(
               'users' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'facultyid' => new external_value(PARAM_RAW, 'id of the faculty'),
                            'facultyname' => new external_value(PARAM_RAW, 'name of the faculty'),
                          )
                    )
                ),
            )
        );
    }

 public static function get_courseenrolledusers_parameters() {
        return new external_function_parameters(
            array(
                
                'courseid' => new external_value(PARAM_RAW, ' contextid
                    ')
               
            )
        );
    }


     public static function get_courseenrolledusers($courseid) {
                global $DB, $CFG, $USER,$COURSE;

            $params = self::validate_parameters(self::get_courseenrolledusers_parameters(),
                                            ['courseid' => $courseid]);

             $users =  $DB->get_records_sql("SELECT u.id,u.firstname,u.lastname,u.email from {course} as c 
                                                join {enrol}  as e on e.courseid = c.id 
                                                join {user_enrolments}  as ue on ue.enrolid = e.id 
                                                join {user} as u on  u.id = ue.userid
                                                where c.id =:courseid ",array('courseid' => $params['courseid']));
           
            $data = [];
            foreach ($users as $user) {
                 $subdata = array();
                 $subdata['userid'] = $user->id;
                 $subdata['username'] = $user->firstname.' '.$user->lastname;
                 $subdata['useremail'] = $user->email;
     
                $data[] =$subdata;
            }


            $return = [];
            $return['users'] = $data;

           return $return;


}





    public static function get_courseenrolledusers_returns() {
        return new external_single_structure (
            array(
               'users' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'userid' => new external_value(PARAM_RAW, 'id of the student'),
                            'username' => new external_value(PARAM_RAW, 'name of the student'),
                            'useremail' => new external_value(PARAM_RAW, 'name of the student'),
                          )
                    )
                ),
            )
        );
    }



    public static function adminuserenrollment_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_RAW, 'The userid to be enrolled'),
                'courseid' => new external_value(PARAM_RAW, ' courseshortname
                    ')
            )
        );
    }



public static function adminuserenrollment($userid, $courseid) 
   {
        global $DB, $CFG, $USER,$COURSE;
        $params = self::validate_parameters(self::adminuserenrollment_parameters(),
                  ['userid' => $userid, 'courseid' => $courseid]);

        $course = $DB->get_record('course', array('id'=>$params['courseid']));
        // $course_counted = $DB->count_records('course', array('shortname'=>$params['courseshortname']));
         $user_data = $DB->get_record('user', array('id'=>$params['userid']));
      
            
                   
                    $instance = $DB->get_record('enrol',array('courseid'=>$course->id,'enrol'=>'manual'));
                    $userenrol = $DB->get_record('user_enrolments',array('enrolid' =>$instance->id , 'userid' => $user_data->id));

                    if($userenrol)
                    {
                    $enrol =   enrol_get_plugin('manual');
                    $enrol->unenrol_user($instance, $user_data->id);
                    $enrolstat = $DB->get_record('user_enrolments',array('enrolid' =>$instance->id , 'userid' => $user_data->id));
                    if(!$enrolstat)
                    {
                       $result = array('status' => '200' , 'username' => $user_data->firstname.' '.$user_data->lastname, 'unitname' =>  $course->shortname );
                        
                    }

                    
                    }
                    else
                    {
                        $result = array('status' => '400' , 'username' => $user_data->firstname.' '.$user_data->lastname, 'unitname' =>  $course->shortname);
                       
                    }

        
       

       

         return $result; 
    }


public static function adminuserenrollment_returns() {
        return new external_single_structure(
                array(
                    'status' => new external_value(PARAM_RAW, 'status: true if success'),
                     'username' => new external_value(PARAM_RAW, 'message return'),
                     'unitname' => new external_value(PARAM_RAW, 'message return'),
                )
            );
    }




}