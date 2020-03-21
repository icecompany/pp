<?php
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;

class TablePpTask_types extends Table
{
    var $id = null;
    var $title = null;
    var $ordering = null;
	public function __construct(JDatabaseDriver $db)
	{
		parent::__construct('#__mkv_pp_task_types', 'id', $db);
	}
}