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
 * @package  block_admindashboard
 * @copyright 
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function block_admindashboard_output_fragment_create_groups($args){
     global $DB, $USER, $CFG, $OUTPUT, $PAGE;

       require_once($CFG->libdir . '/formslib.php');
       require_once($CFG->dirroot.'/blocks/admindashboard/classes/form/group_form.php');
        //print_r($args);exit;
       $args = (object) $args;
    $context = $args->context;

    $formdata = [];
    if (!empty($args->jsonformdata) && $args->jsonformdata != '{}') {
        $serialiseddata = json_decode($args->jsonformdata);
        parse_str($serialiseddata, $formdata);
    }

    list($ignored, $course) = get_context_info_array($context->id);
    $group = new stdClass();
    $group->courseid = $course->id;

    require_capability('moodle/course:managegroups', $context);
    $editoroptions = [
        'maxfiles' => EDITOR_UNLIMITED_FILES,
        'maxbytes' => $course->maxbytes,
        'trust' => false,
        'context' => $context,
        'noclean' => true,
        'subdirs' => false
    ];
    $group = file_prepare_standard_editor($group, 'description', $editoroptions, $context, 'group', 'description', null);

    $mform = new group_form(null, array('editoroptions' => $editoroptions,'courseid' => $course->id,'group' => $group), 'post', '', null, true, $formdata);
    // Used to set the courseid.
    $mform->set_data($group);

    if (!empty($args->jsonformdata)) {
        // If we were passed non-empty form data we want the mform to call validation functions and show errors.
        $mform->is_validated();
    }

    return $mform->render();
}



function block_admindashboard_output_fragment_assignment_details($args){
 global $DB,$CFG,$PAGE,$OUTPUT;

     $output = $PAGE->get_renderer('block_admindashboard');
      $assignmentdetails = '<div class="d-flex justify-content-end mb-3">
         <input type="text" class="form-control w-25" id="assignsearch"  placeholder="Search..." aria-label="Search"  onkeyup="(function(e){ require(\'block_admindashboard/getassign\').search({userid:'.$args['userid'].',courseid:'.$args['courseid'].' }) })(event)">
     </div>';
      $assignmentdetails .=  $output->get_assignments(false, $args['courseid'], $args['userid']);

       return $assignmentdetails;
  }

  function block_admindashboard_output_fragment_assign_users($args){
 global $DB,$CFG,$PAGE,$OUTPUT;

        $output = $PAGE->get_renderer('block_admindashboard');
          $assignmentusers = '<div class="d-flex justify-content-end mb-3">
         <input type="text" class="form-control w-25" id="assignuserssearch"  placeholder="Search..." aria-label="Search"  onkeyup="(function(e){ require(\'block_admindashboard/assignuser\').search({assignid:'.$args['assignid'].',courseid:'.$args['courseid'].',userid:'.$args['userid'].' }) })(event)">
     </div>';
        $assignmentusers .=  $output->get_assignment_users(false, $args['courseid'],$args['assignid'],$args['userid']);

        return $assignmentusers;          

}

function block_admindashboard_output_fragment_assign_subusers($args){
 global $DB,$CFG,$PAGE,$OUTPUT;

        $output = $PAGE->get_renderer('block_admindashboard');
         $assignmentsubusers = '<div class="d-flex justify-content-end mb-3">
         <input type="text" class="form-control w-25" id="assignsubuserssearch"  placeholder="Search..." aria-label="Search"  onkeyup="(function(e){ require(\'block_admindashboard/assignsub\').search({assignid:'.$args['assignid'].',courseid:'.$args['courseid'].',userid:'.$args['userid'].' }) })(event)">
     </div>';
        $assignmentsubusers .=  $output->get_assignment_subusers(false, $args['courseid'],$args['assignid'],$args['userid']);

        return $assignmentsubusers;       
}

function block_admindashboard_output_fragment_user_details($args){
 global $DB,$CFG,$PAGE,$OUTPUT;

        $output = $PAGE->get_renderer('block_admindashboard');

        $assignmentsubusers = '<div class="d-flex justify-content-end mb-3">
         <input type="text" class="form-control w-25" id="userssearch"  placeholder="Search..." aria-label="Search"  onkeyup="(function(e){ require(\'block_admindashboard/getuser\').search({courseid:'.$args['courseid'].' }) })(event)">
     </div>';
        $assignmentsubusers .=  $output->get_usersdetails(false, $args['courseid'], $args['userid']);

        return $assignmentsubusers; 

}
function block_admindashboard_output_fragment_quiz_details($args){
 global $DB,$CFG,$PAGE,$OUTPUT;


  $output = $PAGE->get_renderer('block_admindashboard');

       $quizdetails = '<div class="d-flex justify-content-end mb-3">
         <input type="text" class="form-control w-25" id="quizssearch"  placeholder="Search..." aria-label="Search"  onkeyup="(function(e){ require(\'block_admindashboard/getquiz\').search({courseid:'.$args['courseid'].',userid:'.$args['userid'].' }) })(event)">
     </div>';
        $quizdetails .=  $output->get_quizdetails(false, $args['courseid'],$args['userid']);

        return $quizdetails;  

}

