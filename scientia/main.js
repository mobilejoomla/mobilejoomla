/* ###MJ_SHORTHEADER### */

function ajaxGet(url, onComplete, onError)
{
	if(typeof Request == "function"){
		new Request.HTML({
			url: url,
			method: 'get',
			onSuccess: function(tree, elements, response){if(typeof onComplete=="function")onComplete(response);},
			onFailure: function(){if(typeof onError=="function")onError();}
		}).send();
	}else if(typeof Ajax == "function"){
		new Ajax(url, {
			method: 'get',
			onComplete: function(response){if(typeof onComplete=="function")onComplete(response);},
			onFailure: function(){if(typeof onError=="function")onError();}
		}).request();
	}
}

function onDatabaseInstall(){
	var $db=$$('input[name=database]:checked')[0].get('value');
	if($db=='scientia')
		return true;
	window.parent.SqueezeBox.close();
	return false;
}