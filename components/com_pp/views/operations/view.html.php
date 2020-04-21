<?php
use Joomla\CMS\MVC\View\HtmlView;

defined('_JEXEC') or die;

class PpViewOperations extends HtmlView
{
    protected $sidebar = '';
    public $items, $pagination, $uid, $state, $filterForm, $activeFilters, $parentTask, $taskID, $version;

    public function display($tpl = null)
    {
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        $this->parentTask = $this->get('ParentTask');
        $this->taskID = $this->get('TaskID');
        $this->version = $this->get('Version');

        $this->title();

        // Display it all
        return parent::display($tpl);
    }

    private function title()
    {
        $title = JText::sprintf('COM_PP_HEAD_PAGE_TITLE_OPERATIONS_IN_VERSION', $this->version->version, JDate::getInstance($this->version->dat)->format('d.m.Y'));
        $this->setDocumentTitle($title);
    }
}
