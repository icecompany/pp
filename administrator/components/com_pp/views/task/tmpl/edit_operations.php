<?php
defined('_JEXEC') or die;
$return = PpHelper::getReturnUrl();
$url = JRoute::_("index.php?option=com_pp&amp;task=operation.add&amp;taskID={$this->item->id}&amp;return={$return}");
$link = JHtml::link($url, JText::sprintf('COM_PP_ACTION_LINK_ADD_OPERATION'));
?>
<div><?php echo $link;?></div>

