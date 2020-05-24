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
        $this->id = (!empty($config['id']) && is_numeric($config['id'])) ? $config['id'] : 0;
        $this->for_plan = (bool) (!empty($config['for_plan']) && $config['for_plan']);

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
        if (is_numeric($manager) && ((!$this->for_plan) || (!PpHelper::canDo('core.sections.all')))) {
            $query->where("s.managerID = {$this->_db->q($manager)}");
        }

        if ($this->id > 0) $query->where("(s.id = {$this->id})");

        $query->order($this->_db->escape($orderCol . ' ' . $orderDirn));
        $this->setState('list.limit', $limit);

        return $query;
    }

    public function getItems()
    {
        $items = parent::getItems();
        $result = ['items' => [], 'parents' => [], 'titles' => [], 'flip' => []];
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
            $params = [
                "option" => $this->option,
                "view" => "tasks",
                "filter_section" => (!empty($item->parentID)) ? '' : $item->id,
                "filter_sub_section" => (empty($item->parentID)) ? '' : $item->id,
                "filter_object" => '',
                "filter_manager" => '',
                "filter_director" => '',

            ];
            $url = JRoute::_("index.php?".http_build_query($params));
            $arr['tasks_link'] = JHtml::link($url, $arr['title'], ['style' => $style]);
            $result['titles'][$item->id] = $item->title;
            if (empty($item->parentID)) {
                $result['parents'][$item->id] = $arr;
            }
            else {
                $result['flip'][$item->id] = $item->parentID;
                $result['items'][$item->parentID][] = $arr;
            }
        }
        return $result;
    }

    protected function populateState($ordering = 's.ordering', $direction = 'ASC')
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
        $default_manager = (!PpHelper::canDo('core.sections.all') ? JFactory::getUser()->id : '');
        $manager = $this->getUserStateFromRequest($this->context . '.filter.manager', 'filter_manager', $default_manager);
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

    private $export, $id, $for_plan;
}
