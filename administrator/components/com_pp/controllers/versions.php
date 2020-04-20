<?php
use Joomla\CMS\MVC\Controller\AdminController;

defined('_JEXEC') or die;

class PpControllerVersions extends AdminController
{
    public function getModel($name = 'Version', $prefix = 'PpModel', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }
}
