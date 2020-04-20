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
                'date_2',
                'status',
                'director',
                'section',
                'parent',
                'version',
            );
        }
        parent::__construct($config);
        $input = JFactory::getApplication()->input;
        $this->taskID = (!empty($config['taskID'])) ? $config['taskID'] : 0;
        $this->versionID = $input->getInt('versionID', 0);

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
            ->select("if(o.date_operation < current_date and o.date_close is null, -2, if(o.date_operation < current_date and o.date_close is not null, 3, if(o.date_operation >= current_date, if(o.date_close is not null, 3, if(week(o.date_operation) > week(curdate()), 2, 1)),0))) as status")
            ->select("o.checked_out_time, o.checked_out, u.name as block")
            ->select("v.version")
            ->select("t.task as task_title")
            ->select("s1.title as section")
            ->select("s2.title as parent")
            ->from("#__mkv_pp_operations o")
            ->leftJoin("#__mkv_pp_tasks t on t.id = o.taskID")
            ->leftJoin("#__mkv_pp_sections s1 on s1.id = t.sectionID")
            ->leftJoin("#__mkv_pp_sections s2 on s2.id = s1.parentID")
            ->leftJoin("#__users u on u.id = o.checked_out")
            ->leftJoin("#__mkv_pp_versions v on v.id = t.version_add");

        if ($this->taskID === 0 && $this->versionID === 0) {
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
            if ($this->taskID > 0) {
                $query->where("o.taskID = {$this->_db->q($this->taskID)}");
            }
            if ($this->versionID > 0) {
                $query->where("t.version_add = {$this->_db->q($this->versionID)}");
            }
        }
        $date = $this->getState('filter.date');
        $date_2 = $this->getState('filter.date_2');
        if (!empty($date) && $date !== '0000-00-00 00:00:00') {
            $date = JDate::getInstance($date)->toSql();
            if (!empty($date_2) && $date_2 !== '0000-00-00 00:00:00') {
                $date_2 = JDate::getInstance($date_2)->toSql();
                $query->where("o.date_operation between {$this->_db->q($date)} and {$this->_db->q($date_2)}");
            }
            else {
                $query->where("o.date_operation = {$this->_db->q($date)}");
            }
        }

        $query->order($this->_db->escape($orderCol . ' ' . $orderDirn));
        $this->setState('list.limit', $limit);

        return $query;
    }

    public function getItems()
    {
        $items = parent::getItems();
        $result = [];
        $return = PpHelper::getReturnUrl();
        foreach ($items as $i => $item) {
            $arr = [];
            $arr['id'] = $item->id;
            $date_operation = JDate::getInstance($item->date_operation);
            $arr['date_operation'] = $date_operation->format("d.m.Y");
            $arr['date_close'] = (!empty($item->date_close)) ? JDate::getInstance($item->date_close)->format("d.m.Y") : '';
            $arr['task'] = $item->task;
            $arr['result'] = $item->result;
            $arr['task_title'] = $item->task_title;
            $arr['section'] = $item->section;
            $arr['parent'] = $item->parent;
            $manager = explode(" ", $item->manager);
            $director = explode(" ", $item->director);
            $arr['director'] = $director[0];
            $arr['manager'] = $manager[0];
            $color = PpHelper::getTaskColor($item->status);
            $arr['color'] = $color;
            $arr['status_code'] = $item->status;
            $arr['status'] = "<span style='color:{$color}'>".JText::sprintf("COM_PP_OPERATION_STATUS_{$item->status}")."</span>";
            $arr['status_export'] = JText::sprintf("COM_PP_OPERATION_STATUS_{$item->status}");
            $url = JRoute::_("index.php?option={$this->option}&amp;task=operation.edit&amp;id={$item->id}&amp;return={$return}");
            $arr['edit_link'] = JHtml::link($url, $item->task);
            $canCheckin = ($item->checked_out == 0 || $item->checked_out == JFactory::getUser()->id || ($item->checked_out != JFactory::getUser()->id && PpHelper::canDo('core.tasks.checked_out')));
            if (!$canCheckin) {
                $arr['edit_link'] = JHtml::_('jgrid.checkedout', $i, $item->block, $item->checked_out_time, 'operations.', $canCheckin);
                $arr['edit_link'] .= " " . $item->task;
            }
            $result['items'][] = $arr;
        }
        return $result;
    }

    public function exportToExcel()
    {
        $items = $this->getItems();
        $heads = $this->getColumnHeads();
        JLoader::register('PHPExcel', JPATH_LIBRARIES . '/PHPExcel.php');
        $xls = new PHPExcel();
        $xls->setActiveSheetIndex(0);
        $sheet = $xls->getActiveSheet();
        //Ширина столбцов
        $width = ["A" => 14, "B" => 11, "C" => 78, "D" => 64, "E" => 25, "F" => 25, "G" => 15, "H" => 15];
        foreach ($width as $col => $value) {
            $sheet->getColumnDimension($col)->setWidth($value);
        }
        foreach ($heads as $column => $data) {
            $sheet->setCellValue($column, $data);
            $sheet->getStyle($column)->getFont()->setBold(true);
            $sheet->getStyle($column)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }
        foreach ($items['items'] as $i => $item) {
            $j = $i + 2;
            $sheet->setCellValue("A{$j}", $item['status_export']);
            $sheet->getStyle("A{$j}")->getFont()->getColor()->setRGB(str_ireplace("#", "", $item['color']));
            $sheet->setCellValue("B{$j}", $item['date_operation']);
            $sheet->setCellValue("C{$j}", $item['task']);
            $sheet->setCellValue("D{$j}", $item['result']);
            $sheet->setCellValue("E{$j}", $item['section']);
            $sheet->setCellValue("F{$j}", $item['parent']);
            $sheet->setCellValue("G{$j}", $item['manager']);
            $sheet->setCellValue("H{$j}", $item['director']);
        }
        $filename = sprintf("Operations.xls");
        header("Expires: Mon, 1 Apr 1974 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: public");
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename={$filename}");
        $objWriter = PHPExcel_IOFactory::createWriter($xls, 'Excel5');
        $objWriter->save('php://output');
        jexit();
    }

    public function getParentTask()
    {
        $taskID = JFactory::getApplication()->input->getInt('taskID', 0);
        if ($taskID > 0) {
            $table = parent::getTable('Tasks', 'TablePp');
            $table->load($taskID);
            return $table;
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
        $date_2 = $this->getUserStateFromRequest($this->context . '.filter.date_2', 'filter_date_2');
        $this->setState('filter.date_2', $date_2);
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
        $id .= ':' . $this->getState('filter.date_2');
        $id .= ':' . $this->getState('filter.status');
        return parent::getStoreId($id);
    }

    /**
     *Возвращает заголовки столбцов для экспорта
     * @return array
     *
     * @since version 1.2.5
     */
    private function getColumnHeads(): array
    {
        $arr = [];
        $arr["A1"] = JText::sprintf('COM_PP_HEAD_OPERATIONS_STATUS');
        $arr["B1"] = JText::sprintf('COM_PP_HEAD_OPERATIONS_DATE_OPERATION');
        $arr["C1"] = JText::sprintf('COM_PP_HEAD_OPERATIONS_TASK');
        $arr["D1"] = JText::sprintf('COM_PP_HEAD_OPERATIONS_RESULT');
        $arr["E1"] = JText::sprintf('COM_PP_HEAD_TASKS_SECTION');
        $arr["F1"] = JText::sprintf('COM_PP_HEAD_TASKS_PARENT');
        $arr["G1"] = JText::sprintf('COM_PP_HEAD_TASKS_MANAGER');
        $arr["H1"] = JText::sprintf('COM_PP_HEAD_TASKS_DIRECTOR');
        return $arr;
    }

    private $export, $taskID, $versionID;
}
