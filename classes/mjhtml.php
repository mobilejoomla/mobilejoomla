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

class MjHtml
{
    static public function label($name, $text_code, $tooltip_code = '', $tooltip_extra = '')
    {
        if ($name === '') {
            $html =
                '<label class="control-label' . ($tooltip_code ? ' hasTip hasTooltip' : '') . '"' .
                ($tooltip_code ? ' data-toggle="tooltip" title="' . htmlspecialchars(JText::_($tooltip_code) . JText::_($tooltip_extra)) . '"' : '') .
                '>' .
                JText::_($text_code) .
                '</label>';
        } else {
            $name = self::id($name);

            $html =
                '<label for="' . $name . '"' .
                ' class="control-label' . ($tooltip_code ? ' hasTip hasTooltip' : '') . '"' .
                ($tooltip_code ? ' data-toggle="tooltip" title="' . htmlspecialchars(JText::_($tooltip_code) . JText::_($tooltip_extra)) . '"' : '') .
                '>' .
                JText::_($text_code) .
                '</label>';
        }

        return $html;
    }

    static public function id($name)
    {
        return 'mj_' . str_replace('.', '-', $name);
    }

    static public function text($text)
    {
        $html = '<p class="help-inline">' . $text . '</p>';
        return $html;
    }

    /**
     * @param string $name
     * @param string $value
     * @param array $attrs
     * @return string
     */
    static public function textinput($name, $value, $attrs = array())
    {
        $name = self::id($name);

        $attrs['name'] = $name;
        $attrs['id'] = $name;
        $attrs['value'] = htmlspecialchars($value);
        $attrs['class'] = isset($attrs['class']) ? $attrs['class'] . ' ' : '';
        $attrs['class'] .= 'form-control input-large input-block-level';

        $html = '<input type="text"';
        foreach ($attrs as $name => $value) {
            $html .= ' ' . $name . '="' . $value . '"';
        }
        $html .= '>';

        return $html;
    }

    /**
     * @param string $name
     * @param string $value
     * @param array $attrs
     * @param string $default
     * @return string
     */
    static public function numberinput($name, $value, $attrs = array(), $default = '')
    {
        $name = self::id($name);

        $attrs['name'] = $name;
        $attrs['id'] = $name;
        $attrs['value'] = htmlspecialchars($value);
        $attrs['class'] = isset($attrs['class']) ? $attrs['class'] . ' ' : '';
        $attrs['class'] .= 'form-control input-small input-block-level';
        $attrs['placeholder'] = htmlspecialchars($default);

//        $html = '<input type="number"';
        $html = '<input type="text"'; // allow empty value
        foreach ($attrs as $name => $value) {
            $html .= ' ' . $name . '="' . $value . '"';
        }
        $html .= '>';

        return $html;

    }

    static public function radio($name, $value, $items)
    {
        $name = self::id($name);

        if ($value === null) {
            $value = ''; // "global" value
        }

        $value = (string)$value;

        $html = '<fieldset class="radio btn-group btn-group-yesno">';
        foreach ($items as $key => $title) {
            $html .=
                '<label for="jform_' . $name . $key . '">' .
                '<input type="radio"' .
                ' name="' . $name . '"' .
                ' id="jform_' . $name . $key . '"' .
                ' value="' . $key . '"' .
                ($value === (string)$key ? ' checked="checked"' : '') .
                '>' .
                $title .
                '</label>';
        }
        $html .= '</fieldset>';

        return $html;
    }

    static public function onoff($name, $value)
    {
        $name = self::id($name);

        $html =
            '<fieldset class="radio btn-group btn-group-yesno">' .
            '<label for="jform_' . $name . '1">' .
            '<input type="radio" name="' . $name . '" id="jform_' . $name . '1" value="1"' . ($value ? ' checked="checked"' : '') . '>' .
            JText::_('COM_MJ__YES') .
            '</label>' .
            '<label for="jform_' . $name . '0">' .
            '<input type="radio" name="' . $name . '" id="jform_' . $name . '0" value="0"' . ($value ? '' : ' checked="checked"') . '>' .
            JText::_('COM_MJ__NO') .
            '</label>' .
            '</fieldset>';
        return $html;
    }

