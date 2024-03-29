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
require_once($CFG->dirroot . '/course/externallib.php');

/**
 * Course external functions
 *
 * @package    core_course
 * @category   external
 * @copyright  2011 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.2
 */
class local_course_external extends external_api
{

    //region _VIETNH
    /**
     * VietNH 23-05-2016
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.9 Options available
     * @since Moodle 2.2
     */
    public static function get_course_content_by_id_parameters()
    {
        return new external_function_parameters(
            array('courseid' => new external_value(PARAM_INT, 'course id'),
                'options' => new external_multiple_structure (
                    new external_single_structure(
                        array(
                            'name' => new external_value(PARAM_ALPHANUM,
                                'The expected keys (value format) are:
                                                excludemodules (bool) Do not return modules, return only the sections structure
                                                excludecontents (bool) Do not return module contents (i.e: files inside a resource)
                                                sectionid (int) Return only this section
                                                sectionnumber (int) Return only this section with number (order)
                                                cmid (int) Return only this module information (among the whole sections structure)
                                                modname (string) Return only modules with this name "label, forum, etc..."
                                                modid (int) Return only the module with this id (to be used with modname'),
                            'value' => new external_value(PARAM_RAW, 'the value of the option,
                                                                    this param is personaly validated in the external function.')
                        )
                    ), 'Options, used since Moodle 2.9', VALUE_DEFAULT, array())
            )
        );
    }

    public static function get_course_content_by_id($courseid, $options = array())
    {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/course/lib.php");
        //validate parameter
        $params = self::validate_parameters(self::get_course_content_by_id_parameters(),
            array('courseid' => $courseid, 'options' => $options));

        $filters = array();
        if (!empty($params['options'])) {
            foreach ($params['options'] as $option) {
                $name = trim($option['name']);
                // Avoid duplicated options.
                if (!isset($filters[$name])) {
                    switch ($name) {
                        case 'excludemodules':
                        case 'excludecontents':
                            $value = clean_param($option['value'], PARAM_BOOL);
                            $filters[$name] = $value;
                            break;
                        case 'sectionid':
                        case 'sectionnumber':
                        case 'cmid':
                        case 'modid':
                            $value = clean_param($option['value'], PARAM_INT);
                            if (is_numeric($value)) {
                                $filters[$name] = $value;
                            } else {
                                throw new moodle_exception('errorinvalidparam', 'webservice', '', $name);
                            }
                            break;
                        case 'modname':
                            $value = clean_param($option['value'], PARAM_PLUGIN);
                            if ($value) {
                                $filters[$name] = $value;
                            } else {
                                throw new moodle_exception('errorinvalidparam', 'webservice', '', $name);
                            }
                            break;
                        default:
                            throw new moodle_exception('errorinvalidparam', 'webservice', '', $name);
                    }
                }
            }
        }

        //retrieve the course
        $course = $DB->get_record('course', array('id' => $params['courseid']), '*', MUST_EXIST);

        // now security checks
        $context = context_course::instance($course->id, IGNORE_MISSING);

        //create return value
        $coursecontents = array();

        if ($course->visible) {
            //retrieve sections
            $modinfo = get_fast_modinfo($course);
            $sections = $modinfo->get_section_info_all();

            //for each sections (first displayed to last displayed)
            $modinfosections = $modinfo->get_sections();
            foreach ($sections as $key => $section) {

                if (!$section->uservisible) {
                    continue;
                }

                // This becomes true when we are filtering and we found the value to filter with.
                $sectionfound = false;

                // Filter by section id.
                if (!empty($filters['sectionid'])) {
                    if ($section->id != $filters['sectionid']) {
                        continue;
                    } else {
                        $sectionfound = true;
                    }
                }

                // Filter by section number. Note that 0 is a valid section number.
                if (isset($filters['sectionnumber'])) {
                    if ($key != $filters['sectionnumber']) {
                        continue;
                    } else {
                        $sectionfound = true;
                    }
                }

                // reset $sectioncontents
                $sectionvalues = array();
                $sectionvalues['id'] = $section->id;
                $sectionvalues['name'] = get_section_name($course, $section);
                $sectionvalues['visible'] = $section->visible;
                list($sectionvalues['summary'], $sectionvalues['summaryformat']) =
                    external_format_text($section->summary, $section->summaryformat,
                        $context->id, 'course', 'section', $section->id);
                $sectioncontents = array();

                //for each module of the section
                if (empty($filters['excludemodules']) and !empty($modinfosections[$section->section])) {
                    foreach ($modinfosections[$section->section] as $cmid) {
                        $cm = $modinfo->cms[$cmid];

                        // stop here if the module is not visible to the user
                        if (!$cm->uservisible) {
                            continue;
                        }

                        // This becomes true when we are filtering and we found the value to filter with.
                        $modfound = false;

                        // Filter by cmid.
                        if (!empty($filters['cmid'])) {
                            if ($cmid != $filters['cmid']) {
                                continue;
                            } else {
                                $modfound = true;
                            }
                        }

                        // Filter by module name and id.
                        if (!empty($filters['modname'])) {
                            if ($cm->modname != $filters['modname']) {
                                continue;
                            } else if (!empty($filters['modid'])) {
                                if ($cm->instance != $filters['modid']) {
                                    continue;
                                } else {
                                    // Note that if we are only filtering by modname we don't break the loop.
                                    $modfound = true;
                                }
                            }
                        }

                        $module = array();

                        $modcontext = context_module::instance($cm->id);

                        //common info (for people being able to see the module or availability dates)
                        $module['id'] = $cm->id;
                        $module['name'] = external_format_string($cm->name, $modcontext->id);
                        $module['instance'] = $cm->instance;
                        $module['modname'] = $cm->modname;
                        $module['modplural'] = $cm->modplural;
                        $module['modicon'] = $cm->get_icon_url()->out(false);
                        $module['indent'] = $cm->indent;
                        $module['description'] = $cm->content;
                        //url of the module
                        $url = $cm->url;
                        if ($url) $module['url'] = $url->out(false);


                        //user that can view hidden module should know about the visibility
                        $module['visible'] = $cm->visible;

                        // Availability date (also send to user who can see hidden module).
                        if ($CFG->enableavailability) $module['availability'] = $cm->availability;

                        $baseurl = 'webservice/pluginfile.php';

                        //call $modulename_export_contents
                        //(each module callback take care about checking the capabilities)

                        require_once($CFG->dirroot . '/mod/' . $cm->modname . '/lib.php');
                        $getcontentfunction = $cm->modname . '_export_contents';
                        if (function_exists($getcontentfunction)) {
                            if (empty($filters['excludecontents']) and $contents = $getcontentfunction($cm, $baseurl)) {
                                $module['contents'] = $contents;
                            } else {
                                $module['contents'] = array();
                            }
                        }

                        //assign result to $sectioncontents
                        $sectioncontents[] = $module;

                        // If we just did a filtering, break the loop.
                        if ($modfound) {
                            break;
                        }

                    }
                }
                $sectionvalues['modules'] = $sectioncontents;

                // assign result to $coursecontents
                $coursecontents[] = $sectionvalues;

                // Break the loop if we are filtering.
                if ($sectionfound) {
                    break;
                }
            }
        }
        return $coursecontents;
    }

    public static function get_course_content_by_id_returns()
    {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'Section ID'),
                    'name' => new external_value(PARAM_TEXT, 'Section name'),
                    'visible' => new external_value(PARAM_INT, 'is the section visible', VALUE_OPTIONAL),
                    'summary' => new external_value(PARAM_RAW, 'Section description'),
                    'summaryformat' => new external_format_value('summary'),
                    'modules' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'id' => new external_value(PARAM_INT, 'activity id'),
                                'url' => new external_value(PARAM_URL, 'activity url', VALUE_OPTIONAL),
                                'name' => new external_value(PARAM_RAW, 'activity module name'),
                                'instance' => new external_value(PARAM_INT, 'instance id', VALUE_OPTIONAL),
                                'description' => new external_value(PARAM_RAW, 'activity description', VALUE_OPTIONAL),
                                'visible' => new external_value(PARAM_INT, 'is the module visible', VALUE_OPTIONAL),
                                'modicon' => new external_value(PARAM_URL, 'activity icon url'),
                                'modname' => new external_value(PARAM_PLUGIN, 'activity module type'),
                                'modplural' => new external_value(PARAM_TEXT, 'activity module plural name'),
                                'availability' => new external_value(PARAM_RAW, 'module availability settings', VALUE_OPTIONAL),
                                'indent' => new external_value(PARAM_INT, 'number of identation in the site'),
                                'contents' => new external_multiple_structure(
                                    new external_single_structure(
                                        array(
                                            // content info
                                            'type' => new external_value(PARAM_TEXT, 'a file or a folder or external link'),
                                            'filename' => new external_value(PARAM_FILE, 'filename'),
                                            'filepath' => new external_value(PARAM_PATH, 'filepath'),
                                            'filesize' => new external_value(PARAM_INT, 'filesize'),
                                            'fileurl' => new external_value(PARAM_URL, 'downloadable file url', VALUE_OPTIONAL),
                                            'content' => new external_value(PARAM_RAW, 'Raw content, will be used when type is content', VALUE_OPTIONAL),
                                            'timecreated' => new external_value(PARAM_INT, 'Time created'),
                                            'timemodified' => new external_value(PARAM_INT, 'Time modified'),
                                            'sortorder' => new external_value(PARAM_INT, 'Content sort order'),

                                            // copyright related info
                                            'userid' => new external_value(PARAM_INT, 'User who added this content to moodle'),
                                            'author' => new external_value(PARAM_TEXT, 'Content owner'),
                                            'license' => new external_value(PARAM_TEXT, 'Content license'),
                                        )
                                    ), VALUE_DEFAULT, array()
                                )
                            )
                        ), 'list of module'
                    )
                )
            )
        );
    }

    /** MINHND
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function get_course_thumbnail_by_id_parameters()
    {
        return new external_function_parameters(
            array(
                'courseids' => new external_multiple_structure(new external_value(PARAM_INT, 'course ID')),
            )
        );
    }

    /**
     * Get thumbnail course information
     *
     * @param array $courseid array of course ids
     * @return array An array of arrays thumbnail thumbnail
     */
    public static function get_course_thumbnail_by_id($courseids)
    {
        global $CFG, $COURSE, $DB;
        require_once($CFG->dirroot . "/course/lib.php");
        //validate parameter
        $params = self::validate_parameters(self::get_course_thumbnail_by_id_parameters(), array('courseids' => $courseids));

        // Clean the values.
        $cleanedvalues = array();
        foreach ($courseids as $courseid) {
            $cleanedvalue = clean_param($courseid, PARAM_INT);
            if ($courseid != $cleanedvalue) {
                throw new invalid_parameter_exception('Courseid is invalid: ' . $courseid . '(cleaned value: ' . $cleanedvalue . ')');
            }
            $cleanedvalues[] = $cleanedvalue;
        }

        // Retrieve the courses.
        $courses = $DB->get_records_list('course', 'id', $cleanedvalues, 'id');
        $context = context_system::instance();
        self::validate_context($context);

        // Finally retrieve each courses information.
        $returnedcourses = array();

        foreach ($courses as $course) {
            $coursedetails = course_get_thumbnail($course);

            if (!empty($coursedetails)) {
                $returnedcourses[] = $coursedetails;
            }
        }
        return $returnedcourses;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.2
     * @deprecated Moodle 2.5 MDL-38030 - Please do not call this function any more.
     * @see core_user_external::get_users_by_field_returns()
     */
    public static function get_course_thumbnail_by_id_returns()
    {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'ID of the course'),
                    'fullname' => new external_value(PARAM_RAW, 'The fullname of the course'),
                    'thumbnail_image' => new external_value(PARAM_URL, 'Thumbnail course URL - small version'),
                )
            )
        );
    }

    public static function get_remote_course_mods_parameters()
    {
        return new external_function_parameters(
            array('courseid' => new external_value(PARAM_INT, 'course id'),
            )
        );
    }

    public static function get_remote_course_mods($courseid)
    {
        global $DB;
        $warnings = array();

        //validate parameter
        $params = self::validate_parameters(self::get_remote_course_mods_parameters(), array(
            'courseid' => $courseid
        ));
        $sql = "SELECT cm.*, m.name as modname
                                       FROM {modules} m, {course_modules} cm
                                      WHERE cm.course = ? AND cm.module = m.id AND m.visible = 1";

        $coursemodules = $DB->get_records_sql($sql, array($params['courseid']));

        if (!$coursemodules) {
            $coursemodules = array();
        }

        $result = array();
        $result['coursemodules'] = $coursemodules;
        $result['warnings'] = $warnings;

        return $result;
    }

    public static function get_remote_course_mods_returns()
    {
        return new external_single_structure(
            array(
                'coursemodules' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'the id of course module'),
                            'course' => new external_value(PARAM_INT, 'the id of course'),
                            'module' => new external_value(PARAM_INT, 'the id of module'),
                            'instance' => new external_value(PARAM_INT, 'the instance'),
                            'section' => new external_value(PARAM_INT, 'the id of section'),
                            'idnumber' => new external_value(PARAM_TEXT, 'the id number'),
                            'added' => new external_value(PARAM_INT, 'the added'),
                            'score' => new external_value(PARAM_INT, 'The score'),
                            'indent' => new external_value(PARAM_INT, 'the indent'),
                            'visible' => new external_value(PARAM_INT, 'The visible'),
                            'visibleold' => new external_value(PARAM_INT, 'The visibleold'),
                            'groupmode' => new external_value(PARAM_INT, 'the groupmode'),
                            'groupingid' => new external_value(PARAM_INT, 'the groupingid'),
                            'completion' => new external_value(PARAM_INT, 'The completion'),
                            'completiongradeitemnumber' => new external_value(PARAM_INT, 'The completion grade item number'),
                            'completionview' => new external_value(PARAM_INT, 'The completion view'),
                            'completionexpected' => new external_value(PARAM_INT, 'The completion expected'),
                            'showdescription' => new external_value(PARAM_INT, 'The show description'),
                            'availability' => new external_value(PARAM_RAW, 'the availability'),
                            'modname' => new external_value(PARAM_TEXT, 'The modname')
                        )
                    ), 'coursemodule data'
                ),
                'warnings' => new external_warnings()
            )
        );
    }

    public static function get_remote_course_sections_parameters()
    {
        return new external_function_parameters(
            array('courseid' => new external_value(PARAM_INT, 'course id'),
            )
        );
    }

    public static function get_remote_course_sections($courseid)
    {
        global $DB;
        $warnings = array();

        //validate parameter
        $params = self::validate_parameters(self::get_remote_course_sections_parameters(), array(
            'courseid' => $courseid
        ));

        $sections = $DB->get_records('course_sections', array('course' => $params['courseid']), 'section',
            'section, id, course, name, summary, summaryformat, sequence, visible, ' .
            'availability');

        if (!$sections) {
            $sections = array();
        }

        $result = array();
        $result['sections'] = $sections;
        $result['warnings'] = $warnings;
        return $result;
    }

    public static function get_remote_course_sections_returns()
    {
        return new external_single_structure(
            array(
                'sections' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'the id of course section'),
                            'course' => new external_value(PARAM_INT, 'the id of course'),
                            'section' => new external_value(PARAM_INT, 'the number of section'),
                            'name' => new external_value(PARAM_TEXT, 'the name of course section'),
                            'summary' => new external_value(PARAM_RAW, 'the summary'),
                            'summaryformat' => new external_value(PARAM_INT, 'the summary format'),
                            'sequence' => new external_value(PARAM_RAW, 'the sequence'),
                            'visible' => new external_value(PARAM_INT, 'the visible'),
                            'availability' => new external_value(PARAM_RAW, 'the summary format'),
                        )
                    ), 'section data'
                ),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * @author TiepPT
     * @description validation parametters
     * @return external_function_parameters
     */
    public static function get_course_module_by_cmid_parameters()
    {
        return new external_function_parameters(
            array(
                'modulename' => new external_value(PARAM_RAW, 'The module name'),
                'cmid' => new external_value(PARAM_INT, 'The module id'),
                'courseid' => new external_value(PARAM_INT, 'The course id'),
                'validate' => new external_value(PARAM_BOOL, 'The validation for context'),
            )
        );
    }

    public static function get_course_module_by_cmid($modulename, $cmid, $courseid, $validate)
    {
        //validate parameter
        $params = self::validate_parameters(self::get_course_module_by_cmid_parameters(),
            array(
                'modulename' => $modulename,
                'cmid' => $cmid,
                'courseid' => $courseid,
                'validate' => $validate
            ));
        $warnings = array();
        $cm = self::get_coursemodule_from_id($params['modulename'], $params['cmid'], $params['courseid'], true, MUST_EXIST);
        $info = $cm;

        if ($params['validate']) {
            $context = context_module::instance($cm->id);
            self::validate_context($context);
            // Format name.
            $info->name = external_format_string($cm->name, $context->id);
        }

        $result = array();
        $result['cm'] = $info;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * @description Returns description of method parameters
     * @return external_function_parameters
     * @since Moodle 2.9 Options available
     * @since Moodle 2.2
     */
    public static function get_course_module_by_cmid_returns()
    {
        return core_course_external::get_course_module_returns();
    }

    // MINHND
    public static function get_course_info_by_course_id_parameters()
    {
        return new external_function_parameters(
            array('courseid' => new external_value(PARAM_INT, 'course id'),
            )
        );
    }

    public static function get_course_info_by_course_id($courseid)
    {
        global $DB;

        //validate parameter
        $params = self::validate_parameters(self::get_course_info_by_course_id_parameters(), array('courseid' => $courseid));

        $course = $DB->get_record('course', array('id' => $params['courseid']), "*", MUST_EXIST);
        $courseinfo = $DB->get_record('course_info', array('course' => $params['courseid']), "*", MUST_EXIST);

        $results = array(
            'coursename' => $course->fullname,
            'courseinfo' => $courseinfo->info,
            'validatetime' => $courseinfo->validatetime,
            'note' => $courseinfo->note,
        );

        return $results;
    }

    public static function get_course_info_by_course_id_returns()
    {
        return new external_single_structure(
            array(
                'coursename' => new external_value(PARAM_RAW, 'Course fullname'),
                'courseinfo' => new external_value(PARAM_RAW, 'Course infomation'),
                'validatetime' => new external_value(PARAM_INT, 'Validate Time', VALUE_DEFAULT),
                'note' => new external_value(PARAM_RAW, 'Note'),
            )
        );
    }

    public static function get_course_module_info_parameters()
    {
        return new external_function_parameters(
            array('modname' => new external_value(PARAM_TEXT, 'module name'),
                'instanceid' => new external_value(PARAM_TEXT, 'instance id'),
            )
        );
    }

    public static function get_course_module_info($modname, $instanceid)
    {
        global $DB;

        //validate parameter
        $params = self::validate_parameters(self::get_course_module_info_parameters(), array('modname' => $modname, 'instanceid' => $instanceid));

        return $DB->get_record($modname, array('id' => $instanceid), 'name, intro, introformat', MUST_EXIST);
    }

    public static function get_course_module_info_returns()
    {
        return new external_single_structure(
            array(
                'name' => new external_value(PARAM_TEXT, 'The fullname of the course'),
                'intro' => new external_value(PARAM_RAW, 'Thumbnail course URL - big version'),
                'introformat' => new external_value(PARAM_INT, 'The fullname of the course'),
            )
        );
    }

    /**
     * @description Returns description of method parameters
     * @return external_function_parameters
     * @since Moodle 2.9 Options available
     * @since Moodle 2.2
     */
    public static function get_name_modules_by_id_parameters()
    {
        return new external_function_parameters(
            array('id' => new external_value(PARAM_INT, 'module id')
            )
        );
    }

    /**
     * Get name of modules by id
     *
     * @param $id
     * @return mixed
     * @throws invalid_parameter_exception
     */
    public static function get_name_modules_by_id($id)
    {
        global $DB;

        //validate parameter
        $params = self::validate_parameters(self::get_name_modules_by_id_parameters(),
            array('id' => $id)
        );

        return $DB->get_field('modules', 'name', array('id' => $params['id']));
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function get_name_modules_by_id_returns()
    {
        return new external_value(PARAM_RAW, 'name');
    }

    /**
     * Get modules by id
     *
     * @param $id
     * @return mixed
     * @throws invalid_parameter_exception
     */
    public static function get_modules_by_id_parameters()
    {
        return new external_function_parameters(
            array('id' => new external_value(PARAM_INT, 'module id')
            )
        );
    }

    public static function get_modules_by_id($id)
    {
        global $DB;

        //validate parameter
        $params = self::validate_parameters(self::get_modules_by_id_parameters(),
            array('id' => $id)
        );

        return $module = $DB->get_record('modules', array('id' => $params['id']), '*', MUST_EXIST);
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function get_modules_by_id_returns()
    {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'course id'),
                'name' => new external_value(PARAM_TEXT, 'course short name'),
                'cron' => new external_value(PARAM_INT, 'category id'),
                'lastcron' => new external_value(PARAM_INT, 'full name'),
                'search' => new external_value(PARAM_TEXT, 'short name'),
                'visible' => new external_value(PARAM_INT, 'short name'),
            )
        );
    }

    public static function get_remote_course_section_nav_parameters()
    {
        return new external_function_parameters(
            array(
                'sectionid' => new external_value(PARAM_INT, 'the section id'),
            )
        );
    }

    public static function get_remote_course_section_nav($sectionid)
    {
        global $DB;

        $params = self::validate_parameters(self::get_remote_course_section_nav_parameters(), array(
            'sectionid' => $sectionid
        ));

        $sql = 'SELECT c.*, cs.section AS sectionnumber
                        FROM {course} c
                        LEFT JOIN {course_sections} cs ON cs.course = c.id
                        WHERE cs.id = ?';
        return $DB->get_record_sql($sql, array($sectionid), MUST_EXIST);
    }

    public static function get_remote_course_section_nav_returns()
    {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'course id'),
                'shortname' => new external_value(PARAM_TEXT, 'course short name'),
                'category' => new external_value(PARAM_INT, 'category id'),
                'fullname' => new external_value(PARAM_TEXT, 'full name'),
                'shortname' => new external_value(PARAM_TEXT, 'short name'),
                'idnumber' => new external_value(PARAM_RAW, 'id number', VALUE_OPTIONAL),
                'summary' => new external_value(PARAM_RAW, 'summary'),
                'summaryformat' => new external_format_value('summary'),
                'format' => new external_value(PARAM_PLUGIN,
                    'course format: weeks, topics, social, site,..'),
                'showgrades' => new external_value(PARAM_INT,
                    '1 if grades are shown, otherwise 0', VALUE_OPTIONAL),
                'newsitems' => new external_value(PARAM_INT,
                    'number of recent items appearing on the course page', VALUE_OPTIONAL),
                'startdate' => new external_value(PARAM_INT,
                    'timestamp when the course start'),
                'marker' => new external_value(PARAM_INT,
                    '(deprecated, use courseformatoptions) number of weeks/topics',
                    VALUE_OPTIONAL),
                'legacyfiles' => new external_value(PARAM_INT,
                    '(deprecated, use courseformatoptions) number of weeks/topics',
                    VALUE_OPTIONAL),
                'maxbytes' => new external_value(PARAM_INT,
                    'largest size of file that can be uploaded into the course',
                    VALUE_OPTIONAL),
                'showreports' => new external_value(PARAM_INT,
                    'are activity report shown (yes = 1, no =0)', VALUE_OPTIONAL),
                'visible' => new external_value(PARAM_INT,
                    '1: available to student, 0:not available', VALUE_OPTIONAL),
                'visibleold' => new external_value(PARAM_INT,
                    '(deprecated, use courseformatoptions) How the hidden sections in the course are displayed to students',
                    VALUE_OPTIONAL),
                'groupmode' => new external_value(PARAM_INT, 'no group, separate, visible',
                    VALUE_OPTIONAL),
                'groupmodeforce' => new external_value(PARAM_INT, '1: yes, 0: no',
                    VALUE_OPTIONAL),
                'defaultgroupingid' => new external_value(PARAM_INT, 'default grouping id',
                    VALUE_OPTIONAL),
                'timecreated' => new external_value(PARAM_INT,
                    'timestamp when the course have been created', VALUE_OPTIONAL),
                'timemodified' => new external_value(PARAM_INT,
                    'timestamp when the course have been modified', VALUE_OPTIONAL),
                'enablecompletion' => new external_value(PARAM_INT,
                    'Enabled, control via completion and activity settings. Disbaled,
                                    not shown in activity settings.',
                    VALUE_OPTIONAL),
                'completionnotify' => new external_value(PARAM_INT,
                    '1: yes 0: no', VALUE_OPTIONAL),
                'lang' => new external_value(PARAM_SAFEDIR,
                    'forced course language', VALUE_OPTIONAL),
                'theme' => new external_value(PARAM_TEXT,
                    'name of the force theme', VALUE_OPTIONAL),
                'calendartype' => new external_value(PARAM_TEXT,
                    'name of the force theme', VALUE_OPTIONAL),
                'cacherev' => new external_value(PARAM_ALPHANUMEXT, 'course format option name'),
                'sectionnumber' => new external_value(PARAM_INT, 'section number')
            )
        );
    }

    public static function get_remote_course_format_options_parameters()
    {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'the section id'),
                'format' => new external_value(PARAM_TEXT, 'the section id'),
                'sectionid' => new external_value(PARAM_INT, 'the section id'),
            )
        );
    }

    public static function get_remote_course_format_options($courseid, $format, $sectionid)
    {
        global $DB;

        $params = self::validate_parameters(self::get_remote_course_format_options_parameters(), array(
            'courseid' => $courseid,
            'format' => $format,
            'sectionid' => $sectionid
        ));

        return $DB->get_records('course_format_options',
            array('courseid' => $courseid,
                'sectionid' => $sectionid
            ), '', 'id,name,value');
    }

    public static function get_remote_course_format_options_returns()
    {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'course id'),
                    'name' => new external_value(PARAM_TEXT, 'course id'),
                    'value' => new external_value(PARAM_RAW, 'longtext'),
                )
            )
        );
    }

    public static function get_modules_by_parameters()
    {
        return new external_function_parameters(
            array(
                'parameters' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'name' => new external_value(PARAM_RAW, 'param name'),
                            'value' => new external_value(PARAM_RAW, 'param value'),
                        )
                    ), 'the params'
                ),
                'sort' => new external_value(PARAM_RAW, 'sort'),
                'mustexists' => new external_value(PARAM_BOOL, 'must exists')
            )
        );
    }

    public static function get_modules_by($parameters, $sort, $mustexists)
    {
        global $DB;
        $warnings = array();

        $params = self::validate_parameters(self::get_modules_by_parameters(), array(
            'parameters' => $parameters,
            'sort' => $sort,
            'mustexists' => $mustexists
        ));

        $arr = array();
        foreach ($params['parameters'] as $p) {
            $arr = array_merge($arr, array($p['name'] => $p['value']));
        }

        $result = array();

        if ($params['mustexists'] === FALSE) {
            $module = $DB->get_record("modules", $arr);
        } else if ($params['mustexists'] === FALSE && $params['sort'] != '') {
            $module = $DB->get_record("modules", $arr, $params['sort']);
        } else {
            $module = $DB->get_record("modules", $arr, '*', MUST_EXIST);
        }

        if (!$module) {
            $module = new stdClass();
        }

        $result['module'] = $module;
        $result['warnings'] = $warnings;
        return $result;
    }

    public static function get_modules_by_returns()
    {
        return new external_single_structure(
            array(
                'module' => new external_single_structure(
                    array(
                        'id' => new external_value(PARAM_INT, 'module id'),
                        'name' => new external_value(PARAM_RAW, 'module name'),
                        'cron' => new external_value(PARAM_INT, 'cron'),
                        'lastcron' => new external_value(PARAM_INT, 'last cron'),
                        'search' => new external_value(PARAM_RAW, 'search'),
                        'visible' => new external_value(PARAM_INT, 'visible')
                    )
                ),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Describes the parameters for delete_remote_course_modules_completion_by_cmid_hostip
     *
     * @return external_external_function_parameters
     */
    public static function delete_remote_course_modules_completion_by_cmid_hostip_parameters()
    {
        return new external_function_parameters(
            array(
                'coursemoduleid' => new external_value(PARAM_INT, 'The id of course module'),
                'hostip' => new external_value(PARAM_TEXT, 'The ip address on host')
            )
        );
    }

    /**
     * Delete tbl course_modules_completetion by cmid and hostip
     *
     * @param int $coursemoduleid - The id of course modules
     * @param string $hostip - The ip_address on host
     *
     * @return bool $result true if success
     */
    public static function delete_remote_course_modules_completion_by_cmid_hostip($coursemoduleid, $hostip)
    {
        global $DB;

        $params = self::validate_parameters(self::delete_remote_course_modules_completion_by_cmid_hostip_parameters(), array(
            'coursemoduleid' => $coursemoduleid,
            'hostip' => $hostip,
        ));

        $sql = 'SELECT u.id 
                FROM {user} u 
                JOIN {mnet_host} mh 
                ON u.mnethostid = mh.id 
                WHERE mh.ip_address = ?';

        $result = $DB->delete_records_select('course_modules_completion', 'coursemoduleid = ? AND userid IN(' . $sql . ')',
            array($params['coursemoduleid'], $params['hostip']));

        return $result;
    }

    /**
     * Describes the delete_remote_course_modules_completion_by_cmid_hostip returns value.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function delete_remote_course_modules_completion_by_cmid_hostip_returns()
    {
        return new external_value(PARAM_INT, 'true if success');
    }

    /**
     * Describes the parameters for get_remote_course_modules_completion
     *
     * @return external_external_function_parameters
     */
    public static function get_remote_course_modules_completion_parameters()
    {
        return new external_function_parameters(
            array(
                'coursemoduleid' => new external_value(PARAM_INT, 'The id of course module'),
                'courseid' => new external_value(PARAM_INT, 'The id of course'),
                'hostip' => new external_value(PARAM_TEXT, 'The ip address on host'),
                'field' => new external_value(PARAM_TEXT, 'The field of table to get'),
                'mode' => new external_value(PARAM_TEXT, 'The mode to operate. Current: singlerc, wholecourse, normal'),
                'userid' => new external_value(PARAM_INT, 'The id of user')
            )
        );
    }

    /**
     * Get records tbl course_modules_completetion by cmid and hostip
     *
     * @param int $coursemoduleid - The id of course modules
     * @param int $courseid - The id of course
     * @param string $hostip - The ip_address on host
     * @param string $field - The field of table to get
     * @param string $mode - The mode to operate. Current: singlerc, wholecourse, normal
     * @param string $userid - The id of user
     *
     * @return mixed $result list records table and warnings
     */
    public static function get_remote_course_modules_completion($coursemoduleid, $courseid, $hostip, $field, $mode, $userid)
    {
        global $DB;

        $warnings = array();

        $result = array();

        $params = self::validate_parameters(self::get_remote_course_modules_completion_parameters(), array(
            'coursemoduleid' => $coursemoduleid,
            'courseid' => $courseid,
            'hostip' => $hostip,
            'field' => $field,
            'mode' => $mode,
            'userid' => $userid,
        ));

        $sql = "SELECT u.id
                FROM {user} u 
                JOIN {mnet_host} mh 
                ON u.mnethostid = mh.id AND mh.ip_address = ?";

        switch ($params['mode']) {
            case 'normal':
                $result['cmc'] = $DB->get_records_select('course_modules_completion', 'coursemoduleid = ? AND userid IN(' . $sql . ')',
                    array($params['coursemoduleid'], $params['hostip']), '', $params['field']);
                // If result false return empty array
                if (!$result['cmc']) {
                    $result['cmc'] = array();
                } else {
                    foreach ($result['cmc'] as $cmc) {
                        $cmc->email = self::change_userid_to_email($cmc->userid);
                    }
                }
                break;
            case 'wholecourse':
                $result['cmc'] = $DB->get_records_sql("
                    SELECT
                        cmc.*
                    FROM
                        {course_modules} cm
                        INNER JOIN {course_modules_completion} cmc ON cmc.coursemoduleid=cm.id
                    WHERE
                        cm.course=? AND cmc.userid=?",
                    array($params['courseid'], $params['userid']), '', $params['field']);
                // If result false return empty array
                if (!$result['cmc']) {
                    $result['cmc'] = array();
                } else {
                    foreach ($result['cmc'] as $cmc) {
                        $cmc->email = self::change_userid_to_email($cmc->userid);
                    }
                }
                break;
            case 'singlerc':
                $result['scmc'] = $DB->get_record_select('course_modules_completion', 'coursemoduleid = ? AND userid = ?',
                    array($params['coursemoduleid'], $params['userid']), $params['field']);
                // If result false return empty array
                if (!$result['scmc']) {
                    $result['scmc'] = array();
                } else {
                    $result['scmc']->email = self::change_userid_to_email($result['scmc']->userid);
                }
                break;
            default:
                break;
        }

        $result['warnings'] = array();
        return $result;
    }

    /**
     * Describes the get_remote_course_modules_completion returns value.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function get_remote_course_modules_completion_returns()
    {
        return new external_single_structure(
            array(
                'cmc' => new external_multiple_structure(
                    self::get_course_module_completion_structure(VALUE_OPTIONAL), 'course module completion', VALUE_OPTIONAL),
                'scmc' => self::get_course_module_completion_structure(VALUE_OPTIONAL),
                'warnings' => new external_warnings()
            )
        );
    }


    /**
     * Creates a couse_module_completion structure.
     *
     * @return external_single_structure the grade_grades structure
     */
    private static function get_course_module_completion_structure($required = VALUE_REQUIRED)
    {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'The id of course module completion', VALUE_OPTIONAL),
                'coursemoduleid' => new external_value(PARAM_INT, 'The id of course module', VALUE_OPTIONAL),
                'userid' => new external_value(PARAM_INT, 'The id of user', VALUE_OPTIONAL),
                'email' => new external_value(PARAM_TEXT, 'The email of user', VALUE_OPTIONAL),
                'completionstate' => new external_value(PARAM_INT, 'Completion state', VALUE_OPTIONAL),
                'viewed' => new external_value(PARAM_INT, 'View', VALUE_OPTIONAL),
                'timemodified' => new external_value(PARAM_INT, 'Time viewer', VALUE_OPTIONAL)
            ), 'course module completion', $required
        );
    }

    /**
     * Change userid to email user serve to send data on host
     *
     * @param int $userid - The id of user
     * @return string     - The email of user
     */
    public static function change_userid_to_email($userid)
    {
        global $DB;

        return $DB->get_record('user', array('id' => $userid), 'email')->email;
    }

    /**
     * Describes the parameters for create_update_remote_course_modules_completion
     *
     * @return external_external_function_parameters
     */
    public static function create_update_remote_course_modules_completion_parameters()
    {
        return new external_function_parameters(
            array(
                'coursemoduleid' => new external_value(PARAM_INT, 'The id of course module'),
                'userid' => new external_value(PARAM_INT, 'The id of user'),
                'completionstate' => new external_value(PARAM_INT, 'Completion state'),
                'viewed' => new external_value(PARAM_INT, 'View'),
                'timemodified' => new external_value(PARAM_INT, 'Time viewer')
            )
        );
    }

    /**
     * Update & create table "course_modules_completion" on hub
     *
     * @param int $coursemoduleid - The id of course modules
     * @param int $userid - The id of user on hub
     * @param int $completionstate - The state of completion
     * @param int $viewed - The state of viewed
     * @param int $timemodified - The time modified
     *
     * @return int $result         - The id of course modules completion
     */
    public static function create_update_remote_course_modules_completion($coursemoduleid, $userid, $completionstate, $viewed, $timemodified)
    {
        global $DB;

        $params = self::validate_parameters(self::create_update_remote_course_modules_completion_parameters(), array(
            'coursemoduleid' => $coursemoduleid,
            'userid' => $userid,
            'completionstate' => $completionstate,
            'viewed' => $viewed,
            'timemodified' => $timemodified,
        ));

        $cmc = $DB->get_record('course_modules_completion', array('coursemoduleid' => $params['coursemoduleid'],
            'userid' => $params['userid']));

        $params = (object)$params;

        $trans = $DB->start_delegated_transaction();

        if (!$cmc) {
            $result = $DB->insert_record('course_modules_completion', $params);
        } else {
            $params->id = $cmc->id;
            $result = $DB->update_record('course_modules_completion', $params);
        }

        $trans->allow_commit();

        return $result;
    }

    /**
     * Describes the create_update_remote_course_modules_completion returns value.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function create_update_remote_course_modules_completion_returns()
    {
        return new external_value(PARAM_INT, 'True(1) if success');
    }

    /**
     * Describes the parameters for get_remote_course_modules_completion_by_userid_cmid_completion
     *
     * @return external_external_function_parameters
     */
    public static function get_remote_course_modules_completion_by_userid_cmid_parameters()
    {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'The id of user'),
                'coursemoduleid' => new external_value(PARAM_INT, 'The id of course module'),
            )
        );
    }

    /**
     * Get record remote course module completion by userid and course moduleid
     *
     * @param int $userid - The id of user on hub
     * @param int $coursemoduleid - The id of course modules on hub
     *
     * @return int $result         - The id of course modules completion
     */
    public static function get_remote_course_modules_completion_by_userid_cmid($userid, $coursemoduleid)
    {
        global $DB;

        $result = array();
        $warnings = array();

        $params = self::validate_parameters(self::get_remote_course_modules_completion_by_userid_cmid_parameters(), array(
            'userid' => $userid,
            'coursemoduleid' => $coursemoduleid
        ));

        $result['completion'] = $DB->get_record('course_modules_completion', array('coursemoduleid' => $params['coursemoduleid'],
            'userid' => $params['userid']));

        if (!$result['completion']) {
            $result['completion'] = array();
        }

        $result['warning'] = $warnings;

        return $result;
    }

    /**
     * Describes the get_remote_course_modules_completion_by_userid_cmid returns value.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function get_remote_course_modules_completion_by_userid_cmid_returns()
    {
        return new external_single_structure(
            array(
                'completion' => new external_single_structure(
                    array(
                        'coursemoduleid' => new external_value(PARAM_INT, 'The id of course module'),
                        'userid' => new external_value(PARAM_INT, 'The id of user'),
                        'completionstate' => new external_value(PARAM_INT, 'The state of completion'),
                        'viewed' => new external_value(PARAM_INT, 'The state of viewed'),
                        'timemodified' => new external_value(PARAM_INT, 'The modified time')
                    )
                ),
                'warning' => new external_warnings()
            )
        );
    }

    /**
     * Describes the parameters for delete_remote_course_completions
     *
     * @return external_external_function_parameters
     */
    public static function delete_remote_course_completions_parameters()
    {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'The id of course'),
                'hostip' => new external_value(PARAM_TEXT, 'The ip address on host')
            )
        );
    }

    /**
     * Delete tbl course_completions by cmid and hostip
     *
     * @param int $courseid - The id of course
     * @param string $hostip - The ip_address on host
     *
     * @return bool $result true if success
     */
    public static function delete_remote_course_completions($courseid, $hostip)
    {
        global $DB;

        $params = self::validate_parameters(self::delete_remote_course_completions_parameters(), array(
            'courseid' => $courseid,
            'hostip' => $hostip,
        ));

        $sql = 'SELECT u.id 
                FROM {user} u 
                JOIN {mnet_host} mh 
                ON u.mnethostid = mh.id 
                WHERE mh.ip_address = ?';

        $result = $DB->delete_records_select('course_completions', 'course = ? AND userid IN(' . $sql . ')',
            array($params['courseid'], $params['hostip']));

        return $result;
    }

    /**
     * Describes the delete_remote_course_completions returns value.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function delete_remote_course_completions_returns()
    {
        return new external_value(PARAM_INT, 'true if success');
    }

    /**
     * Describes the parameters for delete_remote_course_completion_crit_compl
     *
     * @return external_external_function_parameters
     */
    public static function delete_remote_course_completion_crit_compl_parameters()
    {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'The id of course'),
                'hostip' => new external_value(PARAM_TEXT, 'The ip address on host')
            )
        );
    }

    /**
     * Delete tbl course_completion_crit_compl by cmid and hostip
     *
     * @param int $courseid - The id of course
     * @param string $hostip - The ip_address on host
     *
     * @return bool $result true if success
     */
    public static function delete_remote_course_completion_crit_compl($courseid, $hostip)
    {
        global $DB;

        $params = self::validate_parameters(self::delete_remote_course_completion_crit_compl_parameters(), array(
            'courseid' => $courseid,
            'hostip' => $hostip,
        ));

        $sql = 'SELECT u.id 
                FROM {user} u 
                JOIN {mnet_host} mh 
                ON u.mnethostid = mh.id 
                WHERE mh.ip_address = ?';

        $result = $DB->delete_records_select('course_completion_crit_compl', 'course = ? AND userid IN(' . $sql . ')',
            array($params['courseid'], $params['hostip']));

        return $result;
    }

    /**
     * Describes the delete_remote_course_completion_crit_compl returns value.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function delete_remote_course_completion_crit_compl_returns()
    {
        return new external_value(PARAM_INT, 'true if success');
    }

    /**
     * Describes the parameters for get_remote_completion_fetch_all_helper
     *
     * @return external_external_function_parameters
     */
    public static function get_remote_completion_fetch_all_helper_parameters()
    {
        return new external_function_parameters(
            array(
                'table' => new external_value(PARAM_TEXT, 'The name of table to get'),
                'course' => new external_value(PARAM_INT, 'The id of course'),
            )
        );
    }

    /**
     * Factory method - uses the parameters to retrieve all matching instances from the DB.
     *
     * @final
     * @param string $table The table name to fetch from
     * @param string $classname The class that you want the result instantiated as
     * @param array $params Any params required to select the desired row
     * @return mixed array of object instances or false if not found
     */
    public static function get_remote_completion_fetch_all_helper($table, $course)
    {
        global $DB, $CFG;

        $result = array();

        $warnings = array();

        $params = self::validate_parameters(self::get_remote_completion_fetch_all_helper_parameters(), array(
            'table' => $table,
            'course' => $course,
        ));

        $result[$table] = $DB->get_records($table, array('course' => $params['course']));

        if (!$result[$table]) {
            $result[$table] = array();
        }

        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Describes the get_remote_completion_fetch_all_helper returns value.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function get_remote_completion_fetch_all_helper_returns()
    {
        return new external_single_structure(
            array(
                'course_completion_criteria' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'course' => new external_value(PARAM_INT, 'The id of course', VALUE_OPTIONAL),
                            'criteriatype' => new external_value(PARAM_INT, 'The criteria types integer constant', VALUE_OPTIONAL),
                            'module' => new external_value(PARAM_TEXT, 'The name of the module', VALUE_OPTIONAL),
                            'moduleinstance' => new external_value(PARAM_INT, 'The id of the activity/resource module or role', VALUE_OPTIONAL),
                            'courseinstance' => new external_value(PARAM_INT, 'The id of course', VALUE_OPTIONAL),
                            'enrolperiod' => new external_value(PARAM_INT, 'The number of seconds after enrolment', VALUE_OPTIONAL),
                            'timeend' => new external_value(PARAM_INT, 'The timestamp of the date for course completion', VALUE_OPTIONAL),
                            'gradepass' => new external_value(PARAM_FLOAT, 'The course grade required to complete this criteria', VALUE_OPTIONAL),
                            'role' => new external_value(PARAM_INT, 'The role id that can mark \'student\'s as complete in the course', VALUE_OPTIONAL),
                        )
                    )
                    , 'Information table completion_criteria', VALUE_OPTIONAL),
                'course_completion_aggr_methd' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'course' => new external_value(PARAM_INT, 'The id of the course that the course completion aggregation relates to', VALUE_OPTIONAL),
                            'criteriatype' => new external_value(PARAM_INT, 'The criteria type\'s integer constant (\'role\', \'activity\') or null if \'overall\' course aggregation.', VALUE_OPTIONAL),
                            'method' => new external_value(PARAM_INT, '\'1\'=\'all\', \'2\'=\'any\', \'3\'=\'fraction\', \'4\'=\'unit\'', VALUE_OPTIONAL),
                            'value' => new external_value(PARAM_INT, 'null for \'all\' and \'any\', 0..1 for \'fraction\', int > 0 for \'unit\'', VALUE_OPTIONAL),
                        )
                    )
                    , 'Information table completion_aggregation', VALUE_OPTIONAL),
                'course_completions' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'The id of tbl completion_completion', VALUE_OPTIONAL),
                            'userid' => new external_value(PARAM_INT, 'The id of the user who has completed the course', VALUE_OPTIONAL),
                            'course' => new external_value(PARAM_INT, 'The id of the completed course', VALUE_OPTIONAL),
                            'timeenrolled' => new external_value(PARAM_INT, 'Timestamp when the user was enrolled in the course. In the case of multiple enrollments, the earliest timestamp for a current enrollment is used. If this is reported as 0, the current time is used instead.', VALUE_OPTIONAL),
                            'timestarted' => new external_value(PARAM_INT, 'Timestamp when the user first made progress in the course', VALUE_OPTIONAL),
                            'timecompleted' => new external_value(PARAM_INT, 'Timestamp when the user completed the course', VALUE_OPTIONAL),
                            'reaggregate' => new external_value(PARAM_INT, 'Re aggregate', VALUE_OPTIONAL),
                        )
                    )
                    , 'Information table completion_completion', VALUE_OPTIONAL),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Describes the parameters for get_remote_modules
     *
     * @return external_external_function_parameters
     */
    public static function get_remote_modules_parameters()
    {
        return new external_function_parameters(
            array(
                'fields' => new external_value(PARAM_TEXT, 'The fields to get')
            )
        );
    }

    /**
     * Get all information about modules.
     *
     * @params string $fields  - The fields of table modules to get
     *
     * @return array $return   - The information tbl modules
     */
    public static function get_remote_modules($fields)
    {
        global $DB;

        $params = self::validate_parameters(self::get_remote_modules_parameters(), array(
            'fields' => $fields,
        ));
        $result = $DB->get_records('modules', array(), '', $params['fields']);

        return $result;
    }

    /**
     * Describes the get_remote_modules returns value.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function get_remote_modules_returns()
    {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'The id of modules', VALUE_OPTIONAL),
                    'name' => new external_value(PARAM_TEXT, 'The name of modules', VALUE_OPTIONAL),
                    'cron' => new external_value(PARAM_INT, 'The cron', VALUE_OPTIONAL),
                    'lastcron' => new external_value(PARAM_INT, 'The last cron', VALUE_OPTIONAL),
                    'search' => new external_value(PARAM_TEXT, 'The string to search', VALUE_OPTIONAL),
                    'visible' => new external_value(PARAM_INT, 'The visible to see', VALUE_OPTIONAL),
                )
            )
        );
    }

    /**
     * Describes the parameters for update_remote_course_completions
     *
     * @return external_external_function_parameters
     */
    public static function update_remote_course_completions_parameters()
    {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'The id of user'),
                'course' => new external_value(PARAM_INT, 'The id of course'),
                'timeenrolled' => new external_value(PARAM_INT, 'The enrolled time course completion'),
                'timestarted' => new external_value(PARAM_INT, 'The started time course completion'),
                'timecompleted' => new external_value(PARAM_INT, 'The completed time course completion', VALUE_DEFAULT, NULL),
                'reaggregate' => new external_value(PARAM_INT, 'Reaggregate')
            )
        );
    }

    /**
     * Get all information about modules.
     *
     * @params int $userid          - The id of user
     * @params int $course          - The id of course
     * @params int $timeenrolled    - The enrolled time course completion
     * @params int $timestarted     - The started time course completion
     * @params int $timecompleted   - The completed time course completion
     * @params int $reaggregate     - Reaggregate
     *
     * @return bool $return         - True if update success
     */
    public static function update_remote_course_completions($userid, $course, $timeenrolled, $timestarted, $timecompleted, $arrgregate)
    {
        global $DB;

        $params = self::validate_parameters(self::update_remote_course_completions_parameters(), array(
            'userid' => $userid,
            'course' => $course,
            'timeenrolled' => $timeenrolled,
            'timestarted' => $timestarted,
            'timecompleted' => $timecompleted,
            'reaggregate' => $arrgregate,
        ));

        $data = (object)$params;
        $cc = $DB->get_record('course_completions', array('userid' => $params['userid'], 'course' => $params['course']));
        if (!$cc) {
            $result = $DB->insert_record('course_completions', $data);
        } else {
            $data->id = $cc->id;
            $result = $DB->update_record('course_completions', $data);
        }

        return $result;
    }

    /**
     * Describes the update_remote_course_completions returns value.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function update_remote_course_completions_returns()
    {
        return new external_value(PARAM_INT, 'Return true if success');
    }

    public static function get_course_sections_by_parameters()
    {
        return new external_function_parameters(
            array(
                'parameters' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'name' => new external_value(PARAM_RAW, 'param name'),
                            'value' => new external_value(PARAM_RAW, 'param value'),
                        )
                    ), 'the params'
                ),
                'strictness' => new external_value(PARAM_INT, 'the strictness')
            )
        );
    }

    public static function get_course_sections_by($parameters, $strictness)
    {
        global $DB;
        $warnings = array();

        $params = self::validate_parameters(self::get_course_sections_by_parameters(), array(
            'parameters' => $parameters,
            'strictness' => $strictness
        ));

        $result = array();
        $arr = array();

        foreach ($params['parameters'] as $p) {
            $arr = array_merge($arr, array($p['name'] => $p['value']));
        }

        $section = $DB->get_record('course_sections', $arr, '*', $params['strictness']);

        if (!$section) {
            $section = new stdClass();
        }

        $result['section'] = $section;
        $result['warnings'] = $warnings;

        return $result;
    }

    public static function get_course_sections_by_returns()
    {
        return new external_single_structure(
            array(
                'section' => new external_single_structure(
                    array(
                        'id' => new external_value(PARAM_INT, 'the id'),
                        'course' => new external_value(PARAM_INT, 'the course id'),
                        'section' => new external_value(PARAM_INT, 'the section'),
                        'name' => new external_value(PARAM_RAW, 'the name'),
                        'summary' => new external_value(PARAM_RAW, 'the summary'),
                        'summaryformat' => new external_value(PARAM_INT, 'the summary format'),
                        'sequence' => new external_value(PARAM_RAW, 'the sequence'),
                        'visible' => new external_value(PARAM_INT, 'the visible'),
                        'availability' => new external_value(PARAM_RAW, 'the availability')
                    )
                ),
                'warnings' => new external_warnings()
            )
        );
    }

    public static function get_course_modules_by_parameters()
    {
        return new external_function_parameters(
            array(
                'parameters' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'name' => new external_value(PARAM_RAW, 'param name'),
                            'value' => new external_value(PARAM_RAW, 'param value'),
                        )
                    ), 'the params'
                ),
                'strictness' => new external_value(PARAM_INT, 'the strictness')
            )
        );
    }

    public static function get_course_modules_by($parameters, $strictness)
    {
        global $DB;
        $warnings = array();

        $params = self::validate_parameters(self::get_course_modules_by_parameters(), array(
            'parameters' => $parameters,
            'strictness' => $strictness
        ));

        $result = array();
        $arr = array();

        foreach ($params['parameters'] as $p) {
            $arr = array_merge($arr, array($p['name'] => $p['value']));
        }

        $cm = $DB->get_record('course_modules', $arr, 'id,course', $params['strictness']);

        if (!$cm) {
            $cm = new stdClass();
        }

        $result['cm'] = $cm;
        $result['warnings'] = $warnings;

        return $result;
    }

    public static function get_course_modules_by_returns()
    {
        return new external_single_structure(
            array(
                'cm' => new external_single_structure(
                    array(
                        'id' => new external_value(PARAM_INT, 'The course module id'),
                        'course' => new external_value(PARAM_INT, 'The course id'),
                    )
                ),
                'warnings' => new external_warnings()
            )
        );
    }

    public static function get_course_module_by_instance_parameters()
    {
        return new external_function_parameters(
            array(
                'module' => new external_value(PARAM_COMPONENT, 'The module name'),
                'instance' => new external_value(PARAM_INT, 'The module instance id')
            )
        );
    }

    public static function get_course_module_by_instance($module, $instance)
    {
        $params = self::validate_parameters(self::get_course_module_by_instance_parameters(),
            array(
                'module' => $module,
                'instance' => $instance,
            ));

        $warnings = array();
        $cm = get_coursemodule_from_instance($params['module'], $params['instance']);
        $info = $cm;

        $result = array();
        $result['cm'] = $info;
        $result['warnings'] = $warnings;
        return $result;
    }

    public static function get_course_module_by_instance_returns()
    {
        return core_course_external::get_course_module_returns();
    }

    public static function get_list_course_module_competencies_in_course_module_parameters()
    {
        return new external_function_parameters(
            array(
                'cmorid' => new external_value(PARAM_RAW, 'id of cm')
            )
        );
    }

    public static function get_list_course_module_competencies_in_course_module($cmorid)
    {
        $params = self::validate_parameters(self::get_list_course_module_competencies_in_course_module_parameters(), array(
            'cmorid' => $cmorid
        ));

        $cm = $params['cmorid'];
        if (!is_object($params['cmorid'])) {
            $cm = self::get_coursemodule_from_id('', $params['cmorid'], 0, true, MUST_EXIST);
        }

        // Check the user have access to the course module.
        self::validate_course_module($cm);
        $context = context_module::instance($cm->id);

        $result = array();
        $result['cm'] = $cm;

        return $result;
    }

    public static function get_list_course_module_competencies_in_course_module_returns()
    {
        return new external_single_structure(
            array(
                'cm' => new external_single_structure(
                    array(
                        'id' => new external_value(PARAM_INT, 'The course module id'),
                        'course' => new external_value(PARAM_INT, 'The course id'),
                        'module' => new external_value(PARAM_INT, 'The module type id'),
                        'instance' => new external_value(PARAM_INT, 'The activity instance id'),
                        'section' => new external_value(PARAM_INT, 'The module section id'),
                        'idnumber' => new external_value(PARAM_RAW, 'Module id number', VALUE_OPTIONAL),
                        'added' => new external_value(PARAM_INT, 'Time added', VALUE_OPTIONAL),
                        'score' => new external_value(PARAM_INT, 'Score', VALUE_OPTIONAL),
                        'indent' => new external_value(PARAM_INT, 'Indentation', VALUE_OPTIONAL),
                        'visible' => new external_value(PARAM_INT, 'If visible', VALUE_OPTIONAL),
                        'visibleold' => new external_value(PARAM_INT, 'Visible old', VALUE_OPTIONAL),
                        'groupmode' => new external_value(PARAM_INT, 'Group mode'),
                        'groupingid' => new external_value(PARAM_INT, 'Grouping id'),
                        'completion' => new external_value(PARAM_INT, 'If completion is enabled'),
                        'completiongradeitemnumber' => new external_value(PARAM_INT, 'Completion grade item', VALUE_OPTIONAL),
                        'completionview' => new external_value(PARAM_INT, 'Completion view setting', VALUE_OPTIONAL),
                        'completionexpected' => new external_value(PARAM_INT, 'Completion time expected', VALUE_OPTIONAL),
                        'showdescription' => new external_value(PARAM_INT, 'If the description is showed', VALUE_OPTIONAL),
                        'availability' => new external_value(PARAM_RAW, 'Availability settings', VALUE_OPTIONAL),
                        'name' => new external_value(PARAM_RAW, 'The activity name'),
                        'modname' => new external_value(PARAM_COMPONENT, 'The module component name (forum, assign, etc..)'),
                        'sectionnum' => new external_value(PARAM_INT, 'The module section number')
                    )
                )
            )
        );
    }

    public static function can_add_moduleinfo_parameters()
    {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, ' the course id'),
                'modulename' => new external_value(PARAM_RAW, 'the module name'),
                'section' => new external_value(PARAM_INT, 'the section number')
            )
        );
    }

    public static function can_add_moduleinfo($courseid, $modulename, $section)
    {
        global $CFG, $DB;
        $warnings = array();

        require_once($CFG->dirroot . '/lib/accesslib.php');
        require_once($CFG->dirroot . '/course/lib.php');

        $params = self::validate_parameters(self::can_add_moduleinfo_parameters(), array(
            'courseid' => $courseid,
            'modulename' => $modulename,
            'section' => $section
        ));

        $course = $DB->get_record('course', array('id' => $params['courseid']), '*', MUST_EXIST);
        $module = $DB->get_record('modules', array('name' => $params['modulename']), '*', MUST_EXIST);
        $context = context_course::instance($course->id);
        require_capability('moodle/course:manageactivities', $context);
        course_create_sections_if_missing($course, $section);
        $cw = get_fast_modinfo($course)->get_section_info($section);
        if (!course_allowed_module($course, $module->name)) {
            $warnings['message'] = print_error('moduledisable');
        }

        $result = array();
        $result['module'] = $module;
        return $result;
    }

    public static function can_add_moduleinfo_returns()
    {
        return new external_single_structure(
            array(
                'module' => new external_single_structure(
                    array(
                        'id' => new external_value(PARAM_INT, 'the id'),
                        'name' => new external_value(PARAM_RAW, 'the name'),
                        'cron' => new external_value(PARAM_INT, 'the cron'),
                        'lastcron' => new external_value(PARAM_INT, 'the last cron'),
                        'search' => new external_value(PARAM_RAW, 'the search'),
                        'visible' => new external_value(PARAM_INT, 'the visible')
                    )
                ),
                'warnings' => new external_warnings()
            )
        );
    }

    public static function add_moduleinfo_by_parameters()
    {
        return new external_function_parameters(
            array(
                'moduleinfo' => new external_value(PARAM_RAW, 'module info'),
                'course' => new external_value(PARAM_INT, 'The id of course'),
            )
        );
    }

    public static function add_moduleinfo_by($moduleinfo, $courseid)
    {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/course/modlib.php');

        $warnings = array();
        $params = self::validate_parameters(self::add_moduleinfo_by_parameters(), array(
            'moduleinfo' => $moduleinfo,
            'course' => $courseid
        ));
        $moduleinfoobj = json_decode($params['moduleinfo']);

        $courseobj = $DB->get_record('course', array('id' => $params['course']), '*', MUST_EXIST);
        if ($courseobj->enablecompletion) {
            unset($moduleinfoobj->availabilityconditionsjson);
            foreach ($moduleinfoobj as $key => $value) {
                if (substr($key, 0, 10) === 'completion') {
                    unset($moduleinfoobj->$key);
                }
            }
        }

        $modulerequire = "$CFG->dirroot/mod/$moduleinfoobj->modulename/lib.php";
        if (file_exists($modulerequire)) {
            require_once($modulerequire);
        } else {
            $warnings['message'] = "File not found";
        }

        $moduleformatfunction = $moduleinfoobj->modulename . '_formatted_moduleinfo';

        if (function_exists($moduleformatfunction)) {
            $moduleinfoobj = $moduleformatfunction($moduleinfoobj);
        }

        $modinfo = add_moduleinfo($moduleinfoobj, $courseobj, null);

        $result = array();
        $result['moduleinfo'] = array(
            'coursemodule' => $modinfo->coursemodule,
            'instance' => $modinfo->instance
        );
        $result['warnings'] = $warnings;

        return $result;
    }

    public static function add_moduleinfo_by_returns()
    {
        return new external_single_structure(
            array(
                'moduleinfo' => new external_single_structure(
                    array(
                        'coursemodule' => new external_value(PARAM_INT, 'The id of course module just created'),
                        'instance' => new external_value(PARAM_INT, 'The id of instance'),
                    )
                ),
                'warnings' => new external_warnings()
            )
        );
    }

    public static function can_update_moduleinfo_parameters()
    {
        return new external_function_parameters(
            array(
                'cmid' => new external_value(PARAM_INT, 'the id of course module')
            )
        );
    }

    public static function can_update_moduleinfo($cmid)
    {
        global $CFG, $DB;
        $warnings = array();

        require_once($CFG->dirroot . '/lib/datalib.php');
        require_once($CFG->dirroot . '/lib/accesslib.php');

        $params = self::validate_parameters(self::can_update_moduleinfo_parameters(), array(
            'cmid' => $cmid
        ));

        $cm = get_coursemodule_from_id('', $params['cmid'], 0, false, MUST_EXIST);

        if (!$cm) {
            $warnings['message'] = "The course module not found";
        }

        // Check the $USER has the right capability.
        $context = context_module::instance($cm->id);
        require_capability('moodle/course:manageactivities', $context);

        // Check module exists.
        $module = $DB->get_record('modules', array('id' => $cm->module), '*', MUST_EXIST);

        // Check the moduleinfo exists.
        $data = $DB->get_record($module->name, array('id' => $cm->instance), '*', MUST_EXIST);

        // Check the course section exists.
        $cw = $DB->get_record('course_sections', array('id' => $cm->section), '*', MUST_EXIST);

        $result = array();
        $result['warnings'] = $warnings;
        $result['module'] = $module;
        $result['data'] = json_encode($data);
        $result['cw'] = $cw;

        return $result;
    }

    public static function can_update_moduleinfo_returns()
    {
        return new external_single_structure(
            array(
                'module' => new external_single_structure(
                    array(
                        'id' => new external_value(PARAM_INT, 'the id'),
                        'name' => new external_value(PARAM_RAW, 'the name'),
                        'cron' => new external_value(PARAM_INT, 'the cron'),
                        'lastcron' => new external_value(PARAM_INT, 'the last cron'),
                        'search' => new external_value(PARAM_RAW, 'the search'),
                        'visible' => new external_value(PARAM_INT, 'the visible')
                    )
                ),
                'data' => new external_value(PARAM_RAW, 'the data'),
                'cw' => new external_single_structure(
                    array(
                        'id' => new external_value(PARAM_INT, 'the id'),
                        'course' => new external_value(PARAM_INT, 'the course id'),
                        'section' => new external_value(PARAM_INT, 'the section'),
                        'name' => new external_value(PARAM_RAW, 'the name'),
                        'summary' => new external_value(PARAM_RAW, 'the summary'),
                        'summaryformat' => new external_value(PARAM_INT, 'the summary format'),
                        'sequence' => new external_value(PARAM_RAW, 'the sequence'),
                        'visible' => new external_value(PARAM_INT, 'the visible'),
                        'availability' => new external_value(PARAM_RAW, 'the availability')
                    )
                ),
                'warnings' => new external_warnings()
            )
        );
    }

    public static function update_moduleinfo_by_parameters()
    {
        return new external_function_parameters(
            array(
                'cm' => new external_value(PARAM_RAW, 'the course module'),
                'moduleinfo' => new external_value(PARAM_RAW, 'the course module'),
                'courseid' => new external_value(PARAM_INT, 'the id of course'),
                'mform' => new external_value(PARAM_RAW, 'the mod form')
            )
        );
    }

    public static function update_moduleinfo_by($cm, $moduleinfo, $courseid, $mform)
    {
        global $DB, $CFG;
        $warnings = array();

        require_once($CFG->dirroot . '/course/modlib.php');

        $warnings = array();
        $params = self::validate_parameters(self::update_moduleinfo_by_parameters(), array(
            'cm' => $cm,
            'moduleinfo' => $moduleinfo,
            'courseid' => $courseid,
            'mform' => $mform
        ));

        $course = $DB->get_record('course', array('id' => $params['courseid']), '*', MUST_EXIST);
        if (!$course) {
            $warnings['message'] = "The course not found";
        }

        $modinfo = json_decode($params['moduleinfo']);
        $modinfo->course = $course->id;

        $modform = json_decode($params['mform']);

        $modulerequire = "$CFG->dirroot/mod/$modinfo->modulename/lib.php";
        if (file_exists($modulerequire)) {
            require_once($modulerequire);
        } else {
            $warnings['message'] = "File not found";
        }

        $moduleformatfunction = $modinfo->modulename . '_formatted_moduleinfo';

        if (function_exists($moduleformatfunction)) {
            $modinfo = $moduleformatfunction($modinfo);
        }

        $moduleformformatfunction = $modinfo->modulename . '_formatted_modform';
        if (function_exists($moduleformformatfunction)) {
            $modform = $moduleformformatfunction($modform);
        }

        $data = new stdClass();
        if ($modform) {
            $data = $modform->get_data();
        }

        // Attempt to include module library before we make any changes to DB.
        include_modulelib($modinfo->modulename);

        $modinfo->course = $course->id;
        $modinfo = set_moduleinfo_defaults($modinfo);

        if (!empty($course->groupmodeforce) or !isset($modinfo->groupmode)) {
            $modinfo->groupmode = $cm->groupmode; // Keep original.
        }

        // Update course module first.
        $cm->groupmode = $modinfo->groupmode;
        if (isset($modinfo->groupingid)) {
            $cm->groupingid = $modinfo->groupingid;
        }
        $completion = new completion_info($course);
        if ($completion->is_enabled()) {
            // Completion settings that would affect users who have already completed
            // the activity may be locked; if so, these should not be updated.
            if (!empty($modinfo->completionunlocked)) {
                $cm->completion = $modinfo->completion;
                $cm->completiongradeitemnumber = $modinfo->completiongradeitemnumber;
                $cm->completionview = $modinfo->completionview;
            }
            // The expected date does not affect users who have completed the activity,
            // so it is safe to update it regardless of the lock status.
            $cm->completionexpected = $modinfo->completionexpected;
        }

        if (!empty($CFG->enableavailability)) {
            // This code is used both when submitting the form, which uses a long
            // name to avoid clashes, and by unit test code which uses the real
            // name in the table.
            if (property_exists($modinfo, 'availabilityconditionsjson')) {
                if ($modinfo->availabilityconditionsjson !== '') {
                    $cm->availability = $modinfo->availabilityconditionsjson;
                } else {
                    $cm->availability = null;
                }
            } else if (property_exists($modinfo, 'availability')) {
                $cm->availability = $modinfo->availability;
            }
            // If there is any availability data, verify it.
            if ($cm->availability) {
                $tree = new \core_availability\tree(json_decode($cm->availability));
                // Save time and database space by setting null if the only data
                // is an empty tree.
                if ($tree->is_empty()) {
                    $cm->availability = null;
                }
            }
        }
        if (isset($modinfo->showdescription)) {
            $cm->showdescription = $modinfo->showdescription;
        } else {
            $cm->showdescription = 0;
        }

        $DB->update_record('course_modules', $cm);

        $modcontext = context_module::instance($modinfo->coursemodule);

        // Update embedded links and save files.
        if (plugin_supports('mod', $modinfo->modulename, FEATURE_MOD_INTRO, true)) {
            $modinfo->intro = file_save_draft_area_files($modinfo->introeditor['itemid'], $modcontext->id,
                'mod_' . $modinfo->modulename, 'intro', 0,
                array('subdirs' => true), $modinfo->introeditor['text']);
            $modinfo->introformat = $modinfo->introeditor['format'];
            unset($modinfo->introeditor);
        }

        // Get the a copy of the grade_item before it is modified incase we need to scale the grades.
        $oldgradeitem = null;
        $newgradeitem = null;
        if (!empty($data->grade_rescalegrades) && $data->grade_rescalegrades == 'yes') {
            // Fetch the grade item before it is updated.
            $oldgradeitem = grade_item::fetch(array('itemtype' => 'mod',
                'itemmodule' => $modinfo->modulename,
                'iteminstance' => $modinfo->instance,
                'itemnumber' => 0,
                'courseid' => $modinfo->course));
        }

        $updateinstancefunction = $modinfo->modulename . "_update_instance";
        if (!$updateinstancefunction($modinfo, $mform)) {
            print_error('cannotupdatemod', '', course_get_url($course, $cm->section), $modinfo->modulename);
        }

        // This needs to happen AFTER the grademin/grademax have already been updated.
        if (!empty($data->grade_rescalegrades) && $data->grade_rescalegrades == 'yes') {
            // Get the grade_item after the update call the activity to scale the grades.
            $newgradeitem = grade_item::fetch(array('itemtype' => 'mod',
                'itemmodule' => $modinfo->modulename,
                'iteminstance' => $modinfo->instance,
                'itemnumber' => 0,
                'courseid' => $modinfo->course));
            if ($newgradeitem && $oldgradeitem->gradetype == GRADE_TYPE_VALUE && $newgradeitem->gradetype == GRADE_TYPE_VALUE) {
                $params = array(
                    $course,
                    $cm,
                    $oldgradeitem->grademin,
                    $oldgradeitem->grademax,
                    $newgradeitem->grademin,
                    $newgradeitem->grademax
                );
                if (!component_callback('mod_' . $modinfo->modulename, 'rescale_activity_grades', $params)) {
                    print_error('cannotreprocessgrades', '', course_get_url($course, $cm->section), $modinfo->modulename);
                }
            }
        }

        // Make sure visibility is set correctly (in particular in calendar).
        if (has_capability('moodle/course:activityvisibility', $modcontext)) {
            set_coursemodule_visible($modinfo->coursemodule, $modinfo->visible);
        }

        if (isset($modinfo->cmidnumber)) { // Label.
            // Set cm idnumber - uniqueness is already verified by form validation.
            set_coursemodule_idnumber($modinfo->coursemodule, $modinfo->cmidnumber);
        }

        // Update module tags.
        if (core_tag_tag::is_enabled('core', 'course_modules') && isset($modinfo->tags)) {
            core_tag_tag::set_item_tags('core', 'course_modules', $modinfo->coursemodule, $modcontext, $modinfo->tags);
        }

        // Now that module is fully updated, also update completion data if required.
        // (this will wipe all user completion data and recalculate it)
        if ($completion->is_enabled() && !empty($modinfo->completionunlocked)) {
            $completion->reset_all_state($cm);
        }
        $cm->name = $modinfo->name;

        \core\event\course_module_updated::create_from_cm($cm, $modcontext)->trigger();

        $modinfo = edit_module_post_actions($modinfo, $course);

        $result = array();
        $result['warnings'] = $warnings;
        $result['cm'] = json_encode($cm);
        $result['moduleinfo'] = json_encode($modinfo);
        return $result;
    }

    public static function update_moduleinfo_by_returns()
    {
        return new external_single_structure(
            array(
                'cm' => new external_value(PARAM_RAW, 'the course module'),
                'moduleinfo' => new external_value(PARAM_RAW, 'the module info'),
                'warnings' => new external_warnings()
            )
        );
    }

    public static function get_record_snapshot_by_parameters()
    {
        return new external_function_parameters(
            array(
                'tablename' => new external_value(PARAM_RAW, ' the table name'),
                'id' => new external_value(PARAM_INT, 'the id')
            )
        );
    }

    public static function get_record_snapshot_by($tablename, $id)
    {
        global $DB;
        $warning = array();

        $params = self::validate_parameters(self::get_record_snapshot_by_parameters(), array(
            'tablename' => $tablename,
            'id' => $id
        ));

        $record = $DB->get_record($tablename, array('id' => $id));

        $result = array();
        $result['record'] = json_encode($record);
        $result['warnings'] = $warning;

        return $result;
    }

    public static function get_record_snapshot_by_returns()
    {
        return new external_single_structure(
            array(
                'record' => new external_value(PARAM_RAW, 'the record that encode'),
                'warnings' => new external_warnings()
            )
        );
    }

    public static function page_get_coursemodule_info_by_parameters()
    {
        return new external_function_parameters(
            array(
                'id' => new external_value(PARAM_INT, 'The id of page')
            )
        );
    }

    public static function page_get_coursemodule_info_by($id)
    {
        global $DB;
        $warnings = array();

        $params = self::validate_parameters(self::page_get_coursemodule_info_by_parameters(), array(
            'id' => $id
        ));

        $page = $DB->get_record('page', array('id' => $params['id']),
            'id, name, display, displayoptions, intro, introformat');

        if (!$page) {
            $page = new stdClass();
        }

        $result = array();
        $result['page'] = $page;
        $result['warnings'] = $warnings;
        return $result;
    }

    public static function page_get_coursemodule_info_by_returns()
    {
        return new external_single_structure(
            array(
                'page' => new external_single_structure(
                    array(
                        'id' => new external_value(PARAM_INT, 'the id of page'),
                        'name' => new external_value(PARAM_RAW, 'the name of page'),
                        'intro' => new external_value(PARAM_RAW, 'introduction'),
                        'introformat' => new external_value(PARAM_INT, 'intro format'),
                        'display' => new external_value(PARAM_INT, 'display'),
                        'displayoptions' => new external_value(PARAM_RAW, 'display options')
                    )
                ),
                'warnings' => new external_warnings()
            )
        );
    }

    public static function get_field_modname_by_id_parameters()
    {
        return new external_function_parameters(
            array(
                'tablename' => new external_value(PARAM_RAW, 'the table name'),
                'id' => new external_value(PARAM_INT, 'the id')
            )
        );
    }

    public static function get_field_modname_by_id($tablename, $id)
    {
        global $DB;
        $warnings = array();

        $params = self::validate_parameters(self::get_field_modname_by_id_parameters(), array(
            'tablename' => $tablename,
            'id' => $id
        ));

        $name = $DB->get_field($params['tablename'], "name", array("id" => $params['id']));

        $result = array();
        $result['name'] = $name;
        $result['warnings'] = $warnings;
        return $result;
    }

    public static function get_field_modname_by_id_returns()
    {
        return new external_single_structure(
            array(
                'name' => new external_value(PARAM_RAW, 'the name'),
                'warnings' => new external_warnings()
            )
        );
    }

    private static function validate_course_module($cmmixed, $throwexception = true)
    {
        $cm = $cmmixed;
        if (!is_object($cm)) {
            $cmrecord = self::get_coursemodule_from_id(null, $cmmixed);
            $modinfo = get_fast_modinfo($cmrecord->course);
            $cm = $modinfo->get_cm($cmmixed);
        } else if (!$cm instanceof cm_info) {
            // Assume we got a course module record.
            $modinfo = get_fast_modinfo($cm->course);
            $cm = $modinfo->get_cm($cm->id);
        }

        if (!$cm->uservisible) {
            if ($throwexception) {
                throw new require_login_exception('Course module is hidden');
            } else {
                return false;
            }
        }

        return true;
    }

    private static function get_coursemodule_from_id($modulename, $cmid, $courseid = 0, $sectionnum = false, $strictness = IGNORE_MISSING)
    {
        global $DB;

        $params = array('cmid' => $cmid);

        if (!$modulename) {
            if (!$modulename = $DB->get_field_sql("SELECT md.name
                                                 FROM {modules} md
                                                 JOIN {course_modules} cm ON cm.module = md.id
                                                WHERE cm.id = :cmid", $params, $strictness)
            ) {
                return false;
            }
        } else {
            if (!core_component::is_valid_plugin_name('mod', $modulename)) {
                throw new coding_exception('Invalid modulename parameter');
            }
        }

        $params['modulename'] = $modulename;

        $courseselect = "";
        $sectionfield = "";
        $sectionjoin = "";

        if ($courseid) {
            $courseselect = "AND cm.course = :courseid";
            $params['courseid'] = $courseid;
        }

        if ($sectionnum) {
            $sectionfield = ", cw.section AS sectionnum";
            $sectionjoin = "LEFT JOIN {course_sections} cw ON cw.id = cm.section";
        }

        $sql = "SELECT cm.*, m.name, md.name AS modname $sectionfield
              FROM {course_modules} cm
                   JOIN {modules} md ON md.id = cm.module
                   JOIN {" . $modulename . "} m ON m.id = cm.instance
                   $sectionjoin
             WHERE cm.id = :cmid AND md.name = :modulename
                   $courseselect";

        return $DB->get_record_sql($sql, $params, $strictness);
    }

    public static function delete_course_modules_parameters()
    {
        return new external_function_parameters(
            array(
                'modname' => new external_value(PARAM_RAW, 'the mod name'),
                'parameters' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'name' => new external_value(PARAM_RAW, 'param name'),
                            'value' => new external_value(PARAM_RAW, 'param value'),
                        )
                    ), 'the params'
                )
            )
        );
    }

    public static function delete_course_modules($modname, $parameters)
    {
        global $DB;
        $warnings = array();

        $params = self::validate_parameters(self::delete_course_modules_parameters(), array(
            'modname' => $modname,
            'parameters' => $parameters
        ));

        $result = array();
        $arr = array();
        foreach ($params['parameters'] as $p) {
            $arr = array_merge($arr, array($p['name'] => $p['value']));
        }

        $transaction = $DB->start_delegated_transaction();
        $result['status'] = $DB->delete_records($params['modname'], $arr);
        $transaction->allow_commit();

        $result['warnings'] = $warnings;

        return $result;
    }

    public static function delete_course_modules_returns()
    {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'bool: true if delete success'),
                'warnings' => new external_warnings()
            )
        );
    }

    public static function delete_instance_by_parameters()
    {
        return new external_function_parameters(
            array(
                'contextlevel' => new external_value(PARAM_INT, 'the context level'),
                'instanceid' => new external_value(PARAM_INT, 'the instance id')
            )
        );
    }

    public static function delete_instance_by($contextlevel, $instanceid)
    {
        global $CFG, $DB;
        $warnings = array();

        require_once($CFG->dirroot . '/lib/accesslib.php');

        $params = self::validate_parameters(self::delete_instance_by_parameters(), array(
            'contextlevel' => $contextlevel,
            'instanceid' => $instanceid
        ));

        $result = array();

        // double check the context still exists
        if ($record = $DB->get_record('context', array('contextlevel' => $params['contextlevel'], 'instanceid' => $params['instanceid']))) {
            $context = context::create_instance_from_record($record);
            $context->delete();
            $status = true;
        } else {
            $status = false;
        }

        $result['status'] = $status;
        $result['warnings'] = $warnings;
        return $result;
    }

    public static function delete_instance_by_returns()
    {
        return self::delete_course_modules_returns();
    }

    public static function delete_mod_from_section_by_parameters()
    {
        return new external_function_parameters(
            array(
                'modid' => new external_value(PARAM_INT, 'the module id'),
                'sectionid' => new external_value(PARAM_INT, 'the section id')
            )
        );
    }

    public static function delete_mod_from_section_by($modid, $sectionid)
    {
        global $CFG, $DB;
        $warnings = array();

        $params = self::validate_parameters(self::delete_mod_from_section_by_parameters(), array(
            'modid' => $modid,
            'sectionid' => $sectionid
        ));

        require_once($CFG->dirroot . '/course/lib.php');

        $status = delete_mod_from_section($params['modid'], $params['sectionid']);

        $result = array();
        $result['status'] = $status;
        $result['warnings'] = $warnings;
        return $result;
    }

    public static function delete_mod_from_section_by_returns()
    {
        return self::delete_course_modules_returns();
    }

    public static function course_delete_module_by_parameters()
    {
        return new external_function_parameters(
            array(
                'cmid' => new external_value(PARAM_INT, 'the id of course module')
            )
        );
    }

    public static function course_delete_module_by($cmid)
    {
        global $CFG;
        $warnings = array();
        require_once($CFG->dirroot . '/course/lib.php');

        $params = self::validate_parameters(self::course_delete_module_by_parameters(), array(
            'cmid' => $cmid
        ));

        $result = array();

        course_delete_module($params['cmid']);

        $result['status'] = true;
        $result['warnings'] = $warnings;
        return $result;
    }

    public static function course_delete_module_by_returns()
    {
        return self::delete_course_modules_returns();
    }

    public static function get_scales_menu_sql_parameters()
    {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'the id of course')
            )
        );
    }

    public static function get_scales_menu_sql($courseid)
    {
        global $DB;
        $warnings = array();

        $params = self::validate_parameters(self::get_scales_menu_sql_parameters(), array(
            'courseid' => $courseid
        ));

        $sql = "SELECT id, name
              FROM {scale}
             WHERE courseid = 0 or courseid = ?
          ORDER BY courseid ASC, name ASC";
        $arr = array($params['courseid']);

        $scales = $DB->get_records_sql($sql, $arr);

        if (!$scales) {
            $scales = array();
        }

        $result = array();
        $result['scales'] = $scales;
        $result['warnings'] = $warnings;
        return $result;
    }

    public static function get_scales_menu_sql_returns()
    {
        return new external_single_structure(
            array(
                'scales' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'the id of scale', VALUE_OPTIONAL),
                            'name' => new external_value(PARAM_RAW, 'the name of scale', VALUE_OPTIONAL)
                        )
                    ), 'data'
                ),
                'warnings' => new external_warnings()
            )
        );
    }

    public static function get_scale_by_id_parameters()
    {
        return new external_function_parameters(
            array(
                'id' => new external_value(PARAM_INT, 'the id of scale')
            )
        );
    }

    public static function get_scale_by_id($id)
    {
        global $DB;
        $warnings = array();

        $params = self::validate_parameters(self::get_scale_by_id_parameters(), array(
            'id' => $id
        ));

        $scale = $DB->get_record('scale', array('id' => $params['id']));
        if (!$scale) {
            $scale = new stdClass();
        }

        $result = array();
        $result['scale'] = $scale;
        $result['warnings'] = $warnings;
        return $result;
    }

    public static function get_scale_by_id_returns()
    {
        return new external_single_structure(
            array(
                'scale' => new external_single_structure(
                    array(
                        'id' => new external_value(PARAM_INT, 'the id of scale', VALUE_OPTIONAL),
                        'courseid' => new external_value(PARAM_INT, 'the id of course', VALUE_OPTIONAL),
                        'userid' => new external_value(PARAM_INT, 'the id of user', VALUE_OPTIONAL),
                        'name' => new external_value(PARAM_RAW, 'the name of scale', VALUE_OPTIONAL),
                        'scale' => new external_value(PARAM_RAW, 'the scale', VALUE_OPTIONAL),
                        'description' => new external_value(PARAM_RAW, 'the description of scale', VALUE_OPTIONAL),
                        'descriptionformat' => new external_value(PARAM_INT, 'the description format', VALUE_OPTIONAL),
                        'timemodified' => new external_value(PARAM_INT, 'the time modified', VALUE_OPTIONAL),
                    )
                ),
                'warnings' => new external_warnings()
            )
        );
    }

    public static function get_count_sql_parameters()
    {
        return new external_function_parameters(
            array(
                'sql' => new external_value(PARAM_RAW, 'the query sql'),
                'parameters' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'name' => new external_value(PARAM_RAW, 'param name'),
                            'value' => new external_value(PARAM_RAW, 'param value'),
                        )
                    ), 'the params'
                )
            )
        );
    }

    public static function get_count_sql($sql, $parameters)
    {
        global $DB;
        $warnings = array();

        $params = self::validate_parameters(self::get_count_sql_parameters(), array(
            'sql' => $sql,
            'parameters' => $parameters
        ));

        $arr = array();
        foreach ($params['parameters'] as $p) {
            $arr = array_merge($arr, array($p['name'] => $p['value']));
        }

        $count = $DB->count_records_sql($params['sql'], $arr);

        $result = array();
        $result['count'] = $count;
        $result['warnings'] = $warnings;
        return $result;
    }

    public static function get_count_sql_returns()
    {
        return new external_single_structure(
            array(
                'count' => new external_value(PARAM_INT, 'the count'),
                'warnings' => new external_warnings()
            )
        );
    }
}