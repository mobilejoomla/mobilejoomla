var jQT = new $.jQTouch({
    addGlossToIcon: true,
    statusBar: 'black'
});

$(function()
{
    if(isFrontPage)
    {
        $("a.back").hide();
        $("a.home").hide();
    }

    $("ul#mainlevel li").each(function()
    {
        $(this).addClass("forward");
    });
});