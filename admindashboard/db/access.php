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
 * Capability definitions for this module.
 *
 * @package   block_admindashboard
 * @copyright 2024 Hiran Kumar
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$capabilities = array(

    'block/admindashboard:myaddinstance' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'user' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/site:manageblocks'
    ),

    'block/admindashboard:addinstance' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'editingteacher' => CAP_ALLOW,
            'teacher' => CAP_PROHIBIT,
            'admin' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/site:manageblocks'
    ),

    'block/admindashboard:view' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'student' => CAP_PROHIBIT,
            'teacher' => CAP_PROHIBIT,
            'manager' => CAP_ALLOW,
            'admin' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW
        )
    ), 'block/admindashboard:mits' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
    ),'block/admindashboard:bits' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,       
    ),'block/admindashboard:mba' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,       
    ),
);
