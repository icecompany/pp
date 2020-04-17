<?php
use Joomla\CMS\MVC\View\HtmlView;

defined('_JEXEC') or die;

class PpViewOperations extends HtmlView
{
    protected $sidebar = '';
    public $items, $pagination, $uid, $state, $filterForm, $activeFilters, $parentTask, $taskID;

    public function display($tpl = null)
    {
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        $this->parentTask = $this->get('ParentTask');
        $this->taskID = $this->get('TaskID');

        // Show the toolbar
        $this->toolbar();

        // Show the sidebar
        PpHelper::addSubmenu('operations');
        $this->sidebar = JHtmlSidebar::render();

        // Display it all
        return parent::display($tpl);
    }

    private function toolbar()
    {
        $title = (!empty($this->parentTask->task)) ? JText::sprintf('COM_PP_MENU_OPERATIONS_PARENT', $this->parentTask->task) : JText::sprintf('COM_PP_MENU_OPERATIONS');
        JToolBarHelper::title($title, 'list');

        if (!empty($this->parentTask)) JToolbarHelper::back();
        if (PpHelper::canDo('core.create') && !empty($this->parentTask->id && !$this->parentTask->date_close))
        {
            JToolbarHelper::addNew('operation.add');
        }
        if (PpHelper::canDo('core.edit') && !$this->parentTask->date_close)
        {
            JToolbarHelper::editList('operation.edit');
        }
        if (PpHelper::canDo('core.delete') && !$this->parentTask->date_close)
        {
            JToolbarHelper::deleteList('COM_PP_CONFIRM_REMOVE_OPERATION', 'operations.delete');
        }
        if (PpHelper::canDo('core.admin'))
        {
            JToolBarHelper::preferences('com_pp');
        }
    }
}
