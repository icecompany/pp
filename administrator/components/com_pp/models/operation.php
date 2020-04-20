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
            if ((int) JFactory::getUser()->id === 377) $item->managerID = 439;
        }
        $item->parent_title = $task->task;
        return $item;
    }

    public function save($data)
    {
        $data['date_operation'] = JDate::getInstance($data['date_operation'])->toSql();
        if (!$this->checkDate($data['taskID'], $data['date_operation'])) {
            JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_PP_ERROR_OPERATION_DATE_NOT_RANGE'), 'error');
            return false;
        }
        if (!empty($data['result'])) $data['date_close'] = JDate::getInstance()->toSql();
        $s1 = parent::save($data);
        //Повторение задачи
        if ($data['repeat']) {
            $need = true;
            while ($need) {
                $data['date_operation'] = JDate::getInstance($data['date_operation'])->modify("+1 {$data['repeat']}")->toSql();
                if (!$this->checkDate($data['taskID'], $data['date_operation'])) {
                    $need = false;
                }
                else {
                    $data['id'] = null;
                    $table = $this->getTable();
                    $table->bind($data);
                    $table->save($data);
                }
            }
        }
        return $s1;
    }

    public function checkDate(int $taskID, string $date): bool
    {
        $table = parent::getTable('Tasks', 'TablePp');
        $table->load($taskID);
        $date = JDate::getInstance($date);
        $date_start = JDate::getInstance($table->date_start);
        $date_end = JDate::getInstance($table->date_end);
        return $date_start <= $date && $date_end >= $date;
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