<?php
use Joomla\CMS\Form\FormRule;
defined('_JEXEC') or die;

class JFormRuleObject extends FormRule
{
    protected $regex = '^[A-Za-zА-Яа-я0-9\"\.\,\s-]{0,255}$';
}