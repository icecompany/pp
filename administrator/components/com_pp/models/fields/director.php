<?php
defined('_JEXEC') or die;
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldDirector extends JFormFieldList
{
    protected $type = 'Director';
    protected $loadExternally = 0;

    protected function getOptions()
    {
        $users = $this->getUserIDs();
        if (empty($users)) return [];
        $ids = implode(", ", $users);
        $db =& JFactory::getDbo();
        $query = $db->getQuery(true);
        $query
            ->select("u.id, u.name")
            ->from("`#__users` u")
            ->where("u.id in ({$ids})")
            ->order("u.name");
        $result = $db->setQuery($query)->loadObjectList();

        $options = array();

        foreach ($result as $item) {
            $options[] = JHtml::_('select.option', $item->id, $item->name);
        }

        if (!$this->loadExternally) {
            $options = array_merge(parent::getOptions(), $options);
        }

        return $options;
    }

    public function getUserIDs()
    {
        $config = JComponentHelper::getParams('com_pp');
        $groupID = $config->get('groups_directors', 1);
        $db =& JFactory::getDbo();
        $query = $db->getQuery(true);
        $query
            ->select("user_id")
            ->from("#__user_usergroup_map")
            ->where("group_id = {$groupID}");
        return $db->setQuery($query)->loadColumn() ?? [];
    }

    public function getOptionsExternally()
    {
        $this->loadExternally = 1;
        return $this->getOptions();
    }
}