<?php
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;

class TablePpVersions extends Table
{
    var $id = null;
    var $version = null;
    var $dat = null;
	public function __construct(JDatabaseDriver $db)
	{
		parent::__construct('#__mkv_pp_versions', 'id', $db);
	}
}