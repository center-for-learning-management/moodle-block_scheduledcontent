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

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $ADMIN->add('blockplugins', new admin_category('block_scheduledcontent', get_string('pluginname', 'block_scheduledcontent')));
    $ADMIN->add('block_scheduledcontent', $settings);
	//$ADMIN->add('localplugins', $settings);
    //$settings->add(new admin_setting_configtext('local_experience/varname', get_string('string:varname', 'local_experience'), '', '', PARAM_TEXT));
    $ADMIN->add(
        'block_scheduledcontent',
        new admin_externalpage(
            'block_scheduledcontent_schedules',
            get_string('schedules', 'block_scheduledcontent'),
            $CFG->wwwroot . '/blocks/scheduledcontent/schedules.php'
        )
    );
}
