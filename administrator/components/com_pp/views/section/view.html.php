<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView;

class PpViewSection extends HtmlView {
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
	    JToolBarHelper::apply('section.apply', 'JTOOLBAR_APPLY');
        JToolbarHelper::save('section.save', 'JTOOLBAR_SAVE');
        JToolbarHelper::cancel('section.cancel', 'JTOOLBAR_CLOSE');
        JFactory::getApplication()->input->set('hidemainmenu', true);
    }

    protected function setDocument() {
        $title = ($this->item->id !== null) ? JText::sprintf('COM_PP_TITLE_SECTION_EDIT', $this->item->title) : JText::sprintf('COM_PP_TITLE_SECTION_ADD');
        JToolbarHelper::title($title, 'grid');
        JHtml::_('bootstrap.framework');
    }
}