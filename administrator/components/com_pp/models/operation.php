<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\AdminModel;

class PpModelOperation extends AdminModel {

    public function getItem($pk = null)
    {
        $item = parent::getItem($pk);
        $taskID = ($item->id !== null) ? $item->taskID : $this->getState('taskID');
        $task = $this->getTask($taskID);
        if ($item->id === null) {
            $item->date_operation = JDate::getInstance()->format("Y-m-d");
            $item->directorID = $task->directorID;
            $item->managerID = $task->managerID;
            $item->taskID = $taskID;
        }
        $item->parent_title = $task->task;
        return $item;
    }

    public function save($data)
    {
        $data['date_operation'] = JDate::getInstance($data['date_operation'])->toSql();
        if (!empty($data['result'])) $data['date_close'] = JDate::getInstance()->toSql();
        return parent::save($data);
    }

    public function getTask(int $taskID)
    {
        $table = $this->getTable('Tasks');
        $table->load(['id' => $taskID]);
        return $table;
    }

    public function getTable($name = 'Operations', $prefix = 'TablePp', $options = array())
    {
        return JTable::getInstance($name, $prefix, $options);
    }

    public function getForm($data = array(), $loadData = true)
    {
        $form = $this->loadForm(
            $this->option.'.operation', 'operation', array('control' => 'jform', 'load_data' => $loadData)
        );
        if (empty($form))
        {
            return false;
        }

        return $form;
    }

    protected function loadFormData()
    {
        $data = JFactory::getApplication()->getUserState($this->option.'.edit.operation.data', array());
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
        $nulls = ['date_close', 'result']; //Поля, которые NULL
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

    protected function populateState()
    {
        $taskID = JFactory::getApplication()->getUserStateFromRequest("{$this->option}.operation.taskID", 'operation.taskID', 0);
        $this->setState('taskID', $taskID);
        parent::populateState();
    }

    protected function canEditState($record)
    {
        $user = JFactory::getUser();

        if (!empty($record->id))
        {
            return $user->authorise('core.edit.state', $this->option . '.operation.' . (int) $record->id);
        }
        else
        {
            return parent::canEditState($record);
        }
    }

    public function getScript()
    {
        return 'administrator/components/' . $this->option . '/models/forms/operation.js';
    }
}