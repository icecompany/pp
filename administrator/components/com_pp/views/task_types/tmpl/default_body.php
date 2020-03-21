<?php
// Запрет прямого доступа.
defined('_JEXEC') or die;
$ii = $this->state->get('list.start', 0);
foreach ($this->items['items'] as $i => $item) :
    ?>
    <tr class="row0">
        <td class="order nowrap center hidden-phone">
            <?php
            $iconClass = '';
            if (!$saveOrder)
            {
                $iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::_('tooltipText', 'JORDERINGDISABLED');
            }
            ?>
            <span class="sortable-handler<?php echo $iconClass ?>">
								<span class="icon-menu" aria-hidden="true"></span>
							</span>
            <?php if ($saveOrder) : ?>
                <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item['ordering']; ?>" class="width-20 text-area-order" />
            <?php endif; ?>
        </td>
        <td class="center">
            <?php echo JHtml::_('grid.id', $i, $item['id']); ?>
        </td>
        <td>
            <?php echo ++$ii; ?>
        </td>
        <td>
            <?php echo $item['edit_link'];?>
        </td>
        <td>
            <?php echo $item['id'];?>
        </td>
    </tr>
<?php endforeach; ?>