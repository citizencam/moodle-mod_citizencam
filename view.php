<?php
/**
 * Prints a particular instance of citizencam
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package   mod
 * @subpackage citizencam
 * @copyright 2017 CitizenCam dev@citizencam.eu
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Replace citizencam with the name of your module and remove this line.

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$config = get_config('citizencam');

$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // ... citizencam instance ID - it should be named as the first character of the module.

if ($id) {
    $cm         = get_coursemodule_from_id('citizencam', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $citizencam  = $DB->get_record('citizencam', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($n) {
    $citizencam  = $DB->get_record('citizencam', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $citizencam->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('citizencam', $citizencam->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);

$srcCss = '/mod/citizencam/css/';
$PAGE->requires->css($srcCss . 'view.css');
$PAGE->requires->css($srcCss . 'material-icons.css');

$event = \mod_citizencam\event\course_module_viewed::create(array(
    'objectid' => $PAGE->cm->instance,
    'context' => $PAGE->context,
));
$event->add_record_snapshot('course', $PAGE->course);
$event->add_record_snapshot($PAGE->cm->modname, $citizencam);
$event->trigger();

// Print the page header.

$PAGE->set_url('/mod/citizencam/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($citizencam->name));
$PAGE->set_heading(format_string($course->fullname));

/*
 * Other things you may want to set - remove if not needed.
 * $PAGE->set_cacheable(false);
 * $PAGE->set_focuscontrol('some-html-id');
 * $PAGE->add_body_class('citizencam-'.$somevar);
 */

// Output starts here.
echo $OUTPUT->header();

// Conditions to show the intro can change to look for own settings or whatever.
if ($citizencam->intro) {
    echo $OUTPUT->box(format_module_intro('citizencam', $citizencam, $cm->id), 'generalbox mod_introbox', 'citizencamintro');
}

// Replace the following lines with you own code.
echo $OUTPUT->heading($citizencam->name);

echo
'<div id="cc-content">
    <div id="cc-video-wrapper">
        <iframe id="cc-video" allowfullscreen frameBorder="0" src="' . $config->citizencam_cctv_url . '/v/' . $citizencam->url . '?embed' . '"/>
    </div>
</div>';

// Finish the page.
echo $OUTPUT->footer();
