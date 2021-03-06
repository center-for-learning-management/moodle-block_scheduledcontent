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
 * @copyright  2020 Center for Learningmanagement (www.lernmanagement.at)
 * @author     Robert Schrenk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/blocks/moodleblock.class.php');
require_once($CFG->dirroot . '/blocks/scheduledcontent/locallib.php');

class block_scheduledcontent extends block_base /* was block_list */ {
    public function init() {
        $this->title = get_string('pluginname', 'block_scheduledcontent');
    }
    public function get_content() {
        global $CFG, $COURSE, $DB, $PAGE;
        // $PAGE->requires->css('/blocks/scheduledcontent/style/main.css');

        if ($this->content !== null) {
          return $this->content;
        }

        $this->content = new stdClass;
        $this->content->title = "";
        $this->content->text  = "";
        $this->content->footer = "";

        $sql = "SELECT *
                    FROM {block_scheduledcontent}
                    WHERE timestart<?
                        AND timeend>?
                        AND courseid=?
                    ORDER BY sort ASC";
        $time = time();
        $schedules = array_values($DB->get_records_sql($sql, array($time, $time, $COURSE->id)));
        $context = \context_course::instance($COURSE->id);

        $canmanage = $PAGE->user_is_editing($this->instance->id);
        if (!empty($canmanage) && has_capability('block/scheduledcontent:manage', $context)) {
            $this->content->text = '<p>' . get_string('currently_no_scheduled_contents', 'block_scheduledcontent', array('no' => count($schedules))) . '</p>';
            $this->content->text .= '<a href="' . $CFG->wwwroot . '/blocks/scheduledcontent/schedules.php?courseid=' . $COURSE->id . '">';
            $this->content->text .= get_string('modify_contents', 'block_scheduledcontent');
            $this->content->text .= '</a>';
        } else {
            foreach($schedules AS $schedule) {
                if (!empty($schedule->showinmodal)) {
                    $this->content->text .= '<div><a href="#" onclick="require([\'block_scheduledcontent/main\'], function(M) { M.modal(' . $schedule->id . '); }); return false;">';
                    $this->content->text .= $schedule->showonpage;
                    $this->content->text .= '</a></div>';
                } else {
                    $this->content->text .= '<div>' . $schedule->showonpage . '</div>';
                }
            }
        }

        return $this->content;
    }
    public function hide_header() {
        return true;
    }
    public function has_config() {
        return false;
    }
    public function instance_allow_multiple() {
        return false;
    }
    public function instance_delete() {
        global $COURSE, $DB;
        //$DB->delete_records('block_scheduledcontent', array('courseid' => $COURSE->id));
    }
}
