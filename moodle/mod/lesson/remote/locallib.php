<?php
defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/lib/remote/lib.php');
require_once($CFG->dirroot . '/lib/additionallib.php');
require_once($CFG->dirroot . '/mnet/service/enrol/locallib.php');

/**
 * get lesson by id
 *
 * @param int $lessonid . the id of lesson
 * @param array $options . the options
 *
 * @return stdClass $lesson
 */
function get_remote_lesson_by_id($lessonid, $options = array())
{
    return moodle_webservice_client(array_merge($options,
        array(
            'domain' => HUB_URL,
            'token' => HOST_TOKEN,
            'function_name' => 'local_mod_get_lesson_by_id',
            'params' => array('lessonid' => $lessonid),
        )
    ));
}

/**
 * get lesson page by lessonid and previous page id
 *
 * @param int $lessonid . The id of lesson
 * @param int $prevpageid . The previous page id
 * @param array $options
 *
 * @return stdClass $lesson_page
 */
function get_remote_lesson_pages_by_lessonid_and_prevpageid($lessonid, $prevpageid, $options = array())
{
    return moodle_webservice_client(array_merge($options,
        array(
            'domain' => HUB_URL,
            'token' => HOST_TOKEN,
            'function_name' => 'local_mod_get_lesson_pages_by_lessonid_and_prevpageid',
            'params' => array('lessonid' => $lessonid, 'prevpageid' => $prevpageid)
        )
    ));
}

/**
 * Get field of lesson pages by lessonid and prevpageid
 *
 * @param $lessonid
 * @param $prevpageid
 * @param array $options
 * @return false|mixed
 */
function get_remote_field_lesson_pages_by_lessonid_and_prevpageid($lessonid, $prevpageid, $options = array())
{
    return moodle_webservice_client(array_merge($options,
        array(
            'domain' => HUB_URL,
            'token' => HOST_TOKEN,
            'function_name' => 'local_mod_get_field_lesson_pages_by_lessonid_and_prevpageid',
            'params' => array('lessonid' => $lessonid, 'prevpageid' => $prevpageid)
        )
    ));
}

/**
 * get lesson page by pageid and lessonid
 *
 * @param int @pageid. The id of lesson page
 * @param int @lessonid. The id of lesson
 * @param array $options
 *
 * @return stdClass $lesson_page
 */
function get_remote_lesson_pages_by_pageid_and_lessonid($pageid, $lessonid, $options = array())
{
    return moodle_webservice_client(array_merge($options,
        array(
            'domain' => HUB_URL,
            'token' => HOST_TOKEN,
            'function_name' => 'local_mod_get_lesson_pages_by_pageid_and_lessonid',
            'params' => array('pageid' => $pageid, 'lessonid' => $lessonid)
        )
    ));
}

/**
 * get lesson timer by userid and lessonid
 *
 * @param int $userid . the id of user
 * @param int $lessonid . the id of lesson
 * @param array $options
 *
 * @return stdClass $lesson_timer
 */
function get_remote_lesson_timer_by_userid_and_lessonid($userid, $lessonid, $options = array())
{
    return moodle_webservice_client(array_merge($options,
        array(
            'domain' => HUB_URL,
            'token' => HOST_TOKEN,
            'function_name' => 'local_get_lesson_timer_by_userid_and_lessonid',
            'params' => array('userid' => $userid, 'lessonid' => $lessonid)
        )
    ));
}

/**
 * get lesson branch by lessonid and userid and retry
 *
 * @param int $lessonid . the id of lesson
 * @param int $userid . the id of user
 * @param int $retry
 * @param array $options
 *
 * @return stdClass $lesson_branch
 */
function get_remote_lesson_branch_by_lessonid_and_userid_and_retry($lessonid, $userid, $retry, $options = array())
{
    return moodle_webservice_client(array_merge($options,
        array(
            'domain' => HUB_URL,
            'token' => HOST_TOKEN,
            'function_name' => 'local_mod_get_lesson_branch_by_lessonid_and_userid_and_retry',
            'params' => array('lessonid' => $lessonid, 'userid' => $userid, 'retry' => $retry)
        )
    ));
}

/**
 * Get retries
 *
 * @param $tablename
 * @param $lessonid
 * @param int $userid
 * @param int $retry
 * @param string $orderby
 * @param array $options
 * @return false|mixed
 */
