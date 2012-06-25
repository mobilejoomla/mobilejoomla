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

function onConfirmChange(){
    var $confirmtext=$('confirmtext'),
        $nextbutton=$('nextbutton');
    if($('confirmbox').checked){
        $confirmtext.setStyle('display','none');
        $nextbutton.removeClass('disabled');
        $nextbutton.addClass('enabled');
    }else{
        $confirmtext.setStyle('display','');
        $nextbutton.removeClass('enabled');
        $nextbutton.addClass('disabled');
    }
    return true;
}

