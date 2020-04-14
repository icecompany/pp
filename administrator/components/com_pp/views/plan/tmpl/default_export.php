<?php
defined('_JEXEC') or die;
$url = "index.php?option=com_pp&task=plan.execute&format=xls";
if ($this->taskID > 0) $url .= "&taskID={$this->taskID}";
$text = JText::sprintf('COM_PP_ACTION_LINK_EXPORT_TO_EXCEL');
echo JHtml::link(JRoute::_($url), $text); ?>
