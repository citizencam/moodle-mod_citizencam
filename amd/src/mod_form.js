define(['jquery'
    , 'core/str'
    , 'mod_citizencam/dialog-polyfill'
    , 'mod_citizencam/material'
    , 'mod_citizencam/moment-with-locales'
], function(
    $
    , str
    , dialogPolyfill
    , material
    , moment
) {

    return {
        init: function() {
            var that = this;
            $(function() {
                var short_hash_id = $('#id_url', window.parent.document).val();
                that.citizencam_highlight(short_hash_id);

                // Disabling the ID field (not doable in PHP because Moodle no longer recognizes the value...)
                $('#id_url').prop('readonly', true);

                $('.ctz-record-card').click(function() {
                    that.citizencam_choose_record(this);
                });
            });
        },

        citizencam_choose_record: function(record) {
            var that = this;
            // moment.locale('fr');
            var label = $(record).attr('label');
            var short_hash_id = $(record).attr('short_hash_id');
            var url = $(record).attr('url');
            $("#ctz-moments", window.parent.document).html('');

            $.ajax({
                method: "GET",
                url: url,
            }).done(function(record_data) {
                var dialog = $('#ctz-record-dialog', window.parent.document);
                dialog.find("#record-title").css('background', "url('" + record_data.preview_url + "')");
                dialog.find("#record-label").html(label);
                dialog.find("#record-date").html(moment(record_data.webrecorder_time_created).format('LL'));
                dialog.find("#record-view-number").html(record_data.cameras.length);

                var noTitle = str.get_string('citizencam_no_title', 'citizencam');
                $.when(noTitle).done(function(noTitle) {
                    for (i=0; i < record_data.moments.length; i++) {
                        var _moment = record_data.moments[i];
                        
                        if (_moment.label == '') {
                            _moment.label = noTitle;
                        }

                        var thumbnail = '';
                        for (var j=0; j<_moment.medias.length; j++) {
                            var _media = _moment.medias[j];
                            if (_media.type == 'image') {
                                thumbnail = _media.url;
                                break;
                            }
                        }
             
                        var template_html = $('template#ctz-moment-template');

                        if (template_html.length > 0) {
                            var length = new Date(_moment.webrecorder_time_ended).getTime() - new Date(_moment.webrecorder_time_started).getTime();
                            length /= 1000;

                            template_html = template_html.clone().get(0);
                            if (template_html.content) template_html = template_html.content; // Modern browsers support of template
                            else template_html = $(template_html).children(':first').get(0); // IE Fix

                            console.log(_moment.label);
                            $(template_html).find('.title').text(_moment.label);
                            $(template_html).find('.thumbnail').attr('src', thumbnail);
                            $(template_html).find('.length').text(that.citizencam_format_time(length));
                            $("#ctz-moments", window.parent.document).append(template_html);
                        } else {
                            $("#ctz-moments", window.parent.document).append("<li>" + _moment.label + "</li>");
                        }
                    }

                    // Show the dialog
                    var dialog = $('#ctz-record-dialog', window.parent.document).get(0);
                    if (! dialog.showModal) {
                      dialogPolyfill.registerDialog(dialog);
                    }
                    dialog.showModal();
                    
                    $(dialog).find('#validate_button').off().click(function() {
                        that.citizencam_validate_record(short_hash_id, label);
                    });
                    $(dialog).find('.citizencam_close').off().click(function() {
                        dialog.close();
                    });
                });
            });
        },

        citizencam_validate_record: function(short_hash_id, label) {
            var that = this;
            $('#id_name', window.parent.document).val(label);
            $('#id_url', window.parent.document).val(short_hash_id);
            $('#ctz-record-dialog', window.parent.document).get(0).close();
            $(".ctz-record-card").css('border', 'none');
            that.citizencam_highlight(short_hash_id);
        },

        citizencam_highlight: function(short_hash_id) {
            $(".ctz-record-card[short_hash_id='"+short_hash_id+"']").css('border', '5px solid #a7c946');
        },

        citizencam_format_time: function(total_seconds) {
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
    };
});