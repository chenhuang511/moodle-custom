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
    'local_mod_get_lesson_by_id' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'get_lesson_by_id',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'Get lesson by id',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_get_lesson_pages_by_lessonid_and_prevpageid' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'get_lesson_pages_by_lessonid_and_prevpageid',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'Get lesson pages by lessonid and prevpageid',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_get_lesson_pages_by_pageid_and_lessonid' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'get_lesson_pages_by_pageid_and_lessonid',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'Get lesson page by page id and lesson id',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_get_field_lesson_pages_by_id' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'get_field_lesson_pages_by_id',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'get field of lesson pages by id',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_get_field_lesson_pages_by_lessonid_and_prevpageid' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'get_field_lesson_pages_by_lessonid_and_prevpageid',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'Get id of lesson page by lessonid and prevpageid',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_get_lesson_timer_by_userid_and_lessonid' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'get_lesson_timer_by_userid_and_lessonid',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'Get lesson timer by userid and lessonid',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_get_lesson_branch_by_lessonid_and_userid_and_retry' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'get_lesson_branch_by_lessonid_and_userid_and_retry',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'Get lesson branch by lessonid and userid and retry',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_save_lesson_branch' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'save_lesson_branch',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'insert new lesson branch',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_get_count_lessonid_and_userid' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'get_count_by_lessonid_and_userid',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'Get count of lesson table by lesson id and user id',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_get_lesson_attempts_by_lessonid_and_userid' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'get_lesson_attempts_by_lessonid_and_userid',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'Get lesson attempts by lesson id and user id',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_get_lesson_answer_by_pageid_and_lessonid' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'get_lesson_answers_by_pageid_and_lessonid',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'Get lesson answer by pageid and lessonid',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_get_lesson_answers_by_id' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'get_lesson_answers_by_id',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'Get lesson answers by id',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_get_lesson_answers_by_lessonid' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'get_lesson_answers_by_lessonid',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'Get list lesson answers by lessonid',
        'type' => 'read',
        'ajax' => true
    ),
    'local_mod_get_lesson_grades_by_lessonid_and_userid' => array(
        'classname' => 'local_mod_lesson_external',
        'methodname' => 'get_lesson_grades_by_lessonid_and_userid',
        'classpath' => 'local/lesson/externallib.php',
        'description' => 'Get lesson grades by lessonid and userid. Return list',
        'type' => 'read',
        'ajax' => true
    ),
);
