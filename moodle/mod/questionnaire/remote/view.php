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

require_once("../../../config.php");
require_once($CFG->dirroot.'/mod/questionnaire/locallib.php');
require_once($CFG->libdir . '/completionlib.php');
require_once($CFG->dirroot.'/mod/questionnaire/questionnaire.class.php');
require_once($CFG->dirroot . '/mod/questionnaire/remote/locallib.php');

if (!isset($SESSION->questionnaire)) {
    $SESSION->questionnaire = new stdClass();
}
$SESSION->questionnaire->current_tab = 'view';

$id = optional_param('id', null, PARAM_INT);    // Course Module ID.
$a = optional_param('a', null, PARAM_INT);      // Or questionnaire ID.

$sid = optional_param('sid', null, PARAM_INT);  // Survey id.

list($cm, $course, $questionnaire) = questionnaire_get_standard_page_items($id, $a);
// Check login and get context.
require_login($course, true, $cm);
$context = context_module::instance($cm->id);

$nonajax = optional_param('nonajax', null, PARAM_INT);
if (!has_capability('moodle/course:manageactivities', $context) && $nonajax != true) {
    $CFG->nonajax = true;
} else {
    $CFG->nonajax = true;
}
$CFG->nonajax = true; // fixed remove ajax layout
$url = new moodle_url($CFG->wwwroot.'/mod/questionnaire/view.php');
if (isset($id)) {
    $url->param('id', $id);
} else {
    $url->param('a', $a);
}
if (isset($sid)) {
    $url->param('sid', $sid);
}
$PAGE->set_url($url);
$PAGE->set_context($context);
$questionnaire = new questionnaire(0, $questionnaire, $course, $cm);
$PAGE->set_title(format_string($questionnaire->name));

$PAGE->set_heading(format_string($course->fullname));
if($CFG->nonajax == true ){
    echo $OUTPUT->header();
}

echo $OUTPUT->heading(format_text($questionnaire->name), 3, array('class' => 'el-heading'));

// Print the main part of the page.
if ($questionnaire->intro) {
    echo $OUTPUT->box(format_module_intro('questionnaire', $questionnaire, $cm->id), 'generalbox', 'intro');
}

echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');

$cm = $questionnaire->cm;
$currentgroupid = groups_get_activity_group($cm);
$remoteuserid = get_remote_mapping_user($USER->id)[0]->id;
if (!groups_is_member($currentgroupid, $USER->id)) {
    $currentgroupid = 0;
}
if (!$questionnaire->is_active()) {
    if ($questionnaire->capabilities->manage) {
        $msg = 'removenotinuse';
    } else {
        $msg = 'notavail';
    }
    echo '<div class="message">'
    .get_string($msg, 'questionnaire')
    .'</div>';

} else if (!$questionnaire->is_open()) {
    echo '<div class="message">'
    .get_string('notopen', 'questionnaire', userdate($questionnaire->opendate))
    .'</div>';
} else if ($questionnaire->is_closed()) {
    echo '<div class="message">'
    .get_string('closed', 'questionnaire', userdate($questionnaire->closedate))
    .'</div>';
} else if ($questionnaire->survey->realm == 'template') {
    print_string('templatenotviewable', 'questionnaire');
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
    exit();
} else if (!$questionnaire->user_is_eligible($USER->id)) {
    if ($questionnaire->questions) {
        echo '<div class="message">'.get_string('noteligible', 'questionnaire').'</div>';
    }
} else if (!$questionnaire->user_can_take($USER->id)) {
    switch ($questionnaire->qtype) {
        case QUESTIONNAIREDAILY:
            $msgstring = ' '.get_string('today', 'questionnaire');
            break;
        case QUESTIONNAIREWEEKLY:
            $msgstring = ' '.get_string('thisweek', 'questionnaire');
            break;
        case QUESTIONNAIREMONTHLY:
            $msgstring = ' '.get_string('thismonth', 'questionnaire');
            break;
        default:
            $msgstring = '';
            break;
    }
    echo ('<div class="message">'.get_string("alreadyfilled", "questionnaire", $msgstring).'</div>');
} else if ($questionnaire->user_can_take($USER->id)) {
    if(MOODLE_RUN_MODE === MOODLE_MODE_HOST){
        $select = 'survey_id = '.$questionnaire->survey->id.' AND username = \''.$USER->id.'\' AND complete = \'n\'';
        $resume = $DB->get_record_select('questionnaire_response', $select, null) !== false;
    } else {
        $sql_select = 'R.survey_id = '.$questionnaire->survey->id.' AND R.username = \''.$remoteuserid.'\' AND R.complete = \'n\'';
        $resume = !empty(get_remote_questionnaire_response($sql_select));
    }
    if (!$resume) {
        $complete = get_string('answerquestions', 'questionnaire');
    } else {
        $complete = get_string('resumesurvey', 'questionnaire');
    }
    if ($questionnaire->questions) { // Sanity check.
        if($CFG->nonajax == true ){
            echo '<a href="'.$CFG->wwwroot.htmlspecialchars('/mod/questionnaire/complete.php?'.
                    'id='.$questionnaire->cm->id.'&resume='.$resume).'&nonajax=1">'.$complete.'</a>';
        } else {
            echo '<a class="sublink get-remote-content remote-link-action" data-module=\'';
            echo json_encode(array('url' => $CFG->wwwroot . '/mod/questionnaire/complete.php?'.'id='.$questionnaire->cm->id.'&resume='.$resume, 'params' => array('id' => $questionnaire->cm->id), 'method' => 'get', 'resume' => $resume));
            echo '\' href="#">'.$complete;
            echo '</a>';
        }
    }
}
if ($questionnaire->is_active() && !$questionnaire->questions) {
    echo '<p>'.get_string('noneinuse', 'questionnaire').'</p>';
}

