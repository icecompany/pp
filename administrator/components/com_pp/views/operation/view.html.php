<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView;

class PpViewOperation extends HtmlView {
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
	    JToolBarHelper::apply('operation.apply', 'JTOOLBAR_APPLY');
        JToolbarHelper::save('operation.save', 'JTOOLBAR_SAVE');
        JToolbarHelper::cancel('operation.cancel', 'JTOOLBAR_CLOSE');
        JFactory::getApplication()->input->set('hidemainmenu', true);
    }

    protected function setDocument() {
        $title = ($this->item->id !== null) ? JText::sprintf('COM_PP_TITLE_OPERATION_EDIT') : JText::sprintf('COM_PP_TITLE_OPERATION_ADD');
        JToolbarHelper::title($title, 'tablet');
        JHtml::_('bootstrap.framework');
    }
}