    static public function gonoff($name, $value)
    {
        $name = self::id($name);

        if ($value === null) {
            $value = ''; // "global" value
        }

        $value = (string)$value;

        $html =
            '<fieldset class="radio btn-group btn-group-yesno">' .
            '<label for="jform_' . $name . '1">' .
            '<input type="radio" name="' . $name . '" id="jform_' . $name . '1" value="1"' . ($value !== '1' ? '' : ' checked="checked"') . '>' .
            JText::_('COM_MJ__YES') .
            '</label>' .
            '<label for="jform_' . $name . '">' .
            '<input type="radio" name="' . $name . '" id="jform_' . $name . '" value=""' . ($value !== '' ? '' : ' checked="checked"') . '>' .
            JText::_('COM_MJ__GLOBAL') .
            '</label>' .
            '<label for="jform_' . $name . '0">' .
            '<input type="radio" name="' . $name . '" id="jform_' . $name . '0" value="0"' . ($value !== '0' ? '' : ' checked="checked"') . '>' .
            JText::_('COM_MJ__NO') .
            '</label>' .
            '</fieldset>';
        return $html;
    }

    static public function slider($name, $value, $min = 0, $max = 100, $unit = '%')
    {
        $name = self::id($name);

        $html =
            '<span class="slider-container">' .
            '<span id="' . $name . '_slider" target="' . $name . '" class="slider">' .
            '<span id="' . $name . '_knob" class="knob"></span>' .
            '</span>' .
            '</span>' .
            '<span class="slider-input-container">' .
            '<span class="input-append">' .
            '<input type="text" class="slider form-control input-mini input-block-level" id="' . $name . '" name="' . $name . '" value="' . $value . '" size="2">' .
            '<span class="add-on">' . $unit . '</span>' .
            '</span>' .
            '</span>';
        return $html;
    }

    static public function menulist($name, $value, $multiple = false, $updateName = '')
    {
        static $menuoptions;
        if (!isset($menuoptions)) {
            $menuoptions = self::menuoptions();
        }

        $html = '<select class="form-control input-large input-block-level" size="7"';
        if ($name) {
            $html .= ' name="' . self::id($name) . ($multiple ? '[]' : '') . '"';
        }
        if ($multiple) {
            $html .= ' multiple="multiple"';
        }
        if ($updateName) {
            $html .= ' onchange="document.getElementById(\'' . self::id($updateName) . '\').value=this.value"';
        }
        $html .= '>';

        $value = (array)$value;

        foreach ($menuoptions as $item) {
            if ($multiple && $item->value = '') {
                continue;
            }
            switch ($item->value) {
                case '<OPTGROUP>':
                    $html .= '<optgroup label="' . $item->text . '">';
                    break;
                case '</OPTGROUP>':
                    $html .= '</optgroup>';
                    break;
                default:
                    $html .=
                        '<option value="' . $item->value . '"' .
                        (in_array((string)$item->value, $value, true) ? ' selected' : '') .
                        ($item->disable ? ' disabled' : '') .
                        '>' .
                        $item->text .
                        '</option>';
            }
        }

        $html .= '</select>';
        return $html;
    }

    static private function menuoptions()
    {
        $joomlaWrapper = MjJoomlaWrapper::getInstance();
        $db = $joomlaWrapper->getDbo();

        $isJoomla15 = (substr(JVERSION, 0, 3) === '1.5');

        $query = new MjQueryBuilder($db);
        if (version_compare(JVERSION, '3.0', '>=')) { // 3.0+
            $query
                ->select('id', 'menutype', 'title', 'link', 'type', 'parent_id')
                ->from('#__menu')
                ->where($query->qn('published') . '=1')
                ->order('menutype', 'parent_id', 'lft')
                ->setQuery();
        } elseif (version_compare(JVERSION, '1.6', '>=')) { //1.6-2.5
            $query
                ->select('id', 'menutype', 'title', 'link', 'type', 'parent_id')
                ->from('#__menu')
                ->where($query->qn('published') . '=1')
                ->order('menutype', 'parent_id', 'ordering')
                ->setQuery();
        } else { //1.5
            $query
                ->select('id', 'menutype', $query->qn('name') . ' AS ' . $query->qn('title'),
                    'link', 'type', $query->qn('parent') . ' AS ' . $query->qn('parent_id'))
                ->from('#__menu')
                ->where($query->qn('published') . '=1')
                ->order('menutype', 'parent', 'ordering')
                ->setQuery();
        }
        /** @var array $mitems */
        $mitems = $db->loadObjectList();
        $children = array();
        foreach ($mitems as $v) {
            $pt = $v->parent_id;
            $list = @$children[$pt] ? $children[$pt] : array();
            array_push($list, $v);
            $children[$pt] = $list;
        }
        $list = array();
        $id = $isJoomla15 ? 0 : 1;
        if (@$children[$id]) {
            self::TreeRecurse($id, '', $list, $children);
        }
        $mitems = array();
        $mitems[] = JHtml::_('select.option', '', '&nbsp;');
        $lastMenuType = null;
        foreach ($list as $list_a) {
            if ($list_a->menutype !== $lastMenuType) {
                if ($lastMenuType) {
                    $mitems[] = JHtml::_('select.option', '</OPTGROUP>');
                }
                $mitems[] = JHtml::_('select.option', '<OPTGROUP>', $list_a->menutype);
                $lastMenuType = $list_a->menutype;
            }
            if ($list_a->type === 'component') {
                $link = $list_a->link . '&Itemid=' . $list_a->id;
            } else {
                $link = '-';
            }
            $mitems[] = JHtml::_('select.option', $link, $list_a->treename, 'value', 'text', $link === '-');
        }
        if ($lastMenuType !== null) {
            $mitems[] = JHtml::_('select.option', '</OPTGROUP>');
        }
        return $mitems;
    }

