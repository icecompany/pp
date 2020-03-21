<?php
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;

class TablePpPlan extends Table
{
    var $id = null;
    var $projectID = null;
    var $actionID = null;
    var $sectionID = null;
    var $typeID = null;
    var $managerID = null;
    var $directorID = null;
    var $contractorID = null;
    var $dae_start = null;
    var $dae_end = null;
    var $task = null;
    var $result = null;
    var $status = null;

	public function __construct(JDatabaseDriver $db)
	{
		parent::__construct('#__mkv_pp_plan', 'id', $db);
	}
}