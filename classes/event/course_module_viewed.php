<?php
/**
 * Defines the view event.
 *
 * @package   mod
 * @subpackage citizencam
 * @copyright 2017 CitizenCam dev@citizencam.eu
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_citizencam\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The mod_citizencam instance viewed event class
 *
 * If the view mode needs to be stored as well, you may need to
 * override methods get_url() and get_legacy_log_data(), too.
 */
class course_module_viewed extends \core\event\course_module_viewed {

    /**
     * Initialize the event
     */
    protected function init() {
        $this->data['objecttable'] = 'citizencam';
        parent::init();
    }
}
