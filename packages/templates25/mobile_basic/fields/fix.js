jqm(function(){
	jqm("#mobile_jqm_params").parent().pagecontainer();
	jqm("#mobile_jqm_params").page().removeClass("ui-page");

	jqm("#mobile_jqm_params .mj-icons img").each(function(){
		jqm(this).css({width:this.getAttribute("width"),height:this.getAttribute("height")});
	});
	jqm("html").removeClass("ui-mobile-rendering");

	// animation speed;
	var animationSpeed = 200;
	function animateCollapsibleSet(elm){
		elm.one("expand", function(){
			jqm(this).parent().find(".ui-collapsible-content").not(".ui-collapsible-content-collapsed").trigger("collapse");
			jqm(this).find(".ui-collapsible-content").slideDown(animationSpeed, function(){
				animateCollapsibleSet(jqm(this).parent().collapsible("expand"));
			});
			return false;
		}).one("collapse", function(){
			jqm(this).find(".ui-collapsible-content").slideUp(animationSpeed, function(){
				jqm(this).parent().collapsible("collapse");
			});
			return false;
		});
	}
	animateCollapsibleSet(jqm("[data-role=\'collapsible-set\'] > [data-role=\'collapsible\']"));

	jqm("#mobile_jqm_params").on("click", ".ui-flipswitch a", function(e){
		e.preventDefault();
	});
});
