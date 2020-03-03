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
    public static $editoroptions = array('subdirs'=>0, 'maxbytes'=>1024*1024, 'maxfiles'=>10,
                           'changeformat'=>1, 'context'=>null, 'noclean'=>0,
                           'trusttext'=>0, 'enable_filemanagement' => true);


    function definition() {
        global $DB, $schedules;

        $mform = $this->_form;
        $this->add_my_action_buttons($mform);
        $mform->addElement('hidden', 'courseid', 0);
        $mform->addElement('hidden', 'elements', count($schedules));
        foreach ($schedules AS $a => $schedule) {
            $mform->addElement('header', 'schedule_' . $a,  (!empty($schedule->caption) ? $schedule->caption : '#' . ($a + 1) ));
            if (!empty($schedule->caption)) {
                $mform->setExpanded('schedule_' . $a, false);
            }
            $mform->addElement('hidden', 'id' . $a, $schedule->id);
            $mform->setType('id' . $a, PARAM_INT);
            $mform->addElement('text', 'caption' . $a, get_string('caption', 'block_scheduledcontent'));
            $mform->setType('caption' . $a, PARAM_TEXT);
            $mform->addElement('text', 'sort' . $a, get_string('order'));
            $mform->setType('sort' . $a, PARAM_INT);
            $mform->setDefault('sort', 0);

            $mform->addElement('date_time_selector', 'timestart' . $a, get_string('from'));
            $mform->setType('timestart' . $a, PARAM_INT);
            $mform->addElement('date_time_selector', 'timeend' . $a, get_string('to'));
            $mform->setType('timeend' . $a, PARAM_INT);

            $mform->addElement('editor', 'showonpage' . $a, get_string('showonpage', 'block_scheduledcontent'), null, self::$editoroptions);
            $mform->setType('showonpage' . $a, PARAM_RAW);
            $mform->addElement('editor', 'showinmodal' . $a, get_string('showinmodal', 'block_scheduledcontent'), null, self::$editoroptions);
            $mform->setType('showinmodal' . $a, PARAM_RAW);

            $mform->closeHeaderBefore('schedule_' . $a);
        }

        $this->add_my_action_buttons($mform);
    }
    function add_my_action_buttons($mform) {
        $buttonarray=array();
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        $buttonarray[] = $mform->createElement('submit', 'addschedule', get_string('add_schedule', 'block_scheduledcontent'));
        //$buttonarray[] = $mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', ' ', false);
    }

    //Custom validation should be added here
    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        return $errors;
    }
}