function get_remote_count_by_lessonid_and_userid($tablename, $lessonid, $userid = 0, $retry = -1, $orderby = '', $options = array())
{
    return moodle_webservice_client(array_merge($options,
        array(
            'domain' => HUB_URL,
            'token' => HOST_TOKEN,
            'function_name' => 'local_mod_get_count_lessonid_and_userid',
            'params' => array('tablename' => $tablename, 'lessonid' => $lessonid, 'userid' => $userid, 'retry' => $retry, 'orderby' => $orderby)
        )
    ));
}

/**
 * Get lesson attempts by lessonid and userid
 *
 * @param $lessonid
 * @param $userid
 * @param $retry
 * @param int $correct
 * @param int $pageid
 * @param string $orderby
 * @param array $options
 * @return false|mixed
 */
function get_remote_lesson_attempts_by_lessonid_and_userid($lessonid, $userid, $retry, $correct = 0, $pageid = -1, $orderby = 'asc', $options = array())
{
    return moodle_webservice_client(array_merge($options,
        array(
            'domain' => HUB_URL,
            'token' => HOST_TOKEN,
            'function_name' => 'local_mod_get_lesson_attempts_by_lessonid_and_userid',
            'params' => array('lessonid' => $lessonid, 'userid' => $userid, 'retry' => $retry, 'correct' => $correct, 'pageid' => $pageid, 'orderby' => $orderby)
        )
    ));
}

/**
 * Get lesson answers by pageid and lessonid
 *
 * @param $pageid
 * @param $lessonid
 * @param array $options
 * @return false|mixed
 */
function get_remote_lesson_answers_by_pageid_and_lessonid($pageid, $lessonid, $options = array())
{
    return moodle_webservice_client(array_merge($options,
        array(
            'domain' => HUB_URL,
            'token' => HOST_TOKEN,
            'function_name' => 'local_mod_get_lesson_answer_by_pageid_and_lessonid',
            'params' => array('pageid' => $pageid, 'lessonid' => $lessonid)
        )
    ));
}

/**
 * get lesson answers by id
 *
 * @param $id
 * @param array $options
 * @return false|mixed
 */
function get_remote_lesson_answers_by_id($id, $options = array())
{
    return moodle_webservice_client(array_merge($options,
        array(
            'domain' => HUB_URL,
            'token' => HOST_TOKEN,
            'function_name' => 'local_mod_get_lesson_answers_by_id',
            'params' => array('id' => $id)
        )
    ));
}

/**
 * Get lesson answers by lessonid
 *
 * @param $lessonid
 * @param array $options
 * @return false|mixed
 */
function get_remote_lesson_answers_by_lessonid($lessonid, $options = array())
{
    return moodle_webservice_client(array_merge($options,
        array(
            'domain' => HUB_URL,
            'token' => HOST_TOKEN,
            'function_name' => 'local_mod_get_lesson_answers_by_lessonid',
            'params' => array('lessonid' => $lessonid)
        )
    ));
}

/**
 * Get lesson grades by lessonid and userid
 *
 * @param $lessonid
 * @param $userid
 * @param array $options
 * @return false|mixed
 */
function get_remote_lesson_grades_by_lessonid_and_userid($lessonid, $userid, $options = array())
{
    return moodle_webservice_client(array_merge($options,
        array(
            'domain' => HUB_URL,
            'token' => HOST_TOKEN,
            'function_name' => 'local_mod_get_lesson_grades_by_lessonid_and_userid',
            'params' => array('lessonid' => $lessonid, 'userid' => $userid)
        )
    ));
}

/**
 * create new a lesson branch
 *
 * @param $branch
 * @return false|mixed
 */
function save_remote_lesson_branch($branch)
{
    return moodle_webservice_client(
        array(
            'domain' => HUB_URL,
            'token' => HOST_TOKEN,
            'function_name' => 'local_mod_save_lesson_branch',
            'params' => array('data' => $branch)
        )
    );
}

/**
 * Get field of lesson page by id
 *
 * @param $id
 * @param string $field
 * @return false|mixed
 */
function get_remote_get_field_lesson_pages_by_id($id, $field = 'title')
{
    return moodle_webservice_client(
        array(
            'domain' => HUB_URL,
            'token' => HOST_TOKEN,
            'function_name' => 'local_mod_get_field_lesson_pages_by_id',
            'params' => array('id' => $id, 'field' => $field)
        )
    );
}