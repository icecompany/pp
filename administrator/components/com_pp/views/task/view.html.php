<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView;

class PpViewTask extends HtmlView {
    protected $item, $form, $script;

    public function display($tmp = null) {
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        $this->script = $this->get('Script');

        $this->addToolbar();
        $this->setDocument();

        //Скрываем поля, доступные только для релиза
        if (!PpHelper::canDo('core.task_types') || ((int) $this->item->typeID !== 2) && $this->item->id !== null) {
            $this->form->removeField('version_add');
        }

        parent::display($tmp);
    }

    protected function addToolbar() {
        if (!$this->item->date_close) {
            JToolBarHelper::apply('task.apply', 'JTOOLBAR_APPLY');
            JToolbarHelper::save('task.save', 'JTOOLBAR_SAVE');
        }
        JToolbarHelper::cancel('task.cancel', 'JTOOLBAR_CLOSE');
        JFactory::getApplication()->input->set('hidemainmenu', true);
    }

    protected function setDocument() {
        $title = ($this->item->id !== null) ? JText::sprintf('COM_PP_TITLE_TASK_EDIT') : JText::sprintf('COM_PP_TITLE_TASK_ADD');
        JToolbarHelper::title($title, 'tablet');
        JHtml::_('bootstrap.framework');
    }
}