<?php

require_once(__DIR__ . '/../../config.php');

require_login();
global $DB, $USER,$CFG, $OUTPUT, $PAGE;

// TODO Add sesskey check to edit
$selected_status   = optional_param_array('selected_status', null, PARAM_RAW);
$selected_roles   = optional_param_array('selected_roles', null, PARAM_RAW);
if($selected_status){
    if (($key = array_search('-1', $selected_status)) !== false) {
        unset($selected_status[$key]);
    }
    $selected_status = implode(',',$selected_status);
}


$sql = "SELECT u.*,r.shortname as role FROM mdl_user as u  ";

if($selected_roles){
$selected_roles = implode(',',$selected_roles);

$sql .= "JOIN mdl_role_assignments ra ON ra.userid = u.id
          JOIN mdl_context c ON c.id = ra.contextid AND c.contextlevel = 50
          JOIN mdl_course co ON co.id = c.instanceid
          JOIN mdl_role as r ON r.id = ra.roleid
          AND ra.roleid IN ($selected_roles) ";

}
$sql .= " WHERE u.deleted = 0";
if($selected_status){
	$sql .= " AND u.suspended IN ($selected_status)";
}

$records = $DB->get_records_sql($sql);
$table = "<table class='generaltable' id='roledata'> <tbody>";
              $table .= "<tr>
              <th>Id</th>
              <th>First Name</th>
              <th>Last Name</th>
              <th>User Name</th>
              <th>Email</th>
              <th>Role</th>
              <th>Edit</th></tr>";

              foreach($records as $user){

              $table .= "<tr>
              <td>".$user->id."</td>
              <td>".$user->firstname."</td>
              <td>".$user->lastname."</td>
              <td>".$user->username."</td>
              <td>".$user->email."</td>
              <td>".$user->role."</td>
              <td class='edit-button'><a href = '".$CFG->wwwroot."/user/editadvanced.php?id=".$user->id."'>Edit</a></td></tr>";

              }

              $table .= "</table> </tbody>";

              echo $table;
              die;

