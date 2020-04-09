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
                's.ordering',
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
        $limit = 0;

        $query
            ->select("s.id, s.title, s.ordering, s.parentID")
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

        $query->order($this->_db->escape($orderCol . ' ' . $orderDirn));
        $this->setState('list.limit', $limit);

        return $query;
    }

    public function getItems()
    {
        $items = parent::getItems();
        $result = ['items' => [], 'parents' => [], 'titles' => []];
        foreach ($items as $item) {
            $arr = [];
            $arr['id'] = $item->id;
            $arr['title'] = $item->title;
            if (!empty($item->parent)) $arr['title'] = "- {$arr['title']}";
            $arr['ordering'] = $item->ordering;
            $arr['manager'] = $item->manager;
            $arr['parentID'] = $item->parentID;
            $url = JRoute::_("index.php?option={$this->option}&amp;task=section.edit&amp;id={$item->id}");
            $style = (empty($item->parent)) ? 'font-weight: bold;' : '';
            $arr['edit_link'] = JHtml::link($url, $arr['title'], ['style' => $style]);
            $url = JRoute::_("index.php?option={$this->option}&amp;view=tasks&amp;filter_section={$item->id}&amp;filter_object=&amp;filter_manager=&amp;filter_director=&amp;back=1");
            $arr['tasks_link'] = JHtml::link($url, $arr['title'], ['style' => $style]);
            $result['titles'][$item->id] = $item->title;
            if (empty($item->parentID)) {
                $result['parents'][$item->id] = $arr;
            }
            else {
                $result['items'][$item->parentID][] = $arr;
            }
        }
        return $result;
    }

    protected function populateState($ordering = 's.ordering', $direction = 'asc')
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
        $manager = $this->getUserStateFromRequest($this->context . '.filter.manager', 'filter_manager', JFactory::getUser()->id);
        $this->setState('filter.manager', $manager);
        parent::populateState($ordering, $direction);
        PpHelper::check_refresh();
    }

    protected function getStoreId($id = '')
    {
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.manager');
        return parent::getStoreId($id);
    }

    private $export;
}
