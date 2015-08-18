/* ###SHORTHEADER### */

jqm(document).ready(function () {
    if ("placeholder" in document.createElement("input"))
        jqm("body").addClass("placeholder");
});

jqm(window).one('pagecontainercreate', function () {
    try {
        var anchor = location.hash.substring(1);
        if (!anchor || jqm("#" + anchor + ":jqmData(role='page')").length) return;
        location.hash = '';
        if (jqm("a[name='" + anchor + "']").length)
            jqm("div:jqmData(role='page')").one('pagecontainershow', function () {
                jqm.mobile.silentScroll(jqm("a[name='" + anchor + "']").get(0).offsetTop);
            });
    } catch (e) {
    }
});

if (!jqm.mobile.ajaxEnabled) {
    var prev_isEmbeddedPage = jqm.mobile.path.isEmbeddedPage;
    jqm.mobile.path.isEmbeddedPage = function (url) {
        if (prev_isEmbeddedPage.apply(this, arguments)) {
            var u = jqm.mobile.path.parseUrl(url),
                hash = ( u.protocol !== "" ) ? u.hash : u.href;
            return !jqm("a[name='" + hash.substring(1) + "']").length;
        }
        return false;
    };
}

jqm(document).on('pagebeforecreate', ":jqmData(role='page')", function () {
    var $page = jqm(this);

    if ("placeholder" in document.createElement("input")) {
        jqm.mobile.enhanceable($page.find("input").filter(":not([type]),[type='text'],[type='password'],[type='email'],[type='number'],[type='search'],[type='tel'],[type='url']")).each(function () {
            if (this.id && jqm("label[for='" + this.id + "']").length)
                jqm(this).attr("placeholder", jqm.trim(jqm("label[for='" + this.id + "']:last").hide().text())).addClass("placeholder").parent().removeClass("ui-field-contain");
        });
        $page.find(":jqmData(role='fieldcontain')").each(function () {
            var $this = jqm(this), $input = $this.find('input,select');
            if ($input.length && !$input.filter(":not([placeholder])").length)
                $this.addClass("ui-hide-label"); // @todo: deprecated
            //$this.children("label").addClass("ui-hidden-accessible");
        });
    }

    $page.find("input").filter("[type='checkbox']:not([data-role]),[type='radio']:not([data-role])").each(function () {
        if (!this.id || !$page.find("label[for='" + this.id + "']").length)
            jqm(this).attr('data-role', 'none');
    });

    $page.find('a').filter(function () {
        return /\.(jpe?g|png|gif|pdf)$/i.test(jqm(this).attr('href'));
    }).attr('data-ajax', 'false');

    if (!('standalone' in navigator && navigator.standalone))
        $page.find('form:not([data-ajax])').attr('data-ajax', 'false');

    var $panel = $page.find("[data-role='panel']>.posmj_panel");
    if ($panel.length && !$panel.html().length)
        $page.find('div.header a.panel').hide();
});

jqm(window).on("pagecontainershow orientationchange resize panelopen panelclose", function () {
    var $page = jqm(":mobile-pagecontainer").pagecontainer('getActivePage');
    if ($page == undefined) return;

    var $innerpage = $page.children('.ui-panel-wrapper');
    if (!$innerpage.length)
        $innerpage = $page;

    var $uicontent = $innerpage.children('.ui-content').first();
    if (!$uicontent.length) return;

    var $panel = $page.children('.ui-panel').children('.ui-panel-inner').first(),
        page_height = Math.max(window.innerHeight, jqm(window).height(),
            $panel.length && $panel.width() ? $panel.position().top + $panel.outerHeight() : 0),
        new_height = page_height - ($uicontent.offset().top + $innerpage.children('.ui-footer').first().outerHeight());

    $uicontent.css('min-height', new_height);
});
