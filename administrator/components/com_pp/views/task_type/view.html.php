<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView;

class PpViewTask_type extends HtmlView {
    protected $item, $form, $script;

    public function display($tmp = null) {
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        $this->script = $this->get('Script');

        $this->addToolbar();
        $this->setDocument();

        parent::display($tmp);
    }

    protected function addToolbar() {
	    JToolBarHelper::apply('task_type.apply', 'JTOOLBAR_APPLY');
        JToolbarHelper::save('task_type.save', 'JTOOLBAR_SAVE');
        JToolbarHelper::cancel('task_type.cancel', 'JTOOLBAR_CLOSE');
        JFactory::getApplication()->input->set('hidemainmenu', true);
    }

    protected function setDocument() {
        $title = ($this->item->id !== null) ? JText::sprintf('COM_PP_TITLE_PRICE_EDIT', $this->item->title) : JText::sprintf('COM_PP_TITLE_PRICE_ADD');
        JToolbarHelper::title($title, 'list');
        JHtml::_('bootstrap.framework');
    }
}