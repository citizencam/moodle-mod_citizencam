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
 * The main citizencam configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package   mod_citizencam
 * @copyright 2017 CitizenCam dev@citizencam.eu
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');
require_once("$CFG->dirroot/mod/citizencam/locallib.php");

/**
 * Module instance settings form
 *
 * @package    mod_citizencam
 * @copyright  2017 CitizenCam dev@citizencam.eu
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_citizencam_mod_form extends moodleform_mod {
    public function definition() {
        global $CFG, $PAGE;

        $mform = $this->_form;

        mod_citizencam_import();
        $PAGE->requires->js_call_amd('mod_citizencam/mod_form', 'init');

        $srcImg = '/mod/citizencam/pix/';

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('html', self::getRecords());

        echo '<template id="ctz-moment-template">
                <div class="ctz-moment">
                    <img class="thumbnail" onerror="this.src = \'' . $srcImg . 'icon.png' . '\';">

                    <h2 class="ctz-metadata">
                        <span class="title"></span>
                    </h2>

                    <div class="length"></div>
                </div>
            </template>';

        echo  '<dialog id="ctz-record-dialog" class="mdl-dialog">
            <div class="mdl-dialog__content">
                <div class="mdl-card__title" id="record-title">
                    <h2 class="mdl-card__title-text" id="record-label"></h2>
                </div>

                <div class="mdl-card__supporting-text dialog-body">
                    <div>'
                        . get_string('citizencam_recorded_on', 'citizencam') . '<span id="record-date"></span>
                    </div>
                    <div id="record-views">'
                        . get_string('citizencam_view_number', 'citizencam') . '<span id="record-view-number"></span>
                    </div>
                    <div>
                        <p>' . get_string('citizencam_moments', 'citizencam') . '</p>
                        <div id="ctz-moments"></div>
                    </div>
                </div>
                <!-- Border and Action Elements -->
                <div class="mdl-card__actions mdl-card--border"></div>
            </div>

            <div class="mdl-dialog__actions">
                <button type="button" id="validate_button" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored">
                    ' . get_string('citizencam_submit_button', 'citizencam') . '
                </button>
                <button type="button" class="mdl-button mdl-js-button mdl-js-ripple-effect citizencam_close">
                    ' . get_string('citizencam_cancel_button', 'citizencam') . '
                </button>
            </div>
          </dialog>';

        $mform->addElement('text', 'url', get_string('citizencam_media_id', 'citizencam'), array());
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('url', PARAM_TEXT);
        } else {
            $mform->setType('url', PARAM_CLEANHTML);
        }
        $mform->addRule('url', null, 'required', null, 'client');
        $mform->addRule('url', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('citizencamname', 'citizencam'), array('size' => 64));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        // Adding the standard "intro" and "introformat" fields.
        if ($CFG->branch >= 29) {
            $this->standard_intro_elements();
        } else {
            $this->add_intro_editor();
        }

        // Add standard grading elements.
        $this->standard_grading_coursemodule_elements();

        // Add standard elements, common to all modules.
        $this->standard_coursemodule_elements();

        // Add standard buttons, common to all modules.
        $this->add_action_buttons();
    }

    private static function getRecords() {
        $config = get_config('citizencam');

        // Moodle user id : $USER->id
        $url = $config->citizencam_studio_url . '/api/records';

        $curl = new curl();
        $curl->setopt(array(
            'CURLOPT_RETURNTRANSFER' => true,
            'CURLOPT_CONNECTTIMEOUT' => 10,
            'CURLOPT_TIMEOUT' => 10
        ));
        $records = $curl->get($url);
  
        $records_grid = '
            <div class="form-group row fitem">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                        <abbr class="initialism text-danger">✲</abbr>
                    </span>
                    <label class="col-form-label d-inline " for="ctz-iframe-content">
                        ' . get_string('citizencam_media_url', 'citizencam') . '
                    </label>
                </div>
                <div class="col-md-9 form-inline felement">
                    <div id="ctz-iframe-content">
                        <div id="records">
                            <div class="mdl-grid">';

        if($records === false) {
            echo 'Erreur Curl : ' . $records;
        } else {
            foreach (json_decode($records) as $record) {
                if (!$record->password_protected) {
                    $length = $record->length > 3600 ? gmdate("H:i:s", $record->length) : gmdate("i:s", $record->length);
                    $records_grid .= '<div short_hash_id="' .  $record->short_hash_id . '" url="' . $url . '/' . $record->short_hash_id . '" label="' . $record->label . '" class="mdl-cell mdl-cell--3-col mdl-cell--4-col-tablet mdl-cell--6-col-phone ctz-record-card">
                            <div class="ctz-small-record mdl-shadow--2dp">
                                <img src="' . $record->preview_url . '?q=100&w=300" alt="' . $record->label . '" />
                                <div class="ctz-record-length">' . $length . '</div>
                                <h3 class="ctz-record-label" title="' . $record->label  . '">' . $record->label . '</h3>
                                <i class="material-icons play-icon">play_arrow</i>
                            </div>
                        </div>';
                }
            }
        }

        $records_grid .= '</div></div></div></div></div>';

        return $records_grid;
    }
}
