<?php
use Joomla\CMS\MVC\Controller\AdminController;

defined('_JEXEC') or die;

class PpControllerPlan extends AdminController
{
    public function download(): void
    {
        echo "<script>window.open('index.php?option=com_pp&task=plan.execute&format=xls');</script>";
        echo "<script>location.href='{$_SERVER['HTTP_REFERER']}'</script>";
        jexit();
    }
}
