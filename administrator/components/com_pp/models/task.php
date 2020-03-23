<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\AdminModel;

class PpModelTask extends AdminModel {

    public function getItem($pk = null)
    {
        $item = parent::getItem($pk);
        if ($item->id === null) {
            $item->date_start = JDate::getInstance()->format("Y-m-d");
            $item->date_end = JDate::getInstance(time() + 86400 * 7)->format("Y-m-d");
            $item->managerID = JFactory::getUser()->id;
        }
        return $item;
    }

    public function save($data)
    {
        $data['date_start'] = JDate::getInstance($data['date_start'])->toSql();
        $data['date_end'] = JDate::getInstance($data['date_end'])->toSql();
        if (!empty($data['result'])) $data['date_close'] = JDate::getInstance()->toSql();
        return parent::save($data);
    }

    public function getTable($name = 'Plan', $prefix = 'TablePp', $options = array())
    {
        return JTable::getInstance($name, $prefix, $options);
    }

    public function getForm($data = array(), $loadData = true)
    {
        $form = $this->loadForm(
            $this->option.'.task', 'task', array('control' => 'jform', 'load_data' => $loadData)
        );
        $form->addFieldPath(JPATH_ADMINISTRATOR."/components/com_prj/models/fields");
        if (empty($form))
        {
            return false;
        }

        return $form;
    }

    protected function loadFormData()
    {
        $data = JFactory::getApplication()->getUserState($this->option.'.edit.task.data', array());
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
        $nulls = ['contractorID', 'date_close', 'result']; //Поля, которые NULL
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
            return $user->authorise('core.edit.state', $this->option . '.task.' . (int) $record->id);
        }
        else
        {
            return parent::canEditState($record);
        }
    }

    public function getScript()
    {
        return 'administrator/components/' . $this->option . '/models/forms/task.js';
    }
}