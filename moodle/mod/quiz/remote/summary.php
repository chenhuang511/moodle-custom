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
 * This page prints a summary of a quiz attempt before it is submitted.
 *
 * @package   mod_quiz
 * @copyright 2009 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

$attemptid = required_param('attempt', PARAM_INT); // The attempt to summarise.

$PAGE->set_url('/mod/quiz/remote/summary.php', array('attempt' => $attemptid));

$attemptobj = quiz_attempt::create($attemptid);

$nonajax = optional_param('nonajax', true, PARAM_BOOL);
$cm = $attemptobj->get_cm();
$context = context_module::instance($cm->id);
if (!has_capability('moodle/course:manageactivities', $context) && $nonajax == false) {
    $CFG->nonajax = false;
} else {
    $CFG->nonajax = true;
}

// Check login.
require_login($attemptobj->get_course(), false, $attemptobj->get_cm());

//$user = get_remote_mapping_user();
// Check that this attempt belongs to this user.
if ($attemptobj->get_userid() != $USER->id) {
    if ($attemptobj->has_capability('mod/quiz:viewreports')) {
        redirect($attemptobj->review_url(null));
    } else {
        throw new moodle_quiz_exception($attemptobj->get_quizobj(), 'notyourattempt');
    }
}

// Check capabilites.
if (!$attemptobj->is_preview_user()) {
    $attemptobj->require_capability('mod/quiz:attempt');
}

if ($attemptobj->is_preview_user()) {
    navigation_node::override_active_url($attemptobj->start_attempt_url());
}

// Check access.
$accessmanager = $attemptobj->get_access_manager(time());
$accessmanager->setup_attempt_page($PAGE);
$output = $PAGE->get_renderer('mod_quiz');
$messages = $accessmanager->prevent_access();
if (!$attemptobj->is_preview_user() && $messages) {
    print_error('attempterror', 'quiz', $attemptobj->view_url(),
            $output->access_messages($messages));
}
if ($accessmanager->is_preflight_check_required($attemptobj->get_attemptid())) {
    redirect($attemptobj->start_attempt_url(null));
}

$displayoptions = $attemptobj->get_display_options(false);

$settinglocal = array();
$quiz = $attemptobj->get_quiz();
if($quiz->settinglocal){
    $fields =  array(
        'timeopen',
        'timeclose',
        'timelimit',
        'overduehandling',
        'graceperiod',
        'attempts',
        'grademethod',
    );
    $index = 0;
    foreach ($fields as $field){
        $settinglocal["setting[$index][name]"] = $field;
        $settinglocal["setting[$index][value]"] = $quiz->$field;
        $index++;
    }
}

// If the attempt is now overdue, or abandoned, deal with that.
$attempt = remote_handle_if_time_expired($quiz->remoteid, $attempt->id, true, $settinglocal);
$summaryremote = get_remote_get_attempt_summary($attemptobj->get_attemptid(), $settinglocal);

// If the attempt is already closed, redirect them to the review page.
//$attempt->state == quiz_attempt::FINISHED || $attempt->state == quiz_attempt::ABANDONED;
if ($attempt->state == quiz_attempt::FINISHED || $attempt->state == quiz_attempt::ABANDONED) {
    redirect($attemptobj->review_url());
}

// Arrange for the navigation to be displayed.
if (empty($attemptobj->get_quiz()->showblocks)) {
    $PAGE->blocks->show_only_fake_blocks();
}

$navbc = $attemptobj->get_navigation_panel($output, 'quiz_attempt_nav_panel', -1, false, $summaryremote);
$regions = $PAGE->blocks->get_regions();
$PAGE->blocks->add_fake_block($navbc, reset($regions));

$PAGE->navbar->add(get_string('summaryofattempt', 'quiz'));
$PAGE->set_title($attemptobj->get_quiz_name());
$PAGE->set_heading($attemptobj->get_course()->fullname);

// Display the page.
echo $output->summary_page($attemptobj, $displayoptions, $summaryremote);

// Log this page view.
$attemptobj->fire_attempt_summary_viewed_event();


