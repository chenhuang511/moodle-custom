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

require_once("$CFG->libdir/externallib.php");


class local_user_external extends external_api {
	
    public static function get_remote_mapping_userid_parameters() {
        return new external_function_parameters(
            array('ipadress' => new external_value(PARAM_TEXT, 'Host IP address'),
                'username' => new external_value(PARAM_TEXT, 'username'))
        );
    }

    public static function get_remote_mapping_userid($ipaddress, $username) {
        global $CFG, $DB;

        //validate parameter
        $params = self::validate_parameters(self::get_remote_mapping_userid_parameters(),
            array('ipadress' => $ipaddress, 'username' => $username));

		return $DB->get_records_sql("SELECT u.id,u.username,u.email,u.auth FROM {user} u
									WHERE u.mnethostid = (SELECT id FROM {mnet_host} m 
									WHERE m.ip_address=?) AND u.username=?",
            array('ip_address' => $ipaddress, 'username' => $username));
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.9 Options available
     * @since Moodle 2.2
     */
    public static function get_remote_mapping_userid_returns() {
        return new external_multiple_structure (
			new external_single_structure(
				array(
					'id' => new external_value(PARAM_INT, 'user id'),
					'username' => new external_value(PARAM_TEXT, 'username'),
					'email' => new external_value(PARAM_TEXT, 'user email'),
					'auth' => new external_value(PARAM_TEXT, 'auth method'),					
				)
			));
    }
}
