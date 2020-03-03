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
 * @copyright  2020 Center for Learning Management (http://www.lernmanagement.at)
 * @author     Robert Schrenk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . "/externallib.php");

class block_scheduledcontent_external extends external_api {
    public static function modal_parameters() {
        return new external_function_parameters(array(
            'id' => new external_value(PARAM_INT, 'id of scheduled content'),
        ));
    }

    /**
     * Get the modal content
     * @return created accesscode
     */
    public static function modal($id) {
        global $DB;
        $params = self::validate_parameters(self::modal_parameters(), array('id' => $id));
        $schedule = $DB->get_record('block_scheduledcontent', array('id' => $id));
        $context = \context_course::instance($schedule->courseid);

        if ($context->get_course_context(false)) {
            if ($schedule->courseid == 1 || has_capability('moodle/course:view', $context)) {
                if ($schedule->timestart < time() AND $schedule->timeend > time()) {
                    return $schedule->showinmodal;
                } else {
                    // Out of period.
                    return "OUT OF PERIOD";
                }
            } else {
                // Missing capability
                return "NO CAPABILITY";
            }
        } else {
            // We are on dashboard - everybody can view that.
            return $schedule->showinmodal;
        }



    }
    /**
     * Return definition.
     * @return external_value
     */
    public static function modal_returns() {
        return new external_value(PARAM_RAW, 'The content as HTML.');
    }
}
