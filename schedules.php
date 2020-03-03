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

require_login();
$PAGE->set_url(new \moodle_url('/blocks/scheduledcontent/schedules.php', array()));
$PAGE->set_context(\context_system::instance());
$PAGE->set_heading(get_string('pluginname', 'block_scheduledcontent'));
$PAGE->set_title(get_string('pluginname', 'block_scheduledcontent'));
$PAGE->requires->css('/block/scheduledcontent/main.css');

echo $OUTPUT->header();
if (!is_siteadmin()) {
    echo $OUTPUT->render_from_template('block_scheduledcontent/alert', array(
        'type' => 'danger',
        'content' => get_string('access_denied', 'block_scheduledcontent'),
        'url' => new \moodle_url('/my', array()),
    ));
    echo $OUTPUT->footer();
    die();
}

if (optional_param('addschedule', 0, PARAM_INT) == 1) {
    lib::addschedule();
    redirect($PAGE->url->__toString());
}

require_once($CFG->dirroot . '/blocks/scheduledcontent/classes/form_schedules.php');
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

        $context = \context_system::instance();

        $showonpagetext = file_save_draft_area_files($showonpagedraftid, $context->id, 'block_scheduledcontent', 'showonpage',
                            $data->{'id' . $a}, array('subdirs'=>false), $showonpagetext);
        $showinmodaltext = file_save_draft_area_files($showinmodaldraftid, $context->id, 'block_scheduledcontent', 'showinmodal',
                            $data->{'id' . $a}, array('subdirs'=>false), $showinmodaltext);

        $schedule = (object) array(
            'id' => $data->{'id' . $a},
            'sort' => $data->{'sort' . $a},
            'timestart' => $data->{'timestart' . $a},
            'timeend' => $data->{'timeend' . $a},
            'caption' => $data->{'caption' . $a},
            'showonpage' => $showonpagetext,
            'showonpageformat' => $showonpageformat,
            'showinmodal' => $showinmodaltext,
            'showinmodalformat' => $showinmodalformat,
        );
        if ($DB->update_record('block_scheduledcontent', $schedule)) {
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
$mform->display();

echo $OUTPUT->footer();