<?php
defined('_JEXEC') or die;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\MVC\Controller\BaseController;

class PpControllerSection extends BaseController
{
    public function getModel($name = 'Section', $prefix = 'PpModel', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }

    public function execute($task)
    {
        $item = $this->getModel()->getItem();
        echo new JsonResponse($item);
    }
}