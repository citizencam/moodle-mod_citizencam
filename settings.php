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
 * Url module admin settings and defaults
 *
 * @package   mod_citizencam
 * @copyright 2017 CitizenCam dev@citizencam.eu
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once("$CFG->libdir/resourcelib.php");

    $settings->add(new admin_setting_configtext('citizencam/citizencam_studio_url', get_string('settings_studio_url', 'citizencam'),
        get_string('settings_studio_url_helptext', 'citizencam'), ''));

    $settings->add(new admin_setting_configtext('citizencam/citizencam_cctv_url', get_string('settings_cctv_url', 'citizencam'),
        get_string('settings_cctv_url_helptext', 'citizencam'), ''));
}