<?php
/**
 * Defines backup_citizencam_activity_structure_step class
 *
 * @package   mod
 * @subpackage citizencam
 * @copyright 2017 CitizenCam dev@citizencam.eu
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Define the complete citizencam structure for backup, with file and id annotations
 */
class backup_citizencam_activity_structure_step extends backup_activity_structure_step {

    /**
     * Defines the backup structure of the module
     *
     * @return backup_nested_element
     */
    protected function define_structure() {

        // Get know if we are including userinfo.
        $userinfo = $this->get_setting_value('userinfo');

        // Define the root element describing the citizencam instance.
        $citizencam = new backup_nested_element('citizencam', array('id'), array(
            'name', 'url', 'intro', 'introformat', 'grade'));

        // If we had more elements, we would build the tree here.

        // Define data sources.
        $citizencam->set_source_table('citizencam', array('id' => backup::VAR_ACTIVITYID));

        // If we were referring to other tables, we would annotate the relation
        // with the element's annotate_ids() method.

        // Define file annotations (we do not use itemid in this example).
        $citizencam->annotate_files('mod_citizencam', 'intro', null);

        // Return the root element (citizencam), wrapped into standard activity structure.
        return $this->prepare_activity_structure($citizencam);
    }
}
