<?php
defined('_JEXEC') or die;
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldSection extends JFormFieldList
{
    protected $type = 'Section';
    protected $loadExternally = 0;

    protected function getOptions()
    {
        $db =& JFactory::getDbo();
        $query = $db->getQuery(true);

        $userID = JFactory::getUser()->id;
        $canDo = PpHelper::canDo('core.sections.all');

        $query
            ->select("s.id, s.title")
            ->from("`#__mkv_pp_sections` s")
            ->where("s.parentID is null")
            ->order("s.ordering");
        if (!$canDo) $query->where("s.managerID = {$userID}");

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