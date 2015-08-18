/* ###SHORTHEADER### */
jQuery(document).ready(function ($) {

    function version_compare(v1, v2) {
        var vm = {'dev': -4, 'alpha': -3, 'beta': -2, 'rc': -1},
            vprep = function (v) {
                return ('' + v).toLowerCase().replace(/([^.\d]+)/g, '.$1.').replace(/\s+/g, '').replace(/\.{2,}/g, '.').split('.');
            },
            vnum = function (v) {
                return !v ? 0 : (isNaN(v) ? vm[v] || -5 : parseInt(v, 10));
            };
        v1 = vprep(v1);
        v2 = vprep(v2);
        var i = 0,
            x = Math.max(v1.length, v2.length);
        for (; i < x; i++) {
            if (v1[i] == v2[i]) continue;
            v1[i] = vnum(v1[i]);
            v2[i] = vnum(v2[i]);
            return (v1[i] < v2[i]) ? 1 : -1;
        }
        return 0;
    }

    $('#mjmsgarea').load('http://ads.mobilejoomla.com/msg.html?v=###VERSION###');

    $.ajax({
        url: 'http://www.mobilejoomla.com/getver.php?v=' + escape('###VERSION###'),
        success: function (response) {
            $('#mjlatestver').text(response);
            if (version_compare($('#mjversion').text(), response) > 0) {
                $('.show-if-update').show();
                if (mj_updater_text !== undefined) {
                    $('#mjupdatearea').append(
                        '<div class="alert alert-info alert-mjupdate">'
                        + mj_updater_text.replace('%s', response)
                        + '</div>'
                    );
                }
            }
        }
    });
});
