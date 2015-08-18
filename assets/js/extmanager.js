function change(id, device, el) {
    el.innerHTML = '<img src="components/com_mobilejoomla/assets/images/ajax-16.gif" width="16" height="16" />';
    var URL = 'index.php?option=com_mobilejoomla&controller=extensions&task=' + mj_extmanager_action
        + '&id=' + id
        + '&device=' + device;

    jQuery.post(URL, function (response) {
        el.innerHTML = response;
    });
}
