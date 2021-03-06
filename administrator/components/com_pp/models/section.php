<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\AdminModel;

class PpModelSection extends AdminModel {

    public function getItem($pk = null)
    {
        $item = parent::getItem($pk);
        if ($item->id === null) {
            $item->managerID = JFactory::getUser()->id;
            $uri = JUri::getInstance($_SERVER['HTTP_REFERER']);
            $section = $uri->getVar('filter_section', 0);
            if ($section > 0) {
                $item->parentID = $section;
                $table = $this->getTable();
                $table->load($section);
                $item->managerID = $table->managerID;
            }
        }
        return $item;
    }

    public function save($data)
    {
        return parent::save($data);
    }

    public function getTable($name = 'Sections', $prefix = 'TablePp', $options = array())
    {
        return JTable::getInstance($name, $prefix, $options);
    }

    public function getForm($data = array(), $loadData = true)
    {
        $form = $this->loadForm(
            $this->option.'.section', 'section', array('control' => 'jform', 'load_data' => $loadData)
        );
        if (empty($form))
        {
            return false;
        }

        return $form;
    }

    protected function loadFormData()
    {
        $data = JFactory::getApplication()->getUserState($this->option.'.edit.section.data', array());
        if (empty($data))
        {
            $data = $this->getItem();
        }

        return $data;
    }

    protected function prepareTable($table)
    {
        $all = get_class_vars($table);
        unset($all['_errors']);
        $nulls = ['parentID']; //Поля, которые NULL
        foreach ($all as $field => $v) {
            if (empty($field)) continue;
            if (in_array($field, $nulls)) {
                if (!strlen($table->$field)) {
                    $table->$field = NULL;
                    continue;
                }
            }
            if (!empty($field)) $table->$field = trim($table->$field);
        }

        parent::prepareTable($table);
    }

    protected function canEditState($record)
    {
        $user = JFactory::getUser();

        if (!empty($record->id))
        {
            return $user->authorise('core.edit.state', $this->option . '.section.' . (int) $record->id);
        }
        else
        {
            return parent::canEditState($record);
        }
    }

    public function getScript()
    {
        return 'administrator/components/' . $this->option . '/models/forms/section.js';
    }
}