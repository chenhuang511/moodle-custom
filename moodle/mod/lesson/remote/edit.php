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
 * Provides the interface for overall authoring of lessons
 *
 * @package mod_lesson
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/
require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->dirroot . '/lib/remote/lib.php');
require_once($CFG->dirroot . '/course/remote/locallib.php');
require_once($CFG->dirroot . '/mod/lesson/locallib.php');
require_once($CFG->dirroot . '/mod/lesson/remote/locallib.php');

$id = required_param('id', PARAM_INT);

$cm = get_remote_course_module_by_cmid('lesson', $id);
$course = get_local_course_record($cm->course, true);

$params = array();
$params['parameters[0][name]'] = "id";
$params['parameters[0][value]'] = $cm->instance;
$lesson = new lesson(get_remote_lesson_by($params, '', true));

require_login($course, false, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/lesson:manage', $context);

$mode = optional_param('mode', get_user_preferences('lesson_view', 'collapsed'), PARAM_ALPHA);
$PAGE->set_url('/mod/lesson/remote/edit.php', array('id' => $cm->id, 'mode' => $mode));

if ($mode != get_user_preferences('lesson_view', 'collapsed') && $mode !== 'single') {
    set_user_preference('lesson_view', $mode);
}

$lessonoutput = $PAGE->get_renderer('mod_lesson');
$PAGE->navbar->add(get_string('edit'));
echo $lessonoutput->header($lesson, $cm, $mode, false, null, get_string('edit', 'lesson'));

if (!$lesson->has_pages()) {
    // There are no pages; give teacher some options
    require_capability('mod/lesson:edit', $context);
    echo $lessonoutput->add_first_page_links($lesson);
} else {
    switch ($mode) {
        case 'collapsed':
            echo $lessonoutput->display_edit_collapsed($lesson, $lesson->firstpageid);
            break;
        case 'single':
            $pageid = required_param('pageid', PARAM_INT);
            $PAGE->url->param('pageid', $pageid);
            $singlepage = $lesson->load_page($pageid);
            echo $lessonoutput->display_edit_full($lesson, $singlepage->id, $singlepage->prevpageid, true);
            break;
        case 'full':
            echo $lessonoutput->display_edit_full($lesson, $lesson->firstpageid, 0);
            break;
    }
}

echo $lessonoutput->footer();
