<?php
defined('_JEXEC') or die;
$listOrder    = $this->escape($this->state->get('list.ordering'));
$listDirn    = $this->escape($this->state->get('list.direction'));
?>
<tr>
    <th style="width: 1%;">
        <?php echo JHtml::_('grid.checkall'); ?>
    </th>
    <th style="width: 1%;">
        №
    </th>
    <th>
        <?php echo JHtml::_('searchtools.sort', 'COM_PP_HEAD_OPERATIONS_STATUS', 'status', $listDirn, $listOrder); ?>
    </th>
    <th>
        <?php echo JHtml::_('searchtools.sort', 'COM_PP_HEAD_OPERATIONS_DATE_OPERATION', 'o.date_operation', $listDirn, $listOrder); ?>
    </th>
    <th>
        <?php echo JText::sprintf('COM_PP_HEAD_OPERATIONS_TASK');?>
    </th>
    <th>
        <?php echo JText::sprintf('COM_PP_HEAD_OPERATIONS_RESULT');?>
    </th>
    <th>
        <?php echo JHtml::_('searchtools.sort', 'COM_PP_HEAD_TASKS_SECTION', 'section', $listDirn, $listOrder); ?>
    </th>
    <th>
        <?php echo JHtml::_('searchtools.sort', 'COM_PP_HEAD_TASKS_PARENT', 'parent', $listDirn, $listOrder); ?>
    </th>
    <th>
        <?php echo JHtml::_('searchtools.sort', 'COM_PP_HEAD_MANAGER', 'manager', $listDirn, $listOrder); ?>
    </th>
    <th>
        <?php echo JHtml::_('searchtools.sort', 'COM_PP_HEAD_DIRECTOR', 'director', $listDirn, $listOrder); ?>
    </th>
    <th style="width: 1%;">
        <?php echo JHtml::_('searchtools.sort', 'ID', 's.id', $listDirn, $listOrder); ?>
    </th>
</tr>
