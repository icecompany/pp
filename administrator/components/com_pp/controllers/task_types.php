<?php
use Joomla\CMS\MVC\Controller\AdminController;

defined('_JEXEC') or die;

class PpControllerTaskTypes extends AdminController
{
    public function getModel($name = 'Task_type', $prefix = 'PpModel', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }
}
