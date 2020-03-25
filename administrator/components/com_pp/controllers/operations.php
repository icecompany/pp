<?php
use Joomla\CMS\MVC\Controller\AdminController;

defined('_JEXEC') or die;

class PpControllerOperations extends AdminController
{
    public function getModel($name = 'Operation', $prefix = 'PpModel', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }
}
