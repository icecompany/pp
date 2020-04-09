<?php
defined('_JEXEC') or die;
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('groupedlist');

class JFormFieldSubsection extends JFormFieldGroupedList
{
    protected $type = 'Subsection';
    protected $loadExternally = 0;

    protected function getGroups()
    {
        $db =& JFactory::getDbo();
        $query = $db->getQuery(true);

        $userID = JFactory::getUser()->id;
        $canDo = PpHelper::canDo('core.sections.all');

        $query
            ->select("s.id, s.title, s.managerID")
            ->select("s1.title as parent")
            ->from("`#__mkv_pp_sections` s")
            ->leftJoin("#__mkv_pp_sections s1 on s1.id = s.parentID")
            ->order("s.ordering");
        if (!$canDo) $query->where("s.managerID = {$userID}");
        $result = $db->setQuery($query)->loadObjectList();

        $options = array();

        foreach ($result as $item) {
            if (!isset($options[$item->parent]) && empty($item->parent) && !empty($item->parent)) $options[$item->parent] = [];
            if (!empty($item->parent)) {
                $options[$item->parent][] = JHtml::_('select.option', $item->id, $item->title);
            }
        }

        if (!$this->loadExternally) {
            $options = array_merge(parent::getGroups(), $options);
        }

        return $options;
    }
}