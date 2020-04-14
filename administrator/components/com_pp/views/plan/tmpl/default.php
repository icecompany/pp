<?php
defined('_JEXEC') or die;
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('searchtools.form');

use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('stylesheet', 'com_pp/style.css', array('version' => 'auto', 'relative' => true));
HTMLHelper::_('script', 'com_pp/script.js', array('version' => 'auto', 'relative' => true));
?>
<div class="row-fluid">
    <div id="j-sidebar-container" class="span2">
        <form action="<?php echo PpHelper::getSidebarAction(); ?>" method="post">
            <?php echo $this->sidebar; ?>
        </form>
    </div>
    <div id="j-main-container" class="span10">
        <form action="<?php echo PpHelper::getActionUrl(); ?>" method="post"
              name="adminForm" id="adminForm">
            <?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
            <div><?php echo $this->loadTemplate('export'); ?></div>
            <table class="table table-striped" id="itemList">
                <?php echo $this->loadTemplate('head');?>
                <?php echo $this->loadTemplate('plan');?>
            </table>
            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="boxchecked" value="0"/>
            <?php echo JHtml::_('form.token');?>
        </form>
    </div>
</div>
