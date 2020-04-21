<?php
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

defined('_JEXEC') or die;

require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/pp.php';

$controller = BaseController::getInstance('pp');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
