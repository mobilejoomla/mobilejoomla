/* ###SHORTHEADER### */
window.addEvent('domready',function(){
	function hidetabs(){
		$$('#submenu li a').each(function(a){
			$$(a.getProperty('href')).setStyle('display','none');
		});
	}
	function initTabs(){
		$$('#submenu li a').addEvent('click',function(e){
			$$('#submenu li a.active').removeClass('active');
			$$('#submenu li.active').removeClass('active');
			var a=$(this);
			a.blur();
			a.addClass('active').getParent().addClass('active');
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
		a.addClass('active').getParent().addClass('active');;
		$$(a.getProperty('href')).setStyle('display','');
	}
	
	function initSlider(){
		var slider=new Slider("mjconfig_jpegquality_slider","mjconfig_jpegquality_knob",{steps:100});
		var quality=$("mjconfig_jpegquality");
		slider.set(+quality.value);
		slider.addEvent('onChange',function(val){quality.value=val});
		quality.addEvent('change',function(){slider.set(+quality.value)});
	}

	function loadNotification(){
		if(typeof Request == "function"){
			new Request.HTML({url: 'http://ads.mobilejoomla.com/msg.html', method: 'get', update: 'mjmsgarea'}).send();
		} else if(typeof Ajax == "function"){
			new Ajax('http://ads.mobilejoomla.com/msg.html', {method: 'get', evalScripts: true, update: $('mjmsgarea')}).request();
		}
	}

	try{
		initTabs();
	}catch(e){}
	try{
		initSlider();
	}catch(e){}
	try{
		loadNotification();
	}catch(e){}
});
