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
 *       - Webservices API: {@link http://docs.moodle.org/dev/Web_services_API}
 *       - External API: {@link http://docs.moodle.org/dev/External_functions_API}
 *       - Upgrade API: {@link http://docs.moodle.org/dev/Upgrade_API}
 *
 * @package       core_webservice
 * @category   webservice
 * @copyright  2009 Petr Skodak
 * @license       http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$functions = array(
    'local_mod_get_duration_lesson_timer_by_lessonid_and_userid' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'get_duration_lesson_timer_by_lessonid_and_userid',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'Get duration lesson timer by userid and lessonid',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_get_maxgrade_lesson_grades_by_userid_and_lessonid' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'get_maxgrade_lesson_grades_by_userid_and_lessonid',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'Get maxgrade of lesson grades by userid and lessonid',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_get_events_by_modulename_and_instance' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'get_events_by_modulename_and_instace',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'Get list of events by module name and instance',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_check_record_exists' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'check_record_exists',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'check record exists',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_get_user_by_lessonid' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'get_user_by_lessonid',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'get user by lessonid',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_get_field_by' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'get_field_by',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'get field of modname by',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_get_count_by' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'get_count_by',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'get count of modname by',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_get_lesson_by' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'get_lesson_by',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'get lesson by',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_get_lesson_pages_by' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'get_lesson_pages_by',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'get lesson pages by',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_get_lesson_grades_by' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'get_lesson_grades_by',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'get lesson grades by',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_get_lesson_answers_by' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'get_lesson_answers_by',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'get lesson answers by',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_get_lesson_overrides_by' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'get_lesson_overrides_by',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'get lesson overrides by',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_get_lesson_attempts_by' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'get_lesson_attempts_by',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'get lesson attempts by',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_get_list_lesson_grades_by' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'get_list_lesson_grades_by',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'get list of lesson grades by',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_get_list_lesson_timer_by' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'get_list_lesson_timer_by',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'get list of lesson timer by',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_get_list_lesson_branch_by' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'get_list_lesson_branch_by',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'get list of lesson branch by',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_get_list_lesson_attempts_by' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'get_list_lesson_attempts_by',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'get list of lesson attempts by',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_get_list_lesson_answers_by' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'get_list_lesson_answers_by',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'get list of lesson answers by',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_get_list_lesson_answers_select' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'get_list_lesson_answers_select',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'get list of lesson answers by',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_get_list_lesson_pages_by' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'get_list_lesson_pages_by',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'get list of lesson pages by',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_get_list_lesson_pages_select' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'get_list_lesson_pages_select',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'get list of lesson pages by',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_get_list_lesson_overrides_by' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'get_list_lesson_overrides_by',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'get list of lesson overrides by',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_get_list_lesson_attempts_sql' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'get_list_lesson_attempts_sql',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'get list of lesson attempts sql',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_get_list_lesson_by' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'get_list_lesson_by',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'get list of lesson by',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_save_mdl_lesson' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'save_mdl_lesson',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'save new a lesson object',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_update_mdl_lesson' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'update_mdl_lesson',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'update data of mdl lesson',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_update_mdl_table' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'update_mdl_table',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'update mdl table',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_delete_mdl_lesson' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'delete_mdl_lesson',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'Delete moodle table',
        'type' => 'read',
        'ajax' => true
    ),
);