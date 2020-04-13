<?php
use Joomla\CMS\MVC\View\HtmlView;

defined('_JEXEC') or die;

class PpViewTasks extends HtmlView
{
    protected $sidebar = '';
    public $items, $pagination, $uid, $state, $filterForm, $activeFilters, $sectionTitle;

    public function display($tpl = null)
    {
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        $this->sectionTitle = $this->get('SectionTitle');

        $this->filterForm->setValue('project', 'filter', $this->state->get('filter.project'));

        // Show the toolbar
        $this->toolbar();

        // Show the sidebar
        PpHelper::addSubmenu('tasks');
        $this->sidebar = JHtmlSidebar::render();

        // Display it all
        return parent::display($tpl);
    }

    private function toolbar()
    {
        $title = JText::sprintf('COM_PP_MENU_TASKS');
        if (!empty($this->sectionTitle)) $title = JText::sprintf('COM_PP_MENU_TASKS_IN_SECTION', $this->sectionTitle);

        JToolBarHelper::title($title, 'calendar');

        if (JFactory::getApplication()->input->getBool('back', false)) JToolbarHelper::back();
        if (PpHelper::canDo('core.create'))
        {
            JToolbarHelper::addNew('task.add');
        }
        if (PpHelper::canDo('core.edit'))
        {
            JToolbarHelper::editList('task.edit');
        }
        if (PpHelper::canDo('core.delete'))
        {
            JToolbarHelper::deleteList('COM_PP_CONFIRM_REMOVE_TASK', 'tasks.delete');
        }
        if (is_numeric($this->state->get('filter.section'))) JToolBarHelper::custom('section.add', 'pie', '', JText::sprintf('COM_PP_ACTION_LINK_ADD_SECTION'), false);
        if (PpHelper::canDo('core.admin'))
        {
            JToolBarHelper::preferences('com_pp');
        }
    }
}
