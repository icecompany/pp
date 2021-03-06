<?php
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

class PpHelper
{
    public function addSubmenu($vName)
    {
        HTMLHelper::_('sidebar.addEntry', JText::sprintf('COM_PP_MENU_PLAN'), 'index.php?option=com_pp&view=plan', $vName === 'plan');
        HTMLHelper::_('sidebar.addEntry', JText::sprintf('COM_PP_MENU_SECTIONS'), 'index.php?option=com_pp&view=sections', $vName === 'sections');
        HTMLHelper::_('sidebar.addEntry', JText::sprintf('COM_PP_MENU_TASKS'), 'index.php?option=com_pp&view=tasks', $vName === 'tasks');
        HTMLHelper::_('sidebar.addEntry', JText::sprintf('COM_PP_MENU_OPERATIONS'), 'index.php?option=com_pp&view=operations', $vName === 'operations');
        HTMLHelper::_('sidebar.addEntry', JText::sprintf('COM_PP_MENU_OBJECTS'), 'index.php?option=com_pp&view=objects', $vName === 'objects');
        if (self::canDo('core.task_types')) {
            HTMLHelper::_('sidebar.addEntry', JText::sprintf('COM_PP_MENU_VERSIONS'), 'index.php?option=com_pp&view=versions', $vName === 'versions');
            HTMLHelper::_('sidebar.addEntry', JText::sprintf('COM_PP_MENU_TASK_TYPES'), 'index.php?option=com_pp&view=task_types', $vName === 'task_types');
        }
    }

    public static function getTaskColor(int $status): string
    {
        $arr = [-2 => '#FF0000', 1 => '#008000', 2 => '#0000FF', 3 => '#000000'];
        return $arr[$status];
    }

    /**
     * Проверяет необходимость перезагрузить страницу. Используется для возврата на предыдущую страницу при отправке формы в админке
     * @throws Exception
     * @since 1.0.4
     */
    public static function check_refresh(): void
    {
        $refresh = JFactory::getApplication()->input->getBool('refresh', false);
        if ($refresh) {
            $current = JUri::getInstance(self::getCurrentUrl());
            $current->delVar('refresh');
            JFactory::getApplication()->redirect($current);
        }
    }

    /**
     * Возвращает параметр ID из реферера
     * @since 1.0.1
     * @return int ID Элемента
     */
    public static function getItemID(): int
    {
        $uri = JUri::getInstance($_SERVER['HTTP_REFERER']);
        return (int) $uri->getVar('id') ?? 0;
    }

    /**
     * Возвращает URL для обработки формы
     * @return string
     * @since 1.0.0
     * @throws
     */
    public static function getActionUrl(): string
    {
        $uri = JUri::getInstance();
        $uri->setVar('refresh', '1');

        $view = JFactory::getApplication()->input->getString('view');
        $taskID = JFactory::getApplication()->input->getInt('taskID');

        if ($view === 'plan' || $view === 'tasks' || ($view === 'operations' && $taskID > 0)) {
            $return = self::getReturnUrl();
            if ($uri->getVar('return', null) === null) $uri->setVar('return', $return);
        }

        $query = $uri->getQuery();
        $client = (!JFactory::getApplication()->isClient('administrator')) ? 'site' : 'administrator';
        return JRoute::link($client, "index.php?{$query}");
    }

    /**
     * Возвращает текущий URL
     * @return string
     * @since 1.0.0
     * @throws
     */
    public static function getCurrentUrl(): string
    {
        $uri = JUri::getInstance();
        $query = $uri->getQuery();
        return "index.php?{$query}";
    }

    /**
     * Возвращает URL для возврата (текущий адрес страницы)
     * @return string
     * @since 1.0.0
     */
    public static function getReturnUrl(): string
    {
        $uri = JUri::getInstance();
        $query = $uri->getQuery();
        return base64_encode("index.php?{$query}");
    }

    /**
     * Возвращает URL для обработки формы левой панели
     * @return string
     * @since 1.0.0
     */
    public static function getSidebarAction():string
    {
        $return = self::getReturnUrl();
        return JRoute::_("index.php?return={$return}");
    }

    public static function canDo(string $action): bool
    {
        return JFactory::getUser()->authorise($action, 'com_pp');
    }

    public static function getConfig(string $param, $default = null)
    {
        $config = JComponentHelper::getParams("com_pp");
        return $config->get($param, $default);
    }
}
