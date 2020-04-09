<?php
// Запрет прямого доступа.
defined('_JEXEC') or die;
$ii = $this->state->get('list.start', 0);
$listOrder = $this->escape($this->state->get('list.ordering'));
$saveOrder = $listOrder == 's.ordering';
if ($saveOrder)
{
    $saveOrderingUrl = 'index.php?option=com_pp&task=sections.saveOrderAjax&tmpl=component';
    JHtml::_('sortablelist.sortable', 'itemList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
foreach ($this->items['parents'] as $i => $parent) :?>
    <tr class="row<?php echo $ii % 2; ?>">
        <td class="order nowrap center hidden-phone">
            <?php
            $iconClass = '';
            if (!$saveOrder) {
                $iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::_('tooltipText', 'JORDERINGDISABLED');
            }
            ?>
            <span class="sortable-handler <?php echo $iconClass ?>">
                <span class="icon-menu" aria-hidden="true"></span>
            </span>
            <?php if ($saveOrder) : ?>
                <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $parent['ordering']; ?>" class="width-20 text-area-order"/>
            <?php endif; ?>
        </td>
        <td class="center">
            <?php echo JHtml::_('grid.id', $i, $parent['id']); ?>
        </td>
        <td>
            <?php echo ++$ii; ?>
        </td>
        <td>
            <?php echo $parent['tasks_link']; ?>
        </td>
        <td>
            <?php echo $parent['manager']; ?>
        </td>
        <td>
            <?php echo $parent['id']; ?>
        </td>
    </tr>
    <?php foreach ($this->items['items'][$i] as $j => $item) :?>
        <tr class="row<?php echo $j % 2; ?>">
            <td class="order nowrap center hidden-phone">
                <?php
                $iconClass = '';
                if (!$saveOrder) {
                    $iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::_('tooltipText', 'JORDERINGDISABLED');
                }
                ?>
                <span class="sortable-handler <?php echo $iconClass ?>">
                <span class="icon-menu" aria-hidden="true"></span>
            </span>
                <?php if ($saveOrder) : ?>
                    <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item['ordering']; ?>" class="width-20 text-area-order"/>
                <?php endif; ?>
            </td>
            <td class="center">
                <?php echo JHtml::_('grid.id', $j, $item['id']); ?>
            </td>
            <td>
                <?php echo ++$ii; ?>
            </td>
            <td>
                <?php echo $item['tasks_link']; ?>
            </td>
            <td>
                <?php echo $item['manager']; ?>
            </td>
            <td>
                <?php echo $item['id']; ?>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endforeach; ?>

