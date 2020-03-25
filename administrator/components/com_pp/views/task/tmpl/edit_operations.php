<?php
defined('_JEXEC') or die;
if ($this->item->id === null) return;
$return = PpHelper::getReturnUrl();
$url = JRoute::_("index.php?option=com_pp&amp;task=operation.add&amp;taskID={$this->item->id}&amp;return={$return}");
$link = JHtml::link($url, JText::sprintf('COM_PP_ACTION_LINK_ADD_OPERATION'));
?>
<div class="center"><h2><?php echo JText::sprintf('COM_PP_TITLE_TASK_OPERATIONS');?></h2></div>
<div><?php echo $link;?></div>
<div>
    <table class="table table-stripped">
        <thead>
            <tr>
                <td><?php echo JText::sprintf('COM_PP_HEAD_OPERATIONS_DATE_OPERATION');?></td>
                <td><?php echo JText::sprintf('COM_PP_HEAD_OPERATIONS_TASK');?></td>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->item->operations as $operation): ?>
                <tr>
                    <td><?php echo $operation['date_operation'];?></td>
                    <td><?php echo $operation['edit_link'];?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
