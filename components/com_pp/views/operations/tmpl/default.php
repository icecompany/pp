<?php
defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('searchtools.form');

use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('stylesheet', 'com_pp/style.css', array('version' => 'auto', 'relative' => true));
HTMLHelper::_('script', 'com_pp/script.js', array('version' => 'auto', 'relative' => true));
?>
<div>
    <h4><?php echo JText::sprintf('COM_PP_HEAD_PAGE_TITLE_OPERATIONS_IN_VERSION', $this->version->version, JDate::getInstance($this->version->dat)->format('d.m.Y'));?></h4>
</div>
<div class="row-fluid">
    <div id="j-main-container" class="span10">
        <form action="<?php echo PpHelper::getActionUrl(); ?>" method="post"
              name="adminForm" id="adminForm">
            <table class="table table-striped" id="itemList">
                <thead><?php echo $this->loadTemplate('head'); ?></thead>
                <tbody><?php echo $this->loadTemplate('body'); ?></tbody>
                <tfoot><?php echo $this->loadTemplate('foot'); ?></tfoot>
            </table>
            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="boxchecked" value="0"/>
            <?php echo JHtml::_('form.token');?>
        </form>
    </div>
</div>
