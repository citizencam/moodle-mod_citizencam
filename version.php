<?php
/**
 * Defines the version and other meta-info about the plugin
 *
 * Setting the $plugin->version to 0 prevents the plugin from being installed.
 * See https://docs.moodle.org/dev/version.php for more info.
 *
 * @package   mod
 * @subpackage citizencam
 * @copyright 2017 CitizenCam dev@citizencam.eu
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'mod_citizencam';
$plugin->version = 2017101104; // The current module version (Date: YYYYMMDDXX)
$plugin->release = '1.0';
$plugin->requires = 2016120502; // Requires this Moodle version
$plugin->maturity = MATURITY_STABLE;
$plugin->cron = 0;