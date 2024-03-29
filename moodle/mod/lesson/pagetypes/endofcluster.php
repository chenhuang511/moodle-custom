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
 * End of cluster
 *
 * @package mod_lesson
 * @copyright  2009 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

defined('MOODLE_INTERNAL') || die();

 /** End of Cluster page */
define("LESSON_PAGE_ENDOFCLUSTER",   "31");

class lesson_page_type_endofcluster extends lesson_page {

    protected $type = lesson_page::TYPE_STRUCTURE;
    protected $typeidstring = 'endofcluster';
    protected $typeid = LESSON_PAGE_ENDOFCLUSTER;
    protected $string = null;
    protected $jumpto = null;

    public function display($renderer, $attempt) {
        return '';
    }
    public function get_typeid() {
        return $this->typeid;
    }
    public function get_typestring() {
        if ($this->string===null) {
            $this->string = get_string($this->typeidstring, 'lesson');
        }
        return $this->string;
    }
    public function get_idstring() {
        return $this->typeidstring;
    }
    public function callback_on_view($canmanage) {
        $this->redirect_to_next_page($canmanage);
        exit;
    }
    public function redirect_to_next_page() {
        global $PAGE;
        if ($this->properties->nextpageid == 0) {
            $nextpageid = LESSON_EOL;
        } else {
            $nextpageid = $this->properties->nextpageid;
        }
        redirect(new moodle_url('/mod/lesson/remote/view.php', array('id'=>$PAGE->cm->id,'pageid'=>$nextpageid)));
    }
    public function get_grayout() {
        return 1;
    }

    public function override_next_page() {
        global $DB;
        $params = array();
        $params['parameters[0][name]'] ="pageid";
        $params['parameters[0][value]'] =$this->properties->id;
        $params['parameters[1][name]'] ="lessonid";
        $params['parameters[1][value]'] =$this->lesson->id;

        $jump = get_remote_field_by("lesson_answers", $params, "jumpto");
        if ($jump == LESSON_NEXTPAGE) {
            if ($this->properties->nextpageid == 0) {
                return LESSON_EOL;
            } else {
                return $this->properties->nextpageid;
            }
        } else {
            return $jump;
        }
    }
    public function add_page_link($previd) {
        global $PAGE, $CFG;
        if ($previd != 0) {
            $addurl = new moodle_url('/mod/lesson/remote/editpage.php', array('id'=>$PAGE->cm->id, 'pageid'=>$previd, 'sesskey'=>sesskey(), 'qtype'=>LESSON_PAGE_ENDOFCLUSTER));
            return array('addurl'=>$addurl, 'type'=>LESSON_PAGE_ENDOFCLUSTER, 'name'=>get_string('addendofcluster', 'lesson'));
        }
        return false;
    }
    public function valid_page_and_view(&$validpages, &$pageviews) {
        return $this->properties->nextpageid;
    }
}

class lesson_add_page_form_endofcluster extends lesson_add_page_form_base {

    public $qtype = LESSON_PAGE_ENDOFCLUSTER;
    public $qtypestring = 'endofcluster';
    protected $standard = false;

    public function custom_definition() {
        global $PAGE;

        $mform = $this->_form;
        $lesson = $this->_customdata['lesson'];
        $jumptooptions = lesson_page_type_branchtable::get_jumptooptions(optional_param('firstpage', false, PARAM_BOOL), $lesson);

        $mform->addElement('hidden', 'firstpage');
        $mform->setType('firstpage', PARAM_BOOL);

        $mform->addElement('hidden', 'qtype');
        $mform->setType('qtype', PARAM_TEXT);

        $mform->addElement('text', 'title', get_string("pagetitle", "lesson"), array('size'=>70));
        $mform->setType('title', PARAM_TEXT);

        $this->editoroptions = array('noclean'=>true, 'maxfiles'=>EDITOR_UNLIMITED_FILES, 'maxbytes'=>$PAGE->course->maxbytes);
        $mform->addElement('editor', 'contents_editor', get_string("pagecontents", "lesson"), null, $this->editoroptions);
        $mform->setType('contents_editor', PARAM_RAW);

        $this->add_jumpto(0);
    }

    public function construction_override($pageid, lesson $lesson) {
        global $CFG, $PAGE, $DB;
        require_sesskey();

        $timenow = time();

        // the new page is not the first page (end of cluster always comes after an existing page)
        $params = array();
        $params['parameters[0][name]'] = "id";
        $params['parameters[0][value]'] = $pageid;
        if (!$page = get_remote_lesson_pages_by($params)) {
            print_error('cannotfindpages', 'lesson');
        }

        // could put code in here to check if the user really can insert an end of cluster

        $newpage = new stdClass;
        $newpage->lessonid = $lesson->id;
        $newpage->prevpageid = $pageid;
        $newpage->nextpageid = $page->nextpageid;
        $newpage->qtype = $this->qtype;
        $newpage->timecreated = $timenow;
        $newpage->title = get_string("endofclustertitle", "lesson");
        $newpage->contents = get_string("endofclustertitle", "lesson");
        $newpageid = $DB->insert_record("lesson_pages", $newpage);
        // update the linked list...
        $DB->set_field("lesson_pages", "nextpageid", $newpageid, array("id" => $pageid));
        if ($page->nextpageid) {
            // the new page is not the last page
            $DB->set_field("lesson_pages", "prevpageid", $newpageid, array("id" => $page->nextpageid));
        }
        // ..and the single "answer"
        $newanswer = new stdClass;
        $newanswer->lessonid = $lesson->id;
        $newanswer->pageid = $newpageid;
        $newanswer->timecreated = $timenow;
        $newanswer->jumpto = LESSON_NEXTPAGE;
        $newanswerid = $DB->insert_record("lesson_answers", $newanswer);
        $lesson->add_message(get_string('addedendofcluster', 'lesson'), 'notifysuccess');
        redirect($CFG->wwwroot.'/mod/lesson/remote/edit.php?id='.$PAGE->cm->id);
    }
}
