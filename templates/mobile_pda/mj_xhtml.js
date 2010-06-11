function init()
{
    var myStyleTweaks = new StyleTweaker();
    myStyleTweaks.add("Series60/5.0", "resources/styles/tweaks/S605th.css");
    myStyleTweaks.add("Firefox/3.0a1 Tablet browser 0.3.7", "resources/styles/tweaks/maemo.css");
    myStyleTweaks.add("N900", "resources/styles/tweaks/maemo.css");
    myStyleTweaks.tweak();
}
addEvent("onload", init);