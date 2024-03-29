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
 * External course API
 *
 * @package    core_course
 * @category   external
 * @copyright  2009 Petr Skodak
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/externallib.php');

class local_role_external extends external_api {
    
    public static function remote_assign_role_to_user_parameters() {
        return new external_function_parameters(
            array('roleid' => new external_value(PARAM_TEXT, 'Role id'),
                'userid' => new external_value(PARAM_TEXT, 'user id'),
        'courseid' => new external_value(PARAM_TEXT, 'Course id'))
        );
    }

    public static function remote_assign_role_to_user($roleid, $userid, $courseid) {
        global $CFG, $DB, $PAGE;

        //validate parameter
        $params = self::validate_parameters(self::remote_assign_role_to_user_parameters(),
            array('roleid' => $roleid, 'userid' => $userid, 'courseid' => $courseid));
                
        $course = $DB->get_record("course", array('id' => $courseid), "*", MUST_EXIST);
        $context = context_course::instance($course->id);
        
        return role_assign($roleid, $userid, $context->id, 'enrol_mnet', NULL);
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.9 Options available
     * @since Moodle 2.2
     */
    public static function remote_assign_role_to_user_returns() {
        return new external_value(PARAM_INT, 'role assignments id');
    }

    public static function remote_unassign_role_to_user_parameters() {
        return new external_function_parameters(
            array('roleid' => new external_value(PARAM_TEXT, 'Role id'),
                'userid' => new external_value(PARAM_TEXT, 'user id'),
                'courseid' => new external_value(PARAM_TEXT, 'Course id'))
        );
    }

    public static function remote_unassign_role_to_user($roleid, $userid, $courseid) {
        global $CFG, $DB, $PAGE;

        //validate parameter
        $params = self::validate_parameters(self::remote_unassign_role_to_user_parameters(),
            array('roleid' => $roleid, 'userid' => $userid, 'courseid' => $courseid));

        $course = $DB->get_record("course", array('id' => $courseid), "*", MUST_EXIST);
        $context = context_course::instance($course->id);
        $msg = 'successfully';
        try {
            role_unassign($roleid, $userid, $context->id, 'enrol_mnet', NULL);
        } catch (Exception $e) {
            $msg = 'unassign fail';
        }
        return $msg;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.9 Options available
     * @since Moodle 2.2
     */
    public static function remote_unassign_role_to_user_returns() {
        return new external_value(PARAM_TEXT, 'notice');
    }
}
