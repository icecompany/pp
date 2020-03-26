<?php
// Запрет прямого доступа.
defined('_JEXEC') or die;
$colspan = '80';?>
<tr>
    <td colspan="<?php echo $colspan;?>" class="pagination-centered"><?php echo $this->pagination->getListFooter(); ?></td>
</tr>