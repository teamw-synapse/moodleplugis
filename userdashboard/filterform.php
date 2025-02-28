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
 * @package    block_userdashboard
 * @copyright  2024 VGPL
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

// moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");
use moodleform;
class filterform_form extends moodleform {
    // Add elements to form.
    public function definition() {
        global $DB;
        // A reference to the form is stored in $this->form.
        // A common convention is to store it in a variable, such as `$mform`.
        $mform = $this->_form; // Don't forget the underscore!

        $options = array('placeholder' => get_string('searchstudents', 'block_userdashboard'),'noselectionstring' => '');
        // $students = $DB->get_records_sql_menu("SELECT id,CONCAT(firstname,' ',lastname) FROM mdl_user WHERE id>2 AND deleted = 0 AND suspended = 0 LIMIT 0,50");         
        $mform->addElement('autocomplete', 'studentids', '', array(), $options);
    }

    // Custom validation should be added here.
    function validation($data, $files) {
        return [];
    }
}
