<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<p>
<input type="text" name="searchword" maxlength="<?php echo $maxlength; ?>" value="<?php echo $text; ?>" />
</p>
<do type="accept" title="<?php echo $button_text; ?>">
<go href="index.php?option=com_search&amp;searchword=$(searchword)" />
</do>