    static private function TreeRecurse($id, $indent, &$list, &$children, $level = 0)
    {
        foreach ($children[$id] as $v) {
            $id = $v->id;
            $list[$id] = $v;
            $list[$id]->treename = $indent . $v->title;
            if (@$children[$id] && $level <= 99) {
                self::TreeRecurse($id, $indent . '&nbsp;&nbsp;', $list, $children, $level + 1);
            }
        }
    }

    static public function template($name, $value)
    {
        static $templates;
        if (!isset($templates)) {
            $isJoomla15 = (substr(JVERSION, 0, 3) === '1.5');

            $templates = array();
            $templates[''] = JText::_('COM_MJ__DEFAULT_TEMPLATE_OPTION');

            if ($isJoomla15) {
                $templateBaseDir = JPATH_SITE . '/templates/';

                jimport('joomla.installer.installer');
                jimport('joomla.filesystem.file');
                jimport('joomla.filesystem.folder');
                $templateDirs = JFolder::folders($templateBaseDir);
                foreach ($templateDirs as $templateDir) {
                    $templateFile = $templateBaseDir . $templateDir . '/templateDetails.xml';
                    if (JFile::exists($templateFile)) {
                        $xml = JApplicationHelper::parseXMLInstallFile($templateFile);
                        if ($xml['type'] === 'template') {
                            $templates[$templateDir] = $templateDir;
                        }
                    }
                }
            } else {
                $joomlaWrapper = MjJoomlaWrapper::getInstance();
                $db = $joomlaWrapper->getDbo();
                $query = new MjQueryBuilder($db);
                $rows = $query
                    ->select('id', 'title')
                    ->from('#__template_styles')
                    ->where('client_id=0')
                    ->order('title')
                    ->setQuery()
                    ->loadObjectList();
                foreach ($rows as $row) {
                    $templates[$row->id] = $row->title;
                }
            }
        }

        return self::select($name, $value, $templates);
    }

    static public function select($name, $value, $items)
    {
        $name = self::id($name);

        if ($value === null) {
            $value = ''; // "global" value
        }

        $value = (string)$value;

        $html = '<select name="' . $name . '" class="form-control">';
        foreach ($items as $key => $item) {
            $html .=
                '<option value="' . $key . '"' . ($value === (string)$key ? ' selected="selected"' : '') . '>' .
                $item .
                '</option>';
        }
        $html .= '</select>';
        return $html;
    }

    /**
     * @param string $name
     * @param string $value
     * @param array $attrs
     * @return string
     */
    static public function hidden($name, $value, $attrs = array())
    {
        $name = self::id($name);

        $attrs['name'] = $name;
        $attrs['id'] = $name;
        $attrs['value'] = htmlspecialchars($value);

        $html = '<input type="hidden"';
        foreach ($attrs as $name => $value) {
            $html .= ' ' . $name . '="' . $value . '"';
        }
        $html .= '>';

        return $html;
    }

    static public function prolabel($text_code)
    {
        return '<label class="mjpro control-label">' . JText::_($text_code) . '</label>';
    }

    static public function proonoff()
    {
        $html =
            '<fieldset class="radio btn-group btn-group-yesno">' .
            '<label class="btn active">' .
            '<input type="radio" value="1">' .
            JText::_('COM_MJ__YES') .
            '</label>' .
            '<label class="btn active">' .
            '<input type="radio" value="0">' .
            JText::_('COM_MJ__NO') .
            '</label>' .
            '</fieldset>';
        return $html;
    }

    static public function proslider($value, $min = 0, $max = 100, $unit = '%')
    {
        $name = mt_rand();
        $html =
            '<span class="slider-container">' .
            '<span id="' . $name . '_slider" target="' . $name . '" class="slider">' .
            '<span id="' . $name . '_knob" class="knob"></span>' .
            '</span>' .
            '</span>' .
            '<span class="slider-input-container">' .
            '<span class="input-append">' .
            '<input type="text" class="slider form-control input-mini input-block-level" id="' . $name . '" value="' . $value . '" size="2">' .
            '<span class="add-on">' . $unit . '</span>' .
            '</span>' .
            '</span>';
        return $html;
    }

}