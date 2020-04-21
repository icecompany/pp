<?php

use Joomla\CMS\MVC\Model\ListModel;

defined('_JEXEC') or die;

class PpModelOperations extends ListModel
{
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'o.id',
                'o.date_operation',
                'status',
            );
        }
        parent::__construct($config);
        $input = JFactory::getApplication()->input;
        $this->taskID = $input->getInt('taskID', 0);
    }

    protected function _getListQuery()
    {
        $query = $this->_db->getQuery(true);

        /* Сортировка */
        $orderCol = $this->state->get('list.ordering');
        $orderDirn = $this->state->get('list.direction');

        //Ограничение длины списка
        $limit = 0;

        $query
            ->select("o.id, o.date_operation, o.task")
            ->select("if(o.date_operation < current_date and o.date_close is null, -2, if(o.date_operation < current_date and o.date_close is not null, 3, if(o.date_operation >= current_date, if(o.date_close is not null, 3, if(week(o.date_operation) > week(curdate()), 2, 1)),0))) as status")
            ->from("#__mkv_pp_operations o");
        if ($this->taskID > 0) {
            $query->where("o.taskID = {$this->_db->q($this->taskID)}");
        }

        $query->order($this->_db->escape($orderCol . ' ' . $orderDirn));
        $this->setState('list.limit', $limit);

        return $query;
    }

    public function getItems()
    {
        $items = parent::getItems();
        $result = [];
        foreach ($items as $i => $item) {
            $arr = [];
            $arr['id'] = $item->id;
            $date_operation = JDate::getInstance($item->date_operation);
            $arr['date_operation'] = $date_operation->format("d.m.Y");
            $arr['task'] = $item->task;
            $arr['task_title'] = $item->task_title;
            $color = PpHelper::getTaskColor($item->status);
            $arr['status'] = "<span style='color:{$color}'>" . JText::sprintf("COM_PP_OPERATION_STATUS_{$item->status}") . "</span>";
            $url = JRoute::_("index.php?option={$this->option}&amp;view=operation&amp;id={$item->id}");
            $arr['show_link'] = JHtml::link($url, $item->task);
            $result['items'][] = $arr;
        }
        return $result;
    }

    public function getParentTask()
    {
        $taskID = JFactory::getApplication()->input->getInt('taskID', 0);
        if ($taskID > 0) {
            $table = parent::getTable('Tasks', 'TablePp');
            $table->load($taskID);
            return $table;
        } else return '';
    }

    public function getVersion()
    {
        $task = $this->getParentTask();
        $table = parent::getTable('Versions', 'TablePp');
        $table->load($task->version_add);
        return $table;
    }

    public function getTaskID()
    {
        return $this->taskID;
    }

    protected function populateState($ordering = 'o.date_operation', $direction = 'asc')
    {
        parent::populateState($ordering, $direction);
        PpHelper::check_refresh();
    }

    protected function getStoreId($id = '')
    {
        return parent::getStoreId($id);
    }

    private $taskID;
}
