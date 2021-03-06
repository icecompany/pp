<?php
use Joomla\CMS\MVC\Model\ListModel;

defined('_JEXEC') or die;

class PpModelPlan extends ListModel
{
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                't.id',
                'search',
                'director',
                'manager',
                'contractor',
                'object',
                'section',
                'sub_section',
                'parent',
                'type',
                'project',
                't.date_start', 'date_end',
                't.date_end', 'date_start',
                't.date_close', 'date_close',
                'status',
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

        $userID = JFactory::getUser()->id;

        //Ограничение длины списка
        $limit = 0;

        $query
            ->select("t.id, t.date_start, t.date_end, t.date_close, t.task, t.managerID")
            ->select("if(t.date_start < current_date and t.date_end < current_date, if (t.date_close is not null, 3, -2), if (t.date_start <= current_date and t.date_end >= current_date, if(t.date_close is not null, 3, 1), 2)) as status")
            ->select("tt.title as type")
            ->select("s.id as sectionID, s.title as section")
            ->select("s1.id as parentID, s1.title as parent")
            ->select("o.title as object")
            ->select("u1.name as manager")
            ->select("u2.name as director")
            ->select("c.title as contractor")
            ->from("#__mkv_pp_tasks t")
            ->leftJoin("#__mkv_pp_task_types tt on tt.id = t.typeID")
            ->leftJoin("#__mkv_pp_sections s on s.id = t.sectionID")
            ->leftJoin("#__mkv_pp_sections s1 on s1.id = s.parentID")
            ->leftJoin("#__mkv_pp_objects o on o.id = t.objectID")
            ->leftJoin("#__mkv_companies c on c.id = t.contractorID")
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
        $status = $this->getState('filter.status');
        if (is_array($status)) {
            $status = implode(', ', $status);
            $query->having("status in ($status)");
        }
        $project = $this->getState('filter.project');
        if (is_numeric($project)) {
            $query->where("t.projectID = {$this->_db->q($project)}");
        }
        $object = $this->getState('filter.object');
        if (is_numeric($object)) {
            $query->where("t.objectID = {$this->_db->q($object)}");
        }
        $section = $this->getState('filter.section');
        if (is_numeric($section)) {
            $query->where("s.parentID = {$this->_db->q($section)}");
        }
        $sub_section = $this->getState('filter.sub_section');
        if (is_numeric($sub_section)) {
            $query->where("s.id = {$this->_db->q($sub_section)}");
        }
        $manager = $this->getState('filter.manager');
        if (is_numeric($manager)) {
            $query->where("t.managerID = {$this->_db->q($manager)}");
        }
        $director = $this->getState('filter.director');
        if (is_numeric($director)) {
            $query->where("t.directorID = {$this->_db->q($director)}");
        }

        $query->order($this->_db->escape($orderCol . ' ' . $orderDirn));
        $this->setState('list.limit', $limit);

        if (!PpHelper::canDo('core.tasks.all')) {
            $query->where("(s.managerID = {$userID} or s1.managerID = {$userID})");
        }

