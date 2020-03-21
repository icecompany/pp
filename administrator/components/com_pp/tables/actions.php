<?php
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;

class TablePpActions extends Table
{
    var $id = null;
    var $title = null;
    var $ordering = null;
	public function __construct(JDatabaseDriver $db)
	{
		parent::__construct('#__mkv_pp_actions', 'id', $db);
	}
}