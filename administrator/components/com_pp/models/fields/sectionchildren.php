<?php
defined('_JEXEC') or die;
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('groupedlist');

class JFormFieldSectionchildren extends JFormFieldGroupedList
{
    protected $type = 'Sectionchildren';
    protected $loadExternally = 0;

    protected function getGroups()
    {
        $db =& JFactory::getDbo();
        $query = $db->getQuery(true);

        $userID = JFactory::getUser()->id;

        $query
            ->select("s.id, s.title, s.managerID")
            ->select("s1.title as parent")
            ->from("`#__mkv_pp_sections` s")
            ->leftJoin("#__mkv_pp_sections s1 on s1.id = s.parentID")
            ->where("s.managerID = {$userID}")
            ->order("s.ordering");
        $result = $db->setQuery($query)->loadObjectList();

        $options = array();

        foreach ($result as $item) {
            if (!isset($options[$item->parent]) && empty($item->parent) && !empty($item->parent)) $options[$item->parent] = [];
            if (!empty($item->parent)) {
                $arr = array('data-director' => $item->managerID);
                $params = array('option.attr' => 'optionattr', 'attr' => $arr);
                $options[$item->parent][] = JHtml::_('select.option', $item->id, $item->title, $params);
            }
        }

        if (!$this->loadExternally) {
            $options = array_merge(parent::getGroups(), $options);
        }

        return $options;
    }
}