/**
 * JS file for the "Edit" page.
 *
 * @package   mod
 * @subpackage citizencam
 * @copyright 2017 CitizenCam dev@citizencam.eu
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$(function() {
	var short_hash_id = $('#id_url', window.parent.document).val();
	highlight(short_hash_id);

    // Disabling the ID field (not doable in PHP because Moodle no longer recognizes the value...)
    $('#id_url').prop('readonly', true);
});

function choose_record(record, url) {
    moment.locale('fr');
    var label = $(record).attr('label');
    var short_hash_id = $(record).attr('short_hash_id');
    $("#moments", window.parent.document).html('');

    $.ajax({
        method: "GET",
        url: url,
    }).done(function(record_data) {
        console.log(record_data.moments);
        $("#record-title", window.parent.document).css('background', "url('" + record_data.preview_url + "')");
        $("#record-label", window.parent.document).html(label);
        $("#record-date", window.parent.document).html(moment(record_data.webrecorder_time_created).format('LL'));
        $("#record-view-number", window.parent.document).html(record_data.cameras.length);

        for (i=0; i < record_data.moments.length; i++) {
            var _moment = record_data.moments[i];
            if (_moment.label == '') {
                _moment.label = 'Sans titre';
            }

            var thumbnail = '';
            for (var j=0; j<_moment.medias.length; j++) {
                var _media = _moment.medias[j];
                if (_media.type == 'image') {
                    thumbnail = _media.url;
                    break;
                }
            }
 
            var template_html = $('template#moment-template');

            if (template_html.length > 0) {
                var length = new Date(_moment.webrecorder_time_ended).getTime() - new Date(_moment.webrecorder_time_started).getTime();
                length /= 1000;

                template_html = template_html.clone().get(0);
                if (template_html.content) template_html = template_html.content; // Modern browsers support of template
                else template_html = $(template_html).children(':first').get(0); // IE Fix

                $(template_html).find('.title').text(_moment.label);
                $(template_html).find('.thumbnail').attr('src', thumbnail);
                $(template_html).find('.length').text(formatTime(length));
                $("#moments", window.parent.document).append(template_html);
            } else {
                 $("#moments", window.parent.document).append("<li>" + _moment.label + "</li>");
            }
        }

        // Show the dialog
        var dialog = $('#citizencam_record_dialog', window.parent.document).get(0);
        if (! dialog.showModal) {
          dialogPolyfill.registerDialog(dialog);
        }
        dialog.showModal();
        
        $(dialog).find('#validate_button').off().click(function() {
            validate_record(short_hash_id, label);
        });
        $(dialog).find('.citizencam_close').off().click(function() {
            dialog.close();
        });
    });
}

function validate_record(short_hash_id, label) {
    $('#id_name', window.parent.document).val(label);
    $('#id_url', window.parent.document).val(short_hash_id);
    $('#citizencam_record_dialog', window.parent.document).get(0).close();
    $(".record").css('border', 'none');
    highlight(short_hash_id);
}

function highlight(short_hash_id) {
	$(".record[short_hash_id='"+short_hash_id+"']").css('border', '5px solid #a7c946');
}

function formatTime(total_seconds) {
    if (total_seconds > 0) {
        var hours = Math.floor(total_seconds / 3600);
        hours = (hours >= 10) ? hours : "0" + hours;
        var minutes = Math.floor(total_seconds % 3600 / 60);
        minutes = (minutes >= 10) ? minutes : "0" + minutes;
        var seconds = Math.floor(total_seconds % 60);
        seconds = (seconds >= 10) ? seconds : "0" + seconds;
        return (total_seconds >= 3600 ? hours + ":": "") + minutes + ":" + seconds;
    } else {
        return "00:00";
    }
}