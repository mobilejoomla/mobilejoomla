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

abstract class MjController
{
    /** @var string */
    public $name = '';

    /** @var string */
    public $action = 'display';

    /** @var MjJoomlaWrapper */
    protected $joomlaWrapper;

    /**
     * @param $joomlaWrapper MjJoomlaWrapper
     */
    public function __construct($joomlaWrapper)
    {
        $this->joomlaWrapper = $joomlaWrapper;
    }

    public function execute($action)
    {
        $this->action = $action;
        if (!method_exists($this, $action)) {
            $this->joomlaWrapper->raiseWarning('Action is not found');
            return false;
        }
        return $this->$action();
    }

    public function display()
    {
        $this->loadFramework();

        $viewName = $this->joomlaWrapper->getRequestWord('view', 'default');

        echo $this->renderView('global/page', array(
            'sidebar' => $this->renderView('global/sidebar', array(
                'controllerName' => $this->name,
                'viewName' => $viewName
            )),
            'content' => $this->renderView($viewName)
        ));
    }

    protected function loadFramework()
    {
        $this->joomlaWrapper->loadMootools();

        $bootstrapTemplate = version_compare(JVERSION, '3.0', '>=');
        if (!$bootstrapTemplate) {
            // @todo: use minified files
            $doc = JFactory::getDocument();
            $doc->addStyleSheet('components/com_mobilejoomla/assets/css/j3x_template.css');
            $doc->addScript('components/com_mobilejoomla/assets/js/jquery.min.js');
            $doc->addScript('components/com_mobilejoomla/assets/js/jquery-noconflict.js');
            $doc->addScript('components/com_mobilejoomla/assets/js/bootstrap.min.js');
            $doc->addScript('components/com_mobilejoomla/assets/js/chosen.jquery.min.js');
            $doc->addScript('components/com_mobilejoomla/assets/js/j3x_template.js');
            JHtml::_('behavior.tooltip');
            if (substr(JVERSION, 0, 3) === '1.5') {
                JHtml::script(JUri::root(true) . '/includes/js/overlib_mini.js');
            }
        } else {
            JHtml::_('jquery.framework');
            JHtml::_('bootstrap.tooltip');
        }
    }

    public function save($msg = '')
    {
        if ($this->action === 'save') {
            $redirectUrl = 'index.php?option=com_mobilejoomla';
        } else {
            $controllerName = $this->name;
            $viewName = $this->joomlaWrapper->getRequestWord('view', 'default');
            $redirectUrl = 'index.php?option=com_mobilejoomla&controller=' . $controllerName . '&view=' . $viewName;
        }

        $app = JFactory::getApplication();
        $app->enqueueMessage($msg);
        $app->redirect($redirectUrl);
    }

    public function apply()
    {
        $this->save();
    }

    public function cancel()
    {
        $app = JFactory::getApplication();
        $app->redirect('index.php?option=com_mobilejoomla');
    }

    /**
     * @param string $viewName
     * @param array $params
     * @return null|string
     */
    protected function renderView($viewName, $params = array())
    {
        include_once JPATH_COMPONENT . '/classes/array_unshift_assoc.php';
        include_once JPATH_COMPONENT . '/classes/mjhtml.php';

        /* @todo: move events to wrapper */
        JPluginHelper::importPlugin('mobile');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onMJRenderView', array($viewName, &$params));

        if (strpos($viewName, '/') !== false) {
            list($controllerName, $viewName) = explode('/', $viewName, 2);
        } else {
            $controllerName = $this->name;
        }

        $filename = JPATH_COMPONENT . '/views/' . $controllerName . '/' . $viewName . '.php';
        if (!is_file($filename)) {
            $this->joomlaWrapper->raiseWarning('View is not found');
            return null;
        }

        ob_start();
        $this->requireFile($filename, $params, $controllerName, $viewName);
        return ob_get_clean();
    }

    private function requireFile($filepath, $params, $controllerName, $viewName)
    {
        return include($filepath);
    }
}