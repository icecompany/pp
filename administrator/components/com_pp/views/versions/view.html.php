<?php
use Joomla\CMS\MVC\View\HtmlView;

defined('_JEXEC') or die;

class PpViewVersions extends HtmlView
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
        PpHelper::addSubmenu('versions');
        $this->sidebar = JHtmlSidebar::render();

        // Display it all
        return parent::display($tpl);
    }

    private function toolbar()
    {
        JToolBarHelper::title(JText::sprintf('COM_PP_MENU_VERSIONS'), 'tags');

        if (PpHelper::canDo('core.create'))
        {
            JToolbarHelper::addNew('version.add');
        }
        if (PpHelper::canDo('core.edit'))
        {
            JToolbarHelper::editList('version.edit');
        }
        if (PpHelper::canDo('core.delete'))
        {
            JToolbarHelper::deleteList('COM_PP_CONFIRM_REMOVE_VERSION', 'versions.delete');
        }
        if (PpHelper::canDo('core.admin'))
        {
            JToolBarHelper::preferences('com_pp');
        }
    }
}
