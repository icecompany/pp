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
        }
        else {
            $query->where("o.taskID = {$this->_db->q($this->taskID)}");
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
            $url = JRoute::_("index.php?option={$this->option}&amp;task=operation.edit&amp;id={$item->id}&amp;return={$return}");
            $arr['edit_link'] = JHtml::link($url, $item->task);
            $result['items'][] = $arr;
        }
        return $result;
    }

    protected function populateState($ordering = 'o.date_operation', $direction = 'desc')
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
        parent::populateState($ordering, $direction);
        PpHelper::check_refresh();
    }

    protected function getStoreId($id = '')
    {
        $id .= ':' . $this->getState('filter.search');
        return parent::getStoreId($id);
    }

    private $export, $taskID;
}
