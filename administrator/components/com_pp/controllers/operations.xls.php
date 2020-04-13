<?php
use Joomla\CMS\MVC\Controller\AdminController;

defined('_JEXEC') or die;

class PpControllerOperations extends AdminController
{
    public function execute($task)
    {
        $model = $this->getModel();
        $model->exportToExcel();
        jexit();
    }

    public function getModel($name = 'Operations', $prefix = 'PpModel', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }
}