if ($questionnaire->is_active() && $questionnaire->capabilities->editquestions && !$questionnaire->questions && (MOODLE_RUN_MODE === MOODLE_MODE_HOST)) { // Sanity check.
    if($CFG->nonajax == true){
        echo '<a href="'.$CFG->wwwroot.htmlspecialchars('/mod/questionnaire/questions.php?'.
                'id='.$questionnaire->cm->id).'&nonajax=1">'.'<strong>'.get_string('addquestions', 'questionnaire').'</strong></a>';
    } else {
        echo '<a class="sublink get-remote-content remote-link-action" data-module=\'';
        echo json_encode(array('url' => $CFG->wwwroot . '/mod/questionnaire/questions.php', 'params' => array('id' => $questionnaire->cm->id), 'method' => 'get', 'resume' => $resume));
        echo '\' href="#">'.'<strong>'.get_string('addquestions', 'questionnaire').'</strong>';
        echo '</a>';
    }
}
echo $OUTPUT->box_end();

if (isguestuser()) {
    $output = '';
    $guestno = html_writer::tag('p', get_string('noteligible', 'questionnaire'));
    $liketologin = html_writer::tag('p', get_string('liketologin'));
    $output .= $OUTPUT->confirm($guestno."\n\n".$liketologin."\n", get_login_url(),
            get_local_referer(false));
    echo $output;
}

// Log this course module view.
// Needed for the event logging.
$context = context_module::instance($questionnaire->cm->id);
$anonymous = $questionnaire->respondenttype == 'anonymous';

$event = \mod_questionnaire\event\course_module_viewed::create(array(
                'objectid' => $questionnaire->id,
                'anonymous' => $anonymous,
                'context' => $context
));
$event->trigger();

$usernumresp = $questionnaire->count_submissions($USER->id);

if ($questionnaire->capabilities->readownresponses && ($usernumresp > 0)) {
    echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');
    $argstr = 'instance='.$questionnaire->id.'&user='.$USER->id;
    if ($usernumresp > 1) {
        $titletext = get_string('viewyourresponses', 'questionnaire', $usernumresp);
    } else {
        $titletext = get_string('yourresponse', 'questionnaire');
        $argstr .= '&byresponse=1&action=vresp';
    }
    if($CFG->nonajax == true){
        echo '<a href="'.$CFG->wwwroot.htmlspecialchars('/mod/questionnaire/myreport.php?'.
                $argstr).'&nonajax=1">'.$titletext.'</a>';
    } else {
        echo '<a class="sublink get-remote-content remote-link-action" data-module=\'';
        echo json_encode(array('url' => $CFG->wwwroot . '/mod/questionnaire/myreport.php?'.
                $argstr, 'params' => array('id' => $questionnaire->cm->id), 'method' => 'get', 'resume' => $resume));
        echo '\' href="#">'.$titletext;
        echo '</a>';
    }
    echo $OUTPUT->box_end();
}

if ($questionnaire->can_view_all_responses($usernumresp)) {
    echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');
    $argstr = 'instance='.$questionnaire->id.'&group='.$currentgroupid;
    if($CFG->nonajax == true){
        echo '<a href="'.$CFG->wwwroot.htmlspecialchars('/mod/questionnaire/report.php?'.
                $argstr).'&nonajax=1" class="btn btn-viewallresponse">'.get_string('viewallresponses', 'questionnaire').'</a>';
    } else {
        echo '<a class="sublink get-remote-content remote-link-action" data-module=\'';
        echo json_encode(array('url' => $CFG->wwwroot . '/mod/questionnaire/report.php?'.
                $argstr, 'params' => array('id' => $questionnaire->cm->id), 'method' => 'get', 'resume' => $resume));
        echo '\' href="#">'.get_string('viewallresponses', 'questionnaire');
        echo '</a>';
    }
    echo $OUTPUT->box_end();
}
if($CFG->nonajax == true ){
    echo $OUTPUT->footer();
}
