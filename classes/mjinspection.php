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
class MjInspection
{
    /** @var array */
    private $blob;

    /**
     * @param MjSettingsModel $mjSettings
     * @return array
     */
    public function getWarnings($mjSettings)
    {
        $this->blob = array();
        $this->checkGD2();
        $this->checkRemoteConnection();
        $this->checkAliasDuplicates();
        $this->checkTemplateAssignments();
        $this->checkForcedMarkup();
        return $this->blob;
    }

    private function checkGD2()
    {
        if (!function_exists('imagecopyresized')) {
            $this->blob[] = array(
                'label' => MjHtml::label('', 'COM_MJ__WARNING_GD2'),
                'input' => MjHtml::text(JText::_('COM_MJ__WARNING_GD2_TEXT'))
            );
        }
    }

    private function checkRemoteConnection()
    {
        if (!preg_match('#\.pro$#', '###VERSION###')) {
            return;
        }
        if (!function_exists('fsockopen')
            && !function_exists('curl_init')
            && !ini_get('allow_url_fopen')
        ) {
            $this->blob[] = array(
                'label' => MjHtml::label('', 'COM_MJ__WARNING_REMOTE'),
                'input' => MjHtml::text(JText::_('COM_MJ__WARNING_REMOTE_TEXT'))
            );
        }
    }

    private function checkAliasDuplicates()
    {
        if (substr(JVERSION, 0, 3) !== '1.5') {
            return;
        }

        $db = JFactory::getDbo();
        $query = "SELECT m1.id, m1.menutype, m1.name AS title, m1.alias FROM #__menu AS m1 LEFT JOIN #__menu AS m2 ON m1.alias=m2.alias WHERE m1.id<>m2.id AND m1.type<>'menulink' AND m2.type<>'menulink' GROUP BY m1.id ORDER BY m1.alias";
        $db->setQuery($query);
        $duples = $db->loadObjectList();

        $url_prefix = 'index.php?option=com_menus&task=edit&cid[]=';

        if (count($duples)) {
            $list = array();
            $alias = $duples[0]->alias;
            foreach ($duples as $item) {
                if ($alias != $item->alias) {
                    $list[] = '';
                    $alias = $item->alias;
                }
                $list[] = '<a href="' . $url_prefix . $item->id . '">' . $item->title . '</a> [' . $item->menutype . ']';
            }
            $this->blob[] = array(
                'label' => MjHtml::label('', 'COM_MJ__WARNING_ALIASES'),
                'input' => MjHtml::text(implode('<br>', $list))
            );
        }
    }

    private function checkTemplateAssignments()
    {
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.folder');
        $db = JFactory::getDbo();

        //get mobile templates
        $jpath_themes = JPATH_ROOT . '/templates';
        $templates = JFolder::folders($jpath_themes);
        $mobile_templates = array();
        foreach ($templates as $template) {
            if (is_file($jpath_themes . '/' . $template . '/templateDetails.xml')
                && is_file($jpath_themes . '/' . $template . '/index.php')
            ) {
                $content = file_get_contents($jpath_themes . '/' . $template . '/index.php');
                if (strpos($content, 'onGetMobileJoomla') !== false) {
                    $mobile_templates[] = $template;
                }
            }
        }

        // get assigned mobile templates
        $list = array();
        foreach ($mobile_templates as $template) {
            $list[] = $db->quote($template);
        }
        $list = implode(', ', $list);

        $isJoomla15 = (substr(JVERSION, 0, 3) === '1.5');

        $assigned_templates = array();
        if ($isJoomla15) {
            $query = "SELECT tm.template, tm.menuid, m.name FROM #__templates_menu AS tm LEFT JOIN #__menu AS m ON m.id=tm.menuid WHERE template IN ($list) AND tm.menuid>=0 AND tm.client_id=0 ORDER BY tm.template, tm.menuid";
            $db->setQuery($query);
            $rows = $db->loadObjectList();
            foreach ($rows as $row) {
                $assigned_templates[$row->template][] = array($row->menuid, $row->name);
            }
        } else {
            $joomlaWrapper = MjJoomlaWrapper::getInstance();
            $db = $joomlaWrapper->getDbo();

            $query = new MjQueryBuilder($db);
            $rows = $query
                ->select('template')
                ->from('#__template_styles')
                ->where("template IN ($list)")
                ->where('home=1')
                ->where('client_id=0')
                ->order('template')
                ->setQuery()
                ->loadObjectList();
            foreach ($rows as $row) {
                $assigned_templates[$row->template][] = array(0, null);
            }

            $query = new MjQueryBuilder($db);
            $rows = $query
                ->select('ts.template, m.id, m.title')
                ->from('#__menu AS m')
                ->leftJoin('#__template_styles AS ts ON m.template_style_id=ts.id')
                ->where("ts.template IN ($list)")
                ->where('ts.client_id=0')
                ->order('ts.template, m.id')
                ->setQuery()
                ->loadObjectList();
            foreach ($rows as $row) {
                $assigned_templates[$row->template][] = array($row->id, $row->title);
            }
        }

        if (count($assigned_templates)) {
            $url_prefix = 'index.php?option=com_menus&task=' . ($isJoomla15 ? 'edit&cid[]=' : 'item.edit&id=');

            $list = array();
            foreach ($assigned_templates as $key => $items) {
                foreach ($items as $item) {
                    $menuid = $item[0];
                    $title = $item[1];
                    if ($menuid) {
                        $list[] = $key . ' &lt; <a href="' . $url_prefix . $menuid . '">' . htmlspecialchars($title) . '</a>';
                    } else {
                        $list[] = '<a href="index.php?option=com_templates">' . $key . '</a>'
                            . ' (' . JText::_('COM_MJ__WARNING_ASSIGNEDTEMPLATES_DEFAULT') . ')';
                    }
                }
            }
            $this->blob[] = array(
                'label' => MjHtml::label('', 'COM_MJ__WARNING_ASSIGNEDTEMPLATES', 'COM_MJ__WARNING_ASSIGNEDTEMPLATES_DESC'),
                'input' => MjHtml::text(implode('<br>', $list))
            );
        }
    }

    private function checkForcedMarkup()
    {
        $markup = isset($_COOKIE['mjmarkup']) ? $_COOKIE['mjmarkup'] : '';
        if ($markup === 'desktop' || $markup === '') {
            return;
        }

        $resetUrl = JUri::root() . '?device=desktop';
        $this->blob[] = array(
            'label' => MjHtml::label('', 'COM_MJ__WARNING_FORCEDMARKUP'),
            'input' => MjHtml::text(
                ucfirst($markup)
                . ' <a href="' . $resetUrl . '" target="_blank" class="btn btn-success btn-mini">'
                . JText::_('COM_MJ__WARNING_FORCEDMARKUP_RESET')
                . '</a>'
            )
        );
    }
}