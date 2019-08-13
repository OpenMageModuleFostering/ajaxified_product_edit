<?php
/**
 * @author    Marcin Frymark
 * @email     contact@alekseon.com
 * @company   Alekseon
 * @website   www.alekseon.com
 */
class Alekseon_AjaxifiedProductEdit_Block_Adminhtml_GridView_Manager_Edit_Tabs_ManageColumns extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function getTabLabel()
    {
        return Mage::helper('alekseon_ajaxifiedProductEdit')->__('Manage Columns');
    }

    public function getTabTitle()
    {
        return Mage::helper('alekseon_ajaxifiedProductEdit')->__('Manage Columns');
    }
    
    public function canShowTab()
    {
        return true;
    }
    
    public function isHidden()
    {
        return false;
    }
    
    public function getTabClass()
    {
        return 'ajax';
    }

    public function getClass()
    {
        return $this->getTabClass();
    }

    public function getTabUrl()
    {
        return $this->getUrl('*/*/manageColumns', array('_current' => true));
    }
}
