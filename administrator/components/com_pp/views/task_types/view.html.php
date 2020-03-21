<?php
use Joomla\CMS\MVC\View\HtmlView;

defined('_JEXEC') or die;

class PpViewTask_types extends HtmlView
{
    protected $sidebar = '';
    public $items, $pagination, $uid, $state, $filterForm, $activeFilters;

    public function display($tpl = null)
    {
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

        // Show the toolbar
        $this->toolbar();

        // Show the sidebar
        PpHelper::addSubmenu('task_types');
        $this->sidebar = JHtmlSidebar::render();

        // Display it all
        return parent::display($tpl);
    }

    private function toolbar()
    {
        JToolBarHelper::title(JText::sprintf('COM_PP_MENU_TASK_TYPES'), 'signup');

        if (PpHelper::canDo('core.create'))
        {
            JToolbarHelper::addNew('task_type.add');
        }
        if (PpHelper::canDo('core.edit'))
        {
            JToolbarHelper::editList('task_type.edit');
        }
        if (PpHelper::canDo('core.delete'))
        {
            JToolbarHelper::deleteList('COM_PP_CONFIRM_REMOVE_TASK_TYPE', 'task_types.delete');
        }
        if (PpHelper::canDo('core.admin'))
        {
            JToolBarHelper::preferences('com_pp');
        }
    }
}
