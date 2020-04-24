<?php
use Joomla\CMS\MVC\Model\ListModel;

defined('_JEXEC') or die;

class PpModelVersions extends ListModel
{
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'v.id',
                'v.version',
                'v.dat desc, v.id',
                'search',
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
            ->select("v.*")
            ->from("#__mkv_pp_versions v");
        $search = (!$this->export) ? $this->getState('filter.search') : JFactory::getApplication()->input->getString('search', '');
        if (!empty($search)) {
            if (stripos($search, 'id:') !== false) { //Поиск по ID
                $id = explode(':', $search);
                $id = $id[1];
                if (is_numeric($id)) {
                    $query->where("v.id = {$this->_db->q($id)}");
                }
            }
            else {
                $text = $this->_db->q("%{$search}%");
                $query->where("(v.version like {$text})");
            }
        }

        $query->order($this->_db->escape($orderCol . ' ' . $orderDirn));
        $this->setState('list.limit', $limit);

        return $query;
    }

    public function getItems()
    {
        $items = parent::getItems();
        $result = ['items' => []];
        foreach ($items as $item) {
            $arr = [];
            $arr['id'] = $item->id;
            $arr['version'] = $item->version;
            $arr['dat'] = JDate::getInstance($item->dat)->format("d.m.Y");
            $url = JRoute::_("index.php?option={$this->option}&amp;task=version.edit&amp;id={$item->id}");
            $arr['edit_link'] = JHtml::link($url, $item->version);
            $result['items'][] = $arr;
        }
        return $result;
    }

    protected function populateState($ordering = 'v.dat desc, v.id', $direction = 'desc')
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

    private $export;
}
