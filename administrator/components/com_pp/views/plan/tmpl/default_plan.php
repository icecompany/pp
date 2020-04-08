<?php
// Запрет прямого доступа.
defined('_JEXEC') or die;
$ii = $this->state->get('list.start', 0);
foreach ($this->items['items'] as $parentID => $parent) :?>
    <tr>
        <td colspan="20" class="center"><h3><?php echo $this->items['parents'][$parentID];?></h3></td>
    </tr>
    <?php foreach ($this->items['items'][$parentID] as $sectionID => $section) :?>
        <tr>
            <td colspan="20" class="center"><h4><?php echo $this->items['sections'][$sectionID];?></h4></td>
        </tr>
        <?php foreach ($this->items['items'][$parentID][$sectionID] as $i => $item) :?>
            <tr class="row<?php echo $i % 2; ?>">
                <td class="center">
                    <?php echo JHtml::_('grid.id', $i, $item['id']); ?>
                </td>
                <td>
                    <?php echo ++$ii; ?>
                </td>
                <td>
                    <?php echo $item['status']; ?>
                </td>
                <td>
                    <?php echo $item['operations_link']; ?>
                </td>
                <td>
                    <?php echo $item['manager']; ?>
                </td>
                <td>
                    <?php echo $item['director']; ?>
                </td>
                <td>
                    <?php echo $item['contractor']; ?>
                </td>
                <td>
                    <?php echo $item['date_close']; ?>
                </td>
                <td>
                    <?php echo $item['date_start']; ?>
                </td>
                <td>
                    <?php echo $item['date_end']; ?>
                </td>
                <td>
                    <?php echo $item['id']; ?>
                </td>
            </tr>
        <?php endforeach;?>
    <?php endforeach;?>
<?php endforeach;?>