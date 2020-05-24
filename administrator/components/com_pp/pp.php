<?php
/**
 * @package    pp
 *
 * @author     anton@nazvezde.ru <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;

defined('_JEXEC') or die;

// Access check.
if (!Factory::getUser()->authorise('core.manage', 'com_pp'))
{
	throw new InvalidArgumentException(Text::_('JERROR_ALERTNOAUTHOR'), 404);
}

//Require the language from com_mkv
JFactory::getLanguage()->load('com_mkv', JPATH_ADMINISTRATOR . "/components/com_mkv", 'ru-RU', true);

// Require the helper
JFactory::getLanguage()->load('com_mkv', JPATH_ADMINISTRATOR . "/components/com_mkv/language", 'ru-RU', false);
require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/pp.php';

// Execute the task
$controller = BaseController::getInstance('pp');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
