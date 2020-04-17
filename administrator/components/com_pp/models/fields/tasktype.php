<?php
defined('_JEXEC') or die;
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldTasktype extends JFormFieldList
{
    protected $type = 'Tasktype';
    protected $loadExternally = 0;

    protected function getOptions()
    {
        $groups = implode(", ", JFactory::getUser()->groups);

        $db =& JFactory::getDbo();
        $query = $db->getQuery(true);
        $query
            ->select("t.id, t.title")
            ->from("`#__mkv_pp_task_types` t")
            ->order("t.ordering")
            ->where("t.groupID in ({$groups})");

        $result = $db->setQuery($query)->loadObjectList();

        $options = array();

        foreach ($result as $item) {
            $options[] = JHtml::_('select.option', $item->id, $item->title);
        }

        if (!$this->loadExternally) {
            $options = array_merge(parent::getOptions(), $options);
        }

        return $options;
    }

    public function getOptionsExternally()
    {
        $this->loadExternally = 1;
        return $this->getOptions();
    }
}