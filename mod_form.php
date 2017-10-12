<?php
/**
 * The main citizencam configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package   mod
 * @subpackage citizencam
 * @copyright 2017 CitizenCam dev@citizencam.eu
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once("$CFG->dirroot/mod/citizencam/locallib.php");
require_once("$CFG->libdir/resourcelib.php");

/**
 * Module instance settings form
 *
 * @package    mod_citizencam
 * @copyright  2016 Your Name <your@email.address>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_citizencam_mod_form extends moodleform_mod {
    public function definition() {
        global $CFG, $PAGE;

        $mform = $this->_form;

        import();
        $srcImg = '/mod/citizencam/pix/';

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('html', self::getRecords());

        echo '<template id="moment-template">
                <div class="moment">
                    <img class="thumbnail" onerror="this.src = \'' . $srcImg . 'icon.png' . '\';">

                    <h2 class="metadatas">
                        <span class="title"></span>
                    </h2>

                    <div class="length"></div>
                </div>
            </template>';

        echo  '<dialog id="citizencam_record_dialog" class="mdl-dialog">
            <div class="mdl-dialog__content">
                <div class="mdl-card__title" id="record-title">
                    <h2 class="mdl-card__title-text" id="record-label"></h2>
                </div>

                <div class="mdl-card__supporting-text dialog-body">
                    <div>
                        Enregistré le : <span id="record-date"></span>
                    </div>
                    <div id="record-views">
                        Nombre de vues : <span id="record-view-number"></span>
                    </div>
                    <div>
                        <p>Moments : </p>
                        <div id="moments"></div>
                    </div>
                </div>
                <!-- Border and Action Elements -->
                <div class="mdl-card__actions mdl-card--border"></div>
            </div>

            <div class="mdl-dialog__actions">
                <button type="button" id="validate_button" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored">Valider</button>
                <button type="button" class="mdl-button mdl-js-button mdl-js-ripple-effect citizencam_close">Annuler</button>
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

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT , 10);
        curl_setopt($curl, CURLOPT_TIMEOUT , 10);

        $records = curl_exec($curl);

        // Fermeture du gestionnaire
        curl_close($curl);

        $records_grid = '
            <div class="form-group row fitem">
                <div class="col-md-3">
                    <span class="pull-xs-right text-nowrap">
                        <abbr class="initialism text-danger">✲</abbr>
                    </span>
                    <label class="col-form-label d-inline " for="citizencam_iframe_content">
                        ' . get_string('citizencam_media_url', 'citizencam') . '
                    </label>
                </div>
                <div class="col-md-9 form-inline felement">
                    <div id="citizencam_iframe_content">
                       <div id="iframe_content">
                        <div id="records">
                            <div class="mdl-grid">';

        if($records === false) {
            echo 'Erreur Curl : ' . curl_error($curl);
        } else {
            foreach (json_decode($records) as $record) {
                if (!$record->password_protected) {
                    $length = $record->length > 3600 ? gmdate("H:i:s", $record->length) : gmdate("i:s", $record->length);
                    $records_grid .= '<div short_hash_id="' .  $record->short_hash_id . '" label="' . $record->label . '" class="mdl-cell mdl-cell--3-col mdl-cell--4-col-tablet mdl-cell--6-col-phone record" onclick="choose_record(this, \'' . $url . '/' . $record->short_hash_id . '\');">
                            <div class="small_record mdl-shadow--2dp">
                                <img src="' . $record->preview_url . '?q=100&w=300" alt="' . $record->label . '" />
                                <div class="record_length">' . $length . '</div>
                                <h3 class="record_label" title="' . $record->label  . '">' . $record->label . '</h3>
                                <i class="material-icons play-icon">play_arrow</i>
                            </div>
                        </div>';
                }
            }
        }

        $records_grid .= '</div></div></div></div></div></div>';

        return $records_grid;
    }
}
