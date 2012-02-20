/* ###SHORTHEADER### */
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

	function checkupdate(){
		if(typeof Ajax == "function"){
			new Ajax( 'http://www.mobilejoomla.com/getver.php?v=' + escape('0.9'), {
				method: 'get',
				update: $('mjlatestver'),
				onComplete: function(response){
					if(response != '0.9'){
						$('mjlatestverurl').setStyle('display', 'block');
					}
				}
			}).request();
		} else if(typeof Request == "function"){
			new Request.HTML( {
				url: 'http://www.mobilejoomla.com/getver.php?v=' + escape('0.9'),
				method: 'get',
				update: 'mjlatestver',
				onSuccess : function(response){
					if(response != '0.9'){
						$('mjlatestverurl').setStyle('display', 'block');
					}
				}
			}).send();
		}
	}

	try{
		inittabs();
	}catch(e){}
	try{
		initslider();
	}catch(e){}
	try{
		checkupdate();
	}catch(e){}
});
