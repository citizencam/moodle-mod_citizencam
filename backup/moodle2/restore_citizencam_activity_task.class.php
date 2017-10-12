<?php
/**
 * Defines restore_citizencam_activity_task class
 *
 * @package   mod
 * @subpackage citizencam
 * @copyright 2017 CitizenCam dev@citizencam.eu
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/citizencam/backup/moodle2/restore_citizencam_stepslib.php');

/**
 * Restore task for the citizencam activity module
 *
 * Provides all the settings and steps to perform complete restore of the activity.
 */
class restore_citizencam_activity_task extends restore_activity_task {

    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity.
    }

    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        // We have just one structure step here.
        $this->add_step(new restore_citizencam_activity_structure_step('citizencam_structure', 'citizencam.xml'));
    }

    /**
     * Define the contents in the activity that must be
     * processed by the link decoder
     */
    static public function define_decode_contents() {
        $contents = array();

        $contents[] = new restore_decode_content('citizencam', array('intro', 'url'), 'citizencam');

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder
     */
    static public function define_decode_rules() {
        $rules = array();

        $rules[] = new restore_decode_rule('citizencamVIEWBYID', '/mod/citizencam/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('citizencamINDEX', '/mod/citizencam/index.php?id=$1', 'course');

        return $rules;

    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * citizencam logs. It must return one array
     * of {@link restore_log_rule} objects
     */
    static public function define_restore_log_rules() {
        $rules = array();

        $rules[] = new restore_log_rule('citizencam', 'add', 'view.php?id={course_module}', '{citizencam}');
        $rules[] = new restore_log_rule('citizencam', 'update', 'view.php?id={course_module}', '{citizencam}');
        $rules[] = new restore_log_rule('citizencam', 'view', 'view.php?id={course_module}', '{citizencam}');

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * course logs. It must return one array
     * of {@link restore_log_rule} objects
     *
     * Note this rules are applied when restoring course logs
     * by the restore final task, but are defined here at
     * activity level. All them are rules not linked to any module instance (cmid = 0)
     */
    static public function define_restore_log_rules_for_course() {
        $rules = array();

        $rules[] = new restore_log_rule('citizencam', 'view all', 'index.php?id={course}', null);

        return $rules;
    }
}
