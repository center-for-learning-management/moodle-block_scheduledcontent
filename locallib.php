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

namespace block_scheduledcontent;

defined('MOODLE_INTERNAL') || die;

class lib {
    public static function addschedule() {
        global $DB;
        $schedule = (object) array(
            'sort' => 99,
            'timestart' => '',
            'timeend' => '',
            'caption' => '',
            'showonpage' => '',
            'showonpageformat' => 1,
            'showinmodal' => '',
            'showinmodalformat' => 1,
        );
        $rule->id = $DB->insert_record('block_scheduledcontent', $rule);
        return $rule;
    }
}
