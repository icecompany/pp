<?php
use Joomla\CMS\MVC\Model\ListModel;

defined('_JEXEC') or die;

class PpModelSections extends ListModel
{
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                's.id',
                's.title',
                's.ordering',
                'parent',
                'search',
                'manager',
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
            ->select("s.id, s.title, s.ordering")
            ->select("s1.title as parent")
            ->select("u.name as manager")
            ->from("#__mkv_pp_sections s")
            ->leftJoin("#__users u on u.id = s.managerID")
            ->leftJoin("#__mkv_pp_sections s1 on s1.id = s.parentID");
        $search = (!$this->export) ? $this->getState('filter.search') : JFactory::getApplication()->input->getString('search', '');
        if (!empty($search)) {
            if (stripos($search, 'id:') !== false) { //Поиск по ID
                $id = explode(':', $search);
                $id = $id[1];
                if (is_numeric($id)) {
                    $query->where("s.id = {$this->_db->q($id)}");
                }
            }
            else {
                $text = $this->_db->q("%{$search}%");
                $query->where("(s.title like {$text})");
            }
        }
        $manager = $this->getState('filter.manager');
        if (is_numeric($manager)) {
            $query->where("s.managerID = {$this->_db->q($manager)}");
        }
        $parent = $this->getState('filter.parent');
        if (is_numeric($parent)) {
            $query->where("s.parentID = {$this->_db->q($parent)}");
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
            $arr['title'] = $item->title;
            $arr['ordering'] = $item->ordering;
            $arr['manager'] = $item->manager;
            $arr['parent'] = $item->parent;
            $url = JRoute::_("index.php?option={$this->option}&amp;task=section.edit&amp;id={$item->id}");
            $arr['edit_link'] = JHtml::link($url, $item->title);
            $result['items'][] = $arr;
        }
        return $result;
    }

    protected function populateState($ordering = 's.ordering', $direction = 'asc')
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
        $manager = $this->getUserStateFromRequest($this->context . '.filter.manager', 'filter_manager');
        $this->setState('filter.manager', $manager);
        $parent = $this->getUserStateFromRequest($this->context . '.filter.parent', 'filter_parent');
        $this->setState('filter.parent', $parent);
        parent::populateState($ordering, $direction);
        PpHelper::check_refresh();
    }

    protected function getStoreId($id = '')
    {
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.manager');
        $id .= ':' . $this->getState('filter.parent');
        return parent::getStoreId($id);
    }

    private $export;
}