        return $query;
    }

    public function getItems()
    {
        $items = parent::getItems();
        $result = ['items' => [], 'sections' => $this->getSections(), 'managers' => [], 'active' => []];
        $ids = [];
        foreach ($items as $item) {
            $arr = [];
            $ids[] = $item->id;
            $arr['id'] = $item->id;
            $arr['task'] = $item->task;
            $arr['type'] = $item->type;
            $arr['section'] = $item->section;
            $arr['parent'] = $item->parent;
            $arr['object'] = $item->object;
            $arr['contractor'] = $item->contractor;
            $manager = explode(" ", $item->manager);
            $director = explode(" ", $item->director);
            $arr['director'] = $director[0];
            $arr['manager'] = $manager[0];
            $color = PpHelper::getTaskColor($item->status);
            $arr['color'] = $color;
            $arr['status'] = "<span style='color: {$color}'>".JText::sprintf("COM_PP_TASK_STATUS_{$item->status}")."</span>";
            $arr['status_export'] = JText::sprintf("COM_PP_TASK_STATUS_{$item->status}");
            $arr['status_code'] = $item->status;
            $date_start = JDate::getInstance($item->date_start);
            $date_end = JDate::getInstance($item->date_end);
            $arr['date_start'] = $date_start->format("d.m.Y");
            $arr['date_end'] = $date_end->format("d.m.Y");
            $arr['date_close'] = (!empty($item->date_close)) ? JDate::getInstance($item->date_close)->format("d.m.Y") : '';
            $url = JRoute::_("index.php?option={$this->option}&amp;task=task.edit&amp;id={$item->id}");
            $arr['edit_link'] = JHtml::link($url, JText::sprintf('JTOOLBAR_EDIT'));
            $url = JRoute::_("index.php?option={$this->option}&amp;view=operations&amp;taskID={$item->id}");
            $arr['operations_link'] = JHtml::link($url, $item->task);
            $result['items'][$item->sectionID][] = $arr;
            //Прибавляем количество задач в текущем разделе у менеджера
            if (!isset($result['managers'][$item->managerID][$item->parentID])) $result['managers'][$item->managerID][$item->parentID] = 0;
            $result['managers'][$item->managerID][$item->parentID]++;
            if (!isset($result['managers'][$item->managerID][$item->sectionID])) $result['managers'][$item->managerID][$item->sectionID] = 0;
            $result['managers'][$item->managerID][$item->sectionID]++;
        }
        //Кол-во активных операционных задач
        $result['active'] = $this->getOperationsCount($ids ?? []);

        return $result;
    }

    private function getOperationsCount(array $ids = array()): array
    {
        $model = ListModel::getInstance('Operations', 'PpModel', ['taskID' => 0, 'taskIDs' => $ids, 'statuses' => [-2, 1, 2]]);
        $items = $model->getItems();
        $result = [];
        foreach ($items['items'] as $item) {
            if(!isset($result[$item['taskID']])) $result[$item['taskID']] = 0;
            $result[$item['taskID']]++;
        }
        return $result;
    }

    public function exportToExcel()
    {
        $items = $this->getItems();
        $heads = $this->getColumnHeads();
        JLoader::discover('PHPExcel', JPATH_LIBRARIES);
        JLoader::register('PHPExcel', JPATH_LIBRARIES . '/PHPExcel.php');
        $managerID = $this->state->get('filter.manager');
        $xls = new PHPExcel();
        $xls->setActiveSheetIndex(0);
        $sheet = $xls->getActiveSheet();
        //Ширина столбцов
        $width = array("A" => 12, "B" => 84, "C" => 15, "D" => 18, "E" => 15, "F" => 34, "G" => 14, "H" => 14, "I" => 14);
        foreach ($width as $col => $value) {
            $sheet->getColumnDimension($col)->setWidth($value);
        }
        foreach ($heads as $column => $data) {
            $sheet->setCellValue($column, $data);
            $sheet->getStyle($column)->getFont()->setBold(true);
            $sheet->getStyle($column)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }
        $str = 2;
        foreach ($items['sections']['parents'] as $parentID => $parent) {
            if (is_numeric($managerID) && $items['managers'][$managerID][$parentID] < 1) continue;
            //Раздел
            $sheet->mergeCells("A{$str}:I{$str}");
            $column = "A{$str}";
            $sheet->setCellValue($column, $parent['title']);
            $sheet->getStyle($column)->getFont()->setBold(true);
            $sheet->getStyle($column)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $str++;
            foreach ($items['sections']['items'][$parentID] as $section) {
                if (is_numeric($managerID) && $items['managers'][$managerID][$section['id']] < 1) continue;
                //Подраздел
                $sheet->mergeCells("A{$str}:I{$str}");
                $column = "A{$str}";
                $sheet->setCellValue($column, $section['title']);
                $sheet->getStyle($column)->getFont()->setBold(true);
                $str++;
                foreach ($items['items'][$section['id']] as $i => $item) {
                    //Задачи
                    $sheet->setCellValue("A{$str}", $item['status_export']);
                    $sheet->getStyle("A{$str}")->getFont()->getColor()->setRGB(str_ireplace("#", "", $item['color']));
                    $sheet->setCellValue("B{$str}", $item['task']);
                    $sheet->setCellValue("C{$str}", $items['active'][$item['id']] ?? 0);
                    $sheet->setCellValue("D{$str}", $item['manager']);
                    $sheet->setCellValue("E{$str}", $item['director']);
                    $sheet->setCellValue("F{$str}", $item['contractor']);
                    $sheet->setCellValue("G{$str}", $item['date_close']);
                    $sheet->setCellValue("H{$str}", $item['date_start']);
                    $sheet->setCellValue("I{$str}", $item['date_end']);
                    $str++;
                }
            }
        }
        $filename = sprintf("Plan.xls");
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

    public function getSections()
    {
        $model = ListModel::getInstance('Sections', 'PpModel', ['for_plan' => true]);
        return $model->getItems();
    }

    public function getSectionTitle()
    {
        $sectionID = $this->state->get('filter.section');
        if (is_numeric($sectionID)) {
            $table = parent::getTable('Sections', 'TablePp');
            $table->load($sectionID);
            return $table->title ?? '';
        }
        else return '';
    }

    public function getColumnHeads(): array
    {
        $result = [];
        $result["A1"] = JText::sprintf('COM_PP_HEAD_TASKS_STATUS');
        $result["B1"] = JText::sprintf('COM_PP_HEAD_TASKS_TASK');
        $result["C1"] = JText::sprintf('COM_PP_HEAD_TASKS_ACTIVE_OPERATIONS');
        $result["D1"] = JText::sprintf('COM_PP_HEAD_MANAGER');
        $result["E1"] = JText::sprintf('COM_PP_HEAD_DIRECTOR');
        $result["F1"] = JText::sprintf('COM_PP_HEAD_TASKS_CONTRACTOR');
        $result["G1"] = JText::sprintf('COM_PP_HEAD_TASKS_DATE_CLOSE');
        $result["H1"] = JText::sprintf('COM_PP_HEAD_TASKS_DATE_START');
        $result["I1"] = JText::sprintf('COM_PP_HEAD_TASKS_DATE_END');
        return $result;
    }

    protected function populateState($ordering = 'status', $direction = 'ASC')
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
        $manager = $this->getUserStateFromRequest($this->context . '.filter.manager', 'filter_manager');
        $this->setState('filter.manager', $manager);
        $director = $this->getUserStateFromRequest($this->context . '.filter.director', 'filter_director');
        $this->setState('filter.director', $director);
        $type = $this->getUserStateFromRequest($this->context . '.filter.type', 'filter_type');
        $this->setState('filter.type', $type);
        $section = $this->getUserStateFromRequest($this->context . '.filter.section', 'filter_section');
        $this->setState('filter.section', $section);
        $sub_section = $this->getUserStateFromRequest($this->context . '.filter.sub_section', 'filter_sub_section');
        $this->setState('filter.sub_section', $sub_section);
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
        $project = $this->getUserStateFromRequest($this->context . '.filter.project', 'filter_project', 11);
        $this->setState('filter.project', $project);
        parent::populateState($ordering, $direction);
        PpHelper::check_refresh();
    }

    protected function getStoreId($id = '')
    {
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.manager');
        $id .= ':' . $this->getState('filter.director');
        $id .= ':' . $this->getState('filter.type');
        $id .= ':' . $this->getState('filter.section');
        $id .= ':' . $this->getState('filter.sub_section');
        $id .= ':' . $this->getState('filter.parent');
        $id .= ':' . $this->getState('filter.object');
        $id .= ':' . $this->getState('filter.date_start');
        $id .= ':' . $this->getState('filter.date_end');
        $id .= ':' . $this->getState('filter.date_close');
        $id .= ':' . $this->getState('filter.status');
        $id .= ':' . $this->getState('filter.project');
        return parent::getStoreId($id);
    }

    private $export;
}
