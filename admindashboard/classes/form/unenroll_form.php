<?php

require_once("$CFG->libdir/formslib.php");

class unenroll_form extends moodleform {
    //Add elements to form
    public function definition() {
        global $CFG, $DB, $PAGE, $COURSE;
        // $PAGE->requires->js('/blocks/admindashboard/js/mycourses.js','true');
        $mform = $this->_form; // Don't forget the underscore!
      
       

        $mform->addElement('autocomplete', 'unenrollcourse', '', $areanames, array(
            'multiple' => false,
            'placeholder' => 'Unit'
            //,'onchange' => 'get_groups(1)'
        ));


       $mform->addElement('autocomplete', 'unenrolluser', '','', array(
            'multiple' => false,
            'placeholder' => 'Users'
        ));


      $mform->addElement('button', 'unenrollbtn', 'Unenroll',array());
      $mform->addElement('button', 'unrollcancelbtn', 'Cancel',array());
      

    }
   
    function validation($data, $files) {

        return $errors;
    }
}
