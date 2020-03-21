<?php
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;

class TablePpObjects extends Table
{
    var $id = null;
    var $title = null;
    var $ordering = null;
	public function __construct(JDatabaseDriver $db)
	{
		parent::__construct('#__mkv_pp_objects', 'id', $db);
	}
}