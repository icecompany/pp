<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Controller\FormController;

class PpControllerOperation extends FormController {
    public function display($cachable = false, $urlparams = array())
    {
        return parent::display($cachable, $urlparams);
    }

    public function add()
    {
        $input = $this->input;
        $taskID = $input->getInt('taskID', 0);
        if ($taskID > 0) {
            JFactory::getApplication()->setUserState("{$this->option}.operation.taskID", $taskID);
        }
        return parent::add();
    }
}