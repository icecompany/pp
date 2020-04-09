<?php
// Запрет прямого доступа.
defined('_JEXEC') or die;
$ii = $this->state->get('list.start', 0);
$sub_section = $this->state->get('filter.sub_section');
foreach ($this->items['sections']['parents'] as $parentID => $parent) :
    if (is_numeric($sub_section) && $parentID != $this->items['sections']['flip'][$sub_section]) continue;
    ?>
    <tr>
        <td colspan="20"><b><?php echo $parent['title'];?></b></td>
    </tr>
    <?php foreach ($this->items['sections']['items'][$parentID] as $section) :
    if (is_numeric($sub_section) && $section['id'] != $sub_section) continue;
    ?>
        <tr>
            <td colspan="20"><b><?php echo $section['title'];?></b></td>
        </tr>
        <?php
        if (empty($this->items['items'][$section['id']])) :?>
            <tr>
                <td colspan="11"><?php echo JText::sprintf('COM_PP_MSG_SUB_SECTION_HAVE_NO_TASKS');?></td>
            </tr>
        <?php endif; ?>
        <?php foreach ($this->items['items'][$section['id']] as $i => $item) :?>
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


