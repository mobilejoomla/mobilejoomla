window.addEvent('domready',function(){
	function hidetabs(){
		$$('#submenu li a').each(function(a){
			$$(a.getProperty('href')).setStyle('display','none');
		});
	}
	function inittabs(){
		$$('#submenu li a').addEvent('click',function(e){
			$$('#submenu li a.active').removeClass('active');
			var a=$(this);
			a.blur();
			a.addClass('active');
			hidetabs();
			$$(a.getProperty('href')).setStyle('display','');
			e.returnValue=false;
			e.preventDefault();
		});
		var maxheight=0;
		$$('#submenu li a').each(function(a){
			var h=$$(a.getProperty('href'))[0].getCoordinates().height;
			if(h>maxheight) maxheight=h;
		});
		$('adminForm').setStyle('height',maxheight);
		hidetabs();
		var a=$$('#submenu li a')[0];
		a.addClass('active');
		$$(a.getProperty('href')).setStyle('display','');
	}
	
	function initslider(){
		var slider=new Slider("mjconfig_jpegquality_slider","mjconfig_jpegquality_knob",{steps:100});
		var quality=$("mjconfig_jpegquality");
		slider.set(+quality.value);
		slider.addEvent('onChange',function(val){quality.value=val});
		quality.addEvent('change',function(){slider.set(+quality.value)});
	}
	
	try{
		inittabs();
	}catch(e){}
	try{
		initslider();
	}catch(e){}
});
