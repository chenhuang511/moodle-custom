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
 * Standard library of functions and constants for lesson
 *
 * @package mod_lesson
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

defined('MOODLE_INTERNAL') || die();

/* Do not include any libraries here! */

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @global object
 * @global object
 * @param object $lesson Lesson post data from the form
 * @return int
 **/
function lesson_add_instance($data, $mform)
{
    global $DB;

    $cmid = $data->coursemodule;
    $draftitemid = $data->mediafile;
    $context = context_module::instance($cmid);

    lesson_process_pre_save($data);

    unset($data->mediafile);
    $lessonid = $DB->insert_record("lesson", $data);
    $data->id = $lessonid;

    lesson_update_media_file($lessonid, $context, $draftitemid);

    lesson_process_post_save($data);

    lesson_grade_item_update($data);

    return $lessonid;
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @global object
 * @param object $lesson Lesson post data from the form
 * @return boolean
 **/
function lesson_update_instance($data, $mform)
{
    global $DB;

    $data->id = $data->instance;
    $cmid = $data->coursemodule;
    $draftitemid = $data->mediafile;
    $context = context_module::instance($cmid);

    lesson_process_pre_save($data);

    unset($data->mediafile);
    $DB->update_record("lesson", $data);

    lesson_update_media_file($data->id, $context, $draftitemid);

    lesson_process_post_save($data);

    // update grade item definition
    lesson_grade_item_update($data);

    // update grades - TODO: do it only when grading style changes
    lesson_update_grades($data, 0, false);

    return true;
}

/**
 * This function updates the events associated to the lesson.
 * If $override is non-zero, then it updates only the events
 * associated with the specified override.
 *
 * @uses LESSON_MAX_EVENT_LENGTH
 * @param object $lesson the lesson object.
 * @param object $override (optional) limit to a specific override
 */
function lesson_update_events($lesson, $override = null)
{
    global $CFG, $DB;

    require_once($CFG->dirroot . '/calendar/lib.php');

    // Load the old events relating to this lesson.
    $conds = array('modulename' => 'lesson',
        'instance' => $lesson->id);
    if (!empty($override)) {
        // Only load events for this override.
        if (isset($override->userid)) {
            $conds['userid'] = $override->userid;
        } else {
            $conds['groupid'] = $override->groupid;
        }
    }
    $oldevents = $DB->get_records('event', $conds);

    // Now make a todo list of all that needs to be updated.
    if (empty($override)) {
        // We are updating the primary settings for the lesson, so we
        // need to add all the overrides.
        $params = array();
        $params['parameters[0][name]'] = "lessonid";
        $params['parameters[0][value]'] = $lesson->id;
        $overrides = get_remote_list_lesson_overrides_by($params, "id");
        // As well as the original lesson (empty override).
        $overrides[] = new stdClass();
    } else {
        // Just do the one override.
        $overrides = array($override);
    }

    foreach ($overrides as $current) {
        $groupid = isset($current->groupid) ? $current->groupid : 0;
        $userid = isset($current->userid) ? $current->userid : 0;
        $available = isset($current->available) ? $current->available : $lesson->available;
        $deadline = isset($current->deadline) ? $current->deadline : $lesson->deadline;

        // Only add open/close events for an override if they differ from the lesson default.
        $addopen = empty($current->id) || !empty($current->available);
        $addclose = empty($current->id) || !empty($current->deadline);

        if (!empty($lesson->coursemodule)) {
            $cmid = $lesson->coursemodule;
        } else {
            $cmid = get_coursemodule_from_instance('lesson', $lesson->id, $lesson->course)->id;
        }

        $event = new stdClass();
        $event->description = format_module_intro('lesson', $lesson, $cmid);
        // Events module won't show user events when the courseid is nonzero.
        $event->courseid = ($userid) ? 0 : $lesson->course;
        $event->groupid = $groupid;
        $event->userid = $userid;
        $event->modulename = 'lesson';
        $event->instance = $lesson->id;
        $event->timestart = $available;
        $event->timeduration = max($deadline - $available, 0);
        $event->visible = instance_is_visible('lesson', $lesson);
        $event->eventtype = 'open';

        // Determine the event name.
        if ($groupid) {
            $params = new stdClass();
            $params->lesson = $lesson->name;
            $params->group = groups_get_group_name($groupid);
            if ($params->group === false) {
                // Group doesn't exist, just skip it.
                continue;
            }
            $eventname = get_string('overridegroupeventname', 'lesson', $params);
        } else if ($userid) {
            $params = new stdClass();
            $params->lesson = $lesson->name;
            $eventname = get_string('overrideusereventname', 'lesson', $params);
        } else {
            $eventname = $lesson->name;
        }
        if ($addopen or $addclose) {
            if ($deadline and $available and $event->timeduration <= LESSON_MAX_EVENT_LENGTH) {
                // Single event for the whole lesson.
                if ($oldevent = array_shift($oldevents)) {
                    $event->id = $oldevent->id;
                } else {
                    unset($event->id);
                }
                $event->name = $eventname;
                // The method calendar_event::create will reuse a db record if the id field is set.
                calendar_event::create($event);
            } else {
                // Separate start and end events.
                $event->timeduration = 0;
                if ($available && $addopen) {
                    if ($oldevent = array_shift($oldevents)) {
                        $event->id = $oldevent->id;
                    } else {
                        unset($event->id);
                    }
                    $event->name = $eventname . ' (' . get_string('lessonopens', 'lesson') . ')';
                    // The method calendar_event::create will reuse a db record if the id field is set.
                    calendar_event::create($event);
                }
                if ($deadline && $addclose) {
                    if ($oldevent = array_shift($oldevents)) {
                        $event->id = $oldevent->id;
                    } else {
                        unset($event->id);
                    }
                    $event->name = $eventname . ' (' . get_string('lessoncloses', 'lesson') . ')';
                    $event->timestart = $deadline;
                    $event->eventtype = 'close';
                    calendar_event::create($event);
                }
            }
        }
    }

    // Delete any leftover events.
    foreach ($oldevents as $badevent) {
        $badevent = calendar_event::load($badevent);
        $badevent->delete();
    }
}

/**
 * This standard function will check all instances of this module
 * and make sure there are up-to-date events created for each of them.
 * If courseid = 0, then every lesson event in the site is checked, else
 * only lesson events belonging to the course specified are checked.
 * This function is used, in its new format, by restore_refresh_events()
 *
 * @param int $courseid
 * @return bool
 */
function lesson_refresh_events($courseid = 0)
{
    global $DB;

    if ($courseid == 0) {
        if (!$lessons = get_remote_list_lesson_by()) {
            return true;
        }
    } else {
        $params = array();
        $params['parameters[0][name]'] = "course";
        $params['parameters[0][value]'] = $courseid;
        if (!$lessons = get_remote_list_lesson_by($params)) {
            return true;
        }
    }

    foreach ($lessons as $lesson) {
        lesson_update_events($lesson);
    }

    return true;
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @global object
 * @param int $id
 * @return bool
 */
function lesson_delete_instance($id)
{
    global $DB, $CFG;
    require_once($CFG->dirroot . '/mod/lesson/locallib.php');

    $params = array();
    $params['parameters[0][name]'] = "id";
    $params['parameters[0][value]'] = $id;
    $lesson = new lesson(get_remote_lesson_by($params, '', true));
    return $lesson->delete();
}

/**
 * Given a course object, this function will clean up anything that
 * would be leftover after all the instances were deleted
 *
 * @global object
 * @param object $course an object representing the course that is being deleted
 * @param boolean $feedback to specify if the process must output a summary of its work
 * @return boolean
 */
function lesson_delete_course($course, $feedback = true)
{
    return true;
}

/**
 * Return a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @global object
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $lesson
 * @return object
 */
function lesson_user_outline($course, $user, $mod, $lesson)
{
    global $CFG, $DB;

    require_once("$CFG->libdir/gradelib.php");
    $grades = grade_get_grades($course->id, 'mod', 'lesson', $lesson->id, $user->id);
    $return = new stdClass();

    if (empty($grades->items[0]->grades)) {
        $return->info = get_string("nolessonattempts", "lesson");
    } else {
        $grade = reset($grades->items[0]->grades);
        if (empty($grade->grade)) {

            // Check to see if it an ungraded / incomplete attempt.
            $params = array();
            $params['parameters[0][name]'] = "lessonid";
            $params['parameters[0][value]'] = $lesson->id;
            $params['parameters[1][name]'] = "userid";
            $params['parameters[1][value]'] = $user->id;

            if ($attempts = get_remote_list_lesson_timer_by($params, "starttime DESC", 0, 1)) {
                $attempt = reset($attempts);
                if ($attempt->completed) {
                    $return->info = get_string("completed", "lesson");
                } else {
                    $return->info = get_string("notyetcompleted", "lesson");
                }
                $return->time = $attempt->lessontime;
            } else {
                $return->info = get_string("nolessonattempts", "lesson");
            }
        } else {
            $return->info = get_string("grade") . ': ' . $grade->str_long_grade;

            // Datesubmitted == time created. dategraded == time modified or time overridden.
            // If grade was last modified by the user themselves use date graded. Otherwise use date submitted.
            // TODO: move this copied & pasted code somewhere in the grades API. See MDL-26704.
            if ($grade->usermodified == $user->id || empty($grade->datesubmitted)) {
                $return->time = $grade->dategraded;
            } else {
                $return->time = $grade->datesubmitted;
            }
        }
    }
    return $return;
}

/**
 * Print a detailed representation of what a  user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @global object
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $lesson
 * @return bool
 */
function lesson_user_complete($course, $user, $mod, $lesson)
{
    global $DB, $OUTPUT, $CFG;

    require_once("$CFG->libdir/gradelib.php");

    $grades = grade_get_grades($course->id, 'mod', 'lesson', $lesson->id, $user->id);

    // Display the grade and feedback.
    if (empty($grades->items[0]->grades)) {
        echo $OUTPUT->container(get_string("nolessonattempts", "lesson"));
    } else {
        $grade = reset($grades->items[0]->grades);
        if (empty($grade->grade)) {
            // Check to see if it an ungraded / incomplete attempt.
            $params = array();
            $params['parameters[0][name]'] = "lessonid";
            $params['parameters[0][value]'] = $lesson->id;
            $params['parameters[1][name]'] = "userid";
            $params['parameters[1][value]'] = $user->id;

            if ($attempt = get_remote_list_lesson_timer_by($params, "starttime desc", 0, 1)) {
                if ($attempt->completed) {
                    $status = get_string("completed", "lesson");
                } else {
                    $status = get_string("notyetcompleted", "lesson");
                }
            } else {
                $status = get_string("nolessonattempts", "lesson");
            }
        } else {
            $status = get_string("grade") . ': ' . $grade->str_long_grade;
        }

        // Display the grade or lesson status if there isn't one.
        echo $OUTPUT->container($status);

        if ($grade->str_feedback) {
            echo $OUTPUT->container(get_string('feedback') . ': ' . $grade->str_feedback);
        }
    }

    // Display the lesson progress.
    // Attempt, pages viewed, questions answered, correct answers, time.
    $params = array();
    $params['parameters[0][name]'] = "lessonid";
    $params['parameters[0][value]'] = $lesson->id;
    $params['parameters[1][name]'] = "userid";
    $params['parameters[1][value]'] = $user->id;
    $attempts = get_remote_list_lesson_attempts_by($params,"retry, timeseen");
    $branches = get_remote_list_lesson_branch_by($params, "retry, timeseen");
    if (!empty($attempts) or !empty($branches)) {
        echo $OUTPUT->box_start();
        $table = new html_table();
        // Table Headings.
        $table->head = array(get_string("attemptheader", "lesson"),
            get_string("totalpagesviewedheader", "lesson"),
            get_string("numberofpagesviewedheader", "lesson"),
            get_string("numberofcorrectanswersheader", "lesson"),
            get_string("time"));
        $table->width = "100%";
        $table->align = array("center", "center", "center", "center", "center");
        $table->size = array("*", "*", "*", "*", "*");
        $table->cellpadding = 2;
        $table->cellspacing = 0;

        $retry = 0;
        $nquestions = 0;
        $npages = 0;
        $ncorrect = 0;

        // Filter question pages (from lesson_attempts).
        foreach ($attempts as $attempt) {
            if ($attempt->retry == $retry) {
                $npages++;
                $nquestions++;
                if ($attempt->correct) {
                    $ncorrect++;
                }
                $timeseen = $attempt->timeseen;
            } else {
                $table->data[] = array($retry + 1, $npages, $nquestions, $ncorrect, userdate($timeseen));
                $retry++;
                $nquestions = 1;
                $npages = 1;
                if ($attempt->correct) {
                    $ncorrect = 1;
                } else {
                    $ncorrect = 0;
                }
            }
        }

        // Filter content pages (from lesson_branch).
        foreach ($branches as $branch) {
            if ($branch->retry == $retry) {
                $npages++;

                $timeseen = $branch->timeseen;
            } else {
                $table->data[] = array($retry + 1, $npages, $nquestions, $ncorrect, userdate($timeseen));
                $retry++;
                $npages = 1;
            }
        }
        if ($npages > 0) {
            $table->data[] = array($retry + 1, $npages, $nquestions, $ncorrect, userdate($timeseen));
        }
        echo html_writer::table($table);
        echo $OUTPUT->box_end();
    }

    return true;
}

/**
 * Prints lesson summaries on MyMoodle Page
 *
 * Prints lesson name, due date and attempt information on
 * lessons that have a deadline that has not already passed
 * and it is available for taking.
 *
 * @global object
 * @global stdClass
 * @global object
 * @uses CONTEXT_MODULE
 * @param array $courses An array of course objects to get lesson instances from
 * @param array $htmlarray Store overview output array( course ID => 'lesson' => HTML output )
 * @return void
 */
function lesson_print_overview($courses, &$htmlarray)
{
    global $USER, $CFG, $DB, $OUTPUT;

    if (!$lessons = get_all_instances_in_courses('lesson', $courses)) {
        return;
    }

    // Get all of the current users attempts on all lessons.
    $params = array($USER->id);
    $sql = 'SELECT lessonid, userid, count(userid) as attempts
              FROM {lesson_grades}
             WHERE userid = ?
          GROUP BY lessonid, userid';
    $allattempts = $DB->get_records_sql($sql, $params);
    $completedattempts = array();
    foreach ($allattempts as $myattempt) {
        $completedattempts[$myattempt->lessonid] = $myattempt->attempts;
    }

    // Get the current course ID.
    $listoflessons = array();
    foreach ($lessons as $lesson) {
        $listoflessons[] = $lesson->id;
    }
    // Get the last page viewed by the current user for every lesson in this course.
    list($insql, $inparams) = $DB->get_in_or_equal($listoflessons, SQL_PARAMS_NAMED);
    $dbparams = array_merge($inparams, array('userid' => $USER->id));

    // Get the lesson attempts for the user that have the maximum 'timeseen' value.
    $select = "SELECT l.id, l.timeseen, l.lessonid, l.userid, l.retry, l.pageid, l.answerid as nextpageid, p.qtype ";
    $from = "FROM {lesson_attempts} l
             JOIN (
                   SELECT idselect.lessonid, idselect.userid, MAX(idselect.id) AS id
                     FROM {lesson_attempts} idselect
                     JOIN (
                           SELECT lessonid, userid, MAX(timeseen) AS timeseen
                             FROM {lesson_attempts}
                            WHERE userid = :userid
                              AND lessonid $insql
                         GROUP BY userid, lessonid
                           ) timeselect
                       ON timeselect.timeseen = idselect.timeseen
                      AND timeselect.userid = idselect.userid
                      AND timeselect.lessonid = idselect.lessonid
                 GROUP BY idselect.userid, idselect.lessonid
                   ) aid
               ON l.id = aid.id
             JOIN {lesson_pages} p
               ON l.pageid = p.id ";
    $lastattempts = $DB->get_records_sql($select . $from, $dbparams);

    // Now, get the lesson branches for the user that have the maximum 'timeseen' value.
    $select = "SELECT l.id, l.timeseen, l.lessonid, l.userid, l.retry, l.pageid, l.nextpageid, p.qtype ";
    $from = str_replace('{lesson_attempts}', '{lesson_branch}', $from);
    $lastbranches = $DB->get_records_sql($select . $from, $dbparams);

    $lastviewed = array();
    foreach ($lastattempts as $lastattempt) {
        $lastviewed[$lastattempt->lessonid] = $lastattempt;
    }

    // Go through the branch times and record the 'timeseen' value if it doesn't exist
    // for the lesson, or replace it if it exceeds the current recorded time.
    foreach ($lastbranches as $lastbranch) {
        if (!isset($lastviewed[$lastbranch->lessonid])) {
            $lastviewed[$lastbranch->lessonid] = $lastbranch;
        } else if ($lastviewed[$lastbranch->lessonid]->timeseen < $lastbranch->timeseen) {
            $lastviewed[$lastbranch->lessonid] = $lastbranch;
        }
    }

    // Since we have lessons in this course, now include the constants we need.
    require_once($CFG->dirroot . '/mod/lesson/locallib.php');

    $now = time();
    foreach ($lessons as $lesson) {
        if ($lesson->deadline != 0                                         // The lesson has a deadline
            and $lesson->deadline >= $now                                  // And it is before the deadline has been met
            and ($lesson->available == 0 or $lesson->available <= $now)
        ) { // And the lesson is available

            // Visibility.
            $class = (!$lesson->visible) ? 'dimmed' : '';

            // Context.
            $context = context_module::instance($lesson->coursemodule);

            // Link to activity.
            $url = new moodle_url('/mod/lesson/view.php', array('id' => $lesson->coursemodule));
            $url = html_writer::link($url, format_string($lesson->name, true, array('context' => $context)), array('class' => $class));
            $str = $OUTPUT->box(get_string('lessonname', 'lesson', $url), 'name');

            // Deadline.
            $str .= $OUTPUT->box(get_string('lessoncloseson', 'lesson', userdate($lesson->deadline)), 'info');

            // Attempt information.
            if (has_capability('mod/lesson:manage', $context)) {
                // This is a teacher, Get the Number of user attempts.
                $params = array();
                $params['parameters[0][name]'] = "lessonid";
                $params['parameters[0][value]'] = $lesson->id;
                $attempts = get_remote_count_by("lesson_grades", $params);
                $str .= $OUTPUT->box(get_string('xattempts', 'lesson', $attempts), 'info');
                $str = $OUTPUT->box($str, 'lesson overview');
            } else {
                // This is a student, See if the user has at least started the lesson.
                if (isset($lastviewed[$lesson->id]->timeseen)) {
                    // See if the user has finished this attempt.
                    if (isset($completedattempts[$lesson->id]) &&
                        ($completedattempts[$lesson->id] == ($lastviewed[$lesson->id]->retry + 1))
                    ) {
                        // Are additional attempts allowed?
                        if ($lesson->retake) {
                            // User can retake the lesson.
                            $str .= $OUTPUT->box(get_string('additionalattemptsremaining', 'lesson'), 'info');
                            $str = $OUTPUT->box($str, 'lesson overview');
                        } else {
                            // User has completed the lesson and no retakes are allowed.
                            $str = '';
                        }

                    } else {
                        // The last attempt was not finished or the lesson does not contain questions.
                        // See if the last page viewed was a branchtable.
                        require_once($CFG->dirroot . '/mod/lesson/pagetypes/branchtable.php');
                        if ($lastviewed[$lesson->id]->qtype == LESSON_PAGE_BRANCHTABLE) {
                            // See if the next pageid is the end of lesson.
                            if ($lastviewed[$lesson->id]->nextpageid == LESSON_EOL) {
                                // The last page viewed was the End of Lesson.
                                if ($lesson->retake) {
                                    // User can retake the lesson.
                                    $str .= $OUTPUT->box(get_string('additionalattemptsremaining', 'lesson'), 'info');
                                    $str = $OUTPUT->box($str, 'lesson overview');
                                } else {
                                    // User has completed the lesson and no retakes are allowed.
                                    $str = '';
                                }

                            } else {
                                // The last page viewed was NOT the end of lesson.
                                $str .= $OUTPUT->box(get_string('notyetcompleted', 'lesson'), 'info');
                                $str = $OUTPUT->box($str, 'lesson overview');
                            }

                        } else {
                            // Last page was a question page, so the attempt is not completed yet.
                            $str .= $OUTPUT->box(get_string('notyetcompleted', 'lesson'), 'info');
                            $str = $OUTPUT->box($str, 'lesson overview');
                        }
                    }

                } else {
                    // User has not yet started this lesson.
                    $str .= $OUTPUT->box(get_string('nolessonattempts', 'lesson'), 'info');
                    $str = $OUTPUT->box($str, 'lesson overview');
                }
            }
            if (!empty($str)) {
                if (empty($htmlarray[$lesson->course]['lesson'])) {
                    $htmlarray[$lesson->course]['lesson'] = $str;
                } else {
                    $htmlarray[$lesson->course]['lesson'] .= $str;
                }
            }
        }
    }
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 * @global stdClass
 * @return bool true
 */
function lesson_cron()
{
    global $CFG;

    return true;
}

/**
 * Return grade for given user or all users.
 *
 * @global stdClass
 * @global object
 * @param int $lessonid id of lesson
 * @param int $userid optional user id, 0 means all users
 * @return array array of grades, false if none
 */
function lesson_get_user_grades($lesson, $userid = 0)
{
    global $CFG, $DB;

    $params = array("lessonid" => $lesson->id, "lessonid2" => $lesson->id);

    if (!empty($userid)) {
        $params["userid"] = $userid;
        $params["userid2"] = $userid;
        $user = "AND u.id = :userid";
        $fuser = "AND uu.id = :userid2";
    } else {
        $user = "";
        $fuser = "";
    }

    if ($lesson->retake) {
        if ($lesson->usemaxgrade) {
            $sql = "SELECT u.id, u.id AS userid, MAX(g.grade) AS rawgrade
                      FROM {user} u, {lesson_grades} g
                     WHERE u.id = g.userid AND g.lessonid = :lessonid
                           $user
                  GROUP BY u.id";
        } else {
            $sql = "SELECT u.id, u.id AS userid, AVG(g.grade) AS rawgrade
                      FROM {user} u, {lesson_grades} g
                     WHERE u.id = g.userid AND g.lessonid = :lessonid
                           $user
                  GROUP BY u.id";
        }
        unset($params['lessonid2']);
        unset($params['userid2']);
    } else {
        // use only first attempts (with lowest id in lesson_grades table)
        $firstonly = "SELECT uu.id AS userid, MIN(gg.id) AS firstcompleted
                        FROM {user} uu, {lesson_grades} gg
                       WHERE uu.id = gg.userid AND gg.lessonid = :lessonid2
                             $fuser
                       GROUP BY uu.id";

        $sql = "SELECT u.id, u.id AS userid, g.grade AS rawgrade
                  FROM {user} u, {lesson_grades} g, ($firstonly) f
                 WHERE u.id = g.userid AND g.lessonid = :lessonid
                       AND g.id = f.firstcompleted AND g.userid=f.userid
                       $user";
    }

    return $DB->get_records_sql($sql, $params);
}

/**
 * Update grades in central gradebook
 *
 * @category grade
 * @param object $lesson
 * @param int $userid specific user only, 0 means all
 * @param bool $nullifnone
 */
function lesson_update_grades($lesson, $userid = 0, $nullifnone = true)
{
    global $CFG, $DB;
    require_once($CFG->libdir . '/gradelib.php');

    if ($lesson->grade == 0 || $lesson->practice) {
        lesson_grade_item_update($lesson);

    } else if ($grades = lesson_get_user_grades($lesson, $userid)) {
        lesson_grade_item_update($lesson, $grades);

    } else if ($userid and $nullifnone) {
        $grade = new stdClass();
        $grade->userid = $userid;
        $grade->rawgrade = null;
        lesson_grade_item_update($lesson, $grade);

    } else {
        lesson_grade_item_update($lesson);
    }
}

/**
 * Create grade item for given lesson
 *
 * @category grade
 * @uses GRADE_TYPE_VALUE
 * @uses GRADE_TYPE_NONE
 * @param object $lesson object with extra cmidnumber
 * @param array|object $grades optional array/object of grade(s); 'reset' means reset grades in gradebook
 * @return int 0 if ok, error code otherwise
 */
function lesson_grade_item_update($lesson, $grades = null)
{
    global $CFG;
    if (!function_exists('grade_update')) { //workaround for buggy PHP versions
        require_once($CFG->libdir . '/gradelib.php');
    }

    if (array_key_exists('cmidnumber', $lesson)) { //it may not be always present
        $params = array('itemname' => $lesson->name, 'idnumber' => $lesson->cmidnumber);
    } else {
        $params = array('itemname' => $lesson->name);
    }

    if (!$lesson->practice and $lesson->grade > 0) {
        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax'] = $lesson->grade;
        $params['grademin'] = 0;
    } else if (!$lesson->practice and $lesson->grade < 0) {
        $params['gradetype'] = GRADE_TYPE_SCALE;
        $params['scaleid'] = -$lesson->grade;

        // Make sure current grade fetched correctly from $grades
        $currentgrade = null;
        if (!empty($grades)) {
            if (is_array($grades)) {
                $currentgrade = reset($grades);
            } else {
                $currentgrade = $grades;
            }
        }

        // When converting a score to a scale, use scale's grade maximum to calculate it.
        if (!empty($currentgrade) && $currentgrade->rawgrade !== null) {
            $grade = grade_get_grades($lesson->course, 'mod', 'lesson', $lesson->id, $currentgrade->userid);
            $params['grademax'] = reset($grade->items)->grademax;
        }
    } else {
        $params['gradetype'] = GRADE_TYPE_NONE;
    }

    if ($grades === 'reset') {
        $params['reset'] = true;
        $grades = null;
    } else if (!empty($grades)) {
        // Need to calculate raw grade (Note: $grades has many forms)
        if (is_object($grades)) {
            $grades = array($grades->userid => $grades);
        } else if (array_key_exists('userid', $grades)) {
            $grades = array($grades['userid'] => $grades);
        }
        foreach ($grades as $key => $grade) {
            if (!is_array($grade)) {
                $grades[$key] = $grade = (array)$grade;
            }
            //check raw grade isnt null otherwise we erroneously insert a grade of 0
            if ($grade['rawgrade'] !== null) {
                $grades[$key]['rawgrade'] = ($grade['rawgrade'] * $params['grademax'] / 100);
            } else {
                //setting rawgrade to null just in case user is deleting a grade
                $grades[$key]['rawgrade'] = null;
            }
        }
    }

    return grade_update('mod/lesson', $lesson->course, 'mod', 'lesson', $lesson->id, 0, $grades, $params);
}

/**
 * List the actions that correspond to a view of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = 'r' and edulevel = LEVEL_PARTICIPATING will
 *       be considered as view action.
 *
 * @return array
 */
function lesson_get_view_actions()
{
    return array('view', 'view all');
}

/**
 * List the actions that correspond to a post of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = ('c' || 'u' || 'd') and edulevel = LEVEL_PARTICIPATING
 *       will be considered as post action.
 *
 * @return array
 */
function lesson_get_post_actions()
{
    return array('end', 'start');
}

/**
 * Runs any processes that must run before
 * a lesson insert/update
 *
 * @global object
 * @param object $lesson Lesson form data
 * @return void
 **/
function lesson_process_pre_save(&$lesson)
{
    global $DB;

    $lesson->timemodified = time();

    if (empty($lesson->timelimit)) {
        $lesson->timelimit = 0;
    }
    if (empty($lesson->timespent) or !is_numeric($lesson->timespent) or $lesson->timespent < 0) {
        $lesson->timespent = 0;
    }
    if (!isset($lesson->completed)) {
        $lesson->completed = 0;
    }
    if (empty($lesson->gradebetterthan) or !is_numeric($lesson->gradebetterthan) or $lesson->gradebetterthan < 0) {
        $lesson->gradebetterthan = 0;
    } else if ($lesson->gradebetterthan > 100) {
        $lesson->gradebetterthan = 100;
    }

    if (empty($lesson->width)) {
        $lesson->width = 640;
    }
    if (empty($lesson->height)) {
        $lesson->height = 480;
    }
    if (empty($lesson->bgcolor)) {
        $lesson->bgcolor = '#FFFFFF';
    }

    // Conditions for dependency
    $conditions = new stdClass;
    $conditions->timespent = $lesson->timespent;
    $conditions->completed = $lesson->completed;
    $conditions->gradebetterthan = $lesson->gradebetterthan;
    $lesson->conditions = serialize($conditions);
    unset($lesson->timespent);
    unset($lesson->completed);
    unset($lesson->gradebetterthan);

    if (empty($lesson->password)) {
        unset($lesson->password);
    }
}

/**
 * Runs any processes that must be run
 * after a lesson insert/update
 *
 * @global object
 * @param object $lesson Lesson form data
 * @return void
 **/
function lesson_process_post_save(&$lesson)
{
    // Update the events relating to this lesson.
    lesson_update_events($lesson);
}


/**
 * Implementation of the function for printing the form elements that control
 * whether the course reset functionality affects the lesson.
 *
 * @param $mform form passed by reference
 */
function lesson_reset_course_form_definition(&$mform)
{
    $mform->addElement('header', 'lessonheader', get_string('modulenameplural', 'lesson'));
    $mform->addElement('advcheckbox', 'reset_lesson', get_string('deleteallattempts', 'lesson'));
    $mform->addElement('advcheckbox', 'reset_lesson_user_overrides',
        get_string('removealluseroverrides', 'lesson'));
    $mform->addElement('advcheckbox', 'reset_lesson_group_overrides',
        get_string('removeallgroupoverrides', 'lesson'));
}

/**
 * Course reset form defaults.
 * @param object $course
 * @return array
 */
function lesson_reset_course_form_defaults($course)
{
    return array('reset_lesson' => 1,
        'reset_lesson_group_overrides' => 1,
        'reset_lesson_user_overrides' => 1);
}

/**
 * Removes all grades from gradebook
 *
 * @global stdClass
 * @global object
 * @param int $courseid
 * @param string optional type
 */
function lesson_reset_gradebook($courseid, $type = '')
{
    global $CFG, $DB;

    $sql = "SELECT l.*, cm.idnumber as cmidnumber, l.course as courseid
              FROM {lesson} l, {course_modules} cm, {modules} m
             WHERE m.name='lesson' AND m.id=cm.module AND cm.instance=l.id AND l.course=:course";
    $params = array("course" => $courseid);
    if ($lessons = $DB->get_records_sql($sql, $params)) {
        foreach ($lessons as $lesson) {
            lesson_grade_item_update($lesson, 'reset');
        }
    }
}

/**
 * Actual implementation of the reset course functionality, delete all the
 * lesson attempts for course $data->courseid.
 *
 * @global stdClass
 * @global object
 * @param object $data the data submitted from the reset course.
 * @return array status array
 */
function lesson_reset_userdata($data)
{
    global $CFG, $DB;

    $componentstr = get_string('modulenameplural', 'lesson');
    $status = array();

    if (!empty($data->reset_lesson)) {
        $lessonssql = "SELECT l.id
                         FROM {lesson} l
                        WHERE l.course=:course";

        $params = array();
        $params['parameters[0][name]'] = "course";
        $params['parameters[0][value]'] = $data->courseid;
        $lessons = get_remote_list_ids_lesson_by($params);

        // Get rid of attempts files.
        $fs = get_file_storage();
        if ($lessons) {
            foreach ($lessons as $lessonid => $unused) {
                if (!$cm = get_coursemodule_from_instance('lesson', $lessonid)) {
                    continue;
                }
                $context = context_module::instance($cm->id);
                $fs->delete_area_files($context->id, 'mod_lesson', 'essay_responses');
            }
        }

        $DB->delete_records_select('lesson_timer', "lessonid IN ($lessonssql)", $params);
        $DB->delete_records_select('lesson_grades', "lessonid IN ($lessonssql)", $params);
        $DB->delete_records_select('lesson_attempts', "lessonid IN ($lessonssql)", $params);
        $DB->delete_records_select('lesson_branch', "lessonid IN ($lessonssql)", $params);

        // remove all grades from gradebook
        if (empty($data->reset_gradebook_grades)) {
            lesson_reset_gradebook($data->courseid);
        }

        $status[] = array('component' => $componentstr, 'item' => get_string('deleteallattempts', 'lesson'), 'error' => false);
    }

    // Remove user overrides.
    if (!empty($data->reset_lesson_user_overrides)) {
        $DB->delete_records_select('lesson_overrides',
            'lessonid IN (SELECT id FROM {lesson} WHERE course = ?) AND userid IS NOT NULL', array($data->courseid));
        $status[] = array(
            'component' => $componentstr,
            'item' => get_string('useroverridesdeleted', 'lesson'),
            'error' => false);
    }
    // Remove group overrides.
    if (!empty($data->reset_lesson_group_overrides)) {
        $DB->delete_records_select('lesson_overrides',
            'lessonid IN (SELECT id FROM {lesson} WHERE course = ?) AND groupid IS NOT NULL', array($data->courseid));
        $status[] = array(
            'component' => $componentstr,
            'item' => get_string('groupoverridesdeleted', 'lesson'),
            'error' => false);
    }
    /// updating dates - shift may be negative too
    if ($data->timeshift) {
        $DB->execute("UPDATE {lesson_overrides}
                         SET available = available + ?
                       WHERE lessonid IN (SELECT id FROM {lesson} WHERE course = ?)
                         AND available <> 0", array($data->timeshift, $data->courseid));
        $DB->execute("UPDATE {lesson_overrides}
                         SET deadline = deadline + ?
                       WHERE lessonid IN (SELECT id FROM {lesson} WHERE course = ?)
                         AND deadline <> 0", array($data->timeshift, $data->courseid));

        shift_course_mod_dates('lesson', array('available', 'deadline'), $data->timeshift, $data->courseid);
        $status[] = array('component' => $componentstr, 'item' => get_string('datechanged'), 'error' => false);
    }

    return $status;
}

/**
 * Returns all other caps used in module
 * @return array
 */
function lesson_get_extra_capabilities()
{
    return array('moodle/site:accessallgroups');
}

/**
 * @uses FEATURE_GROUPS
 * @uses FEATURE_GROUPINGS
 * @uses FEATURE_MOD_INTRO
 * @uses FEATURE_COMPLETION_TRACKS_VIEWS
 * @uses FEATURE_GRADE_HAS_GRADE
 * @uses FEATURE_GRADE_OUTCOMES
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function lesson_supports($feature)
{
    switch ($feature) {
        case FEATURE_GROUPS:
            return true;
        case FEATURE_GROUPINGS:
            return true;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return true;
        case FEATURE_COMPLETION_HAS_RULES:
            return true;
        case FEATURE_GRADE_OUTCOMES:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        default:
            return null;
    }
}

/**
 * Obtains the automatic completion state for this lesson based on any conditions
 * in lesson settings.
 *
 * @param object $course Course
 * @param object $cm course-module
 * @param int $userid User ID
 * @param bool $type Type of comparison (or/and; can be used as return value if no conditions)
 * @return bool True if completed, false if not, $type if conditions not set.
 */
function lesson_get_completion_state($course, $cm, $userid, $type)
{
    global $CFG, $DB;

    // Get lesson details.
    $params = array();
    $params['parameters[0][name]'] = "id";
    $params['parameters[0][value]'] = $cm->instance;
    $lesson = get_remote_lesson_by($params, '', true);

    $result = $type; // Default return value.
    // If completion option is enabled, evaluate it and return true/false.
    if ($lesson->completionendreached) {
        $params = array();
        $params['parameters[0][name]'] ="lessonid";
        $params['parameters[0][value]'] = $lesson->id;
        $params['parameters[1][name]'] ="userid";
        $params['parameters[1][value]'] = $userid;
        $params['parameters[2][name]'] ="completed";
        $params['parameters[2][value]'] = 1;
        $value = check_remote_record_exists("lesson_timer", $params);
        if ($type == COMPLETION_AND) {
            $result = $result && $value;
        } else {
            $result = $result || $value;
        }
    }
    if ($lesson->completiontimespent != 0) {
        $duration = get_remote_duration_lesson_timer_by_lessonid_and_userid($lesson->id, $userid);
        if (!$duration) {
            $duration = 0;
        }
        if ($type == COMPLETION_AND) {
            $result = $result && ($lesson->completiontimespent < $duration);
        } else {
            $result = $result || ($lesson->completiontimespent < $duration);
        }
    }
    return $result;
}

/**
 * This function extends the settings navigation block for the site.
 *
 * It is safe to rely on PAGE here as we will only ever be within the module
 * context when this is called
 *
 * @param settings_navigation $settings
 * @param navigation_node $lessonnode
 */
function lesson_extend_settings_navigation($settings, $lessonnode)
{
    global $PAGE, $DB;

    // We want to add these new nodes after the Edit settings node, and before the
    // Locally assigned roles node. Of course, both of those are controlled by capabilities.
    $keys = $lessonnode->get_children_key_list();
    $beforekey = null;
    $i = array_search('modedit', $keys);
    if ($i === false and array_key_exists(0, $keys)) {
        $beforekey = $keys[0];
    } else if (array_key_exists($i + 1, $keys)) {
        $beforekey = $keys[$i + 1];
    }

    if (has_capability('mod/lesson:manageoverrides', $PAGE->cm->context)) {
        $url = new moodle_url('/mod/lesson/remote/api-overrides.php', array('cmid' => $PAGE->cm->id));
        $node = navigation_node::create(get_string('groupoverrides', 'lesson'),
            new moodle_url($url, array('mode' => 'group')),
            navigation_node::TYPE_SETTING, null, 'mod_lesson_groupoverrides');
        $lessonnode->add_node($node, $beforekey);

        $node = navigation_node::create(get_string('useroverrides', 'lesson'),
            new moodle_url($url, array('mode' => 'user')),
            navigation_node::TYPE_SETTING, null, 'mod_lesson_useroverrides');
        $lessonnode->add_node($node, $beforekey);
    }

    if (has_capability('mod/lesson:edit', $PAGE->cm->context)) {
        $url = new moodle_url('/mod/lesson/remote/view.php', array('id' => $PAGE->cm->id));
        $lessonnode->add(get_string('preview', 'lesson'), $url);
        $editnode = $lessonnode->add(get_string('edit', 'lesson'));
        $url = new moodle_url('/mod/lesson/remote/edit.php', array('id' => $PAGE->cm->id, 'mode' => 'collapsed'));
        $editnode->add(get_string('collapsed', 'lesson'), $url);
        $url = new moodle_url('/mod/lesson/remote/edit.php', array('id' => $PAGE->cm->id, 'mode' => 'full'));
        $editnode->add(get_string('full', 'lesson'), $url);
    }

    if (has_capability('mod/lesson:viewreports', $PAGE->cm->context)) {
        $reportsnode = $lessonnode->add(get_string('reports', 'lesson'));
        $url = new moodle_url('/mod/lesson/remote/report.php', array('id' => $PAGE->cm->id, 'action' => 'reportoverview'));
        $reportsnode->add(get_string('overview', 'lesson'), $url);
        $url = new moodle_url('/mod/lesson/remote/report.php', array('id' => $PAGE->cm->id, 'action' => 'reportdetail'));
        $reportsnode->add(get_string('detailedstats', 'lesson'), $url);
    }

    if (has_capability('mod/lesson:grade', $PAGE->cm->context)) {
        $url = new moodle_url('/mod/lesson/remote/essay.php', array('id' => $PAGE->cm->id));
        $lessonnode->add(get_string('manualgrading', 'lesson'), $url);
    }

}

/**
 * Get list of available import or export formats
 *
 * Copied and modified from lib/questionlib.php
 *
 * @param string $type 'import' if import list, otherwise export list assumed
 * @return array sorted list of import/export formats available
 */
function lesson_get_import_export_formats($type)
{
    global $CFG;
    $fileformats = core_component::get_plugin_list("qformat");

    $fileformatname = array();
    foreach ($fileformats as $fileformat => $fdir) {
        $format_file = "$fdir/format.php";
        if (file_exists($format_file)) {
            require_once($format_file);
        } else {
            continue;
        }
        $classname = "qformat_$fileformat";
        $format_class = new $classname();
        if ($type == 'import') {
            $provided = $format_class->provide_import();
        } else {
            $provided = $format_class->provide_export();
        }
        if ($provided) {
            $fileformatnames[$fileformat] = get_string('pluginname', 'qformat_' . $fileformat);
        }
    }
    natcasesort($fileformatnames);

    return $fileformatnames;
}

/**
 * Serves the lesson attachments. Implements needed access control ;-)
 *
 * @package mod_lesson
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if file not found, does not return if found - justsend the file
 */
function lesson_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array())
{
    global $CFG, $DB;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    $fileareas = lesson_get_file_areas();
    if (!array_key_exists($filearea, $fileareas)) {
        return false;
    }

    $params = array();
    $params['parameters[0][name]'] = "id";
    $params['parameters[0][value]'] = $cm->instance;
    if (!$lesson = get_remote_lesson_by($params, '', true)) {
        return false;
    }

    require_course_login($course, true, $cm);

    if ($filearea === 'page_contents') {
        $pageid = (int)array_shift($args);
        $params = array();
        $params['parameters[0][name]'] = "id";
        $params['parameters[0][value]'] = $pageid;
        if (!$page = get_remote_lesson_pages_by($params)) {
            return false;
        }
        $fullpath = "/$context->id/mod_lesson/$filearea/$pageid/" . implode('/', $args);

    } else if ($filearea === 'page_answers' || $filearea === 'page_responses') {
        $itemid = (int)array_shift($args);
        $params = array();
        $params['parameters[0][name]'] = "id";
        $params['parameters[0][value]'] = $itemid;
        if (!$pageanswers = get_remote_lesson_answers_by($params)) {
            return false;
        }
        $fullpath = "/$context->id/mod_lesson/$filearea/$itemid/" . implode('/', $args);

    } else if ($filearea === 'essay_responses') {
        $itemid = (int)array_shift($args);
        $params = array();
        $params['parameters[0][name]'] = "id";
        $params['parameters[0][value]'] = $itemid;
        if (!$attempt = get_remote_lesson_attempts_by($params)) {
            return false;
        }
        $fullpath = "/$context->id/mod_lesson/$filearea/$itemid/" . implode('/', $args);

    } else if ($filearea === 'mediafile') {
        if (count($args) > 1) {
            // Remove the itemid when it appears to be part of the arguments. If there is only one argument
            // then it is surely the file name. The itemid is sometimes used to prevent browser caching.
            array_shift($args);
        }
        $fullpath = "/$context->id/mod_lesson/$filearea/0/" . implode('/', $args);

    } else {
        return false;
    }

    $fs = get_file_storage();
    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        return false;
    }

    // finally send the file
    send_stored_file($file, 0, 0, $forcedownload, $options); // download MUST be forced - security!
}

/**
 * Returns an array of file areas
 *
 * @package  mod_lesson
 * @category files
 * @return array a list of available file areas
 */
function lesson_get_file_areas()
{
    $areas = array();
    $areas['page_contents'] = get_string('pagecontents', 'mod_lesson');
    $areas['mediafile'] = get_string('mediafile', 'mod_lesson');
    $areas['page_answers'] = get_string('pageanswers', 'mod_lesson');
    $areas['page_responses'] = get_string('pageresponses', 'mod_lesson');
    $areas['essay_responses'] = get_string('essayresponses', 'mod_lesson');
    return $areas;
}

/**
 * Returns a file_info_stored object for the file being requested here
 *
 * @package  mod_lesson
 * @category files
 * @global stdClass $CFG
 * @param file_browse $browser file browser instance
 * @param array $areas file areas
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param int $itemid item ID
 * @param string $filepath file path
 * @param string $filename file name
 * @return file_info_stored
 */
function lesson_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename)
{
    global $CFG, $DB;

    if (!has_capability('moodle/course:managefiles', $context)) {
        // No peaking here for students!
        return null;
    }

    // Mediafile area does not have sub directories, so let's select the default itemid to prevent
    // the user from selecting a directory to access the mediafile content.
    if ($filearea == 'mediafile' && is_null($itemid)) {
        $itemid = 0;
    }

    if (is_null($itemid)) {
        return new mod_lesson_file_info($browser, $course, $cm, $context, $areas, $filearea);
    }

    $fs = get_file_storage();
    $filepath = is_null($filepath) ? '/' : $filepath;
    $filename = is_null($filename) ? '.' : $filename;
    if (!$storedfile = $fs->get_file($context->id, 'mod_lesson', $filearea, $itemid, $filepath, $filename)) {
        return null;
    }

    $itemname = $filearea;
    if ($filearea == 'page_contents') {
        $params = array();
        $params['parameters[0][name]'] = "lessonid";
        $params['parameters[0][value]'] = $cm->instance;
        $params['parameters[1][name]'] = "id";
        $params['parameters[1][value]'] = $itemid;
        $itemname = get_remote_field_by("lesson_pages", $params, "title");
        $itemname = format_string($itemname, true, array('context' => $context));
    } else {
        $areas = lesson_get_file_areas();
        if (isset($areas[$filearea])) {
            $itemname = $areas[$filearea];
        }
    }

    $urlbase = $CFG->wwwroot . '/pluginfile.php';
    return new file_info_stored($browser, $context, $storedfile, $urlbase, $itemname, $itemid, true, true, false);
}


/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function lesson_page_type_list($pagetype, $parentcontext, $currentcontext)
{
    $module_pagetype = array(
        'mod-lesson-*' => get_string('page-mod-lesson-x', 'lesson'),
        'mod-lesson-view' => get_string('page-mod-lesson-view', 'lesson'),
        'mod-lesson-edit' => get_string('page-mod-lesson-edit', 'lesson'));
    return $module_pagetype;
}

/**
 * Update the lesson activity to include any file
 * that was uploaded, or if there is none, set the
 * mediafile field to blank.
 *
 * @param int $lessonid the lesson id
 * @param stdClass $context the context
 * @param int $draftitemid the draft item
 */
function lesson_update_media_file($lessonid, $context, $draftitemid)
{
    global $DB;

    // Set the filestorage object.
    $fs = get_file_storage();
    // Save the file if it exists that is currently in the draft area.
    file_save_draft_area_files($draftitemid, $context->id, 'mod_lesson', 'mediafile', 0);
    // Get the file if it exists.
    $files = $fs->get_area_files($context->id, 'mod_lesson', 'mediafile', 0, 'itemid, filepath, filename', false);
    // Check that there is a file to process.
    if (count($files) == 1) {
        // Get the first (and only) file.
        $file = reset($files);
        // Set the mediafile column in the lessons table.
        $DB->set_field('lesson', 'mediafile', '/' . $file->get_filename(), array('id' => $lessonid));
    } else {
        // Set the mediafile column in the lessons table.
        $DB->set_field('lesson', 'mediafile', '', array('id' => $lessonid));
    }
}

