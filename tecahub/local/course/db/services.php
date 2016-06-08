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
	'local_get_course_content_by_id' => array(
		'classname'   => 'local_mod_course_external',
		'methodname'  => 'get_course_content_by_id',
		'classpath'   => 'local/course/externallib.php',
		'description' => "Get Course Content with courses id",
		'type'        => 'read',
		'ajax'        => true
	),
	'local_get_course_thumbnail_by_id' => array(
		'classname'   => 'local_mod_course_external',
		'methodname'  => 'get_thumbnail_by_id',
		'classpath'   => 'local/course/externallib.php',
		'description' => "Get thumbnail url with courses id",
		'type'        => 'read',
		'ajax'        => true
	),
	'local_get_course_mods' => array(
		'classname'   => 'local_mod_course_external',
		'methodname'  => 'get_remote_course_mods',
		'classpath'   => 'local/course/externallib.php',
		'description' => "Get course modules with courses id",
		'type'        => 'read',
		'ajax'        => true
	),
	'local_get_course_sessions' => array(
		'classname'   => 'local_mod_course_external',
		'methodname'  => 'get_remote_course_sessions',
		'classpath'   => 'local/course/externallib.php',
		'description' => "Get course sessions with courses id",
		'type'        => 'read',
		'ajax'        => true
	),
	'local_get_course_module_by_cmid' => array(
		'classname'	  => 'local_mod_course_external',
		'methodname'  => 'get_course_module_by_cmid',
		'classpath'	  => 'local/course/externallib.php',
		'description' => "Get course by module name and id of course module",
		'type'		  => 'read',
		'ajax'		  => true
	),

	'local_get_course_sections_by_id' => array(
		'classname'     => 'local_mod_course_external',
		'methodname'    => 'mod_get_course_sections_by_id',
		'classpath'     => 'local/course/externallib.php',
		'description'   => 'Get course sections by ID',
		'type'		    => 'read',
		'ajax'		    => true
	),
);
