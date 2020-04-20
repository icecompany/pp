<?php
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;

class TablePpTasks extends Table
{
    var $id = null;
    var $projectID = null;
    var $sectionID = null;
    var $typeID = null;
    var $version_add = null;
    var $managerID = null;
    var $directorID = null;
    var $contractorID = null;
    var $date_start = null;
    var $date_end = null;
    var $date_close = null;
    var $task = null;
    var $result = null;

	public function __construct(JDatabaseDriver $db)
	{
		parent::__construct('#__mkv_pp_tasks', 'id', $db);
	}

	public function store($updateNulls = true)
    {
        return parent::store($updateNulls);
    }
}