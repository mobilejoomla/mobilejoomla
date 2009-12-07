<?php
/**
 * Kuneri Mobile Joomla! for Joomla!1.5
 * http://www.mobilejoomla.com/
 *
 * @version		0.9.0
 * @license		http://www.gnu.org/licenses/gpl-2.0.htm GNU/GPL
 * @copyright	Copyright (C) 2008-2009 Kuneri Ltd. All rights reserved.
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

defined( '_MJ' ) or die( 'Incorrect using of Kuneri Mobile Joomla.' );

$MobileJoomla =& MobileJoomla::getInstance();

if (!$_GET["naked"]) {
    
    $MobileJoomla->showXMLheader();
    $MobileJoomla->showDocType();
    
    $base = JURI::base () . "templates/" . $this->template;
?>

<!doctype html>
<html <?php echo $MobileJoomla->getXmlnsString(); ?>>
    <head>
        <meta http-equiv="Content-Type" content="<?php echo $MobileJoomla->getContentString(); ?>" />
        <?php $MobileJoomla->showHead(); ?>
        <style type="text/css" media="screen">@import "<?php echo $base?>/jqtouch-src/jqtouch/jqtouch.min.css";</style>
        <style type="text/css" media="screen">@import "<?php echo $base?>/jqtouch-src/themes/apple/theme.min.css";</style>
        <style type="text/css" media="screen">@import "<?php echo $base?>/mj_iphone.css";</style>
        <script src="<?php echo $base?>/jqtouch-src/jqtouch/jquery.1.3.2.min.js" type="text/javascript" charset="utf-8"></script>
        <script src="<?php echo $base?>/jqtouch-src/jqtouch/jqtouch.js" type="application/x-javascript" charset="utf-8"></script>
        <script src="<?php echo $base?>/mj_iphone.js" type="text/javascript" charset="utf-8"></script> 
        <?php echo ($MobileJoomla->_ishomepage) ? "<script>var isFrontPage = true;</script>": "<script>var isFrontPage = false;</script>"; ?>           
    </head>
    <body>
<?php } ?>
        <div<?php echo ($MobileJoomla->_ishomepage) ? " id=\"home\"" : ""?>>
        
            <div class="toolbar">
                <h1>Mobile Joomla!</h1>
                <a class="back" href="javascript:history.go(-1)">Back</a>
                <a class="button" href="<?php echo JURI::base();?>">Home</a>
            </div>

            <?php
            
            $modulepos=$MobileJoomla->getPosition('header');
            if( $modulepos && $this->countModules($modulepos)>0) {
            	?><?php $MobileJoomla->loadModules($modulepos); ?><?php
            }
            $modulepos=$MobileJoomla->getPosition('header2');
            if( $modulepos && $this->countModules($modulepos)>0) {
            	?><?php $MobileJoomla->loadModules($modulepos); ?><?php
            }
            
            ?>


            <?php
            $modulepos = $MobileJoomla->getPosition('middle');
            if ($modulepos && $this->countModules($modulepos)>0 && $MobileJoomla->_ishomepage) {
            	?><div id="<?php echo $modulepos; ?>"><?php $MobileJoomla->loadModules($modulepos); ?></div><?php
            }
            ?>
            
            <?php if (!(!$MobileJoomla->config['tmpl_iphone_componenthome'] && $MobileJoomla->_ishomepage)):?>
            <div class="content">
            <?php
            $modulepos=$MobileJoomla->getPosition('middle2');
            if( $modulepos && $this->countModules($modulepos)>0) {
            	?><div id="<?php echo $modulepos; ?>"><?php $MobileJoomla->loadModules($modulepos); ?></div><?php
            }

            $MobileJoomla->showMainBody();
            ?>
            </div>
            <?php endif;?>
            
            <?php
            $modulepos=$MobileJoomla->getPosition('footer');
            if( $modulepos && $this->countModules($modulepos)>0) {
            	?><div id="<?php echo $modulepos; ?>"><?php $MobileJoomla->loadModules($modulepos); ?></div><?php
            }
            $MobileJoomla->showFooter();
            $modulepos=$MobileJoomla->getPosition('footer2');
            if( $modulepos && $this->countModules($modulepos)>0) {
            	?><div id="<?php echo $modulepos; ?>"><?php $MobileJoomla->loadModules($modulepos); ?></div><?php
            }
            
            ?>            

            
        </div>

<?php if(!$_GET["naked"]) { ?>
            
    </body>
</html>

<?php } ?>