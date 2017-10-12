<?php
/**
 * Defines backup_citizencam_activity_task class
 *
 * @package   mod
 * @subpackage citizencam
 * @copyright 2017 CitizenCam dev@citizencam.eu
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/mod/citizencam/backup/moodle2/backup_citizencam_stepslib.php');

/**
 * Provides the steps to perform one complete backup of the citizencam instance
 */
class backup_citizencam_activity_task extends backup_activity_task {

    /**
     * No specific settings for this activity
     */
    protected function define_my_settings() {
    }

    /**
     * Defines a backup step to store the instance data in the citizencam.xml file
     */
    protected function define_my_steps() {
        $this->add_step(new backup_citizencam_activity_structure_step('citizencam_structure', 'citizencam.xml'));
    }

    /**
     * Encodes URLs to the index.php and view.php scripts
     *
     * @param string $content some HTML text that eventually contains URLs to the activity instance scripts
     * @return string the content with the URLs encoded
     */
    static public function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot, '/');

        // Link to the list of citizencams.
        $search = '/('.$base.'\/mod\/citizencam\/index.php\?id\=)([0-9]+)/';
        $content = preg_replace($search, '$@citizencamINDEX*$2@$', $content);

        // Link to citizencam view by moduleid.
        $search = '/('.$base.'\/mod\/citizencam\/view.php\?id\=)([0-9]+)/';
        $content = preg_replace($search, '$@citizencamVIEWBYID*$2@$', $content);

        return $content;
    }
}
