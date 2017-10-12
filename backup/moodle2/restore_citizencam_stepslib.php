<?php
/**
 * Defines restore_citizencam_activity_structure_step class
 *
 * @package   mod
 * @subpackage citizencam
 * @copyright 2017 CitizenCam dev@citizencam.eu
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Structure step to restore one citizencam activity
 */
class restore_citizencam_activity_structure_step extends restore_activity_structure_step {

    /**
     * Defines structure of path elements to be processed during the restore
     *
     * @return array of {@link restore_path_element}
     */
    protected function define_structure() {

        $paths = array();
        $paths[] = new restore_path_element('citizencam', '/activity/citizencam');

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process the given restore path element data
     *
     * @param array $data parsed element data
     */
    protected function process_citizencam($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        if (empty($data->timecreated)) {
            $data->timecreated = time();
        }

        if (empty($data->timemodified)) {
            $data->timemodified = time();
        }

        if ($data->grade < 0) {
            // Scale found, get mapping.
            $data->grade = -($this->get_mappingid('scale', abs($data->grade)));
        }

        // Create the citizencam instance.
        $newitemid = $DB->insert_record('citizencam', $data);
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Post-execution actions
     */
    protected function after_execute() {
        // Add citizencam related files, no need to match by itemname (just internally handled context).
        $this->add_related_files('mod_citizencam', 'intro', null);
    }
}
