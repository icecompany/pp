<?php
// Запрет прямого доступа.
defined('_JEXEC') or die;
$ii = $this->state->get('list.start', 0);
foreach ($this->items['items'] as $parentID => $parent) :?>
    <tr>
        <td colspan="20"><h3><?php echo $this->items['parents'][$parentID];?></h3></td>
    </tr>
    <?php foreach ($this->items['items'][$parentID] as $sectionID => $section) :?>
        <tr>
            <td colspan="20"><h4>- <?php echo $this->items['sections'][$sectionID];?></h4></td>
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
<?php foreach ($this->emptySections as $emptyParentID => $emptyParent) :?>
    <tr>
        <td colspan="20"><h3><?php echo $emptyParent['title'];?></h3></td>
    </tr>
    <?php foreach ($emptyParent as $emptySectionID => $emptySectionTitle) :
        if ($emptySectionTitle === 'title') continue;
        foreach ($emptySectionTitle as $id => $value) :?>
            <tr>
                <td colspan="20"><h4><?php echo $value;?></h4></td>
            </tr>
        <?php endforeach; ?>
    <?php endforeach;?>
<?php endforeach;?>


