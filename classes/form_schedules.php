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
 * @package    block_scheduledcontent
 * @copyright  2018 Digital Education Society (http://www.dibig.at)
 * @author     Robert Schrenk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_scheduledcontent;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . "/formslib.php");

class form_schedules extends \moodleform {
    static $accepted_types = '';
    static $areamaxbytes = 10485760;
    static $maxbytes = 1024*1024;
    static $maxfiles = 1;
    static $subdirs = 0;

    function definition() {
        global $DB, $schedules;

        $editoroptions = array('subdirs'=>0, 'maxbytes'=>0, 'maxfiles'=>0,
                               'changeformat'=>0, 'context'=>null, 'noclean'=>0,
                               'trusttext'=>0, 'enable_filemanagement' => true);

        $mform = $this->_form;
        $mform->addElement('hidden', 'courseid', 0);
        $mform->addElement('hidden', 'elements', count($schedules));
        foreach ($schedules AS $id => $schedule) {
            $mform->addElement('header', 'schedule_' . $id,  (!empty($schedule->caption) ? $schedule->caption : '#' . $id ));
            $mform->addElement('hidden', 'id' . $id, $schedule->id);
            $mform->setType('id' . $id, PARAM_INT);
            $mform->addElement('text', 'caption' . $id, get_string('caption', 'block_scheduledcontent'));
            $mform->setType('caption' . $id, PARAM_RAW);
            $mform->addElement('text', 'sort' . $id, get_string('order'));
            $mform->setType('sort' . $id, PARAM_INT);
            $mform->setDefault('sort', 0);

            // @todo maybe we have to convert the timestamp to an array to pass it to the form.
            $mform->addElement('date_time_selector', 'timestart' . $id, get_string('from'));
            $mform->setType('timestart' . $id, PARAM_INT);
            $mform->addElement('date_time_selector', 'timeend' . $id, get_string('to'));
            $mform->setType('timeend' . $id, PARAM_INT);

            $mform->addElement('editor', 'showonpage' . $id, get_string('showonpage', 'block_scheduledcontent'));
            $mform->setType('showonpage' . $id, PARAM_RAW);
            $mform->addElement('editor', 'showinmodal' . $id, get_string('showinmodal', 'block_scheduledcontent'));
            $mform->setType('showinmodal' . $id, PARAM_RAW);
        }

        $this->add_action_buttons();
    }

    //Custom validation should be added here
    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        return $errors;
    }
}
