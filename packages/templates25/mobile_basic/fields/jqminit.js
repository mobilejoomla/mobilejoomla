jqm=jQuery.noConflict();
jqm(document).on("mobileinit", function(){
	jqm.mobile.autoInitializePage = false;
	jqm.mobile.pushStateEnabled = false;
	jqm.mobile.hashListeningEnabled = false;
	jqm.mobile.ajaxEnabled = false;
	jqm.mobile.ignoreContentEnabled = true;
//	jqm.mobile.selectmenu.prototype.options.nativeMenu = false;
});
