<?php

require_once("$CFG->libdir/formslib.php");

class enroll_form extends moodleform {
    //Add elements to form
    public function definition() {
        global $CFG, $DB, $PAGE, $COURSE;
        // $PAGE->requires->js('/blocks/admindashboard/js/mycourses.js','true');
        $mform = $this->_form; // Don't forget the underscore!
        $attr['id'] = "enrolform1";
        $mform->setAttributes($attr);

        $courses = $DB->get_records_menu('course', array('visible' => 1), '', 'id, shortname');

        // $areanames = array(-1 => get_string('all'));
       
        // foreach ($courses as $id => $name) {
        //     $areanames[$id] = $name;
        // }

        $mform->addElement('autocomplete', 'enrollcourse', '', $areanames, array(
            'multiple' => false,
            'placeholder' => 'Unit'
            //,'onchange' => 'get_groups(1)'
        ));


        $mform->addElement('autocomplete', 'enrollgroup', '', '', array(
            'multiple' => true,
            'placeholder' => 'Groups'
        ));

        // $context = context_course::instance($courses->id);
        // $contextid = $context->id;

        // $mform->addElement('html', '<button type="button" id="add-course-btn" title="Create Group" onclick="require(\'block_admindashboard/getgroup\').init();">Create Group</button>');


        // $mform->addElement('html', '<button type="button" id="add-course-btn" onclick = "(function(e){ require(\'block_admindashboard/getgroup\').init(selector:\'creategroupmodal\',contextid:166)})(event)">+</button>');

        // $mform->addElement('autocomplete', 'enrolluser', '', '', array(
        //     'multiple' => true,
        //     'placeholder' => 'Users'
        // ));

        // $mform->addElement('autocomplete', 'assignrole', '', '', array(
        //     'multiple' => true,
        //     'placeholder' => 'Role'
        // ));

        // // Add a submit button
        $mform->addElement('button', 'add-course-btn', '+', array('onclick'=>"require('block_admindashboard/getgroup').init();"));

    }
    //Custom validation should be added here
    function validation($data, $files) {

        return $errors;
    }
}
