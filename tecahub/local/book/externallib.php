<?php
/**
 * External lesson API
 *
 * @package    core_local_lesson
 * @category   external
 * @copyright  2009 Petr Skodak
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

/**
 * Book external functions
 *
 * @package    core_local_book
 * @category   external
 * @copyright  2016 Teca
 * @license    http://tecapro.vn
 * @since Moodle 3.1
 */
class local_mod_book_external extends external_api
{
    public static function mod_get_book_by_id_parameters()
    {
        return new external_function_parameters(
            array('bookid' => new external_value(PARAM_INT, 'book id'))
        );
    }

    public static function mod_get_book_by_id($bookid)
    {
        global $DB;

        // validate params
        $params = self::validate_parameters(self::mod_get_book_by_id_parameters(),
            array(
                'bookid' => $bookid,
            )
        );

        return $DB->get_record('book', array('id' => $params['bookid']), '*', MUST_EXIST);
    }

    public static function mod_get_book_by_id_returns()
    {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'book id'),
                'course' => new external_value(PARAM_INT, 'course id', VALUE_DEFAULT),
                'name' => new external_value(PARAM_RAW, 'book name'),
                'intro' => new external_value(PARAM_RAW, 'book intro'),
                'introformat' => new external_value(PARAM_INT, 'intro format', VALUE_DEFAULT),
                'numberring' => new external_value(PARAM_INT, 'numberring', VALUE_DEFAULT),
                'navstyle' => new external_value(PARAM_INT, 'navstyle', 1),
                'customtitles' => new external_value(PARAM_INT, 'custom titles', VALUE_DEFAULT),
                'revision' => new external_value(PARAM_INT, 'revision', VALUE_DEFAULT),
                'timecreated' => new external_value(PARAM_INT, 'time created', VALUE_DEFAULT),
                'timemodified' => new external_value(PARAM_INT, 'time modified', VALUE_DEFAULT),
            )
        );
    }

    public static function mod_get_book_chapters_by_id_parameters()
    {
        return new external_function_parameters(
            array('bookid' => new external_value(PARAM_INT, 'book id'))
        );
    }

    public static function mod_get_book_chapters_by_id($bookid)
    {
        global $DB;

        // validate params
        $params = self::validate_parameters(self::mod_get_book_by_id_parameters(),
            array(
                'bookid' => $bookid,
            )
        );

        return $DB->get_records('book_chapters', array('bookid' => $params['bookid']), 'pagenum', 'id, pagenum, subchapter, title, hidden');
    }

    public static function mod_get_book_chapters_by_id_returns()
    {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'book chapters id'),
                    'pagenum' => new external_value(PARAM_INT, 'page num', VALUE_DEFAULT),
                    'subchapter' => new external_value(PARAM_INT, 'subchapter', VALUE_DEFAULT),
                    'title' => new external_value(PARAM_RAW, 'title'),
                    'hidden' => new external_value(PARAM_INT, 'hidden', VALUE_DEFAULT),
                )
            ));
    }

    public static function mod_get_book_chapters_by_bookid_chapterid_parameters(){
        return new external_function_parameters(
            array(
                'bookid' => new external_value(PARAM_INT, 'book ID'),
                'chapterid' => new external_value(PARAM_INT, 'chapter ID')
            )
        );
    }

    public static function mod_get_book_chapters_by_bookid_chapterid($bookid, $chapterid){
        global $DB;
        
        // validate
        $params = self::validate_parameters(self::mod_get_book_chapters_by_bookid_chapterid_parameters(), 
            array(
                'bookid'    =>  $bookid,
                'chapterid'    => $chapterid
            )
        );
        
        return $DB->get_record('book_chapters', array('id' => $params['chapterid'], 'bookid' => $params['bookid']), "*", MUST_EXIST);
    }

    public static function mod_get_book_chapters_by_bookid_chapterid_returns(){
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'book chapters id'),
                'bookid' => new external_value(PARAM_INT, 'book ID', VALUE_DEFAULT),
                'pagenum' => new external_value(PARAM_INT, 'page num', VALUE_DEFAULT),
                'subchapter' => new external_value(PARAM_INT, 'subchapter', VALUE_DEFAULT),
                'title' => new external_value(PARAM_RAW, 'title'),
                'content' => new external_value(PARAM_RAW, 'Content chapter'),
                'contentformat' => new external_value(PARAM_INT, 'Content format', VALUE_DEFAULT),
                'hidden' => new external_value(PARAM_INT, 'hidden', VALUE_DEFAULT),
                'timecreated' => new external_value(PARAM_INT, 'time created', VALUE_DEFAULT),
                'timemodified' => new external_value(PARAM_INT, 'time modified', VALUE_DEFAULT),
                'importsrc' => new external_value(PARAM_RAW, 'import source'),
            )
        );
    }

    public static function mod_get_course_sections_by_id_parameters(){
        return new external_function_parameters(
            array(
                'sectionid'    =>  new external_value(PARAM_INT, 'Section ID')
            )
        );
    }

    public static function mod_get_course_sections_by_id($sectionid){
        global $DB;

        // validate
        $params = self::validate_parameters(self::mod_get_course_sections_by_id_parameters(),
            array(
                'sectionid'    =>  $sectionid,
            )
        );

        return $DB->get_record('course_sections', array('id' =>  $params['sectionid']), "section", MUST_EXIST);
    }

    public static function mod_get_course_sections_by_id_returns(){
        return new external_single_structure(
            array(
                'section' => new external_value(PARAM_INT, 'Section number', VALUE_DEFAULT),
            )
        );
    }
}

