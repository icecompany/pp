<?php
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;

class TablePpSections extends Table
{
    var $id = null;
    var $parentID = null;
    var $managerID = null;
    var $title = null;
    var $ordering = null;
	public function __construct(JDatabaseDriver $db)
	{
		parent::__construct('#__mkv_pp_sections', 'id', $db);
	}
}