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
 * @package    block_facultydashboard
 * @copyright  2024 VGPL
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

// moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");
use moodleform;
class facultyfilter_form extends moodleform {
    // Add elements to form.
    public function definition() {
        global $DB;

        $mform = $this->_form; // Don't forget the underscore!
        $options = array('placeholder' => get_string('searchfaculty', 'block_facultydashboard'),'noselectionstring' => '');
        $faculty = [];
        $mform->addElement('autocomplete', 'facultyids', '', $faculty, $options);
    }

    // Custom validation should be added here.
    function validation($data, $files) {
        return [];
    }
}
