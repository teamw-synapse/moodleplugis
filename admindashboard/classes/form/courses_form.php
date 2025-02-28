<?php

require_once("$CFG->libdir/formslib.php");

class courses_form extends moodleform {
    //Add elements to form
    public function definition() {
        global $CFG, $DB, $PAGE, $COURSE,$USER;
        // $PAGE->requires->js('/blocks/admindashboard/js/mycourses.js','true');
        $mform = $this->_form; // Don't forget the underscore!
        $systemcontext = $context = context_system::instance();

        $areanames = array(null => '');
        if(is_siteadmin()){
             $categories = $DB->get_records('course_categories', array('parent' => 0));       
            foreach ($categories as $category) {

                if($category->$id != 1 && $category->idnumber)
                {
                    $areanames[$category->id] = $category->idnumber;
                }
                
            }
        }else{
            //course coradinatior code start 

            $userunittype = $DB->get_record('local_rolemanagement',array('userid'=>$USER->id));

            if($userunittype){
                $u_unittype=explode(',', $userunittype->mastercoursetype);
                 foreach ($u_unittype as $type) {
                    if(str_contains($type,'ICA70112')){

                         $areanames[14] = 'ICA70112-BITS';

                    }elseif(str_contains($type,'MBA9118')){

                         $areanames[79] = 'MBA9118';

                    }else{
                        if(!has_capability('block/admindashboard:mits', $systemcontext))
                         $areanames[81] = 'MITS9118';

                    }
                 }
            }//course coradinatior code start 
            
        }


         $options = array(
            'multiple' => false,
            'noselectionstring ' => '',
            'placeholder' => 'Course'
            //,'onchange' => 'get_terms(0)'
        );

        // Add fetched categories to the dropdown
        $mform->addElement('autocomplete', 'coursestatus', '', $areanames, $options);
        $mform->settype('coursestatus', PARAM_INT);
        

        // Add fetched sub_categories to the dropdown
        $mform->addElement('autocomplete', 'sub_categories', '', '', array(
            'multiple' => false,
            'placeholder' => 'Sub Course'
            //,'onchange' => 'get_units(0)'
        ));

         $mform->addElement('autocomplete', 'child_categories', '', '', array(
            'multiple' => false,
            'placeholder' => 'Category'
            //,'onchange' => 'get_units(0)'
        ));


        // Add fetched courses to the dropdown
        $mform->addElement('autocomplete', 'all_courses', '', '', array(
            'multiple' => true,
            'placeholder' => 'Unit'
        ));


        $apply = array(
            // 'onClick' => 'get_units()'
        );

        // Add a submit button
        $mform->addElement('button', 'applybtn', get_string('apply'), $apply);
        $mform->addElement('button', 'applycancelbtn', 'Cancel', $apply);

    }
    //Custom validation should be added here
    function validation($data, $files) {

        return $errors;
    }
}
