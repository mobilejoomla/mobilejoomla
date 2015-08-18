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

/** @var JDocumentHTML $document */
/** @var array $headerstuff */

$tagEnd = (JPluginHelper::isEnabled('system', 'sh404sef')
    || JPluginHelper::isEnabled('system', 'shsef')
    || JPluginHelper::isEnabled('system', 'acesef')
    || JPluginHelper::isEnabled('system', 'byebyegenerator')
) ? "/>\n" : '>';

// Convert the tagids to titles
if (class_exists('JHelperTags') && isset($headerstuff['metaTags']['standard']['tags'])) {
    $tagsHelper = new JHelperTags();
    $headerstuff['metaTags']['standard']['tags'] = implode(', ', $tagsHelper->getTagNames($headerstuff['metaTags']['standard']['tags']));
}

// Generate base tag (need to happen early)
$base = $document->getBase();
if (!empty($base))
    $buffer .= '<base href="' . $base . '"' . $tagEnd;

// Generate META tags (needs to happen as early as possible in the head)
foreach ($headerstuff['metaTags'] as $type => $tag) {
    foreach ($tag as $name => $content) {
        if ($type == 'http-equiv' && !($document->isHtml5() && $name == 'content-type'))
            $buffer .= '<meta http-equiv="' . $name . '" content="' . htmlspecialchars($content) . '"' . $tagEnd;
        elseif ($type == 'standard' && !empty($content))
            $buffer .= '<meta name="' . $name . '" content="' . htmlspecialchars($content) . '"' . $tagEnd;
    }
}

// Don't add empty descriptions
$documentDescription = $document->getDescription();
if ($documentDescription)
    $buffer .= '<meta name="description" content="' . htmlspecialchars($documentDescription) . '"' . $tagEnd;

// Don't add empty generators
$generator = $document->getGenerator();
if ($generator)
    $buffer .= '<meta name="generator" content="' . htmlspecialchars($generator) . '"' . $tagEnd;

$buffer .= '<title>' . htmlspecialchars($document->getTitle(), ENT_COMPAT, 'UTF-8') . '</title>';

// Generate link declarations
foreach ($headerstuff['links'] as $link => $linkAtrr) if ($link) {
    $buffer .= '<link href="' . $link . '" ' . $linkAtrr['relType'] . '="' . $linkAtrr['relation'] . '"';
    if ($temp = JArrayHelper::toString($linkAtrr['attribs']))
        $buffer .= ' ' . $temp;
    $buffer .= $tagEnd;
}

// Generate icons
$buffer .= $this->loadIcons();

// Generate preloaded link declarations
foreach ($headerstuff['preload_styleSheets'] as $strSrc => $strAttr) if ($strSrc) {
    $buffer .= '<link rel="stylesheet" href="' . $strSrc . '"' . $tagEnd;
    $this->loaded_css[$strSrc] = 1;
}

// Generate Template link declaration
$buffer .= $this->loadCSS();

// Generate stylesheet links
foreach ($headerstuff['styleSheets'] as $strSrc => $strAttr) if ($strSrc && !isset($this->loaded_css[$strSrc])) {
    $buffer .= '<link rel="stylesheet" href="' . $strSrc . '"';
    if ($strAttr['mime'] !== 'text/css')
        $buffer .= ' type="' . $strAttr['mime'] . '"';
    if (!is_null($strAttr['media']))
        $buffer .= ' media="' . $strAttr['media'] . '" ';
    if ($temp = JArrayHelper::toString($strAttr['attribs']))
        $buffer .= ' ' . $temp;
    $buffer .= $tagEnd;
}

// Generate stylesheet declarations
foreach ($headerstuff['style'] as $type => $content) if ($content) {
    $buffer .= "<style";
    if ($type !== 'text/css')
        $buffer .= " type=\"$type\"";
    $buffer .= ">$content</style>";
}

// Generate preloaded script file links
foreach ($headerstuff['preload_scripts'] as $strSrc => $strAttr) if ($strSrc) {
    $buffer .= '<script src="' . $strSrc . '"></script>';
    $this->loaded_js[$strSrc] = 1;
}

// Generate Template script file links (with custom.js)
$buffer .= $this->loadJS();

// Generate script file links
foreach ($headerstuff['scripts'] as $strSrc => $strAttr) if ($strSrc && !isset($this->loaded_js[$strSrc])) {
    $buffer .= '<script src="' . $strSrc . '"';
    if (!is_null($strAttr['mime']) && $strAttr['mime'] !== 'text/javascript')
        $buffer .= ' type="' . $strAttr['mime'] . '"';
    if ($strAttr['defer'])
        $buffer .= ' defer="defer"';
    if ($strAttr['async'])
        $buffer .= ' async="async"';
    $buffer .= '></script>';
}

// Generate script declarations
foreach ($headerstuff['script'] as $type => $content) if ($content) {
    $buffer .= "<script";
    if ($type !== 'text/javascript')
        $buffer .= " type=\"$type\"";
    $buffer .= ">\n$content\n</script>";
}

// Generate script language declarations.
if (count(JText::script())) {
    $buffer .= '<script type="text/javascript">' . "\n";
    $buffer .= '(function(){';
    $buffer .= 'var strings=' . json_encode(JText::script()) . ';';
    $buffer .= 'if(typeof Joomla==\'undefined\'){';
    $buffer .= 'Joomla={};';
    $buffer .= 'Joomla.JText=strings;';
    $buffer .= '}else{';
    $buffer .= 'Joomla.JText.load(strings);';
    $buffer .= '}';
    $buffer .= '})();';
    $buffer .= '</script>';
}

foreach ($headerstuff['custom'] as $custom) if ($custom)
    $buffer .= $custom . "\n";

if (@filesize(JPATH_THEMES . '/' . $this->template . '/css/custom.css'))
    $buffer .= $this->htmlCSS($this->base . 'css/custom.css');