function block_admindashboard_output_fragment_quiz_enrol($args){
  global $DB,$CFG,$PAGE,$OUTPUT;

        $output = $PAGE->get_renderer('block_admindashboard');

        $quiz_enrol = '<div class="d-flex justify-content-end mb-3">
         <input type="text" class="form-control w-25" id="quizusersearch"  placeholder="Search..." aria-label="Search"  onkeyup="(function(e){ require(\'block_admindashboard/quizuser\').search({quizid:'.$args['quizid'].',courseid:'.$args['courseid'].',userid:'.$args['userid'].' }) })(event)">
     </div>';
        $quiz_enrol .=  $output->get_quiz_enrol(false, $args['courseid'],$args['quizid'],$args['userid']);

        return $quiz_enrol;
}

function block_admindashboard_output_fragment_quiz_subusers($args){
 global $DB,$CFG,$PAGE,$OUTPUT;

        $output = $PAGE->get_renderer('block_admindashboard');

         $quiz_subusers = '<div class="d-flex justify-content-end mb-3">
         <input type="text" class="form-control w-25" id="quizsubsearch"  placeholder="Search..." aria-label="Search"  onkeyup="(function(e){ require(\'block_admindashboard/quizsub\').search({quizid:'.$args['quizid'].',courseid:'.$args['courseid'].',userid:'.$args['userid'].' }) })(event)">
     </div>';
        $quiz_subusers .=  $output->get_quiz_subusers(false, $args['courseid'],$args['userid'],$args['quizid']);

        return $quiz_subusers;
}

function block_admindashboard_output_fragment_forum_enrol($args){
 global $DB,$CFG,$PAGE,$OUTPUT;


          $output = $PAGE->get_renderer('block_admindashboard');
            $forum_enrol = '<div class="d-flex justify-content-end mb-3">
         <input type="text" class="form-control w-25" id="forumusersearch"  placeholder="Search..." aria-label="Search"  onkeyup="(function(e){ require(\'block_admindashboard/forumuser\').search({forumid:'.$args['forumid'].',courseid:'.$args['courseid'].',userid:'.$args['userid'].' }) })(event)">
     </div>';
                $forum_enrol .=  $output->get_forum_enrol(false, $args['courseid'],$args['forumid'],$args['userid']);

                return $forum_enrol;       


}

function block_admindashboard_output_fragment_forum_discussions($args){
 global $DB,$CFG,$PAGE,$OUTPUT;

            // print_r($args);
          $output = $PAGE->get_renderer('block_admindashboard');

           $forum_enrol = '<div class="d-flex justify-content-end mb-3">
         <input type="text" class="form-control w-25" id="forumdiscsearch"  placeholder="Search..." aria-label="Search"  onkeyup="(function(e){ require(\'block_admindashboard/forumdisc\').search({forumid:'.$args['forumid'].',courseid:'.$args['courseid'].',userid:'.$args['userid'].' }) })(event)">
     </div>';

          $forum_enrol .=  $output->get_forum_discussions(false, $args['courseid'],$args['forumid'],$args['userid']);

                return $forum_enrol;

}

function block_admindashboard_output_fragment_forum_sub($args){
global $DB,$CFG,$PAGE,$OUTPUT;


          $output = $PAGE->get_renderer('block_admindashboard');
          $forum_sub = '<div class="d-flex justify-content-end mb-3">
         <input type="text" class="form-control w-25" id="forumsubsearch"  placeholder="Search..." aria-label="Search"  onkeyup="(function(e){ require(\'block_admindashboard/forumsub\').search({forumid:'.$args['forumid'].',courseid:'.$args['courseid'].',discussionid:'.$args['discussionid'].',userid:'.$args['userid'].' }) })(event)">
     </div>';
          $forum_sub .=  $output->get_forum_sub(false, $args['courseid'],$args['forumid'],$args['discussionid'],$args['userid']);

          return $forum_sub;
}
function block_admindashboard_output_fragment_forum_details($args){
global $DB,$CFG,$PAGE,$OUTPUT;
            $output = $PAGE->get_renderer('block_admindashboard');

             $forum_details = '<div class="d-flex justify-content-end mb-3">
         <input type="text" class="form-control w-25" id="forumsearch"  placeholder="Search..." aria-label="Search"  onkeyup="(function(e){ require(\'block_admindashboard/getforum\').search({courseid:'.$args['courseid'].',userid:'.$args['userid'].' }) })(event)">
     </div>';

            $forum_details .=  $output->get_forum_details(false, $args['courseid'],$args['userid']);

            return $forum_details;       
           

}
