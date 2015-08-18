function mobilesim_cleanup(device){
	return function(){
		$('sbox-window').removeClass(device);
		var l=$$('#sbox-content iframe')[0].contentWindow.location.href.replace(/device=[^&#]*/,'device=');
		try{
			new Request({url:l}).send();
		}catch(e){
			new Ajax(l).request();
		}
	}
}