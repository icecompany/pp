<?php
use Joomla\CMS\MVC\Model\ListModel;

defined('_JEXEC') or die;

class PpModelTasks extends ListModel
{
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                't.id',
                'search',
                'director',
                'manager',
                'object',
                'section',
                'parent',
                'type',
                't.date_start', 'date_end',
                't.date_end', 'date_start',
                't.date_close', 'date_close',
                't.status', 'status',
            );
        }
        parent::__construct($config);
        $input = JFactory::getApplication()->input;
        $this->export = ($input->getString('format', 'html') === 'html') ? false : true;
    }

    protected function _getListQuery()
    {
        $query = $this->_db->getQuery(true);

        /* Сортировка */
        $orderCol = $this->state->get('list.ordering');
        $orderDirn = $this->state->get('list.direction');

        //Ограничение длины списка
        $limit = (!$this->export) ? $this->getState('list.limit') : 0;

        $query
            ->select("t.id, t.date_start, t.date_end, t.date_close, t.status, t.task, t.result")
            ->select("tt.title as type")
            ->select("s.title as section")
            ->select("s1.title as parent")
            ->select("o.title as object")
            ->select("u1.name as manager")
            ->select("u2.name as director")
            ->from("#__mkv_pp_plan t")
            ->leftJoin("#__mkv_pp_task_types tt on tt.id = t.typeID")
            ->leftJoin("#__mkv_pp_sections s on s.id = t.sectionID")
            ->leftJoin("#__mkv_pp_sections s1 on s1.id = s.parentID")
            ->leftJoin("#__mkv_pp_objects o on o.id = t.objectID")
            ->leftJoin("#__users u1 on u1.id = t.managerID")
            ->leftJoin("#__users u2 on u2.id = t.directorID");
        $search = (!$this->export) ? $this->getState('filter.search') : JFactory::getApplication()->input->getString('search', '');
        if (!empty($search)) {
            if (stripos($search, 'id:') !== false) { //Поиск по ID
                $id = explode(':', $search);
                $id = $id[1];
                if (is_numeric($id)) {
                    $query->where("t.id = {$this->_db->q($id)}");
                }
            }
            else {
                $text = $this->_db->q("%{$search}%");
                $query->where("(t.task like {$text} or t.result like {$text})");
            }
        }

        $query->order($this->_db->escape($orderCol . ' ' . $orderDirn));
        $this->setState('list.limit', $limit);

        return $query;
    }

    public function getItems()
    {
        $items = parent::getItems();
        $result = array();
        foreach ($items as $item) {
            $arr = ['items' => []];
            $arr['id'] = $item->id;
            $arr['task'] = $item->task;
            $arr['result'] = $item->result;
            $arr['type'] = $item->type;
            $arr['section'] = $item->section;
            $arr['parent'] = $item->parent;
            $arr['object'] = $item->object;
            $arr['director'] = $item->director;
            $arr['manager'] = $item->manager;
            $date_start = JDate::getInstance($item->date_start);
            $date_end = JDate::getInstance($item->date_end);
            $arr['date_start'] = $date_start->format("d.m.Y");
            $arr['date_end'] = $date_end->format("d.m.Y");
            $arr['date_close'] = (!empty($item->date_close)) ? JDate::getInstance($item->date_close)->format("d.m.Y") : '';
            $url = JRoute::_("index.php?option={$this->option}&amp;task=task.edit&amp;id={$item->id}");
            $arr['edit_link'] = JHtml::link($url, JText::sprintf('JTOOLBAR_EDIT'));
            $result['items'][] = $arr;
        }
        return $result;
    }

    protected function populateState($ordering = 't.id', $direction = 'asc')
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
        $manager = $this->getUserStateFromRequest($this->context . '.filter.manager', 'filter_manager');
        $this->setState('filter.manager', $manager);
        $type = $this->getUserStateFromRequest($this->context . '.filter.type', 'filter_type');
        $this->setState('filter.type', $type);
        $section = $this->getUserStateFromRequest($this->context . '.filter.section', 'filter_section');
        $this->setState('filter.section', $section);
        $parent = $this->getUserStateFromRequest($this->context . '.filter.parent', 'filter_parent');
        $this->setState('filter.parent', $parent);
        $object = $this->getUserStateFromRequest($this->context . '.filter.object', 'filter_object');
        $this->setState('filter.object', $object);
        $date_start = $this->getUserStateFromRequest($this->context . '.filter.date_start', 'filter_date_start');
        $this->setState('filter.date_start', $date_start);
        $date_end = $this->getUserStateFromRequest($this->context . '.filter.date_end', 'filter_date_end');
        $this->setState('filter.date_end', $date_end);
        $date_close = $this->getUserStateFromRequest($this->context . '.filter.date_close', 'filter_date_close');
        $this->setState('filter.date_close', $date_close);
        $status = $this->getUserStateFromRequest($this->context . '.filter.status', 'filter_status');
        $this->setState('filter.status', $status);
        parent::populateState($ordering, $direction);
        PpHelper::check_refresh();
    }

    protected function getStoreId($id = '')
    {
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.manager');
        $id .= ':' . $this->getState('filter.type');
        $id .= ':' . $this->getState('filter.section');
        $id .= ':' . $this->getState('filter.parent');
        $id .= ':' . $this->getState('filter.object');
        $id .= ':' . $this->getState('filter.date_start');
        $id .= ':' . $this->getState('filter.date_end');
        $id .= ':' . $this->getState('filter.date_close');
        $id .= ':' . $this->getState('filter.status');
        return parent::getStoreId($id);
    }

    private $export;
}