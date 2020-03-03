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
 * @copyright  2020 Zentrum für Lernmanagement (www.lernmanagement.at)
 * @author    Robert Schrenk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Manage the schedules of block_scheduledcontent.
 */

namespace block_scheduledcontent;

require('../../config.php');
require_once(__DIR__ . '/locallib.php');

$courseid = required_param('courseid', PARAM_INT);
$context = \context_course::instance($courseid);

require_login();
$PAGE->set_url(new \moodle_url('/blocks/scheduledcontent/schedules.php', array('courseid' => $courseid)));
$PAGE->set_context($context);
$PAGE->set_heading(get_string('pluginname', 'block_scheduledcontent'));
$PAGE->set_title(get_string('pluginname', 'block_scheduledcontent'));
$PAGE->requires->css('/block/scheduledcontent/main.css');

$settingsnode = $PAGE->settingsnav->add(get_string('pluginname', 'block_scheduledcontent'));
$editurl = new \moodle_url('/blocks/scheduled/schedule.php', array('courseid' => $courseid));
$editnode = $settingsnode->add(get_string('edit'), $editurl);
$editnode->make_active();

echo $OUTPUT->header();
if (!has_capability('block/scheduledcontent:manage', $context)) {
    echo $OUTPUT->render_from_template('block_scheduledcontent/alert', array(
        'type' => 'danger',
        'content' => get_string('access_denied', 'block_scheduledcontent'),
        'url' => new \moodle_url('/my', array()),
    ));
    echo $OUTPUT->footer();
    die();
}

if (!empty(optional_param('addschedule', '', PARAM_TEXT))) {
    lib::addschedule($courseid);
    redirect($PAGE->url->__toString());
}

echo $OUTPUT->render_from_template('block_scheduledcontent/add_schedule', array('courseid' => $courseid));

require_once($CFG->dirroot . '/blocks/scheduledcontent/classes/form_schedules.php');

$schedules = array_values($DB->get_records('block_scheduledcontent', array('courseid' => $courseid), 'sort ASC,timestart ASC,timeend ASC'));
$mform = new form_schedules();
if ($data = $mform->get_data()) {
    $success = 0; $failed = 0;
    for ($a = 0; $a < $data->elements; $a++) {
        $showonpagetext = $data->{'showonpage' . $a}['text'];
        $showonpageformat = $data->{'showonpage' . $a}['format'];
        $showinmodaltext = $data->{'showinmodal' . $a}['text'];
        $showinmodalformat = $data->{'showinmodal' . $a}['format'];

        $showonpagedraftid = file_get_submitted_draft_itemid('showonpage' . $a);
        $showinmodaldraftid = file_get_submitted_draft_itemid('showinmodal' . $a);

        $showonpagetext = file_save_draft_area_files($showonpagedraftid, $context->id, 'block_scheduledcontent', 'showonpage',
                            $data->{'id' . $a}, array('subdirs'=>false), $showonpagetext);
        $showinmodaltext = file_save_draft_area_files($showinmodaldraftid, $context->id, 'block_scheduledcontent', 'showinmodal',
                            $data->{'id' . $a}, array('subdirs'=>false), $showinmodaltext);

        $schedules[$a] = (object) array(
            'id' => $data->{'id' . $a},
            'blockid' => $blockid,
            'sort' => $data->{'sort' . $a},
            'timestart' => $data->{'timestart' . $a},
            'timeend' => $data->{'timeend' . $a},
            'caption' => $data->{'caption' . $a},
            'showonpage' => $showonpagetext,
            'showonpageformat' => $showonpageformat,
            'showinmodal' => $showinmodaltext,
            'showinmodalformat' => $showinmodalformat,
        );
        if ($DB->update_record('block_scheduledcontent', $schedules[$a])) {
            $success++;
        } else {
            $failed++;
        }
    }
    if ($success > 0) {
        echo $OUTPUT->render_from_template('block_scheduledcontent/alert', array(
            'content' => get_string('successfully_saved_no', 'block_scheduledcontent', array('no' => $success)),
            'type' => 'success',
        ));
    }
    if ($failed > 0) {
        echo $OUTPUT->render_from_template('block_scheduledcontent/alert', array(
            'content' => get_string('failed_to_save_no', 'block_scheduledcontent', array('no' => $success)),
            'type' => 'danger',
        ));
    }
}


if (count($schedules) == 0) {
    lib::addschedule($courseid);
    redirect($PAGE->url->__toString());
}

$data = array('courseid' => $courseid);
foreach ($schedules AS $a => $schedule) {
    $fields = array_keys((array) $schedule);
    foreach ($fields AS $field) {
        $data[$field . $a] = $schedule->{$field};
    }
    $data['showonpage' . $a] = array(
        'text' => $schedule->showonpage,
        'format' => $schedule->showonpageformat,
    );
    $data['showinmodal' . $a] = array(
        'text' => $schedule->showinmodal,
        'format' => $schedule->showinmodalformat,
    );
}
$mform->set_data($data);

$mform->display();

echo $OUTPUT->footer();
