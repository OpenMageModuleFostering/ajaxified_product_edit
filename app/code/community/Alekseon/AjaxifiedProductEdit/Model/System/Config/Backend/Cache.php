<?php
/**
 * @author    Marcin Frymark
 * @email     contact@alekseon.com
 * @company   Alekseon
 * @website   www.alekseon.com
 */
class Alekseon_AjaxifiedProductEdit_Model_System_Config_Backend_Cache extends Mage_Core_Model_Config_Data
{
    protected function _afterSave()
    {
        if ($this->isValueChanged()) {
            Mage::app()->cleanCache();
        }
    }
}