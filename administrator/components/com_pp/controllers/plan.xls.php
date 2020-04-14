<?php
use Joomla\CMS\MVC\Controller\AdminController;

defined('_JEXEC') or die;

class PpControllerPlan extends AdminController
{
    public function execute($task)
    {
        $model = $this->getModel();
        $model->exportToExcel();
        jexit();
    }

    public function getModel($name = 'Plan', $prefix = 'PpModel', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }
}
