<?php
defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/lib/remote/lib.php');
require_once($CFG->dirroot . '/lib/additionallib.php');
require_once($CFG->dirroot . '/mnet/service/enrol/locallib.php');

function get_remote_enrol_course_by_host()
{
    global $DB;
    $service = mnetservice_enrol::get_instance();

    if (!$service->is_available()) {
        print_error('mnetserviceisnotenabled', 'mnetservice_enrol');
        return null;
    }

    // remote hosts that may publish remote enrolment service and we are subscribed to it
    $hosts = $service->get_remote_publishers();
    $host = new StdClass();
    foreach ($hosts as $h) {
        $host = $h;
        break;
    }

    $courseids = $DB->get_records('course', array('hostid' => $host->id), '', 'remoteid');
    $opids = array();
    $i = 0;
    foreach ($courseids as $key => $val) {
        $opids['options[ids][' . $i++ . ']'] = $key;
    }
    $options = array('params' => $opids);

    return get_remote_courses($options);
}

function get_remote_courses($options = array())
{
    return moodle_webservice_client(array_merge($options, array('domain' => HUB_URL,
        'token' => HOST_TOKEN_M,
        'function_name' => 'core_course_get_courses'
    )));
}

function get_remote_course_content($courseid, $options = array())
{
    $coursedetail = moodle_webservice_client(array_merge($options, array('domain' => HUB_URL,
        'token' => HOST_TOKEN,
        'function_name' => 'local_get_course_content_by_id',
        'params' => array('courseid' => $courseid),
    )));

    return array('courseid' => $courseid, 'content' => $coursedetail);
}

function get_remote_course_category($ccatid, $options = array())
{
    return moodle_webservice_client(array_merge($options, array('domain' => HUB_URL,
        'token' => HOST_TOKEN,
        'function_name' => 'core_course_get_categories',
        'params' => array('criteria[0][key]' => 'id', 'criteria[0][value]' => $ccatid),
    )));
}

function get_remote_label_content($labelid, $options = [])
{
    return moodle_webservice_client(array_merge($options, array('domain' => HUB_URL,
        'token' => HOST_TOKEN,
        'function_name' => 'local_mod_get_label_by_id',
        'params' => array('labelid' => $labelid),
    )));
}

function get_remote_page_content($pageid, $options = [])
{
    return moodle_webservice_client(array_merge($options, array('domain' => HUB_URL,
        'token' => HOST_TOKEN,
        'function_name' => 'local_mod_get_page_by_id',
        'params' => array('pageid' => $pageid),
    )));
}

function get_remote_quiz_content($quizid, $options = [])
{
    return moodle_webservice_client(array_merge($options, array('domain' => HUB_URL,
        'token' => HOST_TOKEN,
        'function_name' => 'local_mod_get_quiz_by_id',
        'params' => array('quizid' => $quizid),
    )));
}

function get_remote_url_content($urlid, $options = [])
{
    return moodle_webservice_client(array_merge($options,
        array(
            'domain' => HUB_URL,
            'token' => HOST_TOKEN,
            'function_name' => 'local_mod_get_url_by_id',
            'params' => array('urlid' => $urlid),
        )
    ));
}

function get_remote_course_module_by_instance($modulename, $instace, $options = array())
{
    $resp = moodle_webservice_client(array_merge($options,
        array(
            'domain' => HUB_URL,
            'token' => HOST_TOKEN_M,
            'function_name' => 'core_course_get_course_module_by_instance',
            'params' => array('module' => $modulename, 'instance' => $instace)
        )
    ));

    return $resp->cm;
}

function get_remote_course_module_by_cmid($modulename, $cmid, $options = array())
{
    $resp = moodle_webservice_client(array_merge($options,
        array(
            'domain' => HUB_URL,
            'token' => HOST_TOKEN,
            'function_name' => 'local_get_course_module_by_cmid',
            'params' => array('module' => $modulename, 'id' => $cmid)
        )
    ));

    return $resp->cm;
}

function get_remote_core_course_get_course_module($cmid)
{
    $result = moodle_webservice_client(
        array(
            'domain' => HUB_URL,
            'token' => HOST_TOKEN_M,
            'function_name' => 'core_course_get_course_module',
            'params' => array('cmid' => $cmid)
        )
    );

    return $result->cm;
}

function get_remote_course_info($courseid, $options = array())
{
    $courseinfo = moodle_webservice_client(array_merge($options,
        array(
            'domain' => HUB_URL,
            'token' => HOST_TOKEN,
            'function_name' => 'local_get_course_info_by_course_id',
            'params' => array('courseid' => $courseid),
        )
    ));

    return $courseinfo;
}

function get_remote_name_modules_by_id($id)
{
    return moodle_webservice_client(
        array(
            'domain' => HUB_URL,
            'token' => HOST_TOKEN,
            'function_name' => 'local_get_name_modules_by_id',
            'params' => array('id' => $id),
        )
    );
}

function get_remote_modules_by_id($id)
{
    return moodle_webservice_client(
        array(
            'domain' => HUB_URL,
            'token' => HOST_TOKEN,
            'function_name' => 'local_get_modules_by_id',
            'params' => array('id' => $id),
        )
    );
}

function get_remote_course_section_nav_by_section($sectionid)
{
    return moodle_webservice_client(
        array(
            'domain' => HUB_URL,
            'token' => HOST_TOKEN,
            'function_name' => 'local_get_remote_course_section_nav',
            'params' => array('sectionid' => $sectionid),
        )
    );
}

function get_remote_course_format_options($courseid, $format, $sectionid, $assockey = false)
{
    global $DB;
    $remotecourseid = $DB->get_field('course', 'remoteid', array('id' => $courseid), MUST_EXIST);
    $result = moodle_webservice_client(
        array(
            'domain' => HUB_URL,
            'token' => HOST_TOKEN,
            'function_name' => 'local_get_remote_course_format_options',
            'params' => array('courseid' => $remotecourseid, 'format' => $format, 'sectionid' => $sectionid),
        )
    );

    if ($assockey) {
        $result = change_key_by_value($result, $assockey);
    }

    return $result;
}
