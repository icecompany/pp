<?php
use Joomla\CMS\MVC\Controller\AdminController;

defined('_JEXEC') or die;

class PpControllerObjects extends AdminController
{
    public function getModel($name = 'Object', $prefix = 'PpModel', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }
}
