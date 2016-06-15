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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	 See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Core external functions and service definitions.
 *
 * The functions and services defined on this file are
 * processed and registered into the Moodle DB after any
 * install or upgrade operation. All plugins support this.
 *
 * For more information, take a look to the documentation available:
 *	   - Webservices API: {@link http://docs.moodle.org/dev/Web_services_API}
 *	   - External API: {@link http://docs.moodle.org/dev/External_functions_API}
 *	   - Upgrade API: {@link http://docs.moodle.org/dev/Upgrade_API}
 *
 * @package	   core_webservice
 * @category   webservice
 * @copyright  2009 Petr Skodak
 * @license	   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$functions = array(
    'local_mod_assign_get_assign_by_id' => array(
		'classname'	  => 'local_mod_assign_external',
		'methodname'  => 'get_mod_assign_by_id',
		'classpath'	  => 'local/assignment/externallib.php',
		'description' => "Get assign Submition status",
		'type'		  => 'read',
		'ajax'		  => true
	),
	'local_mod_assign_get_submission_status' => array(
		'classname'	  => 'local_mod_assign_external',
		'methodname'  => 'get_mod_assign_submission_status',
		'classpath'	  => 'local/assignment/externallib.php',
		'description' => "Get assign Submition status",
		'type'		  => 'read',
		'ajax'		  => true
	),
	'local_mod_assign_get_submission_by_host_ip' => array(
		'classname'	  => 'local_mod_assign_external',
		'methodname'  => 'get_submissions_by_host_ip',
		'classpath'	  => 'local/assignment/externallib.php',
		'description' => "Get assign Submition by assign ids and host ip",
		'type'		  => 'read',
		'ajax'		  => true
	),
);

