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
 * Defines mobile handlers.
 *
 * @package   mod_customcert
 * @copyright 2018 Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;
$addons = array(
    "block_userdashboard" => array( // Plugin identifier.
        'handlers' => array( // Different places where the plugin will display content.
            'userdashboard' => array( // Handler unique name (alphanumeric).
                'delegate'    => 'CoreBlockDelegate', // Delegate (where to display the link to the plugin).
                'method' => 'view_dashboard',
                'styles' => array(
                    'url' => $CFG->dirroot.'/blocks/userdashboard/css/styles.css',
                    'version' => ''
                ) 
            )
        ),
        'lang' => [ // Language strings that are used in all the handlers.
            ['blockname', 'block_userdashboard'],
            ['pluginname', 'block_userdashboard'],
        ],

    )
);
