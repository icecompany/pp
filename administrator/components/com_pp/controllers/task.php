<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Controller\FormController;

class PpControllerTask extends FormController {
    public function add()
    {
        $uri = JUri::getInstance($_SERVER['HTTP_REFERER']);
        $sectionID = $uri->getVar('filter_sub_section', '');
        JFactory::getApplication()->setUserState("{$this->option}.task.sectionID", $sectionID);
        return parent::add();
    }

    public function display($cachable = false, $urlparams = array())
    {
        return parent::display($cachable, $urlparams);
    }
}