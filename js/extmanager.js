function change(id, markup, el) {
	el.innerHTML = '<img src="components/com_mobilejoomla/images/ajax-16.gif" width="16" height="16" />';
	var URL = 'index.php?option=com_mobilejoomla&extmanager='+mj_extmanager_action+'&id='+id+'&markup='+markup;
	if(typeof Request == "function"){
		new Request.HTML( {
			url: URL,
			method: 'get',
			onSuccess : function(tree, elements, response){
				el.innerHTML = response;
			}
		}).send();
	} else if(typeof Ajax == "function"){
		new Ajax( URL, {
			method: 'get',
			onComplete: function(response){
				el.innerHTML = response;
			}
		}).request();
	}
}
