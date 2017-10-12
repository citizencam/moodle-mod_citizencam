<?php
/**
 * Internal library of functions for module citizencam
 *
 * All the citizencam specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package   mod
 * @subpackage citizencam
 * @copyright 2017 CitizenCam dev@citizencam.eu
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function curl($url) {
	try {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 3);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        $result = curl_exec($curl);        
        curl_close($curl);
    } catch (\Exception $e) {
        throw new Exception("Cannot send the request to CitizenCam Studio");
    }

    
    return $result;
}

/* Helper function that imports all the necessary CSS and JS files */
function import($iframe = false) {
    global $CFG, $PAGE;

    $js = [
        'jquery.js',
        'edit.js',
        'dialog-polyfill.js',
        'material.min.js',
        'moment-with-locales.js'
    ];

    $css = [
        'dialog-polyfill.css',
        'material.custom.css',
        'material-icons.css',
        'font-awesome.min.css',
        'small_card.css',
        'style.css'
   ];

    $srcJs = $iframe ? $CFG->wwwroot : '';
    $srcCss = $iframe ? $CFG->wwwroot : '';
    $srcJs .= '/mod/citizencam/js/';
    $srcCss .= '/mod/citizencam/css/';

    foreach ($js as $file) {
        $file = $srcJs . $file . '?' . time();
        if ($iframe) {
            echo '<script type="text/javascript" src="' . $file . '"></script>';
        } else {
            $PAGE->requires->js($file, true);
        }
    }

    foreach ($css as $file) {
        $file = $srcCss . $file. '?' . time();
        if ($iframe) {
            echo '<link rel="stylesheet" href="' . $file . '">';
        } else {
            $PAGE->requires->css($file);
        }
    }
}