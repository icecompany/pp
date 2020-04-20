<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView;

class PpViewVersion extends HtmlView {
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
        JToolBarHelper::apply('version.apply', 'JTOOLBAR_APPLY');
        JToolbarHelper::save('version.save', 'JTOOLBAR_SAVE');
        JToolbarHelper::cancel('version.cancel', 'JTOOLBAR_CLOSE');
        JFactory::getApplication()->input->set('hidemainmenu', true);
    }

    protected function setDocument() {
        $title = ($this->item->id !== null) ? JText::sprintf('COM_PP_TITLE_VERSION_EDIT', $this->item->version) : JText::sprintf('COM_PP_TITLE_VERSION_ADD');
        JToolbarHelper::title($title, 'tag');
        JHtml::_('bootstrap.framework');
    }
}