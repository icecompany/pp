<?php
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;

class TablePpOperations extends Table
{
    var $id = null;
    var $taskID = null;
    var $date_operation = null;
    var $managerID = null;
    var $directorID = null;
    var $date_close = null;
    var $task = null;
    var $result = null;
    var $checked_out = null;
    var $checked_out_time = null;

	public function __construct(JDatabaseDriver $db)
	{
		parent::__construct('#__mkv_pp_operations', 'id', $db);
	}
}