<?php

require_once("$CFG->libdir/formslib.php");

class userenroll_form extends moodleform {
    //Add elements to form
    public function definition() {
        global $CFG, $DB, $PAGE, $COURSE;
        // $PAGE->requires->js('/blocks/admindashboard/js/mycourses.js','true');
        $mform = $this->_form; // Don't forget the underscore!
        $attr['id'] = "enrolform2";
        $mform->setAttributes($attr);

        $mform->addElement('autocomplete', 'enrolluser', '','', array(
            'multiple' => true,
            'placeholder' => 'Users'
        ));

        $roles  = $DB->get_records_sql("SELECT id,name,shortname  from {role}",array());
        $rolenames = array(5 => 'Student');
        foreach ($roles as $role) {
            if($role->name){    
                $rolenames[$role->id] = $role->name ? $role->name :$role->shortname;
            }
            
        }
        $mform->addElement('autocomplete', 'assignrole', '', $rolenames, array(
            'multiple' => false,
            'placeholder' => 'Role'
        ));
        

            // $rolelist = array();
            // foreach ($roles as $role) {
            //      $subdata = array();
            //      $subdata['roleid'] = $role->id;
            //      $subdata['rolename'] = $role->name ? $role->name :$role->shortname;
     
            //      $rolelist[] =$subdata;
            // }
   
        $mform->addElement('button', 'enrollbtn', 'Enroll',array());

        $mform->addElement('button', 'enrollcancelbtn', 'Cancel',array());

    }

    function validation($data, $files) {

        return $errors;
    }
}
