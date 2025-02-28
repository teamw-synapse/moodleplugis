<?php

require_once(__DIR__ . '/../../config.php');

require_login();
global $DB, $USER,$CFG, $OUTPUT, $PAGE;

// TODO Add sesskey check to edit
$selected_course   = optional_param('courseid', 0, PARAM_INT);
$contextid = $DB->get_field('context','id',array('instanceid' => $selected_course,'contextlevel' => 50));

echo json_encode(array('contextid' => $contextid));