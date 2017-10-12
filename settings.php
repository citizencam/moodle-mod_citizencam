<?php
/**
 * Url module admin settings and defaults
 *
 * @package   mod
 * @subpackage citizencam
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