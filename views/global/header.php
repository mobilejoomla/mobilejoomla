<?php
/**
 * ###DESC###
 * ###URL###
 *
 * @version    ###VERSION###
 * @license    ###LICENSE###
 * @copyright  ###COPYRIGHT###
 * @date       ###DATE###
 */
defined('_JEXEC') or die('Restricted access');

/** @var MjController $this */
/** @var array $params */
/** @var string $controllerName */
/** @var string $viewName */

JToolbarHelper::title(JText::_('COM_MJ__MOBILEJOOMLA'), version_compare(JVERSION, '3.2', '>=') ? 'mobile' : 'config');

$doc = JFactory::getDocument();

$doc->addStyleSheet('components/com_mobilejoomla/assets/css/mjbanner.css');
$doc->addStyleSheet('components/com_mobilejoomla/assets/css/slider.css');
$doc->addStyleSheet('components/com_mobilejoomla/assets/css/mjfix.css');

$doc->addScriptDeclaration(
    'window.addEvent("domready", function(){
        $$("#mj span.slider").each(function(el){
    		var slider = new Slider(el.id,el.firstChild.id,{steps:100}),
	    	    quality = $(el.getProperty("target"));
		    slider.set(+quality.value);
		    slider.addEvent("onChange",function(val){quality.value=val});
		    quality.addEvent("change",function(){slider.set(+quality.value)});
        });
    });'
);

$doc->addScript('components/com_mobilejoomla/assets/js/jquery.are-you-sure.js');
$doc->addScriptDeclaration(
    'jQuery.propHooks.checked = {
        set: function(elem, value, name) {
            var ret = (elem[ name ] = value);
            jQuery(elem).trigger("change");
            return ret;
        }
    };
    jQuery(document).ready(function(){
        // $ - mooTools
        // Joomla!3.x
        $("adminForm").addEvent("onsubmit", function(){
            this.removeClass("dirty");
        });
        // Joomla!1.5-2.5
        $("adminForm").addEvent("submit", function(){
            this.removeClass("dirty");
        });
        jQuery("#adminForm").areYouSure({fieldSelector:"input:not(input[type=submit]):not(input[type=button]),select"});
    });'
);
