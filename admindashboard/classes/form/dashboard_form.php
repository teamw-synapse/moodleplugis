<?php

require_once("$CFG->libdir/formslib.php");

class simplehtml_form extends moodleform {
    //Add elements to form
    public function definition() {
        global $CFG, $DB;

        $mform = $this->_form; // Don't forget the underscore! 

        $areanames = array(null => '',0 => 'Active',1=>'Inactive');
        $options = array(
            'multiple' => false,
            'placeholder' => 'Status'
        );
        $mform->addElement('autocomplete', 'status', '', $areanames, $options);
        $mform->setDefault('status', '');

         $categories = $DB->get_records('course_categories', array('parent' => 0));

        $areanames = array(null => '');
       
        foreach ($categories as $category) {

            // if($category->$id != 1 && $category->idnumber)
            // {
                $areanames[$category->id] = $category->idnumber ? $category->idnumber :$category->name;
            //}
            
        }
        $options = array(
            'multiple' => true,
            'placeholder' => 'Course type'
        );
        $mform->addElement('autocomplete', 'type', '', $areanames, $options);
        $mform->setDefault('type', '');
        $rolesdata = array(null => '');
        $roles = $DB->get_records_sql_menu("SELECT id,name FROM mdl_role WHERE shortname = 'vit_trainer' OR shortname = 'student' OR shortname = 'vit_coordinator' OR shortname = 'observer_teacher' OR shortname = 'unit_facilitator' OR shortname = 'editingteacher' OR shortname = 'teacher'");
        // $roles = array(5 => 'Students',3 => 'Editing Teacher',4 => 'Teacher');
        $options = array(
            'multiple' => false,
            'placeholder' => 'Role'
        );
        $mform->addElement('autocomplete', 'roles', '', $rolesdata+$roles, $options);
        $mform->setDefault('roles', '');

        // $attributes =  array('placeholder' =>'Search User');
        // $mform->addElement('text', 'userssearch', '',$attributes);


        // Add a submit button
        $mform->addElement('button', 'submitbtn', get_string('apply'));
         $mform->addElement('button', 'submitcancelbtn', 'Cancel');

    }
    //Custom validation should be added here
    function validation($data, $files) {

        return $errors;
    }
}
