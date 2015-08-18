/* ###SHORTHEADER### */
function ajaxfileupload(el) {
    if (el.value === '') {
        return;
    }
    if (el.value.split('.').pop().toLowerCase() != 'zip') {
        alert('The select file type is invalid. File must be zip.');
        return;
    }

    document.getElementById('ajaxUploader-iframe').onload = function () {
        jqm.mobile.hidePageLoadingMsg();
        var form = el.parentNode;
        form.parentNode.insertBefore(el, form);
        form.parentNode.removeChild(form);
        var response = this.contentWindow.document.body.innerHTML;
        if (response == '*') {
            location.reload(true);
        } else {
            var newInput = document.createElement('input');
            newInput.type = 'file';
            newInput.name = el.name;
            newInput.onchange = new Function('ajaxfileupload(this);');
            el.parentNode.replaceChild(newInput, el);
            alert(response);
        }
    };

    jqm.mobile.showPageLoadingMsg();
    var wrapper = document.createElement('div'),
        template = document.getElementsByName('id')[0].value;
    wrapper.innerHTML = '<form action="../templates/' + template + '/fields/upload.php" method="post" enctype="multipart/form-data" target="ajaxUploader-iframe" /></form>';
    var form = wrapper.firstChild;
    el.parentNode.insertBefore(form, el);
    form.appendChild(el);
    form.submit();
}
