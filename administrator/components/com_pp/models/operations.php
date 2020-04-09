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
                'o.title',
                'o.ordering',
                'search',
                'manager',
                'date',
                'status',
                'director',
            );
        }
        parent::__construct($config);
        $input = JFactory::getApplication()->input;
        $this->taskID = (!empty($config['taskID'])) ? $config['taskID'] : 0;
        $this->export = ($input->getString('format', 'html') === 'html') ? false : true;
    }

    protected function _getListQuery()
    {
        $query = $this->_db->getQuery(true);

        /* Сортировка */
        $orderCol = $this->state->get('list.ordering');
        $orderDirn = $this->state->get('list.direction');

        //Ограничение длины списка
        $limit = (!$this->export && $this->taskID === 0) ? $this->getState('list.limit') : 0;

        $query
            ->select("o.id, o.date_operation, o.task, o.result")
            ->select("if(o.date_operation < current_date and o.date_close is null, -2, if(o.date_operation < current_date and o.date_close is not null, 3, if(o.date_operation >= current_date, if(o.date_close is not null, 3, 1),0))) as status")
            ->from("#__mkv_pp_operations o");

        if ($this->taskID === 0) {
            $search = (!$this->export) ? $this->getState('filter.search') : JFactory::getApplication()->input->getString('search', '');
            if (!empty($search)) {
                if (stripos($search, 'id:') !== false) { //Поиск по ID
                    $id = explode(':', $search);
                    $id = $id[1];
                    if (is_numeric($id)) {
                        $query->where("o.id = {$this->_db->q($id)}");
                    }
                } else {
                    $text = $this->_db->q("%{$search}%");
                    $query->where("(o.task like {$text} or o.result like {$text})");
                }
            }
            $query
                ->select("o.date_close, u1.name as manager, u2.name as director")
                ->leftJoin("#__users u1 on u1.id = o.managerID")
                ->leftJoin("#__users u2 on u2.id = o.directorID");
            $taskID = JFactory::getApplication()->input->getInt('taskID', 0);
            if ($taskID > 0) {
                $query->where("o.taskID = {$this->_db->q($taskID)}");
            }
            $manager = $this->getState('filter.manager');
            if (is_numeric($manager)) {
                $query->where("o.managerID = {$this->_db->q($manager)}");
            }
            $director = $this->getState('filter.director');
            if (is_numeric($director)) {
                $query->where("o.directorID = {$this->_db->q($director)}");
            }
            $status = $this->getState('filter.status');
            if (is_array($status) && !empty($status)) {
                $status = implode(", ", $status);
                $query->having("status in ({$this->_db->q($status)})");
            }
        }
        else {
            $query->where("o.taskID = {$this->_db->q($this->taskID)}");
        }
        $date = $this->getState('filter.date');
        if (!empty($date)) {
            $date = JDate::getInstance($date)->toSql();
            $query->where("o.date_operation = {$this->_db->q($date)}");
        }

        $query->order($this->_db->escape($orderCol . ' ' . $orderDirn));
        $this->setState('list.limit', $limit);

        return $query;
    }

    public function getItems()
    {
        $items = parent::getItems();
        $result = array();
        $return = PpHelper::getReturnUrl();
        foreach ($items as $item) {
            $arr = ['items' => []];
            $arr['id'] = $item->id;
            $date_operation = JDate::getInstance($item->date_operation);
            $arr['date_operation'] = $date_operation->format("d.m.Y");
            $arr['date_close'] = (!empty($item->date_close)) ? JDate::getInstance($item->date_close)->format("d.m.Y") : '';
            $arr['task'] = $item->task;
            $arr['result'] = $item->result;
            $arr['manager'] = $item->manager;
            $arr['director'] = $item->director;
            $color = PpHelper::getTaskColor($item->status);
            $arr['status'] = "<span style='color:{$color}'>".JText::sprintf("COM_PP_OPERATION_STATUS_{$item->status}")."</span>";
            $url = JRoute::_("index.php?option={$this->option}&amp;task=operation.edit&amp;id={$item->id}&amp;return={$return}");
            $arr['edit_link'] = JHtml::link($url, $item->task);
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
            return $table->task ?? '';
        }
        else return '';
    }

    public function getTaskID()
    {
        return $taskID = JFactory::getApplication()->input->getInt('taskID', 0);
    }

    protected function populateState($ordering = 'status', $direction = 'asc')
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
        $manager = $this->getUserStateFromRequest($this->context . '.filter.manager', 'filter_manager');
        $this->setState('filter.manager', $manager);
        $director = $this->getUserStateFromRequest($this->context . '.filter.director', 'filter_director');
        $this->setState('filter.director', $director);
        $date = $this->getUserStateFromRequest($this->context . '.filter.date', 'filter_date');
        $this->setState('filter.date', $date);
        $status = $this->getUserStateFromRequest($this->context . '.filter.status', 'filter_status');
        $this->setState('filter.status', $status);

        parent::populateState($ordering, $direction);
        PpHelper::check_refresh();
    }

    protected function getStoreId($id = '')
    {
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.manager');
        $id .= ':' . $this->getState('filter.director');
        $id .= ':' . $this->getState('filter.date');
        $id .= ':' . $this->getState('filter.status');
        return parent::getStoreId($id);
    }

    private $export, $taskID;
}
