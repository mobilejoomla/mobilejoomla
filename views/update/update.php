<script type="text/javascript" src="components/com_mobilejoomla/assets/js/jquery.min.js"></script>
<script type="text/javascript">
//<![CDATA[
    jQuery.noConflict();
    function mjOnError()
    {
        jQuery("#mjlink").css('display','block');
    }
    function mjTestStatus(textStatus)
    {
        switch(textStatus){
            case "success": break;
            case "notmodified": break;
            case "error": jQuery("#mjstatus").html("<?php echo JText::_('COM_MJ__UPDATE_AJAX_ERROR'); ?>"); mjOnError(); break;
            case "timeout": jQuery("#mjstatus").html("<?php echo JText::_('COM_MJ__UPDATE_AJAX_TIMEOUT'); ?>"); mjOnError(); break;
            case "abort": jQuery("#mjstatus").html("<?php echo JText::_('COM_MJ__UPDATE_AJAX_ABORT'); ?>"); mjOnError(); break;
            case "parsererror": jQuery("#mjstatus").html("<?php echo JText::_('COM_MJ__UPDATE_AJAX_PARSEERROR'); ?>"); mjOnError(); break;
        }
    }
    function mjAjaxDownload()
    {
        jQuery("#mjdownload").addClass("highlight").addClass("ajaxload");
        jQuery.ajax({
            type: "GET",
            url: "index.php?option=com_mobilejoomla&controller=update&task=download&tmpl=none",
            success: function(data){
                if(data!="ok") {
                    jQuery("#mjdownload").addClass("error");
                    jQuery("#mjstatus").html(data);
                    mjOnError();
                } else {
                    jQuery("#mjdownload").addClass("pass");
                    mjAjaxUnpack();
                }
            },
            error: function(){
                jQuery("#mjdownload").addClass("error");
                mjOnError();
            },
            complete: function(jqXHR, textStatus){
                jQuery("#mjdownload").removeClass("ajaxload");
                mjTestStatus(textStatus);
            }
        });
    }
    function mjAjaxUnpack()
    {
        jQuery("#mjunpack").addClass("highlight").addClass("ajaxload");
        jQuery.ajax({
            type: "GET",
            url: "index.php?option=com_mobilejoomla&controller=update&task=unpack&tmpl=none",
            success: function(data){
                if(data!="ok") {
                    jQuery("#mjunpack").addClass("error");
                    jQuery("#mjstatus").html(data);
                    mjOnError();
                } else {
                    jQuery("#mjunpack").addClass("pass");
                    mjAjaxInstall();
                }
            },
            error: function(){
                jQuery("#mjunpack").addClass("error");
                mjOnError();
            },
            complete: function(jqXHR, textStatus){
                jQuery("#mjunpack").removeClass("ajaxload");
                mjTestStatus(textStatus);
            }
        });
    }
    function mjAjaxInstall()
    {
        jQuery("#mjinstall").addClass("highlight").addClass("ajaxload");
        jQuery.ajax({
            type: "GET",
            url: "index.php?option=com_mobilejoomla&controller=update&task=install&tmpl=none",
            success: function(data){
                if(data!="ok") {
                    jQuery("#mjinstall").addClass("error");
                    jQuery("#mjstatus").html(data);
                    mjOnError();
                } else {
                    jQuery("#mjinstall").addClass("pass");
                    window.parent.location.reload();
                }
            },
            error: function(){
                jQuery("#mjinstall").addClass("error");
                mjOnError();
            },
            complete: function(jqXHR, textStatus){
                jQuery("#mjinstall").removeClass("ajaxload");
                mjTestStatus(textStatus);
            }
        });
    }
    jQuery(document).ready(mjAjaxDownload);
    //]]>
</script>
<style type="text/css">
    .mjheader {
        font-size: 20px;
        font-weight: bold;
        line-height: 48px;
        margin-left: 5px;
        padding-left: 5px;
    }
    #mjstages {
        list-style-type: none;
        margin: 0;
        padding: 0;
    }
    #mjstages li {
        height: 22px;
        padding: 10px 0 0 32px;
        margin: 0;
    }
    #mjlink {
        display: none;
        background: url("components/com_mobilejoomla/images/warning.png") no-repeat scroll 10px 50% #FDFBB9;
        font-weight: bold;
        -moz-border-radius: 8px;
        -webkit-border-radius: 8px;
        border-radius: 8px;
        border: 3px solid #f00;
        line-height: 135%;
        margin-top: 15px;
        padding: 10px 10px 10px 48px;
        text-align: left;
    }
    #mjstatus {
        font-size: 80%;
        padding: 16px 8px 0;
    }
    .highlight {
        font-weight: bold;
    }
    .ajaxload {
        background: url("components/com_mobilejoomla/images/ajax-loader.gif") no-repeat scroll 0 50% #FFF;
        line-height: 100%;
        text-align: left;
    }
    .pass {
        background: url("components/com_mobilejoomla/images/tick.png") no-repeat scroll 8px 50% #FFF;
        line-height: 100%;
        text-align: left;
    }
    .error {
        background: url("components/com_mobilejoomla/images/error.png") no-repeat scroll 8px 50% #FDFBB9;
        color: #f00;
        line-height: 100%;
        text-align: left;
    }
</style>
<div class="mjheader"><?php echo JText::_('COM_MJ__UPDATE_HEADER'); ?></div>
<ul id="mjstages">
    <li id="mjdownload"><?php echo JText::_('COM_MJ__UPDATE_DOWNLOAD'); ?></li>
    <li id="mjunpack"><?php echo JText::_('COM_MJ__UPDATE_UNPACK'); ?></li>
    <li id="mjinstall"><?php echo JText::_('COM_MJ__UPDATE_INSTALL'); ?></li>
</ul>
<div id="mjlink"><?php echo JText::_('COM_MJ__UPDATE_DOWNLOAD_LINK'); ?></div>
<div id="mjstatus"></div>
