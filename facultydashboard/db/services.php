<?php

$functions = array(
        'block_facultydashboard' => array(                                                
                'classname'   => 'block_create_facultydashboard_external',
                'methodname'  => 'faculty_courses',
                'classpath'   => 'blocks/facultydashboard/externallib.php',
                'description' => 'this function get facultydashboard',
                'type'        => 'write', 
                'ajax'        => true, 
                'capabilities'=> 'moodle:view',
        ),
);
